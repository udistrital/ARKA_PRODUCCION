<?php

namespace inventarios\gestionEntradas\consultaEntradas;

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
				$cadenaSql = " SELECT DISTINCT id_entrada valor, consecutivo||' - ('||entrada.vigencia||')' descripcion  ";
				$cadenaSql .= " FROM entrada  ";
				$cadenaSql .= "WHERE entrada.cierre_contable='f' ";
				$cadenaSql .= "ORDER BY id_entrada DESC ;";
				
				break;
			
			case "proveedor_informacion" :
				$cadenaSql = " SELECT \"PRO_NIT\", \"PRO_RAZON_SOCIAL\"  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor ";
				$cadenaSql .= " WHERE \"PRO_NIT\"='" . $variable . "'";
				
				break;
			
			case "proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\",\"PRO_NIT\"||' - '||\"PRO_RAZON_SOCIAL\" AS proveedor ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor ";
				
				break;
			
			case "consultarEntrada" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "entrada.id_entrada, entrada.fecha_registro,  ";
				$cadenaSql .= " clase_entrada.descripcion,proveedor,entrada.consecutivo||' - ('||entrada.vigencia||')' consecutivo,estado_entrada.descripcion estado_entradas ,cierre_contable  ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "JOIN clase_entrada ON clase_entrada.id_clase = entrada.clase_entrada ";
				$cadenaSql .= "JOIN estado_entrada ON estado_entrada.id_estado = entrada.estado_entrada ";
				$cadenaSql .= "JOIN salida sa ON sa.id_entrada = entrada.id_entrada ";
				$cadenaSql .= "WHERE 1=1 ";
				$cadenaSql .= "AND entrada.cierre_contable='" . $variable [5] . "' ";
				$cadenaSql .= "AND sa.id_entrada IS NOT NULL   ";
				
				if ($variable [0] != '') {
					$cadenaSql .= " AND entrada.id_entrada = '" . $variable [0] . "'";
				}
				
				if ($variable [1] != '') {
					$cadenaSql .= " AND entrada.fecha_registro BETWEEN CAST ( '" . $variable [1] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [2] . "' AS DATE)  ";
				}
				
				if ($variable [3] != '') {
					$cadenaSql .= " AND estado_entrada	 = '" . $variable [3] . "'";
				}
				if ($variable [4] != '') {
					$cadenaSql .= " AND entrada.proveedor = '" . $variable [4] . "'";
				}
				$cadenaSql .= "ORDER BY entrada.id_entrada DESC ; ";
				
				break;
			
			case "estado_entrada" :
				$cadenaSql = " SELECT id_estado, descripcion ";
				$cadenaSql .= "FROM estado_entrada;";
				
				break;
			
			case "consultarEstadoEntradas" :
				
				$cadenaSql = " SELECT DISTINCT ";
				$cadenaSql .= " consecutivo||' - ('||entrada.vigencia||')' entrada, entrada.fecha_registro,clase_entrada.descripcion clase_entrada";
				$cadenaSql .= " ,estado_entrada.descripcion estado";
				$cadenaSql .= " FROM entrada ";
				$cadenaSql .= " JOIN clase_entrada ON clase_entrada.id_clase = entrada.clase_entrada ";
				$cadenaSql .= " JOIN estado_entrada ON estado_entrada.id_estado = entrada.estado_entrada ";
				$cadenaSql .= " WHERE ";
				$cadenaSql .= " id_entrada = '" . $variable . "';";
				
				break;
			
			case "actualizarEstado" :
				$cadenaSql = " UPDATE entrada ";
				$cadenaSql .= " SET estado_entrada='" . $variable ['estadoNuevo'] . "', ";
				$cadenaSql .= " fecha_registro='" . $variable ['fechaRegistro'] . "', ";
				$cadenaSql .= " estado_registro='" . $variable ['estadoRegistro'] . "' ";
				$cadenaSql .= "  WHERE id_entrada='" . $variable ['numeroEntrada'] . "' ";
				// $cadenaSql .= " RETURNING consecutivo||' - ('||entrada.vigencia||')' entrada ; ";
				break;
		}
		return $cadenaSql;
	}
}
?>
