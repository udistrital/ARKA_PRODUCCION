<?php
session_start();

$browser = $_SERVER['HTTP_USER_AGENT'];

if(preg_match("/Opera/",$browser))
	$browser = "opera"; 
elseif(preg_match("/MSIE (9|10|11)/",$browser))
	$browser = "ie9";
elseif(preg_match("/MSIE [7-8]/",$browser))
	$browser = "ie7";
elseif(preg_match("/MSIE [1-6]/",$browser))
	$browser = "ie6";
elseif(preg_match("/AppleWebKit/",$browser))
	$browser = "webkit";
else
	$browser = "gecko";

$reldir = "";
if(isset($_POST['reldir']))$reldir = $_POST['reldir'];
$confdir = $reldir."ki_config/";
$galleriesdir = $reldir."ki_galleries/";
$basedir = $reldir."ki_base/";

/* --------------------------------------------------------- functions ------------------------------------------------------ */

if (!function_exists('file_get_contents')) {
	function file_get_contents($filename) {
		if ($handle = @fopen($filename, 'rb')) {
			$data = fread($handle, filesize($filename));
			fclose($fh);
			return $data;
		}
	}
}

if (!function_exists('file_put_contents')) {
    function file_put_contents($filename, $data) {
        $f = @fopen($filename, 'w');
        if (!$f) {
            return false;
        } else {
            $bytes = fwrite($f, $data);
            fclose($f);
            return $bytes;
        }
    }
}


function draw_image($filename, $id, $style, $params) {
	global $browser, $basedir;
	$idstring = "";
	if($id != "")$idstring = "id='".$id."' ";
	if($browser == "ie6") {
		$imgsize = getimagesize($filename);
		echo "<img ".$idstring."style='filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(src=".$basedir.$filename."); width:".$imgsize[0]."px; height:".$imgsize[1]."px; ".$style."' src='".$basedir."ki_noimage.gif' ".$params." />";
	} else {
		if($style != "")$style = " style='".$style."' ";
		echo "<img ".$idstring."src='".$basedir.$filename."'".$style.$params." />";
	}
}

/* ---------------------------------------------------------- end functions -------------------------------------------------- */

/* --------------------------------------------------------- Display full image ---------------------------------------------------------------------- */

if(isset($_POST['file']))
	$file = rawurldecode($_POST['file']);
else
	exit;

if(isset($_POST['gallery']))
	$gallery = $_POST['gallery'];
else
	exit;

// -------------- Sicherheitsabfragen!
if(preg_match("/[\.]*\//", $file))exit();
if(preg_match("/[\.]*\//", $gallery))exit();
if(!is_file("../ki_galleries/".$gallery."/".$file))exit();
// ---------- Ende Sicherheitsabfragen!

include_once("../ki_config/ki_setup.php");
if(is_file("../ki_config/".$gallery."_ki_setup.php"))include_once("../ki_config/".$gallery."_ki_setup.php");

$imgsize = getimagesize("../ki_galleries/".$gallery."/".$file);

// ---------------- exif data --------------------------------------
if($ki_checkgps == 1){
	$latitude = 0;
	$longitude = 0;
	if(function_exists("exif_read_data")){
		$exif = @exif_read_data("../ki_galleries/".$gallery."/".$file);
		if(isset($exif['GPSLatitude']) && isset($exif['GPSLongitude'])){
			if(isset($exif['GPSLatitudeRef']) && isset($exif['GPSLongitudeRef'])){
				if(count($exif['GPSLatitude']) == 3 && count($exif['GPSLongitude']) == 3){
					$temp = $exif['GPSLatitude'][0];
					$temp = explode("/", $temp);
					$latitude = intval($temp[0])/intval($temp[1]);
					$temp = $exif['GPSLatitude'][1];
					$temp = explode("/", $temp);
					$latitude += ((intval($temp[0])/intval($temp[1]))/60);
					$temp = $exif['GPSLatitude'][2];
					$temp = explode("/", $temp);
					$latitude += ((intval($temp[0])/intval($temp[1]))/3600);
					if($exif['GPSLatitudeRef'] == "S")$latitude*=-1;
					$temp = $exif['GPSLongitude'][0];
					$temp = explode("/", $temp);
					$longitude = intval($temp[0])/intval($temp[1]);
					$temp = $exif['GPSLongitude'][1];
					$temp = explode("/", $temp);
					$longitude += ((intval($temp[0])/intval($temp[1]))/60);
					$temp = $exif['GPSLongitude'][2];
					$temp = explode("/", $temp);
					$longitude += ((intval($temp[0])/intval($temp[1]))/3600);
					if($exif['GPSLongitudeRef'] == "W")$longitude*=-1;
				}
			}
		}
	}
}
if($ki_read_meta == 1){
	if(!ini_get('date.timezone') && function_exists("date_default_timezone_set"))date_default_timezone_set('Europe/Berlin');
	$ex_model = "-";
	$ex_make = "-";
	$ex_exptime = "-";
	$ex_apnum = "-";
	$ex_datetime = "-";
	$ex_iso = "-";
	$ex_flash = "-";
	if(!isset($exif))$exif = @exif_read_data("../ki_galleries/".$gallery."/".$file);
	if(isset($exif['Make'])){ // Hersteller
		if(!is_array($exif['Make']))$ex_make = $exif['Make'];
	}
	if(isset($exif['Model'])){
		if(!is_array($exif['Model'])){
			$ex_model = $exif['Model'];
			$herstell = explode(" ", $ex_make);
			$herstell = $herstell[0];
			if(stristr($ex_model, $herstell) === FALSE)$ex_model = $herstell." ".$ex_model;
		}
	}
	if(isset($exif['ExposureTime'])){
		if(!is_array($exif['ExposureTime']))$ex_exptime = $exif['ExposureTime'];
	}
	if(isset($exif['COMPUTED']['ApertureFNumber'])){
		if(!is_array($exif['COMPUTED']['ApertureFNumber']))$ex_apnum = $exif['COMPUTED']['ApertureFNumber'];
	}
	if(isset($exif['ISOSpeedRatings'])){
		if(!is_array($exif['ISOSpeedRatings']))$ex_iso = $exif['ISOSpeedRatings'];
	}
	if(isset($exif['DateTime'])){
		if(!is_array($exif['DateTime'])){
			$ex_datetime = $exif['DateTime'];
			$ex_datetime = date("d.m.Y H:i", strtotime($ex_datetime));
		}
	}
	if(isset($exif['Flash'])){
		if(!is_array($exif['Flash'])){
			$ex_flash = decbin($exif['Flash']);
			if(substr($ex_flash, strlen($ex_flash)-1) === '1')
				$ex_flash = "yes";
			else
				$ex_flash = "no";
		}
	}
}
// ---------------- end exif data --------------------------------------

if(isset($_POST['x']))
	$x = $_POST['x'];
else
	$x = $imgsize[0];
	
if(isset($_POST['y']))
	$y = $_POST['y'];
else
	$y = $imgsize[1];
	
$commfile = "../ki_galleries/".$gallery."/comments/".substr($file, 0, -4).".txt";
$srcfile = $gallery."/".$file;

if($x != $imgsize[0] || $y != $imgsize[1]){
	$srcfile = $basedir."ki_makepic.php?fullimg=1&file=".$gallery."/".rawurlencode($file)."&width=".$x."&height=".$y;
} else {
	$srcfile = $galleriesdir.$srcfile;
}
?>
<img id='thepicture' style='visibility:hidden; position:absolute; left:50%; top:50%; margin-left:-<?php echo floor($x*0.5) ?>px; margin-top:-<?php echo floor($y*0.5) ?>px; padding:0px; border:0px; width:<?php echo $x ?>px; height:<?php echo $y ?>px; display:block; cursor:pointer;' alt='<?php echo $srcfile ?>' onclick='kiv.goon(1)' />
<?php
if(is_file($commfile)){
	$comm = file_get_contents($commfile);
} else {
	$comm = "";
}

if($ki_comm_auto == 1){
	$comm = $ki_comm_auto_string;
}

$pwok = 0;
if(isset($_SESSION['pwquery'])){
	if($_SESSION['pwquery'] === $ki_pw)$pwok = 1;
}

if($ki_comments == 1){
	if($pwok == 0){
		if($comm != ""){
			$comm = nl2br($comm);
			echo "<div id='thecomment' style='display:none;'>".stripslashes($comm)."</div>";
		}
	} else {
		echo "<div id='thecomment' style='display:none;'><textarea style='font:".$ki_comm_text_size."px ".$ki_comm_text_font."; color:".$ki_comm_text_color."; width:".($x+(2*$ki_bord_size)-22)."px; text-align:".$ki_comm_text_align."; height:50px; overflow:hidden; resize:none; overflow-y:scroll; float:none; margin:0px; padding:0px; background:".$ki_bord_color."; border:1px dashed ".$ki_comm_text_color.";' onkeyup='kiv.savecomment(event, this.value)' id='focusme'>".stripslashes($comm)."</textarea></div>";
	}
}

if($ki_checkgps == 1){
	echo "<input id='kiv_gps' type='hidden' value='".$latitude.",".$longitude."' />";
}
if($ki_read_meta == 1){
	echo "<input id='kiv_meta' type='hidden' value='".$ex_make."|".$ex_model."|".$ex_exptime."|".$ex_apnum."|".$ex_iso."|".$ex_datetime."|".$ex_flash."' />";
}

if($ki_viewercomments == 1){
	$vcommfile = "../ki_galleries/".$gallery."/viewercomments/".substr($file, 0, -4).".txt";
	$value = 0;
	if(is_file($vcommfile)){
		$value = 1;
	}
	echo "<input id='kiv_vcomm'	type='hidden' value='".$value."' />";
}

if($pwok == 1 && $ki_th_to_square == 1 && $ki_thumbs_to_disk == 1){
	if($x >= $y){
		$aspect = $x/$y;
		$x = round($aspect*100);
		$y = 100;
		switch($ki_th_2sq_crop_hori){
			case "left":
				$posx = 0;
			break;
			case "center";
				$posx = round($x*0.5 - 50);
			break;
			case "right":
				$posx = $x - 100;
			break;
		}
		$stylestring1 = "height:100px; width:".$posx."px;";
		$stylestring2 = "height:100px; width:".($x-($posx+100))."px;";
	} else {
		$aspect = $y/$x;
		$y = round($aspect*100);
		$x = 100;
		switch($ki_th_2sq_crop_vert){
			case "top":
				$posy = 0;
			break;
			case "middle";
				$posy = round($y*0.5 - 50);
			break;
			case "bottom":
				$posy = $y - 100;
			break;
		}
		$stylestring1 = "width:100px; height:".$posy."px;";
		$stylestring2 = "width:100px; height:".($y-($posy+100))."px;";		
	}
	if($browser == "ie6" || $browser == "ie7"){
		$more = "filter:progid:DXImageTransform.Microsoft.Alpha(opacity=70);";
	} else {
		$more = "opacity:0.7;";
	}
	$stylestring1 .= $more;
	$stylestring2 .= $more;		
	$srcfile = $basedir."ki_makepic.php?file=".$gallery."/".rawurlencode($file)."&width=".$x."&height=".$y."&fullimg=1";
	echo "<div id='square_selector' style='position:absolute; left:".$ki_bord_size."px; top:".$ki_bord_size."px; width:".$x."px; height:".$y."px; z-index:1001; padding:2px; background:#000000; overflow:hidden; cursor:pointer; display:none;'><img src='".$srcfile."' style='visibility:hidden;' onload=\"this.style.visibility='visible';\" /><div id='square_1' style='background:#000000; position:absolute; left:2px; top:2px; overflow:hidden;".$stylestring1."'></div><div id='square_2' style='background:#000000; position:absolute; right:2px; bottom:2px; overflow:hidden;".$stylestring2."'></div></div>";
}
?>