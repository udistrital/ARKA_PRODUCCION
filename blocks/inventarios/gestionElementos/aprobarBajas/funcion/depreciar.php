<?php

use inventarios\gestionDepreciacion\registrarDepreciacion\Sql;

$conexion = 'inventarios';
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

$cadenaSql = $this->miSql->getCadenaSql('mostrarInfoDepreciar', $datos);
$items = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

if ($items) {
    $a = 0;
// Calculando la depreciacion
    foreach ($items as $key => $values) {

        $cadenaSql = $this->miSql->getCadenaSql('mostrarInfoDepreciar_elemento', $items[$key][0]);
        $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cantidad = 1;
        $meses = intval($elemento[0]['grupo_vidautil']);
        $precio = $elemento[0]['valor'];
        $inflacion = $elemento[0]['ajuste_inflacionario'];

        $fecha_salida = new \DateTime($elemento[0]['fecha_registro']);
        $fecha_corte = new \DateTime($_REQUEST['fechaCorte']);
        $fecha_limite = new \DateTime('1999-12-31');

        $periodos = $fecha_corte->diff($fecha_salida);
        $meses_periodo = $periodos->format("%m");
        $años_periodo = $periodos->format("%y") * 12;

        $periodos_fecha1 = $meses_periodo + $años_periodo + 1;

        $valor_historico = $precio * $cantidad;

        if ($fecha_salida <= $fecha_limite) {
            $inflacion = $inflacion;
        } else {
            $inflacion = 0;
        }

        $valor_ajustado = $valor_historico + $inflacion;

//Valor de la Cuota
        if ($meses == 0) {
            $cuota = 0;
            $periodos_fecha = 0;
        } else {
            $cuota = $valor_historico / $meses;
            $periodos_fecha = $periodos_fecha1;
        }


//DEPRECIACION AUMULADA
        if ($meses !== 0) {
            if ($periodos_fecha >= $meses) {

                $dep_acumulada = $valor_historico;
            } else {
                $dep_acumulada = $cuota * $periodos_fecha;
            }
        } else {
            $dep_acumulada = 0;
        }

//CIRCULAR 56
        $circular56 = $valor_historico + $inflacion;


//CUOTAS AJUSTES POR INFLACION 
        if ($meses == 0) {
            $cuota_inflacion = 0;
        } else {
            if ($inflacion > 0) {
                $cuota_inflacion = $inflacion / $meses;
            } else {
                $cuota_inflacion = 0;
            }
        }

//AJUSTE POR INFLACION A LA DEPRECIACION ACUMULADA
        if ($meses !== 0) {
            if ($periodos_fecha >= $meses) {
                $api_acumulada = $inflacion;
            } else {
                $api_acumulada = $cuota_inflacion * $periodos_fecha;
            }
        } else {
            $api_acumulada = 0;
        }


//CIRCULAR 56 - DEPRECIACIÓN
        $circular_depreciacion = $api_acumulada + $dep_acumulada;

//VALOR A LOS LIBROS
        $valor_libros = $valor_ajustado - $circular_depreciacion;

        $depreciacion_calculada[$a] = array(
            0 => $elemento[0]['placa'],
            'placa' => $elemento[0]['placa'],
            1 => $elemento[0]['grupo_codigo'],
            'cuenta' => $elemento[0]['grupo_codigo'],
            2 => $elemento[0]['elemento_nombre'],
            'grupo' => $elemento[0]['elemento_nombre'],
            3 => $meses,
            'meses_depreciar' => $meses,
            4 => $elemento[0]['fecha_registro'],
            'fechaSalida' => $elemento[0]['fecha_registro'],
            5 => $_REQUEST['fechaCorte'],
            'fechaCorte' => $_REQUEST['fechaCorte'],
            6 => $periodos_fecha,
            'periodos_fecha' => $periodos_fecha,
            7 => $valor_historico,
            'valor_historico' => $valor_historico,
            8 => $valor_ajustado,
            'valor_ajustado' => $valor_ajustado,
            9 => $cuota,
            'cuota' => $cuota,
            10 => $dep_acumulada,
            'depreciacion_acumulada' => $dep_acumulada,
            11 => $circular56,
            'circular_56' => $circular56,
            12 => $cuota_inflacion,
            'cuota_inflacion' => $cuota_inflacion,
            13 => $api_acumulada,
            'api_acumulada' => $api_acumulada,
            14 => $circular_depreciacion,
            'circular_depreciacion' => $circular_depreciacion,
            15 => $valor_libros,
            'valor_libros' => $valor_libros,
        );

        $depreciacion_reporte[$a] = array(
            0 => $elemento[0]['grupo_cuentasalida'],
            'cuenta' => $elemento[0]['grupo_cuentasalida'],
            1 => $elemento[0]['elemento_nombre'],
            'grupo' => $elemento[0]['elemento_nombre'],
            2 => $elemento[0]['placa'],
            'placa' => $elemento[0]['placa'],
            3 => $elemento[0]['descripcion'],
            'nombre_elemento' => $elemento[0]['descripcion'],
            4 => $meses,
            'meses_depreciar' => $meses,
            5 => $elemento[0]['fecha_registro'],
            'fechaSalida' => $elemento[0]['fecha_registro'],
            6 => $_REQUEST['fechaCorte'],
            'fechaCorte' => $_REQUEST['fechaCorte'],
            7 => $periodos_fecha,
            'periodos_fecha' => $periodos_fecha,
            8 => number_format($valor_historico, 2, ',', '.'),
            'valor_historico' => number_format($valor_historico, 2, ',', '.'),
            9 => number_format($valor_ajustado, 2, ',', '.'),
            'valor_ajustado' => number_format($valor_ajustado, 2, ',', '.'),
            10 => number_format($cuota, 2, ',', '.'),
            'cuota' => number_format($cuota, 2, ',', '.'),
            11 => number_format($dep_acumulada, 2, ',', '.'),
            'depreciacion_acumulada' => number_format($dep_acumulada, 2, ',', '.'),
            12 => number_format($circular56, 2, ',', '.'),
            'circular_56' => number_format($circular56, 2, ',', '.'),
            13 => number_format($cuota_inflacion, 2, ',', '.'),
            'cuota_inflacion' => number_format($cuota_inflacion, 2, ',', '.'),
            14 => number_format($api_acumulada, 2, ',', '.'),
            'ajuste_inflacionario' => number_format($api_acumulada, 2, ',', '.'),
            15 => number_format($circular_depreciacion, 2, ',', '.'),
            'circular_depreciacion' => number_format($circular_depreciacion, 2, ',', '.'),
            16 => number_format($valor_libros, 2, ',', '.'),
            'valor_libros' => number_format($valor_libros, 2, ',', '.'),
        );
        $a++;
    }
}
