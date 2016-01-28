<?php

namespace inventarios\gestionEntradas\registrarEntradas;

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
			
			// _________________________________________________
			
			case "consultar_id_acta" :
				$cadenaSql = " SELECT DISTINCT ar.id_actarecibido, ar.id_actarecibido as acta_serial";
				$cadenaSql .= " FROM registro_actarecibido ar";
				$cadenaSql .= " JOIN    elemento_acta_recibido  ela ON ela.id_acta=ar.id_actarecibido ";
				$cadenaSql .= " LEFT JOIN  entrada  en ON en.acta_recibido=ar.id_actarecibido ";
				$cadenaSql .= " WHERE ela.estado ='TRUE'   ";
				$cadenaSql .= " AND ar.estado_registro = 1   ";
				$cadenaSql .= " AND  en.acta_recibido IS NULL   ";
				$cadenaSql .= " ORDER BY  id_actarecibido DESC ;  ";
				
				
				break;
			
			case "proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\",\"PRO_NIT\"||' - '||\"PRO_RAZON_SOCIAL\" AS proveedor ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor ";
				
				break;
			
			case "insertarInformación" :
				$cadenaSql = " INSERT INTO info_clase_entrada(  ";
				$cadenaSql .= " observacion, id_entrada, id_salida, id_hurto,";
				$cadenaSql .= " num_placa, val_sobrante, ruta_archivo, nombre_archivo)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "',";
				$cadenaSql .= "'" . $variable [7] . "') ";
				$cadenaSql .= "RETURNING  id_info_clase; ";
				
				break;
			
			case "seleccion_proveedor" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " id_proveedor,";
				$cadenaSql .= " nit_proveedor ||' - '|| razon_social as proveedor ";
				$cadenaSql .= " FROM";
				$cadenaSql .= " proveedor  ";
				$cadenaSql .= " UNION SELECT ";
				$cadenaSql .= " id_proveedor_n,";
				$cadenaSql .= " nit_proveedor ||' - '|| razon_social as proveedor ";
				$cadenaSql .= " FROM";
				$cadenaSql .= " proveedor_nuevo ;";
				
				break;
			
			case "consultarActa" :
				
				$cadenaSql = "SELECT  DISTINCT  ";
				$cadenaSql .= "ra.id_actarecibido, ra.fecha_registro,  ";
				$cadenaSql .= "ra. proveedor nit_proveedor , pr.\"PRO_RAZON_SOCIAL\" nombre_proveedor  ";
				
				$cadenaSql .= "FROM registro_actarecibido ra   ";
				$cadenaSql .= " JOIN elemento_acta_recibido  ela ON ela.id_acta=ra. id_actarecibido ";
				$cadenaSql .= " LEFT JOIN  entrada  en ON en.acta_recibido=ra. id_actarecibido ";
				$cadenaSql .= " LEFT  JOIN arka_parametros.arka_proveedor  pr ON pr.\"PRO_NIT\"=ra.proveedor::text  ";
				$cadenaSql .= "WHERE ra.estado_registro= 1 ";
				$cadenaSql .= " AND  en.acta_recibido IS NULL   "; 
				 
				if ($variable [0] != '') {
					$cadenaSql .= " AND ra.id_actarecibido = '" . $variable [0] . "'";
				}
				
				if ($variable [1] != '') {
					$cadenaSql .= " AND  ra.proveedor= '" . $variable [1] . "'";
				}
				
				if ($variable [2] != '') {
					$cadenaSql .= " AND ra.fecha_registro BETWEEN CAST ( '" . $variable [2] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [3] . "' AS DATE)  ";
				}
				
				$cadenaSql .= " ; ";
				
				break;
			
			case "consultaActaParticular" :
				
				$cadenaSql = "SELECT  DISTINCT  ";
				$cadenaSql .= "ra.*, pr.\"PRO_RAZON_SOCIAL\" nombre_proveedor ,ra.proveedor ||'  - ('|| pr.\"PRO_RAZON_SOCIAL\"  ||')'  nit_nombre
										  , cot.numero_contrato , cot.fecha_contrato, ord.\"ORG_NOMBRE\"  nombre_ordenador ,ord.\"ORG_TIPO_ORDENADOR\"  tipo_ordenador  ";
				
				$cadenaSql .= "FROM registro_actarecibido ra   ";
				$cadenaSql .= " JOIN  elemento_acta_recibido  ela ON ela.id_acta=ra. id_actarecibido ";
				$cadenaSql .= " LEFT  JOIN arka_parametros.arka_proveedor  pr ON pr.\"PRO_NIT\"=ra.proveedor::text  ";
				$cadenaSql .= "LEFT  JOIN    contratos cot ON cot.id_contrato=ra. id_contrato  ";
				$cadenaSql .= "LEFT  JOIN    arka_parametros.arka_ordenadores ord ON ord.\"ORG_IDENTIFICACION\"=ra. ordenador_gasto  ";
				$cadenaSql .= "WHERE ra.id_actarecibido = '" . $variable . "'";
				$cadenaSql .= " ; ";
				
				break;
			
			case "clase_entrada" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_clase, descripcion  ";
				$cadenaSql .= "FROM clase_entrada ";
				$cadenaSql .= "WHERE  id_clase > 1 ";
				$cadenaSql .= "ORDER BY  id_clase  ASC ;";
				
				break;
			
			case "tipo_contrato" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_tipo, descripcion  ";
				$cadenaSql .= "FROM tipo_contrato ";
				$cadenaSql .= "WHERE  id_tipo > 0;";
				
				break;
			
			case "tipoComprador" :
				
				$cadenaSql = " 	SELECT \"ORG_IDENTIFICACION\",\"ORG_ORDENADOR_GASTO\" ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE \"ORG_ESTADO\"='A' ";
				
				break;
			
			case "dependencias" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";
				$cadenaSql .= " AND  sa.\"ESF_ID_SEDE\"='".$variable."'  ;";
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
				$cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
				
				break;
			case "informacion_ordenador" :
				$cadenaSql = " SELECT \"ORG_NOMBRE\",\"ORG_IDENTIFICACION\",\"ORG_TIPO_ORDENADOR\"  ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE  \"ORG_IDENTIFICACION\"='" . $variable . "' ";
				$cadenaSql .= " AND \"ORG_ESTADO\"='A' ";
				
				break;
			
			case "tipo_contrato_avance" :
				
				$cadenaSql = "SELECT id_tipo, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.tipo_contrato ";
				$cadenaSql .= "WHERE id_tipo<>1;";
				
				break;
			
			case "proveedor" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_proveedor, razon_social ";
				$cadenaSql .= "FROM proveedor;";
				
				break;
			
			case "actasRecibido" :
				$cadenaSql = " SELECT id_actarecibido, id_actarecibido ";
				$cadenaSql .= "FROM registro_actarecibido ";
				
				break;
			
			case "insertarReposicion" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " reposicion_entrada(";
				$cadenaSql .= " id_entrada, id_hurto, id_salida )";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "') ";
				$cadenaSql .= "RETURNING  id_reposicion; ";
				
				break;
			
			case "insertarDonacion" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " donacion_entrada(";
				$cadenaSql .= " ruta_acto, nombre_acto)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "') ";
				$cadenaSql .= "RETURNING  id_donacion; ";
				
				break;
			
			case "insertarSobrante" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " sobrante_entrada(";
				$cadenaSql .= " observaciones, ruta_acta, nombre_acta)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "') ";
				$cadenaSql .= "RETURNING  id_sobrante; ";
				
				break;
			
			case "insertarProduccion" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " produccion_entrada(";
				$cadenaSql .= " observaciones, ruta_acta, nombre_acta)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "') ";
				$cadenaSql .= "RETURNING  id_produccion; ";
				
				break;
			
			case "insertarRecuperacion" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " recuperacion_entrada(";
				$cadenaSql .= " observaciones, ruta_acta, nombre_acta)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "') ";
				$cadenaSql .= "RETURNING  id_recuperacion; ";
				
				break;
			
			// INSERT INTO entrada(
			// id_entrada, fecha_registro, consecutivo, vigencia, clase_entrada,
			// info_clase, tipo_contrato, numero_contrato, fecha_contrato, proveedor,
			// numero_factura, fecha_factura, observaciones, acta_recibido,
			// ordenador, sede, dependencia, supervisor, estado_entrada, estado_registro)
			// VALUES (?, ?, ?, ?, ?,
			// ?, ?, ?, ?, ?,
			// ?, ?, ?, ?,
			// ?, ?, ?, ?, ?, ?);
			
			case "insertarEntrada" :
				
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " entrada(";
				$cadenaSql .= " fecha_registro, vigencia, clase_entrada, info_clase, ";
				$cadenaSql .= " tipo_contrato, numero_contrato, fecha_contrato, proveedor, numero_factura, ";
				$cadenaSql .= " fecha_factura, observaciones, acta_recibido,ordenador,sede,dependencia,supervisor,tipo_ordenador,identificacion_ordenador,id_entrada )";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= (is_null($variable[4])==true)?"NULL,":"'" . $variable [4] . "',";
				$cadenaSql .= (is_null($variable[5])==true)?"NULL,":"'" . $variable [5] . "',";
				$cadenaSql .= (is_null($variable[6])==true)?"NULL,":"'" . $variable [6] . "',";
				$cadenaSql .= (is_null($variable[7])==true)?"NULL,":"'" . $variable [7] . "',";
				$cadenaSql .= (is_null($variable[8])==true)?"NULL,":"'" . $variable [8] . "',";
				$cadenaSql .= (is_null($variable[9])==true)?"NULL,":"'" . $variable [9] . "',";
				$cadenaSql .= "" . $variable [10] . ",";
				$cadenaSql .= "'" . $variable [11] . "',";
				$cadenaSql .= (is_null($variable[12])==true)?"NULL,":"'" . $variable [12] . "',";
				$cadenaSql .= "'" . $variable [13] . "',";
				$cadenaSql .= "'" . $variable [14] . "',";
				$cadenaSql .= "'" . $variable [15] . "',";
				$cadenaSql .= (is_null($variable[16])==true)?"NULL,":"'" . $variable [16] . "',";
				$cadenaSql .= (is_null($variable[17])==true)?"NULL,":"'" . $variable [17] . "',";
				$cadenaSql .= "'" . $variable [18] . "') ";
				$cadenaSql .= "RETURNING  consecutivo, acta_recibido; ";
				
				break;
			
			// _______________________________________________
			case "consultarContratista" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " nombre_razon_social, identificacion,direccion,telefono, cargo ";
				$cadenaSql .= " FROM contratista_servicios ";
				$cadenaSql .= " WHERE id_contratista='" . $variable . "'";
				
				break;
			
			case "consultarSupervisor" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= "  nombre, cargo, dependencia";
				$cadenaSql .= " FROM supervisor_servicios ";
				$cadenaSql .= " WHERE id_supervisor='" . $variable . "'";
				
				break;
			
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
			
			case "dependenciasConsultadas" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE sa.\"ESF_ID_SEDE\"='" . $variable . "' ";
				$cadenaSql .= " AND  ad.\"ESF_ESTADO\"='A'";
				
				break;
			
			case 'consultarActas' :
				$cadenaSql = "SELECT registro_actarecibido.* , contratos.numero_contrato, contratos.fecha_contrato, \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  nom_razon ";
				$cadenaSql .= "FROM registro_actarecibido  ";
				$cadenaSql .= "LEFT JOIN  contratos ON contratos.id_contrato=registro_actarecibido.id_contrato ";
				$cadenaSql .= "JOIN  arka_parametros.arka_proveedor ap  ON ap.\"PRO_NIT\"=CAST(registro_actarecibido.proveedor AS CHAR(50) )";
				
				$cadenaSql .= "WHERE  id_actarecibido='" . $variable . "';";
				
				break;
			case "buscar_Proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  value,\"PRO_NIT\"  AS data  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= "WHERE cast(\"PRO_NIT\" as text) LIKE '%" . $variable . "%' ";
				$cadenaSql .= "OR \"PRO_RAZON_SOCIAL\" LIKE '%" . $variable . "%' LIMIT 10; ";
				
				break;
			
			case 'consultaConsecutivo' :
				$cadenaSql = "SELECT consecutivo ";
				$cadenaSql .= "FROM entrada  ";
				$cadenaSql .= "WHERE  vigencia='" . $variable . "';";
				
				break;
			
			case 'reiniciarConsecutivo' :
				$cadenaSql = "SELECT SETVAL((SELECT pg_get_serial_sequence('entrada', 'consecutivo')), 1, false);";
				break;
			
			case "funcionarios" :
				
				$cadenaSql = "SELECT \"FUN_IDENTIFICACION\", \"FUN_IDENTIFICACION\" ||' - '|| \"FUN_NOMBRE\" ";
				$cadenaSql .= "FROM arka_parametros.arka_funcionarios ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				
				break;
			
			case 'consultarEntradas' :
				$cadenaSql = "SELECT id_entrada, consecutivo||' - ('||vigencia||')' entradas ";
				$cadenaSql .= "FROM entrada  ";
				$cadenaSql .= "WHERE consecutivo > 0  ";
				break;
			
			case 'consultarSalidas' :
				$cadenaSql = "SELECT id_salida, consecutivo||' - ('||vigencia||')' salidas ";
				$cadenaSql .= "FROM salida  ";
				$cadenaSql .= "WHERE consecutivo > 0  ";
				break;
			
			case 'consultarHurtos' :
				$cadenaSql = "SELECT id_estado_elemento, id_hurto||' - ('||fecha_hurto||')' hurtos ";
				$cadenaSql .= "FROM estado_elemento  ";
				$cadenaSql .= "WHERE id_hurto > 0  ";
				$cadenaSql .= "ORDER BY  id_hurto ASC ";
				
				break;
			
			case 'consultarPlacas' :
				$cadenaSql = "SELECT id_elemento_ind, placa  ";
				$cadenaSql .= "FROM elemento_individual  ";
				$cadenaSql .= "WHERE placa <> '' ";
				break;
			
			case 'idMaximoEntrada' :
				$cadenaSql = "SELECT max(id_entrada)  ";
				$cadenaSql .= "FROM entrada  ";
				break;
		}
		return $cadenaSql;
	}
}
?>
