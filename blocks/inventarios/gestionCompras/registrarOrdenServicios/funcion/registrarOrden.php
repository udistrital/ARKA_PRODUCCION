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
			exit ();
		}
		
		if ($_REQUEST ['forma_pago'] == '') {
			
			redireccion::redireccionar ( 'notextos' );
			exit ();
		}
		
		$datosSupervisor = array (
				$_REQUEST ['nombre_supervisor'],
				$_REQUEST ['cargo_supervisor'],
				$_REQUEST ['dependencia_supervisor'],
				$_REQUEST ['sede_super'],
				
				)
		;
		
		
		// Registro Supervisor
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarSupervisor', $datosSupervisor );
		$id_supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda",$datosSupervisor,'insertarSupervisor');
		
		$datosContratistaC = array (
				$_REQUEST ['nombre_razon_contratista'],
				$_REQUEST ['identifcacion_contratista'],
				$_REQUEST ['direccion_contratista'],
				$_REQUEST ['telefono_contratista'],
				$_REQUEST ['cargo_contratista'] 
		);
		
		// Registro Contratista
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarContratista', $datosContratistaC );
		
		$id_ContratistaC = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda",$datosContratistaC,'insertarContratista');
		
		// Registro Orden
		
		$arreglo = array (
				$fechaActual,
				$_REQUEST ['vigencia_disponibilidad'],
				$_REQUEST ['diponibilidad'],
				$_REQUEST ['valor_disponibilidad'],
				$_REQUEST ['fecha_diponibilidad'],
				$_REQUEST ['valorLetras_disponibilidad'],
				$_REQUEST ['vigencia_disponibilidad'],
				$_REQUEST ['registro'],
				$_REQUEST ['valor_registro'],
				$_REQUEST ['fecha_registro'],
				$_REQUEST ['valorL_registro'],
				$_REQUEST ['unidad_ejecutora'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarInformacionPresupuestal', $arreglo );
		
		$info_presupuestal = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo,'insertarInformacionPresupuestal');
		// var_dump ( $_REQUEST );
		
		switch ($_REQUEST ['tipo_orden']) {
			case '1' :
				$nombre = "ORDEN DE COMPRA";
				$cadenaSql = $this->miSql->getCadenaSql ( 'consecutivo_compra', date ( 'Y' ) );
				
				$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				$consecutivo_servicio = 'NULL';
				if (is_null ( $consecutivo [0] [0] )) {
					
					$consecutivo_compra = 1;
				} else {
					
					$consecutivo_compra = $consecutivo [0] [0] + 1;
				}
				
				break;
			
			case '9' :
				
				$nombre = "ORDEN DE SERVICIO";
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'consecutivo_servicios', date ( 'Y' ) );
				
				$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda");
				$consecutivo_compra = 'NULL';
				if (is_null ( $consecutivo [0] [0] )) {
					
					$consecutivo_servicio = 1;
				} else {
					
					$consecutivo_servicio = $consecutivo [0] [0] + 1;
				}
				
				break;
		}
		
		$datosOrden = array (
				$_REQUEST ['tipo_orden'],
				date ( 'Y' ),
				$consecutivo_servicio,
				$consecutivo_compra,
				date ( 'Y-m-d' ),
				$info_presupuestal [0] [0],
				$_REQUEST ['dependencia_solicitante'],
				$_REQUEST ['sede'],
				$_REQUEST ['rubro'],
				$_REQUEST ['objeto_contrato'],
				isset ( $_REQUEST ['polizaA'] ),
				isset ( $_REQUEST ['polizaB'] ),
				isset ( $_REQUEST ['polizaC'] ),
				isset ( $_REQUEST ['polizaD'] ),
				$_REQUEST ['numero_dias'],
				$_REQUEST ['fecha_inicio_pago'],
				$_REQUEST ['fecha_final_pago'],
				$_REQUEST ['forma_pago'],
				$id_ContratistaC [0] [0],
				$id_supervisor [0] [0],
				$_REQUEST ['id_ordenador'],
				$_REQUEST ['tipo_ordenador'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarOrden', $datosOrden );
		
		$consecutivos_orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ,$datosOrden,'insertarOrden');
		
		$consecutivo_orden = $consecutivos_orden [0];
		
		if ($consecutivo_orden) {
			
			
			for($i=0;$i<=1;$i++){
				
				if (! is_null ( $consecutivo_orden[$i] )) {
					$consecutivo = $consecutivo_orden[$i];
				}
				
			}
			
			$datos = "NÚMERO DE " . $nombre . " # " . $consecutivo . "<br> Y VIGENCIA " . date ( 'Y' );
			$this->miConfigurador->setVariableConfiguracion("cache",true);
			redireccion::redireccionar ( 'inserto', array($datos,$consecutivo_orden[2]) );
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

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>