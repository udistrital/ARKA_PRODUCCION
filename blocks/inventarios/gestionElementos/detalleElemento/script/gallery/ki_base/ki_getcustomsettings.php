<?php
ob_start("ob_gzhandler");

header("Expires: Mon, 01 Jul 2003 00:00:00 GMT"); // Past date 
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); // Consitnuously modified 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1 
header("Pragma: no-cache"); // NO CACHE

$verz = @opendir("../ki_config/");
while($file = @readdir($verz))
{
	if($file != "." && $file != ".." && strtolower(substr($file, -12)) === "ki_setup.php"){
		include("../ki_config/ki_setup.php");
		if($file === "ki_setup.php")
			$temp = "default";
		else {
			$temp = substr($file, 0, -13);
			include("../ki_config/".$file);				
		}
		
		echo "kib.customsettings['".$temp."'] = Array(".$ki_fr_width.", ".$ki_fr_height.", \"".$ki_fr_color."\", \"".$ki_th_bord_color."\", \"".$ki_th_bord_hover_color."\", ".$ki_th_bord_hover_increase.", ".$ki_resize_auto.", ".$ki_nav_style.", ".$ki_th_bord_size.", ".$ki_show_nav.", ".$ki_nav_always.", ".$ki_slideshow.", \"".$ki_nav_color."\", \"".$ki_nav_border_color."\", \"".$ki_nav_pos."\", ".$ki_show_explorer.", ".$ki_th_shadow.");\r\n";
	}
}
@closedir($verz);
?>