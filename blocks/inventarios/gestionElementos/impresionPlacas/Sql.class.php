<?php

namespace inventarios\gestionElementos\impresionPlacas;

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
			
			case "buscar_placa" :
				$cadenaSql = " SELECT DISTINCT id_elemento_ind, placa as placas ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "WHERE placa IS NOT NULL ";
				$cadenaSql .= "AND estado_registro='t' ";
				$cadenaSql .= "ORDER BY placa ASC ";
				
				break;
			
			case "buscar_serie" :
				$cadenaSql = " SELECT DISTINCT serie, serie as series ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "WHERE  serie <> '' ";
				$cadenaSql .= "ORDER BY serie DESC ";
				
				break;
			
			case "consultarPlacas" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "id_elemento_ind, placa,  ";
				$cadenaSql .= "fecha_registro ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "WHERE 1=1 AND estado_registro='TRUE' ";
				$cadenaSql .= "AND placa IS NOT NULL ";
				if ($variable ['placa_inicial'] != '') {
					$cadenaSql .= " AND id_elemento_ind BETWEEN CAST ( '" . $variable ['placa_inicial'] . "' AS INTEGER) ";
					$cadenaSql .= " AND  CAST ( '" . $variable ['placa_final'] . "' AS INTEGER)  ";
				}
				
				if ($variable ['fecha_inicio'] != '') {
					$cadenaSql .= " AND fecha_registro BETWEEN CAST ( '" . $variable ['fecha_inicio'] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable ['fecha_final'] . "' AS DATE)  ";
				}
				
				$cadenaSql .= "ORDER BY id_elemento_ind ASC ; ";
				
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
				$cadenaSql .= " nivel='" . $variable [14] . "'  ";
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
				$cadenaSql .= " nivel='" . $variable [17] . "' ";
				$cadenaSql .= "  WHERE id_elemento='" . $variable [16] . "';";
				
				break;
			
			case "consultar_placa_actulizada" :
				$cadenaSql = " SELECT placa";
				$cadenaSql .= " FROM elemento ";
				$cadenaSql .= " WHERE id_elemento='" . $variable . "'";
				
				break;
			
			case "estado_elemento" :
				
				$cadenaSql = " UPDATE ";
				$cadenaSql .= " elemento";
				$cadenaSql .= " SET ";
				$cadenaSql .= " estado='FALSE' ";
				$cadenaSql .= "  WHERE id_elemento='" . $variable . "';";
				break;
			// INSERT INTO elemento_anulado(
			// id_elemento_anulado, id_elemento, observacion)
			// VALUES ?, ?);
			
			case "anular_elemento" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " elemento_anulado(";
				$cadenaSql .= "id_elemento, observacion) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "') ";
				
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
			
			case "buscarMaxPlacas" :
				$cadenaSql = " SELECT DISTINCT id_elemento_ind, placa as placas ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "WHERE id_elemento_ind >= " . $variable . " ";
				$cadenaSql .= "AND estado_registro='t' ";
				$cadenaSql .= "ORDER BY placa ASC ";
				
				break;
			
			case "ConsultasPlacas" :
				$cadenaSql = " SELECT DISTINCT placa AS value, id_elemento_ind as data ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "WHERE placa IS NOT NULL  ";
				$cadenaSql .= " AND placa LIKE '%" . $variable . "%' ";
				$cadenaSql .= "ORDER BY placa DESC ;";
				break;
		}
		return $cadenaSql;
	}
}
?>
