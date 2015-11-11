<?php

namespace inventarios\gestionElementos\detalleElemento;

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

// Para evitar redefiniciones de clases el nombre de la clase del archivo sqle debe corresponder al nombre del bloque
// en camel case precedida por la palabra sql
class Sql extends \Sql {

    var $miConfigurador;

    function __construct() {
        $this->miConfigurador = \Configurador::singleton();
    }

    function getCadenaSql($tipo, $variable = "") {

        /**
         * 1.
         * Revisar las variables para evitar SQL Injection
         */
        $prefijo = $this->miConfigurador->getVariableConfiguracion("prefijo");
        $idSesion = $this->miConfigurador->getVariableConfiguracion("id_sesion");

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
                $cadenaSql .= "'" . time() . "' ";
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

                foreach ($_REQUEST as $clave => $valor) {
                    $cadenaSql .= "( ";
                    $cadenaSql .= "'" . $idSesion . "', ";
                    $cadenaSql .= "'" . $variable ['formulario'] . "', ";
                    $cadenaSql .= "'" . $clave . "', ";
                    $cadenaSql .= "'" . $valor . "', ";
                    $cadenaSql .= "'" . $variable ['fecha'] . "' ";
                    $cadenaSql .= "),";
                }

                $cadenaSql = substr($cadenaSql, 0, (strlen($cadenaSql) - 1));
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
            case "buscar_placa":
                $cadenaSql = " SELECT placa AS value, id_elemento_ind AS data FROM elemento_individual WHERE placa LIKE '%" . $variable . "%';";
                break;

            case "buscar_serie" :
                $cadenaSql = " SELECT DISTINCT serie AS value, serie AS data ";
                $cadenaSql .= "FROM elemento_individual ";
                $cadenaSql .= "WHERE  serie <> '' ";
                $cadenaSql .= "ORDER BY serie DESC ";
                break;

            case "consultarElemento" :
                $cadenaSql = "SELECT DISTINCT ";
                $cadenaSql .= " entrada.consecutivo, id_elemento_ind,";
                $cadenaSql .= " placa, ";
                $cadenaSql .= " elemento.descripcion, ";
                $cadenaSql .= ' sedes."ESF_SEDE" sede_nombre, ';
                $cadenaSql .= ' dependencias."ESF_DEP_ENCARGADA" dependencia_nombre, ';

                $cadenaSql .= ' funcionarios."FUN_NOMBRE" fun_nombre ';
                $cadenaSql .= ' FROM arka_inventarios.elemento   ';
                $cadenaSql .= ' JOIN arka_inventarios.entrada ON entrada.id_entrada = elemento.id_entrada  ';
                $cadenaSql .= ' JOIN arka_inventarios.elemento_individual ON elemento_individual.id_elemento_gen = elemento.id_elemento  ';
                $cadenaSql .= ' LEFT JOIN arka_parametros.arka_funcionarios funcionarios ON funcionarios."FUN_IDENTIFICACION" = elemento_individual.funcionario ';
                $cadenaSql .= ' left join arka_parametros.arka_espaciosfisicos ubicacion ON ubicacion."ESF_ID_ESPACIO"=elemento_individual.ubicacion_elemento ';
                $cadenaSql .= ' left JOIN arka_parametros.arka_dependencia dependencias ON dependencias."ESF_ID_ESPACIO"=ubicacion."ESF_ID_ESPACIO" ';
                $cadenaSql .= ' LEFT JOIN arka_parametros.arka_sedes as sedes ON sedes."ESF_COD_SEDE"=ubicacion."ESF_COD_SEDE"  ';
                $cadenaSql .= " WHERE 1=1 AND estado='TRUE' ";

                if ($variable ['dependencia'] != '') {
                    $cadenaSql .= ' AND dependencias."ESF_CODIGO_DEP" = ';
                    $cadenaSql .= " '" . $variable ['dependencia'] . "' ";
                }

                if ($variable ['sede'] != '') {
                    $cadenaSql .= ' AND sedes."ESF_ID_SEDE" = ';
                    $cadenaSql .= " '" . $variable ['sede'] . "' ";
                }

                if ($variable ['funcionario'] != '') {
                    $cadenaSql .= " AND elemento_individual.funcionario = '" . $variable ['funcionario'] . "'";
                }

                if ($variable ['entrada'] != '') {
                    $cadenaSql .= " AND entrada.id_entrada = '" . $variable ['entrada'] . "'";
                }

                if ($variable['fechainicial'] != '' && $variable ['fechafinal'] != '') {
                    $cadenaSql .= " AND entrada.fecha_registro BETWEEN CAST ( '" . $variable ['fechainicial'] . "' AS DATE) ";
                    $cadenaSql .= " AND  CAST ( '" . $variable ['fechafinal'] . "' AS DATE)  ";
                }

                if ($variable['elemento'] != '') {
                    $cadenaSql .= " AND elemento_individual.id_elemento_ind='" . $variable ['elemento'] . "' ";
                }

                break;

            case "consultarElementoParticular" :
                $cadenaSql = " SELECT DISTINCT ";
                $cadenaSql .= " placa, ";
                $cadenaSql .= " entrada.fecha_registro fecha_entrada, ";
                $cadenaSql .= " salida.fecha_registro fecha_salida, ";
                $cadenaSql .= " elemento_individual.serie, elemento.descripcion descripcion_elemento,marca, ";
                $cadenaSql .= " tipo_bienes.descripcion tipo_bien, ";
                $cadenaSql .= " catalogo.catalogo_elemento.elemento_nombre nivel,   ";
                $cadenaSql .= " valor, ";
                $cadenaSql .= " CASE WHEN grupo_depreciacion=false THEN 'No Aplica' ELSE 'Si aplica' END as depreciacion, ";
                $cadenaSql .= " CASE WHEN grupo_depreciacion=false THEN 0 ELSE grupo_vidautil  END as vida_util ";
//                $cadenaSql .= " elemento_individual.funcionario as funcionario_actual, ";
//                $cadenaSql .= 'arka_parametros.arka_funcionarios."FUN_NOMBRE" funcionario_actual_nombre, ';
//                $cadenaSql .= ' dependencia."ESF_DEP_ENCARGADA" dependencia_actual';
                $cadenaSql .= " FROM arka_inventarios.elemento   ";
                $cadenaSql .= " JOIN arka_inventarios.elemento_individual ON arka_inventarios.elemento_individual.id_elemento_gen=arka_inventarios.elemento.id_elemento ";
                $cadenaSql .= " LEFT JOIN salida ON salida.id_salida=elemento_individual.id_salida ";
                $cadenaSql .= " LEFT JOIN entrada ON salida.id_entrada=entrada.id_entrada ";
                $cadenaSql .= " LEFT JOIN tipo_bienes ON tipo_bienes.id_tipo_bienes=tipo_bien ";
                $cadenaSql .= " LEFT JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel ";
                $cadenaSql .= " LEFT JOIN grupo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_grupoc=cast(grupo.catalogo_elemento.elemento_codigo as character varying) ";
                $cadenaSql .= " LEFT JOIN grupo.grupo_descripcion ON grupo.grupo_descripcion.grupo_id=cast(grupo.catalogo_elemento.elemento_id as character varying) ";
                $cadenaSql .= ' LEFT JOIN arka_parametros.arka_funcionarios ON arka_parametros.arka_funcionarios."FUN_IDENTIFICACION" = elemento_individual.funcionario ';
                $cadenaSql .= ' left join arka_parametros.arka_espaciosfisicos ubicacion ON ubicacion."ESF_ID_ESPACIO"=elemento_individual.ubicacion_elemento ';
                $cadenaSql .= ' left JOIN arka_parametros.arka_dependencia dependencia ON dependencia."ESF_ID_ESPACIO"=ubicacion."ESF_ID_ESPACIO" ';
                $cadenaSql .= " WHERE 1=1 ";
                $cadenaSql .= " AND elemento_individual.id_elemento_ind= '" . $variable . "'";
                break;

            case "consultarElementoParticular_dep" :
                $cadenaSql = " SELECT DISTINCT ";
                $cadenaSql .= " elemento_individual.funcionario as funcionario_Documento, ";
                $cadenaSql .= 'arka_parametros.arka_funcionarios."FUN_NOMBRE" funcionario_Nombre, ';
                $cadenaSql .= ' dependencia."ESF_DEP_ENCARGADA" dependencia';
                $cadenaSql .= " FROM arka_inventarios.elemento   ";
                $cadenaSql .= " JOIN arka_inventarios.elemento_individual ON arka_inventarios.elemento_individual.id_elemento_gen=arka_inventarios.elemento.id_elemento ";
                $cadenaSql .= " LEFT JOIN salida ON salida.id_salida=elemento_individual.id_salida ";
                $cadenaSql .= " LEFT JOIN entrada ON salida.id_entrada=entrada.id_entrada ";
                $cadenaSql .= " LEFT JOIN tipo_bienes ON tipo_bienes.id_tipo_bienes=tipo_bien ";
                $cadenaSql .= " LEFT JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel ";
                $cadenaSql .= " LEFT JOIN grupo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_grupoc=cast(grupo.catalogo_elemento.elemento_codigo as character varying) ";
                $cadenaSql .= " LEFT JOIN grupo.grupo_descripcion ON grupo.grupo_descripcion.grupo_id=cast(grupo.catalogo_elemento.elemento_id as character varying) ";
                $cadenaSql .= ' LEFT JOIN arka_parametros.arka_funcionarios ON arka_parametros.arka_funcionarios."FUN_IDENTIFICACION" = elemento_individual.funcionario ';
                $cadenaSql .= ' left join arka_parametros.arka_espaciosfisicos ubicacion ON ubicacion."ESF_ID_ESPACIO"=elemento_individual.ubicacion_elemento ';
                $cadenaSql .= ' left JOIN arka_parametros.arka_dependencia dependencia ON dependencia."ESF_ID_ESPACIO"=ubicacion."ESF_ID_ESPACIO" ';
                $cadenaSql .= " WHERE 1=1 ";
                $cadenaSql .= " AND elemento_individual.id_elemento_ind= '" . $variable . "'";
                break;

            case "consultar_tipo_bien" :

                $cadenaSql = "SELECT id_tipo_bienes, descripcion ";
                $cadenaSql .= "FROM arka_inventarios.tipo_bienes;
            ";


                break;

            case "consultar_placa" :

                $cadenaSql = "SELECT MAX( placa) ";
                $cadenaSql .= "FROM elemento ";
                $cadenaSql .= "WHERE tipo_bien = '1';
            ";

                break;

            case "consultar_placa_actulizada" :
                $cadenaSql = " SELECT placa";
                $cadenaSql .= " FROM elemento ";
                $cadenaSql .= " WHERE id_elemento = '" . $variable . "'";

                break;

            case "estado_elemento" :

                $cadenaSql = " UPDATE ";
                $cadenaSql .= " elemento";
                $cadenaSql .= " SET ";
                $cadenaSql .= " estado = 'FALSE' ";
                $cadenaSql .= " WHERE id_elemento = '" . $variable . "';
            ";
                break;

            case "consultar_traslados" :
                $cadenaSql = " SELECT DISTINCT historial_elemento_individual.fecha_registro,";
                $cadenaSql .= " placa,";
                $cadenaSql .= ' arka_parametros.arka_dependencia."ESF_DEP_ENCARGADA" dependencia, ';
                $cadenaSql .= ' pasado."FUN_NOMBRE" as funcionario_anterior, ';
                $cadenaSql .= ' actual."FUN_NOMBRE" as funcionario_siguiente, ';
                $cadenaSql .= " historial_elemento_individual.observaciones observaciones_traslados ";
                $cadenaSql .= " FROM arka_inventarios.elemento_individual";
                $cadenaSql .= " JOIN arka_inventarios.elemento ON id_elemento_gen = elemento.id_elemento";
                $cadenaSql .= " JOIN arka_inventarios.historial_elemento_individual ON historial_elemento_individual.elemento_individual = elemento_individual.id_elemento_ind ";
                $cadenaSql .= " JOIN arka_inventarios.salida ON salida.id_salida = elemento_individual.id_salida";
                $cadenaSql .= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel";
                $cadenaSql .= ' JOIN arka_parametros.arka_funcionarios actual ON actual."FUN_IDENTIFICACION" = elemento_individual.funcionario ';
                $cadenaSql .= ' JOIN arka_parametros.arka_funcionarios pasado ON cast(pasado."FUN_IDENTIFICACION" as character varying) = historial_elemento_individual.descripcion_funcionario ';
                $cadenaSql .= ' JOIN arka_parametros.arka_dependencia ON arka_parametros.arka_dependencia."ESF_CODIGO_DEP" = salida.dependencia ';
                $cadenaSql .= " JOIN catalogo.catalogo_lista ON catalogo.catalogo_elemento.elemento_catalogo=catalogo.catalogo_lista.lista_id";
                $cadenaSql .= " WHERE elemento_individual.id_elemento_ind='" . $variable . "' ";
                $cadenaSql .= " ORDER BY historial_elemento_individual.fecha_registro ASC; ";
                break;

            case "consultar_estado":
                $cadenaSql = " SELECT DISTINCT ";
                $cadenaSql .= " CASE WHEN id_faltante=0 THEN 'No registra faltante' ELSE 'Registra faltante' END faltante, ";
                $cadenaSql .= " CASE WHEN id_faltante=0 THEN '' ELSE cast(fecha_registro as character varying) END faltante_fecha, ";
                $cadenaSql .= " CASE WHEN id_sobrante=0 THEN 'No registra sobrante' ELSE 'Registra sobrante' END sobrante, ";
                $cadenaSql .= " CASE WHEN id_sobrante=0 THEN '' ELSE cast(fecha_registro as character varying) END sobrante_fecha, ";
                $cadenaSql .= " CASE WHEN id_hurto=0 THEN 'No registra hurto' ELSE 'Registra hurto' END hurto, ";
                $cadenaSql .= " CASE WHEN id_hurto=0 THEN '' ELSE cast(fecha_registro as character varying) END hurto_fecha ";
                $cadenaSql .= " FROM arka_inventarios.estado_elemento ";
                $cadenaSql .= " WHERE id_elemento_ind='" . $variable . "' ";
                break;

            case "consultar_baja":
                $cadenaSql = " SELECT DISTINCT ";
                $cadenaSql .= " CASE WHEN estado_registro=TRUE THEN 'Registra baja' ELSE 'No Registra baja' END baja, ";
                $cadenaSql .= " CASE WHEN estado_registro=TRUE THEN cast(fecha_registro as character varying) ELSE ''  END baja_fecha ";
                $cadenaSql .= " FROM arka_inventarios.baja_elemento ";
                $cadenaSql .= " WHERE estado_aprobacion=TRUE ";
                $cadenaSql .= " AND id_elemento_ind='" . $variable . "' ";
                break;

            case "consultar_levantamiento":
                $cadenaSql = " SELECT fecha_registro, CASE WHEN creador_observacion=0 THEN 'Funcionario' ELSE 'Almacén' END autor, observacion ";
                $cadenaSql.= ' FROM arka_movil.detalle_levantamiento ';
                $cadenaSql.= ' WHERE 1=1';
                //$cadenaSql.= ' AND creador_observacion=0 ';
                $cadenaSql.= ' AND estado_registro=TRUE ';
                $cadenaSql.= " AND id_elemento_individual='" . $variable . "' ";
                break;


            case "consultar_nivel_inventario" :
                $cadenaSql = "SELECT elemento_id, elemento_padre||''|| elemento_codigo||' - '||elemento_nombre ";
                $cadenaSql .= "FROM catalogo.catalogo_elemento ";
                $cadenaSql .= "WHERE elemento_catalogo = 1 ";
                $cadenaSql .= "ORDER BY elemento_id DESC; ";
                break;

            case "buscar_entradas":
                $cadenaSql = " SELECT id_entrada valor,consecutivo||' - ('||vigencia||')' descripcion  ";
                $cadenaSql.= " FROM entrada; ";
                break;

            case "dependencias" :
                $cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
                $cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
                $cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
                $cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
                $cadenaSql .= " WHERE ad.\"ESF_ESTADO\"='A'";
                break;

            case "dependenciasConsultadas" :
                $cadenaSql = "SELECT DISTINCT  \"ESF_CODIGO_DEP\" , \"ESF_DEP_ENCARGADA\" ";
                $cadenaSql .= " FROM arka_parametros.arka_dependencia ad ";
                $cadenaSql .= " JOIN  arka_parametros.arka_espaciosfisicos ef ON  ef.\"ESF_ID_ESPACIO\"=ad.\"ESF_ID_ESPACIO\" ";
                $cadenaSql .= " JOIN  arka_parametros.arka_sedes sa ON sa.\"ESF_COD_SEDE\"=ef.\"ESF_COD_SEDE\" ";
                $cadenaSql .= " WHERE sa.\"ESF_ID_SEDE\"='" . $variable . "' ";
                $cadenaSql .= " AND  ad.\"ESF_ESTADO\"='A'";
                break;

            case "ubicaciones" :
                $cadenaSql = "SELECT DISTINCT  ef.\"ESF_ID_ESPACIO\" , ef.\"ESF_NOMBRE_ESPACIO\" ";
                $cadenaSql .= " FROM arka_parametros.arka_espaciosfisicos ef  ";
                $cadenaSql .= " JOIN arka_parametros.arka_dependencia ad ON ad.\"ESF_ID_ESPACIO\"=ef.\"ESF_ID_ESPACIO\" ";
                $cadenaSql .= " WHERE  ef.\"ESF_ESTADO\"='A'";
                break;

            case "ubicacionesConsultadas" :
                $cadenaSql = "SELECT DISTINCT  ef.\"ESF_ID_ESPACIO\" , ef.\"ESF_NOMBRE_ESPACIO\" ";
                $cadenaSql .= " FROM arka_parametros.arka_espaciosfisicos ef  ";
                $cadenaSql .= " JOIN arka_parametros.arka_dependencia ad ON ad.\"ESF_ID_ESPACIO\"=ef.\"ESF_ID_ESPACIO\" ";
                $cadenaSql .= " WHERE ad.\"ESF_CODIGO_DEP\"='" . $variable . "' ";
                $cadenaSql .= " AND  ef.\"ESF_ESTADO\"='A'";
                break;

            case "sede" :
                $cadenaSql = "SELECT DISTINCT  \"ESF_ID_SEDE\", \"ESF_SEDE\" ";
                $cadenaSql .= " FROM arka_parametros.arka_sedes ";
                $cadenaSql .= " WHERE   \"ESF_ESTADO\"='A' ";
                $cadenaSql .= " AND    \"ESF_COD_SEDE\" >  0 ";
                break;

            case "funcionarios" :
                $cadenaSql = ' SELECT "FUN_IDENTIFICACION", "FUN_IDENTIFICACION" ';
                $cadenaSql.= " ||'-'|| ";
                $cadenaSql.= ' "FUN_NOMBRE" ';
                $cadenaSql.= ' FROM arka_parametros.arka_funcionarios ';
                $cadenaSql.= ' WHERE "FUN_ESTADO" ';
                $cadenaSql.= " ='A' ";
                //$cadenaSql .= "AND FUN_IDENTIFICACION='" . $variable . "' ";
                break;


            //-------------------- Cláusulas de todo lo de imágenes  ---------------/
            case "consultar_imagenperfil":
                $cadenaSql = " SELECT imagen ";
                $cadenaSql .= " FROM arka_movil.asignar_imagen ";
                $cadenaSql .= " WHERE estado_registro='TRUE' ";
                $cadenaSql .= " AND prioridad=1 ";
                $cadenaSql .= " AND id_elemento='" . $variable . "' ";
                break;

            case "consultar_fotos":
                $cadenaSql = " SELECT imagen, num_registro,id_elemento ";
                $cadenaSql .= " FROM arka_movil.asignar_imagen ";
                $cadenaSql .= " WHERE estado_registro='TRUE' ";
                $cadenaSql .= " AND id_elemento='" . $variable . "' ";
                break;

            case "eliminar_fotos":
                $cadenaSql = " DELETE ";
                $cadenaSql .= " FROM arka_movil.asignar_imagen ";
                $cadenaSql .= " WHERE num_registro='" . $variable . "' ";
                break;

            case "consultarElemento_foto":
                $cadenaSql = " SELECT imagen, num_registro ";
                $cadenaSql .= " FROM arka_movil.asignar_imagen ";
                $cadenaSql .= " WHERE estado_registro='TRUE' ";
                $cadenaSql .= " AND num_registro='" . $variable . "' ";
                break;

            case "consultarComentario_foto":
                $cadenaSql = " SELECT id_comentario, id_numfoto, usuario, comentario, fecha_registro ";
                $cadenaSql .= " FROM arka_movil.asignar_imagen_comentarios ";
                $cadenaSql .= " WHERE estado_registro='TRUE' ";
                $cadenaSql .= " AND id_num_foto='" . $variable . "' ORDER BY fecha_registro DESC LIMIT 10";
                break;

            case "consultarElemento_foto_antes":
                $cadenaSql = " SELECT num_registro ";
                $cadenaSql .= " FROM arka_movil.asignar_imagen ";
                $cadenaSql .= " WHERE estado_registro='TRUE' ";
                $cadenaSql .= " AND num_registro < '" . $variable . "' ";
                $cadenaSql .= " ORDER BY num_registro LIMIT 1 ";
                break;

            case "consultarElemento_foto_despues":
                $cadenaSql = " SELECT num_registro ";
                $cadenaSql .= " FROM arka_movil.asignar_imagen ";
                $cadenaSql .= " WHERE estado_registro='TRUE' ";
                $cadenaSql .= " AND num_registro > '" . $variable . "' ";
                $cadenaSql .= " ORDER BY num_registro LIMIT 1 ";
                break;

            case "guardar_foto":
                $cadenaSql = " INSERT INTO arka_movil.asignar_imagen( ";
                $cadenaSql.= " id_elemento, prioridad, imagen) ";
                $cadenaSql.= " VALUES ('" . $variable['id_elemento'] . "', '" . $variable['prioridad'] . "', '" . $variable['imagen'] . "'); ";
                break;
        }
        return $cadenaSql;
    }

}

?>
