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
    echo '{"suggestions":' . json_encode($resultado) . '}';
}


if ($_REQUEST ['funcion'] == 'grupo') {
    $parametro = $_REQUEST['query'];
    $cadenaSql = $this->sql->getCadenaSql('consultar_grupo_contable', $parametro);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'busqueda');


    foreach ($resultadoItems as $key => $values) {
        $keys = array('value', 'data');
        $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
    }
    echo '{"suggestions":' . json_encode($resultado) . '}';
}



if ($_REQUEST ['funcion'] == 'cuenta') {
    $parametro = $_REQUEST['query'];
    $cadenaSql = $this->sql->getCadenaSql('consultar_cuentasalida', $parametro);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'busqueda');


    foreach ($resultadoItems as $key => $values) {
        $keys = array('value', 'data');
        $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
    }
    echo '{"suggestions":' . json_encode($resultado) . '}';
}


if ($_REQUEST ['funcion'] == 'funcionario') {
    $parametro = $_REQUEST['query'];
    $cadenaSql = $this->sql->getCadenaSql('funcionarios', $parametro);
    $resultadoItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'busqueda');


    foreach ($resultadoItems as $key => $values) {
        $keys = array('value', 'data');
        $resultado[$key] = array_intersect_key($resultadoItems[$key], array_flip($keys));
    }
    echo '{"suggestions":' . json_encode($resultado) . '}';
}






if ($_REQUEST ['funcion'] == 'Consulta') {
    $arreglo = unserialize($_REQUEST['arreglo']);

    //var_dump($arreglo);
    $cadenaSql = $this->sql->getCadenaSql('mostrarInfoDepreciar', $arreglo);
    $items = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    $to = (int) count($items);
    for ($key = 0; $key < $to; $key++) {
        $fecha_nueva = $items[$key]['fecha_registro'];
        $meses = intval($items[$key]['grupo_vidautil']);
        $date = DateTime::createFromFormat("Y-m-d", $fecha_nueva);
        $numero = $date->format("d");

        if ($numero === '31') {
            $fecha_nueva = date('Y-m-d', (strtotime('-1 day', strtotime($items[$key]['fecha_registro']))));
        }

        $diferencia = (new \DateTime($fecha_nueva))->diff(new \DateTime($arreglo['fecha_corte']));
        $periodos_fecha = ($diferencia->y * 12 ) + $diferencia->m;
        $valor_ajustado = $items[$key]['valor'] + $items[$key]['ajuste_inflacionario'];

        if ($meses === 0) {
            $cuota = 0;
            $periodos_fecha = 0;
            $dep_acumulada = 0;
            $cuota_inflacion = 0;
            $api_acumulada = 0;
        } else {
            $cuota_inflacion = $items[$key]['ajuste_inflacionario'] / $meses;
            $items[$key]['valor_cuota'] = $items[$key]['valor'] / $meses;

            if ($periodos_fecha >= $meses) {
                $dep_acumulada = $items[$key]['valor'];
                $api_acumulada = $items[$key]['ajuste_inflacionario'];
            } else {
                $dep_acumulada = $items[$key]['valor_cuota'] * $periodos_fecha;
                $api_acumulada = $cuota_inflacion * $periodos_fecha;
            }
        }

        $circular56 = round($items[$key]['valor'] + $items[$key]['ajuste_inflacionario'], 2);
        $circular_depreciacion = round($api_acumulada + $dep_acumulada, 2);
        $valor_libros = round(($items[$key]['valor'] + $items[$key]['ajuste_inflacionario'] - ($api_acumulada + $dep_acumulada)), 2);

        $resultadoFinal[] = array(
            'placa' => "<center>" . $items[$key]['placa'] . "</center>",
            'cuenta' => "<center>" . $items[$key]['grupo_cuentasalida'] . "</center>",
            'grupo' => "<center>" . $items[$key]['grupo_nombre'] . "</center>",
            'meses_depreciar' => "<center>" . $meses . "</center>",
            'fechaSalida' => "<center>" . $items[$key]['fecha_registro'] . "</center>",
            'fechaCorte' => "<center>" . $arreglo['fecha_corte'] . "</center>",
            'periodos_fecha' => "<center>" . $periodos_fecha . "</center>",
            'valor_historico' => "<center>" . round($items[$key]['valor'], 2) . "</center>",
            'ajuste_inflacion' => "<center>" . round($items[$key]['ajuste_inflacionario'], 2) . "</center>",
            'valor_ajustado' => "<center>" . round($valor_ajustado, 2) . "</center>",
            'cuota' => "<center>" . round($items[$key]['valor_cuota'], 2) . "</center>",
            'depreciacion_acumulada' => "<center>" . round($dep_acumulada, 2) . "</center>",
            'circular_56' => "<center>" . $circular56 . "</center>",
            'cuota_inflacion' => "<center>" . round($cuota_inflacion, 2) . "</center>",
            'api_acumulada' => "<center>" . round($api_acumulada, 2) . "</center>",
            'circular_depreciacion' => "<center>" . $circular_depreciacion . "</center>",
            'valor_libros' => "<center>" . $valor_libros . "</center>",
        );
    }

    $total = count($resultadoFinal);
    $resultado = json_encode($resultadoFinal);
    $resultado = '{
                "startIndex":0, 
                "sort":null, 
                "dir":"asc", 
                "pageSize":10, 
                "recordsReturned":5, 
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
		"data":' . $resultado . '}';

    echo $resultado;
}
?>