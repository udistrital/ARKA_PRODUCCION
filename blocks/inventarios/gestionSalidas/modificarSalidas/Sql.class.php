<?php

namespace inventarios\gestionSalidas\modificarSalidas;

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
			case "buscar_entradas" :
				
				$cadenaSql = "SELECT DISTINCT entrada.id_entrada, entrada.consecutivo||' - ('||entrada.vigencia||')' entradas ";
				$cadenaSql .= "FROM entrada  ";
				$cadenaSql .= "JOIN elemento ON elemento.id_entrada = entrada.id_entrada ";
				$cadenaSql .= "JOIN elemento_individual ei ON ei.id_elemento_gen = elemento.id_elemento ";
				$cadenaSql .= "WHERE cierre_contable ='f' ";
				$cadenaSql .= "AND   estado_entrada = 1  ";
				$cadenaSql .= "AND entrada.estado_registro='t' ";
				$cadenaSql .= "AND ei.id_salida IS NOT NULL  ";
				$cadenaSql .= "ORDER BY entrada.id_entrada DESC ;";
				break;
			
			case "buscar_salidas" :
				
				$cadenaSql = "SELECT DISTINCT sal.id_salida, sal.consecutivo||' - ('||sal.vigencia||')' salidas ";
				$cadenaSql .= "FROM entrada  ";
				$cadenaSql .= "JOIN elemento ON elemento.id_entrada = entrada.id_entrada ";
				$cadenaSql .= "JOIN elemento_individual ei ON ei.id_elemento_gen = elemento.id_elemento ";
				$cadenaSql .= "JOIN salida sal ON sal.id_salida = ei.id_salida ";
				$cadenaSql .= "WHERE entrada.cierre_contable ='f' ";
				$cadenaSql .= "AND   entrada.estado_entrada = 1  ";
				$cadenaSql .= "AND entrada.estado_registro='t' ";
				$cadenaSql .= "AND ei.id_salida IS NOT NULL  ";
				
				break;
			
			case "funcionario_informacion" :
				
				$cadenaSql = "SELECT FUN_IDENTIFICACION,  FUN_NOMBRE ";
				$cadenaSql .= "FROM FUNCIONARIOS ";
				$cadenaSql .= "WHERE FUN_IDENTIFICACION='" . $variable . "' ";
				// $cadenaSql .= "AND FUN_ESTADO='A' ";
				
				break;
			
			case "funcionarios" :
				
				$cadenaSql = "SELECT \"FUN_IDENTIFICACION\", \"FUN_IDENTIFICACION\" ||' - '||  \"FUN_NOMBRE\" ";
				$cadenaSql .= "FROM  arka_parametros.arka_funcionarios ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				break;
			
			case "consultar_funcionario" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_funcionario,(identificacion||' - '|| nombre) AS funcionario ";
				$cadenaSql .= "FROM funcionario;";
				
				break;
			
			case "consultarSalida" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "entrada.vigencia,salida.id_salida,entrada.id_entrada,
						salida.fecha_registro,fn.\"FUN_IDENTIFICACION\" as identificacion ,
						  salida.consecutivo||' - ('||salida.vigencia||')' salidas,entrada.consecutivo||' - ('||entrada.vigencia||')' entradas, entrada.cierre_contable,
						    fn.\"FUN_NOMBRE\" as nombre_fun ";
				$cadenaSql .= "FROM salida ";
				$cadenaSql .= "JOIN entrada ON entrada.id_entrada = salida.id_entrada ";
				$cadenaSql .= "JOIN elemento_individual ON elemento_individual.id_salida = salida.id_salida ";
				$cadenaSql .= "JOIN arka_parametros.arka_funcionarios fn ON fn.\"FUN_IDENTIFICACION\" = salida.funcionario ";
				$cadenaSql .= "WHERE entrada.cierre_contable ='f' ";
				$cadenaSql .= "AND   entrada.estado_entrada = 1  ";
				$cadenaSql .= "AND   entrada.estado_registro='t' ";
				
				if ($variable [0] != '') {
					$cadenaSql .= " AND salida.vigencia = '" . $variable [0] . "'";
				}
				
				if ($variable [1] != '') {
					$cadenaSql .= " AND salida.id_entrada = '" . $variable [1] . "'";
				}
				
				if ($variable [2] != '') {
					$cadenaSql .= " AND salida.id_salida = '" . $variable [2] . "'";
				}
				
				if ($variable [3] != '') {
					$cadenaSql .= " AND salida.funcionario = '" . $variable [3] . "'";
				}
				
				if ($variable [4] != '') {
					$cadenaSql .= " AND salida.fecha_registro BETWEEN CAST ( '" . $variable [4] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable [5] . "' AS DATE)  ";
				}
				$cadenaSql .= "ORDER BY salida.id_salida DESC ";
				
				break;
			
			case "consultar_dependencia" :
				
				$cadenaSql = "SELECT id_dependencia, (id_dependencia||' - '|| nombre) AS Dependencia ";
				$cadenaSql .= "FROM dependencia ;";
				
				break;
			
			case "consultar_ubicacion" :
				
				$cadenaSql = "SELECT id_seccion,nombre  ";
				$cadenaSql .= "FROM seccion;";
				
				break;
			
			// case "consulta_elementos" :
			// $cadenaSql = "SELECT id_elemento, elemento_padre||''||elemento_codigo||' - '||elemento_nombre AS item, cantidad, descripcion ";
			// $cadenaSql .= "FROM elemento ";
			// $cadenaSql .= " JOIN grupo.catalogo_elemento ce ON ce.elemento_id = elemento.nivel ";
			// $cadenaSql .= "JOIN grupo.catalogo_lista cl ON cl.lista_id = ce.elemento_catalogo ";
			// $cadenaSql .= "JOIN entrada en ON en.id_entrada = elemento.id_entrada ";
			// $cadenaSql .= "WHERE elemento.id_entrada='" . $variable . "' ";
			// $cadenaSql .= "AND cl.lista_activo = 1 ";
			// $cadenaSql .= "AND en.cierre_contable ='f' ";
			// $cadenaSql .= "AND en.estado_entrada = 1 ";
			
			// break;
			
			case "consulta_elementos_informacion" :
				$cadenaSql = "SELECT el.* ";
				$cadenaSql .= "FROM elemento el ";
				$cadenaSql .= "JOIN catalogo_elemento ON id_catalogo = el.nivel ";
				$cadenaSql .= "WHERE el.id_entrada='" . $variable . "';";
				
				break;
			
			case "consulta_elementos_totales" :
				
				$cadenaSql = "SELECT id_items, item, cantidad, descripcion, id_salida ";
				$cadenaSql .= "FROM items_actarecibido ";
				$cadenaSql .= "WHERE id_acta='" . $variable . "' ";
				
				break;
			
			case "consulta_elementos_sin_actualizar" :
				
				$cadenaSql = "SELECT id_elemento, cantidad cantidad, descripcion ";
				$cadenaSql .= "FROM id_elemento ";
				$cadenaSql .= "JOIN  id_elemento_ind eil ON id_elemento_gen=id_elemento";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "' ";
				$cadenaSql .= "AND  id_salida=NULL;";
				
				break;
			
			case "consultarEntradaParticular" :
				
				$cadenaSql = "SELECT DISTINCT  ";
				$cadenaSql .= "fecha_registro, vigencia, clase_entrada, ";
				$cadenaSql .= "	tipo_contrato, numero_contrato, fecha_contrato, proveedor,  ";
				$cadenaSql .= "numero_factura, fecha_factura, observaciones, acta_recibido  ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "' ";
				$cadenaSql .= "AND   cierre_contable ='f' ";
				$cadenaSql .= "AND   estado_entrada = 1  ";
				
				break;
			
			case "consultarSalidaParticular" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= " dependencia, sede, funcionario, observaciones, vigencia, ubicacion  ";
				$cadenaSql .= "FROM salida ";
				$cadenaSql .= "WHERE id_salida='" . $variable . "';";
				
				break;
			
			case "consultarFuncionario" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= "nombre, identificacion ";
				$cadenaSql .= "FROM funcionario ";
				$cadenaSql .= "WHERE id_funcionario='" . $variable . "';";
				
				break;
			
			case "actualizar_funcionario" :
				
				$cadenaSql = " UPDATE funcionario ";
				$cadenaSql .= " SET nombre='" . $variable [0] . "', ";
				$cadenaSql .= "  identificacion='" . $variable [1] . "'  ";
				$cadenaSql .= "  WHERE id_funcionario='" . $variable [2] . "' ;";
				
				break;
			
			case "limpiarIndividuales" :
				
				$cadenaSql = " UPDATE elemento_individual ";
				$cadenaSql .= " SET id_salida=NULL, ";
				$cadenaSql .= "  ubicacion_elemento=NULL ";
				$cadenaSql .= "  WHERE id_elemento_ind='" . $variable . "' ;";
				
				break;
			
			case "ajustar_elementos_salida" :
				
				$cadenaSql = " UPDATE elemento_individual ";
				$cadenaSql .= " SET id_salida='" . $variable [0] . "', ";
				$cadenaSql .= " ubicacion_elemento='" . $variable [2] . "' ";
				$cadenaSql .= "  WHERE id_elemento_ind='" . $variable [1] . "' ;";
				
				break;
			
			case "consulta_elementos" :
				
				$cadenaSql = "SELECT id_elemento,elemento_padre||''||elemento_codigo||' - '||elemento_nombre AS item, 
						  descripcion ,elemento_individual.id_elemento_ind, cantidad_asignada,cantidad_por_asignar,  elemento.tipo_bien, placa , elemento_individual.id_salida ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "JOIN elemento  ON elemento.id_elemento = elemento_individual.id_elemento_gen ";
				$cadenaSql .= "JOIN catalogo.catalogo_elemento ce ON ce.elemento_id = elemento.nivel ";
				$cadenaSql .= "JOIN catalogo.catalogo_lista cl ON cl.lista_id = ce.elemento_catalogo  ";
				$cadenaSql .= "JOIN  entrada en ON en.id_entrada = elemento.id_entrada  ";
				$cadenaSql .= "JOIN  salida sal ON sal.id_salida = elemento_individual.id_salida  ";
				$cadenaSql .= "WHERE elemento.id_entrada='" . $variable [0] . "' ";
				$cadenaSql .= "AND elemento_individual.id_salida='" . $variable [1] . "' ";
				$cadenaSql .= "AND cl.lista_activo = 1  ";
				$cadenaSql .= "AND en.cierre_contable ='f' ";
				$cadenaSql .= "AND en.estado_entrada = 1  ";
				$cadenaSql .= "AND en.estado_registro='t' ";
				$cadenaSql .= "AND elemento_individual.id_salida IS NOT NULL   ";
				$cadenaSql .= "AND elemento_individual.estado_registro='t'  ";
				$cadenaSql .= "ORDER BY elemento_individual.id_elemento_ind ASC;  ";
				
				/*
				 * $cadenaSql = "SELECT id_elemento, elemento_padre||''||elemento_codigo||' - '||elemento_nombre AS item, cantidad, descripcion "; $cadenaSql .= "FROM elemento "; $cadenaSql .= " JOIN grupo.catalogo_elemento ce ON ce.elemento_id = elemento.nivel "; $cadenaSql .= "JOIN grupo.catalogo_lista cl ON cl.lista_id = ce.elemento_catalogo "; $cadenaSql .= "JOIN entrada en ON en.id_entrada = elemento.id_entrada "; $cadenaSql .= "JOIN elemento_individual ei ON ei.id_elemento_gen = elemento.id_elemento "; $cadenaSql .= "JOIN salida sal ON sal.id_salida = ei.id_salida "; $cadenaSql .= "WHERE elemento.id_entrada='" . $variable [0] . "' "; $cadenaSql .= "AND ei.id_salida='" . $variable [1] . "' "; $cadenaSql .= "AND cl.lista_activo = 1 "; $cadenaSql .= "AND en.cierre_contable ='f' "; $cadenaSql .= "AND en.estado_entrada = 1 "; $cadenaSql .= "AND ei.id_salida IS NOT NULL "; $cadenaSql .= "ORDER BY ei.id_elemento_ind ASC; ";
				 */
				break;
			
			case 'elementosIndividuales' :
				
				$cadenaSql = "SELECT ei.id_elemento_ind,ei.id_salida,tb.descripcion,el.tipo_bien, el.id_elemento ";
				$cadenaSql .= " FROM elemento_individual ei ";
				$cadenaSql .= " JOIN  elemento  el ON  el.id_elemento =ei.id_elemento_gen ";
				$cadenaSql .= " JOIN  tipo_bienes tb ON  tb.id_tipo_bienes =el.tipo_bien ";
				$cadenaSql .= " JOIN  salida_contable sc ON  sc.salida_general =ei.id_salida ";
				$cadenaSql .= " WHERE ei.id_salida ='" . $variable . "' ";
				$cadenaSql .= " ORDER BY ei.id_elemento_ind ASC  ";
				
				break;
			
			case "actualizar_salida" :
				
				$cadenaSql = " UPDATE salida ";
				$cadenaSql .= " SET dependencia='" . $variable [0] . "', ";
				$cadenaSql .= "  sede='" . $variable [1] . "' , ";
				$cadenaSql .= "  observaciones='" . $variable [2] . "',  ";
				$cadenaSql .= "  funcionario='" . $variable [4] . "',  ";
				$cadenaSql .= "  vigencia='" . $variable [5] . "',  ";
				$cadenaSql .= "  ubicacion='" . $variable [6] . "'  ";
				$cadenaSql .= "  WHERE id_salida='" . $variable [3] . "' ;";
				
				break;
			
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
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_CODIGO_DEP\"='" . $variable [0] . "' ";
				$cadenaSql .= " AND  sa.\"ESF_ID_SEDE\"='" . $variable [1] . "' ";
				$cadenaSql .= " AND  ef.\"ESF_ESTADO\"='A'";
				break;
			
			case "dependencias" :
				
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";
				
				break;
			
			case "ubicaciones" :
				$cadenaSql = "SELECT DISTINCT  ef.\"ESF_ID_ESPACIO\" , ef.\"ESF_NOMBRE_ESPACIO\" ";
				$cadenaSql .= " FROM arka_parametros.arka_espaciosfisicos ef  ";
				$cadenaSql .= " JOIN arka_parametros.arka_dependencia ad ON ad.\"ESF_ID_ESPACIO\"=ef.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " WHERE  ef.\"ESF_ESTADO\"='A'";
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
				$cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
				
				break;
			
			case "busqueda_elementos_individuales" :
				$cadenaSql = "SELECT id_elemento_ind  id ";
				$cadenaSql .= "FROM elemento_individual  ";
				$cadenaSql .= "WHERE id_elemento_gen ='" . $variable . "' ";
				$cadenaSql .= "AND  id_salida IS  NUll ";
				$cadenaSql .= "ORDER BY id ASC;";
				break;
			
			case "restaurar_elementos" :
				
				$cadenaSql = " UPDATE elemento_individual ";
				$cadenaSql .= " SET id_salida=NULL ";
				$cadenaSql .= "  WHERE id_elemento_gen='" . $variable . "';";
				
				break;
			
			case "insertar_salida_item" :
				$cadenaSql = "UPDATE items_actarecibido ";
				$cadenaSql .= "SET id_salida ='" . $variable [1] . "'  ";
				$cadenaSql .= "WHERE id_items='" . $variable [0] . "';";
				
				break;
			
			case "id_items" :
				$cadenaSql = "SELECT id_elemento ";
				$cadenaSql .= "FROM elemento  ";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "';";
				
				break;
			
			case "actualizar_entrada" :
				$cadenaSql = "UPDATE entrada ";
				$cadenaSql .= "SET id_salida ='TRUE'  ";
				$cadenaSql .= "WHERE id_entrada='" . $variable [1] . "';";
				
				break;
			
			case "actualizar_elementos_individuales" :
				$cadenaSql = "UPDATE elemento_individual ";
				$cadenaSql .= "SET id_salida='" . $variable [1] . "' ";
				$cadenaSql .= "WHERE id_elemento_ind ='" . $variable [0] . "';";
				
				break;
			
			// _________________________________________________________________________
			case "clase_entrada" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_clase, descripcion  ";
				$cadenaSql .= "FROM clase_entrada;";
				
				break;
			
			case "tipo_contrato" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_tipo, descripcion  ";
				$cadenaSql .= "FROM tipo_contrato;";
				
				break;
			
			case "proveedor" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_proveedor, razon_social ";
				$cadenaSql .= "FROM proveedor;";
				
				break;
			
			case "consultarEntradaParticular" :
				
				$cadenaSql = "SELECT DISTINCT ";
				$cadenaSql .= " vigencia, clase_entrada, tipo_entrada, ";
				$cadenaSql .= "	tipo_contrato, numero_contrato, fecha_contrato, proveedor, nit,  ";
				$cadenaSql .= "numero_factura, fecha_factura, observaciones, acta_recibido  ";
				$cadenaSql .= "FROM entrada ";
				$cadenaSql .= "WHERE id_entrada='" . $variable . "';";
				
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
			
			case "EliminarSalidasSinElementos" :
				$cadenaSql = " UPDATE recuperacion_entrada ";
				$cadenaSql .= " SET observaciones='" . $variable [2] . "' ";
				if ($variable [0] == 1) {
					
					$cadenaSql .= " , ruta_acta='" . $variable [3] . "' , ";
					$cadenaSql .= "  nombre_acta='" . $variable [4] . "'  ";
				}
				$cadenaSql .= "  WHERE id_recuperacion='" . $variable [1] . "' ";
				$cadenaSql .= "  RETURNING  id_recuperacion ";
				
				break;
			
			case "busqueda_elementos_individuales_cantidad_restante" :
				$cadenaSql = "SELECT id_elemento_ind  id ";
				$cadenaSql .= "FROM elemento_individual  ";
				$cadenaSql .= "WHERE id_elemento_gen ='" . $variable . "'";
				$cadenaSql .= "ORDER BY id ASC;";
				
				break;
			
			case "actualizarFuncionarios" :
				$cadenaSql = "UPDATE salida
									SET
									funcionario='51882328'
								WHERE funcionario='" . $variable . "';";
				
				break;
			
			case "LimpiarElementosIndividuales" :
				$cadenaSql = " UPDATE elemento_individual ";
				$cadenaSql .= " SET  id_salida=NULL, ";
				$cadenaSql .= "  ubicacion_elemento=NULL, ";
				$cadenaSql .= "  funcionario=NULL  ";
				$cadenaSql .= "  WHERE id_elemento_ind='" . $variable . "' ;";
				
				break;
			
			case "ActualizarElementosIndividuales" :
				$cadenaSql = " UPDATE elemento ";
				$cadenaSql .= " SET  cantidad_por_asignar='" . $variable [1] . "'  ";
				$cadenaSql .= "  WHERE id_elemento='" . $variable [0] . "' ;";
				
				break;
			
			case "actualizar_elementos" :
				$cadenaSql = " UPDATE elemento_individual ";
				$cadenaSql .= " SET  funcionario='" . $variable ['funcionario'] . "',  ";
				$cadenaSql .= "  ubicacion_elemento='" . $variable ['ubicacion'] . "'   ";
				$cadenaSql .= "  WHERE id_elemento_ind='" . $variable ['identificacion_elemento'] . "' ;";
				
				break;
			
			case "EliminarElementosIndividuales" :
				$cadenaSql = " DELETE FROM elemento_individual ";
				$cadenaSql .= "  WHERE id_elemento_ind='" . $variable . "' ;";
				
				break;
			
			case "Eliminar_Depreciacion" :
				$cadenaSql = " DELETE FROM detalle_depreciacion ";
				$cadenaSql .= "  WHERE id_elemento_ind='" . $variable . "' ;";
				
				break;
		}
		
		return $cadenaSql;
	}
}
?>
