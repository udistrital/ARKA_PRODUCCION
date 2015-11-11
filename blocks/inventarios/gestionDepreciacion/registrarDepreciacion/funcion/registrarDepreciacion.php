<?php

namespace inventarios\gestionDepreciacion\registrarDepreciacion\funcion;

use inventarios\gestionDepreciacion\registrarDepreciacion\funcion\redireccion;

include_once ('redireccionar.php');
if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class RegistradorDepreciacion {

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

    function procesarFormulario() {


        $fechaActual = date('Y-m-d');

        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/inventarios/gestionActa/";
        $rutaBloque .= $esteBloque ['nombre'];
        $host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/inventarios/gestionActa/" . $esteBloque ['nombre'];

        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);


// Calculando la depreciacion

        $cantidad = 1;
        $meses = $_REQUEST['meses_depreciar'];
        $precio = $_REQUEST['precio'];
        $inflacion = 0;

        $fecha_salida = new \DateTime($_REQUEST['fechaSalida']);
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

                // Organizar contenido del registro
        $datos_depreciacion = array(
            'elemento_general' => (isset($_REQUEST['elemento_general']) ? $_REQUEST['elemento_general'] : ''),
            'grupo_contable' => (isset($_REQUEST['nivel']) ? $_REQUEST['nivel'] : ''),
            'meses_depreciar' => (isset($_REQUEST['meses_depreciar']) ? $_REQUEST['meses_depreciar'] : ''),
            'fechaSalida' => (isset($_REQUEST['fechaSalida']) ? $_REQUEST['fechaSalida'] : ''),
            'fechaCorte' => (isset($_REQUEST['fechaCorte']) ? $_REQUEST['fechaCorte'] : ''),
            'cantidad' => $cantidad,
            'precio' => number_format((float) (isset($_REQUEST['precio']) ? $_REQUEST['precio'] : ''), 2, '.', ''),
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
        

//        // Organizar contenido del registro
//        $datos_depreciacion = array(
//            'elemento_general' => (isset($_REQUEST['elemento_general']) ? $_REQUEST['elemento_general'] : ''),
//            'grupo_contable' => (isset($_REQUEST['nivel']) ? $_REQUEST['nivel'] : ''),
//            'meses_depreciar' => (isset($_REQUEST['meses_depreciar']) ? $_REQUEST['meses_depreciar'] : ''),
//            'fechaSalida' => (isset($_REQUEST['fechaSalida']) ? $_REQUEST['fechaSalida'] : ''),
//            'fechaCorte' => (isset($_REQUEST['fechaCorte']) ? $_REQUEST['fechaCorte'] : ''),
//            'cantidad' => (isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : ''),
//            'precio' => number_format((float) (isset($_REQUEST['precio']) ? $_REQUEST['precio'] : ''), 2, '.', ''),
//            'valor_historico' => number_format((float) (isset($_REQUEST['valor_historico']) ? $_REQUEST['valor_historico'] : ''), 2, '.', ''),
//            'valor_ajustado' => number_format((float) (isset($_REQUEST['valor_ajustado']) ? $_REQUEST['valor_ajustado'] : ''), 2, '.', ''),
//            'cuota' => number_format((float) (isset($_REQUEST['cuota']) ? $_REQUEST['cuota'] : ''), 2, '.', ''),
//            'periodos_fecha' => (isset($_REQUEST['periodos_fecha']) ? $_REQUEST['periodos_fecha'] : ''),
//            'depreciacion_acumulada' => number_format((float) (isset($_REQUEST['depreciacion_acumulada']) ? $_REQUEST['depreciacion_acumulada'] : ''), 2, '.', ''),
//            'circular_56' => number_format((float) (isset($_REQUEST['circular_56']) ? $_REQUEST['circular_56'] : ''), 2, '.', ''),
//            'cuota_inflacion' => number_format((float) (isset($_REQUEST['cuota_inflacion']) ? $_REQUEST['cuota_inflacion'] : ''), 2, '.', ''),
//            'api_acumulada' => number_format((float) (isset($_REQUEST['api_acumulada']) ? $_REQUEST['api_acumulada'] : ''), 2, '.', ''),
//            'circular_depreciacion' => number_format((float) (isset($_REQUEST['circular_depreciacion']) ? $_REQUEST['circular_depreciacion'] : ''), 2, '.', ''),
//            'valor_libros' => number_format((float) (isset($_REQUEST['valor_libros']) ? $_REQUEST['valor_libros'] : ''), 2, '.', ''),
//            'estado' => TRUE,
//            'fregistro' => $fechaActual,
//        );


        $cadenaSql = $this->miSql->getCadenaSql('registrarDepreciacion', $datos_depreciacion);
        $registro_depreciacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");


        $datos = array(
            'elemento_general' => (isset($_REQUEST['elemento_general']) ? $_REQUEST['elemento_general'] : ''),
        );
//inactivar item para asignar
        if ($registro_depreciacion == true) {
            redireccion::redireccionar('inserto', $datos);
        } else {
            redireccion::redireccionar('noInserto', $datos);
        }
    }

}

$miRegistrador = new RegistradorDepreciacion($this->lenguaje, $this->sql, $this->funcion);

$resultado = $miRegistrador->procesarFormulario();
?>
       