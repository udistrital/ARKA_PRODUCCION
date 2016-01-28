<?php

namespace inventarios\asignarInventarioC\asignarInventario;

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
			
			case "consultarContratista" :
				$cadenaSql = " SELECT id_contratista, identificacion ";
				$cadenaSql .= " FROM contratista_servicios ";
				$cadenaSql .= " WHERE identificacion='" . $variable . "';";
				break;
			
			case "consultarElementosSupervisor" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "  eli.id_elemento_ind  , eli.placa ,
											ele.marca marca,
											ele.serie serie,
										sas.\"ESF_SEDE\" sede,
						       ad.\"ESF_DEP_ENCARGADA\" dependencia,
						   espacios.\"ESF_NOMBRE_ESPACIO\" espacio_fisico, 
						ele.descripcion,
						
						
						
							CASE eli.confirmada_existencia
											WHEN  't'  THEN 'X' 
											ELSE  ' '
											END  marca_existencia,
										\"FUN_NOMBRE\" nombre_funcionario, 					
                						 
                						CASE
                						WHEN  tfs.descripcion IS  NULL THEN 'Activo'
										ELSE  tfs.descripcion  
                						END   as estado_bien, 
						               ele.descripcion descripcion_elemento,
						              eli.confirmada_existencia , 
						               eli.tipo_confirmada, espacios.\"ESF_NOMBRE_ESPACIO\" espaciofisico,
										crn.\"CON_IDENTIFICACION\"||'-'||crn.\"CON_NOMBRE\" contratista 
						
						
						
						               ";
				$cadenaSql .= "FROM elemento_individual  eli ";
				$cadenaSql .= "JOIN elemento ele ON ele.id_elemento =eli .id_elemento_gen ";
				$cadenaSql .= "JOIN tipo_bienes  tb ON tb.id_tipo_bienes = ele.tipo_bien ";
				$cadenaSql .= "LEFT JOIN estado_elemento  est ON est.id_elemento_ind = eli.id_elemento_ind ";
				$cadenaSql .= "LEFT JOIN tipo_falt_sobr  tfs ON tfs.id_tipo_falt_sobr = est.tipo_faltsobr   ";
				$cadenaSql .= "LEFT JOIN arka_parametros.arka_funcionarios fn ON fn.\"FUN_IDENTIFICACION\"= eli.funcionario ";
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO"=eli.ubicacion_elemento ';
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_dependencia as ad ON ad."ESF_ID_ESPACIO"=eli.ubicacion_elemento ';
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_sedes as sas ON sas."ESF_COD_SEDE"=espacios."ESF_COD_SEDE" ';
				$cadenaSql .= ' LEFT JOIN  asignar_elementos asl ON asl.id_elemento=eli.id_elemento_ind ';
				$cadenaSql .= ' LEFT JOIN  arka_parametros.arka_contratistas crn ON crn."CON_IDENTIFICACION"=asl.contratista  ';
				$cadenaSql .= "WHERE tb.id_tipo_bienes <> 1 ";
				$cadenaSql .= " AND eli.estado_registro = 'TRUE'  ";
				$cadenaSql .= " AND eli.estado_asignacion=FALSE  ";
				$cadenaSql .= " AND eli.funcionario= '" . $variable . "' ";
				
				break;
			
			case "consultarID" :
				$cadenaSql = " SELECT id_contratista ";
				$cadenaSql .= " FROM contratista_servicios ";
				$cadenaSql .= " WHERE identificacion = '" . $variable . "' ";
				break;
			
			case "asignarElemento" :
				$cadenaSql = "INSERT INTO asignar_elementos( ";
				$cadenaSql .= " contratista, ";
				$cadenaSql .= " supervisor, ";
				$cadenaSql .= " id_elemento, ";
				$cadenaSql .= " estado, ";
				$cadenaSql .= " fecha_registro,tipo_contrato,numero_contrato,vigencia) ";
				$cadenaSql .= " VALUES ( ";
				$cadenaSql .= " '" . $variable [0] . "', ";
				$cadenaSql .= " '" . $variable [1] . "', ";
				$cadenaSql .= " '" . $variable [2] . "', ";
				$cadenaSql .= " '" . $variable [3] . "', ";
				$cadenaSql .= " '" . $variable [4] . "', ";
				$cadenaSql .= " '" . $variable [5] . "', ";
				$cadenaSql .= " '" . $variable [6] . "', ";
				$cadenaSql .= " '" . $variable [7] . "'";
				$cadenaSql .= " );";
				
				break;
			
			case "inactivarElemento" :
				$cadenaSql = "UPDATE elemento_individual ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado_asignacion = '" . $variable [1] . "', ";
				$cadenaSql .= " fecha_asignacion = '" . $variable [2] . "'";
				$cadenaSql .= " WHERE id_elemento_ind = '" . $variable [0] . "';";
				break;
			
			/* **************** */
			
			// Consultas de Oracle para rescate de información de Sicapital
			case "dependencias" :
				$cadenaSql = "SELECT DEP_IDENTIFICADOR, ";
				$cadenaSql .= " DEP_IDENTIFICADOR ||' '|| DEP_DEPENDENCIA ";
				// $cadenaSql .= " DEP_DIRECCION, DEP_TELEFONO ";F
				$cadenaSql .= " FROM DEPENDENCIAS ";
				break;
			
			case "proveedores" :
				$cadenaSql = "SELECT PRO_NIT, PRO_NIT ||' '|| PRO_RAZON_SOCIAL";
				$cadenaSql .= " FROM PROVEEDORES ";
				break;
			
			case "select_proveedor" :
				$cadenaSql = "SELECT PRO_RAZON_SOCIAL";
				$cadenaSql .= " FROM PROVEEDORES ";
				$cadenaSql .= " WHERE PRO_NIT = '" . $variable . "' ";
				break;
			
			case "contratistas" :
				
				$cadenaSql = "SELECT DISTINCT \"CON_IDENTIFICACION\"||'@'||tc_descripcion||'@'||\"CON_NUMERO_CONTRATO\"||'@'||\"CON_VIGENCIA_FISCAL\" valor
						, \"CON_IDENTIFICACION\" ||'-'|| \"CON_NOMBRE\"||': Contrato '|| tc_descripcion ||' N#' ||\"CON_NUMERO_CONTRATO\"||' - Vigencia '||\"CON_VIGENCIA_FISCAL\" ";
				$cadenaSql .= "FROM arka_parametros.arka_contratistas ";
				$cadenaSql .= "JOIN arka_parametros.arka_tipo_contrato tp ON tp.tc_identificador=\"CON_TIPO_CONTRATO\"";
				$cadenaSql .= "WHERE \"CON_FECHA_INICIO\" <= '" . date ( 'Y-m-d' ) . "' ";
				$cadenaSql .= "AND \"CON_FECHA_FINAL\" >= '" . date ( 'Y-m-d' ) . "' ";
				
				break;
			
			case "nombreContratista" :
				$cadenaSql = " SELECT \"CON_IDENTIFICACION\", \"CON_NOMBRE\" ";
				$cadenaSql .= "FROM arka_parametros.arka_contratistas ";
				$cadenaSql .= " WHERE \"CON_IDENTIFICACION\"='" . $variable . "' ";
				break;
		}
		return $cadenaSql;
	}
}

?>
