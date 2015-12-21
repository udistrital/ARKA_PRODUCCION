<?php

use inventarios\gestionDepreciacion\depreciacionGeneral\Sql;

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

if ($_REQUEST ['funcion'] == 'Consulta') {

    $arreglo = unserialize($_REQUEST['arreglo']);
    $cadenaSql = $this->sql->getCadenaSql('mostrarInfoDepreciar', $arreglo);
    $items = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    $arr = array();

    foreach ($items as $key => &$entry) {
        $arr[$entry['grupo_cuentasalida']][$key] = $entry;
    }

    $a = 0;
    $cuota_inflacion = 0;
// Calculando la depreciacion
    $to2 = (int) count($arr);

    foreach ($arr as $llave => $items) {
        $total_valorhistorico = 0;
        $total_ajusteinflacion = 0;
        $total_valorajustado = 0;
        $total_depreciacion = 0;
        $total_api = 0;
        $total_dep56 = 0;
        $total_libros = 0;
        $count = 0;

        foreach ($items as $key => $values) {
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

            $circular56 = $items[$key]['valor'] + $items[$key]['ajuste_inflacionario'];
            $circular_depreciacion = $api_acumulada + $dep_acumulada;
            $valor_libros = ($items[$key]['valor'] + $items[$key]['ajuste_inflacionario'] - ($api_acumulada + $dep_acumulada));

            //totalizar los valores de los elementos
            $total_valorhistorico = $total_valorhistorico + $items[$key]['valor'];
            $total_ajusteinflacion = $total_ajusteinflacion + $items[$key]['ajuste_inflacionario'];
            $total_valorajustado = $total_valorajustado + $valor_ajustado;
            $total_depreciacion = $total_depreciacion + $dep_acumulada;
            $total_api = $total_api + $api_acumulada;
            $total_dep56 = $total_dep56 + $circular_depreciacion;
            $total_libros = $total_libros + $valor_libros;
            $nombre_cuenta = $items[$key]['grupo_nombre'];



            $count ++;
        }

        $codificar[$llave] = array(
            'cuenta' => $llave,
            'grupo' => $nombre_cuenta,
            'total_elementos' => $count,
            'total_valor_historico' => $total_valorhistorico,
            'total_ajuste_inflacion' => $total_ajusteinflacion,
            'total_valor_ajustado' => $total_valorajustado,
            'total_depreciacion' => $total_depreciacion,
            'total_ajuste_inflacionario' => $total_api,
            'total_depreciacion_circular56' => $total_dep56,
            'total_libros' => $total_libros,
        );

        $resultadoFinal[] = array(
            'cuenta' => "<center>" . $llave . "</center>",
            'grupo' => "<center>" . $nombre_cuenta . "</center>",
            'total_elementos' => "<center>" . $count . "</center>",
            'total_valor_historico' => "<center>" . number_format($total_valorhistorico, 2, ',', '.') . "</center>",
            'total_ajuste_inflacion' => "<center>" . number_format($total_ajusteinflacion, 2, ',', '.') . "</center>",
            'total_valor_ajustado' => "<center>" . number_format($total_valorajustado, 2, ',', '.') . "</center>",
            'total_depreciacion' => "<center>" . number_format($total_depreciacion, 2, ',', '.') . "</center>",
            'total_ajuste_inflacionario' => "<center>" . number_format($total_api, 2, ',', '.') . "</center>",
            'total_depreciacion_circular56' => "<center>" . number_format($total_dep56, 2, ',', '.') . "</center>",
            'total_libros' => "<center>" . number_format($total_libros, 2, ',', '.') . "</center>",
        );
    }

    $codificado = array(
        'json' => json_encode($codificar),
        'fecha_corte' => $arreglo['fecha_corte'],
        'estado' => 0,
        'registro' => date('Y-m-d')
    );

    $cadenaSql = $this->sql->getCadenaSql('insertarDepreciacion', $codificado);
    $tmp = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'insertar', $arreglo);


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