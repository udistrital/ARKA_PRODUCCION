<?php

namespace inventarios\gestionActa\activacionElementosActa\funcion;

use inventarios\gestionActa\activacionElementosActa\funcion\redireccion;

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
		
		for($i = 0; $i <= 10000; $i ++) {
			if (isset ( $_REQUEST ['item_' . $i] )) {
				$elementos [] = $_REQUEST ['item_' . $i];
			}
		}
		
		foreach ( $elementos as $i ) {
			$id_elemento_acta = $i;
			$cadenaSql = $this->miSql->getCadenaSql ( 'idElementoMax' );
			
			$elemento_id_max = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$elemento_id_max = $elemento_id_max [0] [0] + 1;
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consulta_elementos_activar', $i );
			$elementos_particulares = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$fechaActual = date ( 'Y-m-d' );
			
			$elementos_particulares = $elementos_particulares [0];
			$arreglo = array (
					$fechaActual,
					$elementos_particulares ['nivel'],
					$elementos_particulares ['tipo_bien'],
					$elementos_particulares ['descripcion'],
					$elementos_particulares ['cantidad'],
					$elementos_particulares ['unidad'],
					$elementos_particulares ['valor'],
					$elementos_particulares ['iva'],
					NULL,
					NULL,
					$elementos_particulares ['subtotal_sin_iva'],
					$elementos_particulares ['total_iva'],
					$elementos_particulares ['total_iva_con'],
					$elementos_particulares ['tipo_poliza'],
					$elementos_particulares ['fecha_inicio_pol'],
					$elementos_particulares ['fecha_final_pol'],
					$elementos_particulares ['marca'],
					$elementos_particulares ['serie'],
					$_REQUEST ['numero_entrada'],
					$elemento_id_max,
					$elementos_particulares ['cantidad'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento', $arreglo );
			
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, "ingresar_elemento" );
			
			$placa = date ( 'Ymd' ) . "00000";
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'buscar_repetida_placa', $placa );
			
			$num_placa = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'idElementoMaxIndividual' );
			
			$elemento_id_max_indiv = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			$elemento_id_max_indiv = $elemento_id_max_indiv [0] [0] + 1;
			
			$sumaplaca = 0;
			
			if ($elementos_particulares ['tipo_bien'] == 2 || $elementos_particulares ['tipo_bien'] == 3) {
				
				if ($num_placa [0] [0] == 0) {
					
					for($i = 0; $i < $elementos_particulares ['cantidad']; $i ++) {
						$arregloElementosInv = array (
								$fechaActual,
								$placa + $sumaplaca,
								(is_null ( $elementos_particulares ['serie'] ) == true) ? NULL : $elementos_particulares ['serie'],
								$elemento [0] [0],
								$elemento_id_max_indiv 
						);
						
						$sumaplaca = ($elementos_particulares ['tipo_bien'] == 1) ? $sumaplaca : $sumaplaca ++;
						
						$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_individual', $arregloElementosInv );
						
						$elemento_id [$i] = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arregloElementosInv, "ingresar_elemento_individual" );
						
						$elemento_id_max_indiv = $elemento_id_max_indiv + 1;
						
						// ******************* AQUI QUEDA LA FOTO INDIVIDUAL ********************************//
						if (is_null ( $elementos_particulares ['imagen'] ) == false) {
							$arreglo = array (
									"elemento" => $elemento_id [$i] [0] [0],
									"imagen" => $elementos_particulares ['imagen'] 
							);
							
							$cadenaSql = $this->miSql->getCadenaSql ( 'ElementoImagen', $arreglo );
							$imagen = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
						}
					}
				} else if ($num_placa [0] [0] != 0) {
					
					$cadenaSql = $this->miSql->getCadenaSql ( 'buscar_placa_maxima', $placa );
					
					$num_placa = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					
					$placa = $num_placa [0] [0];
					$sumaplaca = 1;
					
					for($i = 1; $i <= $elementos_particulares ['cantidad']; $i ++) {
						$arregloElementosInv = array (
								$fechaActual,
								$placa + $sumaplaca,
								(is_null ( $elementos_particulares ['serie'] ) == true) ? NULL : $elementos_particulares ['serie'],
								$elemento [0] [0],
								$elemento_id_max_indiv 
						);
						
						$sumaplaca = ($elementos_particulares ['tipo_bien'] == 1) ? $sumaplaca : $sumaplaca ++;
						
						$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_individual', $arregloElementosInv );
						
						$elemento_id [$i] = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arregloElementosInv, "ingresar_elemento_individual" );
						
						if (is_null ( $elementos_particulares ['imagen'] ) == false) {
							$arreglo = array (
									"elemento" => $elemento_id [$i] [0] [0],
									"imagen" => $elementos_particulares ['imagen'] 
							);
							
							$cadenaSql = $this->miSql->getCadenaSql ( 'ElementoImagen', $arreglo );
							$imagen = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, "ElementoImagen" );
						}
						
						$elemento_id_max_indiv = $elemento_id_max_indiv + 1;
					}
				}
			}
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'anular_elementos_acta', $id_elemento_acta );
			
			$elemento_anulado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $id_elemento_acta, "anular_elementos_acta" );
		}
		
		$datos = array (
				$_REQUEST ['numero_entrada'],
				$_REQUEST ['numero_acta'],
				$_REQUEST ['consecutivo_entrada'] 
		);
		
		if ($elemento) {
			
			$this->miConfigurador->setVariableConfiguracion ( "cache", true );
			redireccion::redireccionar ( 'inserto', $datos );
			exit ();
		} else {
			
			redireccion::redireccionar ( 'noInserto' );
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