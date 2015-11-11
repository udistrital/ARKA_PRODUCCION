<?php

namespace inventarios\gestionElementos\periodoLevantamiento;

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
	function cadena_sql($tipo, $variable = "") {
		
		/**
		 * 1.
		 * Revisar las variables para evitar SQL Injection
		 */
		$prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
		$idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );
		
		switch ($tipo) {
			
			case "Actualizar_Tabla_Periodo" :
				$cadenaSql = " UPDATE arka_movil.periodo_levantamiento  ";
				$cadenaSql .= " SET  fecha_cierre=' " . date ( 'Y-m-d' ) . "', ";
				$cadenaSql .= "cierre_levantamiento=' TRUE  ";
				$cadenaSql .= "WHERE estado_registro=TRUE ;  ";
				
				break;
			
			
			case "Limpiar_Radicados" :
				
				$cadenaSql = "TRUNCATE TABLE  arka_movil.radicado_levantamiento  ";
				
				break;
			
			case "Limpiar_Elementos_Individuales" :
				
				$cadenaSql = "UPDATE elemento_individual  ";
				$cadenaSql .= "SET confirmada_existencia='FALSE' ,tipo_confirmada=0; ";
				
				break;
			
			case "Registrar_Historial_Funcionario" :
				
				$cadenaSql = "INSERT INTO historial_levantamiento_funcionario(";
				$cadenaSql .= " id_levantamiento, dependencia, funcionario,
											num_elementos, radicacion,aprobacion , fecha_registro, sede) ";
				$cadenaSql .= " VALUES ( ";
				$cadenaSql .= "'" . $variable [0] . "', ";
				$cadenaSql .= "'" . $variable [1] . "', ";
				$cadenaSql .= "'" . $variable [2] . "', ";
				$cadenaSql .= "'" . $variable [3] . "', ";
				$cadenaSql .= "'" . $variable [4] . "', ";
				$cadenaSql .= "'" . $variable [5] . "', ";
				$cadenaSql .= "'" . $variable [6] . "', ";
				$cadenaSql .= "'" . $variable [7] . "') ;";
				
				break;
			
			case "Registrar_Historial_Placas" :
				
				$cadenaSql = "INSERT INTO historial_levantamiento_placa( ";
				$cadenaSql .= "id_levantamiento, funcionario, placa, confirmacion_existencia, fecha_registro, ubicacion ) ";
				$cadenaSql .= " VALUES ( ";
				$cadenaSql .= "'" . $variable [0] . "', ";
				$cadenaSql .= "'" . $variable [1] . "', ";
				$cadenaSql .= "'" . $variable [2] . "', ";
				$cadenaSql .= "'" . $variable [3] . "', ";
				$cadenaSql .= "'" . $variable [4] . "', ";
				$cadenaSql .= "'" . $variable [5] . "') ;";
				
				break;
			
			// SELECT
			// eli.funcionario funcionario,
			
			// ad."ESF_CODIGO_DEP" id_dependencia,
			// count(placa),
			// CASE rl.estado_radicacion WHEN 'TRUE' THEN 'TRUE' ELSE 'FALSE' END radicacion,
			// CASE eli.tipo_confirmada WHEN 1 THEN 'TRUE' ELSE 'FALSE' END aprobacion
			// FROM elemento_individual eli
			// JOIN elemento ele ON ele.id_elemento =eli .id_elemento_gen
			// JOIN tipo_bienes tb ON tb.id_tipo_bienes = ele.tipo_bien
			// LEFT JOIN arka_movil.radicado_levantamiento rl ON rl.funcionario = eli.funcionario
			// LEFT JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO"=eli.ubicacion_elemento
			// LEFT JOIN arka_parametros.arka_dependencia as ad ON ad."ESF_ID_ESPACIO"=eli.ubicacion_elemento
			// LEFT JOIN arka_parametros.arka_sedes as sas ON sas."ESF_COD_SEDE"=espacios."ESF_COD_SEDE"
			
			// WHERE 1=1 AND tb.id_tipo_bienes <> 1
			
			// GROUP BY eli.funcionario , ad."ESF_CODIGO_DEP",rl.estado_radicacion,eli.tipo_confirmada ;
			
			case "Rescatar_Datos_Levantamiento" :
				
				$cadenaSql = "SELECT DISTINCT  ";
				$cadenaSql .= " eli.funcionario funcionario,
				 ad.\"ESF_CODIGO_DEP\" id_dependencia,
				ad.\"ESF_DEP_ENCARGADA\" nombre_dependencia,
				sas.\"ESF_ID_SEDE\" id_sede,
				sas.\"ESF_SEDE\" nombre_sede,		
				count(placa) num_elementos,
				CASE rl.estado_radicacion WHEN 'TRUE' THEN 'TRUE' ELSE 'FALSE' END radicacion,
				CASE eli.tipo_confirmada WHEN 1 THEN 'TRUE' ELSE 'FALSE' END 	aprobacion
				 ";
				$cadenaSql .= "FROM elemento_individual eli ";
				$cadenaSql .= "JOIN elemento ele ON ele.id_elemento =eli .id_elemento_gen ";
				$cadenaSql .= "JOIN tipo_bienes tb ON tb.id_tipo_bienes = ele.tipo_bien ";
				$cadenaSql .= "LEFT JOIN  arka_movil.radicado_levantamiento rl ON rl.funcionario =  eli.funcionario ";
				$cadenaSql .= "JOIN  arka_parametros.arka_funcionarios  fn ON fn.\"FUN_IDENTIFICACION\" =  eli.funcionario ";
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO"=eli.ubicacion_elemento ';
				$cadenaSql .= ' INNER JOIN arka_parametros.arka_dependencia as ad ON ad."ESF_ID_ESPACIO"=eli.ubicacion_elemento ';
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_sedes as sas ON sas."ESF_COD_SEDE"=espacios."ESF_COD_SEDE" ';
				$cadenaSql .= "WHERE fn.\"FUN_ESTADO\"='A' ";
				$cadenaSql .= " AND tb.id_tipo_bienes <> 1  ";
				$cadenaSql .= " AND  eli.estado_registro = 'TRUE'  ";
				$cadenaSql .= " AND eli.funcionario <> 0  ";
				$cadenaSql .= "GROUP BY eli.funcionario ,sas.\"ESF_SEDE\" ,sas.\"ESF_ID_SEDE\",ad.\"ESF_DEP_ENCARGADA\",ad.\"ESF_CODIGO_DEP\",rl.estado_radicacion,eli.tipo_confirmada ;";
				
				break;
			
			case "Inhabilitar_periodos_anteriores" :
				
				$cadenaSql = "UPDATE historial_levantamiento_placa  ";
				$cadenaSql .= "SET estado='FALSE'  ";
				
				break;
			
			case "Rescatar_Verificacion_Placas" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "eli.placa, eli.funcionario, eli.confirmada_existencia, eli.ubicacion_elemento  ";
				$cadenaSql .= "FROM elemento_individual  eli ";
				$cadenaSql .= "JOIN elemento ele ON ele.id_elemento =eli .id_elemento_gen ";
				$cadenaSql .= "JOIN tipo_bienes  tb ON tb.id_tipo_bienes = ele.tipo_bien ";
				$cadenaSql .= "WHERE 1=1 ";
				$cadenaSql .= " AND tb.id_tipo_bienes <> 1 ";
				$cadenaSql .= " AND 	eli.estado_registro = 'TRUE'  ";
				$cadenaSql .= " AND eli.funcionario IS NOT NULL  ";
				$cadenaSql .= " AND eli.funcionario <> 0 ";
				
				break;
			
			case "Actualizar_Periodos" :
				$cadenaSql = " UPDATE arka_movil.periodo_levantamiento  ";
				$cadenaSql .= " SET  fecha_inicio=' " . $variable [0] . "', ";
				$cadenaSql .= "fecha_final=' " . $variable [1] . "'  ";
				$cadenaSql .= "WHERE estado_registro=TRUE ;  ";
				
				break;
			
			case "Verificar_Periodo" :
				$cadenaSql = " SELECT *   ";
				$cadenaSql .= " FROM  arka_movil.periodo_levantamiento ";
				$cadenaSql .= "WHERE estado_registro=TRUE ;";
				
				break;
			
			case "Actualizar_Periodos_Anterioes" :
				$cadenaSql = " UPDATE arka_movil.periodo_levantamiento  ";
				$cadenaSql .= " SET  estado_registro=false ; ";
				
				break;
			case "registroPeriodo" :
				$cadenaSql = " INSERT INTO arka_movil.periodo_levantamiento( ";
				$cadenaSql .= " fecha_inicio, fecha_final, vigencia,fecha_registro) ";
				$cadenaSql .= " VALUES ( ";
				$cadenaSql .= "' " . $variable [0] . "', ";
				$cadenaSql .= "' " . $variable [1] . "', ";
				$cadenaSql .= "' " . $variable [2] . "', ";
				$cadenaSql .= "' " . $variable [3] . "') ";
				
				break;
			
			case "proveedores" :
				$cadenaSql = " SELECT PRO_NIT,PRO_NIT||' - '||PRO_RAZON_SOCIAL AS proveedor ";
				$cadenaSql .= " FROM PROVEEDORES ";
				
				break;
			
			case "consultarContratoParticular" :
				$cadenaSql = "SELECT  ";
				$cadenaSql .= "nombre_contratista, numero_contrato, fecha_contrato,id_documento_soporte,\"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  nom_razon  ";
				$cadenaSql .= " FROM contratos cn";
				$cadenaSql .= " JOIN  arka_parametros.arka_proveedor ap ON ap.\"PRO_NIT\"=cn.nombre_contratista ";
				$cadenaSql .= "WHERE  id_contrato='" . $variable . "';";
				
				break;
			
			case "buscar_Proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  value,\"PRO_NIT\"  AS data  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= "WHERE cast(\"PRO_NIT\" as text) LIKE '%" . $variable . "%' ";
				$cadenaSql .= "OR \"PRO_RAZON_SOCIAL\" LIKE '%" . $variable . "%' LIMIT 10; ";
				
				break;
			
			case 'registroContrato' :
				$cadenaSql = 'INSERT INTO ';
				$cadenaSql .= 'contratos';
				$cadenaSql .= '( ';
				$cadenaSql .= 'nombre_contratista,';
				$cadenaSql .= 'numero_contrato,';
				$cadenaSql .= 'fecha_contrato,';
				$cadenaSql .= 'id_documento_soporte,';
				$cadenaSql .= 'fecha_registro,';
				$cadenaSql .= 'estado';
				$cadenaSql .= ') ';
				$cadenaSql .= 'VALUES ';
				$cadenaSql .= '( ';
				$cadenaSql .= '\'' . $variable [0] . '\', ';
				$cadenaSql .= '\'' . $variable [1] . '\', ';
				$cadenaSql .= '\'' . $variable [2] . '\', ';
				$cadenaSql .= '\'' . $variable [3] . '\', ';
				$cadenaSql .= '\'' . $variable [4] . '\', ';
				$cadenaSql .= '\'' . $variable [5] . '\'';
				$cadenaSql .= ');';
				
				break;
			
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
				$cadenaSql .= '\'' . $variable ['nombre_archivo'] . '\', ';
				$cadenaSql .= '\'' . $variable ['id_unico'] . '\', ';
				$cadenaSql .= '\'' . $variable ['fecha_registro'] . '\', ';
				$cadenaSql .= '\'' . $variable ['ruta'] . '\', ';
				$cadenaSql .= '\'' . $variable ['estado'] . '\'';
				$cadenaSql .= ') RETURNING documento_id;';
				break;
			
			case "consultarContrato" :
				
				$cadenaSql = "SELECT  ";
				$cadenaSql .= " documento_nombre, ";
				$cadenaSql .= " documento_fechar, ";
				$cadenaSql .= " documento_ruta, ";
				$cadenaSql .= " documento_estado, ";
				$cadenaSql .= " documento_idunico, ";
				
				$cadenaSql .= " nombre_contratista, ";
				$cadenaSql .= " numero_contrato, ";
				$cadenaSql .= " fecha_contrato, ";
				$cadenaSql .= " fecha_registro, ";
				$cadenaSql .= " id_contrato  ";
				$cadenaSql .= " FROM ";
				$cadenaSql .= " contratos ";
				$cadenaSql .= " JOIN registro_documento ON documento_id= id_documento_soporte ";
				$cadenaSql .= " WHERE 1=1 AND contratos.estado='TRUE' ";
				if ($variable [0] != '') {
					$cadenaSql .= " AND  numero_contrato= '" . $variable [0] . "'";
				}
				
				if ($variable [1] != '') {
					$cadenaSql .= " AND fecha_contrato BETWEEN CAST ( '" . $variable [1] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [2] . "' AS DATE)  ";
				}
				
				if ($variable [3] != '') {
					$cadenaSql .= " AND fecha_registro BETWEEN CAST ( '" . $variable [3] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [4] . "' AS DATE)  ";
				}
				
				break;
			
			case 'actualizarDocumento' :
				$cadenaSql = 'UPDATE arka_inventarios.registro_documento SET ';
				$cadenaSql .= 'documento_nombre=\'' . $variable ['nombre_archivo'] . '\',';
				$cadenaSql .= 'documento_idunico=\'' . $variable ['id_unico'] . '\',';
				$cadenaSql .= 'documento_fechar=\'' . $variable ['fecha_registro'] . '\',';
				$cadenaSql .= 'documento_ruta=\'' . $variable ['ruta'] . '\',';
				$cadenaSql .= 'documento_estado=\'' . $variable ['estado'] . '\'';
				$cadenaSql .= ' WHERE documento_id=';
				$cadenaSql .= '\'' . $variable ['id_doc'] . '\' ';
				break;
			
			case 'actualizarContrato' :
				$cadenaSql = "UPDATE contratos SET ";
				$cadenaSql .= "nombre_contratista='" . $variable [0] . "',";
				$cadenaSql .= "numero_contrato='" . $variable [1] . "',";
				$cadenaSql .= "fecha_contrato='" . $variable [2] . "' ";
				$cadenaSql .= " WHERE id_contrato=";
				$cadenaSql .= "'" . $variable [3] . "' ";
				
				break;
			/**
			 * /**
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
		}
		
		return $cadenaSql;
	}
}

?>
