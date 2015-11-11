<?php

namespace inventarios\gestionElementos\aprobarBajas;

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
			
			case "seleccionar_muebles" :
				
				$cadenaSql = "SELECT id_tipo_mueble, descripcion ";
				$cadenaSql .= "FROM tipo_mueble;  ";
				
				break;
			
			case "seleccionar_estado_baja" :
				
				$cadenaSql = " SELECT id_estado, descripcion ";
				$cadenaSql .= "FROM estado_baja  ";
				$cadenaSql .= " WHERE id_estado <=2;";
				
				break;
			
			case "seleccionar_estado_servible" :
				
				$cadenaSql = "SELECT id_estado, descripcion ";
				$cadenaSql .= "FROM estado_baja  ";
				$cadenaSql .= " WHERE id_estado >2;";
				
				break;
			
			case "max_id_baja" :
				
				$cadenaSql = "SELECT MAX(id_baja) ";
				$cadenaSql .= "FROM baja_elemento ";
				
				break;
			
			case "insertar_baja" :
				
				$cadenaSql = "INSERT INTO baja_elemento( ";
				$cadenaSql .= "dependencia_funcionario, estado_funcional, tramite, ";
				$cadenaSql .= "tipo_mueble, ruta_radicacion, nombre_radicacion, observaciones, id_elemento_ind,fecha_registro,sede,id_baja ) ";
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
				$cadenaSql .= "'" . $variable [10] . "') ";
				$cadenaSql .= "RETURNING  id_baja; ";
				
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
				$cadenaSql .= " FROM tipo_falt_sobr;";
				break;
			case "funcionarios" :
				
				$cadenaSql = "SELECT FUN_IDENTIFICACION, FUN_IDENTIFICACION ||' - '|| FUN_NOMBRE ";
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
			
			case "dependencia" :
				
				$cadenaSql = " SELECT JEF_IDENTIFICADOR,JEF_DEPENDENCIA_PERTENECIENTE ";
				$cadenaSql .= " FROM JEFES_DE_SECCION ";
				
				break;
			
			case "dependencia_dep" :
				$cadenaSql = " SELECT DEP_IDENTIFICADOR, DEP_IDENTIFICADOR ||' - ' ||DEP_DEPENDENCIA  ";
				$cadenaSql .= "FROM DEPENDENCIAS ";
				break;
			
			case "seleccion_funcionario" :
				
				$cadenaSql = "SELECT id_funcionario, identificacion ||'-'||nombre AS funcionario  ";
				$cadenaSql .= "FROM funcionario;";
				break;
			
			case "seleccion_info_elemento" :
				
				$cadenaSql = "SELECT ";
				$cadenaSql .= "id_elemento_ind, elemento_individual.placa, elemento_individual.serie,funcionario, id_elemento_gen, ";
				$cadenaSql .= "salida.consecutivo||' - ('||salida.vigencia||')' salidas ,tipo_bienes.descripcion ,salida.id_salida as salida ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "JOIN elemento ON elemento.id_elemento = elemento_individual.id_elemento_gen ";
				$cadenaSql .= "JOIN salida ON salida.id_salida = elemento_individual.id_salida ";
				$cadenaSql .= "JOIN tipo_bienes ON tipo_bienes.id_tipo_bienes = elemento.tipo_bien ";
				// $cadenaSql .= "JOIN funcionario ON funcionario.id_funcionario = salida.funcionario ";
				// $cadenaSql .= "left JOIN dependencia ON dependencia.id_dependencia = funcionario.dependencia ";
				$cadenaSql .= "WHERE 1=1 ";
				$cadenaSql .= "AND elemento.tipo_bien <> 1 ";
				$cadenaSql .= " AND  id_elemento_ind= '" . $variable . "'";
				$cadenaSql .= " ; ";
				
				break;
			
			case "consultarElemento" :
				
				$cadenaSql = ' SELECT baja_elemento.id_elemento_ind, elemento_individual.placa, elemento_individual.serie, elemento_individual.funcionario, ';
				$cadenaSql .= " id_elemento_gen, salida.consecutivo||' - ('||salida.vigencia||')' salidas, tipo_bienes.descripcion, ";
				$cadenaSql .= ' sedes."ESF_SEDE" sede, elemento.descripcion descripcion_elemento, ';
				$cadenaSql .= ' dependencias."ESF_DEP_ENCARGADA" dependencia, espacios."ESF_NOMBRE_ESPACIO" ubicacion, salida.id_salida as salida, ';
				$cadenaSql .= ' arka_parametros.arka_funcionarios."FUN_NOMBRE" as fun_nombre ';
				$cadenaSql .= ' FROM arka_inventarios.baja_elemento ';
				$cadenaSql .= ' JOIN arka_inventarios.elemento_individual ON elemento_individual.id_elemento_ind = baja_elemento.id_elemento_ind ';
				$cadenaSql .= ' JOIN arka_inventarios.elemento ON elemento.id_elemento = elemento_individual.id_elemento_gen ';
				$cadenaSql .= ' JOIN arka_inventarios.salida ON salida.id_salida = elemento_individual.id_salida ';
				$cadenaSql .= ' JOIN arka_inventarios.tipo_bienes ON tipo_bienes.id_tipo_bienes = elemento.tipo_bien ';
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_funcionarios ON arka_parametros.arka_funcionarios."FUN_IDENTIFICACION" = baja_elemento.funcionario_dependencia ';
				$cadenaSql .= ' JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id = nivel ';
				$cadenaSql .= ' JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO" = baja_elemento.ubicacion ';
				$cadenaSql .= ' JOIN arka_parametros.arka_dependencia as dependencias ON dependencias."ESF_ID_ESPACIO" = espacios."ESF_ID_ESPACIO" ';
				$cadenaSql .= ' JOIN arka_parametros.arka_sedes as sedes ON sedes."ESF_COD_SEDE" = espacios."ESF_COD_SEDE" ';
				$cadenaSql .= ' WHERE 1 = 1 AND elemento.tipo_bien <> 1 AND baja_elemento.id_elemento_ind IN (SELECT id_elemento_ind FROM baja_elemento WHERE estado_aprobacion = FALSE)';
				
				if ($variable ['fecha_inicio'] != '' && $variable ['fecha_final'] != '') {
					$cadenaSql .= " AND baja_elemento.fecha_registro BETWEEN CAST ( '" . $variable ['fecha_inicio'] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable ['fecha_final'] . "' AS DATE)  ";
				}
				
				if ($variable ['vigencia'] != '') {
					$cadenaSql .= " AND salida.vigencia= '" . $variable ['vigencia'] . "'";
				}
				
				if ($variable ['sede'] != '') {
					$cadenaSql .= ' AND sedes."ESF_ID_SEDE" = ';
					$cadenaSql .= " '" . $variable ['sede'] . "' ";
				}
				
				if ($variable ['dependencia'] != '') {
					$cadenaSql .= ' AND dependencias."ESF_CODIGO_DEP" = ';
					$cadenaSql .= " '" . $variable ['dependencia'] . "' ";
				}
				
				if ($variable ['ubicacion'] != '') {
					$cadenaSql .= ' AND espacios."ESF_ID_ESPACIO" = ';
					$cadenaSql .= " '" . $variable ['ubicacion'] . "' ";
				}
				break;
			
			case "consultarElemento_0" :
				
				$cadenaSql = " SELECT id_elemento_ind, elemento_individual.placa, elemento_individual.serie,elemento_individual.funcionario, ";
				$cadenaSql .= " id_elemento_gen, salida.consecutivo||'- ('||salida.vigencia||')' salidas ,tipo_bienes.descripcion , ";
				$cadenaSql .= ' sedes."ESF_SEDE" sede, ';
				$cadenaSql .= ' dependencias."ESF_DEP_ENCARGADA" dependencia, ';
				$cadenaSql .= ' espacios."ESF_NOMBRE_ESPACIO" ubicacion, salida.id_salida as salida, arka_parametros.arka_funcionarios."FUN_NOMBRE" as fun_nombre, elemento.descripcion descripcion_elemento, ';
				$cadenaSql .= " grupo.catalogo_elemento.elemento_nombre grupo_bien ";
				$cadenaSql .= " FROM elemento_individual  ";
				$cadenaSql .= " JOIN elemento ON elemento.id_elemento = elemento_individual.id_elemento_gen ";
				$cadenaSql .= " JOIN salida ON salida.id_salida = elemento_individual.id_salida JOIN tipo_bienes ON tipo_bienes.id_tipo_bienes = elemento.tipo_bien  ";
				$cadenaSql .= ' LEFT JOIN arka_parametros.arka_funcionarios ON arka_parametros.arka_funcionarios."FUN_IDENTIFICACION" = baja_elemento.funcionario ';
				$cadenaSql .= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel  ";
				$cadenaSql .= " JOIN grupo.grupo_descripcion ON grupo.grupo_descripcion.grupo_id=cast(nivel as character varying)  ";
				$cadenaSql .= " JOIN grupo.catalogo_elemento ON cast(grupo.catalogo_elemento.elemento_id as character varying)=grupo.grupo_descripcion.grupo_id   ";
				$cadenaSql .= ' JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO" = baja_elemento.ubicacion ';
				$cadenaSql .= ' JOIN arka_parametros.arka_dependencia as dependencias ON dependencias."ESF_ID_ESPACIO" = espacios."ESF_ID_ESPACIO" ';
				$cadenaSql .= ' JOIN arka_parametros.arka_sedes as sedes ON sedes."ESF_COD_SEDE" = espacios."ESF_COD_SEDE" ';
				$cadenaSql .= ' WHERE 1 = 1';
				// $cadenaSql .= "AND entrada.cierre_contable='f' ";
				// $cadenaSql .= "AND entrada.estado_entrada = 1 ";
				// $cadenaSql .= "AND entrada.estado_registro='t' ";
				
				$cadenaSql .= ' AND elemento.tipo_bien <> 1 AND id_elemento_ind IN (SELECT id_elemento_ind FROM baja_elemento WHERE estado_aprobacion = FALSE) ';
				
				if ($variable ['fecha_inicio'] != '' && $variable ['fecha_final'] != '') {
					$cadenaSql .= " AND baja_elemento.fecha_registro BETWEEN CAST ( '" . $variable ['fecha_inicio'] . "' AS DATE) ";
					$cadenaSql .= " AND  CAST ( '" . $variable ['fecha_final'] . "' AS DATE)  ";
				}
				
				if ($variable ['vigencia'] != '') {
					$cadenaSql .= " AND salida.vigencia= '" . $variable ['vigencia'] . "'";
				}
				
				if ($variable ['sede'] != '') {
					$cadenaSql .= ' AND sedes."ESF_ID_SEDE" = ';
					$cadenaSql .= " '" . $variable ['sede'] . "' ";
				}
				
				if ($variable ['dependencia'] != '') {
					$cadenaSql .= ' AND dependencias."ESF_CODIGO_DEP" = ';
					$cadenaSql .= " '" . $variable ['dependencia'] . "' ";
				}
				
				if ($variable ['ubicacion'] != '') {
					$cadenaSql .= ' AND espacios."ESF_ID_ESPACIO" = ';
					$cadenaSql .= " '" . $variable ['ubicacion'] . "' ";
				}
				
				$cadenaSql .= ' ORDER BY elemento_individual.id_elemento_ind DESC;
                ';
				break;
			
			case "seleccion_funcionario_anterior" :
				
				$cadenaSql = "SELECT id_elemento_ind,identificacion ||' - '||nombre AS funcionario ,id_funcionario ";
				$cadenaSql .= "FROM elemento_individual ";
				$cadenaSql .= "JOIN salida ON salida.id_salida = elemento_individual.id_salida ";
				$cadenaSql .= "JOIN funcionario  ON funcionario.id_funcionario = salida.funcionario ";
				$cadenaSql .= "WHERE  id_elemento_ind='" . $variable . "';";
				
				break;
			
			case "mostrarInfoDepreciar" :
				$cadenaSql = " SELECT DISTINCT   ";
				$cadenaSql .= " id_elemento_ind,  ";
				$cadenaSql .= " placa, ";
				$cadenaSql .= " elemento.descripcion, ";
				$cadenaSql .= " elemento_nombre grupo, ";
				$cadenaSql .= " grupo_vidautil,  ";
				$cadenaSql .= " elemento.valor, ";
				$cadenaSql .= " salida.fecha_registro  ";
				$cadenaSql .= " FROM elemento_individual  ";
				$cadenaSql .= " JOIN elemento ON elemento.id_elemento=elemento_individual.id_elemento_gen  ";
				$cadenaSql .= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel  ";
				$cadenaSql .= " JOIN salida ON salida.id_salida=elemento_individual.id_salida  ";
				$cadenaSql .= " JOIN catalogo.catalogo_lista ON catalogo.catalogo_lista.lista_id=elemento_catalogo   ";
				$cadenaSql .= " INNER JOIN grupo.grupo_descripcion ON grupo.grupo_descripcion.grupo_id=cast(elemento_grupoc as character varying)  ";
				$cadenaSql .= " WHERE catalogo.catalogo_elemento.elemento_id>0   ";
				$cadenaSql .= " AND catalogo.catalogo_lista.lista_activo=1  ";
				// $cadenaSql.= " AND grupo.grupo_descripcion.grupo_depreciacion='t' ";
				$cadenaSql .= " AND elemento.estado=TRUE   ";
				$cadenaSql .= " AND id_elemento_ind NOT IN (    ";
				$cadenaSql .= " SELECT dep_idelemento   ";
				$cadenaSql .= " FROM registro_depreciacion   ";
				$cadenaSql .= " )  ";
				$cadenaSql .= " AND id_elemento_ind NOT IN (    ";
				$cadenaSql .= " SELECT id_elemento_ind   ";
				$cadenaSql .= " FROM estado_elemento  ";
				$cadenaSql .= " )  ";
				$cadenaSql .= " AND id_elemento_ind='" . $variable . "'  ";
				
				break;
			
			case "mostrarInfoDepreciar_elemento" :
				$cadenaSql = " SELECT DISTINCT   ";
				$cadenaSql .= " id_elemento_ind,placa,descripcion,salida.consecutivo,  ";
				$cadenaSql .= " elemento_id grupo, ";
				$cadenaSql .= " elemento_codigo grupo_codigo, grupo_cuentasalida,";
				$cadenaSql .= " grupo_vidautil,  ";
				$cadenaSql .= " elemento.valor, ";
				$cadenaSql .= " salida.fecha_registro,ajuste_inflacionario,catalogo.catalogo_elemento.elemento_nombre   ";
				$cadenaSql .= " FROM elemento_individual  ";
				$cadenaSql .= " JOIN elemento ON elemento.id_elemento=elemento_individual.id_elemento_gen  ";
				$cadenaSql .= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel  ";
				$cadenaSql .= " JOIN salida ON salida.id_salida=elemento_individual.id_salida  ";
				$cadenaSql .= " JOIN catalogo.catalogo_lista ON catalogo.catalogo_lista.lista_id=elemento_catalogo   ";
				$cadenaSql .= " INNER JOIN grupo.grupo_descripcion ON grupo.grupo_descripcion.grupo_id=cast(elemento_grupoc as character varying)  ";
				$cadenaSql .= " WHERE catalogo.catalogo_elemento.elemento_id>0   ";
				$cadenaSql .= " AND catalogo.catalogo_lista.lista_activo=1  ";
				// $cadenaSql.= " AND grupo.grupo_descripcion.grupo_depreciacion='t' ";
				$cadenaSql .= " AND elemento.estado=TRUE   ";
				$cadenaSql .= " AND id_elemento_ind='" . $variable . "'";
				break;
			
			case "registrarDepreciacion" :
				$cadenaSql = " INSERT INTO registro_depreciacion( ";
				$cadenaSql .= " dep_idelemento, ";
				$cadenaSql .= " dep_grupocontable, ";
				$cadenaSql .= " dep_meses, ";
				$cadenaSql .= " dep_fechasalida, ";
				$cadenaSql .= " dep_fechacorte, ";
				$cadenaSql .= " dep_cantidad, ";
				$cadenaSql .= " dep_precio, ";
				$cadenaSql .= " dep_valorhistorico, ";
				$cadenaSql .= " dep_valorajustado, ";
				$cadenaSql .= " dep_cuota, ";
				$cadenaSql .= " dep_periodo, ";
				$cadenaSql .= " dep_depacumulada, ";
				$cadenaSql .= " dep_circular56, ";
				$cadenaSql .= " dep_cuotainflacion, ";
				$cadenaSql .= " dep_apicacumulada, ";
				$cadenaSql .= " dep_circulardeprecia, ";
				$cadenaSql .= " dep_libros, ";
				$cadenaSql .= " dep_estado, ";
				$cadenaSql .= " dep_registro) ";
				$cadenaSql .= " VALUES ( ";
				$cadenaSql .= "'" . $variable ['id_elemento'] . "', ";
				$cadenaSql .= "'" . $variable ['grupo_contable'] . "', ";
				$cadenaSql .= "'" . $variable ['meses_depreciar'] . "', ";
				$cadenaSql .= "'" . $variable ['fechaSalida'] . "', ";
				$cadenaSql .= "'" . $variable ['fechaCorte'] . "', ";
				$cadenaSql .= "'" . $variable ['cantidad'] . "', ";
				$cadenaSql .= "'" . $variable ['precio'] . "', ";
				$cadenaSql .= "'" . $variable ['valor_historico'] . "', ";
				$cadenaSql .= "'" . $variable ['valor_ajustado'] . "', ";
				$cadenaSql .= "'" . $variable ['cuota'] . "', ";
				$cadenaSql .= "'" . $variable ['periodos_fecha'] . "', ";
				$cadenaSql .= "'" . $variable ['depreciacion_acumulada'] . "', ";
				$cadenaSql .= "'" . $variable ['circular_56'] . "', ";
				$cadenaSql .= "'" . $variable ['cuota_inflacion'] . "', ";
				$cadenaSql .= "'" . $variable ['api_acumulada'] . "', ";
				$cadenaSql .= "'" . $variable ['circular_depreciacion'] . "', ";
				$cadenaSql .= "'" . $variable ['valor_libros'] . "', ";
				$cadenaSql .= "'" . $variable ['estado'] . "', ";
				$cadenaSql .= "'" . $variable ['fregistro'] . "') ";
				$cadenaSql .= " RETURNING dep_id; ";
				break;
			
			case "insertar_historico" :
				
				$cadenaSql = " INSERT INTO historial_elemento_individual( ";
				$cadenaSql .= "fecha_registro, elemento_individual, funcionario,descripcion_funcionario)  ";
				$cadenaSql .= " VALUES (";
				$cadenaSql .= "'" . $variable [0] . "',";
				$cadenaSql .= "'" . $variable [1] . "',";
				$cadenaSql .= "'" . $variable [2] . "',";
				$cadenaSql .= "'" . $variable [3] . "') ";
				$cadenaSql .= "RETURNING  id_evento; ";
				
				break;
			
			case "actualizar_salida" :
				
				$cadenaSql = " UPDATE salida ";
				$cadenaSql .= "SET funcionario='" . $variable [1] . "',";
				$cadenaSql .= " observaciones='" . $variable [2] . "' ";
				$cadenaSql .= " WHERE id_salida=(SELECT id_salida FROM elemento_individual WHERE id_elemento_ind='" . $variable [0] . "' ) ;";
				
				break;
			
			case "funcionario_informacion" :
				
				$cadenaSql = "SELECT FUN_IDENTIFICACION,  FUN_NOMBRE  ";
				$cadenaSql .= "FROM FUNCIONARIOS ";
				$cadenaSql .= "WHERE FUN_ESTADO='A' ";
				
				break;
			
			case "funcionario_informacion_consultada" :
				
				$cadenaSql = "SELECT FUN_IDENTIFICACION,  FUN_NOMBRE  ";
				$cadenaSql .= "FROM FUNCIONARIOS ";
				$cadenaSql .= "WHERE FUN_ESTADO='A' ";
				$cadenaSql .= "AND FUN_IDENTIFICACION='" . $variable . "' ";
				
				break;
			
			case "dependencias_encargada" :
				$cadenaSql = 'SELECT DISTINCT "ESF_CODIGO_DEP", "ESF_DEP_ENCARGADA" ';
				$cadenaSql .= " FROM arka_parametros.arka_dependencia as dependencia ";
				$cadenaSql .= ' JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO" = dependencia."ESF_ID_ESPACIO" ';
				$cadenaSql .= ' JOIN arka_parametros.arka_sedes as sedes ON sedes."ESF_COD_SEDE" = espacios."ESF_COD_SEDE" ';
				$cadenaSql .= ' WHERE 1 = 1 ';
				$cadenaSql .= ' AND "ESF_ID_SEDE" = ';
				$cadenaSql .= "'" . $variable . "'";
				$cadenaSql .= ' AND dependencia."ESF_ESTADO" = ';
				$cadenaSql .= "'A'";
				break;
			
			case "vigencia" :
				$cadenaSql = " SELECT DISTINCT vigencia, vigencia as nombrevigencia ";
				$cadenaSql .= "FROM salida; ";
				break;
			
			case "registroDocumento_Aprobacion" :
				$cadenaSql = " INSERT INTO aprobar_baja ( ";
				$cadenaSql .= "fecha_registro, ruta_acto, nombre_acto, estado_registro) ";
				$cadenaSql .= " VALUES ('" . $variable ['fecha'] . "', ";
				$cadenaSql .= " '" . $variable ['destino'] . "', ";
				$cadenaSql .= " '" . $variable ['nombre'] . "', '" . $variable ['estado'] . "') ";
				$cadenaSql .= " RETURNING id_aprobacion ;";
				break;
			
			case "eliminarAprobar" :
				$cadenaSql = " DELETE FROM aprobar_baja ";
				$cadenaSql .= " WHERE id_aprobacion='" . $variable . "'";
				break;
			
			case "actualizarAprobar" :
				$cadenaSql = " UPDATE baja_elemento ";
				$cadenaSql .= " SET estado_aprobacion='" . $variable ['estado'] . "', ";
				$cadenaSql .= "  id_aprobacion='" . $variable ['id_aprobacion'] . "', ";
				$cadenaSql .= "  id_depreciacion='" . $variable ['id_depreciacion'] . "' ";
				$cadenaSql .= " WHERE id_elemento_ind='" . $variable ['id_elemento'] . "' ";
				break;
		}
		return $cadenaSql;
	}
}

?>
