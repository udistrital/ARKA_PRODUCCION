<?php

namespace inventarios\radicarEntradaSalida\funcion;

use inventarios\radicarEntradaSalida\funcion\redireccion;

include_once ('redireccionar.php');
if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class RegistradorAvance {

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

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/inventarios/";
        $rutaBloque .= $esteBloque ['nombre'];
        $host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/inventarios/" . $esteBloque ['nombre'];

        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);


        if ($_REQUEST ['identrada'] == '') {
            redireccion::redireccionar('noDatos');
        }
        
         if ($_REQUEST ['revisor'] == '') {
            redireccion::redireccionar('noDatos');
        }
        
         if ($_REQUEST ['firma'] == '') {
            redireccion::redireccionar('noDatos');
        }

        //Registro del Radicar Entrada Salida

        $datosRadicado = array(
            $_REQUEST ['identrada'],
            $_REQUEST ['revisor'],
            $_REQUEST ['firma'],
            $fechaActual,
            1,
        );

       $cadenaSql = $this->miSql->getCadenaSql('registrarRadicarES', $datosRadicado);
        $id_radicar = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
    
            $datos = array(
            $id_radicar[0][0],
            $fechaActual
        );


        if (!empty($id_radicar)) {
            redireccion::redireccionar('inserto', $datos);
        } else {
            redireccion::redireccionar('noInserto', $datos);
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

$miRegistrador = new RegistradorAvance($this->lenguaje, $this->sql, $this->funcion);

$resultado = $miRegistrador->procesarFormulario();
?>