<?php

use inventarios\reportesGenerales\reporteSalidas\Sql;

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

if ($_REQUEST ['funcion'] == 'consultarDependencia') {
    $cadenaSql = $this->sql->getCadenaSql('dependenciasConsultadas', $_REQUEST['valor']);
    $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    $resultado = json_encode($resultado);
    echo $resultado;
}

if ($_REQUEST ['funcion'] == 'consultarUbicacion') {
    $cadenaSql = $this->sql->getCadenaSql('ubicacionesConsultadas', $_REQUEST['valor']);
    $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    $resultado = json_encode($resultado);
    echo $resultado;
}

//var_dump($_REQUEST);
if ($_REQUEST ['funcion'] == 'consultaSalida') {
    $parametro = $_REQUEST['query'];
    $cadenaSql = $this->sql->getCadenaSql('buscar_salidas', $parametro);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'busqueda');

    foreach ($resultadoItems as $key => $values) {
        $keys = array('value', 'data');
        $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
    }
    echo '{"suggestions":' . json_encode($resultado) . '}';
}

if ($_REQUEST ['funcion'] == 'consultaEntrada') {
    $parametro = $_REQUEST['query'];
    $cadenaSql = $this->sql->getCadenaSql('buscar_entradas', $parametro);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'busqueda');

    foreach ($resultadoItems as $key => $values) {
        $keys = array('value', 'data');
        $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
    }
    echo '{"suggestions":' . json_encode($resultado) . '}';
}
?>
