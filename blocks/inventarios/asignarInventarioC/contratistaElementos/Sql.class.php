<?php

namespace inventarios\asignarInventarioC\contratistaElementos;

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
				$cadenaSql .= " ORDER BY id_contratista ASC ";
				break;
			
			case "consultarID" :
				$cadenaSql = " SELECT id_contratista ";
				$cadenaSql .= " FROM contratista_servicios ";
				$cadenaSql .= " WHERE identificacion='" . $variable . "' ";
				break;
			
			case "consultarElementosSupervisor" :
				$cadenaSql = "SELECT id_elemento_ind,elemento_nombre as nivel, marca, ei.placa,ei.serie, valor, subtotal_sin_iva, ";
				$cadenaSql .= " total_iva, total_iva_con ";
				$cadenaSql .= " FROM elemento ";
				$cadenaSql .= " JOIN elemento_individual ei ON elemento.id_elemento=ei.id_elemento_gen  ";
				$cadenaSql .= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel ";
				$cadenaSql .= " WHERE  ";
				$cadenaSql .= " ei.estado_registro=TRUE  ";
				$cadenaSql .= " AND ei.estado_asignacion IS NULL  ";
				$cadenaSql .= " AND funcionario='" . $variable . "' ORDER BY nivel ASC ";
				break;
			
			case "consultarElementosContratista" :
				$cadenaSql = " SELECT id_elemento_ind,elemento_nombre as nivel, marca, ";
				$cadenaSql .= " elemento_individual.placa,elemento_individual.serie, valor, subtotal_sin_iva, ";
				$cadenaSql .= " total_iva, total_iva_con, asignar_elementos.verificar_existencia,supervisor, elemento.descripcion ,
						       sas.\"ESF_SEDE\" sede, ad.\"ESF_DEP_ENCARGADA\" dependencia,espacios.\"ESF_NOMBRE_ESPACIO\" espacio_fisico, \"FUN_IDENTIFICACION\"||'-'||\"FUN_NOMBRE\" supervisor_funcionario ";
				$cadenaSql .= " FROM asignar_elementos ";
				$cadenaSql .= " JOIN elemento_individual ON elemento_individual.id_elemento_ind=asignar_elementos.id_elemento ";
				$cadenaSql .= " JOIN arka_parametros.arka_funcionarios fn ON fn.\"FUN_IDENTIFICACION\"= asignar_elementos.supervisor::numeric ";
				$cadenaSql .= " JOIN elemento ON elemento.id_elemento=elemento_individual.id_elemento_gen ";
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO"=elemento_individual.ubicacion_elemento ';
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_dependencia as ad ON ad."ESF_ID_ESPACIO"=elemento_individual.ubicacion_elemento ';
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_sedes as sas ON sas."ESF_COD_SEDE"=espacios."ESF_COD_SEDE" ';
				$cadenaSql .= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel ";
				$cadenaSql .= " WHERE elemento_individual.estado_registro=TRUE  ";
				$cadenaSql .= " AND elemento_individual.id_elemento_ind=asignar_elementos.id_elemento  ";
				$cadenaSql .= " AND elemento_individual.estado_asignacion=TRUE  ";
				$cadenaSql .= " AND asignar_elementos.estado=1  ";
				$cadenaSql .= " AND contratista='" . $variable . "' ORDER BY nivel ASC ";
				
				break;
			
			case "asignarElemento" :
				$cadenaSql = "INSERT INTO asignar_elementos( ";
				$cadenaSql .= " supervisor, ";
				$cadenaSql .= " contratista, ";
				$cadenaSql .= " id_elemento, ";
				$cadenaSql .= " estado, ";
				$cadenaSql .= " fecha_registro) ";
				$cadenaSql .= " VALUES ( ";
				$cadenaSql .= " '" . $variable [0] . "', ";
				$cadenaSql .= " '" . $variable [1] . "', ";
				$cadenaSql .= " '" . $variable [2] . "', ";
				$cadenaSql .= " '" . $variable [3] . "', ";
				$cadenaSql .= " '" . $variable [4] . "'";
				$cadenaSql .= " ); ";
				break;
			
			case "inactivarElemento" :
				$cadenaSql = "UPDATE elemento_individual";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado_asignacion= '" . $variable [1] . "', ";
				$cadenaSql .= " fecha_asignacion = '" . $variable [2] . "'";
				$cadenaSql .= " WHERE id_elemento_ind = '" . $variable [0] . "'; ";
				break;
			
			case "asignarElemento_sup" :
				$cadenaSql = "UPDATE asignar_elementos ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado='" . $variable [1] . "' ";
				// $cadenaSql.= " fecha_registro='" . $variable[2] . "', ";
				$cadenaSql .= " WHERE id_elemento= '" . $variable [0] . "'; ";
				break;
			
			case "inactivarElemento_sup" :
				$cadenaSql = "UPDATE elemento_individual";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado_asignacion= '" . $variable [1] . "', ";
				$cadenaSql .= " fecha_asignacion = '" . $variable [2] . "'";
				$cadenaSql .= " WHERE id_elemento_ind = '" . $variable [0] . "'; ";
				break;
			
			case "activarElemento" :
				$cadenaSql = "UPDATE elemento_individual ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado_asignacion= '" . $variable [1] . "', ";
				$cadenaSql .= " fecha_asignacion = '" . $variable [2] . "'";
				$cadenaSql .= " WHERE id_elemento_ind = '" . $variable [0] . "'; ";
				break;
			
			case "inactivarAsignacion" :
				$cadenaSql = "UPDATE asignar_elementos ";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado='" . $variable [1] . "' ";
				// $cadenaSql.= " fecha_registro='" . $variable[2] . "', ";
				$cadenaSql .= " WHERE id_elemento= '" . $variable [0] . "'; ";
				break;
			
			// Consultas de Oracle para rescate de información de Sicapital
			case "dependencias" :
				$cadenaSql = "SELECT DEP_IDENTIFICADOR, ";
				$cadenaSql .= " DEP_IDENTIFICADOR ||' '|| DEP_DEPENDENCIA ";
				// $cadenaSql .= " DEP_DIRECCION,DEP_TELEFONO ";F
				$cadenaSql .= " FROM DEPENDENCIAS ";
				break;
			
			case "proveedores" :
				$cadenaSql = "SELECT PRO_NIT,PRO_NIT ||' '|| PRO_RAZON_SOCIAL";
				$cadenaSql .= " FROM PROVEEDORES ";
				break;
			
			case "select_proveedor" :
				$cadenaSql = "SELECT PRO_RAZON_SOCIAL";
				$cadenaSql .= " FROM PROVEEDORES ";
				$cadenaSql .= " WHERE PRO_NIT='" . $variable . "' ";
				break;
			
			case "contratistas" :
				$cadenaSql = "SELECT DISTINCT \"CON_IDENTIFICACION\", \"CON_IDENTIFICACION\" ||'-'|| \"CON_NOMBRE\"||': Contrato N#' ||\"CON_NUMERO_CONTRATO\"||' - Vigencia '||\"CON_VIGENCIA_FISCAL\" ";
				$cadenaSql .= "FROM arka_parametros.arka_contratistas ";
				$cadenaSql .= "WHERE \"CON_FECHA_INICIO\" <= '" . date ( 'Y-m-d' ) . "' ";
				$cadenaSql .= "AND \"CON_FECHA_FINAL\" >= '" . date ( 'Y-m-d' ) . "' ";
				
				break;
			
			case "nombreContratista" :
				$cadenaSql = " SELECT *  ";
				$cadenaSql .= "FROM arka_parametros.arka_contratistas ";
				$cadenaSql .= " WHERE \"CON_IDENTIFICACION\"='" . $variable . "' ";
				$cadenaSql .= " AND  \"CON_FECHA_INICIO\" <= '" . date('Y-m-d') . "' ";
				$cadenaSql .= " AND \"CON_FECHA_FINAL\"  >='" . date('Y-m-d') . "' ";
				break;
			
			case "datosFuncionario" :
				$cadenaSql = " SELECT * ";
				$cadenaSql .= "FROM arka_parametros.arka_funcionarios ";
				$cadenaSql .= " WHERE \"FUN_IDENTIFICACION\"='" . $variable . "' ";
				break;
			
			case "jefe_recursos_fisicos" :
				
				$cadenaSql = 'SELECT "FUN_IDENTIFICACION" identificacion, "FUN_NOMBRE" nombre ';
				$cadenaSql .= " FROM arka_parametros.arka_funcionarios ";
				$cadenaSql .= " WHERE 1=1 ";
				$cadenaSql .= ' AND "FUN_ESTADO"=';
				$cadenaSql .= "'A'  ";
				$cadenaSql .= ' AND "FUN_CARGO" ';
				$cadenaSql .= " ='JEFE DE SECCION' ";
				$cadenaSql .= ' AND "FUN_DEP_COD_ACADEMICA"=60 ; ';
				
				break;
			
			case "validacionDefault" :
				$cadenaSql = " UPDATE arka_inventarios.asignar_elementos ";
				$cadenaSql .= "SET verificar_existencia='FALSE'  ";
				$cadenaSql .= "WHERE contratista='" . $variable . "' ";
				
				break;
			
			case "actualizarVerificacion" :
				$cadenaSql = " UPDATE arka_inventarios.asignar_elementos ";
				$cadenaSql .= "SET verificar_existencia='TRUE'  ";
				$cadenaSql .= "WHERE id_elemento='" . $variable . "' "; 
				
				break;
		}
		return $cadenaSql;
	}
}

?>
