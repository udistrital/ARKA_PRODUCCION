<?php

namespace inventarios\asignarInventarioC\contratistaElementos\funcion;

use inventarios\asignarInventarioC\contratistaElementos\funcion\redireccion;

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

		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarElementosContratista',$_REQUEST['contratista'] );
		$elementos_contratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

		// Ahora para los elementos asociados al contratista inicialmente
		if ($elementos_contratista !== false) {
			
			for($i = 0; $i <= 1000000; $i ++) {
				if (isset ( $_REQUEST ['item_cont' . $i] )) {
					$items_cont [] = $_REQUEST ['item_cont' . $i];
				}
			}
			
			
			
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'validacionDefault',$_REQUEST['contratista'] );
			
			$validacion_default = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $_REQUEST['contratista'], "validacionDefault" );
			
			
			
			
			foreach ( $items_cont as $valor ) {
				
					
				
				
				

				$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarVerificacion', $valor );
				
				$validacion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $valor, "actualizarVerificacion" );
				
				
				
				}
				
			}

		// inactivar item para asignar
		if ($validacion) {
			redireccion::redireccionar ( 'inserto', $_REQUEST['usuario'] );
			exit();
		} else {
			redireccion::redireccionar ( 'noInserto', $_REQUEST['usuario'] );
			exit();
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