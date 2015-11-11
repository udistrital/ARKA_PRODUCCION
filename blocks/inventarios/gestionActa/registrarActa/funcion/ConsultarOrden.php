<?php

namespace inventarios\gestionActa\registrarActa\funcion;

use inventarios\gestionActa\registrarActa\funcion\redireccion;

include_once ('redireccionar.php');
if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class RegistradorOrden {

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

        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);


        // Revisar tipo de orden para saber qué consulta hacer

        $tipo_orden = $_REQUEST['tipoOrden'];

        switch ($tipo_orden) {
            case 1:
                $cadenaSql = $this->miSql->getCadenaSql('consultarOrdenServicios');
                $resultado_orden = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                break;

            case 2:
                $cadenaSql = $this->miSql->getCadenaSql('consultarOrdenCompra');
                $resultado_orden = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                break;

            case 3:
                $cadenaSql = $this->miSql->getCadenaSql('consultarOrdenOtros');
                $resultado_orden = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                break;

            default:
                break;
        }

        if ($resultado_orden == true) {
            redireccion::redireccionar('');
        }
        if ($_REQUEST ['obligacionesProveedor'] == '') {

            redireccion::redireccionar('noObligaciones');
        }

        if ($_REQUEST ['obligacionesContratista'] == '') {

            redireccion::redireccionar('noObligaciones');
        }

     
    }

    function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST [$clave]);
            }
        }
    }

}

$miRegistrador = new RegistradorOrden($this->lenguaje, $this->sql, $this->funcion);

$resultado = $miRegistrador->procesarFormulario();
?>