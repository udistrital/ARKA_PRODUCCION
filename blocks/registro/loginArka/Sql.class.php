<?php

namespace registro\loginArka;

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
}

if (! isset ( $GLOBALS ["autorizado"] )) {
    include ("../index.php");
    exit ();
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
        $prefijo = $this->miConfigurador->getVariableConfiguracion ( "prefijo" );
        $idSesion = $this->miConfigurador->getVariableConfiguracion ( "id_sesion" );

        switch ($tipo) {

            /**
			 * Clausulas específicas
			 */
			
			case "buscarUsuario" :
				$cadenaSql = 'SELECT ';
				$cadenaSql .= 'id_usuario, ';
				$cadenaSql .= 'nombre, ';
				$cadenaSql .= 'apellido, ';
				$cadenaSql .= 'correo, ';
				$cadenaSql .= 'telefono, ';
				$cadenaSql .= 'imagen, ';
				$cadenaSql .= 'clave, ';
				$cadenaSql .= 'tipo, ';
				$cadenaSql .= 'estilo, ';
				$cadenaSql .= 'idioma, ';
				$cadenaSql .= 'estado ';
				$cadenaSql .= 'FROM ';
				$cadenaSql .= $prefijo . 'usuario ';
				$cadenaSql .= "WHERE ";
				$cadenaSql .= "id_usuario = '" . trim ( $variable ["usuario"] ) . "' ";
				break;
			
			case "registrarEvento" :
				$cadenaSql = "INSERT INTO ";
				$cadenaSql .= $prefijo . "logger( ";
				$cadenaSql .= "id_usuario, ";
				$cadenaSql .= "evento, ";
				$cadenaSql .= "fecha) ";
				$cadenaSql .= "VALUES( ";
				$cadenaSql .= "'" . $variable[0] . "', ";
				$cadenaSql .= "'" . $variable[1] . "', ";
				$cadenaSql .= "'" . time () . "') ";
				break;

        }

        return $cadenaSql;

    }
}
?>
