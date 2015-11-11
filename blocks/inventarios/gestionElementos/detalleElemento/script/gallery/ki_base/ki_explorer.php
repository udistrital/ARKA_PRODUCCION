<?php
ob_start("ob_gzhandler");

//----------------------------------------------------------------------------- functions --------------------------------------------------------------------------------

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

if (!function_exists('file_get_contents')) {
	function file_get_contents($filename) {
		if ($handle = @fopen($filename, 'rb')) {
			$data = fread($handle, filesize($filename));
			fclose($fh);
			return $data;
		}
	}
}

function cmp_0($a, $b)
{
	global $galleryfolder;
    return (filemtime($galleryfolder.$a[0]) < filemtime($galleryfolder.$b[0])) ? -1 : 1;
}

function cmp_1($a, $b)
{
	global $galleryfolder;
    return (filemtime($galleryfolder.$a[0]) > filemtime($galleryfolder.$b[0])) ? -1 : 1;
}

function cmp_2($a, $b)
{
	global $galleryfolder;
	
	$exif = @exif_read_data($galleryfolder.$a[0]);
	$date1 = "";
    if (isset($exif['DateTimeOriginal']))
        $date1 = $exif['DateTimeOriginal'];
    if (empty($date1) && isset($exif['DateTime']))
        $date1 = $exif['DateTime'];
    if (!empty($date1)){
        $date1 = split(':', str_replace(' ',':', $date1));
        $date1 = "{$date1[0]}-{$date1[1]}-{$date1[2]} {$date1[3]}:{$date1[4]}";
		if(sizeof($date1) > 5)$date1 .= ":{$date1[5]}";
		$date1 = strtotime($date1);
	} else {
		$date1 = filemtime($galleryfolder.$a[0]);
	}
	$exif = @exif_read_data($galleryfolder.$b[0]);
	$date2 = "";
    if (isset($exif['DateTimeOriginal']))
        $date2 = $exif['DateTimeOriginal'];
    if (empty($date2) && isset($exif['DateTime']))
        $date2 = $exif['DateTime'];
    if (!empty($date2)){
        $date2 = split(':', str_replace(' ',':', $date2));
        $date2 = "{$date2[0]}-{$date2[1]}-{$date2[2]} {$date2[3]}:{$date2[4]}";
		if(sizeof($date2) > 5)$date2 .= ":{$date1[5]}";
		$date2 = strtotime($date2);
	} else {
		$date2 = filemtime($galleryfolder.$b[0]);
	}	
	
    return $date1 > $date2 ? 1 : -1;
}

function cmp_3($a, $b)
{
	return strcmp($a[0], $b[0]);
}

//-------------------------------------------------------------------------- end functions --------------------------------------------------------------------------------

$supported = array("jpg","png","gif");

$reldir = "";
if(isset($_POST['reldir']))$reldir = $_POST['reldir'];
$confdir = $reldir."ki_config/";
$galleriesdir = $reldir."ki_galleries/";
$basedir = $reldir."ki_base/";

if(isset($_POST['gallery']))
	$gallery = $_POST['gallery'];
else
	exit();
	
if(isset($_POST['gallerynumber']))
	$gallerynumber = $_POST['gallerynumber'];
else
	exit();
	
// -------------- Sicherheitsabfragen!
if(preg_match("/[\.]*\//", $gallery))exit();
// ---------- Ende Sicherheitsabfragen!

include_once("../ki_config/ki_setup.php");
if(is_file("../ki_config/".$gallery."_ki_setup.php")){
	include_once("../ki_config/".$gallery."_ki_setup.php");
	$configfile = "../ki_config/".$gallery."_ki_setup.php";
} else {
	$configfile = "../ki_config/ki_setup.php";
}
$galleryfolder = "../ki_galleries/".$gallery."/";
$thumbsfolder = $galleryfolder."thumbs/";
$commentsfolder = $galleryfolder."comments/";

$pwok = 0;
session_start();
if(isset($_POST['todelete']) || isset($_POST['tochange'])){
	if(isset($_SESSION['pwquery'])){
		if($_SESSION['pwquery'] === $ki_pw)$pwok = 1;
	}
	if($pwok == 0)exit();
}

$temp = getimagesize("ki_nav_next.png");
if($ki_nav_always == 1 && $ki_show_nav == 1)$ki_fr_height -= ($temp[1]+18);
if($ki_th_lines == "auto")$ki_th_lines = ceil($ki_thumbs/($ki_th_per_line));
if($ki_th_width == "auto")$ki_th_width = round($ki_fr_width/($ki_th_per_line)) - round($ki_fr_height*0.04) - 4;
if($ki_th_height == "auto")$ki_th_height = round($ki_fr_height/($ki_th_lines)) - round($ki_fr_height*0.04) - 4;
$ki_th_width = $ki_th_width - 2*$ki_th_bord_size;
$ki_th_height = $ki_th_height - 2*$ki_th_bord_size;

/*------------------- error/warning checking ------------------*/
if(!is_dir($galleryfolder)) {
	echo "<div style='background:#ffbbbb; color:#000000; padding:4px;'>ERROR: KoschtIT Image Gallery could't find the following folder on the server: '".htmlentities($gallery)."' . Please check if the folder is available in the 'ki_galleries' folder.</div>";
	exit();
}
if($ki_pic_order == 3){
	if(!function_exists("exif_read_data")){
		$ki_pic_order = 2;
	}
}
/*------------------- end error/warning checking ------------------*/

/*------------------- config settings ------------------*/
$savefile = $gallery."_lastmodified";
$lastmodified = filemtime($configfile);
if(!file_exists($savefile)) {
	$ki_thumbs_to_disk = 0;
}
/*------------------- end config settings ------------------*/

$files = array();
$temp = array();
if(is_file($gallery."_dir")){
	$temp = explode(PHP_EOL, file_get_contents($gallery."_dir"));
	$files = unserialize($temp[1]);		
} else {
	$iterator = new DirectoryIterator($galleryfolder);
	foreach ($iterator as $fileInfo) {
		$tfile = $fileInfo->getFilename();
		if(!in_array(strtolower(substr($tfile, -3)), $supported))$continue;
		$imgsize = @getimagesize($galleryfolder.$tfile);
		if($imgsize[0]){
			$files[] = array($tfile, $imgsize[0], $imgsize[1]);
		}
	}
	switch($ki_pic_order){
		case 0:
			usort($files, "cmp_1");
		break;
		case 1:
			usort($files, "cmp_0");
		break;
		case 2:
			usort($files, "cmp_3");
		break;
		case 3:
			usort($files, "cmp_2");
		break;
		default:
			usort($files, "cmp_1");
		break;
	}
	reset($files);
}

$id = 0;
foreach ($files as $file) {
	$id++;

	$breite = $file[1];
	$hoehe = $file[2];
	
	if( ($breite / $hoehe) > 1){
		$k = $hoehe / $breite;
		$breite = $ki_th_width;
		$hoehe = $k*$breite;
		if($hoehe > $ki_th_height){
			$hoehe = $ki_th_height;
			$breite = (1/$k) * $hoehe;
		}
	} else {
		$k = $breite / $hoehe;
		$hoehe = $ki_th_height;
		$breite = $k*$hoehe;
		if($breite > $ki_th_width){
			$breite = $ki_th_width;
			$hoehe = (1/$k) * $breite;
		}
	}
							
	if($ki_th_to_square == 1) {
		if($ki_th_width < $ki_th_height){
			$breite = $ki_th_width;
		} else {
			$breite = $ki_th_height;
		}
		$hoehe = $breite;
	}
	
	$breite = round($breite);
	$hoehe = round($hoehe);
	
	if($ki_th_bord_hover_increase > 1){
		$inc_breite = round($breite*$ki_th_bord_hover_increase);
		$inc_hoehe = round($breite*$ki_th_bord_hover_increase);
	} else {
		$inc_breite = $breite;
		$inc_hoehe = $hoehe;	
	}

	$src = $basedir."ki_makepic.php?file=".$gallery."/".rawurlencode($file[0])."&width=".$inc_breite."&height=".$inc_hoehe;
	$style = "border:".$ki_th_bord_size."px solid ".$ki_th_bord_color."; visibility:hidden; display:block; position:absolute; width:".$breite."px; height:".$hoehe."px;";
	if($ki_thumbs_to_disk == 1){
		if(!is_file($thumbsfolder.$lastmodified.$file[0])){
			$src .= "&picname=".$lastmodified.rawurlencode($file[0]);
		} else {
			$src = $galleriesdir.$gallery."/thumbs/".$lastmodified.$file[0];
		}
	}
	if(isset($_POST['todelete']))
		echo "<img id='".$gallerynumber."_".($id-1)."' src='".$src."' style='".$style." cursor:pointer;' onclick='kid.deleteImage(this)' onload=\"this.style.visibility='visible'\" alt='".$breite."_".$hoehe."' onmouseover='fw.fade(this, 33)' onmouseout='fw.fade(this, 100)' />";	
	elseif(isset($_POST['tochange']))
		echo "<img id='".$gallerynumber."_".($id-1)."' src='".$src."' style='".$style." z-index:1; cursor:move;' onmousedown='kic.selectForDrag(this)' onmouseup='kic.releaseForDrag(this)' onmouseover='kic.hoverDrag(this)' oncontextmenu='return false' ondragstart='return false' onselectstart='return false' onload=\"this.style.visibility='visible'\" moved='0' alt='".$breite."_".$hoehe."' />";	
	else
		echo "<img id='".$gallerynumber."_".($id-1)."' src='".$src."' style='".$style." cursor:pointer;' onclick='kie.getImage(this.id)' onload=\"this.style.visibility='visible'\" onmouseover='kib.makebigger(this)' onmouseout='kib.makesmaller(this)' alt='".$breite."_".$hoehe."' />";
	
}
echo "<div style='position:absolute; width:1px; left:0px;'></div>";
?>
