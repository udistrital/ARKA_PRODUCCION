<?php

namespace inventarios\gestionElementos\registrarTraslados\funcion;

use inventarios\gestionElementos\registrarTraslados\funcion\redireccion;

include_once ('redireccionar.php');

$ruta_1 = $this->miConfigurador->getVariableConfiguracion ( 'raizDocumento' ) . '/plugin/php_excel/Classes/PHPExcel.class.php';
$ruta_2 = $this->miConfigurador->getVariableConfiguracion ( 'raizDocumento' ) . '/plugin/php_excel/Classes/PHPExcel/Reader/Excel2007.class.php';

include_once ($ruta_1);
include_once ($ruta_2);

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
		
		// $conexion = "sicapital";
		// $esteRecursoDBO = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'funcionario_informacion_fn', $_REQUEST ['responsable_reci'] );
		
		$funcionario_enviar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$fechaActual = date ( 'Y-m-d' );
		$elementos_traslado = unserialize ( $_REQUEST ['informacion_elementos'] );
		
		if (! isset ( $_REQUEST ['responsable_reci'] ) || ! isset ( $_REQUEST ['ubicacion'] ) || $_REQUEST ['responsable_reci'] == '' || $_REQUEST ['ubicacion'] == '') {
			redireccion::redireccionar ( 'noInserto', false );
			exit ();
		}
		// trasladar cada elementos
		foreach ( $elementos_traslado as $key => $values ) {
			$datos = array (
					$fechaActual,
					$elementos_traslado [$key] ['id'],
					$_REQUEST ['responsable_reci'],
					$elementos_traslado [$key] ['funcionario'],
					$_REQUEST ['ubicacion'],
					$_REQUEST ['observaciones'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'insertar_historico', $datos );
			$historico = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $datos, "insertar_historico" );
			
			if ($historico == false) {
				
				redireccion::redireccionar ( 'noInserto', false );
				exit ();
			}
			
			$arreglo_datos = array (
					$elementos_traslado [$key] ['id'],
					$_REQUEST ['responsable_reci'],
					$_REQUEST ['observaciones'],
					$_REQUEST ['dependencia'],
					$_REQUEST ['ubicacion'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'buscar_salidas', $elementos_traslado [$key] ['id'] );
			$salidas [] = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizar_salida', $arreglo_datos );
			$traslado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $arreglo_datos, "actualizar_salida" );
			
			if ($traslado == false) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'eliminar_historico', $historico [0] [0] );
				$historico = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $historico [0] [0], "eliminar_historico" );
				
				redireccion::redireccionar ( 'noInserto', $_REQUEST ['usuario'] );
				exit ();
			}
		}
		
		// foreach ($salidas as $tipo) {
		//
		// $arreglo = array(
		// $tipo[0] ['salida'],
		// $_REQUEST ['responsable_reci'],
		// $_REQUEST ['sede'],
		// $_REQUEST ['dependencia'],
		// $_REQUEST ['ubicacion']
		// );
		//
		// $cadenaSql = $this->miSql->getCadenaSql('actualizar_registro_salida', $arreglo);
		// $ActualizarRegistroSalida = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
		// }
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'dependencia_nombre', $_REQUEST ['ubicacion'] );
		$dep_nombre = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$datos = array (
				'responsable' => $_REQUEST ['responsable_reci'],
				'dependencia' => $dep_nombre [0] [0],
				"usuario" => $_REQUEST ['usuario'] 
		);
		if ($traslado == true) {
			$this->miConfigurador->setVariableConfiguracion ( 'cache', 'true' );
			redireccion::redireccionar ( 'inserto', $datos );
			exit ();
		} else {
			
			redireccion::redireccionar ( 'noInserto', $_REQUEST ['usuario'] );
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