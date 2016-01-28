<?php

namespace inventarios\reportesGenerales\reporteSalidas;

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
            case "datosUsuario":
                $cadenaSql = " SELECT DISTINCT ";
                $cadenaSql.=" id_usuario, ";
                $cadenaSql.=" nombre ||' '||";
                $cadenaSql.=" apellido as nombre,";
                $cadenaSql.=" correo ,";
                $cadenaSql.=" imagen ,";
                $cadenaSql.=" estado ";
                $cadenaSql.=" FROM " . $prefijo . "usuario";
                $cadenaSql.=" WHERE id_usuario='" . $variable . "' ";
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
            case "buscar_salidas_00" :

                $cadenaSql = " SELECT DISTINCT sal.id_salida, sal.consecutivo||' - ('||sal.vigencia||')' || tipo_bien salidas ";
                $cadenaSql .= " FROM salida sal ";
                $cadenaSql .= " JOIN entrada ON sal.id_entrada=entrada.id_entrada ";
                $cadenaSql .= " JOIN elemento_individual ON entrada.id_entrada=elemento_individual.id_entrada ";
                $cadenaSql .= " WHERE 1=1 ";
                $cadenaSql .= " AND entrada.cierre_contable ='f' ";
                $cadenaSql .= " AND entrada.estado_entrada = 1  ";
                $cadenaSql .= " AND entrada.estado_registro='t' ";
                $cadenaSql .= " ORDER BY salidas ASC  ";
                break;

            case "buscar_salidas":
                $cadenaSql = "SELECT DISTINCT sal.id_salida as data, sal.consecutivo||' - ('||sal.vigencia||')' || '('||tipo_bienes.descripcion||')' as value";
                $cadenaSql .= " FROM arka_inventarios.salida sal ";
                $cadenaSql .= " JOIN arka_inventarios.entrada ON sal.id_entrada=entrada.id_entrada ";
                $cadenaSql .= " JOIN arka_inventarios.elemento ON entrada.id_entrada=elemento.id_entrada ";
                $cadenaSql .= " JOIN arka_inventarios.tipo_bienes ON tipo_bienes.id_tipo_bienes=tipo_bien ";
                $cadenaSql .= " WHERE 1=1 ";
                //$cadenaSql .= " AND entrada.cierre_contable ='f' ";
                //$cadenaSql .= " AND entrada.estado_entrada = 1  ";
                $cadenaSql .= " AND entrada.estado_registro='t' ";
                $cadenaSql .= " AND cast(sal.consecutivo as character varying) LIKE '" . $variable . "%' ORDER BY data ASC ";
                break;

            case "buscar_entradas":
                $cadenaSql = "SELECT DISTINCT entrada.id_entrada as data, entrada.consecutivo||' - ('||entrada.vigencia||')'  as value";
                $cadenaSql .= " FROM arka_inventarios.entrada ";
                $cadenaSql .= " WHERE 1=1 ";
                //$cadenaSql .= " AND entrada.cierre_contable ='f' ";
                //$cadenaSql .= " AND entrada.estado_entrada = 1  ";
                $cadenaSql .= " AND entrada.estado_registro='t' ";
                $cadenaSql .= " AND cast(entrada.consecutivo as character varying) LIKE '" . $variable . "%' ORDER BY data ASC ";
                break;

            case "funcionario_informacion" :

                $cadenaSql = "SELECT FUN_IDENTIFICACION,  FUN_NOMBRE ";
                $cadenaSql .= "FROM FUNCIONARIOS ";
                $cadenaSql .= "WHERE FUN_IDENTIFICACION='" . $variable . "' ";
                // $cadenaSql .= "AND FUN_ESTADO='A' ";

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

            case "consultar_funcionario" :

                $cadenaSql = "SELECT ";
                $cadenaSql .= "id_funcionario,(identificacion||' - '|| nombre) AS funcionario ";
                $cadenaSql .= "FROM funcionario;";

                break;

            case "buscar_entradas":
                $cadenaSql = " SELECT id_entrada valor, entrada.consecutivo||' - ('||entrada.vigencia||')' descripcion  ";
                $cadenaSql.= " FROM entrada  ";
                $cadenaSql.= " WHERE estado_registro=TRUE; ";
                break;

            case "buscar_vigencias" :
                $cadenaSql = " SELECT DISTINCT vigencia valor, vigencia descripcion ";
                $cadenaSql.= " FROM entrada  ";
                $cadenaSql.= " WHERE estado_registro=TRUE ORDER BY vigencia ASC; ";
                break;


//--------------- Consultas Reportes Específicos -----------------//


            case "consultarEntrada" :
                $cadenaSql = "SELECT DISTINCT entrada.id_entrada id_entrada, ";
                $cadenaSql.= "consecutivo,  ";
                $cadenaSql.= "entrada.fecha_registro, ";
                $cadenaSql.= "clase_entrada.descripcion as clase_entrada, ";
                $cadenaSql.= "vigencia, ";
                $cadenaSql.= "tipo_contrato.descripcion as tipo_contrato,  ";
                $cadenaSql.= "numero_contrato,  ";
                $cadenaSql.= "fecha_contrato,  ";
                $cadenaSql.= "proveedor,  ";
                $cadenaSql.= ' "PRO_RAZON_SOCIAL" nombre_proveedor, ';
                $cadenaSql.= "numero_factura,  ";
                $cadenaSql.= "fecha_factura,  ";
                $cadenaSql.= "observaciones,  ";
                $cadenaSql.= "estado_entrada.descripcion as estado_entrada ";
                $cadenaSql.= "FROM entrada ";
                $cadenaSql.= "JOIN clase_entrada ON entrada.clase_entrada=clase_entrada.id_clase ";
                $cadenaSql.= "LEFT JOIN tipo_contrato ON tipo_contrato.id_tipo=entrada.tipo_contrato ";
                $cadenaSql.= "JOIN estado_entrada ON estado_entrada.id_estado=entrada.estado_entrada ";
                $cadenaSql.= ' LEFT JOIN arka_parametros.arka_proveedor ON arka_parametros.arka_proveedor."PRO_NIT"=cast(proveedor as character varying) ';
                $cadenaSql.= " JOIN arka_inventarios.elemento ON elemento.id_entrada=entrada.id_entrada ";
                $cadenaSql.= "WHERE estado_registro='TRUE' ";
                $cadenaSql.= "AND 1=1 ";

                if ($variable ['numero_entrada'] != '') {
                    $cadenaSql .= " AND entrada.id_entrada = '" . $variable ['numero_entrada'] . "'";
                }

                if ($variable ['vigencia_entrada'] != '') {
                    $cadenaSql .= " AND entrada.vigencia = '" . $variable ['vigencia_entrada'] . "'";
                }

                if ($variable ['proveedor'] != '') {
                    $cadenaSql .= " AND entrada.proveedor = '" . $variable ['proveedor'] . "'";
                }

                if ($variable ['tipo_entrada'] != '') {
                    $cadenaSql .= " AND entrada.clase_entrada = '" . $variable ['tipo_entrada'] . "'";
                }

                if ($variable['fecha_inicio'] != '' && $variable ['fecha_final'] != '') {
                    $cadenaSql .= " AND entrada.fecha_registro BETWEEN CAST ( '" . $variable ['fecha_inicio'] . "' AS DATE) ";
                    $cadenaSql .= " AND  CAST ( '" . $variable ['fecha_final'] . "' AS DATE)  ";
                }
                break;



            case "consultarEntrada_pdf" :
                $cadenaSql = " SELECT id_entrada, ";
                $cadenaSql.= " consecutivo,  ";
                $cadenaSql.= " entrada.fecha_registro, ";
                $cadenaSql.= " clase_entrada.descripcion as clase_entrada, ";
                $cadenaSql.= " vigencia, ";
                $cadenaSql.= " tipo_contrato.descripcion as tipo_contrato,  ";
                $cadenaSql.= " numero_contrato,  ";
                $cadenaSql.= " fecha_contrato,  ";
                $cadenaSql.= " proveedor,  ";
                $cadenaSql.= ' "PRO_RAZON_SOCIAL" nombre_proveedor, ';
                $cadenaSql.= " numero_factura,  ";
                $cadenaSql.= " fecha_factura,  ";
                $cadenaSql.= " observaciones,  ";
                $cadenaSql.= " estado_entrada.descripcion as estado_entrada ";
                $cadenaSql.= " FROM entrada ";
                $cadenaSql.= " JOIN clase_entrada ON entrada.clase_entrada=clase_entrada.id_clase ";
                $cadenaSql.= " LEFT JOIN tipo_contrato ON tipo_contrato.id_tipo=entrada.tipo_contrato ";
                $cadenaSql.= " JOIN estado_entrada ON estado_entrada.id_estado=entrada.estado_entrada ";
                $cadenaSql.= ' LEFT JOIN arka_parametros.arka_proveedor ON arka_parametros.arka_proveedor."PRO_NIT"=cast(proveedor as character varying) ';
                $cadenaSql.= " WHERE estado_registro='TRUE' ";
                $cadenaSql.= " AND id_entrada='" . $variable . "' ";
                break;

            case "consultarSalida" :
                $cadenaSql = ' SELECT  salida.id_salida, ';
                $cadenaSql.= ' salida.consecutivo num_salida, ';
                $cadenaSql.= ' entrada.consecutivo num_entrada,  ';
                $cadenaSql.= ' salida.fecha_registro ,  ';
                $cadenaSql.= ' sedes."ESF_SEDE" sede,  ';
                $cadenaSql.= ' dependencias."ESF_DEP_ENCARGADA" dependencia,  ';
                $cadenaSql.= ' espacios."ESF_NOMBRE_ESPACIO" ubicacion,  ';
                $cadenaSql.= ' salida.funcionario,arka_parametros.arka_funcionarios."FUN_NOMBRE" nombre_funcionario,  ';
                $cadenaSql.= ' salida.observaciones,  ';
                $cadenaSql.= ' sum(cantidad_asignada) as numero_elementos,  ';
                $cadenaSql.= ' sum(total_iva_con) valor_salida  ';
                $cadenaSql.= ' FROM salida  ';
                $cadenaSql.= ' JOIN entrada ON salida.id_entrada=entrada.id_entrada ';
                $cadenaSql.= ' JOIN elemento_individual ON elemento_individual.id_salida=salida.id_salida  ';
                $cadenaSql.= ' JOIN elemento ON elemento_individual.id_elemento_gen=elemento.id_elemento  ';
                $cadenaSql.= ' JOIN arka_parametros.arka_funcionarios ON arka_parametros.arka_funcionarios."FUN_IDENTIFICACION"=salida.funcionario  ';
                $cadenaSql.= ' JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO"=salida.ubicacion  ';
                $cadenaSql.= ' JOIN arka_parametros.arka_sedes as sedes ON sedes."ESF_COD_SEDE"=espacios."ESF_COD_SEDE" ';
                $cadenaSql.= ' JOIN (SELECT DISTINCT "ESF_CODIGO_DEP", "ESF_DEP_ENCARGADA" ';
                $cadenaSql.= ' FROm arka_parametros.arka_dependencia ';
                $cadenaSql.= ' WHERE "ESF_ESTADO"= ';
                $cadenaSql.= " 'A') as dependencias ";
                $cadenaSql.= ' ON dependencias."ESF_CODIGO_DEP"=salida.dependencia ';
                $cadenaSql.= ' WHERE 1=1 AND entrada.id_entrada=salida.id_entrada ';
                $cadenaSql.= "AND entrada.estado_entrada <> 3 ";

                if ($variable ['dependencia'] != '') {
                    $cadenaSql .= ' AND dependencias."ESF_CODIGO_DEP" = ';
                    $cadenaSql .= " '" . $variable ['dependencia'] . "' ";
                }

                if ($variable ['sede'] != '') {
                    $cadenaSql .= ' AND sedes."ESF_ID_SEDE" = ';
                    $cadenaSql .= " '" . $variable ['sede'] . "' ";
                }

                if ($variable ['funcionario'] != '') {
                    $cadenaSql .= " AND salida.funcionario = '" . $variable ['funcionario'] . "'";
                }


                if ($variable ['numero_salida'] != '') {
                    $cadenaSql .= " AND salida.id_salida = '" . $variable ['numero_salida'] . "'";
                }

                if ($variable['fecha_inicio'] != '' && $variable ['fecha_final'] != '') {
                    $cadenaSql .= " AND salida.fecha_registro BETWEEN CAST ( '" . $variable ['fecha_inicio'] . "' AS DATE) ";
                    $cadenaSql .= " AND  CAST ( '" . $variable ['fecha_final'] . "' AS DATE)  ";
                }

                if ($variable ['numero_entrada'] != '') {
                    $cadenaSql .= " AND entrada.id_entrada = '" . $variable ['numero_entrada'] . "'";
                }

                $cadenaSql.= ' GROUP BY salida.id_salida, salida.fecha_registro, salida.dependencia, salida.sede, salida.funcionario,salida.observaciones,  ';
                $cadenaSql.= ' entrada.consecutivo,arka_parametros.arka_funcionarios."FUN_NOMBRE", sedes."ESF_SEDE", ';
                $cadenaSql.= ' dependencias."ESF_DEP_ENCARGADA", espacios."ESF_NOMBRE_ESPACIO"  ';
                break;

            case "consultarSalida_pdf":
                $cadenaSql = " SELECT salida.id_salida, ";
                $cadenaSql.= ' salida.consecutivo num_salida, ';
                $cadenaSql .= ' entrada.consecutivo num_entrada,  ';
                $cadenaSql .= ' salida.fecha_registro fecha_salida,  ';
                $cadenaSql .= ' entrada.fecha_registro fecha_entrada,  ';
                $cadenaSql .= ' entrada.numero_factura, ';
                $cadenaSql .= ' entrada.fecha_factura, ';
                $cadenaSql .= ' sedes."ESF_SEDE" sede,  ';
                $cadenaSql .= ' dependencias."ESF_DEP_ENCARGADA" dependencia,   ';
                $cadenaSql .= ' espacios."ESF_NOMBRE_ESPACIO" ubicacion,   ';
                $cadenaSql .= ' salida.funcionario,arka_parametros.arka_funcionarios."FUN_NOMBRE" nombre_funcionario,  ';
                $cadenaSql .= ' salida.observaciones,  ';
                $cadenaSql .= ' tipo_bienes.descripcion objeto_final, ';
                $cadenaSql .= ' proveedor, ';
                $cadenaSql .= ' "PRO_RAZON_SOCIAL" nombre_proveedor ';
                $cadenaSql .= ' FROM salida  ';
                $cadenaSql .= ' JOIN arka_inventarios.entrada ON salida.id_entrada=entrada.id_entrada  ';
                $cadenaSql .= ' JOIN arka_inventarios.salida_contable ON salida.id_salida=salida_contable.salida_general  ';
                $cadenaSql .= ' JOIN arka_inventarios.tipo_bienes ON tipo_bienes.id_tipo_bienes=salida_contable.tipo_bien  ';
                $cadenaSql .= ' JOIN arka_parametros.arka_funcionarios ON arka_parametros.arka_funcionarios."FUN_IDENTIFICACION"=salida.funcionario   ';
                $cadenaSql .= ' JOIN arka_parametros.arka_espaciosfisicos as espacios ON espacios."ESF_ID_ESPACIO"=salida.ubicacion  ';
                $cadenaSql .= ' LEFT JOIN arka_parametros.arka_proveedor ON "PRO_NIT" = cast(proveedor as character varying)  ';
                $cadenaSql .= ' JOIN arka_parametros.arka_sedes as sedes ON sedes."ESF_COD_SEDE"=espacios."ESF_COD_SEDE" ';
                $cadenaSql .= ' JOIN (SELECT DISTINCT "ESF_CODIGO_DEP", "ESF_DEP_ENCARGADA" ';
                $cadenaSql .= ' FROM arka_parametros.arka_dependencia ';
                $cadenaSql .= ' WHERE "ESF_ESTADO"=';
                $cadenaSql .= " 'A') as dependencias ";
                $cadenaSql .= ' ON dependencias."ESF_CODIGO_DEP"=salida.dependencia  ';
                $cadenaSql .= ' WHERE 1=1  ';
                $cadenaSql .= " AND salida.id_salida= '" . $variable . "' ";
                break;

            case "consultarElementos_pdf":
                $cadenaSql = "SELECT grupo_cuentasalida, unidad, cantidad, elemento.descripcion, valor,  ";
                $cadenaSql .= " subtotal_sin_iva, aplicacion_iva.iva, total_iva, total_iva_con ,  tipo_bienes.descripcion as tipo_bien, placa ";
                $cadenaSql .= " FROM arka_inventarios.elemento  ";
                $cadenaSql .= " JOIN arka_inventarios.elemento_individual On elemento_individual.id_elemento_gen=elemento.id_elemento ";
                $cadenaSql .= " JOIN arka_inventarios.salida ON elemento_individual.id_salida=salida.id_salida  ";
                $cadenaSql .= " JOIN arka_inventarios.aplicacion_iva ON arka_inventarios.aplicacion_iva.id_iva=elemento.iva  ";
                $cadenaSql .= " JOIN arka_inventarios.tipo_bienes ON tipo_bienes.id_tipo_bienes=elemento.tipo_bien  ";
                $cadenaSql .= " JOIN catalogo.catalogo_elemento ON catalogo.catalogo_elemento.elemento_id=nivel  ";
                $cadenaSql .= " JOIN grupo.grupo_descripcion ON catalogo.catalogo_elemento.elemento_grupoc=CAST(grupo.grupo_descripcion.grupo_id as character varying)  ";
                $cadenaSql .= " WHERE elemento.estado='1'  ";
                $cadenaSql .= " AND salida.id_salida='" . $variable . "' ";
                $cadenaSql .= " ORDER BY grupo_cuentasalida ASC  ";
                break;

            case "consultarJefe":
                $cadenaSql = 'SELECT "FUN_IDENTIFICACION", "FUN_NOMBRE" ';
                $cadenaSql .= " FROM arka_parametros.arka_funcionarios ";
                $cadenaSql .= " WHERE 1=1 ";
                $cadenaSql .= ' AND "FUN_ESTADO" = ';
                $cadenaSql .= "'A'  ";
                $cadenaSql .= ' AND "FUN_CARGO" ';
                $cadenaSql .= " ='JEFE DE SECCION' ";
                $cadenaSql .= ' AND "FUN_DEP_COD_ACADEMICA" = 60;';
                break;
        }
        return $cadenaSql;
    }

}
?>


