<?php

namespace reportes\reportico;

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {

    var $miConfigurador;

    function __construct() {
        $this->miConfigurador = \Configurador::singleton();
    }

    function getCadenaSql($tipo, $variable = "") {

        /**
         * 1.
         * Revisar las variables para evitar SQL Injection
         */
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

        switch ($tipo) {

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
                $cadenaSql .= "id_sesion, ";
                $cadenaSql .= "formulario, ";
                $cadenaSql .= "campo, ";
                $cadenaSql .= "valor, ";
                $cadenaSql .= "fecha ";
                $cadenaSql .= "FROM ";
                $cadenaSql .= $prefijo . "tempFormulario ";
                $cadenaSql .= "WHERE ";
                $cadenaSql .= "id_sesion='" . $idSesion . "'";
                break;

            /**
             * Clausulas Del Caso Uso.
             */
            case "actualizarItems" :
                $cadenaSql = " INSERT INTO ";
                $cadenaSql .= " arka_inventarios.items_actarecibido(";
                $cadenaSql .= " id_acta, item,  descripcion,cantidad, ";
                $cadenaSql .= " valor_unitario, valor_total, estado_registro, fecha_registro)";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= "'" . $variable [0] . "',";
                $cadenaSql .= "'" . $variable [1] . "',";
                $cadenaSql .= "'" . $variable [2] . "',";
                $cadenaSql .= "'" . $variable [3] . "',";
                $cadenaSql .= "'" . $variable [4] . "',";
                $cadenaSql .= "'" . $variable [5] . "',";
                $cadenaSql .= "'1',";
                $cadenaSql .= "'" . date('Y-m-d') . "');";
                break;


            case "consultarItems" :
                $cadenaSql = " SELECT ";
                $cadenaSql .= " id_items,";
                $cadenaSql .= " item, ";
                $cadenaSql .= " cantidad, ";
                $cadenaSql .= " descripcion, ";
                $cadenaSql .= " valor_unitario, ";
                $cadenaSql .= " valor_total";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.items_actarecibido ";
                $cadenaSql .= " WHERE id_acta='" . $variable . "';";
                break;

            case "id_items_temporal" :
                $cadenaSql = " SELECT ";
                $cadenaSql .= " max(id_items)";
                $cadenaSql .= " FROM arka_inventarios.items_actarecibido_temp;";
                break;

            case "items2" :
                $cadenaSql = " SELECT ";
                $cadenaSql .= " id_items,";
                $cadenaSql .= " item, ";
                $cadenaSql .= " cantidad, ";
                $cadenaSql .= " descripcion, ";
                $cadenaSql .= " valor_unitario, ";
                $cadenaSql .= " valor_total";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.items_actarecibido ";
                $cadenaSql .= " WHERE id_acta='" . $variable['tiempo'] . "';";
                break;

            case "items" :
                $cadenaSql = " SELECT ";
                $cadenaSql .= " id_items,";
                $cadenaSql .= " item, ";
                $cadenaSql .= " cantidad, ";
                $cadenaSql .= " descripcion, ";
                $cadenaSql .= " valor_unitario, ";
                $cadenaSql .= " valor_total";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.items_actarecibido_temp ";
                $cadenaSql .= " WHERE seccion='" . $variable . "';";
                break;

            case "insertarItem" :
                $cadenaSql = " INSERT INTO ";
                $cadenaSql .= " arka_inventarios.items_actarecibido_temp(";
                $cadenaSql .= " id_items,item,descripcion,cantidad, ";
                $cadenaSql .= " valor_unitario,valor_total,seccion)";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= "'" . $variable [0] . "',";
                $cadenaSql .= "'" . $variable [1] . "',";
                $cadenaSql .= "'" . $variable [2] . "',";
                $cadenaSql .= "'" . $variable [3] . "',";
                $cadenaSql .= "'" . $variable [4] . "',";
                $cadenaSql .= "'" . $variable [5] . "',";
                $cadenaSql .= "'" . $variable [6] . "');";
                break;

            case "insertarItems" :
                $cadenaSql = " INSERT INTO ";
                $cadenaSql .= " arka_inventarios.items_actarecibido(";
                $cadenaSql .= " id_acta, item,  descripcion,cantidad, ";
                $cadenaSql .= " valor_unitario, valor_total, estado_registro, fecha_registro)";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= "'" . $variable [0] . "',";
                $cadenaSql .= "'" . $variable [1] . "',";
                $cadenaSql .= "'" . $variable [2] . "',";
                $cadenaSql .= "'" . $variable [3] . "',";
                $cadenaSql .= "'" . $variable [4] . "',";
                $cadenaSql .= "'" . $variable [5] . "',";
                $cadenaSql .= "'1',";
                $cadenaSql .= "'" . date('Y-m-d') . "');";
                break;

            case "inactivarItems" :
                $cadenaSql = " UPDATE ";
                $cadenaSql .= " arka_inventarios.items_actarecibido ";
                $cadenaSql .= " SET ";
                $cadenaSql .= " estado_registro='0',";
                $cadenaSql .= " fecha_registro='".date('Y-m-d')."'";
                $cadenaSql .= " WHERE id_acta='".$variable."'";
                break;

            case "insertarItemTemporal" :
                $cadenaSql = " INSERT INTO ";
                $cadenaSql .= " arka_inventarios.items_actarecibido_temp(";
                $cadenaSql .= " id_items,item,descripcion,cantidad, ";
                $cadenaSql .= " valor_unitario,valor_total,seccion)";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= "'" . $variable [0] . "',";
                $cadenaSql .= "'" . $variable [1] . "',";
                $cadenaSql .= "'" . $variable [2] . "',";
                $cadenaSql .= "'" . $variable [3] . "',";
                $cadenaSql .= "'" . $variable [4] . "',";
                $cadenaSql .= "'" . $variable [5] . "',";
                $cadenaSql .= "'" . $variable ['tiempo'] . "');";
                break;

            case "eliminarItem" :
                $cadenaSql = " DELETE FROM ";
                $cadenaSql .= " arka_inventarios.items_actarecibido_temp";
                $cadenaSql .= " WHERE id_items ='" . $variable . "';";
                break;

            case "limpiarItems" :
                $cadenaSql = " DELETE FROM ";
                $cadenaSql .= " arka_inventarios.items_actarecibido";
                $cadenaSql .= " WHERE id_acta ='" . $variable . "';";
                break;

            case "limpiar_tabla_items" :
                $cadenaSql = " DELETE FROM ";
                $cadenaSql .= " arka_inventarios.items_actarecibido_temp";
                $cadenaSql .= " WHERE seccion ='" . $variable . "';";
                break;


            // _________________________________________________update___________________________________________


            case "actualizarActa" :
                $cadenaSql = " UPDATE ";
                $cadenaSql .= " registro_actarecibido ";
                $cadenaSql .= " SET ";
                $cadenaSql .= "dependencia='" . $variable [0] . "',";
                $cadenaSql .= "fecha_recibido='" . $variable [1] . "',";
                $cadenaSql .= "tipo_bien='" . $variable [2] . "',";
                $cadenaSql .= "nitproveedor='" . $variable [3] . "',";
                $cadenaSql .= "proveedor='" . $variable [4] . "',";
                $cadenaSql .= "numfactura='" . $variable [5] . "',";
                $cadenaSql .= "fecha_factura='" . $variable [6] . "',";
                $cadenaSql .= "tipocomprador='" . $variable [7] . "',";
                $cadenaSql .= "tipoaccion='" . $variable [8] . "',";
                $cadenaSql .= "fecha_revision='" . $variable [9] . "',";
                $cadenaSql .= "revisor='" . $variable [10] . "',";
                $cadenaSql .= "observacionesacta='" . $variable [11] . "',";
                $cadenaSql .= "estado_registro='" . $variable [12] . "',";
                $cadenaSql .= "fecha_registro='" . $variable [1] . "' ";
                $cadenaSql .= " WHERE id_actarecibido = '" . $variable [13] . "' ";
                $cadenaSql .= "RETURNING id_actarecibido";
                break;

            case "consultarActa" :

                $cadenaSql = "SELECT DISTINCT ";
                $cadenaSql .= " id_actarecibido, dependencia, fecha_recibido, tb_descripcion, nitproveedor, ";
                $cadenaSql .= " proveedor, numfactura, fecha_factura, tc_descripcion, ta_descripcion, ";
                $cadenaSql .= " fecha_revision, revisor, observacionesacta ";
                $cadenaSql .= "FROM registro_actarecibido ";
                $cadenaSql .= "JOIN tipo_accion ON tipo_accion.ta_idaccion = registro_actarecibido.tipoaccion ";
                $cadenaSql .= "JOIN tipo_bien ON tipo_bien.tb_idbien = registro_actarecibido.tipo_bien ";
                $cadenaSql .= "JOIN tipo_comprador ON tipo_comprador.tc_idcomprador = registro_actarecibido.tipocomprador ";
                $cadenaSql .= "WHERE 1 = 1";
                $cadenaSql .= "AND estado_registro = 1";
                if ($variable [0] != '') {
                    $cadenaSql .= " AND id_actarecibido = '" . $variable [0] . "'";
                }
                if ($variable [1] != '') {
                    $cadenaSql .= " AND fecha_recibido = '" . $variable [1] . "'";
                }
                if ($variable [2] != '') {
                    $cadenaSql .= " AND nitproveedor = '" . $variable [2] . "'";
                }
                if ($variable [3] != '') {
                    $cadenaSql .= " AND numfactura = '" . $variable [3] . "'";
                }

                // echo $cadenaSql;exit;
                break;

            case "consultarActaM" :

                $cadenaSql = "SELECT DISTINCT ";
                $cadenaSql .= " id_actarecibido, dependencia, fecha_recibido, tipo_bien, nitproveedor, ";
                $cadenaSql .= " proveedor, numfactura, fecha_factura, tipocomprador, tipoaccion, ";
                $cadenaSql .= " fecha_revision, revisor, observacionesacta, estado_registro ";
                $cadenaSql .= " FROM registro_actarecibido ";
                $cadenaSql .= " JOIN tipo_accion ON tipo_accion.ta_idaccion = registro_actarecibido.tipoaccion ";
                $cadenaSql .= " JOIN tipo_bien ON tipo_bien.tb_idbien = registro_actarecibido.tipo_bien ";
                $cadenaSql .= " JOIN tipo_comprador ON tipo_comprador.tc_idcomprador = registro_actarecibido.tipocomprador ";
                $cadenaSql .= " WHERE 1 = 1 ";
                $cadenaSql .= " AND estado_registro = 1 ";
                $cadenaSql .= " AND id_actarecibido = '" . $variable . "' ";

                // echo $cadenaSql;exit;
                break;

            case "inactivarActa" :
                $cadenaSql = " UPDATE ";
                $cadenaSql .= " registro_actarecibido ";
                $cadenaSql .= " SET ";
                $cadenaSql .= "estado_registro='0',";
                $cadenaSql .= "fecha_registro='" . date('Y-m-d') . "' ";
                $cadenaSql .= " WHERE id_actarecibido = '" . $variable . "' ";
                $cadenaSql .= "RETURNING id_actarecibido";
                break;


            case "tipoOrden":
                $cadenaSql = " SELECT ";
                $cadenaSql .= " to_id, ";
                $cadenaSql .= " to_nombre ";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.tipo_orden ";
                $cadenaSql .= " WHERE to_estado = '1';
            ";
                break;

            case "tipoComprador":
                $cadenaSql = " SELECT ";
                $cadenaSql .= " tc_idcomprador, ";
                $cadenaSql .= " tc_descripcion ";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.tipo_comprador ";
                $cadenaSql .= " WHERE tc_estado = '1';
            ";
                break;

            case "tipoAccion":
                $cadenaSql = " SELECT ";
                $cadenaSql .= " ta_idaccion, ";
                $cadenaSql .= " ta_descripcion ";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.tipo_accion ";
                $cadenaSql .= " WHERE ta_estado = '1';
            ";
                break;

            case "tipoBien":
                $cadenaSql = " SELECT ";
                $cadenaSql .= " tb_idbien, ";
                $cadenaSql .= " tb_descripcion ";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.tipo_bien ";
                $cadenaSql .= " WHERE tb_estado = '1';                      ";
                break;
        }

        return $cadenaSql;
    }

}

?>
