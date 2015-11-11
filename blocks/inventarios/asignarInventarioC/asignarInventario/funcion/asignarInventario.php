<?php

namespace inventarios\asignarInventarioC\asignarInventario\funcion;

use inventarios\asignarInventarioC\asignarInventario\funcion\redireccion;

include_once ('redireccionar.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class RegistradorActa {
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
		$fechaActual = date ( 'Y-m-d' );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionActa/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionActa/" . $esteBloque ['nombre'];
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// recuperar datos de la asignacion
		$datos = array (
				$_REQUEST ['contratista'],
				$_REQUEST ['supervisor'],
				$_REQUEST ['usuario'] 
		);
		// asociar super-cont-item
		
		for($i = 0; $i <= 1000000; $i ++) {
			if (isset ( $_REQUEST ['item' . $i] )) {
				$items [] = $_REQUEST ['item' . $i];
			}
		}
		;
		
		foreach ( $items as $key => $values ) {
			$datosAsignacion = array (
					$_REQUEST ['contratista'],
					$_REQUEST ['supervisor'],
					$items [$key],
					1,
					$fechaActual 
			);
			
			$datosInactivar = array (
					$items [$key],
					TRUE,
					$fechaActual 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'asignarElemento', $datosAsignacion );
			$asignar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $datosAsignacion, "asignarElemento" );
			
			$cadenaSql2 = $this->miSql->getCadenaSql ( 'inactivarElemento', $datosInactivar );
			$inactivar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql2, "acceso", $datosInactivar, "inactivarElemento" );
		}
		// inactivar item para asignar
		if ($inactivar == true && $asignar == true) {
			redireccion::redireccionar ( 'inserto', $datos );
			exit;
		} else {
			redireccion::redireccionar ( 'noInserto', $_REQUEST ['usuario'] );
			exit;
		}
	}
	function resetForm() {
		foreach ( $_REQUEST as $clave => $valor ) {
			if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
				unset ( $_REQUEST [$clave] );
			}
		}
	}
}

$miRegistrador = new RegistradorActa ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();
?>