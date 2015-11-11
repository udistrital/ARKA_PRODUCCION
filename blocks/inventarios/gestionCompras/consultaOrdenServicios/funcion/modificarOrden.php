<?php

namespace inventarios\gestionCompras\consultaOrdenServicios\funcion;

use inventarios\gestionCompras\consultaOrdenServicios\funcion\redireccion;

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
		
		if ($_REQUEST ['objeto_contrato'] == '') {
		
			redireccion::redireccionar ( 'notextos' );
		}
		
		if ($_REQUEST ['forma_pago'] == '') {
		
			redireccion::redireccionar ( 'notextos' );
		}
		

		
		$datosSupervisor = array (
				$_REQUEST ['nombre_supervisor'],
				$_REQUEST ['cargo_supervisor'],
				$_REQUEST ['dependencia_supervisor'],
				$_REQUEST ['sede_super'],
				$_REQUEST['supervisor'],
		);
		
		// Actualizar Supervisor
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarSupervisor', $datosSupervisor );
		$id_supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda",$datosSupervisor,'actualizarSupervisor' );
		
		$datosContratistaC = array (
				$_REQUEST ['nombre_razon_contratista'],
				$_REQUEST ['identifcacion_contratista'],
				$_REQUEST ['direccion_contratista'],
				$_REQUEST ['telefono_contratista'],
				$_REQUEST ['cargo_contratista'],
				$_REQUEST['contratista']
		);
		
		// Actualizar Contratista
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarContratista', $datosContratistaC );
		$id_ContratistaC = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda",$datosContratistaC,'actualizarContratista' );
		
		
		$arreglo = array (
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
				$_REQUEST['info_presupuestal'],
				$_REQUEST['unidad_ejecutora']
		);
		
		
		
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarPresupuestal', $arreglo );
		
		$inf_pre = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso",$arreglo,'actualizarPresupuestal' );
		
		// Actualizar Orden
			
		
		$datosOrden = array (
				$_REQUEST['dependencia_solicitante'],
				$_REQUEST['sede'],
				$_REQUEST['rubro'],				
				$_REQUEST ['objeto_contrato'],
				isset ( $_REQUEST ['poliza1'] ),
				isset ( $_REQUEST ['poliza2'] ),
				isset ( $_REQUEST ['poliza3'] ),
				isset ( $_REQUEST ['poliza4'] ),
				$_REQUEST ['duracion'],
				$_REQUEST ['fecha_inicio_pago'],
				$_REQUEST ['fecha_final_pago'],
				$_REQUEST ['forma_pago'],
				$_REQUEST['id_ordenador'],
				$_REQUEST['tipo_ordenador'],
				$_REQUEST['id_orden'],
				
		);
		
	
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarOrden', $datosOrden );

  
		$id_orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso",$datosOrden,'actualizarOrden' );


		$datos = array (
				$_REQUEST ['id_orden'],
				$_REQUEST['mensaje_titulo'],
				$_REQUEST['arreglo']
		);
	
		if ($id_orden == true) {
			$this->miConfigurador->setVariableConfiguracion("cache",true);
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