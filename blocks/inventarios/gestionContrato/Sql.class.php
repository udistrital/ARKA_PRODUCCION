<?php

namespace inventarios\gestionContrato;

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

//Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
//en camel case precedida por la palabra sql

class Sql extends \Sql {

    var $miConfigurador;

    function __construct() {

        $this->miConfigurador = \Configurador::singleton();
    }

    function cadena_sql($tipo, $variable = "") {

        /**
         * 1.
         * Revisar las variables para evitar SQL Injection
         */
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

        switch ($tipo) {

            case 'registroDocumento' :
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= 'arka_inventarios.registro_documento';
                $cadenaSql .= '( ';
                $cadenaSql .= 'documento_nombre,';
                $cadenaSql .= 'documento_idunico,';
                $cadenaSql .= 'documento_fechar,';
                $cadenaSql .= 'documento_ruta,';
                $cadenaSql .= 'documento_estado';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $variable['nombre_archivo'] . '\', ';
                $cadenaSql .= '\'' . $variable['id_unico'] . '\', ';
                $cadenaSql .= '\'' . $variable['fecha_registro'] . '\', ';
                $cadenaSql .= '\'' . $variable['ruta'] . '\', ';
                $cadenaSql .= '\'' . $variable['estado'] . '\'';
                $cadenaSql .= ') ';
                break;

            case 'consultarContrato':
                $cadenaSql = 'SELECT  ';
                $cadenaSql .= ' documento_nombre, ';
                $cadenaSql .= ' documento_fechar, ';
                $cadenaSql .= ' documento_ruta, ';
                $cadenaSql .= ' documento_estado, ';
                $cadenaSql .= ' documento_idunico ';
                $cadenaSql .= ' FROM ';
                $cadenaSql .= 'arka_inventarios.registro_documento';
            if ($variable != '') {
                   $cadenaSql .= ' WHERE documento_fechar=';
                   $cadenaSql .= '\'' . $variable . '\' ';
                }
               
                
                break;

            case 'actualizarDocumento' :
                $cadenaSql = 'UPDATE arka_inventarios.registro_documento SET ';
                $cadenaSql .= 'documento_nombre=\'' . $variable['nombre_archivo'] . '\',';
                $cadenaSql .= 'documento_idunico=\'' . $variable['id_unico'] . '\',';
                $cadenaSql .= 'documento_fechar=\'' . $variable['fecha_registro'] . '\',';
                $cadenaSql .= 'documento_ruta=\'' . $variable['ruta'] . '\',';
                $cadenaSql .= 'documento_estado=\'' . $variable['estado'] . '\'';
                $cadenaSql .= ' WHERE documento_idunico=';
                $cadenaSql .= '\'' . $variable['anterior_documento'] . '\' ';
                break;
            /**
             * Clausulas específicas
             */
            case "buscarUsuario" :
                $cadenaSql = "SELECT ";
                $cadenaSql .= "FECHA_CREACION, ";
                $cadenaSql .= "PRIMER_NOMBRE ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= "USUARIOS ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "`PRIMER_NOMBRE` ='" . $variable . "' ";
                break;

            case "insertarRegistro" :
                $cadenaSql = "INSERT INTO ";
                $cadenaSql .= $prefijo . "registradoConferencia ";
                $cadenaSql .= "( ";
                $cadenaSql .= "`idRegistrado`, ";
                $cadenaSql .= "`nombre`, ";
                $cadenaSql .= "`apellido`, ";
                $cadenaSql .= "`identificacion`, ";
                $cadenaSql .= "`codigo`, ";
                $cadenaSql .= "`correo`, ";
                $cadenaSql .= "`tipo`, ";
                $cadenaSql .= "`fecha` ";
                $cadenaSql .= ") ";
                $cadenaSql .= "VALUES ";
                $cadenaSql .= "( ";
                $cadenaSql .= "NULL, ";
                $cadenaSql .= "'" . $variable ['nombre'] . "', ";
                $cadenaSql .= "'" . $variable ['apellido'] . "', ";
                $cadenaSql .= "'" . $variable ['identificacion'] . "', ";
                $cadenaSql .= "'" . $variable ['codigo'] . "', ";
                $cadenaSql .= "'" . $variable ['correo'] . "', ";
                $cadenaSql .= "'0', ";
                $cadenaSql .= "'" . time() . "' ";
                $cadenaSql .= ")";
                break;

            case "actualizarRegistro" :
                $cadenaSql = "UPDATE ";
                $cadenaSql .= $prefijo . "conductor ";
                $cadenaSql .= "SET ";
                $cadenaSql .= "`nombre` = '" . $variable ["nombre"] . "', ";
                $cadenaSql .= "`apellido` = '" . $variable ["apellido"] . "', ";
                $cadenaSql .= "`identificacion` = '" . $variable ["identificacion"] . "', ";
                $cadenaSql .= "`telefono` = '" . $variable ["telefono"] . "' ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "`idConductor` =" . $_REQUEST ["registro"] . " ";
                break;

            /**
             * Clausulas genéricas.
             * se espera que estén en todos los formularios
             * que utilicen esta plantilla
             */
            case "iniciarTransaccion" :
                $cadenaSql = "START TRANSACTION";
                break;

            case "finalizarTransaccion" :
                $cadenaSql = "COMMIT";
                break;

            case "cancelarTransaccion" :
                $cadenaSql = "ROLLBACK";
                break;

            case "eliminarTemp" :

                $cadenaSql = "DELETE ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= $prefijo . "tempFormulario ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "id_sesion = '" . $variable . "' ";
                break;

            case "insertarTemp" :
                $cadenaSql = "INSERT INTO ";
                $cadenaSql .= $prefijo . "tempFormulario ";
                $cadenaSql .= "( ";
                $cadenaSql .= "id_sesion, ";
                $cadenaSql .= "formulario, ";
                $cadenaSql .= "campo, ";
                $cadenaSql .= "valor, ";
                $cadenaSql .= "fecha ";
                $cadenaSql .= ") ";
                $cadenaSql .= "VALUES ";

                foreach ($_REQUEST as $clave => $valor) {
                    $cadenaSql .= "( ";
                    $cadenaSql .= "'" . $idSesion . "', ";
                    $cadenaSql .= "'" . $variable ['formulario'] . "', ";
                    $cadenaSql .= "'" . $clave . "', ";
                    $cadenaSql .= "'" . $valor . "', ";
                    $cadenaSql .= "'" . $variable ['fecha'] . "' ";
                    $cadenaSql .= "),";
                }

                $cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
                break;

            case "rescatarTemp" :
                $cadenaSql = "SELECT ";
                $cadenaSql.= "id_sesion, ";
                $cadenaSql.= "formulario, ";
                $cadenaSql.= "campo, ";
                $cadenaSql.= "valor, ";
                $cadenaSql.= "fecha ";
                $cadenaSql.= "FROM ";
                $cadenaSql.= $prefijo . "tempFormulario ";
                $cadenaSql.= "WHERE ";
                $cadenaSql.= "id_sesion='" . $idSesion . "'";
                break;
        }

        return $cadenaSql;
    }

}

?>
