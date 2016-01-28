<?php

namespace inventarios\gestionDepreciacion\registrarDepreciacion;

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

//-----------------------------** Cláusulas del caso de uso**----------------------------------//
            case "funcionarios":
                $cadenaSql = "SELECT data, value FROM ( ";
                $cadenaSql.= ' SELECT "FUN_IDENTIFICACION" as data, "FUN_IDENTIFICACION" ';
                $cadenaSql.= " ||' - '|| ";
                $cadenaSql.= ' "FUN_NOMBRE" as value ';
                $cadenaSql.= "FROM arka_parametros.arka_funcionarios) as consulta";
                $cadenaSql.= " WHERE value LIKE '%" . $variable . "%';";
                break;

            case "buscar_placa":
                $cadenaSql = " SELECT placa AS value, id_elemento_ind AS data FROM elemento_individual WHERE placa LIKE '%" . $variable . "%';";
                break;

            case "consultar_grupo_contable" :
                $cadenaSql = "SELECT data, value FROM ( ";
                $cadenaSql.= "SELECT elemento_id as data, elemento_codigo ||' - '||elemento_nombre as value ";
                $cadenaSql.= " FROM grupo.catalogo_elemento ";
                $cadenaSql.= " JOIN grupo.catalogo_lista ON grupo.catalogo_lista.lista_id=elemento_catalogo ";
                $cadenaSql.= " WHERE elemento_id>0 ";
                $cadenaSql.= " AND catalogo_elemento.elemento_tipobien <>1 ";
                $cadenaSql.= " AND lista_activo=1 ) as consulta";
                $cadenaSql.= " WHERE value LIKE '%" . $variable . "%' ";

                break;

            case "consultar_cuentasalida":
                $cadenaSql = "SELECT DISTINCT grupo_cuentasalida as data,grupo_cuentasalida as value FROM grupo.grupo_descripcion WHERE grupo_depreciacion=TRUE ";
                $cadenaSql.= " AND grupo_cuentasalida LIKE '%" . $variable . "%' ;";
                break;


            case "consultar_nivel_inventario" :
                $cadenaSql = "SELECT elemento_id, elemento_codigo ||' - '||elemento_nombre as nivel ";
                $cadenaSql.= " FROM catalogo.catalogo_elemento  ";
                $cadenaSql.= " JOIN catalogo.catalogo_lista ON catalogo.catalogo_lista.lista_id=elemento_catalogo  ";
                $cadenaSql.= " INNER JOIN grupo.grupo_descripcion ON grupo.grupo_descripcion.grupo_id=cast(elemento_id as character varying) ";
                $cadenaSql.= " WHERE elemento_id>0  ";
                $cadenaSql.= " AND lista_activo=1 ";
                //$cadenaSql.= " AND grupo.grupo_descripcion.grupo_depreciacion='t'";
                break;

            case "informacionDepreciacion":
                $cadenaSql = "SELECT elemento_id, elemento_nombre as nivel , grupo_vidautil ";
                $cadenaSql.= " FROM catalogo.catalogo_elemento  ";
                $cadenaSql.= " JOIN catalogo.catalogo_lista ON catalogo.catalogo_lista.lista_id=elemento_catalogo  ";
                $cadenaSql.= " INNER JOIN grupo.grupo_descripcion ON grupo.grupo_descripcion.grupo_id=cast(elemento_id as character varying) ";
                $cadenaSql.= " WHERE elemento_id>0  ";
                $cadenaSql.= " AND lista_activo=1 ";
                //$cadenaSql.= " AND grupo.grupo_descripcion.grupo_depreciacion='t' ";
                $cadenaSql.= " AND grupo_id='" . $variable . "' ";
                break;


            case "consultarElementos" :
                $cadenaSql = "SELECT ";
                $cadenaSql.= " id_elemento_ind, ";
                $cadenaSql.= " elemento_nombre,   ";
                $cadenaSql.= " marca,   ";
                $cadenaSql.= " elemento_individual.serie  ";
                $cadenaSql.= " FROM elemento_individual ";
                $cadenaSql.= " JOIN elemento ON elemento.id_elemento=elemento_individual.id_elemento_gen ";
                $cadenaSql.= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel ";
                $cadenaSql.= " WHERE elemento.estado=TRUE  ";
                $cadenaSql.= " AND nivel='" . $variable . "'  ";
                $cadenaSql.= " AND id_elemento_ind NOT IN (   ";
                $cadenaSql.= " SELECT dep_idelemento  ";
                $cadenaSql.= " FROM registro_depreciacion  ";
                $cadenaSql.= " ) ";
                $cadenaSql.= " AND id_elemento_ind NOT IN (   ";
                $cadenaSql.= " SELECT id_elemento_ind  ";
                $cadenaSql.= " FROM estado_elemento ";
                $cadenaSql.= " ) ";
                $cadenaSql.= " ORDER BY id_elemento_ind ASC ";
                break;

            case "consultarElemento_especifico" :
                $cadenaSql = "SELECT DISTINCT  ";
                $cadenaSql.= " id_elemento_ind, ";
                $cadenaSql.= " elemento_nombre,   ";
                $cadenaSql.= " valor, ";
                $cadenaSql.= " salida.fecha_registro ";
                $cadenaSql.= " FROM elemento_individual ";
                $cadenaSql.= " JOIN elemento ON elemento.id_elemento=elemento_individual.id_elemento_gen ";
                $cadenaSql.= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel ";
                $cadenaSql.= " JOIN salida ON salida.id_salida=elemento_individual.id_salida ";
                $cadenaSql.= " WHERE elemento.estado=TRUE  ";
                $cadenaSql.= " AND id_elemento_ind NOT IN (   ";
                $cadenaSql.= " SELECT dep_idelemento  ";
                $cadenaSql.= " FROM registro_depreciacion  ";
                $cadenaSql.= " ) ";
                $cadenaSql.= " AND id_elemento_ind NOT IN (   ";
                $cadenaSql.= " SELECT id_elemento_ind  ";
                $cadenaSql.= " FROM estado_elemento ";
                $cadenaSql.= " ) ";
                $cadenaSql.= " AND id_elemento_ind = '" . $variable . "' ";
                $cadenaSql.= " ORDER BY id_elemento_ind ASC ";
                break;

            case "mostrarInfoDepreciar":
                $cadenaSql = " SELECT DISTINCT   ";
                $cadenaSql .= " id_elemento_ind,  ";
                $cadenaSql .= " grupo_cuentasalida,  ";
                $cadenaSql .= " placa,  ";
                $cadenaSql .= " descripcion,  ";
                $cadenaSql .= " salida.fecha_registro,  ";
                $cadenaSql .= " salida.consecutivo,  ";
                $cadenaSql .= " grupo.catalogo_elemento.elemento_id grupo,  ";
                $cadenaSql .= " grupo.catalogo_elemento.elemento_codigo grupo_codigo,  ";
                $cadenaSql .= " grupo.catalogo_elemento.elemento_nombre grupo_nombre,   ";
                $cadenaSql .= " grupo_vidautil,   ";
                $cadenaSql .= " elemento.total_iva_con - coalesce(elemento.ajuste,0) valor,  ";
                $cadenaSql .= " 0 as valor_cuota,  ";
                $cadenaSql .= " ajuste_inflacionario  ";
                $cadenaSql .= " FROM arka_inventarios.elemento_individual    ";
                $cadenaSql .= " JOIN arka_inventarios.elemento ON elemento.id_elemento=elemento_individual.id_elemento_gen   ";
                $cadenaSql .= " JOIN arka_inventarios.entrada ON entrada.id_entrada=elemento.id_entrada ";
                $cadenaSql .= " JOIN arka_inventarios.salida ON salida.id_salida=elemento_individual.id_salida   ";
                $cadenaSql .= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel    ";
                $cadenaSql .= " JOIN catalogo.catalogo_lista ON catalogo.catalogo_elemento.elemento_catalogo=catalogo.catalogo_lista.lista_id   ";
                $cadenaSql .= " JOIN grupo.catalogo_elemento ON cast(grupo.catalogo_elemento.elemento_id as character varying)=catalogo.catalogo_elemento.elemento_grupoc   ";
                $cadenaSql .= " JOIN grupo.grupo_descripcion ON grupo.grupo_descripcion.grupo_id=cast(grupo.catalogo_elemento.elemento_id as character varying)   ";
                $cadenaSql .= " WHERE catalogo.catalogo_elemento.elemento_id>0   ";
                $cadenaSql .= " AND catalogo.catalogo_lista.lista_activo=1    ";
                $cadenaSql .= " AND elemento.tipo_bien <> 1  ";
                $cadenaSql .= " AND elemento.estado=TRUE   ";
                $cadenaSql .= " AND entrada.estado_entrada <> 3 ";
                $cadenaSql .= " AND id_elemento_ind NOT IN (     ";
                $cadenaSql .= " SELECT id_elemento_ind    ";
                $cadenaSql .= " FROM estado_elemento   ";
                $cadenaSql .= " )  ";

                if ($variable ['funcionario'] != '') {
                    $cadenaSql .= " AND salida.funcionario = '" . $variable ['funcionario'] . "'";
                }

                if ($variable ['cuenta_salida'] != '') {
                    $cadenaSql .= " AND grupo_cuentasalida = '" . $variable ['cuenta_salida'] . "'";
                }


                if ($variable ['placa'] != '') {
                    $cadenaSql .= " AND id_elemento = '" . $variable ['placa'] . "'";
                }

                if ($variable ['grupo'] != '') {
                    $cadenaSql.= " AND grupo_id='" . $variable['grupo'] . "' ";
                }

                if ($variable ['fecha_corte'] != '') {
                    $cadenaSql.= " AND salida.fecha_registro<='" . $variable['fecha_corte'] . "' ";
                }

                $cadenaSql.= " ORDER BY id_elemento_ind ASC ";
                break;

            //-------------- LAS QUE FINALMENTE QUEDARON DEL CASO DE USO --------------//


            case "mostrarInfoDepreciar_2":
                $cadenaSql = " SELECT ";
                $cadenaSql.= " detalle_depreciacion.id_elemento_ind, ";
                $cadenaSql.= " grupo_cuentasalida, ";
                $cadenaSql.= " placa, ";
                $cadenaSql.= " descripcion, ";
                $cadenaSql.= " fecha_salida,  ";
                $cadenaSql.= " grupo_contable grupo, ";
                $cadenaSql.= " grupo.catalogo_elemento.elemento_codigo grupo_codigo,  ";
                $cadenaSql.= " grupo.catalogo_elemento.elemento_nombre grupo_nombre,  ";
                $cadenaSql.= " vida_util,  ";
                $cadenaSql.= " detalle_depreciacion.valor,  ";
                $cadenaSql.= " valor_cuota, ";
                $cadenaSql.= " ajuste_inflacionario ";
                $cadenaSql.= " FROM arka_inventarios.detalle_depreciacion ";
                $cadenaSql.= " JOIN arka_inventarios.elemento_individual ON elemento_individual.id_elemento_ind=detalle_depreciacion.id_elemento_ind ";
                $cadenaSql.= " JOIN arka_inventarios.elemento ON elemento.id_elemento=elemento_individual.id_elemento_gen ";
                $cadenaSql.= " JOIN grupo.grupo_descripcion gd ON  gd.grupo_id=cast(detalle_depreciacion.grupo_contable as character varying) ";
                $cadenaSql.= " JOIN grupo.catalogo_elemento ON cast(grupo.catalogo_elemento.elemento_id as character varying)=gd.grupo_id ";
                $cadenaSql.= " JOIN arka_inventarios.salida ON salida.id_salida = detalle_depreciacion.id_salida ";
                $cadenaSql.= " WHERE 1=1 ";
                $cadenaSql.= " AND detalle_depreciacion.estado_registro=TRUE ";
                $cadenaSql.= " AND elemento_individual.estado_registro=TRUE  ";
                $cadenaSql.= " AND detalle_depreciacion.id_elemento_ind NOT IN (    ";
                $cadenaSql.= " SELECT id_elemento_ind   ";
                $cadenaSql.= " FROM arka_inventarios.estado_elemento  ";
                $cadenaSql.= " )  ";

                if ($variable ['funcionario'] != '') {
                    $cadenaSql .= " AND salida.funcionario = '" . $variable ['funcionario'] . "'";
                }

                if ($variable ['cuenta_salida'] != '') {
                    $cadenaSql .= " AND grupo_cuentasalida = '" . $variable ['cuenta_salida'] . "'";
                }


                if ($variable ['placa'] != '') {
                    $cadenaSql .= " AND detalle_depreciacion.id_elemento_ind = '" . $variable ['placa'] . "'";
                }

                if ($variable ['grupo'] != '') {
                    $cadenaSql.= " AND grupo_id='" . $variable['grupo'] . "' ";
                }

                $cadenaSql.= " ORDER BY id_elemento_ind ASC ";
                break;


            case "mostrarInfoDepreciar_elemento":
                $cadenaSql = " SELECT DISTINCT  ";
                $cadenaSql.= " id_elemento_ind, ";
                $cadenaSql.= " placa, ";
                $cadenaSql.= " descripcion, ";
                $cadenaSql.= " salida.consecutivo, ";
                $cadenaSql.= " grupo.catalogo_elemento.elemento_id grupo,  ";
                $cadenaSql.= " grupo.catalogo_elemento.elemento_codigo grupo_codigo, ";
                $cadenaSql.= " grupo_vidautil,  ";
                $cadenaSql.= " elemento.valor,  ";
                $cadenaSql.= " salida.fecha_registro,  ";
                $cadenaSql.= " grupo_cuentasalida, ";
                $cadenaSql.= " ajuste_inflacionario, ";
                $cadenaSql.= " catalogo.catalogo_elemento.elemento_nombre elemento_nombre ";
                $cadenaSql.= " FROM elemento_individual  ";
                $cadenaSql.= " JOIN elemento ON elemento.id_elemento=elemento_individual.id_elemento_gen ";
                $cadenaSql.= " JOIN salida ON salida.id_salida=elemento_individual.id_salida ";
                $cadenaSql.= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel  ";
                $cadenaSql.= " JOIN catalogo.catalogo_lista ON catalogo.catalogo_elemento.elemento_catalogo=catalogo.catalogo_lista.lista_id ";
                $cadenaSql.= " JOIN grupo.catalogo_elemento ON cast(grupo.catalogo_elemento.elemento_id as character varying)=catalogo.catalogo_elemento.elemento_grupoc ";
                $cadenaSql.= " JOIN grupo.grupo_descripcion ON grupo.grupo_descripcion.grupo_id=cast(grupo.catalogo_elemento.elemento_id as character varying) ";
                $cadenaSql.= " WHERE catalogo.catalogo_elemento.elemento_id>0 ";
                $cadenaSql.= " AND catalogo.catalogo_lista.lista_activo=1 ";
                $cadenaSql.= " AND elemento.estado=TRUE   ";
                $cadenaSql.= " AND id_elemento_ind='" . $variable . "'";
                break;

            case "registrarDepreciacion":
                $cadenaSql = " INSERT INTO arka_inventarios.registro_depreciacion_contabilidad( ";
                $cadenaSql.= " depcontable_idelemento, ";
                $cadenaSql.= " depcontable_grupocontable, ";
                $cadenaSql.= " depcontable_meses, ";
                $cadenaSql.= " depcontable_fechasalida, ";
                $cadenaSql.= " depcontable_fechacorte, ";
                $cadenaSql.= " depcontable_cantidad, ";
                $cadenaSql.= " depcontable_precio, ";
                $cadenaSql.= " depcontable_valorhistorico, ";
                $cadenaSql.= " depcontable_valorajustado, ";
                $cadenaSql.= " depcontable_cuota, ";
                $cadenaSql.= " depcontable_periodo, ";
                $cadenaSql.= " depcontable_depacumulada, ";
                $cadenaSql.= " depcontable_circular56, ";
                $cadenaSql.= " depcontable_cuotainflacion, ";
                $cadenaSql.= " depcontable_apicacumulada, ";
                $cadenaSql.= " depcontable_circulardeprecia, ";
                $cadenaSql.= " depcontable_libros) ";
                $cadenaSql.= " VALUES ( ";
                $cadenaSql.= "'" . $variable['elemento_individual'] . "', ";
                $cadenaSql.= "'" . $variable['grupo'] . "', ";
                $cadenaSql.= "'" . $variable['meses_depreciar'] . "', ";
                $cadenaSql.= "'" . $variable['fechaSalida'] . "', ";
                $cadenaSql.= "'" . $variable['fechaCorte'] . "', ";
                $cadenaSql.= "'" . $variable['cantidad'] . "', ";
                $cadenaSql.= "'" . $variable['precio'] . "', ";
                $cadenaSql.= "'" . $variable['valor_historico'] . "', ";
                $cadenaSql.= "'" . $variable['valor_ajustado'] . "', ";
                $cadenaSql.= "'" . $variable['cuota'] . "', ";
                $cadenaSql.= "'" . $variable['periodos_fecha'] . "', ";
                $cadenaSql.= "'" . $variable['depreciacion_acumulada'] . "', ";
                $cadenaSql.= "'" . $variable['circular_56'] . "', ";
                $cadenaSql.= "'" . $variable['cuota_inflacion'] . "', ";
                $cadenaSql.= "'" . $variable['api_acumulada'] . "', ";
                $cadenaSql.= "'" . $variable['circular_depreciacion'] . "', ";
                $cadenaSql.= "'" . $variable['valor_libros'] . "') ";
                $cadenaSql.= ";";
                break;

            case "limpiarDepreciacion":
                $cadenaSql = "DELETE FROM arka_inventarios.registro_depreciacion_contabilidad; ALTER sequence registro_depreciacion_contabilidad_depcontable_id_seq restart with 1;";
                break;

            /*             * ***************** */
        }
        return $cadenaSql;
    }

}

?>
