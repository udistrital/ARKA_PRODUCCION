<?php

namespace inventarios\gestionActa\activacionElementosActa;

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
            case "buscar_entradas" :
                $cadenaSql = " SELECT DISTINCT id_entrada valor, consecutivo||' - ('||entrada.vigencia||')' descripcion  ";
                $cadenaSql .= " FROM entrada  ";
                $cadenaSql .= "WHERE entrada.cierre_contable='f' ";
                $cadenaSql .= "ORDER BY id_entrada DESC ;";
                break;


            case "anular_elementos_acta" :
                $cadenaSql = " UPDATE ";
                $cadenaSql .= " elemento_acta_recibido  ";
                $cadenaSql .= " SET ";
                $cadenaSql .= " estado='false'  ";
                $cadenaSql .= " WHERE id_elemento_ac='" . $variable . "'";
                break;

            // -------

            /*
             * Modificar Elemento
             */



            case "consultarEntrada" :
                $cadenaSql = " SELECT en.* , cl.descripcion";
                $cadenaSql .= " FROM entrada en  ";
                $cadenaSql .= " JOIN clase_entrada cl ON cl.id_clase=en.clase_entrada ";
                $cadenaSql .= "WHERE en.id_entrada=" . $variable;
                break;

            case "buscar_placa_maxima" :
                $cadenaSql = " SELECT  MAX(placa::FLOAT) placa_max ";
                $cadenaSql .= " FROM elemento_individual ";
                break;

            case "ingresar_elemento_individual" :

                $cadenaSql = " 	INSERT INTO elemento_individual(";
                $cadenaSql .= "fecha_registro, placa, serie, id_elemento_gen,id_elemento_ind) ";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= "'" . $variable [0] . "',";
                $cadenaSql .= ((is_null($variable [1])) ? 'null' . "," : "'" . $variable [1] . "',");
                $cadenaSql .= ((is_null($variable [2])) ? 'null' . "," : "'" . $variable [2] . "',");
                $cadenaSql .= "'" . $variable [3] . "',";
                $cadenaSql .= "'" . $variable [4] . "') ";
                $cadenaSql .= "RETURNING id_elemento_ind; ";

                break;


            case "buscar_repetida_placa" :
                $cadenaSql = " SELECT  count (placa) ";
                $cadenaSql .= " FROM elemento_individual ";
                $cadenaSql .= " WHERE placa ='" . $variable . "';";
                break;

            case "idElementoMaxIndividual" :

                $cadenaSql = "SELECT max(id_elemento_ind) ";
                $cadenaSql .= "FROM elemento_individual  ";

                break;

            case "ElementoImagen" :

                $cadenaSql = " 	INSERT INTO arka_movil.asignar_imagen(";
                $cadenaSql .= " id_elemento, prioridad, imagen ) ";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= "'" . $variable ['elemento'] . "',";
                $cadenaSql .= "1,";
                $cadenaSql .= "'" . $variable ['imagen'] . "') ";
                $cadenaSql .= "RETURNING num_registro; ";

                break;



            case "ingresar_elemento" :
                $cadenaSql = " INSERT INTO ";
                $cadenaSql .= " elemento(";
                $cadenaSql .= "fecha_registro,nivel,tipo_bien, descripcion, cantidad, ";
                $cadenaSql .= "unidad, valor, iva, ajuste, bodega, subtotal_sin_iva, total_iva, ";
                $cadenaSql .= "total_iva_con,tipo_poliza, fecha_inicio_pol, fecha_final_pol,marca,serie,id_entrada,id_elemento,cantidad_por_asignar) ";
                $cadenaSql .= " VALUES (";
                $cadenaSql .= "'" . $variable [0] . "',";
                $cadenaSql .= "'" . $variable [1] . "',";
                $cadenaSql .= "'" . $variable [2] . "',";
                $cadenaSql .= "'" . $variable [3] . "',";
                $cadenaSql .= "'" . $variable [4] . "',";
                $cadenaSql .= "'" . $variable [5] . "',";
                $cadenaSql .= "'" . $variable [6] . "',";
                $cadenaSql .= "'" . $variable [7] . "',";
                $cadenaSql .= "NULL,";
                $cadenaSql .= "NULL,";
                $cadenaSql .= "'" . $variable [10] . "',";
                $cadenaSql .= "'" . $variable [11] . "',";
                $cadenaSql .= "'" . $variable [12] . "',";
                $cadenaSql .= (is_null($variable [13]) == true) ? ' NULL , ' : "'" . $variable [13] . "',";
                $cadenaSql .= (is_null($variable [14]) == true) ? ' NULL , ' : "'" . $variable [14] . "',";
                $cadenaSql .= (is_null($variable [15]) == true) ? ' NULL , ' : "'" . $variable [15] . "',";
                $cadenaSql .= (is_null($variable [16]) == true) ? ' NULL , ' : "'" . $variable [16] . "',";
                $cadenaSql .= (is_null($variable [17]) == true) ? ' NULL , ' : "'" . $variable [17] . "',";
                $cadenaSql .= "'" . $variable [18] . "',";
                $cadenaSql .= "'" . $variable [19] . "',";
                $cadenaSql .= "'" . $variable [20] . "') ";
                $cadenaSql .= "RETURNING  id_elemento; ";

                break;

            case "idElementoMax" :

                $cadenaSql = "SELECT max(id_elemento) ";
                $cadenaSql .= "FROM elemento  ";

                break;

            case "consulta_elementos_activar" :
                $cadenaSql = " SELECT ar.*,  im.imagen ";
                $cadenaSql .= " FROM elemento_acta_recibido  ar ";
                $cadenaSql .= " LEFT  JOIN  asignar_imagen_acta im ON im.id_elemento_acta=ar.id_elemento_ac   ";
                $cadenaSql .= " WHERE ar.id_elemento_ac='" . $variable . "'";
                break;

            case "consultar_nivel_inventario" :

                $cadenaSql = "SELECT ce.elemento_id, ce.elemento_codigo||' - '||ce.elemento_nombre ";
                $cadenaSql .= "FROM grupo.catalogo_elemento  ce ";
                $cadenaSql .= "JOIN grupo.catalogo_lista cl ON cl.lista_id = ce.elemento_catalogo  ";
                $cadenaSql .= "WHERE cl.lista_activo = 1  ";
                $cadenaSql .= "AND  ce.elemento_id > 0  ";
                $cadenaSql .= "ORDER BY ce.elemento_codigo ASC ;";

                break;

            case "consultar_tipo_iva" :

                $cadenaSql = "SELECT id_iva, descripcion ";
                $cadenaSql .= "FROM arka_inventarios.aplicacion_iva;";

                break;

            case "ConsultaTipoBien" :

                $cadenaSql = "SELECT  ce.elemento_tipobien , tb.descripcion  ";
                $cadenaSql .= "FROM grupo.catalogo_elemento ce ";
                $cadenaSql .= "JOIN  arka_inventarios.tipo_bienes tb ON tb.id_tipo_bienes = ce.elemento_tipobien  ";
                $cadenaSql .= "WHERE ce.elemento_id = '" . $variable . "';";

                break;

            case "consultar_tipo_poliza" :

                $cadenaSql = "SELECT id_tipo_poliza, descripcion ";
                $cadenaSql .= "FROM arka_inventarios.tipo_poliza;";

                break;

            // ----

            case "consultarActaM" :

                $cadenaSql = "SELECT DISTINCT ra.*, \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  nom_razon  ";
                $cadenaSql .= " FROM registro_actarecibido ra  ";
                $cadenaSql .= " JOIN  arka_parametros.arka_proveedor ap ON ap.\"PRO_NIT\" =CAST(ra.proveedor AS CHAR (50))  ";
                $cadenaSql .= " WHERE 1 = 1 ";
                $cadenaSql .= " AND estado_registro = 1 ";
                $cadenaSql .= " AND id_actarecibido = '" . $variable . "' ";
                break;

            case "buscar_Proveedores" :
                $cadenaSql = " SELECT \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  value,\"PRO_NIT\"  AS data  ";
                $cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
                $cadenaSql .= "WHERE cast(\"PRO_NIT\" as text) LIKE '%" . $variable . "%' ";
                $cadenaSql .= "OR \"PRO_RAZON_SOCIAL\" LIKE '%" . $variable . "%' LIMIT 10; ";

                break;

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
                $cadenaSql .= " WHERE id_acta='" . $variable ['tiempo'] . "';";
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
                $cadenaSql .= " id_items,item,cantidad,descripcion, ";
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
                $cadenaSql .= " id_acta, item, descripcion,cantidad, ";
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
                $cadenaSql .= " fecha_registro='" . date('Y-m-d') . "'";
                $cadenaSql .= " WHERE id_acta='" . $variable . "'";
                break;

            case "insertarItemTemporal" :
                $cadenaSql = " INSERT INTO ";
                $cadenaSql .= " arka_inventarios.items_actarecibido_temp(";
                $cadenaSql .= " item,cantidad,descripcion, ";
                $cadenaSql .= " valor_unitario,valor_total,seccion)";
                $cadenaSql .= " VALUES (";
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
            // UPDATE registro_actarecibido
            // SET id_actarecibido=?, sede=?, dependencia=?, fecha_recibido=?, tipo_bien=?,
            // proveedor=?, ordenador_gasto=?, tipo_orden=?, fecha_revision=?,
            // revisor=?, observacionesacta=?, enlace_soporte=?, nombre_soporte=?,
            // numero_orden=?, estado_registro=?, fecha_registro=?
            // WHERE <condition>;

            case "actualizarActa_soporte" :
                $cadenaSql = " UPDATE ";
                $cadenaSql .= " registro_actarecibido ";
                $cadenaSql .= " SET ";
                $cadenaSql .= "sede='" . $variable ['sede'] . "',";
                $cadenaSql .= "dependencia='" . $variable ['dependencia'] . "',";
                $cadenaSql .= "fecha_recibido='" . $variable ['fecha_registro'] . "',";
                $cadenaSql .= "tipo_bien='" . $variable ['tipo_bien'] . "',";
                $cadenaSql .= "proveedor='" . $variable ['nit_proveedor'] . "',";
                $cadenaSql .= "ordenador_gasto='" . $variable ['ordenador'] . "',";
                $cadenaSql .= "fecha_revision='" . $variable ['fecha_revision'] . "',";
                $cadenaSql .= "revisor='" . $variable ['revisor'] . "',";
                $cadenaSql .= "observacionesacta='" . $variable ['observaciones'] . "',";
                $cadenaSql .= "enlace_soporte='" . $variable ['enlace_soporte'] . "',";
                $cadenaSql .= "nombre_soporte='" . $variable ['nombre_soporte'] . "',";
                $cadenaSql .= "estado_registro='" . $variable ['estado'] . "',";
                $cadenaSql .= "fecha_registro='" . $variable ['fecha_registro'] . "', ";
                $cadenaSql .= "id_contrato='" . $variable ['identificador_contrato'] . "' ";
                $cadenaSql .= " WHERE id_actarecibido = '" . $variable ['id_acta'] . "' ";
                $cadenaSql .= "RETURNING id_actarecibido";

                break;

            case "actualizarActa" :
                $cadenaSql = " UPDATE ";
                $cadenaSql .= " registro_actarecibido ";
                $cadenaSql .= " SET ";
                $cadenaSql .= "sede='" . $variable ['sede'] . "',";
                $cadenaSql .= "dependencia='" . $variable ['dependencia'] . "',";
                $cadenaSql .= "fecha_recibido='" . $variable ['fecha_registro'] . "',";
                $cadenaSql .= "tipo_bien='" . $variable ['tipo_bien'] . "',";
                $cadenaSql .= "proveedor='" . $variable ['nit_proveedor'] . "',";
                $cadenaSql .= "ordenador_gasto='" . $variable ['ordenador'] . "',";
                $cadenaSql .= "fecha_revision='" . $variable ['fecha_revision'] . "',";
                $cadenaSql .= "revisor='" . $variable ['revisor'] . "',";
                $cadenaSql .= "observacionesacta='" . $variable ['observaciones'] . "',";
                $cadenaSql .= "estado_registro='" . $variable ['estado'] . "',";
                $cadenaSql .= "fecha_registro='" . $variable ['fecha_registro'] . "', ";
                $cadenaSql .= "id_contrato='" . $variable ['identificador_contrato'] . "' ";
                $cadenaSql .= " WHERE id_actarecibido = '" . $variable ['id_acta'] . "' ";
                $cadenaSql .= "RETURNING id_actarecibido";

                break;

            case "consultarActaEntrada" :
                $cadenaSql = "SELECT DISTINCT ";
                $cadenaSql .= "en.id_entrada id_entrada ,en.consecutivo||' - ('||en.vigencia||')' consecutivo_entrada,
											en.fecha_registro fecha_registro_entrada ,ar.id_actarecibido id_acta ";
                $cadenaSql .= "FROM registro_actarecibido ar ";
                $cadenaSql .= "JOIN elemento_acta_recibido  el  ON el.id_acta=  ar.id_actarecibido ";
                $cadenaSql .= "JOIN  entrada en ON en.acta_recibido= ar.id_actarecibido ";
                $cadenaSql .= "WHERE  ar.estado_registro = 1 
											AND el.estado='true'   ";
                if ($variable ['numero_acta'] != '') {
                    $cadenaSql .= " AND ar.id_actarecibido = '" . $variable ['numero_acta'] . "' ";
                }
                if ($variable ['numero_entrada'] != '') {
                    $cadenaSql .= " AND en.id_entrada = '" . $variable ['numero_entrada'] . "' ";
                }
                if ($variable ['fecha_inicio'] != '') {
                    $cadenaSql .= " AND en.fecha_registro  BETWEEN CAST ( '" . $variable ['fecha_inicio'] . "' AS DATE) ";
                    $cadenaSql .= " AND  CAST ( '" . $variable ['fecha_final'] . "' AS DATE)  ";
                }

                $cadenaSql .= " ; ";

                break;

            case "consultarProveedor" :
                $cadenaSql = "SELECT PRO_NIT ||' - '|| PRO_RAZON_SOCIAL  ";
                $cadenaSql .= "FROM PROVEEDORES ";
                $cadenaSql .= "WHERE PRO_NIT='" . $variable . "' ";

                break;

            case "consultarActaElementos" :
                $cadenaSql = "SELECT  id_elemento_ac  ";
                $cadenaSql .= "FROM elemento_acta_recibido ";
                $cadenaSql .= "WHERE id_acta ='" . $variable . "'  ";
                $cadenaSql .= "AND estado='true' ";
                break;

            case "consultarElementosActa" :
                $cadenaSql = "SELECT  ela.*, ct.elemento_nombre nivel_nombre, tb.descripcion nombre_tipo, iv.descripcion nombre_iva ";
                $cadenaSql .= "FROM elemento_acta_recibido ela ";
                $cadenaSql .= "JOIN  catalogo.catalogo_elemento ct ON ct.elemento_id=ela.nivel ";
                $cadenaSql .= "JOIN  tipo_bienes tb ON tb.id_tipo_bienes=ela.tipo_bien ";
                $cadenaSql .= "JOIN  aplicacion_iva iv ON iv.id_iva=ela.iva  ";
                $cadenaSql .= "WHERE id_acta ='" . $variable . "'  ";
                $cadenaSql .= "AND  ela.estado=true ";

                break;

            // UPDATE elemento_acta_recibido
            // SET id_elemento_ac=?, fecha_registro=?, nivel=?, tipo_bien=?, descripcion=?,
            // cantidad=?, unidad=?, valor=?, iva=?, subtotal_sin_iva=?, total_iva=?,
            // total_iva_con=?, tipo_poliza=?, fecha_inicio_pol=?, fecha_final_pol=?,
            // marca=?, serie=?, id_acta=?, estado=?
            // WHERE <condition>;

            case "actualizar_elemento_tipo_2" :
                $cadenaSql = "UPDATE elemento_acta_recibido ";
                $cadenaSql .= "SET nivel='" . $variable [0] . "', ";
                $cadenaSql .= "tipo_bien='" . $variable [1] . "', ";
                $cadenaSql .= "descripcion='" . $variable [2] . "', ";
                $cadenaSql .= "cantidad='" . $variable [3] . "', ";
                $cadenaSql .= "unidad='" . $variable [4] . "', ";
                $cadenaSql .= "valor='" . $variable [5] . "', ";
                $cadenaSql .= "iva='" . $variable [6] . "', ";
                $cadenaSql .= "subtotal_sin_iva='" . $variable [7] . "', ";
                $cadenaSql .= "total_iva='" . $variable [8] . "', ";
                $cadenaSql .= "total_iva_con='" . $variable [9] . "', ";
                $cadenaSql .= "tipo_poliza='" . $variable [10] . "', ";
                if ($variable [10] == 0) {

                    $cadenaSql .= "fecha_inicio_pol=NULL, ";
                    $cadenaSql .= "fecha_final_pol=NULL, ";
                } else if ($variable [10] == 1) {

                    $cadenaSql .= "fecha_inicio_pol='" . $variable [11] . "', ";
                    $cadenaSql .= "fecha_final_pol='" . $variable [12] . "', ";
                }
                $cadenaSql .= (is_null($variable [13]) == true) ? "marca=NULL, " : "marca='" . $variable [13] . "', ";
                $cadenaSql .= (is_null($variable [14]) == true) ? "serie=NULL, " : "serie='" . $variable [14] . "'  ";
                $cadenaSql .= "WHERE id_elemento_ac ='" . $variable [15] . "' ";

                break;

            case "actualizar_elemento_tipo_1" :
                $cadenaSql = "UPDATE elemento_acta_recibido ";
                $cadenaSql .= "SET nivel='" . $variable [0] . "', ";
                $cadenaSql .= "tipo_bien='" . $variable [1] . "', ";
                $cadenaSql .= "descripcion='" . $variable [2] . "', ";
                $cadenaSql .= "cantidad='" . $variable [3] . "', ";
                $cadenaSql .= "unidad='" . $variable [4] . "', ";
                $cadenaSql .= "valor='" . $variable [5] . "', ";
                $cadenaSql .= "iva='" . $variable [6] . "', ";
                $cadenaSql .= "subtotal_sin_iva='" . $variable [7] . "', ";
                $cadenaSql .= "total_iva='" . $variable [8] . "', ";
                $cadenaSql .= "total_iva_con='" . $variable [9] . "', ";
                $cadenaSql .= (is_null($variable [10]) == true) ? "marca=NULL, " : "marca='" . $variable [10] . "', ";
                $cadenaSql .= (is_null($variable [11]) == true) ? "serie=NULL, " : "serie='" . $variable [11] . "'  ";
                $cadenaSql .= "WHERE id_elemento_ac ='" . $variable [12] . "'  ";

                break;

            case "consultar_iva" :

                $cadenaSql = "SELECT iva ";
                $cadenaSql .= "FROM arka_inventarios.aplicacion_iva ";
                $cadenaSql .= "WHERE id_iva='" . $variable . "';";

                break;

            case "consultarElemento" :
                $cadenaSql = "SELECT  * ";
                $cadenaSql .= "FROM arka_inventarios.elemento_acta_recibido ";
                $cadenaSql .= "WHERE  id_elemento_ac ='" . $variable . "'  ;";

                break;

                // $cadenaSql = "SELECT DISTINCT ";
                // $cadenaSql .= " id_actarecibido, dependencia, fecha_recibido, tipo_bien as tipoBien, nitproveedor, ";
                // $cadenaSql .= " proveedor, tipocomprador as tipoComprador, ";
                // $cadenaSql .= " fecha_revision, revisor, observacionesacta, estado_registro ";
                // $cadenaSql .= " FROM registro_actarecibido ";
                // $cadenaSql .= " JOIN tipo_bien ON tipo_bien.tb_idbien = registro_actarecibido.tipo_bien ";
                // $cadenaSql .= " JOIN tipo_comprador ON tipo_comprador.tc_idcomprador = registro_actarecibido.tipocomprador ";
                // $cadenaSql .= " WHERE 1 = 1 ";
                // $cadenaSql .= " AND estado_registro = 1 ";
                // $cadenaSql .= " AND id_actarecibido = '" . $variable . "' ";
                // echo $cadenaSql;exit;
                break;

            case "consultar_id_acta" :
                $cadenaSql = " SELECT DISTINCT id_actarecibido, id_actarecibido as acta_serial";
                $cadenaSql .= " FROM registro_actarecibido ";
                $cadenaSql .= " JOIN    entrada  en ON en.acta_recibido=registro_actarecibido.id_actarecibido  ";
                $cadenaSql .= " JOIN    elemento_acta_recibido  ela ON ela.id_acta=registro_actarecibido. id_actarecibido ";
                $cadenaSql .= " WHERE ela.estado='true'  ";
                $cadenaSql .= " ORDER BY  id_actarecibido DESC ;  ";
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

            case "tipoOrden" :
                $cadenaSql = " SELECT ";
                $cadenaSql .= " to_id, ";
                $cadenaSql .= " to_nombre ";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.tipo_orden ";
                $cadenaSql .= " WHERE to_estado = '1';
            ";
                break;

            case "tipoAccion" :
                $cadenaSql = " SELECT ";
                $cadenaSql .= " ta_idaccion, ";
                $cadenaSql .= " ta_descripcion ";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.tipo_accion ";
                $cadenaSql .= " WHERE ta_estado = '1';
            ";
                break;

            case "tipoBien" :
                $cadenaSql = " SELECT ";
                $cadenaSql .= " tb_idbien, ";
                $cadenaSql .= " tb_descripcion ";
                $cadenaSql .= " FROM ";
                $cadenaSql .= " arka_inventarios.tipo_bien ";
                $cadenaSql .= " WHERE tb_estado = '1';                      ";
                break;

            // Consultas de Oracle para rescate de información de Sicapital
            /*
             * case "dependencias": $cadenaSql = "SELECT DEP_IDENTIFICADOR, "; $cadenaSql.= " DEP_IDENTIFICADOR ||' '|| DEP_DEPENDENCIA "; //$cadenaSql .= " DEP_DIRECCION,DEP_TELEFONO ";F $cadenaSql.= " FROM DEPENDENCIAS "; break;
             */

            case "select_proveedor" :
                $cadenaSql = "SELECT \"PRO_RAZON_SOCIAL\" ";
                $cadenaSql .= " FROM   arka_parametros.arka_proveedor ";
                $cadenaSql .= " WHERE \"PRO_NIT\"='" . $variable . "' ";
                break;

            case "contratistas" :
                $cadenaSql = "SELECT CON_IDENTIFICACION,CON_IDENTIFICACION ||' '|| CON_NOMBRE ";
                /*
                 * $cadenaSql .= " CON_CARGO, "; $cadenaSql .= " CON_DIRECCION, "; $cadenaSql .= " CON_TELEFONO ";
                 */
                $cadenaSql .= " FROM CONTRATISTAS ";
                break;

            // ---------------

            case "dependenciasConsultadas" :
                $cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
                $cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
                $cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
                $cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
                $cadenaSql .= " WHERE sa.\"ESF_ID_SEDE\"='" . $variable . "' ";
                $cadenaSql .= " AND  ad.\"ESF_ESTADO\"='A'";
                break;

            case "sede" :
                $cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
                $cadenaSql .= " FROM arka_parametros.arka_sedes ";
                $cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
                $cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
                break;
            case "dependencias" :
                $cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
                $cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
                $cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
                $cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
                $cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";

                break;

            case "consultaDependencia" :
                $cadenaSql = "SELECT DISTINCT  ESF_ID_ESPACIO, ESF_NOMBRE_ESPACIO ";
                $cadenaSql .= " FROM ESPACIOS_FISICOS ";
                $cadenaSql .= " WHERE  ESF_ESTADO='A' ";
                $cadenaSql .= " AND  ESF_ID_ESPACIO='" . $variable . "' ";
                break;

            case "proveedores" :
                $cadenaSql = " SELECT \"PRO_NIT\",\"PRO_NIT\"||' - '||\"PRO_RAZON_SOCIAL\" AS proveedor ";
                $cadenaSql .= " FROM arka_parametros.arka_proveedor ";

                break;

            case "tipoComprador" :

                $cadenaSql = " 	SELECT \"ORG_IDENTIFICACION\", \"ORG_ORDENADOR_GASTO\" ";
                $cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
                $cadenaSql .= " WHERE \"ORG_ESTADO\"='A' ";

                break;

            case "informacion_ordenador" :
                $cadenaSql = " SELECT \"ORG_NOMBRE\",\"ORG_IDENTIFICACION\"  ";
                $cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
                $cadenaSql .= " WHERE  \"ORG_IDENTIFICACION\"='" . $variable . "' ";
                $cadenaSql .= " AND \"ORG_ESTADO\"='A' ";
                break;

            case "consultarContratos" :
                $cadenaSql = "SELECT id_contrato,numero_contrato||' - ('||fecha_contrato||') ' contrato ";
                $cadenaSql .= "FROM contratos ";

                break;

            case "consultarInfoContrato" :
                $cadenaSql = "SELECT contratos.*,rd.documento_ruta  ";
                $cadenaSql .= " FROM contratos ";
                $cadenaSql .= "JOIN registro_documento  rd  ON rd.documento_id = contratos.id_documento_soporte ";
                $cadenaSql .= " WHERE id_contrato='" . $variable . "'; ";

                break;

            case "informacionContrato" :
                $cadenaSql = "SELECT contratos.*,rd.documento_ruta  ";
                $cadenaSql .= " FROM contratos ";
                $cadenaSql .= "JOIN registro_documento  rd  ON rd.documento_id = contratos.id_documento_soporte ";
                $cadenaSql .= " WHERE id_contrato='" . $variable . "'; ";
                break;
        }

        return $cadenaSql;
    }

}

?>
