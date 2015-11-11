<?php

namespace arka\catalogo;

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

//            ------------- QUERYS ESPECÍFICOS DEL CATÁLOGO ----------------------------------//
            case 'listarCatalogos':
                $cadenaSql = " SELECT lista_id, lista_nombre, lista_fecha_creacion, ";
                $cadenaSql .= "CASE WHEN lista_activo=0 ";
                $cadenaSql .= "THEN 'INACTIVO' ELSE 'ACTIVO' END ";
                $cadenaSql .= "FROM catalogo.catalogo_lista  ";
                $cadenaSql .= "ORDER BY 3 DESC  ";
                break;

            case 'crearCatalogo':
                $cadenaSql = 'INSERT INTO catalogo.catalogo_lista( lista_nombre) VALUES (';
                $cadenaSql .= "'" . $variable . "')";
                break;

            case "buscarCatalogo":
                $cadenaSql = " SELECT lista_id , lista_nombre , lista_fecha_creacion ";
                $cadenaSql .= " FROM catalogo.catalogo_lista ";
                $cadenaSql .= " WHERE lista_nombre = '" . $variable . "' ";
                break;

            case "buscarCatalogoId":
                $cadenaSql = " SELECT lista_id , lista_nombre , lista_fecha_creacion ";
                $cadenaSql .= " FROM catalogo.catalogo_lista ";
                $cadenaSql .= " WHERE lista_id = '" . $variable . "' ";
                break;

//            case "eliminarCatalogo":
//                $cadenaSql = "DELETE FROM catalogo.catalogo_lista WHERE lista_id ='" . $variable . "' ";
//                break;

            case "eliminarCatalogo":
                $cadenaSql = "UPDATE catalogo.catalogo_lista SET lista_activo=1 WHERE lista_id='" . $variable . "';"
                        . " update  catalogo.catalogo_lista SET lista_activo=0 WHERE lista_id!='" . $variable . "';";
                break;

            case "buscarUltimoIdCatalogo":
                $cadenaSql = "select max(lista_id) from catalogo.catalogo_lista";
                break;

            case "cambiarNombreCatalogo":
                $cadenaSql = " UPDATE catalogo.catalogo_lista ";
                $cadenaSql .= " SET lista_nombre='" . $variable[0] . "' ";
                $cadenaSql .= " WHERE lista_id='" . $variable[1] . "' ";
                break;

            //            ------------- QUERYS ESPECÍFICOS DE LOS ELEMENTOS ----------------------------------//

            case "listarElementos":
                $cadenaSql = "SELECT elemento_id, elemento_padre, elemento_codigo, elemento_catalogo, ";
                $cadenaSql .= " elemento_nombre, elemento_fecha_creacion, elemento_grupoc ";
                $cadenaSql .= " FROM catalogo.catalogo_elemento ";
                $cadenaSql .= " WHERE elemento_id >0 ";
                break;

            case "listarElementosID":
                $cadenaSql = "SELECT elemento_id, elemento_padre, elemento_codigo, elemento_catalogo, ";
                $cadenaSql .= " elemento_nombre, elemento_fecha_creacion, elemento_grupoc  ";
                $cadenaSql .= " FROM catalogo.catalogo_elemento ";
                $cadenaSql .= " WHERE elemento_catalogo='" . $variable . "' ";
                $cadenaSql .= " AND elemento_id >0 ";
                break;

            case "crearElementoCatalogo":
                $cadenaSql = " INSERT INTO catalogo.catalogo_elemento( ";
                $cadenaSql .= " elemento_padre, elemento_codigo, elemento_catalogo, ";
                $cadenaSql .= " elemento_nombre,elemento_grupoc)   VALUES ( ";
                $cadenaSql .= " '" . $variable['idPadre'] . "', ";
                $cadenaSql .= " '" . $variable['id'] . "', ";
                $cadenaSql .= " '" . $variable['idCatalogo'] . "', ";
                $cadenaSql .= " '" . $variable['nombreElemento'] . "', ";
                $cadenaSql .= " '" . $variable['idGrupo'] . "')";
                break;

            case "buscarElementoId":
                $cadenaSql = " select max(elemento_id) from catalogo.catalogo_elemento ";
                break;

            case "buscarIdPadre":
                $cadenaSql = " SELECT elemento_id , elemento_padre , elemento_codigo, elemento_catalogo , elemento_nombre , elemento_fecha_creacion, elemento_grupoc ";
                $cadenaSql.= " FROM catalogo.catalogo_elemento ";
                $cadenaSql.= " WHERE elemento_codigo = '" . $variable[0] . "' ";
                $cadenaSql.= " AND elemento_catalogo ='" . $variable[1] . "' ";
                $cadenaSql .= " AND elemento_id >0 ";
                break;

            case "buscarIdElemento":
                $cadenaSql = " SELECT elemento_id , elemento_padre , elemento_codigo, elemento_catalogo , elemento_nombre , elemento_fecha_creacion, elemento_grupoc ";
                $cadenaSql.= " FROM catalogo.catalogo_elemento ";
                $cadenaSql.= " WHERE elemento_codigo = '" . $variable[0] . "' ";
                $cadenaSql.= " AND elemento_padre = '" . $variable[1] . "' ";
                $cadenaSql.= " AND elemento_catalogo ='" . $variable[2] . "' ";
                $cadenaSql.= " AND elemento_id >0 ";
                break;

            case "buscarNombreElementoNivel":
                $cadenaSql = " SELECT elemento_id , elemento_padre , elemento_codigo, elemento_catalogo , elemento_nombre , elemento_fecha_creacion, elemento_grupoc  ";
                $cadenaSql .= " FROM catalogo.catalogo_elemento ";
                $cadenaSql .= " WHERE  ";
                $cadenaSql .= " elemento_padre = '" . $variable[1] . "' ";
                $cadenaSql .= " AND elemento_catalogo ='" . $variable[2] . "' ";
                $cadenaSql .= " AND elemento_nombre ='" . $variable[3] . "' ";
               // $cadenaSql .= " AND elemento_estado=1 ";
                break;

            case "elementosNivel":
                $cadenaSql = " SELECT coalesce(k.elemento_codigo,0) codigo_padre,catalogo.catalogo_elemento.elemento_id , catalogo.catalogo_elemento.elemento_padre , catalogo.catalogo_elemento.elemento_codigo, catalogo.catalogo_elemento.elemento_catalogo , upper(catalogo.catalogo_elemento.elemento_nombre) as elemento_nombre , catalogo.catalogo_elemento.elemento_fecha_creacion, catalogo.catalogo_elemento.elemento_grupoc  ";
                $cadenaSql .= " FROM catalogo.catalogo_elemento ";
                $cadenaSql .= " LEFT JOIN catalogo.catalogo_elemento as k ON k.elemento_id=catalogo.catalogo_elemento.elemento_padre ";
                $cadenaSql .= " WHERE catalogo.catalogo_elemento.elemento_catalogo ='" . $variable[0] . "' ";
                $cadenaSql .= " AND catalogo.catalogo_elemento.elemento_padre='" . $variable[1] . "'   AND catalogo.catalogo_elemento.elemento_id>0  ";
                $cadenaSql .= " AND catalogo.catalogo_elemento.elemento_estado=1 ORDER BY elemento_codigo ";
                break;

            case "eliminarElementoCatalogo":
                $cadenaSql = "UPDATE catalogo.catalogo_elemento SET elemento_estado=0 WHERE elemento_id= '" . $variable . "' ";
                break;

            case "guardarEdicionElementoCatalogo":
                $cadenaSql = " UPDATE catalogo.catalogo_elemento ";
                $cadenaSql .= " SET  elemento_padre='" . $variable['padre'] . "', ";
                $cadenaSql .= " elemento_codigo='" . $variable['idElemento'] . "', ";
                //$cadenaSql .= " elemento_catalogo='" . $variable['idCatalogo'] . "', ";
                $cadenaSql .= " elemento_nombre='" . $variable['nombreElemento'] . "', ";
                $cadenaSql .= " elemento_grupoc='" . $variable['idGrupo'] . "' ";
                $cadenaSql .= " WHERE elemento_id=" . $variable['idElementoEd'] . " ";
                break;

            // ------------------------------ CONSULTAS PARA ALIMENTAR LOS SELECT ------------------------//
            case "gruposcontables" :
                $cadenaSql = " SELECT elemento_id, elemento_id ||' - '|| elemento_nombre ";
                $cadenaSql.= " FROM grupo.catalogo_elemento ";
                $cadenaSql.= " WHERE elemento_id >0 ";

                break;
        }

        return $cadenaSql;
    }

}
?>
