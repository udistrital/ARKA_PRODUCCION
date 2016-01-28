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
		
		$variables = array (
				"funcionario" => $_REQUEST ['supervisor'],
				"contratista" => $_REQUEST ['contratista'],
				"tipo_contrato" => $_REQUEST ['tipo_contrato'],
				"numero_contrato" => $_REQUEST ['numero_contrato'],
				"vigencia" => $_REQUEST ['vigencia'] 
		)
		;
		
		// // COnsultar Elementos Activos del supervisor para asignarlos al contratista
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultarElementosSupervisor', $variables );
		// $elementos_supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// Consultar Elementos Asignados al contratista
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarElementosContratista', $variables );
		
		$elementos_contratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// recuperar datos de la asignacion
		$datos = array (
				$_REQUEST ['contratista'],
				$_REQUEST ['supervisor'],
				$_REQUEST ['usuario'] 
		);
		// asociar super-cont-item
		
		$items_sup [] = 0;
		$items_cont [] = 0;
		
		$valor = 0;
		
		for($i = 0; $i <= 1000000; $i ++) {
			if (isset ( $_REQUEST ['item_sup' . $i] )) {
				$items_sup [] = $_REQUEST ['item_sup' . $i];
			}
		}
		

		
		// Ahora para los elementos asociados al contratista inicialmente
		if ($elementos_contratista !== false) {
			
			for($i = 0; $i <= 1000000; $i ++) {
				if (isset ( $_REQUEST ['item_cont' . $i] )) {
					$items_cont [] = $_REQUEST ['item_cont' . $i];
				}
			}
			$valor = 0;
			
			foreach ( $elementos_contratista as $key => $values ) {
				foreach ( $items_cont as $cont => $values ) {
					if ($items_cont [$cont] == $elementos_contratista [$key] ['id_elemento_ind']) {
						// si son iguales, significa que un elemento del contratista esta asignado aún al contratista
						$valor = 1;
					} else {
						$valor = $valor;
						// inactivar el que no está
					}
				}
				
				if ($valor == 0) {
					// inactivar asignación
					$datosInactivar = array (
							$elementos_contratista [$key] ['id_elemento_ind'],
							0,
							$fechaActual 
					);
					
					$datosActivar = array (
							$elementos_contratista [$key] ['id_elemento_ind'],
							'f',
							$fechaActual 
					);
					$cadenaSql = $this->miSql->getCadenaSql ( 'activarElemento', $datosActivar );
					$asignar_cont = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $datosActivar, "activarElemento" );
					$cadenaSql2 = $this->miSql->getCadenaSql ( 'inactivarAsignacion', $datosInactivar );
					$inactivar_cont = $esteRecursoDB->ejecutarAcceso ( $cadenaSql2, "acceso", $datosInactivar, "inactivarAsignacion" );
				}
				$valor = 0;
			}
		}
		
		// inactivar item para asignar
		if ($asignar_cont == true && $inactivar_cont == true) {
			redireccion::redireccionar ( 'inserto', $datos );
			exit ();
		} else {
			redireccion::redireccionar ( 'noInserto', $datos );
			exit ();
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