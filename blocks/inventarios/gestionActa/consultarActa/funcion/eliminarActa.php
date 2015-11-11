<?php

namespace inventarios\gestionActa\consultarActa\funcion;

use inventarios\gestionActa\consultarActa\funcion\redireccion;

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

        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/inventarios/gestionActa/";
        $rutaBloque .=$esteBloque ['nombre'];
        $host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/inventarios/gestionActa/consultarActa/";

        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('items', $_REQUEST ['seccion']);
        $items = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");


        $fechaActual = date('Y-m-d');
        $datosActa = $_REQUEST['numero_acta'];

        //identificar el acta, e inactivarla
        $cadenaSql = $this->miSql->getCadenaSql('consultarActaM', $datosActa);
        $registro_acta = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $estado_registro = $registro_acta[0]['estado_registro'];

        if ($estado_registro == 1) {
            $cadenaSql = $this->miSql->getCadenaSql('inactivarActa', $datosActa);
            $registro_acta = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        }

        //identificar los items, e inactivarlos
         $cadenaSql = $this->miSql->getCadenaSql('inactivarItems', $datosActa);
            $items = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");

            $datosActa=array($datosActa,$_REQUEST['arreglo']);
//redireccionar
        if ($items == 1) {
$this->miConfigurador->setVariableConfiguracion("cache",true);
            redireccion::redireccionar('elimino', $datosActa);
            exit();
        } else {

            redireccion::redireccionar('noElimino', $datosActa);
            exit();
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