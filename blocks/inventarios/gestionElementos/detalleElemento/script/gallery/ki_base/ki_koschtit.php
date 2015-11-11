<?php
ob_start("ob_gzhandler");
if(!ini_get('date.timezone') && function_exists("date_default_timezone_set"))date_default_timezone_set('Europe/Berlin');

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
        $date1 = explode(':', str_replace(' ',':', $date1));
		if(sizeof($date1) > 4){
	        $date1 = "{$date1[0]}-{$date1[1]}-{$date1[2]} {$date1[3]}:{$date1[4]}";
			if(sizeof($date1) > 5)$date1 .= ":{$date1[5]}";
			$date1 = strtotime($date1);
		}
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
        $date2 = explode(':', str_replace(' ',':', $date2));
		if(sizeof($date2) > 4){
			$date2 = "{$date2[0]}-{$date2[1]}-{$date2[2]} {$date2[3]}:{$date2[4]}";
			if(sizeof($date2) > 5)$date2 .= ":{$date1[5]}";
			$date2 = strtotime($date2);
		} else {
			$date2 = filemtime($galleryfolder.$b[0]);
		}
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
	
if(isset($_POST['startfrom']))
	$startfrom = $_POST['startfrom'];
else
	$startfrom = 0;
	
if(isset($_POST['collectinfo']))
	$collectinfo = 1;
else
	$collectinfo = 0;
	
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
$viewercommentsfolder = $galleryfolder."viewercomments/";

$temp = getimagesize("ki_nav_next.png");
if($ki_nav_always == 1 && $ki_show_nav == 1)$ki_fr_height -= ($temp[1]+18);
if($ki_th_lines == "auto")$ki_th_lines = ceil($ki_thumbs/($ki_th_per_line));
if($ki_th_width == "auto")$ki_th_width = round($ki_fr_width/($ki_th_per_line)) - round($ki_fr_height*0.04) - 4;
if($ki_th_height == "auto")$ki_th_height = round($ki_fr_height/($ki_th_lines)) - round($ki_fr_height*0.04) - 4;
$ki_th_width = $ki_th_width - 2*$ki_th_bord_size;
$ki_th_height = $ki_th_height - 2*$ki_th_bord_size;
if(($ki_th_lines*$ki_th_per_line) < $ki_thumbs)$ki_thumbs = $ki_th_lines*$ki_th_per_line;

$zeile = 1;
$spalte = 0;

$spaltenbreite = $ki_fr_width/($ki_th_per_line);
$zeilenhoehe = $ki_fr_height/($ki_th_lines);

/*------------------- error/warning checking ------------------*/
if($collectinfo == 1){
	if (!function_exists('imagecreatetruecolor')) {
		echo "<div style='background:#ffbbbb; color:#000000; margin-bottom:5px; position:relative; z-index:10; padding:4px;'>ERROR: KoschtIT Image Gallery can't find the PHP GD2 Library available. Please make sure you have removed the semicolon from this line ';extension=php_gd2.dll' in your php.ini and the library is correctly installed.</div>";
	}
	if(!is_dir($galleryfolder)) {
		echo "<div style='background:#ffbbbb; color:#000000; margin-bottom:5px; position:relative; z-index:10; padding:4px;'>ERROR: KoschtIT Image Gallery can't find the following folder on the server: '".htmlentities($gallery)."' . Please check if the folder is available in the 'ki_galleries' folder.</div>";
		echo "<input type='hidden' id='".$gallerynumber."_info' value='[ ]' />";
		exit();
	}
	if($ki_checkgps == 1){
		if(!function_exists("exif_read_data")){
			if($ki_show_warnings == 1)echo "<div style='background:#bbbbff; color:#000000; margin-bottom:5px; position:relative; z-index:10; padding:4px;'>WARNING: You don't have the exif extension for PHP installed. The script won't be able to read any positioning metadata from your pictures. Please disable '\$ki_checkgps' for fixing this issue. Set '\$ki_show_warnings = 0' if you don't want to see this warning again.</div>";
		}
	}
	if($ki_pic_order == 3){
		if(!function_exists("exif_read_data")){
			$ki_pic_order = 2;
			if($ki_show_warnings == 1)echo "<div style='background:#bbbbff; color:#000000; margin-bottom:5px; position:relative; z-index:10; padding:4px;'>WARNING: You don't have the exif extension for PHP installed. You can't set \'$ki_pic_order\' to 3. Please set a different value for '\$ki_pic_order'. Set '\$ki_show_warnings = 0' if you don't want to see this warning again.</div>";
		}
	}
	if($ki_checkgps == 1 && $ki_cellinfo == 1){
		if(!function_exists("curl_init")){
			if($ki_show_warnings == 1)echo "<div style='background:#bbbbff; color:#000000; margin-bottom:5px; position:relative; z-index:10; padding:4px;'>WARNING: You don't have the curl extension for PHP installed. You will need this extension for retrieving geo-tagging information from opencellid.com . Please disable '\$ki_cellinfo' for fixing this issue. Set '\$ki_show_warnings = 0' if you don't want to see this warning again.</div>";
		}
	}
	if($ki_thumbs_to_disk == 1){
		$error = 0;
		if(!is_dir($thumbsfolder)){
			@chmod($galleryfolder, 0777);
			if( !@mkdir($thumbsfolder, 0777) ) {
				$error = 1;
			}
		} else {
			if(!is_file($thumbsfolder."test") && !$f = @fopen($thumbsfolder."test", 'w')){
				$error = 1;
			}
			@fclose($f);
			@unlink($thumbsfolder."test");
		}
		if($error == 1 && $ki_show_warnings == 1)echo "<div style='background:#bbbbff; color:#000000; margin-bottom:5px; position:relative; z-index:10; padding:4px;'>WARNING: KoschtIT Image Gallery can't get writing permission on the server (\"ki_galleries\\".$gallery."\\thumbs\"-folder). Thumbs won't be saved. Please disable '\$ki_thumbs_to_disk' or grant writing permission. Set '\$ki_show_warnings = 0' if you don't want to see this warning again.</div>";
	}
	if($ki_comments == 1){
		$error = 0;
		if(!is_dir($commentsfolder)){
			@chmod($galleryfolder, 0777);
			if( !@mkdir($commentsfolder, 0777) ) {
				$error = 1;
			}
		} else {
			if(!is_file($commentsfolder."test") && !$f = @fopen($commentsfolder."test", 'w')){
				$error = 1;
			}
			@fclose($f);
			@unlink($commentsfolder."test");
		}
		if($error == 1 && $ki_show_warnings == 1)echo "<div style='background:#bbbbff; color:#000000; margin-bottom:5px; z-index:3000; position:relative; z-index:10; padding:4px;'>WARNING: KoschtIT Image Gallery can't get writing permission on the server (\"ki_galleries\\".$gallery."\\comments\"-folder). Picture comments won't be saved, but you can still use '\$ki_comm_auto'. Please disable '\$ki_comments' or grant writing permission. Set '\$ki_show_warnings = 0' if you don't want to see this warning again.</div>";
	}
	if($ki_viewercomments == 1){
		$error = 0;
		if(!is_dir($viewercommentsfolder)){
			@chmod($galleryfolder, 0777);
			if( !@mkdir($viewercommentsfolder, 0777) ) {
				$error = 1;
			}
		} else {
			if(!is_file($viewercommentsfolder."test") && !$f = @fopen($viewercommentsfolder."test", 'w')){
				$error = 1;
			}
			@fclose($f);
			@unlink($viewercommentsfolder."test");
		}
		if($error == 1 && $ki_show_warnings == 1)echo "<div style='background:#bbbbff; color:#000000; margin-bottom:5px; z-index:3000; position:relative; z-index:10; padding:4px;'>WARNING: KoschtIT Image Gallery can't get writing permission on the server (\"ki_galleries\\".$gallery."\\viewercomments\"-folder). User comments won't be saved. Please disable '\$ki_viewercomments' or grant writing permission. Set '\$ki_show_warnings = 0' if you don't want to see this warning again.</div>";
	}
}
/*------------------- end error/warning checking ------------------*/

$files = array();
$temp = array();
$savedfolderhash = 0;
if(is_file($gallery."_dir")){
	$temp = explode(PHP_EOL, file_get_contents($gallery."_dir"));
	$savedfolderhash = unserialize($temp[0]);
}
//$folderhash = pic_order + MTime of $galleryfolder + fileSize of all files
$folderhash = $ki_pic_order;
$iterator = new DirectoryIterator($galleryfolder);
foreach ($iterator as $fileInfo) {
    if($fileInfo->isDot()){
		$folderhash += $fileInfo->getMTime();
		continue;
	} elseif($fileInfo->isFile()) {
		$folderhash += $fileInfo->getSize();
	}
}

/*------------------- config settings ------------------*/
$savefile = $gallery."_lastmodified";
$lastmodified = filemtime($configfile);
$saved = $ki_fr_width.$ki_fr_height.$ki_thumbs.$ki_th_per_line.$ki_th_lines.$ki_th_width.$ki_th_height.$ki_th_bord_size.$ki_th_bord_hover_increase.$ki_th_to_square.$ki_th_2sq_crop_vert.$ki_th_2sq_crop_hori.$ki_show_nav.$ki_nav_always;
$writestring  = "<?php\r\n\$lm_saved = \"".$saved."\";\r\n";
$writestring .= "\$lm_lastmodified = ".$lastmodified.";\r\n?>";
if(!file_exists($savefile))@file_put_contents($savefile, $writestring);
if(!file_exists($savefile)) {
	$ki_thumbs_to_disk = 0;
} else {
	include_once($savefile);
	if($lm_saved !== $saved){ // Config changed
		@file_put_contents($savefile, $writestring);
		$matches = @glob($thumbsfolder."*.*", GLOB_ERR);
		if(is_array($matches)){
			foreach($matches as $sf) {
				if(!is_dir($sf) && !is_link($sf)){
					@unlink($sf);
				} 
			}
		}
	} else if($folderhash != $savedfolderhash){ // Folder changed
		$matches = @glob($thumbsfolder."*.*", GLOB_ERR);
		if(is_array($matches)){
			foreach($matches as $sf) {
				if(!is_dir($sf) && !is_link($sf)){
					@unlink($sf);
				} 
			}
		}
		@file_put_contents($configfile, file_get_contents($configfile));
		$lastmodified = filemtime($configfile);
	} else {
		$lastmodified = $lm_lastmodified;
	}
}
/*------------------- end config settings ------------------*/

if($folderhash != $savedfolderhash){
	if($ki_pic_order == 4 && sizeof($temp) > 1){
		$files = unserialize($temp[1]); // $files beinhaltet alle in _dir gespeicherten Bilder
		$iterator->rewind();
		foreach ($iterator as $fileInfo) {
			$file = $fileInfo->getFilename(); // Tatsächlich vorhandene Datei im Ordner
			if(!in_array(strtolower(substr($file, -3)), $supported))$continue;
			$imgsize = @getimagesize($galleryfolder.$file);
			if($imgsize[0]){
				$found = false;	// Find inside $files
				for($i = 0; $i < sizeof($files); $i++){
					if($files[$i][0] === $file){
						$found = true;
						$files[$i][1] = $imgsize[0];
						$files[$i][2] = $imgsize[1];
						break;
					}
				}
				if(!$found){
					$newcandidate = array($file, $imgsize[0], $imgsize[1]);
					$files[] = $newcandidate;
				}
			}
		}
		for($i = 0; $i < sizeof($files); $i++){ // Gelöschte Dateien entfernen
			$found = false;
			$iterator->rewind();
			foreach ($iterator as $fileInfo) {
				$file = $fileInfo->getFilename();
				if($file === $files[$i][0]){
					$found = true;
					break;	
				}
			}
			if(!$found){
				array_splice($files, $i, 1);
				$i--;
			}
		}
	} else {
		$iterator->rewind();
		foreach ($iterator as $fileInfo) {
			$file = $fileInfo->getFilename();
			if(!in_array(strtolower(substr($file, -3)), $supported))$continue;
			$imgsize = @getimagesize($galleryfolder.$file);
			if($imgsize[0]){
				$files[] = array($file, $imgsize[0], $imgsize[1]);
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
	@file_put_contents($gallery."_dir", serialize($folderhash).PHP_EOL.serialize($files));
} else {
	$files = unserialize($temp[1]);
}

if($collectinfo == 1){
	$id = 0;
	$fileinfo = "";
	foreach ($files as $file) {
		if($id != 0)$fileinfo .= ", ";
		$id++;
		$fileinfo .=  "{ \"file\" : \"".$file[0]."\", \"x\" : ".$file[1].", \"y\" : ".$file[2]." }";
	}
	echo "<input id='".$gallerynumber."_info' type='hidden' value='[ ".$fileinfo." ]' />";
}

$id = 0;
foreach ($files as $file) {
	$id++;
	if($id > $startfrom) {
	
		$spalte++;
		if($spalte == $ki_th_per_line+1){
			$zeile++;
			$spalte = 1;
		}
		
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
								
		$x_pos = round($spaltenbreite*($spalte - 0.5) - 0.5*$breite) - $ki_th_bord_size;
		$y_pos = round($zeilenhoehe*($zeile - 0.5) - 0.5*$hoehe) - $ki_th_bord_size;
		
		$breite = round($breite);
		$hoehe = round($hoehe);
		
		if($ki_th_bord_hover_increase > 1){
			$inc_breite = round($breite*$ki_th_bord_hover_increase);
			$inc_hoehe = round($breite*$ki_th_bord_hover_increase);
		} else {
			$inc_breite = $breite;
			$inc_hoehe = $hoehe;	
		}

		if($zeile <= $ki_th_lines && ($id-$startfrom) <= $ki_thumbs)
		{
			$src = $basedir."ki_makepic.php?file=".$gallery."/".rawurlencode($file[0])."&width=".$inc_breite."&height=".$inc_hoehe;
			$style = "border:".$ki_th_bord_size."px solid ".$ki_th_bord_color."; position:absolute; left:".$x_pos."px; top:".$y_pos."px; cursor:pointer; visibility:hidden; margin:0px; width:".$breite."px; height:".$hoehe."px;";
			if($ki_thumbs_to_disk == 1){
				if(!is_file($thumbsfolder.$lastmodified.$file[0])){
					$src .= "&picname=".$lastmodified.rawurlencode($file[0]);
				} else {
					$src = $galleriesdir.$gallery."/thumbs/".$lastmodified.$file[0];
				}
			}
			echo "<img id='".$gallerynumber."_".($id-1)."' src='".$src."' style='".$style."' onclick='kib.getImage(this.id)' onload=\"this.style.visibility='visible'\" onmouseover='kib.makebigger(this)' onmouseout='kib.makesmaller(this)' alt='".$breite."_".$hoehe."_".$x_pos."_".$y_pos."' />";			
		}
	}
}

if(($id-$startfrom) > $ki_thumbs)echo "<span id='".$gallerynumber."_next' style='display:none;'>".($startfrom+$ki_thumbs)."</span>";
if($startfrom != 0)echo "<span id='".$gallerynumber."_prev' style='display:none;'>".($startfrom-$ki_thumbs)."</span>";
?>
