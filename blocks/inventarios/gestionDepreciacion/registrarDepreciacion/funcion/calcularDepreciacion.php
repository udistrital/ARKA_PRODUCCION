<?php

namespace inventarios\gestionDepreciacion\registrarDepreciacion\funcion;

include_once ('redireccionar.php');
if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class calcularDepreciacion {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miFuncion;
    var $miSql;
    var $conexion;

    function __construct($lenguaje, $sql, $funcion) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
    }

    function calcularDepreciacionElementos($fecha_corte3) {

        $fechaActual = date('Y-m-d');

        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/inventarios/gestionActa/";
        $rutaBloque .= $esteBloque ['nombre'];
        $host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/inventarios/gestionActa/" . $esteBloque ['nombre'];

        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $fechaCorte = $fecha_corte3;

        for ($i = 0; $i <= 1000000; $i ++) {
            if (isset($_REQUEST ['item' . $i])) {
                $items[$i][0] = $_REQUEST ['item' . $i];
            }
        };

// Calculando la depreciacion
        foreach ($items as $key => $values) {

            $cadenaSql = $this->miSql->getCadenaSql('mostrarInfoDepreciar_elemento', $items[$key][0]);
            $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            $cantidad = 1;
            $meses = $elemento[0]['grupo_vidautil'];
            $precio = $elemento[0]['valor'];
            $inflacion = 0;

            $fecha_salida = new \DateTime($elemento[0]['fecha_registro']);
            $fecha_corte = new \DateTime($_REQUEST['fechaCorte']);

            $periodos = $fecha_corte->diff($fecha_salida);
            $meses_periodo = $periodos->format("%m");
            $años_periodo = $periodos->format("%y") * 12;

            $periodos_fecha1 = $meses_periodo + $años_periodo;

            $valor_historico = $precio * $cantidad;
            $valor_ajustado = $valor_historico + $inflacion;


//Valor de la Cuota
            if ($meses == 0) {
                $cuota = 0;
                $periodos_fecha = 0;
            } else {
                $cuota = $valor_historico / $meses;
                $periodos_fecha = $periodos_fecha1;
            }

//
//DEPRECIACION AUMULADA
            if ($periodos_fecha >= $meses) {
                $dep_acumulada = $valor_historico;
            } else {
                $dep_acumulada = $cuota * $periodos_fecha;
            }


//CIRCULAR 56
            $circular56 = $valor_historico;

//CUOTAS AJUSTES POR INFLACION -- el usuario determinó que no se realizan ajustes
            $cuota_inflacion = 0;

//AJUSTE POR INFLACION A LA DEPRECIACION ACUMULADA
            $api_acumulada = 0;

//CIRCULAR 56 - DEPRECIACIÓN
            $circular_depreciacion = $api_acumulada + $dep_acumulada;

//VALOR A LOS LIBROS
            $valor_libros = $valor_ajustado - $circular_depreciacion;

            $depreciacion_calculada[$key] = array(
                'id_elemento'=>$elemento[0]['id_elemento_ind'],
                'grupo_contable' => $elemento[0]['grupo'],
                'meses_depreciar' => $meses,
                'fechaSalida' => $elemento[0]['fecha_registro'],
                'fechaCorte' => $fechaCorte,
                'cantidad' => $cantidad,
                'precio' => $precio,
                'valor_historico' => $valor_historico,
                'valor_ajustado' => $valor_ajustado,
                'cuota' => $cuota,
                'periodos_fecha' => $periodos_fecha,
                'depreciacion_acumulada' => $dep_acumulada,
                'circular_56' => $circular56,
                'cuota_inflacion' => $cuota_inflacion,
                'api_acumulada' => $api_acumulada,
                'circular_depreciacion' => $circular_depreciacion,
                'valor_libros' => $valor_libros,
                'estado' => TRUE,
                'fregistro' => $fechaActual,
            );
        }

        return $depreciacion_calculada;
    }

}

$miRegistrador = new calcularDepreciacion($this->lenguaje, $this->sql, $this->funcion);
$resultado = $miRegistrador->calcularDepreciacionElementos();
?>
       