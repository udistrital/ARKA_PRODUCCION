<?php

namespace registro\loginArka;

use registro\loginArka\funcion\Redireccionador;

include_once ('Redireccionador.php');

// var_dump($_REQUEST);exit;
class FormProcessor {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;
    var $conexion;

    function __construct($lenguaje, $sql) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miSesion = \Sesion::singleton();
    }

    function procesarFormulario() {

        /**
         *
         * @todo lógica de procesamiento
         */
        $sesionActiva = $_REQUEST['sesion'];
        $borrarSesion = $this->miSesion->borrarValorSesion('TODOS', $sesionActiva);
        $terminarSesion=$this->miSesion->terminarSesion($sesionActiva);

        Redireccionador::redireccionar('paginaPrincipal', false);
    }

}

$miProcesador = new FormProcessor($this->lenguaje, $this->sql);

$resultado = $miProcesador->procesarFormulario();



