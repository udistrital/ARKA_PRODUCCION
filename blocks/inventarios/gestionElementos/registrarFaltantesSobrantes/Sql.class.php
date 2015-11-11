<?php

namespace inventarios\gestionElementos\registrarFaltantesSobrantes;

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
			
			case "ubicacionesConsultadas" :
				$cadenaSql = "SELECT DISTINCT  ef.\"ESF_ID_ESPACIO\" , ef.\"ESF_NOMBRE_ESPACIO\" ";
				$cadenaSql .= " FROM arka_parametros.arka_espaciosfisicos ef  ";
				$cadenaSql .= " JOIN arka_parametros.arka_dependencia ad ON ad.\"ESF_ID_ESPACIO\"=ef.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE ad.\"ESF_CODIGO_DEP\"='" . $variable [0] . "' ";
				$cadenaSql .= " AND  sa.\"ESF_ID_SEDE\"='" . $variable [1] . "' ";
				$cadenaSql .= " AND  ef.\"ESF_ESTADO\"='A'";
				
				break;
			
			case "dependenciasConsultadas" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
				$cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
				$cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " WHERE sa.\"ESF_ID_SEDE\"='" . $variable . "' ";
				$cadenaSql .= " AND  ad.\"ESF_ESTADO\"='A'";
				
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
				$cadenaSql .= " FROM arka_parametros.arka_sedes ";
				$cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
				$cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
				
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
			
			case "actualizacion_estado_elemento" :
				
				$cadenaSql = " UPDATE elemento_individual ";
				$cadenaSql .= "SET estado_elemento='" . $variable [1] . "' ";
				$cadenaSql .= " WHERE id_elemento_ind='" . $variable [0] . "';";
				
				break;
			
			case "max_estado_elemento" :
				
				$cadenaSql = "SELECT MAX(id_estado_elemento) ";
				$cadenaSql .= "FROM estado_elemento ";
				
				break;
			
			case "insertar_faltante_sobrante" :
				
				$cadenaSql = "INSERT INTO estado_elemento( ";
				$cadenaSql .= "id_elemento_ind,id_faltante, id_sobrante, id_hurto, observaciones,  ";
				$cadenaSql .= "ruta_denuncia, nombre_denuncia, fecha_denuncia, fecha_hurto, fecha_registro,tipo_faltsobr, id_estado_elemento) ";
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
				$cadenaSql .= "'" . $variable [11] . "') ";
				$cadenaSql .= "RETURNING  id_faltante,id_sobrante,id_hurto,id_estado_elemento; ";
				
				break;
			
			case "id_sobrante" :
				$cadenaSql = " SELECT MAX(id_sobrante) ";
				$cadenaSql .= " FROM estado_elemento;";
				break;
			
			case "id_hurto" :
				$cadenaSql = " SELECT MAX(id_hurto) ";
				$cadenaSql .= " FROM estado_elemento;";
				break;
			
			case "id_faltante" :
				$cadenaSql = " SELECT MAX(id_faltante) ";
				$cadenaSql .= " FROM estado_elemento;";
				break;
			case "tipo_faltante" :
				$cadenaSql = " SELECT id_tipo_falt_sobr, descripcion ";
				$cadenaSql .= " FROM tipo_falt_sobr ";
				$cadenaSql .= " WHERE  id_tipo_falt_sobr < 4";
				break;
			
			// case "funcionarios" :
			// $cadenaSql = "SELECT JEF_IDENTIFICADOR,JEF_INDENTIFICACION ||' - '|| JEF_NOMBRE ";
			// $cadenaSql .= "FROM JEFES_DE_SECCION ";
			// break;
			
			case "dependencia" :
				
				$cadenaSql = " SELECT JEF_IDENTIFICADOR,JEF_DEPENDENCIA_PERTENECIENTE ";
				$cadenaSql .= " FROM JEFES_DE_SECCION ";
				
				break;
			
			case "seleccion_funcionario" :
				
				$cadenaSql = "SELECT id_funcionario, identificacion ||'-'||nombre AS funcionario  ";
				$cadenaSql .= "FROM funcionario;";
				break;
			
			case "seleccion_info_elemento" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_elemento_ind, elemento_individual.placa, elemento_individual.serie,elemento_individual.funcionario, id_elemento_gen, ";
				$cadenaSql .= "salida.consecutivo||' - ('||salida.vigencia||')' salidas,tipo_bienes.descripcion ,salida.id_salida as salida, ";
				$cadenaSql .= ' "ESF_NOMBRE_ESPACIO" ubicacion,"ESF_DEP_ENCARGADA" dependencia  ';
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "JOIN elemento ON elemento.id_elemento = elemento_individual.id_elemento_gen ";
				$cadenaSql .= "JOIN salida ON salida.id_salida = elemento_individual.id_salida ";
				$cadenaSql .= " JOIN tipo_bienes ON tipo_bienes.id_tipo_bienes = elemento.tipo_bien  ";
				$cadenaSql .= ' JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO"=elemento_individual.ubicacion_elemento ';
				
				$cadenaSql .= " JOIN  arka_parametros.arka_sedes sedes ON sedes.\"ESF_COD_SEDE\"=espacios.\"ESF_COD_SEDE\" ";
				$cadenaSql .= " JOIN   arka_parametros.arka_dependencia dependencias ON dependencias.\"ESF_ID_ESPACIO\"=espacios.\"ESF_ID_ESPACIO\" ";
				
				// $cadenaSql .= "JOIN funcionario ON funcionario.id_funcionario = salida.funcionario ";
				// $cadenaSql .= "left JOIN dependencia ON dependencia.id_dependencia = funcionario.dependencia ";
				$cadenaSql .= "WHERE 1=1 ";
				$cadenaSql .= "AND elemento.tipo_bien <> 1 ";
				$cadenaSql .= " AND  id_elemento_ind= '" . $variable . "'";
				$cadenaSql .= " ; ";
				
				break;
			
			case "funcionarios" :
				$cadenaSql = "SELECT \"FUN_IDENTIFICACION\", \"FUN_IDENTIFICACION\" ||' - '||  \"FUN_NOMBRE\" ";
				$cadenaSql .= "FROM  arka_parametros.arka_funcionarios ";
				// $cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				
				break;
			
			case "funcionario_informacion_consultada" :
				
				$cadenaSql = "SELECT \"FUN_IDENTIFICACION\" itendificacion,  \"FUN_NOMBRE\" nombre  ";
				$cadenaSql .= "FROM  arka_parametros.arka_funcionarios ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				$cadenaSql .= "AND \"FUN_IDENTIFICACION\"='" . $variable . "' ";
				
				break;
			
			case "consultarElemento" :
				
				$cadenaSql = "SELECT ei.*, dep.\"ESF_DEP_ENCARGADA\" dependencia_encargada, eps.\"ESF_NOMBRE_ESPACIO\" ubicacion_especifica,
						tfs.descripcion elemento_estado, fn.\"FUN_NOMBRE\" nombre_funcionario,sa.consecutivo||' - ('||sa.vigencia||')' salidas, tb.descripcion bien_tipo,est.tipo_faltsobr id_tipo_estado_elemento,
						id_baja baja,sa.funcionario funcionario_encargado,el.descripcion descripcion_elemento    ";
				
				$cadenaSql .= "FROM arka_inventarios.elemento_individual ei ";
				$cadenaSql .= "LEFT JOIN arka_inventarios.estado_elemento est  ON est.id_elemento_ind=ei.id_elemento_ind ";
				$cadenaSql .= "LEFT JOIN arka_inventarios.baja_elemento bj  ON bj.id_elemento_ind=ei.id_elemento_ind ";
				$cadenaSql .= "JOIN arka_inventarios.elemento el  ON el.id_elemento=ei.id_elemento_gen ";
				$cadenaSql .= "JOIN arka_inventarios.salida  sa ON sa.id_salida=ei.id_salida ";
				$cadenaSql .= "JOIN arka_inventarios.tipo_bienes  tb ON tb.id_tipo_bienes=el.tipo_bien ";
				$cadenaSql .= "JOIN arka_parametros.arka_dependencia  dep ON dep.\"ESF_ID_ESPACIO\"=sa.ubicacion ";
				$cadenaSql .= "JOIN arka_parametros.arka_espaciosfisicos eps ON eps.\"ESF_ID_ESPACIO\"=sa.ubicacion ";
				$cadenaSql .= "LEFT JOIN arka_inventarios.tipo_falt_sobr tfs ON tfs.id_tipo_falt_sobr=est.tipo_faltsobr ";
				$cadenaSql .= "JOIN arka_parametros.arka_funcionarios  fn ON fn.\"FUN_IDENTIFICACION\"=sa.funcionario ";
				$cadenaSql .= " WHERE 1=1 AND el.tipo_bien <> 1 ";
				$cadenaSql .= " AND id_baja IS NULL  ";
				$cadenaSql .= " AND est.tipo_faltsobr IS NULL  ";
				
				if ($variable [0] != '') {
					$cadenaSql .= " AND sa.funcionario = '" . $variable [0] . "'";
				}
				if ($variable [1] != '') {
					$cadenaSql .= " AND  ei.serie= '" . $variable [1] . "'";
				}
				if ($variable [2] != '') {
					$cadenaSql .= " AND  ei.placa= '" . $variable [2] . "'";
				}
				if ($variable [3] != '') {
					$cadenaSql .= " AND  sa.dependencia= '" . $variable [3] . "'";
				}
				
				if ($variable [4] != '') {
					$cadenaSql .= " AND  sa.ubicacion= '" . $variable [4] . "'";
				}
				
				// $cadenaSql .= " LIMIT 1000 ";
				
				// $cadenaSql = "SELECT * FROM ((SELECT elemento_individual.id_elemento_ind, elemento_individual.placa,";
				// $cadenaSql .= " elemento_individual.serie,elemento_individual.funcionario, id_elemento_gen, salida.consecutivo||' - ('||salida.vigencia||')' salidas ,";
				// $cadenaSql .= ' tipo_bienes.descripcion ,"ESF_DEP_ENCARGADA" dependencia ,salida.id_salida as salida, ';
				// $cadenaSql .= " '' descripcion_estado , \"ESF_NOMBRE_ESPACIO\" ubicacion";
				// $cadenaSql .= " FROM elemento_individual ";
				// $cadenaSql .= " JOIN elemento ON elemento.id_elemento = elemento_individual.id_elemento_gen ";
				// $cadenaSql .= " JOIN salida ON salida.id_salida = elemento_individual.id_salida ";
				// $cadenaSql .= " JOIN tipo_bienes ON tipo_bienes.id_tipo_bienes = elemento.tipo_bien ";
				// // $cadenaSql .= ' JOIN arka_parametros.arka_espaciosfisicos ON arka_parametros.arka_espaciosfisicos."ESF_ID_ESPACIO"=dependencia ';
				
				// $cadenaSql .= ' JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO"=elemento_individual.ubicacion_elemento ';
				
				// $cadenaSql .= " JOIN arka_parametros.arka_sedes sedes ON sedes.\"ESF_COD_SEDE\"=espacios.\"ESF_COD_SEDE\" ";
				// $cadenaSql .= " JOIN arka_parametros.arka_dependencia dependencias ON dependencias.\"ESF_ID_ESPACIO\"=espacios.\"ESF_ID_ESPACIO\" ";
				
				// $cadenaSql .= " WHERE 1=1 AND elemento.tipo_bien <> 1 )";
				// $cadenaSql .= " UNION";
				// $cadenaSql .= " (SELECT elemento_individual.id_elemento_ind, elemento_individual.placa,";
				// $cadenaSql .= " elemento_individual.serie,elemento_individual.funcionario, id_elemento_gen, salida.consecutivo||' - ('||salida.vigencia||')' salidas ,";
				// $cadenaSql .= " tipo_bienes.descripcion ,dependencia ,salida.id_salida as salida, ";
				// $cadenaSql .= " tipo_falt_sobr.descripcion as descripcion_estado, ubicacion_elemento ubicacion ";
				// $cadenaSql .= " FROM elemento_individual ";
				// $cadenaSql .= " JOIN elemento ON elemento.id_elemento = elemento_individual.id_elemento_gen ";
				// $cadenaSql .= " JOIN salida ON salida.id_salida = elemento_individual.id_salida ";
				// $cadenaSql .= " JOIN tipo_bienes ON tipo_bienes.id_tipo_bienes = elemento.tipo_bien ";
				// $cadenaSql .= " JOIN estado_elemento on estado_elemento.id_elemento_ind=elemento_individual.id_elemento_ind";
				// $cadenaSql .= " JOIN tipo_falt_sobr ON tipo_falt_sobr.id_tipo_falt_sobr=tipo_faltsobr";
				// // $cadenaSql .= ' JOIN arka_parametros.arka_espaciosfisicos ON arka_parametros.arka_espaciosfisicos."ESF_ID_ESPACIO"=dependencia ';
				
				// $cadenaSql .= ' JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO"=elemento_individual.ubicacion_elemento ';
				
				// $cadenaSql .= " JOIN arka_parametros.arka_sedes sedes ON sedes.\"ESF_COD_SEDE\"=espacios.\"ESF_COD_SEDE\" ";
				// $cadenaSql .= " JOIN arka_parametros.arka_dependencia dependencias ON dependencias.\"ESF_ID_ESPACIO\"=espacios.\"ESF_ID_ESPACIO\" ";
				
				// $cadenaSql .= " WHERE 1=1 AND elemento.tipo_bien <> 1 ";
				// $cadenaSql .= " )) consulta ";
				// $cadenaSql .= " WHERE 1=1 ";
				
				// if ($variable [0] != '') {
				// $cadenaSql .= " AND funcionario = '" . $variable [0] . "'";
				// }
				// if ($variable [1] != '') {
				// $cadenaSql .= " AND serie= '" . $variable [1] . "'";
				// }
				// if ($variable [2] != '') {
				// $cadenaSql .= " AND placa= '" . $variable [2] . "'";
				// }
				// if ($variable [3] != '') {
				// $cadenaSql .= " AND dependencia= '" . $variable [3] . "'";
				// }
				
				// if ($variable [4] != '') {
				// $cadenaSql .= " AND ubicacion= '" . $variable [4] . "'";
				// }
				
				// $cadenaSql .= " LIMIT 1000 ";
				
				break;
			
			case "seleccion_funcionario_anterior" :
				
				$cadenaSql = "SELECT id_elemento_ind,identificacion ||'  -  '||nombre AS funcionario ,id_funcionario ";
				$cadenaSql .= "FROM arka_inventarios.elemento_individual ";
				$cadenaSql .= "JOIN arka_inventarios.salida ON salida.id_salida = elemento_individual.id_salida ";
				$cadenaSql .= "JOIN arka_inventarios.funcionario  ON funcionario.id_funcionario = salida.funcionario ";
				$cadenaSql .= "WHERE  id_elemento_ind='" . $variable . "';";
				
				break;
			
			case "insertar_historico" :
				
				$cadenaSql = " INSERT INTO arka_inventarios.historial_elemento_individual( ";
				$cadenaSql .= "fecha_registro, elemento_individual, funcionario,descripcion_funcionario)  ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "') ";
				$cadenaSql .= "RETURNING  id_evento; ";
				
				break;
			
			case "actualizar_salida" :
				
				$cadenaSql = " UPDATE arka_inventarios.salida ";
				$cadenaSql .= "SET funcionario='" . $variable [1] . "',";
				$cadenaSql .= " observaciones='" . $variable [2] . "' ";
				$cadenaSql .= " WHERE id_salida=(SELECT id_salida FROM elemento_individual WHERE id_elemento_ind='" . $variable [0] . "' ) ;";
				
				break;
			
			case "funcionarios" :
				$cadenaSql = "SELECT \"FUN_IDENTIFICACION\", \"FUN_IDENTIFICACION\" ||' - '||  \"FUN_NOMBRE\" ";
				$cadenaSql .= "FROM  arka_parametros.arka_funcionarios ";
				$cadenaSql .= "WHERE \"FUN_ESTADO\"='A' ";
				
				break;
			case "funcionario_informacion" :
				
				$cadenaSql = "SELECT FUN_IDENTIFICACION,  FUN_NOMBRE  ";
				$cadenaSql .= "FROM FUNCIONARIOS ";
				$cadenaSql .= "WHERE FUN_ESTADO='A' ";
				
				break;
			
			case "dependencias" :
				$cadenaSql = "SELECT DISTINCT  ESF_ID_ESPACIO, ESF_NOMBRE_ESPACIO ";
				$cadenaSql .= " FROM ESPACIOS_FISICOS ";
				$cadenaSql .= " WHERE ESF_ID_SEDE='" . $variable . "' ";
				$cadenaSql .= " AND  ESF_ESTADO='A'";
				
				break;
			
			case "sede" :
				$cadenaSql = "SELECT DISTINCT  ESF_ID_SEDE, ESF_SEDE ";
				$cadenaSql .= " FROM ESPACIOS_FISICOS ";
				$cadenaSql .= " WHERE   ESF_ESTADO='A'";
				break;
			
			case "buscar_serie" :
				$cadenaSql = " SELECT DISTINCT serie, serie as series ";
				$cadenaSql .= "FROM arka_inventarios.elemento_individual ";
				$cadenaSql .= "WHERE  serie IS NOT NULL ";
				$cadenaSql .= "ORDER BY serie DESC ";
				
				break;
			
			case "buscar_placa" :
				$cadenaSql = " SELECT DISTINCT placa, placa as placas ";
				$cadenaSql .= "FROM arka_inventarios.elemento_individual ";
				$cadenaSql .= "WHERE  placa IS NOT NULL  ";
				$cadenaSql .= "ORDER BY placa DESC ";
				
				break;
			
			case "ConsultasPlacas" :
				$cadenaSql = " SELECT DISTINCT placa AS value, placa as data ";
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
