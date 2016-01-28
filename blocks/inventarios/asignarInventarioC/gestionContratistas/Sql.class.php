<?php

namespace inventarios\asignarInventarioC\gestionContratista;

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
			
			case "ConsultaTipoBien" :
				
				$cadenaSql = "SELECT ge.elemento_tipobien , tb.descripcion  ";
				$cadenaSql .= "FROM  catalogo.catalogo_elemento ce ";
				$cadenaSql .= "JOIN  grupo.catalogo_elemento ge  ON (ge.elemento_id)::text =ce .elemento_grupoc  ";
				$cadenaSql .= "JOIN  arka_inventarios.tipo_bienes tb ON tb.id_tipo_bienes = ge.elemento_tipobien  ";
				$cadenaSql .= "WHERE ce.elemento_id = '" . $variable . "';";
				
				break;
			
			case "buscar_placa_maxima" :
				$cadenaSql = " SELECT  MAX(placa::FLOAT) placa_max ";
				$cadenaSql .= " FROM elemento_individual ";
				break;
			
			case "buscar_repetida_placa" :
				$cadenaSql = " SELECT  count (placa) ";
				$cadenaSql .= " FROM elemento_individual ";
				$cadenaSql .= " WHERE placa ='" . $variable . "';";
				break;
			
			case "proveedor_informacion" :
				$cadenaSql = " SELECT PRO_NIT,PRO_RAZON_SOCIAL  ";
				$cadenaSql .= " FROM PROVEEDORES ";
				$cadenaSql .= " WHERE PRO_NIT='" . $variable . "'";
				
				break;
			
			case "proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\",\"PRO_NIT\"||' - '||\"PRO_RAZON_SOCIAL\" AS proveedor ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor ";
				
				break;
			
			case "clase_entrada" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_clase, descripcion  ";
				$cadenaSql .= "FROM clase_entrada;";
				
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
			
			case "consultar_entrada_acta" :
				
				$cadenaSql = "SELECT acta_recibido ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "'";
				
				break;
			
			case "consultar_elementos_acta" :
				
				$cadenaSql = "SELECT id_items ";
				$cadenaSql .= "FROM items_actarecibido ";
				$cadenaSql .= "WHERE id_acta='" . $variable . "'";
				
				break;
			
			case "consultar_elementos_entrada" :
				
				$cadenaSql = "SELECT id_elemento ";
				$cadenaSql .= "FROM elemento  ";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "'";
				
				break;
			
			case "idElementoMax" :
				
				$cadenaSql = "SELECT max(id_elemento) ";
				$cadenaSql .= "FROM elemento  ";
				
				break;
			
			case "idElementoMaxIndividual" :
				
				$cadenaSql = "SELECT max(id_elemento_ind) ";
				$cadenaSql .= "FROM elemento_individual  ";
				
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
			
			// INSERT INTO arka_movil.asignar_imagen(
			// num_registro, , estado_registro)
			// VALUES (?, ?, ?, ?, ?);
			
			case "ElementoImagen" :
				
				$cadenaSql = " 	INSERT INTO arka_movil.asignar_imagen(";
				$cadenaSql .= " id_elemento, prioridad, imagen ) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable ['elemento'] . "',";
				$cadenaSql .= "'" . $variable ['prioridad'] . "',";
				$cadenaSql .= "'" . $variable ['imagen'] . "') ";
				$cadenaSql .= "RETURNING num_registro; ";
				
				break;
			
			case "ingresar_elemento_individual" :
				
				$cadenaSql = " 	INSERT INTO elemento_individual(";
				$cadenaSql .= "fecha_registro, placa, serie, id_elemento_gen,id_elemento_ind) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= ((is_null ( $variable [1] )) ? 'null' . "," : "'" . $variable [1] . "',");
				$cadenaSql .= ((is_null ( $variable [2] )) ? 'null' . "," : "'" . $variable [2] . "',");
				$cadenaSql .= "'" . $variable [3] . "',";
				$cadenaSql .= "'" . $variable [4] . "') ";
				$cadenaSql .= "RETURNING id_elemento_ind; ";
				
				break;
			
			case "ingresar_elemento_tipo_1" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " elemento(";
				$cadenaSql .= "fecha_registro,nivel, tipo_bien, descripcion, cantidad, ";
				$cadenaSql .= "unidad, valor, iva, ajuste, bodega, subtotal_sin_iva, total_iva, ";
				$cadenaSql .= "total_iva_con,marca,serie,id_entrada, id_elemento, cantidad_por_asignar ) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
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
				$cadenaSql .= "'" . $variable [11] . "',";
				$cadenaSql .= "'" . $variable [12] . "',";
				$cadenaSql .= "'" . $variable [13] . "',";
				$cadenaSql .= "'" . $variable [14] . "',";
				$cadenaSql .= "'" . $variable [15] . "',";
				$cadenaSql .= "'" . $variable [16] . "',";
				$cadenaSql .= "'" . $variable [4] . "') ";
				$cadenaSql .= "RETURNING  id_elemento; ";
				
				break;
			
			case "ingresar_elemento_tipo_2" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " elemento(";
				$cadenaSql .= "fecha_registro,nivel,tipo_bien, descripcion, cantidad, ";
				$cadenaSql .= "unidad, valor, iva, ajuste, bodega, subtotal_sin_iva, total_iva, ";
				$cadenaSql .= "total_iva_con,tipo_poliza, fecha_inicio_pol, fecha_final_pol,marca,serie,id_entrada,id_elemento,cantidad_por_asignar) ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
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
				$cadenaSql .= "'" . $variable [11] . "',";
				$cadenaSql .= "'" . $variable [12] . "',";
				$cadenaSql .= "'" . $variable [13] . "',";
				if ($variable [13] == 0) {
					
					$cadenaSql .= "NULL,";
					$cadenaSql .= "NULL,";
				} else {
					
					$cadenaSql .= "'" . $variable [14] . "',";
					$cadenaSql .= "'" . $variable [15] . "',";
				}
				
				$cadenaSql .= "'" . $variable [16] . "',";
				$cadenaSql .= "'" . $variable [17] . "',";
				$cadenaSql .= "'" . $variable [18] . "',";
				$cadenaSql .= "'" . $variable [19] . "',";
				$cadenaSql .= "'" . $variable [4] . "') ";
				$cadenaSql .= "RETURNING  id_elemento; ";
				
				break;
			
			case "buscar_entradas" :
				$cadenaSql = " SELECT DISTINCT id_entrada valor, consecutivo||' - ('||entrada.vigencia||')' descripcion  ";
				$cadenaSql .= " FROM entrada  ";
				$cadenaSql .= "WHERE cierre_contable='f' ";
				$cadenaSql .= "AND   estado_registro='t' ";
				$cadenaSql .= "AND   estado_entrada = 1  ";
				$cadenaSql .= "ORDER BY id_entrada DESC ;";
				
				break;
			
			case "consultarEntrada" :
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "en.id_entrada, en.fecha_registro,  ";
				$cadenaSql .= " ce.descripcion,pr.\"PRO_NIT\" as nit , en.consecutivo||' - ('||en.vigencia||')' entradas , en.vigencia ,  pr.\"PRO_RAZON_SOCIAL\" as razon_social  ";
				$cadenaSql .= "FROM entrada en  ";
				$cadenaSql .= "JOIN clase_entrada ce ON ce.id_clase = en.clase_entrada ";
				$cadenaSql .= "LEFT JOIN arka_parametros.arka_proveedor pr ON pr.\"PRO_NIT\" = CAST(en.proveedor AS CHAR(50)) ";
				$cadenaSql .= "WHERE en.cierre_contable='f'  ";
				$cadenaSql .= "AND   en.estado_registro='t' ";
				$cadenaSql .= "AND   en.estado_entrada = 1  ";
				
				if ($variable [0] != '') {
					$cadenaSql .= " AND en.id_entrada = '" . $variable [0] . "' ";
				}
				
				if ($variable [1] != '') {
					$cadenaSql .= " AND en.fecha_registro BETWEEN CAST ( '" . $variable [1] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [2] . "' AS DATE)  ";
				}
				
				if ($variable [3] != '') {
					$cadenaSql .= " AND clase_entrada = '" . $variable [3] . "' ";
				}
				if ($variable [4] != '') {
					$cadenaSql .= " AND en.proveedor = '" . $variable [4] . "' ";
				}
				
				$cadenaSql .= "ORDER BY en.id_entrada DESC ;";
				
				break;
			
			case "consultarEntradaParticular" :
				
				$cadenaSql = "SELECT  ";
				$cadenaSql .= "entrada.id_entrada, entrada.fecha_registro,  ";
				$cadenaSql .= " cl.descripcion,proveedor, consecutivo||' - ('||entrada.vigencia||')' entradas,entrada.vigencia    ";
				$cadenaSql .= "FROM arka_inventarios.entrada ";
				$cadenaSql .= "JOIN arka_inventarios.clase_entrada cl ON cl.id_clase = entrada.clase_entrada ";
				$cadenaSql .= "WHERE entrada.id_entrada = '" . $variable . "';";
				
				break;
			
			case "buscar_Proveedores" :
				$cadenaSql = " SELECT \"PRO_NIT\"||' - ('||\"PRO_RAZON_SOCIAL\"||')' AS  value,\"PRO_NIT\"  AS data  ";
				$cadenaSql .= " FROM arka_parametros.arka_proveedor  ";
				$cadenaSql .= "WHERE cast(\"PRO_NIT\" as text) LIKE '%" . $variable . "%' ";
				$cadenaSql .= "OR \"PRO_RAZON_SOCIAL\" LIKE '%" . $variable . "%' LIMIT 10; ";
				
				break;
			
			// SELECT "CON_IDENTIFICADOR", "CON_IDENTIFICACION", "CON_NOMBRE", "CON_NUMERO_CONTRATO",
			// "CON_VIGENCIA_FISCAL", "CON_FECHA_INICIO", "CON_FECHA_FINAL"
			// FROM arka_parametros.arka_contratistas;
			
			case "Consultar_Contratistas" :
				$cadenaSql = " SELECT cns.*,tp.tc_descripcion ";
				$cadenaSql .= " FROM arka_parametros.arka_contratistas cns ";
				$cadenaSql .= " JOIN  arka_parametros.arka_tipo_contrato tp ON tp.tc_identificador= cns.\"CON_TIPO_CONTRATO\"  ";
				$cadenaSql .= " WHERE \"CON_VIGENCIA_FISCAL\"= '" . $variable . "' ";
				$cadenaSql .= " AND cns.estado=TRUE ; ";
				
				break;
			
			case "registrarContratista" :
				$cadenaSql = " INSERT INTO ";
				$cadenaSql .= " arka_parametros.arka_contratistas(";
				$cadenaSql .= ' "CON_IDENTIFICACION", "CON_NOMBRE", "CON_NUMERO_CONTRATO",
						        "CON_VIGENCIA_FISCAL", "CON_FECHA_INICIO", "CON_FECHA_FINAL","CON_TIPO_CONTRATO",fecha_registro,estado) ';
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable ['identificacion'] . "',";
				$cadenaSql .= "'" . $variable ['nombres'] . "',";
				$cadenaSql .= "'" . $variable ['numero'] . "',";
				$cadenaSql .= "'" . $variable ['vigencia'] . "',";
				$cadenaSql .= "'" . $variable ['fecha_inicial'] . "',";
				$cadenaSql .= "'" . $variable ['fecha_final'] . "',";
				$cadenaSql .= "'" . $variable ['tipo_contrato'] . "',";
				$cadenaSql .= "'" . date ( 'Y-m-d' ) . "',";
				$cadenaSql .= " TRUE )";
				
				break;
			
			case "consultarContrato" :
				$cadenaSql = " SELECT *  ";
				$cadenaSql .= " FROM arka_parametros.arka_contratistas  ";
				$cadenaSql .= "WHERE \"CON_IDENTIFICADOR\"= '" . $variable . "'; ";
				
				break;
			
			case "consultarvigencias" :
				$cadenaSql = " SELECT DISTINCT  \"CON_VIGENCIA_FISCAL\" vigencia, \"CON_VIGENCIA_FISCAL\" descripcion  ";
				$cadenaSql .= " FROM arka_parametros.arka_contratistas  ";
				
				break;
			
			case "modificarContrato" :
				
				$cadenaSql = "UPDATE arka_parametros.arka_contratistas ";
				$cadenaSql .= "SET  ";
				$cadenaSql .= " \"CON_NUMERO_CONTRATO\" ='" . $variable ['numero'] . "', ";
				$cadenaSql .= " \"CON_IDENTIFICACION\"='" . $variable ['identificacion'] . "', ";
				$cadenaSql .= " \"CON_NOMBRE\"='" . $variable ['nombre'] . "', ";
				$cadenaSql .= " \"CON_FECHA_INICIO\"='" . $variable ['fecha_inicio'] . "', ";
				$cadenaSql .= " \"CON_FECHA_FINAL\"='" . $variable ['fecha_final'] . "' , ";
				$cadenaSql .= " \"CON_TIPO_CONTRATO\"='" . $variable ['tipo_contrato'] . "'  ";
				$cadenaSql .= " WHERE \"CON_IDENTIFICADOR\"='" . $variable ['identificador'] . "' ;";
				
				break;
			
			case 'consultarTiposContrato' :
				
				$cadenaSql = "SELECT tc_identificador, tc_descripcion  ";
				$cadenaSql .= "FROM arka_parametros.arka_tipo_contrato  ";
				$cadenaSql .= "WHERE  estado=TRUE ; ";
				
				break;
			
			case 'Consultar_Tipo_Contrato_Particular' :
				$cadenaSql = "SELECT \"CON_TIPO_CONTRATO\" tipo_contrato  ";
				$cadenaSql .= "FROM arka_parametros.arka_contratistas   ";
				$cadenaSql .= "WHERE \"CON_IDENTIFICACION\"='" . $variable . "' ";
				$cadenaSql .= "AND \"CON_FECHA_INICIO\" <= '" . date ( 'Y-m-d' ) . "' ";
				$cadenaSql .= "AND \"CON_FECHA_FINAL\" >= '" . date ( 'Y-m-d' ) . "' ; ";
				
				break;
		}
		return $cadenaSql;
	}
}
?>
