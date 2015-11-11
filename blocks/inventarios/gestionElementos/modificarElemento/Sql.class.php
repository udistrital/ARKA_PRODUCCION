<?php

namespace inventarios\gestionElementos\modificarElemento;

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
			
			case "buscar_placa_maxima" :
				$cadenaSql = " SELECT  MAX(placa::FLOAT) placa_max ";
				$cadenaSql .= " FROM elemento_individual ";
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
			
			case "ingresar_elemento_individual" :
				
				$cadenaSql = " 	INSERT INTO elemento_individual(";
				$cadenaSql .= "fecha_registro, placa, serie, id_elemento_gen,id_elemento_ind,id_salida,funcionario,ubicacion_elemento,cantidad_asignada) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= ((is_null ( $variable [1] )) ? 'null' . "," : "'" . $variable [1] . "',");
				$cadenaSql .= ((is_null ( $variable [2] )) ? 'null' . "," : "'" . $variable [2] . "',");
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "',";
				if (is_null ( $variable ['id_salida'] ) == false) {
					$cadenaSql .= "'" . $variable ['id_salida'] . "',";
				} else {
					$cadenaSql .= "NULL,";
				}
				if (is_null ( $variable ['funcionario'] ) == false) {
					$cadenaSql .= "'" . $variable ['funcionario'] . "',";
				} else {
					$cadenaSql .= "NULL,";
				}
				if (is_null ( $variable ['ubicacion_elemento'] ) == false) {
					$cadenaSql .= "'" . $variable ['ubicacion_elemento'] . "',";
				} else {
					$cadenaSql .= "NULL,";
				}
				
				$cadenaSql .= "'" . $variable ['cantidad_asignada'] . "') ";
				$cadenaSql .= "RETURNING id_elemento_ind; ";
				
				break;
			
			case "Informacion_Elemento" :
				$cadenaSql = "SELECT *  ";
				$cadenaSql .= " FROM elemento ";
				$cadenaSql .= " WHERE  id_elemento='" . $variable . "';";
				
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A'";
				
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
			
			case "funcionarios" :
				
				$cadenaSql = "SELECT \"FUN_IDENTIFICACION\", \"FUN_IDENTIFICACION\" ||' - '||  \"FUN_NOMBRE\" ";
				$cadenaSql .= "FROM  arka_parametros.arka_funcionarios ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				
				break;
			
			case "buscar_placa" :
				$cadenaSql = " SELECT DISTINCT placa, placa as placas ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "WHERE placa IS NOT NULL  ";
				$cadenaSql .= "ORDER BY placa DESC ;";
				
				break;
			
			case "buscar_serie" :
				$cadenaSql = " SELECT DISTINCT serie, serie as series ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "WHERE  serie IS NOT NULL ";
				$cadenaSql .= "ORDER BY serie DESC ";
				
				break;
			
			case "buscar_entradas" :
				
				$cadenaSql = "SELECT DISTINCT en.id_entrada, en.consecutivo||' - ('||en.vigencia||')' entradas ";
				$cadenaSql .= "FROM entrada en ";
				$cadenaSql .= "JOIN  elemento el ON el.id_entrada=en.id_entrada ";
				$cadenaSql .= "WHERE en.cierre_contable='f' ";
				$cadenaSql .= "AND   en.estado_registro='t' ";
				$cadenaSql .= "AND   en.estado_entrada = 1  ";
				$cadenaSql .= "ORDER BY en.id_entrada DESC ;";
				
				break;
			
			case "consultarElementoCD" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "placa,  ";
				$cadenaSql .= "elemento.serie, elemento.fecha_registro as fecharegistro, id_elemento as idelemento,
						       estado_entrada as estadoentrada, 
						      entrada.cierre_contable as cierrecontable,
						       entrada.consecutivo||' - ('||entrada.vigencia||')' entrada,
						       elemento.descripcion descripcion , elemento.tipo_bien  ";
				$cadenaSql .= "FROM elemento ";
				$cadenaSql .= "JOIN tipo_bienes ON tipo_bienes.id_tipo_bienes = elemento.tipo_bien ";
				$cadenaSql .= "JOIN elemento_individual ON elemento_individual.id_elemento_gen = elemento.id_elemento ";
				$cadenaSql .= "JOIN entrada ON entrada.id_entrada = elemento.id_entrada ";
				$cadenaSql .= "WHERE  elemento.estado=TRUE ";
				// $cadenaSql .= "AND elemento_individual.id_salida IS NULL ";
				$cadenaSql .= "AND entrada.cierre_contable='f' ";
				$cadenaSql .= "AND   entrada.estado_entrada='1' ";
				$cadenaSql .= "AND elemento_individual.estado_registro='TRUE' ";
				$cadenaSql .= "AND   entrada.estado_registro='t' ";
				$cadenaSql .= "AND   tipo_bienes.id_tipo_bienes <> '1' ";
				
				if ($variable [0] != '') {
					$cadenaSql .= " AND elemento.fecha_registro BETWEEN CAST ( '" . $variable [0] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [1] . "' AS DATE)  ";
				}
				if ($variable [2] != '') {
					$cadenaSql .= " AND elemento_individual.placa = '" . $variable [2] . "' ";
				}
				if ($variable [3] != '') {
					$cadenaSql .= " AND  elemento.serie= '" . $variable [3] . "' ";
				}
				
				if ($variable [4] != '') {
					$cadenaSql .= " AND  entrada.id_entrada= '" . $variable [4] . "' ";
				}
				
				break;
			
			case "consultarElementoC" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "'' as placa,  ";
				$cadenaSql .= "elemento.serie, elemento.fecha_registro as fecharegistro, id_elemento as idelemento,
						       estado_entrada as estadoentrada,
						      entrada.cierre_contable as cierrecontable,
						       entrada.consecutivo||' - ('||entrada.vigencia||')' entrada,
						       elemento.descripcion descripcion,  elemento.tipo_bien ";
				$cadenaSql .= "FROM elemento ";
				$cadenaSql .= "JOIN tipo_bienes ON tipo_bienes.id_tipo_bienes = elemento.tipo_bien ";
				$cadenaSql .= "LEFT JOIN elemento_individual ON elemento_individual.id_elemento_gen = elemento.id_elemento ";
				$cadenaSql .= "JOIN entrada ON entrada.id_entrada = elemento.id_entrada ";
				$cadenaSql .= "WHERE 1=1 ";
				$cadenaSql .= "AND elemento.estado='t' ";
				$cadenaSql .= "AND entrada.cierre_contable='f' ";
// 				$cadenaSql .= "AND elemento_individual.estado_registro='t' ";
				// $cadenaSql .= "AND elemento_individual.id_salida IS NULL ";
				$cadenaSql .= "AND   entrada.estado_registro='t' ";
				$cadenaSql .= "AND   entrada.estado_entrada='1' ";
				$cadenaSql .= "AND   tipo_bienes.id_tipo_bienes='1' ";
				
				if ($variable [0] != '') {
					$cadenaSql .= " AND elemento.fecha_registro BETWEEN CAST ( '" . $variable [0] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [1] . "' AS DATE)  ";
				}
				if ($variable [2] != '') {
					$cadenaSql .= " AND elemento_individual.placa = '" . $variable [2] . "' ";
				}
				if ($variable [3] != '') {
					$cadenaSql .= " AND  elemento.serie= '" . $variable [3] . "' ";
				}
				
				if ($variable [4] != '') {
					$cadenaSql .= " AND  entrada.id_entrada= '" . $variable [4] . "' ";
				}
				
				
				break;
			
			case "consultarElementoParticular" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= "nivel,tipo_bien, descripcion, cantidad, ";
				$cadenaSql .= "unidad, valor, iva, ajuste, bodega, subtotal_sin_iva, total_iva,";
				$cadenaSql .= "total_iva_con, tipo_poliza, fecha_inicio_pol, fecha_final_pol, ";
				$cadenaSql .= "marca, serie ";
				$cadenaSql .= " FROM arka_inventarios.elemento ";
				$cadenaSql .= " WHERE id_elemento='" . $variable . "'";
				
				break;
			
			case "consultar_tipo_bien" :
				
				$cadenaSql = "SELECT id_tipo_bienes, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.tipo_bienes;";
				break;
			
			case "consultar_tipo_poliza" :
				
				$cadenaSql = "SELECT id_tipo_poliza, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.tipo_poliza;";
				
				break;
			
			case "consultar_tipo_iva" :
				
				$cadenaSql = "SELECT id_iva, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.aplicacion_iva;";
				
				break;
			
			case "consultar_bodega" :
				
				$cadenaSql = "SELECT id_bodega, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.bodega;";
				
				break;
			
			case "consultar_placa" :
				
				$cadenaSql = "SELECT MAX( placa) ";
				$cadenaSql .= "FROM elemento ";
				$cadenaSql .= "WHERE tipo_bien='1';";
				
				break;
			
			// SELECT id_elemento, fecha_registro, tipo_bien, descripcion, cantidad,
			// unidad, valor, iva, ajuste, bodega, subtotal_sin_iva, total_iva,
			// total_iva_con, placa, tipo_poliza, fecha_inicio_pol, fecha_final_pol,
			// marca, serie, id_entrada, estado
			// FROM elemento;
			
			case "actualizar_elemento_tipo_1" :
				
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " elemento ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " tipo_bien='" . $variable [0] . "', ";
				$cadenaSql .= " descripcion='" . $variable [1] . "', ";
				$cadenaSql .= " cantidad='" . $variable [2] . "', ";
				$cadenaSql .= " unidad='" . $variable [3] . "', ";
				$cadenaSql .= " valor='" . $variable [4] . "', ";
				$cadenaSql .= " iva='" . $variable [5] . "', ";
				$cadenaSql .= " ajuste='" . $variable [6] . "', ";
				$cadenaSql .= " bodega='" . $variable [7] . "', ";
				$cadenaSql .= " subtotal_sin_iva='" . $variable [8] . "', ";
				$cadenaSql .= " total_iva='" . $variable [9] . "', ";
				$cadenaSql .= " total_iva_con='" . $variable [10] . "', ";
				$cadenaSql .= " marca='" . $variable [11] . "', ";
				$cadenaSql .= " serie='" . $variable [12] . "',  ";
				$cadenaSql .= " nivel='" . $variable [14] . "',  ";
				$cadenaSql .= " cantidad_por_asignar='0'  ";
				$cadenaSql .= "  WHERE id_elemento='" . $variable [13] . "';";
				
				break;
			
			case "actualizar_elemento_tipo_2" :
				
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " elemento ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " tipo_bien='" . $variable [0] . "', ";
				$cadenaSql .= " descripcion='" . $variable [1] . "', ";
				$cadenaSql .= " cantidad='" . $variable [2] . "', ";
				$cadenaSql .= " unidad='" . $variable [3] . "', ";
				$cadenaSql .= " valor='" . $variable [4] . "', ";
				$cadenaSql .= " iva='" . $variable [5] . "', ";
				$cadenaSql .= " ajuste='" . $variable [6] . "', ";
				$cadenaSql .= " bodega='" . $variable [7] . "', ";
				$cadenaSql .= " subtotal_sin_iva='" . $variable [8] . "', ";
				$cadenaSql .= " total_iva='" . $variable [9] . "', ";
				$cadenaSql .= " total_iva_con='" . $variable [10] . "', ";
				$cadenaSql .= " tipo_poliza='" . $variable [11] . "', ";
				$cadenaSql .= " fecha_inicio_pol='" . $variable [12] . "',  ";
				$cadenaSql .= " fecha_final_pol='" . $variable [13] . "',  ";
				$cadenaSql .= " marca='" . $variable [14] . "', ";
				$cadenaSql .= " serie='" . $variable [15] . "', ";
				$cadenaSql .= " nivel='" . $variable [17] . "', ";
				$cadenaSql .= " cantidad_por_asignar='0'  ";
				$cadenaSql .= "  WHERE id_elemento='" . $variable [16] . "';";
				
				break;
			
			case "inhabilitar_elementos_individuales" :
				$cadenaSql = " UPDATE elemento_individual   ";
				$cadenaSql .= " SET  estado_registro='FALSE' ";
				$cadenaSql .= " WHERE id_elemento_ind='" . $variable . "'  ";
				
				break;
			
			case "consultar_elementos_individuales_sin_placa" :
				$cadenaSql = " SELECT * ";
				$cadenaSql .= " FROM elemento_individual  ";
				$cadenaSql .= " WHERE id_elemento_gen='" . $variable . "'  ";
				$cadenaSql .= "AND  placa IS  NULL ;";
				break;
			
			case "consultar_elementos_individuales" :
				$cadenaSql = " SELECT *  ";
				$cadenaSql .= " FROM elemento_individual  ";
				$cadenaSql .= " WHERE id_elemento_gen='" . $variable . "'  ";
				$cadenaSql .= "AND  placa IS NOT NULL ;";
				
				break;
			
			case "estado_elemento_individual" :
				
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " elemento_individual ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado_registro='FALSE' ";
				$cadenaSql .= "  WHERE id_elemento='" . $variable . "';";
				break;
			
			case "estado_elemento" :
				
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " elemento";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado='FALSE' ";
				$cadenaSql .= "  WHERE id_elemento='" . $variable . "';";
				break;
			
			case "anular_elemento" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " elemento_anulado(";
				$cadenaSql .= "id_elemento,observacion) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "') ;";
				
				break;
			
			// _________________________________________________
			
			case "consultarSolicitante" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= "dependencia, rubro ";
				$cadenaSql .= " FROM solicitante_servicios ";
				$cadenaSql .= " WHERE id_solicitante='" . $variable . "'";
				
				break;
			
			case "consultarEncargado" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " id_tipo_encargado, nombre, identificacion, cargo,asignacion  ";
				$cadenaSql .= " FROM encargado ";
				$cadenaSql .= " WHERE id_encargado='" . $variable . "'";
				
				break;
			
			// _________________________________________________update___________________________________________
			
			case "actualizarSolicitante" :
				
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " solicitante_servicios";
				$cadenaSql .= " SET ";
				$cadenaSql .= " dependencia='" . $variable [0] . "',";
				$cadenaSql .= " rubro='" . $variable [1] . "' ";
				$cadenaSql .= "  WHERE id_solicitante='" . $variable [2] . "';";
				break;
			
			case "actualizarSupervisor" :
				$cadenaSql = " UPDATE supervisor_servicios ";
				$cadenaSql .= " SET nombre='" . $variable [0] . "', ";
				$cadenaSql .= " cargo='" . $variable [1] . "', ";
				$cadenaSql .= " dependencia='" . $variable [2] . "' ";
				$cadenaSql .= "  WHERE id_supervisor='" . $variable [3] . "';";
				
				break;
			
			case "actualizarContratista" :
				$cadenaSql = " UPDATE contratista_servicios ";
				$cadenaSql .= " SET nombre_razon_social='" . $variable [0] . "', ";
				$cadenaSql .= " identificacion='" . $variable [1] . "', ";
				$cadenaSql .= " direccion='" . $variable [2] . "', ";
				$cadenaSql .= " telefono='" . $variable [3] . "', ";
				$cadenaSql .= " cargo='" . $variable [4] . "' ";
				$cadenaSql .= "  WHERE id_contratista='" . $variable [5] . "';";
				
				break;
			
			case "actualizarEncargado" :
				$cadenaSql = " UPDATE encargado ";
				$cadenaSql .= " SET id_tipo_encargado='" . $variable [0] . "', ";
				$cadenaSql .= " nombre='" . $variable [1] . "', ";
				$cadenaSql .= " identificacion='" . $variable [2] . "', ";
				$cadenaSql .= " cargo='" . $variable [3] . "', ";
				$cadenaSql .= " asignacion='" . $variable [4] . "' ";
				$cadenaSql .= "  WHERE id_encargado='" . $variable [5] . "';";
				
				break;
			
			case "actualizarOrden" :
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " orden_servicio ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " objeto_contrato='" . $variable [0] . "', ";
				if ($variable [1] != '') {
					$cadenaSql .= " poliza1='" . $variable [1] . "', ";
				} else {
					$cadenaSql .= " poliza1='0', ";
				}
				if ($variable [2] != '') {
					$cadenaSql .= " poliza2='" . $variable [2] . "', ";
				} else {
					$cadenaSql .= " poliza2='0', ";
				}
				if ($variable [3] != '') {
					$cadenaSql .= " poliza3='" . $variable [3] . "', ";
				} else {
					$cadenaSql .= " poliza3='0', ";
				}
				if ($variable [4] != '') {
					$cadenaSql .= " poliza4='" . $variable [4] . "', ";
				} else {
					$cadenaSql .= " poliza4='0', ";
				}
				$cadenaSql .= " duracion_pago='" . $variable [5] . "', ";
				$cadenaSql .= " fecha_inicio_pago='" . $variable [6] . "', ";
				$cadenaSql .= " fecha_final_pago='" . $variable [7] . "', ";
				$cadenaSql .= " forma_pago='" . $variable [8] . "', ";
				$cadenaSql .= " total_preliminar='" . $variable [9] . "', ";
				$cadenaSql .= " iva='" . $variable [10] . "', ";
				$cadenaSql .= " total='" . $variable [11] . "', ";
				$cadenaSql .= " fecha_diponibilidad='" . $variable [12] . "', ";
				$cadenaSql .= " numero_disponibilidad='" . $variable [13] . "', ";
				$cadenaSql .= " valor_disponibilidad='" . $variable [14] . "', ";
				$cadenaSql .= " fecha_registrop='" . $variable [15] . "', ";
				$cadenaSql .= " numero_registrop='" . $variable [16] . "', ";
				$cadenaSql .= " valor_registrop='" . $variable [17] . "', ";
				$cadenaSql .= " letra_registrop='" . $variable [18] . "'  ";
				$cadenaSql .= "  WHERE id_orden_servicio='" . $variable [19] . "';";
				
				break;
			
			case "limpiarItems" :
				$cadenaSql = " DELETE FROM ";
				$cadenaSql .= " items_orden_compra ";
				$cadenaSql .= " WHERE id_orden='" . $variable . "';";
				break;
			
			case "insertarItems" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " items_orden_compra(";
				$cadenaSql .= " id_orden, item, unidad_medida, cantidad, descripcion, ";
				$cadenaSql .= " valor_unitario, valor_total)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "');";
				
				break;
			
			// listo
			case "consultarOrden" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "id_orden_servicio, fecha_registro,  ";
				$cadenaSql .= "identificacion, dependencia  ";
				$cadenaSql .= "FROM orden_servicio ";
				$cadenaSql .= "JOIN solicitante_servicios ON solicitante_servicios.id_solicitante = orden_servicio.id_solicitante ";
				$cadenaSql .= "JOIN contratista_servicios ON contratista_servicios.id_contratista = orden_servicio.id_contratista ";
				$cadenaSql .= "WHERE 1=1";
				if ($variable [0] != '') {
					$cadenaSql .= " AND fecha_registro BETWEEN CAST ( '" . $variable [0] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [1] . "' AS DATE)  ";
				}
				if ($variable [2] != '') {
					$cadenaSql .= " AND id_orden_servicio = '" . $variable [2] . "'";
				}
				if ($variable [3] != '') {
					$cadenaSql .= " AND  identificacion= '" . $variable [3] . "'";
				}
				if ($variable [4] != '') {
					$cadenaSql .= " AND  dependencia= '" . $variable [4] . "'";
				}
				
				break;
			
			case "consultar_nivel_inventario" :
				
				$cadenaSql = "SELECT ce.elemento_id, ce.elemento_codigo||' - '||ce.elemento_nombre ";
				$cadenaSql .= "FROM catalogo.catalogo_elemento  ce ";
				$cadenaSql .= "JOIN catalogo.catalogo_lista cl ON cl.lista_id = ce.elemento_catalogo  ";
				$cadenaSql .= "WHERE cl.lista_activo = 1  ";
				$cadenaSql .= "AND  ce.elemento_id > 0  ";
				$cadenaSql .= "AND  ce.elemento_padre > 0  ";
				$cadenaSql .= "ORDER BY ce.elemento_codigo ASC ;";
				
				break;
			
			case "ConsultaTipoBien" :
				$cadenaSql = "SELECT ge.elemento_tipobien , tb.descripcion  ";
				$cadenaSql .= "FROM  catalogo.catalogo_elemento ce ";
				$cadenaSql .= "JOIN  grupo.catalogo_elemento ge  ON (ge.elemento_id)::text =ce .elemento_grupoc  ";
				$cadenaSql .= "JOIN  arka_inventarios.tipo_bienes tb ON tb.id_tipo_bienes = ge.elemento_tipobien  ";
				$cadenaSql .= "WHERE ce.elemento_id = '" . $variable . "';";
				
				break;
			
			case 'consultarExistenciaImagen' :
				
				$cadenaSql = "SELECT num_registro  ";
				$cadenaSql .= "FROM  arka_movil.asignar_imagen ";
				$cadenaSql .= "WHERE  id_elemento ='" . $variable . "';";
				
				break;
			
			case "RegistrarElementoImagen" :
				
				$cadenaSql = " 	INSERT INTO arka_movil.asignar_imagen(";
				$cadenaSql .= " id_elemento, imagen ,prioridad) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable ['elemento'] . "',";
				$cadenaSql .= "'" . $variable ['imagen'] . "',";
				$cadenaSql .= "1) ";
				$cadenaSql .= "RETURNING num_registro; ";
				
				break;
			case "ActualizarElementoImagen" :
				
				$cadenaSql = " UPDATE arka_movil.asignar_imagen  ";
				$cadenaSql .= "SET  id_elemento='" . $variable ['elemento'] . "', imagen='" . $variable ['imagen'] . "' ";
				$cadenaSql .= "WHERE num_registro='" . $variable ['id_imagen'] . "';";
				
				break;
			
			case "ConsultasPlacas" :
				$cadenaSql = " SELECT DISTINCT placa AS value, placa as data ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "WHERE placa IS NOT NULL  ";
				$cadenaSql .= " AND placa LIKE '%" . $variable . "%' ";
				$cadenaSql .= "ORDER BY placa DESC ;";
				break;
			
			case "actualizar_cantidad_elemento" :
				
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " elemento ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " cantidad_por_asignar='".$variable['cantidad']."'  ";
				$cadenaSql .= "  WHERE id_elemento='" . $variable ['id_elemento'] . "';";
				
				break;
		}
		return $cadenaSql;
	}
}

?>
