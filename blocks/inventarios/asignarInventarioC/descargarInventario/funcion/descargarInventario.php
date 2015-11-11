<?php

namespace inventarios\asignarInventarioC\descargarInventario\funcion;

use inventarios\asignarInventarioC\descargarInventario\funcion\redireccion;

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
		$paz_salvo = '';
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionActa/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionActa/" . $esteBloque ['nombre'];
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// recuperar datos de la asignacion
		// asociar super-cont-item
		
		for($i = 0; $i <= 100000; $i ++) {
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
					FALSE,
					$fechaActual 
			);
			
			$datosInactivar1 = array (
					$items [$key],
					0,
					$fechaActual 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'inactivarAsignacion', $datosInactivar1 );
			$inasignar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $datosInactivar1, "inactivarAsignacion" );
			
			$cadenaSql2 = $this->miSql->getCadenaSql ( 'inactivarElemento', $datosInactivar );
			$inactivar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql2, "insertar", $datosInactivar, "inactivarElemento" );
		}
		
		if ($inactivar == true) {
			// determinar si posee paz y salvo
			$items_inactivados = count ( $items );
			$items_antes = $_REQUEST ['num_elementos'];
			
			if ($items_inactivados < $items_antes) {
				$paz_salvo = "Paz y Salvo NO activo. Elementos por entregar pendientes.";
			} else {
				$datosPZ = array (
						$_REQUEST ['contratista'],
						1,
						$fechaActual 
				);
				$paz_salvo = "Paz y Salvo ACTIVO";
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'estadoPazSalvo', $datosPZ );
				$estado_pz = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $datosPZ, "estadoPazSalvo" );
			}
		}
		
		$datos = array (
				$_REQUEST ['contratista'],
				$_REQUEST ['supervisor'],
				$paz_salvo,
				$_REQUEST ['usuario'] 
		);
		
		if ($inactivar == true && $inasignar == true) {
			redireccion::redireccionar ( 'inserto', $datos );
			exit;
		} else {
			redireccion::redireccionar ( 'noInserto', $_REQUEST['usuario'] );
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