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
				$_REQUEST ['supervisor'],
				$_REQUEST ['contratista'] 
		);
		
		// COnsultar Elementos Activos del supervisor para asignarlos al contratista
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarElementosSupervisor', $variables );
		$elementos_supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
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
		
		if ($elementos_supervisor !== false) {
			foreach ( $elementos_supervisor as $key => $values ) {
				foreach ( $items_sup as $cont => $values ) {
					if ($items_sup [$cont] == $elementos_supervisor [$key] ['id_elemento_ind']) {
						$valor = 1;
					} else {
						$valor = $valor;
					}
					
					if ($valor == 1) {
						// si son iguales, significa que un elemento del supervisor fue asignado
						// activar asignación a contratista
						$datosAsignacion = array (
								$_REQUEST ['supervisor'],
								$_REQUEST ['contratista'],
								$elementos_supervisor [$key] ['id_elemento_ind'],
								1,
								$fechaActual 
						);
						
						$datosInactivar = array (
								$elementos_supervisor [$key] ['id_elemento_ind'],
								't',
								$fechaActual 
						);
						
						$cadenaSql = $this->miSql->getCadenaSql ( 'asignarElemento', $datosAsignacion );
						$asignar_sup = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $datosAsignacion, "asignarElemento" );
						
						$cadenaSql2 = $this->miSql->getCadenaSql ( 'inactivarElemento', $datosInactivar );
						$inactivar_sup = $esteRecursoDB->ejecutarAcceso ( $cadenaSql2, "insertar", $datosInactivar, "inactivarElemento" );
					} else {
						// si son diferentes, significa que los demas elementos pertenecen al supervisor
						// activar asignación a supervisor
						$datosAsignacion = array (
								$elementos_supervisor [$key] ['id_elemento_ind'],
								0,
								$fechaActual 
						);
						
						$datosInactivar = array (
								$elementos_supervisor [$key] ['id_elemento_ind'],
								'f',
								$fechaActual 
						);
						
						$cadenaSql = $this->miSql->getCadenaSql ( 'asignarElemento_sup', $datosAsignacion );
						$asignar_sup = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $datosAsignacion, "asignarElemento_sup" );
						
						$cadenaSql2 = $this->miSql->getCadenaSql ( 'inactivarElemento_sup', $datosInactivar );
						$inactivar_sup = $esteRecursoDB->ejecutarAcceso ( $cadenaSql2, "insertar", $datosInactivar, "inactivarElemento_sup" );
					}
				}
				
				$valor = 0;
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
					$asignar_cont = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $datosActivar, "activarElemento" );
					$cadenaSql2 = $this->miSql->getCadenaSql ( 'inactivarAsignacion', $datosInactivar );
					$inactivar_cont = $esteRecursoDB->ejecutarAcceso ( $cadenaSql2, "insertar", $datosInactivar, "inactivarAsignacion" );
				}
				$valor = 0;
			}
		}
		
		// inactivar item para asignar
		if (isset ( $asignar_cont ) == true && isset ( $asignar_sup ) == true || isset ( $inactivar_cont ) == true || isset ( $inactivar_sup ) == true) {
			redireccion::redireccionar ( 'inserto', $datos );
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