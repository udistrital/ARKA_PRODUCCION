<?php

namespace inventarios\gestionEntradas\modificarEntradas;

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
				$cadenaSql .= "FROM entrada  ";
				$cadenaSql .= "WHERE cierre_contable ='f' ";
				$cadenaSql .= "AND   estado_entrada = 1  ";
				$cadenaSql .= "AND estado_registro='t' ";
				$cadenaSql .= "ORDER BY id_entrada DESC ";
				
				break;
			
			case "consultarInfoClase" :
				$cadenaSql = " SELECT  observacion, id_entrada, id_salida, id_hurto,";
				$cadenaSql .= " num_placa, val_sobrante, ruta_archivo, nombre_archivo  ";
				$cadenaSql .= " FROM info_clase_entrada ";
				$cadenaSql .= " WHERE id_info_clase='" . $variable . "';";
				
				break;
			
			case "proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\",\"PRO_NIT\"||' - '||\"PRO_RAZON_SOCIAL\" AS proveedor ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor ";
				
				break;
			case "tipo_contrato_avance" :
				
				$cadenaSql = "SELECT id_tipo, descripcion ";
				$cadenaSql .= "FROM arka_inventarios.tipo_contrato ";
				$cadenaSql .= "WHERE id_tipo<>1;";
				
				break;
			
			case "clase_entrada" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_clase, descripcion  ";
				$cadenaSql .= "FROM clase_entrada ";
				$cadenaSql .= "WHERE id_clase > 1 ";
				$cadenaSql .= "ORDER BY  descripcion ASC  ;";
				
				break;
			
			case "clase_entrada_consulta" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_clase, descripcion  ";
				$cadenaSql .= "FROM clase_entrada ";
// 				$cadenaSql .= "WHERE id_clase > 1 ";
				$cadenaSql .= "ORDER BY  descripcion ASC  ;";
				
				break;
			
			case "tipo_contrato" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_tipo, descripcion  ";
				$cadenaSql .= "FROM tipo_contrato ";
				$cadenaSql .= "WHERE id_tipo > 0;";
				
				break;
			
			case "consultarEntrada" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "entrada.id_entrada, entrada.fecha_registro,  ";
				$cadenaSql .= " descripcion,pr.\"PRO_NIT\" as nit ,consecutivo||' - ('||entrada.vigencia||')' consecutivo , cierre_contable, pr.\"PRO_RAZON_SOCIAL\" as razon_social ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "JOIN clase_entrada ON clase_entrada.id_clase = entrada.clase_entrada ";
				$cadenaSql .= "LEFT JOIN arka_parametros.arka_proveedor pr ON pr.\"PRO_NIT\" = CAST(entrada.proveedor AS CHAR(50)) ";
				$cadenaSql .= "WHERE 1=1 ";
				$cadenaSql .= "AND entrada.cierre_contable='f' ";
				$cadenaSql .= "AND entrada.estado_entrada = 1 ";
				$cadenaSql .= "AND entrada.estado_registro='t' ";
				
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
			
			// SELECT id_entrada, fecha_registro, consecutivo, vigencia, clase_entrada,
			// info_clase, tipo_contrato, numero_contrato, fecha_contrato, proveedor,
			// numero_factura, fecha_factura, observaciones, acta_recibido,
			// ordenador, sede, dependencia, supervisor, estado_entrada, estado_registro
			// FROM entrada;
			
			case "consultarEntradaParticular" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= " vigencia, clase_entrada, info_clase , ";
				$cadenaSql .= "	tipo_contrato, numero_contrato, fecha_contrato, proveedor,";
				$cadenaSql .= "numero_factura, fecha_factura, observaciones, acta_recibido , ordenador, sede, dependencia, supervisor ,tipo_ordenador, identificacion_ordenador,
						 \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  nom_razon ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "LEFT JOIN arka_parametros.arka_proveedor ap ON ap.\"PRO_NIT\"=CAST(entrada.proveedor AS CHAR(50))";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "';";
				
				break;
			
			case "actasRecicbido" :
				$cadenaSql = " SELECT id_actarecibido, id_actarecibido ";
				$cadenaSql .= "FROM registro_actarecibido ";
				
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
				$cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
				
				break;
			case "informacion_ordenador" :
				$cadenaSql = " SELECT \"ORG_NOMBRE\",\"ORG_IDENTIFICACION\",\"ORG_TIPO_ORDENADOR\",\"ORG_IDENTIFICACION\" as identificacion   ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE  \"ORG_IDENTIFICACION\"='" . $variable [0] . "' ";
				$cadenaSql .= " AND \"ORG_TIPO_ORDENADOR\"='" . $variable [1] . "' ";
				
				break;
			
			case "tipoComprador" :
				
				$cadenaSql = " 	SELECT \"ORG_IDENTIFICACION\", \"ORG_ORDENADOR_GASTO\" ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE \"ORG_ESTADO\"='A' ";
				
				break;
			
			case "informacion_ordenadorConsultados" :
				$cadenaSql = " SELECT \"ORG_NOMBRE\",\"ORG_IDENTIFICACION\",\"ORG_TIPO_ORDENADOR\",\"ORG_IDENTIFICACION\" as identificacion   ";
				$cadenaSql .= " FROM arka_parametros.arka_ordenadores ";
				$cadenaSql .= " WHERE  \"ORG_IDENTIFICACION\"='" . $variable . "' ";
				$cadenaSql .= " AND \"ORG_ESTADO\"='A' ";
				break;
			
			case "dependencias" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";
				break;
			case "consultarReposicion" :
				
				$cadenaSql = "SELECT  ";
				$cadenaSql .= " id_entrada, id_hurto, id_salida ";
				$cadenaSql .= "FROM reposicion_entrada ";
				$cadenaSql .= "WHERE id_reposicion='" . $variable . "';";
				
				break;
			
			case "consultarDonacion" :
				
				$cadenaSql = "SELECT  ";
				$cadenaSql .= " ruta_acto, nombre_acto ";
				$cadenaSql .= "FROM donacion_entrada ";
				$cadenaSql .= "WHERE id_donacion='" . $variable . "';";
				
				break;
			
			case "consultarSobrante" :
				
				$cadenaSql = "SELECT  ";
				$cadenaSql .= " observaciones, nombre_acta ";
				$cadenaSql .= "FROM sobrante_entrada ";
				$cadenaSql .= "WHERE id_sobrante='" . $variable . "';";
				
				break;
			
			case "consultarProduccion" :
				
				$cadenaSql = "SELECT  ";
				$cadenaSql .= " observaciones, nombre_acta ";
				$cadenaSql .= "FROM produccion_entrada ";
				$cadenaSql .= "WHERE id_produccion='" . $variable . "';";
				
				break;
			
			case "consultarRecuperacion" :
				
				$cadenaSql = "SELECT  ";
				$cadenaSql .= "observaciones, nombre_acta ";
				$cadenaSql .= "FROM recuperacion_entrada ";
				$cadenaSql .= "WHERE id_recuperacion='" . $variable . "';";
				
				break;
			
			case "ActualizarReposicion" :
				$cadenaSql = " UPDATE reposicion_entrada ";
				$cadenaSql .= " SET id_entrada='" . $variable [0] . "', ";
				$cadenaSql .= "  id_hurto='" . $variable [1] . "', ";
				$cadenaSql .= "  id_salida='" . $variable [2] . "' ";
				$cadenaSql .= "  WHERE id_reposicion='" . $variable [3] . "' ";
				$cadenaSql .= "  RETURNING  id_reposicion ";
				
				break;
			
			case "actualizarDonacion" :
				$cadenaSql = " UPDATE donacion_entrada ";
				$cadenaSql .= " SET ruta_acto='" . $variable [0] . "', ";
				$cadenaSql .= "  nombre_acto='" . $variable [1] . "'  ";
				$cadenaSql .= "  WHERE id_donacion='" . $variable [2] . "' ";
				$cadenaSql .= "  RETURNING  id_donacion ";
				
				break;
			
			case "actualizarSobrante" :
				$cadenaSql = " UPDATE sobrante_entrada ";
				$cadenaSql .= " SET observaciones='" . $variable [2] . "' ";
				if ($variable [0] == 1) {
					
					$cadenaSql .= " , ruta_acta='" . $variable [3] . "' , ";
					$cadenaSql .= "  nombre_acta='" . $variable [4] . "'  ";
				}
				$cadenaSql .= "  WHERE id_sobrante='" . $variable [1] . "' ";
				$cadenaSql .= "  RETURNING  id_sobrante ";
				break;
			
			case "actualizarProduccion" :
				$cadenaSql = " UPDATE produccion_entrada ";
				$cadenaSql .= " SET observaciones='" . $variable [2] . "' ";
				if ($variable [0] == 1) {
					
					$cadenaSql .= " , ruta_acta='" . $variable [3] . "' , ";
					$cadenaSql .= "  nombre_acta='" . $variable [4] . "'  ";
				}
				$cadenaSql .= "  WHERE id_produccion='" . $variable [1] . "' ";
				$cadenaSql .= "  RETURNING  id_produccion ";
				
				break;
			
			case "actualizarRecuperacion" :
				$cadenaSql = " UPDATE recuperacion_entrada ";
				$cadenaSql .= " SET observaciones='" . $variable [2] . "' ";
				if ($variable [0] == 1) {
					
					$cadenaSql .= " , ruta_acta='" . $variable [3] . "' , ";
					$cadenaSql .= "  nombre_acta='" . $variable [4] . "'  ";
				}
				$cadenaSql .= "  WHERE id_recuperacion='" . $variable [1] . "' ";
				$cadenaSql .= "  RETURNING  id_recuperacion ";
				
				break;
			// UPDATE info_clase_entrada
			// SET id_info_clase=?, observacion=?, id_entrada=?, id_salida=?, id_hurto=?,
			// num_placa=?, val_sobrante=?, ruta_archivo=?, nombre_archivo=?
			// WHERE <condition>;
			case "actualizarInformacionArchivo" :
				$cadenaSql = " UPDATE info_clase_entrada ";
				$cadenaSql .= " SET observacion= '" . $variable [0] . "', ";
				$cadenaSql .= "  id_entrada='" . $variable [1] . "', ";
				$cadenaSql .= "  id_salida='" . $variable [2] . "', ";
				$cadenaSql .= "  id_hurto='" . $variable [3] . "', ";
				$cadenaSql .= "  num_placa='" . $variable [4] . "', ";
				$cadenaSql .= "  val_sobrante='" . $variable [5] . "', ";
				$cadenaSql .= "  ruta_archivo='" . $variable [6] . "', ";
				$cadenaSql .= "  nombre_archivo='" . $variable [7] . "'  ";
				$cadenaSql .= "  WHERE id_info_clase='" . $variable [8] . "' ";
				
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
			
			case "actualizarInformacion" :
				$cadenaSql = " UPDATE info_clase_entrada ";
				$cadenaSql .= " SET observacion= '" . $variable [0] . "', ";
				$cadenaSql .= "  id_entrada='" . $variable [1] . "', ";
				$cadenaSql .= "  id_salida='" . $variable [2] . "', ";
				$cadenaSql .= "  id_hurto='" . $variable [3] . "', ";
				$cadenaSql .= "  num_placa='" . $variable [4] . "', ";
				$cadenaSql .= "  val_sobrante='" . $variable [5] . "', ";
				$cadenaSql .= "  WHERE id_info_clase='" . $variable [6] . "' ";
				
				break;
			
			case "actualizarEntrada" :
				$cadenaSql = " UPDATE entrada ";
				$cadenaSql .= " SET vigencia='" . $variable [0] . "', ";
				$cadenaSql .= "  clase_entrada='" . $variable [1] . "', ";
				$cadenaSql .= "  tipo_contrato='" . $variable [2] . "', ";
				$cadenaSql .= "  numero_contrato='" . $variable [3] . "', ";
				$cadenaSql .= "  fecha_contrato=" . $variable [4] . ", ";
				$cadenaSql .= "  proveedor=". $variable [5] . ", ";
				$cadenaSql .= "  numero_factura=" . $variable [6] . ", ";
				$cadenaSql .= "  fecha_factura=" . $variable [7] . ", ";
				$cadenaSql .= "  observaciones='" . $variable [8] . "', ";
				$cadenaSql .= "  acta_recibido='" . $variable [10] . "', ";
				$cadenaSql .= "  ordenador=" . $variable [11] . ", ";
				$cadenaSql .= "  sede='" . $variable [12] . "', ";
				$cadenaSql .= "  dependencia='" . $variable [13] . "', ";
				$cadenaSql .= "  supervisor='" . $variable [14] . "', ";
				$cadenaSql .= "  tipo_ordenador=" . $variable [15] . ", ";
				$cadenaSql .= "  identificacion_ordenador=" . $variable [16] . ", ";
				$cadenaSql .= "  info_clase='" . $variable [17] . "', ";
				$cadenaSql .= "  estado_entrada='1'  ";
				$cadenaSql .= "  WHERE id_entrada='" . $variable [9] . "' ";
				$cadenaSql .= "  RETURNING  consecutivo||' - ('||vigencia||')' entrada ";
				
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
			
			case "dependenciasConsultadas" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE sa.\"ESF_ID_SEDE\"='" . $variable . "' ";
				$cadenaSql .= " AND  ad.\"ESF_ESTADO\"='A'";
				
				break;
			
			case 'consultarActas' :
				$cadenaSql = "SELECT registro_actarecibido.* , contratos.numero_contrato, contratos.fecha_contrato ";
				$cadenaSql .= "FROM registro_actarecibido  ";
				$cadenaSql .= "LEFT JOIN  contratos ON contratos.id_contrato=registro_actarecibido.id_contrato ";
				$cadenaSql .= "WHERE  id_actarecibido='" . $variable . "';";
				
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
		}
		return $cadenaSql;
	}
}
?>
