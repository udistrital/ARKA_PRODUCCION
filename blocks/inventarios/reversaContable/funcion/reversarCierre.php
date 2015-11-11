<?php

namespace inventarios\reversaContable\funcion;

use inventarios\reversaContable\funcion\redireccion;

include_once ('redireccionar.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class RegistradorCierre {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql, $funcion) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	function procesarFormulario() {
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/" . $esteBloque ['nombre'];
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		for($i = 0; $i <= count ( $_REQUEST ); $i ++) {
			if (isset ( $_REQUEST ['item_' . $i] )) {
				$items [] = $_REQUEST ['item_' . $i];
			}
		}
		
		// Registro del Radicar Asignar entrada de compras
		
		foreach ( $items as $key => $values ) {
			$datosRegistro = array (
					'fechaRegistro' => date ( 'Y-m-d' ),
					'id_cierre' => $items [$key],
					'estado' => 0,
					'usuario' => $_REQUEST ['usuario'] 
			);
			
			// consultar si la vigencia y la entrada existen
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarCierre', $datosRegistro );
			$estado_asignar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $datosRegistro, "actualizarCierre" );
			
			if ($estado_asignar == false) {
				redireccion::redireccionar ( 'noInserto', $datosRegistro );
			}
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarEntrada', $datosRegistro );
			$resultadoActualizacion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $datosRegistro, "actualizarEntrada" );
			
			if ($resultadoActualizacion == false) {
				$datosRegistro = array (
						'fechaRegistro' => date ( 'Y-m-d' ),
						'id_cierre' => $items [$key],
						'estado' => 1,
						'usuario' => $_REQUEST ['usuario'] 
				);
				$cadenaSql = $this->miSql->getCadenaSql ( 'activarCierre', $datosRegistro );
				$estado_asignar3 = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $datosRegistro, "activarCierre" );
				redireccion::redireccionar ( 'noInserto', $datosRegistro );
			}
		}
		
		if ($estado_asignar == true && $resultadoActualizacion == true) {
			$this->miConfigurador->setVariableConfiguracion ( "cache", true );
			redireccion::redireccionar ( 'inserto', $_REQUEST ['usuario'] );
		} else {
			redireccion::redireccionar ( 'noInserto', array (
					"usuario" => $_REQUEST ['usuario'] 
			) );
		}
		function resetForm() {
			foreach ( $_REQUEST as $clave => $valor ) {
				if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
					unset ( $_REQUEST [$clave] );
				}
			}
		}
	}
}

$miRegistrador = new RegistradorCierre ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();
?>