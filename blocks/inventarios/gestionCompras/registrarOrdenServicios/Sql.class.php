<?php

namespace inventarios\gestionCompras\registrarOrdenServicios;

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
				$cadenaSql .= " WHERE ad.\"ESF_CODIGO_DEP\"='" . $variable . "' ";
				$cadenaSql .= " AND  ef.\"ESF_ESTADO\"='A'";
				
				break;
			
			case "informacion_proveedor" :
				$cadenaSql = " SELECT \"PRO_RAZON_SOCIAL\",\"PRO_NIT\",\"PRO_DIRECCION\",\"PRO_TELEFONO\"  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= " WHERE \"PRO_NIT\"='" . $variable . "' ";
				
				break;
			
			case "buscar_Proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  value,\"PRO_NIT\"  AS data  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= "WHERE cast(\"PRO_NIT\" as text) LIKE '%" . $variable . "%' ";
				$cadenaSql .= "OR \"PRO_RAZON_SOCIAL\" LIKE '%" . $variable . "%' LIMIT 10; ";
				
				break;
			
			case "proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\",\"PRO_NIT\"||' - '||\"PRO_RAZON_SOCIAL\" AS proveedor ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor ";
				
				break;
			
			case "cargoSuper" :
				
				$cadenaSql = "SELECT \"FUN_CARGO\" ";
				$cadenaSql .= "FROM arka_parametros.arka_funcionarios  ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				$cadenaSql .= "AND \"FUN_IDENTIFICACION\"='" . $variable . "' ";
				
				break;
			
			case "funcionarios" :
				
				$cadenaSql = "SELECT \"FUN_IDENTIFICACION\", \"FUN_IDENTIFICACION\" ||' - '|| \"FUN_NOMBRE\" ";
				$cadenaSql .= "FROM arka_parametros.arka_funcionarios ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				
				break;
			
			case "dependencias" :
				$cadenaSql = "SELECT DISTINCT  ESF_ID_ESPACIO, ESF_NOMBRE_ESPACIO ";
				$cadenaSql .= " FROM ESPACIOS_FISICOS ";
				$cadenaSql .= " WHERE ESF_ID_SEDE='" . $variable . "' ";
				$cadenaSql .= " AND  ESF_ESTADO='A'";
				
				break;
			
			case "sede" :
				
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
				$cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ;";
				break;
			
			case "buscar_contratista" :
				$cadenaSql = "SELECT CON_IDENTIFICADOR AS IDENTIFICADOR , CON_IDENTIFICACION ||'  -  '||CON_NOMBRE AS CONTRATISTA ";
				$cadenaSql .= "FROM CONTRATISTAS ";
				$cadenaSql .= "WHERE CON_VIGENCIA ='" . $variable . "' ";
				break;
			
			case "vigencia_contratista" :
				$cadenaSql = "SELECT CON_VIGENCIA AS VALOR , CON_VIGENCIA AS VIGENCIA  ";
				$cadenaSql .= "FROM CONTRATISTAS ";
				$cadenaSql .= "GROUP BY CON_VIGENCIA ";
				break;
			
			case "vigencia_disponibilidad" :
				$cadenaSql = "SELECT \"DIS_VIGENCIA\" AS valor, \"DIS_VIGENCIA\" AS vigencia  ";
				$cadenaSql .= "FROM arka_parametros.arka_disponibilidadpresupuestal ";
				$cadenaSql .= "GROUP BY \"DIS_VIGENCIA\" ORDER BY  \"DIS_VIGENCIA\"  DESC; ";
				break;
			
			case "buscar_disponibilidad" :
				$cadenaSql = "SELECT DISTINCT \"DIS_NUMERO_DISPONIBILIDAD\" AS identificador,\"DIS_NUMERO_DISPONIBILIDAD\" AS numero ";
				$cadenaSql .= "FROM arka_parametros.arka_disponibilidadpresupuestal  ";
				$cadenaSql .= "WHERE \"DIS_VIGENCIA\"='" . $variable[0] . "' ";
				$cadenaSql .= "AND \"DIS_UNIDAD_EJECUTORA\"='" . $variable[1]. "' ";
				$cadenaSql .= "ORDER BY \"DIS_NUMERO_DISPONIBILIDAD\" DESC ;";
				
				
				break;
			
			case "info_disponibilidad" :
				$cadenaSql = "SELECT DISTINCT \"DIS_FECHA_REGISTRO\" AS FECHA, \"DIS_VALOR\" ";
				$cadenaSql .= "FROM arka_parametros.arka_disponibilidadpresupuestal  ";
				$cadenaSql .= "WHERE \"DIS_VIGENCIA\"='" . $variable [1] . "' ";
				$cadenaSql .= "AND  \"DIS_IDENTIFICADOR\"='" . $variable [0] . "' ";
				$cadenaSql .= "AND  \"DIS_UNIDAD_EJECUTORA\"='" . $variable [2] . "' ";

				
				break;
			
			case "vigencia_registro" :
				$cadenaSql = "SELECT REP_VIGENCIA AS VALOR,REP_VIGENCIA AS VIGENCIA ";
				$cadenaSql .= "FROM REGISTRO_PRESUPUESTAL ";
				$cadenaSql .= "GROUP BY REP_VIGENCIA ";
				
				break;
			
			case "buscar_registro" :
				$cadenaSql = "SELECT  \"REP_IDENTIFICADOR\" AS identificador,\"REP_IDENTIFICADOR\" AS numero ";
				$cadenaSql .= "FROM arka_parametros.arka_registropresupuestal ";
				$cadenaSql .= "WHERE \"REP_VIGENCIA\"='" . $variable [0] . "'";
				$cadenaSql .= "AND  \"REP_NUMERO_DISPONIBILIDAD\"='" . $variable [1] . "'";
				$cadenaSql .= "AND  \"REP_UNIDAD_EJECUTORA\"='" . $variable [2] . "'";
				
				break;
			
			case "info_registro" :
				$cadenaSql = "SELECT \"REP_FECHA_REGISTRO\" AS fecha, \"REP_VALOR\" valor ";
				$cadenaSql .= "FROM arka_parametros.arka_registropresupuestal  ";
				$cadenaSql .= "WHERE \"REP_VIGENCIA\"='" . $variable [1] . "'  ";
				$cadenaSql .= "AND  \"REP_IDENTIFICADOR\"='" . $variable [0] . "' ";
				
				break;
			case "informacion_cargo_jefe" :
				$cadenaSql = " SELECT JEF_NOMBRE,JEF_IDENTIFICADOR ";
				$cadenaSql .= " FROM JEFES_DE_SECCION ";
				$cadenaSql .= " WHERE  JEF_IDENTIFICADOR='" . $variable . "' ";
				
				break;
			case "informacion_ordenador" :
				$cadenaSql = " SELECT \"ORG_NOMBRE\",\"ORG_IDENTIFICACION\" , \"ORG_TIPO_ORDENADOR\" ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE  \"ORG_IDENTIFICACION\"='" . $variable . "' ";
				$cadenaSql .= " AND \"ORG_ESTADO\"='A' ";
				
				break;
			
			case "ordenador_gasto" :
				$cadenaSql = " 	SELECT ORG_IDENTIFICACION, ORG_ORDENADOR_GASTO ";
				$cadenaSql .= " FROM ORDENADORES_GASTO ";
				$cadenaSql .= " WHERE ORG_ESTADO='A' ";
				break;
			
			case "constratistas" :
				$cadenaSql = " SELECT CON_IDENTIFICADOR,CON_IDENTIFICACION ||' - '|| CON_NOMBRE ";
				$cadenaSql .= "FROM CONTRATISTAS ";
				
				break;
			
			case "cargo_jefe" :
				$cadenaSql = " SELECT JEF_IDENTIFICADOR,JEF_DEPENDENCIA_PERTENECIENTE ";
				$cadenaSql .= " FROM JEFES_DE_SECCION ";
				
				break;
			
			case "rubros" :
				$cadenaSql = " SELECT \"RUB_IDENTIFICADOR\", \"RUB_RUBRO\" ||' - '|| \"RUB_NOMBRE_RUBRO\" ";
				$cadenaSql .= "FROM arka_parametros.arka_rubros ";
				$cadenaSql .= "WHERE \"RUB_VIGENCIA\"='" . date ( 'Y' ) . "';";
				
				break;
			
			case "dependencia" :
				$cadenaSql = " SELECT DEP_IDENTIFICADOR, DEP_IDENTIFICADOR ||' - ' ||DEP_DEPENDENCIA  ";
				$cadenaSql .= "FROM DEPENDENCIAS ";
				break;
			
			case "polizas" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " id_polizas,";
				$cadenaSql .= " poliza_1, ";
				$cadenaSql .= " poliza_2, ";
				$cadenaSql .= " poliza_3,";
				$cadenaSql .= " poliza_4 ";
				$cadenaSql .= " FROM";
				$cadenaSql .= " polizas ";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " estado=TRUE ";
				$cadenaSql .= " AND ";
				$cadenaSql .= " modulo_tipo=2 ";
				break;
			
			case "textos" :
				$cadenaSql = " SELECT ";
				$cadenaSql .= " tipo_parrafo,parrafo";
				$cadenaSql .= " FROM";
				$cadenaSql .= " parrafos ";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " estado=TRUE ";
				$cadenaSql .= " AND ";
				$cadenaSql .= " modulo_parrafo=2  ";
				$cadenaSql .= " ORDER BY id_parrafos DESC ";
				break;
			
			case "insertarSolicitante" :
				$cadenaSql = " INSERT INTO solicitante_servicios(";
				$cadenaSql .= " dependencia, ";
				$cadenaSql .= " rubro )";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "') ";
				$cadenaSql .= "RETURNING  id_solicitante; ";
				break;
			
			case "insertarSupervisor" :
				$cadenaSql = " INSERT INTO supervisor_servicios(";
				$cadenaSql .= " nombre,cargo, dependencia,sede) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "') ";
				$cadenaSql .= "RETURNING  id_supervisor; ";
				break;
			
			case "insertarContratista" :
				$cadenaSql = " INSERT INTO contratista_servicios(";
				$cadenaSql .= " nombre_razon_social, identificacion,direccion, telefono,cargo) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "') ";
				$cadenaSql .= "RETURNING  id_contratista; ";
				break;
			
			case "insertarEncargado" :
				$cadenaSql = " INSERT INTO arka_inventarios.encargado(";
				$cadenaSql .= " id_tipo_encargado,";
				$cadenaSql .= " nombre, ";
				$cadenaSql .= " identificacion,";
				$cadenaSql .= " cargo, ";
				$cadenaSql .= " asignacion)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "') ";
				$cadenaSql .= "RETURNING  id_encargado; ";
				break;
			
			// INSERT
			// orden(
			// id_orden, tipo_orden, vigencia, consecutivo_servicio, consecutivo_compras,
			// fecha_registro, info_presupuestal, dependencia_solicitante, sede,
			// rubro, objeto_contrato, poliza1, poliza2, poliza3, poliza4, duracion_pago,
			// fecha_inicio_pago, fecha_final_pago, forma_pago, id_ordenador_encargado,
			// estado)
			// ;
			
			case "insertarOrden" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " orden(";
				$cadenaSql .= "tipo_orden, vigencia, consecutivo_servicio, consecutivo_compras, 
								            fecha_registro, info_presupuestal, dependencia_solicitante, sede, 
								            rubro, objeto_contrato, poliza1, poliza2, poliza3, poliza4, duracion_pago, 
								            fecha_inicio_pago, fecha_final_pago, forma_pago,id_contratista,id_supervisor, id_ordenador_encargado, tipo_ordenador)";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "" . $variable [2] . ",";
				$cadenaSql .= "" . $variable [3] . ",";
				$cadenaSql .= "'" . $variable [4] . "',";
				$cadenaSql .= "'" . $variable [5] . "',";
				$cadenaSql .= "'" . $variable [6] . "',";
				$cadenaSql .= "'" . $variable [7] . "',";
				$cadenaSql .= "'" . $variable [8] . "',";
				$cadenaSql .= "'" . $variable [9] . "',";
				
				if ($variable [10] != '') {
					$cadenaSql .= "'" . $variable [10] . "',";
				} else {
					$cadenaSql .= "'0',";
				}
				
				if ($variable [11] != '') {
					$cadenaSql .= "'" . $variable [11] . "',";
				} else {
					$cadenaSql .= "'0',";
				}
				if ($variable [12] != '') {
					$cadenaSql .= "'" . $variable [12] . "',";
				} else {
					$cadenaSql .= "'0',";
				}
				if ($variable [13] != '') {
					$cadenaSql .= "'" . $variable [13] . "',";
				} else {
					$cadenaSql .= "'0',";
				}
				
				$cadenaSql .= "'" . $variable [14] . "',";
				$cadenaSql .= "'" . $variable [15] . "',";
				$cadenaSql .= "'" . $variable [16] . "',";
				$cadenaSql .= "'" . $variable [17] . "',";
				$cadenaSql .= "'" . $variable [18] . "',";
				$cadenaSql .= "'" . $variable [19] . "',";
				$cadenaSql .= "'" . $variable [20] . "',";
				$cadenaSql .= "'" . $variable [21] . "') ";
				$cadenaSql .= "RETURNING  consecutivo_compras,consecutivo_servicio,id_orden  ; ";
				
				break;
			
			case "insertarInformacionPresupuestal" :
				$cadenaSql = " INSERT INTO informacion_presupuestal_orden( ";
				$cadenaSql .= " vigencia_dispo, numero_dispo, valor_disp, fecha_dip,
								letras_dispo, vigencia_regis, numero_regis, valor_regis, fecha_regis,
								letras_regis, fecha_registro,unidad_ejecutora)";
				$cadenaSql .= " VALUES (";
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
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [11] . "') ";
				$cadenaSql .= "RETURNING  id_informacion; ";
				
				break;
			
			case "consultarDependencia" :
				$cadenaSql = " SELECT   ESF_ID_ESPACIO, ESF_NOMBRE_ESPACIO ";
				$cadenaSql .= "FROM ESPACIOS_FISICOS  ";
				$cadenaSql .= " WHERE ESF_ID_ESPACIO='" . $variable . "' ";
				$cadenaSql .= " AND  ESF_ESTADO='A'";
				
				break;
			
			case "consultarRubro" :
				$cadenaSql = " SELECT RUB_NOMBRE_RUBRO ";
				$cadenaSql .= " FROM RUBROS ";
				$cadenaSql .= " WHERE  RUB_IDENTIFICADOR='" . $variable . "'";
				
				break;
			
			case "consultarDependenciaSupervisor" :
				$cadenaSql = " SELECT   ESF_ID_ESPACIO, ESF_NOMBRE_ESPACIO ";
				$cadenaSql .= "FROM ESPACIOS_FISICOS  ";
				$cadenaSql .= " WHERE ESF_ID_ESPACIO='" . $variable . "' ";
				$cadenaSql .= " AND  ESF_ESTADO='A'";
				break;
			
			case "consultarSupervisor" :
				$cadenaSql = " SELECT nombre, cargo, dependencia ";
				$cadenaSql .= " FROM supervisor_servicios ";
				$cadenaSql .= " WHERE id_supervisor='" . $variable . "'";
				break;
			
			case "consultarOrdenador_gasto" :
				$cadenaSql = " 	SELECT ORG_ORDENADOR_GASTO,ORG_NOMBRE ";
				$cadenaSql .= " FROM ORDENADORES_GASTO ";
				$cadenaSql .= " WHERE ORG_IDENTIFICADOR ='" . $variable . "' ";
				$cadenaSql .= " AND ORG_ESTADO='A' ";
				break;
			
			case "consultarContratista" :
				$cadenaSql = "SELECT CON_IDENTIFICACION , CON_NOMBRE AS CONTRATISTA ";
				$cadenaSql .= "FROM CONTRATISTAS ";
				$cadenaSql .= "WHERE CON_VIGENCIA ='" . $variable [1] . "' ";
				$cadenaSql .= "AND  CON_IDENTIFICADOR ='" . $variable [0] . "' ";
				break;
			
			case "consultarCosntraistaServicios" :
				$cadenaSql = " SELECT nombre_razon_social, identificacion, direccion,telefono, cargo ";
				$cadenaSql .= " FROM contratista_servicios ";
				$cadenaSql .= " WHERE id_contratista='" . $variable . "'";
				break;
			
			case "informacionPresupuestal" :
				$cadenaSql = "SELECT  vigencia_dispo, numero_dispo, valor_disp, fecha_dip,
									letras_dispo, vigencia_regis, numero_regis, valor_regis, fecha_regis,
									letras_regis  ";
				$cadenaSql .= "FROM informacion_presupuestal_orden ";
				$cadenaSql .= "WHERE id_informacion ='" . $variable . "' ";
				
				break;
			
			case "consultarOrdenServicios" :
				$cadenaSql = "SELECT  fecha_registro, info_presupuestal, dependencia_solicitante,
				rubro, objeto_contrato, poliza1, poliza2, poliza3, poliza4, duracion_pago,
				fecha_inicio_pago, fecha_final_pago, forma_pago, total_preliminar,
				iva, total, id_contratista,id_supervisor,
				id_ordenador_encargado, estado ";
				$cadenaSql .= "FROM orden_servicio  ";
				$cadenaSql .= "WHERE  id_orden_servicio='" . $variable . "';";
				
				break;
			
			case "tipoComprador" :
				
				$cadenaSql = " 	SELECT \"ORG_IDENTIFICACION\",\"ORG_ORDENADOR_GASTO\" ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE \"ORG_ESTADO\"='A' ";
				
				break;
			
			case "tipo_orden" :
				
				$cadenaSql = " 	SELECT 	id_tipo , descripcion ";
				$cadenaSql .= " FROM tipo_contrato ";
				$cadenaSql .= " WHERE id_tipo =  1 ";
				$cadenaSql .= "OR   id_tipo =  9  ";
				
				break;
			
			case "consecutivo_compra" :
				
				$cadenaSql = " 	SELECT max(consecutivo_compras)  ";
				$cadenaSql .= " FROM orden ";
				$cadenaSql .= " WHERE vigencia ='" . $variable . "';";
				
				break;
			
			case "consecutivo_servicios" :
				
				$cadenaSql = " 	SELECT max(consecutivo_servicio)  ";
				$cadenaSql .= " FROM orden ";
				$cadenaSql .= " WHERE vigencia ='" . $variable . "';";
				
				break;
			
			case "Unidad_Ejecutoria" :
				
				$cadenaSql = " SELECT DISTINCT \"DIS_UNIDAD_EJECUTORA\" valor ,\"DIS_UNIDAD_EJECUTORA\" descripcion  ";
				$cadenaSql .= "FROM arka_parametros.arka_disponibilidadpresupuestal; ";
				
				break;
		}
		return $cadenaSql;
	}
}
?>
