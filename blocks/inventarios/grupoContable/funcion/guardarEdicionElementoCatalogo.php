<?php

namespace arka\grupoContable\guardarEdicionElementoCatalogo;

if (!isset($GLOBALS["autorizado"])) {
    include("../index.php");
    exit;
}

class Formulario {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $sql;
    var $esteRecursoDB;

    function __construct($lenguaje, $formulario, $sql) {

        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->sql = $sql;

        $conexion = "inventarios";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        if (!$this->esteRecursoDB) {
            //Este se considera un error fatal
            exit;
        }
    }

    public function validarEntrada() {

        //validar request salida
        if (!isset($_REQUEST['cuentaSalida'])) {
          $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorSalida');
            $this->mensaje();
            exit;
        }
        //validar request nombre
        if (!isset($_REQUEST['nombreElemento'])) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorNombre');
            $this->mensaje();
            exit;
        }

        if (strlen($_REQUEST['nombreElemento']) > 50) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorLargoNombre');
            $this->mensaje();
            exit;
        }

        //validar request idCatalogo
        if (!isset($_REQUEST['idCatalogo'])) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorId');
            $this->mensaje();
            exit;
        }

        if (strlen($_REQUEST['idCatalogo']) > 50 || !is_numeric($_REQUEST['idCatalogo'])) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorValId');
            $this->mensaje();
            exit;
        }

        //validar request id
        if (!isset($_REQUEST['id'])) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorIdE');
            $this->mensaje();
            exit;
        }

        if (strlen($_REQUEST['id']) > 50 || !is_numeric($_REQUEST['id'])) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorValId');
            $this->mensaje();
            exit;
        }

        //validar request idPadre
        if (!isset($_REQUEST['idPadre'])) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorIdP');
            $this->mensaje();
            exit;
        }

        if (strlen($_REQUEST['idPadre']) > 50 || !is_numeric($_REQUEST['idPadre']) || strlen($_REQUEST['idReg']) > 50 || !is_numeric($_REQUEST['idReg'])) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorIdP');
            $this->mensaje();
            exit;
        }

        //validar catalogo existente
        $cadena_sql = $this->sql->getCadenaSql("buscarCatalogoId", $_REQUEST['idCatalogo']);
        $registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        if (!$registros) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorCatalogoExiste');
            $this->mensaje();
            exit;
        }
    }

    function guardarEdicionElementoCatalogo() {

        $datos = array(
            'idPadre' => $_REQUEST['idPadre'],
            'id' => $_REQUEST['id'],
            'idCatalogo' => $_REQUEST['idCatalogo'],
            'nombreElemento' => $_REQUEST['nombreElemento'],
            'idElemento' => $_REQUEST['idElementoEd'],
        );

        $cadena_sql = $this->sql->getCadenaSql("guardarEdicionElementoCatalogo", $datos);
        $registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");

        // para la descripción del grupo contable

        $datos2 = array(
            'idCuenta' => $registros[0][0],
            'cuentasalida' => (isset($_REQUEST['cuentaSalida']) ? $_REQUEST['cuentaSalida'] : ''),
            'cuentaentrada' => (isset($_REQUEST['cuentaEntrada']) ? $_REQUEST['cuentaEntrada'] : ''),
            'depreciacion' => (isset($_REQUEST['depreciacion']) ? $_REQUEST['depreciacion'] : ''),
            'vidautil' => (isset($_REQUEST['vidautil']) ? $_REQUEST['vidautil'] : ''),
            'cuentacredito' => (isset($_REQUEST['cuentaCredito']) ? $_REQUEST['cuentaCredito'] : '0'),
            'cuentadebito' => (isset($_REQUEST['cuentaDebito']) ? $_REQUEST['cuentaDebito'] : '0'),
        );
        
        $cadena_sql2 = $this->sql->getCadenaSql("guardarEdicionElementoCatalogoDescripcion", $datos2);
        $registros2 = $this->esteRecursoDB->ejecutarAcceso($cadena_sql2,"acceso",$datos2,"guardarEdicionElementoCatalogoDescripcion");
  
        if (!$registros2) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorCreacion');
            $this->mensaje();
            exit;
        }

        //consultar elementos 

        /*
          //buscarElemento Creado
          $cadena_sql = $this->sql->getCadenaSql("buscarElementoId","");
          $registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");

          if(!$registros){
          $this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorCatalogoExiste' );
          $this->mensaje();
          exit;
          }
          echo $registros[0][0];
         */
        echo $_REQUEST['idCatalogo'];
    }

    function mensaje() {

        // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion('mostrarMensaje');
        //$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );

        if ($mensaje) {

            $tipoMensaje = $this->miConfigurador->getVariableConfiguracion('tipoMensaje');

            if ($tipoMensaje == 'json') {

                $atributos ['mensaje'] = $mensaje;
                $atributos ['json'] = true;
            } else {
                $atributos ['mensaje'] = $this->lenguaje->getCadena($mensaje);
            }
            // -------------Control texto-----------------------
            $esteCampo = 'divMensaje';
            $atributos ['id'] = $esteCampo;
            $atributos ["tamanno"] = '';
            if ($tipoMensaje)
                $atributos ["estilo"] = $tipoMensaje;
            else
                $atributos ["estilo"] = 'information';
            $atributos ["etiqueta"] = '';
            $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje($atributos);
            unset($atributos);
        }

        return true;
    }

    function mensaje2($mensaje) {




        $atributos ['mensaje'] = $this->lenguaje->getCadena($mensaje);

        // -------------Control texto-----------------------
        $esteCampo = 'divMensaje';
        $atributos ['id'] = $esteCampo;
        $atributos ["tamanno"] = '';
        $atributos ["estilo"] = 'information';
        $atributos ["etiqueta"] = '';
        $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
        echo $this->miFormulario->campoMensaje($atributos);
        unset($atributos);




        return true;
    }

}

$miFormulario = new Formulario($this->lenguaje, $this->miFormulario, $this->sql);

$miFormulario->validarEntrada();
$miFormulario->guardarEdicionElementoCatalogo();
$miFormulario->mensaje();
?>