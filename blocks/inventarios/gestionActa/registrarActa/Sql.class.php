<?php

namespace inventarios\gestionActa\registrarActa;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {
	var $miConfigurador;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	function getCadenaSql($tipo, $variable = "") {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
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
				$cadenaSql .= "'" . time () . "' ";
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
				
				foreach ( $_REQUEST as $clave => $valor ) {
					$cadenaSql .= "( ";
					$cadenaSql .= "'" . $idSesion . "', ";
					$cadenaSql .= "'" . $variable ['formulario'] . "', ";
					$cadenaSql .= "'" . $clave . "', ";
					$cadenaSql .= "'" . $valor . "', ";
					$cadenaSql .= "'" . $variable ['fecha'] . "' ";
					$cadenaSql .= "),";
				}
				
				$cadenaSql = substr ( $cadenaSql, 0, (strlen ( $cadenaSql ) - 1) );
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
			
			// -----------------------------** Cláusulas del caso de uso**----------------------------------//
			
			// --- Caragar elemento--
			
			case "consultar_nivel_inventario" :
				
				$cadenaSql = "SELECT ce.elemento_id, ce.elemento_codigo||' - '||ce.elemento_nombre ";
				$cadenaSql .= "FROM grupo.catalogo_elemento  ce ";
				$cadenaSql .= "JOIN grupo.catalogo_lista cl ON cl.lista_id = ce.elemento_catalogo  ";
				$cadenaSql .= "WHERE cl.lista_activo = 1  ";
				$cadenaSql .= "AND  ce.elemento_id > 0  ";
				$cadenaSql .= "AND  ce.elemento_padre > 0  ";
				$cadenaSql .= "ORDER BY ce.elemento_codigo ASC ;";
				
				break;
			
			case "consultar_tipo_poliza" :
				
				$cadenaSql = "SELECT id_tipo_poliza, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.tipo_poliza;";
				
				break;
			
			case "consultar_tipo_iva" :
				
				$cadenaSql = "SELECT id_iva, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.aplicacion_iva;";
				
				break;
			
			case "consultar_iva" :
				
				$cadenaSql = "SELECT iva ";
				$cadenaSql .= "FROM arka_inventarios.aplicacion_iva ";
				$cadenaSql .= "WHERE id_iva='" . $variable . "';";
				
				break;
			
			case "ConsultaTipoBien" :
				
				$cadenaSql = "SELECT  ce.elemento_tipobien , tb.descripcion  ";
				$cadenaSql .= "FROM grupo.catalogo_elemento ce ";
				$cadenaSql .= "JOIN  arka_inventarios.tipo_bienes tb ON tb.id_tipo_bienes = ce.elemento_tipobien  ";
				$cadenaSql .= "WHERE ce.elemento_id = '" . $variable . "';";
				
				break;
			
			case "ingresar_elemento_tipo_1" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " elemento_acta_recibido(
							             fecha_registro, nivel, tipo_bien, descripcion, 
							            cantidad, unidad, valor, iva, subtotal_sin_iva, total_iva, total_iva_con, 
							             marca, serie, id_acta) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "',";
				$cadenaSql .= "'" . $variable [7] . "',";
				$cadenaSql .= "'" . $variable [8] . "',";
				$cadenaSql .= "'" . $variable [9] . "',";
				$cadenaSql .= "'" . $variable [10] . "',";
				$cadenaSql .= (is_null ( $variable [11] ) == true) ? ' NULL , ' : "'" . $variable [11] . "',";
				$cadenaSql .= (is_null ( $variable [12] ) == true) ? ' NULL , ' : "'" . $variable [12] . "',";
				$cadenaSql .= "'" . $variable [13] . "') ";
				$cadenaSql .= "RETURNING  id_elemento_ac ";
				
				break;
			
			case "ingresar_elemento_tipo_2" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " elemento_acta_recibido(";
				$cadenaSql .= "  fecha_registro, nivel, tipo_bien, descripcion,
											 cantidad, unidad, valor, iva, subtotal_sin_iva, total_iva, total_iva_con,
											 tipo_poliza, fecha_inicio_pol, fecha_final_pol, marca, serie,
											 id_acta)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "',";
				$cadenaSql .= "'" . $variable [7] . "',";
				$cadenaSql .= "'" . $variable [8] . "',";
				$cadenaSql .= "'" . $variable [9] . "',";
				$cadenaSql .= "'" . $variable [10] . "',";
				$cadenaSql .= "'" . $variable [11] . "',";
				if ($variable [11] == 0) {
					
					$cadenaSql .= "NULL,";
					$cadenaSql .= "NULL,";
				} else {
					
					$cadenaSql .= "'" . $variable [12] . "',";
					$cadenaSql .= "'" . $variable [13] . "',";
				}
				
				$cadenaSql .= (is_null ( $variable [14] ) == true) ? ' NULL , ' : "'" . $variable [14] . "',";
				$cadenaSql .= (is_null ( $variable [15] ) == true) ? ' NULL , ' : "'" . $variable [15] . "',";
				
				$cadenaSql .= "'" . $variable [16] . "') ";
				$cadenaSql .= "RETURNING  id_elemento_ac; ";
				
				break;
			
			case "ElementoImagen" :
				
				$cadenaSql = " 	INSERT INTO asignar_imagen_acta(";
				$cadenaSql .= " id_elemento_acta, imagen ) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable ['elemento'] . "',";
				$cadenaSql .= "'" . $variable ['imagen'] . "') ";
				$cadenaSql .= "RETURNING id_imagen; ";
				
				break;
			
			// --------------------------------
			
			case "consultarCompras" :
				$cadenaSql = " SELECT  oc.*,ap.\"PRO_NIT\"||' - ('||ap.\"PRO_RAZON_SOCIAL\"||')' AS  nombre_proveedor ";
				$cadenaSql .= " FROM orden_compra oc";
				$cadenaSql .= " JOIN arka_parametros.arka_proveedor ap ON ap.\"PRO_NIT\"= CAST(oc.id_proveedor AS CHAR (50)) ";
				$cadenaSql .= " WHERE id_orden_compra ='" . $variable . "';";
				
				break;
			
			case "indentificacion_contratista" :
				$cadenaSql = " SELECT  *, identificacion||' - ('||nombre_razon_social||')' AS nom_razon  ";
				$cadenaSql .= " FROM contratista_servicios";
				$cadenaSql .= " WHERE id_contratista ='" . $variable . "';";
				break;
			
			case "buscar_Proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  value,\"PRO_NIT\"  AS data  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= "WHERE cast(\"PRO_NIT\" as text) LIKE '%" . $variable . "%' ";
				$cadenaSql .= "OR \"PRO_RAZON_SOCIAL\" LIKE '%" . $variable . "%' LIMIT 10; ";
				
				break;
			
			// ----------------------------------
			
			case "consultarOrden" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " to_id,";
				$cadenaSql .= " to_nombre ";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " arka_inventarios.tipo_orden ";
				$cadenaSql .= " WHERE to_estado='1';";
				break;
			
			case "consultarOrdenCompra" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "id_orden_compra, fecha_registro  ";
				$cadenaSql .= "  ";
				$cadenaSql .= "FROM orden_compra ";
				// $cadenaSql .= "JOIN proveedor ON proveedor.id_proveedor = orden_compra.id_proveedor ";
				// $cadenaSql .= "JOIN dependencia ON dependencia.id_dependencia = orden_compra.id_dependencia ";
				$cadenaSql .= "WHERE 1=1";
				if ($variable [0] != '') {
					$cadenaSql .= " AND fecha_registro BETWEEN CAST ( '" . $variable [0] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [1] . "' AS DATE)  ";
				}
				if ($variable [2] != '') {
					$cadenaSql .= " AND id_orden_compra = '" . $variable [2] . "'";
				}
				
				break;
			
			case "OrdenConsultada" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "orden.id_orden, orden.fecha_registro,  ";
				$cadenaSql .= "identificacion, dependencia_solicitante , orden.sede,
								CASE orden.tipo_orden
										WHEN 1 THEN orden.vigencia || ' - ' ||orden.consecutivo_compras
										WHEN 9 THEn orden.vigencia || ' - ' ||orden.consecutivo_servicio
								 END identificador, 
								id_actarecibido ";
				$cadenaSql .= "FROM orden ";
				$cadenaSql .= "LEFT JOIN registro_actarecibido ar ON ar.numero_orden = orden.id_orden ";
				$cadenaSql .= "JOIN contratista_servicios ON contratista_servicios.id_contratista = orden.id_contratista ";
				$cadenaSql .= "WHERE 1=1";
				if ($variable [0] != '') {
					$cadenaSql .= " AND orden.fecha_registro BETWEEN CAST ( '" . $variable [0] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [1] . "' AS DATE)  ";
				}
				if ($variable [2] != '') {
					$cadenaSql .= " AND orden.tipo_orden = '" . $variable [2] . "'";
				}
				
				break;
			
			case "consultarOrdenOtros" :
				
				$cadenaSql = "";
				// echo $cadenaSql;exit;
				break;
			
			case "tipoOrden" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " to_id,";
				$cadenaSql .= " to_nombre ";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " tipo_orden ";
				$cadenaSql .= " WHERE to_estado='1' ";
				$cadenaSql .= " ORDER BY to_id DESC ; ";
				
				break;
			
			case "tipoOrden_nombre" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " to_nombre ";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " arka_inventarios.tipo_orden ";
				$cadenaSql .= " WHERE to_estado='1' ";
				$cadenaSql .= " AND to_id='" . $variable . "' ";
				break;
			
			// case "ordenador_gasto" :
			// $cadenaSql = " SELECT ORG_IDENTIFICADOR, ORG_ORDENADOR_GASTO ";
			// $cadenaSql .= " FROM ORDENADORES_GASTO ";
			// break;
			
			case "informacion_ordenador" :
				$cadenaSql = " SELECT \"ORG_NOMBRE\",\"ORG_IDENTIFICACION\"  ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE  \"ORG_IDENTIFICACION\"='" . $variable . "' ";
				$cadenaSql .= " AND \"ORG_ESTADO\"='A' ";
				
				break;
			
			case "tipoComprador" :
				
				$cadenaSql = " 	SELECT \"ORG_IDENTIFICACION\",\"ORG_ORDENADOR_GASTO\" ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE \"ORG_ESTADO\"='A' ";
				
				break;
			
			case "tipoAccion" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " ta_idaccion,";
				$cadenaSql .= " ta_descripcion ";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " arka_inventarios.tipo_accion ";
				$cadenaSql .= " WHERE ta_estado='1';";
				break;
			
			case "tipoBien" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " tb_idbien,";
				$cadenaSql .= " tb_descripcion ";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " arka_inventarios.tipo_bien ";
				$cadenaSql .= " WHERE tb_estado='1';";
				break;
			
			// ---------- Para registrar los items de la factura ------------//
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
			
			case "limpiar_tabla_items" :
				$cadenaSql = " DELETE FROM ";
				$cadenaSql .= " arka_inventarios.items_actarecibido_temp";
				$cadenaSql .= " WHERE seccion ='" . $variable . "';";
				break;
			
			case "insertarItem" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " arka_inventarios.items_actarecibido_temp(";
				$cadenaSql .= " id_items,item,cantidad, descripcion,";
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
			
			case "eliminarItem" :
				$cadenaSql = " DELETE FROM ";
				$cadenaSql .= " arka_inventarios.items_actarecibido_temp";
				$cadenaSql .= " WHERE id_items ='" . $variable . "';";
				break;
			
			case "id_items_temporal" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " max(id_items)";
				$cadenaSql .= " FROM arka_inventarios.items_actarecibido_temp;";
				break;
			
			case "Orden_Consultada" :
				$cadenaSql = " SELECT  * ";
				$cadenaSql .= " FROM orden ";
				$cadenaSql .= " WHERE id_orden ='" . $variable . "';";
				break;
			
			// ----homologacion Dependencias
			// case "ids" :
			// $cadenaSql = " SELECT id_actarecibido, dependencia ";
			// $cadenaSql .= " FROM registro_actarecibido ";
			
			// break;
			
			// case "update_dependencia" :
			// $cadenaSql = " UPDATE registro_actarecibido ";
			// $cadenaSql .= "SET dependencia=(SELECT \"ESF_CODIGO_DEP\"
			// FROM arka_parametros.arka_dependencia
			// WHERE \"ESF_ID_ESPACIO\"='" . $variable [1] . "')";
			// $cadenaSql .= "WHERE id_actarecibido= '" . $variable [0] . "' ";
			// $cadenaSql .= "AND dependencia= '" . $variable [1] . "' ";
			
			// break;
			
			/* **** */
			
			case "insertarActa" :
				$cadenaSql = " INSERT INTO registro_actarecibido( ";
				$cadenaSql .= " sede, dependencia, fecha_recibido, tipo_bien,
						proveedor, ordenador_gasto, tipo_orden,
						fecha_revision, revisor, observacionesacta, enlace_soporte, nombre_soporte,numero_orden,
						estado_registro, fecha_registro, id_contrato )";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable ['sede'] . "',";
				$cadenaSql .= "'" . $variable ['dependencia'] . "',";
				$cadenaSql .= "'" . $variable ['fecha_registro'] . "',";
				$cadenaSql .= "'" . $variable ['tipo_bien'] . "',";
				$cadenaSql .= (is_null ( $variable ['nitproveedor'] ) == true) ? ' NULL , ' : "'" . $variable ['nitproveedor'] . "',";
				$cadenaSql .= (is_null ( $variable ['ordenador'] ) == true) ? ' NULL , ' : "'" . $variable ['ordenador'] . "',";
				$cadenaSql .= "'" . $variable ['tipo_orden'] . "',";
				$cadenaSql .= "'" . $variable ['fecha_revision'] . "',";
				$cadenaSql .= "NULL,";
				$cadenaSql .= "'" . $variable ['observacion'] . "',";
				$cadenaSql .= (is_null ( $variable ['enlace_soporte'] ) == true) ? "NULL, " : "'" . $variable ['enlace_soporte'] . "',";
				$cadenaSql .= (is_null ( $variable ['nombre_soporte'] ) == true) ? "NULL , " : "'" . $variable ['nombre_soporte'] . "',";
				$cadenaSql .= "" . $variable ['numero_orden'] . ", ";
				$cadenaSql .= "'" . $variable ['estado'] . "',";
				$cadenaSql .= "'" . $variable ['fecha_registro'] . "',";
				$cadenaSql .= (is_null ( $variable ['identificador_contrato'] ) == true) ? "NULL ) " : "'" . $variable ['identificador_contrato'] . "') ";
				$cadenaSql .= "RETURNING  id_actarecibido; ";
				
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
				$cadenaSql .= "'" . date ( 'Y-m-d' ) . "');";
				break;
			
			// Consultas de Oracle para rescate de información de Sicapital
			/*
			 * case "dependencias": $cadenaSql = "SELECT DEP_IDENTIFICADOR, "; $cadenaSql.= " DEP_IDENTIFICADOR ||' '|| DEP_DEPENDENCIA "; //$cadenaSql .= " DEP_DIRECCION,DEP_TELEFONO ";F $cadenaSql.= " FROM DEPENDENCIAS "; break;
			 */
			
			case "dependenciasConsultadas" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE sa.\"ESF_ID_SEDE\"='" . $variable . "' ";
				$cadenaSql .= " AND  ad.\"ESF_ESTADO\"='A'";
				
				break;
			case "dependencias" :
				
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";
				
				break;
				
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
				$cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
				
				break;
			
			case "proveedores" :
				
				$cadenaSql = " SELECT \"PRO_NIT\",\"PRO_NIT\"||' - '||\"PRO_RAZON_SOCIAL\" AS proveedor ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor ";
				
				break;
			
			case "select_proveedor" :
				$cadenaSql = "SELECT PRO_RAZON_SOCIAL";
				$cadenaSql .= " FROM PROVEEDORES ";
				$cadenaSql .= " WHERE PRO_NIT='" . $variable . "' ";
				break;
			
			case "contratistas" :
				$cadenaSql = "SELECT CON_IDENTIFICACION ||' '|| CON_NOMBRE, ";
				/*
				 * $cadenaSql .= " CON_CARGO, "; $cadenaSql .= " CON_DIRECCION, "; $cadenaSql .= " CON_TELEFONO ";
				 */
				$cadenaSql .= " FROM CONTRATISTAS ";
				break;
			
			case "consultarContratos" :
				$cadenaSql = "SELECT id_contrato,numero_contrato||' - ('||fecha_contrato||') ' contrato ";
				$cadenaSql .= "FROM contratos ";
				$cadenaSql .= "WHERE 1=1";
				if ($variable [0] != '') {
					$cadenaSql .= " AND contratos.fecha_registro BETWEEN CAST ( '" . $variable [0] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [1] . "' AS DATE)  ";
				}
				
				break;
			
			case "informacionContrato" :
				$cadenaSql = "SELECT contratos.*,rd.documento_ruta  ";
				$cadenaSql .= " FROM contratos ";
				$cadenaSql .= "JOIN registro_documento  rd  ON rd.documento_id = contratos.id_documento_soporte ";
				$cadenaSql .= " WHERE id_contrato='" . $variable . "'; ";
				break;
			
			case "ConsultaElementosOrden" :
				$cadenaSql = "SELECT id_elemento_ac   ";
				$cadenaSql .= " FROM elemento_acta_recibido ";
				$cadenaSql .= " WHERE id_orden='" . $variable . "'; ";
				
				break;
			
			case "RegistrarActaElementos" :
				$cadenaSql = "UPDATE elemento_acta_recibido ";
				$cadenaSql .= "SET id_acta='" . $variable [1] . "' ";
				$cadenaSql .= "WHERE id_elemento_ac='" . $variable [0] . "'; ";
			
				break;
		}
		return $cadenaSql;
	}
}

?>
