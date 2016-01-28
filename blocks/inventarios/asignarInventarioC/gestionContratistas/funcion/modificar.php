<?php
use inventarios\asignarInventarioC\gestionContratista\funcion\redireccion;

include_once ('redireccionar.php');

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class RegistradorOrden {
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
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		/*
		 * Validar que el Contratista no tenga mas de un contrato de orden de prestacion de servicios(OPS)
		 */
		
		if ($_REQUEST ['tipo_contrato'] == 1 && $_REQUEST ['tipo_contrato_actual'] != '1') {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'Consultar_Tipo_Contrato_Particular', $_REQUEST ['identificacion'] );
			
			$contratos_tipo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			foreach ( $contratos_tipo as $valor ) {
				
				if ($valor ['tipo_contrato'] == '1') {
					redireccion::redireccionar ( "ErrorTipoContrato" );
					exit ();
				}
			}
		}
		
		/*
		 * Registrar Contratista.
		 */
		
		$datos = array (
				"numero" => $_REQUEST ['numero'],
				"tipo_contrato" => $_REQUEST ['tipo_contrato'],
				"identificacion" => $_REQUEST ['identificacion'],
				"nombre" => strtoupper ( $_REQUEST ['nombre'] ),
				"fecha_inicio" => $_REQUEST ['fecha_inicio'],
				"fecha_final" => $_REQUEST ['fecha_final'],
				"identificador" => $_REQUEST ['identificador_contratista'] 
		);
		
		$anio_inicio = date ( 'Y', strtotime ( $_REQUEST ['fecha_inicio'] ) );
		
		$anio_actual = date ( 'Y' );
		
		if ($anio_inicio != $anio_actual) {
			$registrar = false;
		} else {
			$registrar = true;
		}
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'modificarContrato', $datos );
		if ($registrar == true) {
			$Actualizacion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $datos, 'modificarContrato' );
		} else {
			$Actualizacion = false;
		}
		
		if ($Actualizacion != false) {
			$this->miConfigurador->setVariableConfiguracion ( "cache", true );
			
			redireccion::redireccionar ( "Actualizo" );
			exit ();
		} else {
			
			redireccion::redireccionar ( "NoActualizo" );
			exit ();
		}
	}
}

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>