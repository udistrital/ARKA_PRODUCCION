<?php

namespace inventarios\gestionSalidas\registrarSalidas;

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
			
			/**
			 * Clausulas Del Caso Uso.
			 */
			case "buscar_Proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  value,\"PRO_NIT\"  AS data  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= "WHERE cast(\"PRO_NIT\" as text) LIKE '%" . $variable . "%' ";
				$cadenaSql .= "OR \"PRO_RAZON_SOCIAL\" LIKE '%" . $variable . "%' LIMIT 10; ";
				
				break;
			
			case "buscar_entradas" :
				
				$cadenaSql = "SELECT DISTINCT entrada.id_entrada, entrada.consecutivo||' - ('||entrada.vigencia||')' entradas ";
				$cadenaSql .= " FROM entrada  ";
				$cadenaSql .= " JOIN elemento ON elemento.id_entrada = entrada.id_entrada ";
				// $cadenaSql .= "JOIN elemento_individual ei ON ei.id_elemento_gen = elemento.id_elemento ";
				$cadenaSql .= " WHERE cierre_contable ='f' ";
				$cadenaSql .= " AND estado_entrada = 1  ";
				$cadenaSql .= " AND entrada.id_entrada NOT IN (SELECT id_entrada FROM salida) ";
				// $cadenaSql .= "AND ei.id_salida IS NULL ";
				$cadenaSql .= "AND entrada.estado_registro='t' ";
				$cadenaSql .= "ORDER BY entrada.id_entrada DESC ";
				
				break;
			
			case "funcionarios" :
				$cadenaSql = "SELECT \"FUN_IDENTIFICACION\", \"FUN_IDENTIFICACION\" ||' - '||  \"FUN_NOMBRE\" ";
				$cadenaSql .= "FROM  arka_parametros.arka_funcionarios ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				$cadenaSql .= "AND \"FUN_IDENTIFICACION\"<>'899999230' ";
				
				$cadenaSql .= "AND \"FUN_IDENTIFICACION\"<>'0' ";
				
				break;
			case "dependencia" :
				$cadenaSql = " SELECT DEP_IDENTIFICADOR, DEP_IDENTIFICADOR ||' - ' ||DEP_DEPENDENCIA  ";
				$cadenaSql .= "FROM DEPENDENCIAS ";
				break;
			
			case "proveedor_informacion" :
				$cadenaSql = " SELECT \"PRO_NIT\", \"PRO_RAZON_SOCIAL\"  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor ";
				$cadenaSql .= " WHERE \"PRO_NIT\"='" . $variable . "'";
				
				break;
			
			case "proveedores" :
				$cadenaSql = " SELECT PRO_NIT,PRO_NIT||' - '||PRO_RAZON_SOCIAL AS proveedor ";
				$cadenaSql .= " FROM PROVEEDORES ";
				
				break;
			
			case "consultarEntrada_busqueda" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "entrada.id_entrada, entrada.fecha_registro,  ";
				$cadenaSql .= " clase_entrada.descripcion, proveedor ,consecutivo||' - ('||vigencia||')' consecutivos, entrada.vigencia   ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "JOIN clase_entrada ON clase_entrada.id_clase = entrada.clase_entrada ";
				$cadenaSql .= "JOIN elemento ON elemento.id_entrada = entrada.id_entrada ";
				$cadenaSql .= "WHERE 1=1 ";
				$cadenaSql .= "AND entrada.cierre_contable='f' ";
				$cadenaSql .= "AND entrada.estado_entrada = 1 ";
				$cadenaSql .= "AND entrada.estado_registro='t' ";
				$cadenaSql .= " AND cantidad-cantidad_por_asignar >= 0";
				if ($variable [0] != '') {
					$cadenaSql .= " AND entrada.id_entrada = '" . $variable [0] . "'";
				}
				
				if ($variable [1] != '') {
					$cadenaSql .= " AND entrada.fecha_registro BETWEEN CAST ( '" . $variable [1] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [2] . "' AS DATE)  ";
				}
				
				if ($variable [3] != '') {
					$cadenaSql .= " AND clase_entrada = '" . $variable [3] . "'";
				}
				if ($variable [4] != '') {
					$cadenaSql .= " AND entrada.proveedor = '" . $variable [4] . "'";
				}
				
				break;
			
			case "clase_entrada" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_clase, descripcion  ";
				$cadenaSql .= "FROM clase_entrada;";
				break;
			
			case "consultarEntrada" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "id_entrada, fecha_registro,  ";
				$cadenaSql .= "nit, razon_social  ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "JOIN proveedor ON proveedor.id_proveedor = entrada.proveedor ";
				$cadenaSql .= "WHERE 1=1 AND id_salida ='0' ";
				
				if ($variable [0] != '') {
					$cadenaSql .= " AND id_entrada = '" . $variable [0] . "'";
				}
				if ($variable [1] != '') {
					$cadenaSql .= " AND  nit= '" . $variable [1] . "'";
				}
				if ($variable [2] != '') {
					$cadenaSql .= " AND  nombre= '" . $variable [3] . "'";
				}
				
				if ($variable [3] != '') {
					$cadenaSql .= " AND fecha_registro BETWEEN CAST ( '" . $variable [3] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [4] . "' AS DATE)  ";
				}
				break;
			
			case "consultarEntradaParticular" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "fecha_registro, vigencia, clase_entrada, descripcion ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "JOIN clase_entrada ON clase_entrada.id_clase = entrada.clase_entrada ";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "';";
				
				break;
			
			case "clase_entrada_descrip" :
				
				$cadenaSql = "SELECT descripcion ";
				$cadenaSql .= "FROM clase_entrada ";
				$cadenaSql .= "WHERE id_clase='" . $variable . "';";
				
				break;
			
			case "consulta_proveedor" :
				
				$cadenaSql = "SELECT razon_social ";
				$cadenaSql .= "FROM proveedor ";
				$cadenaSql .= "WHERE id_proveedor='" . $variable . "';";
				
				break;
			
			case "consultar_nivel_inventario" :
				
				$cadenaSql = "SELECT id_catalogo,(codigo||' - '||nombre) AS nivel ";
				$cadenaSql .= "FROM catalogo_elemento ;";
				
				break;
			
			case "consulta_elementos" :
				$cadenaSql = "SELECT e.id_elemento, ce.elemento_codigo||' - '||ce.elemento_nombre AS item, e.cantidad, e.descripcion ,  cantidad_por_asignar, ";
				$cadenaSql .= "  (cantidad-cantidad_por_asignar) cantidad_asignada";
				$cadenaSql .= " FROM elemento e";
				$cadenaSql .= " JOIN  catalogo.catalogo_elemento  ce ON ce.elemento_id = e.nivel ";
				$cadenaSql .= "JOIN catalogo.catalogo_lista cl ON cl.lista_id = ce.elemento_catalogo  ";
				$cadenaSql .= "WHERE e.id_entrada='" . $variable . "' ";
				$cadenaSql .= "AND cantidad_por_asignar <> 0 ;";
				
				break;
			
			case "dependencias" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";
				
				break;
			case "dependenciasConsultadas" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE sa.\"ESF_ID_SEDE\"='" . $variable . "' ";
				$cadenaSql .= " AND  ad.\"ESF_ESTADO\"='A'";
				
				break;
			
			case "ubicacionesConsultadas" :
				$cadenaSql = "SELECT DISTINCT  ef.\"ESF_ID_ESPACIO\" , ef.\"ESF_NOMBRE_ESPACIO\" ";
				$cadenaSql .= " FROM arka_parametros.arka_espaciosfisicos ef  ";
				$cadenaSql .= " JOIN arka_parametros.arka_dependencia ad ON ad.\"ESF_ID_ESPACIO\"=ef.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_CODIGO_DEP\"='" . $variable [0] . "' ";
				$cadenaSql .= " AND  sa.\"ESF_ID_SEDE\"='" . $variable [1] . "' ";
				$cadenaSql .= " AND  ef.\"ESF_ESTADO\"='A'";
				
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
				$cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
				
				break;
			
			case "consultar_dependencia" :
				
				$cadenaSql = "SELECT id_dependencia, (id_dependencia||' - '|| nombre) AS Dependencia ";
				$cadenaSql .= "FROM dependencia ;";
				
				break;
			
			case "consultar_ubicacion" :
				
				$cadenaSql = "SELECT id_seccion,nombre  ";
				$cadenaSql .= "FROM seccion;";
				
				break;
			
			case "insertar_funcionario" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " funcionario(";
				$cadenaSql .= "  nombre, identificacion)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "') ";
				$cadenaSql .= "RETURNING  id_funcionario; ";
				
				break;
			
			case "insertar_salida" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " salida( fecha_registro, dependencia, funcionario, observaciones,";
				$cadenaSql .= " id_entrada,sede,ubicacion,vigencia,id_salida)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "" . $variable [3] . ",";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "',";
				$cadenaSql .= "'" . $variable [7] . "',";
				$cadenaSql .= "'" . $variable [8] . "') ";
				$cadenaSql .= "RETURNING  id_salida,consecutivo ; ";
				
				break;
			
			case "id_salida_maximo" :
				$cadenaSql = " SELECT MAX(id_salida) ";
				$cadenaSql .= " FROM salida ";
				break;
			
			case "insertar_salida_item" :
				$cadenaSql = "UPDATE items_actarecibido ";
				$cadenaSql .= "SET id_salida ='" . $variable [1] . "'  ";
				$cadenaSql .= "WHERE id_items='" . $variable [0] . "';";
				
				break;
			case "actualizar_entrada" :
				$cadenaSql = "UPDATE entrada ";
				$cadenaSql .= "SET id_salida ='TRUE'  ";
				$cadenaSql .= "WHERE id_entrada='" . $variable [1] . "';";
				
				break;
			
			case "busqueda_elementos_individuales" :
				$cadenaSql = " SELECT id_elemento id, cantidad_por_asignar,  ";
				$cadenaSql .= " cantidad, (cantidad-cantidad_por_asignar) cantidad_asignada, tipo_bien  ";
				$cadenaSql .= " FROM arka_inventarios.elemento   ";
				$cadenaSql .= " WHERE id_elemento ='" . $variable . "' ";
				$cadenaSql .= "ORDER BY id ASC;";
				break;
			
			case "busqueda_elementos_bienes" :
				$cadenaSql = "SELECT count(*) conteo, el.tipo_bien   ";
				$cadenaSql .= "FROM elemento_individual ei ";
				$cadenaSql .= "JOIN  elemento el ON el.id_elemento=ei.id_elemento_gen  ";
				$cadenaSql .= "JOIN tipo_bienes  tb ON tb.id_tipo_bienes = el.tipo_bien  ";
				$cadenaSql .= "JOIN salida  sa ON sa.id_salida = ei.id_salida  ";
				$cadenaSql .= "WHERE sa.id_salida ='" . $variable . "' ";
				$cadenaSql .= "GROUP BY el.tipo_bien  ";
				// $cadenaSql .= "ORDER BY id ASC;";
				
				break;
			
			case "busqueda_elementos_individuales_cantidad_restante" :
				$cadenaSql = "SELECT id_elemento_ind  id, cantidad_asignada ";
				$cadenaSql .= "FROM elemento_individual  ";
				$cadenaSql .= "WHERE id_elemento_gen ='" . $variable . "'";
				$cadenaSql .= "AND id_salida IS NOT NULL ";
				$cadenaSql .= "ORDER BY id ASC;";
				
				break;
			
			case "actualizar_elementos_individuales" :
				$cadenaSql = "UPDATE elemento_individual ";
				$cadenaSql .= "SET id_salida='" . $variable ['id_salida'] . "', ";
				$cadenaSql .= " funcionario='" . $variable ['funcionario'] . "', ";
				$cadenaSql .= " ubicacion_elemento='" . $variable ['ubicacion'] . "', ";
				$cadenaSql .= " cantidad_asignada='" . $variable ['cantidad'] . "' ";
				$cadenaSql .= "WHERE id_elemento_gen ='" . $variable ['id_elemento'] . "';";
				break;
			
			case "actualizar_elemento_general" :
				$cadenaSql = "UPDATE elemento ";
				$cadenaSql .= "SET cantidad_por_asignar=0 ";
				$cadenaSql .= "WHERE id_elemento ='" . $variable . "';";
				break;
			
			case "actualizar_elemento_general_consumo" :
				$cadenaSql = "UPDATE elemento ";
				$cadenaSql .= "SET cantidad_por_asignar=cantidad_por_asignar-" . $variable ['cantidad'] . " ";
				$cadenaSql .= "WHERE id_elemento ='" . $variable ['id_elemento'] . "';";
				break;
			
			case "consulta_elementos_validar" :
				$cadenaSql = "SELECT COUNT(id_elemento) ";
				$cadenaSql .= " FROM elemento  ";
				$cadenaSql .= "WHERE 1=1";
				$cadenaSql .= " AND cantidad_por_asignar>0 ";
				$cadenaSql .= " AND id_entrada='" . $variable . "' ";
				
				break;
			
			case 'consultaConsecutivo' :
				$cadenaSql = "SELECT consecutivo ";
				$cadenaSql .= "FROM salida  ";
				$cadenaSql .= "WHERE  fecha_registro='" . $variable . "';";
				
				break;
			
			case 'reiniciarConsecutivo' :
				$cadenaSql = "SELECT SETVAL((SELECT pg_get_serial_sequence('salida', 'consecutivo')), 1, false);";
				break;
			
			case 'SalidaContableVigencia' :
				$cadenaSql = "SELECT max(consecutivo) ";
				$cadenaSql .= "FROM salida_contable  ";
				$cadenaSql .= "WHERE  tipo_bien='" . $variable [1] . "' ";
				$cadenaSql .= "AND  vigencia='" . $variable [0] . "' ";
				
				break;
			
			case "InsertarSalidaContable" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " salida_contable(
						        fecha_registro, salida_general, tipo_bien, 
            					vigencia, consecutivo)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "') ";
				$cadenaSql .= "RETURNING  id_salida_contable; ";
				
				break;
			
			case "ingresar_elemento_individual" :
				
				$cadenaSql = " 	INSERT INTO elemento_individual(";
				$cadenaSql .= "fecha_registro, id_elemento_gen,id_elemento_ind, funcionario, ubicacion_elemento, id_salida,cantidad_asignada) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable ['fecha_registro'] . "',";
				$cadenaSql .= "'" . $variable ['id_elemento_gen'] . "',";
				$cadenaSql .= "'" . $variable ['id_elemento'] . "',";
				$cadenaSql .= "'" . $variable ['funcionario'] . "',";
				$cadenaSql .= "'" . $variable ['ubicacion_elemento'] . "',";
				$cadenaSql .= "'" . $variable ['id_salida'] . "',";
				$cadenaSql .= "'" . $variable ['cantidad_asignada'] . "') ";
				$cadenaSql .= "RETURNING id_elemento_ind; ";
				break;
			
			case "idElementoMaxIndividual" :
				$cadenaSql = "SELECT max(id_elemento_ind) ";
				$cadenaSql .= "FROM elemento_individual  ";
				break;
			
			case "consultaNivel" :
				$cadenaSql = "SELECT  ce.elemento_codigo codigo_elemento ";
				$cadenaSql .= "FROM elemento ";
				$cadenaSql .= "JOIN elemento_individual ON elemento_individual.id_elemento_gen = elemento.id_elemento ";
				$cadenaSql .= "JOIN  catalogo.catalogo_elemento  ce ON  ce.elemento_id= elemento.nivel ";
				$cadenaSql .= "JOIN catalogo.catalogo_lista cl ON cl.lista_id = ce.elemento_catalogo  ";
				$cadenaSql .= "WHERE cl.lista_activo = 1  ";
				$cadenaSql .= "AND  elemento_individual.id_elemento_ind ='" . $variable . "'";
				break;
			
			case "elementos_salida" :
				$cadenaSql = " SELECT ei.id_elemento_ind, nivel, sal.fecha_registro fecha_salida, total_iva_con, grupo_id, grupo_vidautil, sal.id_salida ";
				$cadenaSql .= " FROM arka_inventarios.salida sal ";
				$cadenaSql .= " JOIN arka_inventarios.elemento_individual ei ON ei.id_salida=sal.id_salida ";
				$cadenaSql .= " JOIN arka_inventarios.elemento ele ON ele.id_elemento=ei.id_elemento_gen ";
				$cadenaSql .= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel ";
				$cadenaSql .= " JOIN grupo.grupo_descripcion ON catalogo.catalogo_elemento.elemento_grupoc=CAST(grupo.grupo_descripcion.grupo_id as character varying)   ";
				$cadenaSql .= " WHERE 1=1 ";
				$cadenaSql .= " AND sal.id_salida='" . $variable . "' ";
				$cadenaSql .= " AND ele.estado=TRUE ";
				$cadenaSql .= " AND ei.estado_registro=TRUE ";
				$cadenaSql .= " AND sal.estado_registro=TRUE ";
				$cadenaSql .= " AND grupo_depreciacion=TRUE ";
				break;
			
			case "registro_detalle_depreciacion" :
				$cadenaSql = " INSERT INTO arka_inventarios.detalle_depreciacion (id_elemento_ind, ";
				$cadenaSql .= " id_salida,fecha_salida, grupo_contable,vida_util,valor, valor_cuota) VALUES (";
				$cadenaSql .= " '" . $variable ['id_elemento_ind'] . "',";
				$cadenaSql .= " '" . $variable ['id_salida'] . "',";
				$cadenaSql .= " '" . $variable ['fecha_salida'] . "',";
				$cadenaSql .= " '" . $variable ['grupo_contable'] . "',";
				$cadenaSql .= " '" . $variable ['vida_util'] . "',";
				$cadenaSql .= " '" . $variable ['valor'] . "',";
				$cadenaSql .= " '" . $variable ['valor_cuota'] . "'";
				$cadenaSql .= " );";
				break;
			
			// _________________________________________________
		}
		return $cadenaSql;
	}
}

?>
