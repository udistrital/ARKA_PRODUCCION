<?php

namespace arka\grupoContable;

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

include_once ("core/manager/Configurador.class.php");
include_once ("core/connection/Sql.class.php");

/**
 * IMPORTANTE: Se recomienda que no se borren registros. Utilizar mecanismos para - independiente del motor de bases de datos,
 * poder realizar rollbacks gestionados por el aplicativo.
 */
class Sql extends \Sql {

    var $miConfigurador;

    function getCadenaSql($tipo, $variable = '') {

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
            case 'insertarRegistro' :
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= $prefijo . 'pagina ';
                $cadenaSql .= '( ';
                $cadenaSql .= 'nombre,';
                $cadenaSql .= 'descripcion,';
                $cadenaSql .= 'modulo,';
                $cadenaSql .= 'nivel,';
                $cadenaSql .= 'parametro';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $_REQUEST ['nombrePagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['descripcionPagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['moduloPagina'] . '\', ';
                $cadenaSql .= $_REQUEST ['nivelPagina'] . ', ';
                $cadenaSql .= '\'' . $_REQUEST ['parametroPagina'] . '\'';
                $cadenaSql .= ') ';
                break;

            case 'actualizarRegistro' :
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= $prefijo . 'pagina ';
                $cadenaSql .= '( ';
                $cadenaSql .= 'nombre,';
                $cadenaSql .= 'descripcion,';
                $cadenaSql .= 'modulo,';
                $cadenaSql .= 'nivel,';
                $cadenaSql .= 'parametro';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $_REQUEST ['nombrePagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['descripcionPagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['moduloPagina'] . '\', ';
                $cadenaSql .= $_REQUEST ['nivelPagina'] . ', ';
                $cadenaSql .= '\'' . $_REQUEST ['parametroPagina'] . '\'';
                $cadenaSql .= ') ';
                break;

            case 'buscarRegistro' :

                $cadenaSql = 'SELECT ';
                $cadenaSql .= 'id_pagina as PAGINA, ';
                $cadenaSql .= 'nombre as NOMBRE, ';
                $cadenaSql .= 'descripcion as DESCRIPCION,';
                $cadenaSql .= 'modulo as MODULO,';
                $cadenaSql .= 'nivel as NIVEL,';
                $cadenaSql .= 'parametro as PARAMETRO ';
                $cadenaSql .= 'FROM ';
                $cadenaSql .= $prefijo . 'pagina ';
                $cadenaSql .= 'WHERE ';
                $cadenaSql .= 'nombre=\'' . $_REQUEST ['nombrePagina'] . '\' ';
                break;

            case 'borrarRegistro' :
                $cadenaSql = 'INSERT INTO ';
                $cadenaSql .= $prefijo . 'pagina ';
                $cadenaSql .= '( ';
                $cadenaSql .= 'nombre,';
                $cadenaSql .= 'descripcion,';
                $cadenaSql .= 'modulo,';
                $cadenaSql .= 'nivel,';
                $cadenaSql .= 'parametro';
                $cadenaSql .= ') ';
                $cadenaSql .= 'VALUES ';
                $cadenaSql .= '( ';
                $cadenaSql .= '\'' . $_REQUEST ['nombrePagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['descripcionPagina'] . '\', ';
                $cadenaSql .= '\'' . $_REQUEST ['moduloPagina'] . '\', ';
                $cadenaSql .= $_REQUEST ['nivelPagina'] . ', ';
                $cadenaSql .= '\'' . $_REQUEST ['parametroPagina'] . '\'';
                $cadenaSql .= ') ';
                break;

            //**** Consultas Específicas del Caso de Uso *****//

            case 'listarCatalogos':
                $cadenaSql = " SELECT lista_id, lista_nombre, lista_fecha_creacion, ";
                $cadenaSql .= "CASE WHEN lista_activo=0 ";
                $cadenaSql .= "THEN 'INACTIVO' ELSE 'ACTIVO' END ";
                $cadenaSql .= "FROM grupo.catalogo_lista  ";
                $cadenaSql .= "ORDER BY 3 DESC  ";
                break;

            case 'crearCatalogo':
                $cadenaSql = 'INSERT INTO grupo.catalogo_lista( lista_nombre) VALUES (';
                $cadenaSql .= "'" . $variable . "')";
                break;

            case "buscarCatalogo":
                $cadenaSql = " SELECT lista_id , lista_nombre , lista_fecha_creacion ";
                $cadenaSql .= " FROM grupo.catalogo_lista ";
                $cadenaSql .= " WHERE lista_nombre = '" . $variable . "' ";
                break;

            case "buscarCatalogoId":
                $cadenaSql = " SELECT lista_id , lista_nombre , lista_fecha_creacion ";
                $cadenaSql .= " FROM grupo.catalogo_lista ";
                $cadenaSql .= " WHERE lista_id = '" . $variable . "' ";
                break;

            case "eliminarCatalogo":
//                $cadenaSql = "DELETE FROM grupo.catalogo_lista WHERE lista_id ='" . $variable . "' ";
//                break;

            case "eliminarCatalogo":
                $cadenaSql = "UPDATE grupo.catalogo_lista SET lista_activo=1 WHERE lista_id='" . $variable . "';"
                        . " update  grupo.catalogo_lista SET lista_activo=0 WHERE lista_id!='" . $variable . "';";
                break;

            case "buscarUltimoIdCatalogo":
                $cadenaSql = "select max(lista_id) from grupo.catalogo_lista";
                break;

            case "cambiarNombreCatalogo":
                $cadenaSql = " UPDATE grupo.catalogo_lista ";
                $cadenaSql .= " SET lista_nombre='" . $variable[0] . "' ";
                $cadenaSql .= " WHERE lista_id='" . $variable[1] . "' ";
                break;


            case "listarElementos":
                $cadenaSql = "SELECT elemento_id, elemento_padre, elemento_codigo, elemento_catalogo, ";
                $cadenaSql.= " elemento_nombre, elemento_fecha_creacion, ";
                $cadenaSql.= " grupo_cuentasalida, grupo_cuentaentrada, cast(grupo_depreciacion as integer) as grupo_depreciacion, grupo_vidautil, grupo_dcuentadebito, grupo_dcuentacredito ";
                $cadenaSql.= " FROM grupo.catalogo_elemento ";
                $cadenaSql.= " JOIN grupo.grupo_descripcion ON cast(elemento_id as character varying)=grupo_id";
                $cadenaSql.= " WHERE elemento_id >0 ";
                break;

            case "listarElementosID":
                $cadenaSql = "SELECT elemento_id, elemento_padre, elemento_codigo, elemento_catalogo, ";
                $cadenaSql .= " elemento_nombre, elemento_fecha_creacion, ";
                $cadenaSql .= " grupo_cuentasalida, grupo_cuentaentrada, cast(grupo_depreciacion as integer) as grupo_depreciacion, grupo_vidautil, grupo_dcuentadebito, grupo_dcuentacredito ";
                $cadenaSql .= " FROM grupo.catalogo_elemento ";
                $cadenaSql .= " JOIN grupo.grupo_descripcion ON cast(elemento_id as character varying)=grupo_id";
                $cadenaSql .= " WHERE elemento_catalogo='" . $variable . "' ";
                $cadenaSql.= " AND elemento_id >0 ";
                break;



            case "crearElementoCatalogo":
                $cadenaSql = " INSERT INTO grupo.catalogo_elemento( ";
                $cadenaSql .= " elemento_padre, elemento_codigo, elemento_catalogo, ";
                $cadenaSql .= " elemento_nombre, elemento_tipobien)   VALUES ( ";
                $cadenaSql .= " '" . $variable['idPadre'] . "', ";
                $cadenaSql .= " '" . $variable['id'] . "', ";
                $cadenaSql .= " '" . $variable['idCatalogo'] . "', ";
                $cadenaSql .= " '" . $variable['nombreElemento'] . "', ";
                $cadenaSql .= " '" . $variable['tipoBien'] . "')";
                $cadenaSql .= " RETURNING elemento_id;";
                break;

            case "crearElementoCatalogoDescripcion":
                $cadenaSql = " INSERT INTO grupo.grupo_descripcion( ";
                $cadenaSql.= "  grupo_id, ";
                $cadenaSql.= "  grupo_cuentasalida, ";
                $cadenaSql.= "  grupo_cuentaentrada, ";
                $cadenaSql.= "  grupo_depreciacion, ";
                $cadenaSql.= "  grupo_vidautil, ";
                $cadenaSql.= "  grupo_dcuentadebito, ";
                $cadenaSql.= "  grupo_dcuentacredito)  VALUES ( ";
                $cadenaSql.= " '" . $variable['idCuenta'] . "', ";
                $cadenaSql.= " '" . $variable['cuentasalida'] . "', ";
                $cadenaSql.= " '" . $variable['cuentaentrada'] . "', ";
                $cadenaSql.= " '" . $variable['depreciacion'] . "', ";
                $cadenaSql.= " '" . intval($variable['vidautil']) . "', ";
                $cadenaSql.= " '" . intval($variable['cuentacredito']) . "', ";
                $cadenaSql.= " '" . intval($variable['cuentadebito']) . "')";
                break;

            case "buscarElementoId":
                $cadenaSql = " select max(elemento_id) from grupo.catalogo_elemento ";
                break;

            case "buscarIdPadre":
                $cadenaSql = " SELECT elemento_id , elemento_padre , elemento_codigo, elemento_catalogo , elemento_nombre , elemento_fecha_creacion ";
                $cadenaSql .= " ,grupo_cuentasalida, grupo_cuentaentrada, cast(grupo_depreciacion as integer) as grupo_depreciacion, grupo_vidautil, grupo_dcuentadebito, grupo_dcuentacredito ";
                $cadenaSql .= " FROM grupo.catalogo_elemento ";
                $cadenaSql .= " JOIN grupo.grupo_descripcion ON cast(elemento_id as character varying)=grupo_id";
                $cadenaSql .= " WHERE elemento_codigo = " . $variable[0];
                $cadenaSql .= " AND elemento_catalogo =" . $variable[1] . " ";
                $cadenaSql .= " AND elemento_estado=1 ";
                $cadenaSql.= " AND elemento_id >0 ";
                break;

            case "buscarIdElemento":
                $cadenaSql = " SELECT elemento_id , elemento_padre , elemento_codigo, elemento_catalogo , elemento_nombre , elemento_fecha_creacion ";
                $cadenaSql .= " ,grupo_cuentasalida, grupo_cuentaentrada, cast(grupo_depreciacion as integer) as grupo_depreciacion, grupo_vidautil, grupo_dcuentadebito, grupo_dcuentacredito ";
                $cadenaSql .= " FROM grupo.catalogo_elemento ";
                $cadenaSql .= " JOIN grupo.grupo_descripcion ON cast(elemento_id as character varying)=grupo_id";
                $cadenaSql .= " WHERE elemento_codigo = " . $variable[0] . " ";
                $cadenaSql .= " AND elemento_padre = " . $variable[1] . " ";
                $cadenaSql .= " AND elemento_catalogo =" . $variable[2] . " ";
                $cadenaSql.= " AND elemento_id >0 ";
                break;

            case "buscarNombreElementoNivel":
                $cadenaSql = " SELECT elemento_id , elemento_padre , elemento_codigo, elemento_catalogo , elemento_nombre , elemento_fecha_creacion ";
                $cadenaSql .= " ,grupo_cuentasalida, grupo_cuentaentrada, cast(grupo_depreciacion as integer) as grupo_depreciacion, grupo_vidautil, grupo_dcuentadebito, grupo_dcuentacredito ";
                $cadenaSql .= " FROM grupo.catalogo_elemento ";
                $cadenaSql .= " JOIN grupo.grupo_descripcion ON cast(elemento_id as character varying)=grupo_id";
                $cadenaSql .= " WHERE  ";
                $cadenaSql .= " elemento_padre = '" . $variable[1] . "' ";
                $cadenaSql .= " AND elemento_catalogo ='" . $variable[2] . "' ";
                $cadenaSql .= " AND elemento_nombre ='" . $variable[3] . "' ";
                //$cadenaSql .= " AND elemento_estado=1 ";
                break;


            //--- Consultas para los elementos ----//
            case "elementosNivel":
                $cadenaSql = " SELECT grupo.catalogo_elemento.elemento_tipobien,coalesce(k.elemento_codigo,0) codigo_padre,grupo.catalogo_elemento.elemento_id , grupo.catalogo_elemento.elemento_padre , grupo.catalogo_elemento.elemento_codigo,  ";
                $cadenaSql .= " grupo.catalogo_elemento.elemento_catalogo, upper(grupo.catalogo_elemento.elemento_nombre) as elemento_nombre ,  ";
                $cadenaSql .= " grupo.catalogo_elemento.elemento_fecha_creacion,  ";
                $cadenaSql .= " coalesce(grupo_cuentasalida,'0') as grupo_cuentasalida,  ";
                $cadenaSql .= " coalesce(grupo_cuentaentrada,'0') as grupo_cuentaentrada, ";
                $cadenaSql .= " coalesce(cast(grupo_depreciacion as integer),0) as grupo_depreciacion,  ";
                $cadenaSql .= " coalesce(grupo_vidautil,0) as grupo_vidautil, ";
                $cadenaSql .= " coalesce(grupo_dcuentadebito,'0') as grupo_dcuentadebito, ";
                $cadenaSql .= " coalesce(grupo_dcuentacredito,'0') as grupo_dcuentacredito ";
                $cadenaSql .= " FROM grupo.catalogo_elemento  ";
                $cadenaSql .= " LEFT JOIN grupo.grupo_descripcion ON cast(elemento_id as character varying)=grupo_id  ";
                $cadenaSql .= " LEFT JOIN grupo.catalogo_elemento as k ON k.elemento_id=grupo.catalogo_elemento.elemento_padre ";
                $cadenaSql .= " WHERE grupo.catalogo_elemento.elemento_catalogo ='" . $variable[0] . "' AND grupo.catalogo_elemento.elemento_padre='" . $variable[1] . "' ";
                $cadenaSql .= " AND grupo.catalogo_elemento.elemento_id >0 AND grupo.catalogo_elemento.elemento_estado=1 ORDER BY grupo.catalogo_elemento.elemento_codigo ";
                break;
            
            case "eliminarElementoCatalogo":
                $cadenaSql = "UPDATE grupo.catalogo_elemento SET elemento_estado=0 WHERE elemento_id= '" . $variable . "' ";
                break;

            case "guardarEdicionElementoCatalogo":
                $cadenaSql = " UPDATE grupo.catalogo_elemento ";
                $cadenaSql .= " SET  elemento_padre='" . $variable['idPadre'] . "', ";
                $cadenaSql .= " elemento_codigo='" . $variable['id'] . "', ";
                //$cadenaSql .= " elemento_catalogo='" . $variable['idCatalogo'] . "', ";
                $cadenaSql .= " elemento_nombre='" . $variable['nombreElemento'] . "' ";
                $cadenaSql .= " WHERE elemento_id='" . $variable['idElemento'] . "' ";

                $cadenaSql .= " RETURNING elemento_id;";
                break;

            case "guardarEdicionElementoCatalogoDescripcion":
                $cadenaSql = " UPDATE grupo.grupo_descripcion ";
                $cadenaSql .= " SET  ";
                $cadenaSql.= "  grupo_cuentasalida='" . $variable['cuentasalida'] . "', ";
                $cadenaSql.= "  grupo_cuentaentrada='" . $variable['cuentaentrada'] . "', ";
                $cadenaSql.= "  grupo_depreciacion='" . $variable['depreciacion'] . "', ";
                $cadenaSql.= "  grupo_vidautil='" . intval($variable['vidautil']) . "', ";
                $cadenaSql.= "  grupo_dcuentadebito='" . intval($variable['cuentacredito']) . "', ";
                $cadenaSql.= "  grupo_dcuentacredito='" . intval($variable['cuentadebito']) . "' ";
                $cadenaSql .= " WHERE grupo_id='" . $variable['idCuenta'] . "' ";
                break;

            case "tipoBien" :
                $cadenaSql = "SELECT id_tipo_bienes, descripcion ";
                $cadenaSql .= "FROM arka_inventarios.tipo_bienes;";
                break;
        }
        return $cadenaSql;
    }

}

?>
