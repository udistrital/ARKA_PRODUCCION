<?php

namespace inventarios\gestionEntradas\consultaEntradas;

use inventarios\gestionEntradas\consultaEntradas\funcion;
use inventarios\gestionCompras\consultaOrdenCompra\funcion\redireccion;

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

        $arreglo = unserialize(base64_decode($_REQUEST['datos_entradas']));
        
        foreach ($arreglo as $key => $values) {
            $arreglo[$key]['estadoNuevo'] = $_REQUEST['estado'];
            $arreglo[$key]['estadoRegistro'] = ($_REQUEST['estado']==3)?'false':'true';
            $cadenaSql = $this->miSql->getCadenaSql('actualizarEstado', $arreglo[$key]);
            $modificar = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar",$arreglo[$key],"actualizarEstado");  

            if ($modificar == false) {
                \inventarios\gestionEntradas\consultaEntradas\funcion\redireccion::redireccionar('noInserto');
            }
        }

        
         
        if ($modificar != false) {
        	$this->miConfigurador->setVariableConfiguracion("cache",true);
            \inventarios\gestionEntradas\consultaEntradas\funcion\redireccion::redireccionar('inserto', false);
            exit;
        } else {
            \inventarios\gestionEntradas\consultaEntradas\funcion\redireccion::redireccionar('noInserto');
            exit;
        }
    }


}

$miRegistrador = new RegistradorOrden($this->lenguaje, $this->sql, $this->funcion);

$resultado = $miRegistrador->procesarFormulario();
?>