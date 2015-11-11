<?php

use inventarios\gestionDepreciacion\registrarDepreciacion\Sql;

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

if ($_REQUEST ['funcion'] == 'SeleccionMeses') {
    $cadenaSql = $this->sql->getCadenaSql('consultar_meses', $_REQUEST['contable']);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
    $resultado = json_encode($resultadoItems[0]);
    echo $resultado;
}
?>