<?php

namespace inventarios\gestionActa\consultarActa\funcion;

use inventarios\gestionActa\consultarActa\funcion\redireccion;

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
		
		// foreach ( $_FILES as $key => $values ) {
		
		// $archivoImagen = $_FILES [$key];
		// }
		
		// if ($archivoImagen ['error'] == 0) {
		
		// if ($archivoImagen ['type'] != 'image/jpeg') {
		// redireccion::redireccionar ( 'noFormatoImagen' );
		// exit ();
		// }
		// }
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultar_iva', $_REQUEST ['iva'] );
		
		$valor_iva = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$valor_iva = $valor_iva [0] [0];
		
		if ($_REQUEST ['id_tipo_bien'] == 1) {
			
			$arreglo = array (
					$_REQUEST ['nivel'],
					$_REQUEST ['id_tipo_bien'],
					$_REQUEST ['descripcion'],
					$_REQUEST ['cantidad'],
					$_REQUEST ['unidad'],
					$_REQUEST ['valor'],
					$_REQUEST ['iva'],
					$_REQUEST ['cantidad'] * $_REQUEST ['valor'],
					$_REQUEST ['cantidad'] * $_REQUEST ['valor'] * $valor_iva,
					round ( $_REQUEST ['cantidad'] * $_REQUEST ['valor'] + $_REQUEST ['cantidad'] * $_REQUEST ['valor'] * $valor_iva ),
					($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : null,
					($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : null,
					$_REQUEST ['id_elemento_acta'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizar_elemento_tipo_1', $arreglo );
			
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		} else if ($_REQUEST ['id_tipo_bien'] == 2) {
			
			$arreglo = array (
					$_REQUEST ['nivel'],
					$_REQUEST ['id_tipo_bien'],
					$_REQUEST ['descripcion'],
					1,
					$_REQUEST ['unidad'],
					$_REQUEST ['valor'],
					$_REQUEST ['iva'],
					1 * $_REQUEST ['valor'],
					1 * $_REQUEST ['valor'] * $valor_iva,
					round ( 1 * $_REQUEST ['valor'] + 1 * $_REQUEST ['valor'] * $valor_iva ),
					($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : null,
					($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : null,
					$_REQUEST ['id_elemento_acta'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizar_elemento_tipo_1', $arreglo );
			
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		} else if ($_REQUEST ['id_tipo_bien'] == 3) {
			
			if ($_REQUEST ['tipo_poliza'] == 0) {
				$arreglo = array (
						
						$_REQUEST ['nivel'],
						$_REQUEST ['id_tipo_bien'],
						$_REQUEST ['descripcion'],
						$_REQUEST ['cantidad'] = 1,
						$_REQUEST ['unidad'],
						$_REQUEST ['valor'],
						$_REQUEST ['iva'],
						$_REQUEST ['cantidad'] * $_REQUEST ['valor'],
						$_REQUEST ['cantidad'] * $_REQUEST ['valor'] * $valor_iva,
						round ( $_REQUEST ['cantidad'] * $_REQUEST ['valor'] + $_REQUEST ['cantidad'] * $_REQUEST ['valor'] * $valor_iva ),
						$_REQUEST ['tipo_poliza'],
						NULL,
						NULL,
						($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : NULL,
						($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : NULL,
						$_REQUEST ['id_elemento_acta'] 
				);
			} else if ($_REQUEST ['tipo_poliza'] == 1) {
				$arreglo = array (
						$_REQUEST ['nivel'],
						$_REQUEST ['id_tipo_bien'],
						$_REQUEST ['descripcion'],
						$_REQUEST ['cantidad'] = 1,
						$_REQUEST ['unidad'],
						$_REQUEST ['valor'],
						$_REQUEST ['iva'],
						$_REQUEST ['cantidad'] * $_REQUEST ['valor'],
						$_REQUEST ['cantidad'] * $_REQUEST ['valor'] * $valor_iva,
						round ( $_REQUEST ['cantidad'] * $_REQUEST ['valor'] + $_REQUEST ['cantidad'] * $_REQUEST ['valor'] * $valor_iva ),
						$_REQUEST ['tipo_poliza'],
						$_REQUEST ['fecha_inicio'],
						$_REQUEST ['fecha_final'],
						($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : NULL,
						($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : NULL,
						$_REQUEST ['id_elemento_acta'] 
				);
			}
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizar_elemento_tipo_2', $arreglo );
			
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		}
		
		
		
		// if ($archivoImagen ['type'] == 'image/jpeg') {
		
		// $data = base64_encode ( file_get_contents ( $archivo ['tmp_name'] ) );
		
		// $arreglo = array (
		// "elemento" => $_REQUEST ['id_elemento_acta'] ,
		// "imagen" => $data
		// );
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'ActualizarElementoImagen', $arreglo );
		
		// $imagen = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		// }
		
		
		
		if ($elemento) {
			$this->miConfigurador->setVariableConfiguracion("cache",true);
			redireccion::redireccionar ( 'ActualizoElemento' );
			exit ();
		} else {
			
			redireccion::redireccionar ( 'noActualizoElemento' );
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