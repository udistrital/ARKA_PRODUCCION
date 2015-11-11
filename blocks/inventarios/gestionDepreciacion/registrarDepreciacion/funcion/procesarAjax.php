<?php

use inventarios\gestionDepreciacion\registrarDepreciacion\Sql;

$conexion = 'inventarios';
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

if ($_REQUEST ['funcion'] == 'SeleccionMeses') {
    $cadenaSql = $this->sql->getCadenaSql('consultar_meses', $_REQUEST['contable']);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'busqueda');
    $resultado = json_encode($resultadoItems[0]);
    echo $resultado;
}

if ($_REQUEST ['funcion'] == 'placas') {
    $parametro = $_REQUEST['query'];
    $cadenaSql = $this->sql->getCadenaSql('buscar_placa', $parametro);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'busqueda');


    foreach ($resultadoItems as $key => $values) {
        $keys = array('value', 'data');
        $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
    }

//    var_dump($resultado);


    echo '{"suggestions":'.json_encode($resultado).'}';
}
?>