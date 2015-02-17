<?php

namespace inventarios\gestionCompras\registrarOrdenServicios\funcion;

use inventarios\gestionCompras\registrarOrdenServicios\funcion\redireccion;

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
		
		$fechaActual = date ( 'Y-m-d' );
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		if ($_REQUEST ['objeto_contrato'] == '') {
		
		redireccion::redireccionar ( 'notextos' );
		}
		
		if ($_REQUEST ['forma_pago'] == '') {
		
		redireccion::redireccionar ( 'notextos' );
		}
		
		$datosSolicitante = array (
				$_REQUEST ['dependencia_solicitante'],
				$_REQUEST ['rubro'] 
		);
		
		// Registro Solicitante
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarSolicitante', $datosSolicitante );
		$id_solicitante = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$datosSupervisor = array (
				$_REQUEST ['nombre_supervisor'],
				$_REQUEST ['cargo_supervisor'],
				$_REQUEST ['dependencia_supervisor'] 
		);
		
		// Registro Supervisor
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarSupervisor', $datosSupervisor );
		$id_supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$datosContratistaC = array (
				$_REQUEST ['nombre_razon_contratista'],
				$_REQUEST ['identifcacion_contratista'],
				$_REQUEST ['direccion_contratista'],
				$_REQUEST ['telefono_contratista'],
				$_REQUEST ['cargo_contratista'] 
		);
		
		// Registro Contratista
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarContratista', $datosContratistaC );
		$id_ContratistaC = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// Registro Encargados
		
		$datosContratista = array (
				'3',
				$_REQUEST ['nombreContratista'],
				$_REQUEST ['identificacionContratista'],
				'NULL',
				'NULL' 
		);
		
		$datosjefe = array (
				'2',
				$_REQUEST ['nombreJefeSeccion'],
				'NULL',
				$_REQUEST ['cargoJefeSeccion'],
				'NULL' 
		);
		
		$datosOrdenador = array (
				'1',
				$_REQUEST ['nombreOrdenador'],
				'NULL',
				'NULL',
				$_REQUEST ['asignacionOrdenador'] 
		);
		
		// Registro Encargados
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarEncargado', $datosContratista );
		$id_contratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarEncargado', $datosjefe );
		$id_jefe = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarEncargado', $datosOrdenador );
		$id_ordenador = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		

		
		
		// Registro Orden
		
		$datosOrden = array (
				$fechaActual,
				$_REQUEST ['objeto_contrato'],
				isset ( $_REQUEST ['polizaA'] ),
				isset ( $_REQUEST ['polizaB'] ),
				isset ( $_REQUEST ['polizaC'] ),
				isset ( $_REQUEST ['polizaD'] ),
				$_REQUEST ['duracion'],
				$_REQUEST ['fecha_inicio_pago'],
				$_REQUEST ['fecha_final_pago'],
				$_REQUEST ['forma_pago'],
				$_REQUEST ['total_preliminar'],
				$_REQUEST ['iva'],
				$_REQUEST ['total'],
				$_REQUEST ['fecha_disponibilidad'],
				$_REQUEST ['numero_disponibilidad'],
				$_REQUEST ['valor_disponibilidad'],
				$_REQUEST ['fecha_registro'],
				$_REQUEST ['numero_registro'],
				$_REQUEST ['valor_registro'],
				$_REQUEST ['valorLetras_registro'],
				$id_ContratistaC [0] [0],
				$id_contratista [0] [0],
				$id_jefe [0] [0],
				$id_ordenador [0] [0],
				$id_solicitante[0][0],
				$id_supervisor[0][0] 
		);
				
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarOrden', $datosOrden );
		
		$id_orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		
		
		$datos = array (
				$id_orden [0] [0],
				$fechaActual 
		);
		

		 
		if ($id_orden) {
			
			redireccion::redireccionar ( 'inserto', $datos );
		} else {
			
			redireccion::redireccionar ( 'noInserto', $datos );
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

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>