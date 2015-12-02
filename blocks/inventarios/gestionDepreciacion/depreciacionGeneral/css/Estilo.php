<?php
$indice=0;
$estilo[$indice++]="ui.jqgrid.css";
$estilo[$indice++]="ui.multiselect.css";
$estilo[$indice++]="timepicker.css";
$estilo[$indice++]="jquery-te.css";
$estilo[$indice++]="validationEngine.jquery.css";
$estilo[$indice++]="autocomplete.css";
$estilo[$indice++]="chosen.css";
$estilo[$indice++]="select2.css";
$estilo[$indice++]="jquery_switch.css";
$estilo[$indice++]="jquery_switch.css";
$estilo[$indice++]="dataTables.tableTools.css";
$estilo[$indice++]="dataTables.tableTools.min.css";
$estilo[$indice++]="jquery-ui.css";

// Tablas
$estilo[$indice++]="demo_page.css";
$estilo[$indice++]="demo_table.css";
$estilo[$indice++]="jquery.dataTables.css";
$estilo[$indice++]="jquery.dataTables.min.css";
$estilo[$indice++]="jquery.dataTables_themeroller.css";

$estilo[$indice++] = "buttons.bootstrap.css";
$estilo[$indice++] = "buttons.bootstrap.min.css";
$estilo[$indice++] = "buttons.dataTables.css";
$estilo[$indice++] = "buttons.dataTables.min.css";
$estilo[$indice++] = "buttons.foundation.css";
$estilo[$indice++] = "buttons.foundation.min.css";
$estilo[$indice++] = "buttons.jqueryui.css";
$estilo[$indice++] = "buttons.jqueryui.min.css";
$estilo[$indice++] = "common.scss";
$estilo[$indice++] = "mixins.scss";




$rutaBloque=$this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque.=$this->miConfigurador->getVariableConfiguracion("site");

if($unBloque["grupo"]==""){
	$rutaBloque.="/blocks/".$unBloque["nombre"];
}else{
	$rutaBloque.="/blocks/".$unBloque["grupo"]."/".$unBloque["nombre"];
}

foreach ($estilo as $nombre){
	echo "<link rel='stylesheet' type='text/css' href='".$rutaBloque."/css/".$nombre."'>\n";
}
?>
