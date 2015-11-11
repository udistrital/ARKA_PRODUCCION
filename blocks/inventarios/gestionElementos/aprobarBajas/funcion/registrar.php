<?php

namespace inventarios\gestionElementos\aprobarBajas\funcion;

use inventarios\gestionElementos\aprobarBajas\funcion\redireccion;

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
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionElementos/";
		$rutaBloque .= $esteBloque ['nombre'];
		
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionElementos/" . $esteBloque ['nombre'];
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$fechaActual = date ( 'Y-m-d' );
		
		$i = 0;
		foreach ( $_FILES as $key => $values ) {
			
			$archivo [$i] = $_FILES [$key];
			$i ++;
		}
		
		$archivo = $archivo [0];
		
		if (isset ( $archivo )) {
			// obtenemos los datos del archivo
			$tamano = $archivo ['size'];
			$tipo = $archivo ['type'];
			$archivo1 = $archivo ['name'];
			$prefijo = substr ( md5 ( uniqid ( rand () ) ), 0, 6 );
			
			if ($archivo1 != "") {
				// guardamos el archivo a la carpeta files
				$destino1 = $rutaBloque . "/documento_radicacion/" . $prefijo . "_" . $archivo1;
				if (copy ( $archivo ['tmp_name'], $destino1 )) {
					$status = "Archivo subido: <b>" . $archivo1 . "</b>";
					$destino1 = $host . "/documento_radicacion/" . $prefijo . "_" . $archivo1;
					
					$arreglo = array (
							'fecha' => $fechaActual,
							'destino' => $destino1,
							'nombre' => $archivo1,
							'estado' => 1 
					);
					
					$cadenaSql = $this->miSql->getCadenaSql ( "registroDocumento_Aprobacion", $arreglo );
					$idAprobacion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda', $arreglo, "registroDocumento_Aprobacion" );
					
					if ($idAprobacion == false) {
						redireccion::redireccionar ( 'noInserto', $_REQUEST ['usuario'] );
					}
				} else {
					$status = "Error al subir el archivo";
					
					redireccion::redireccionar ( 'noInserto', $_REQUEST ['usuario'] );
					// echo $status;
				}
			} else {
				$status = "Error al subir archivo";
				
				redireccion::redireccionar ( 'noInserto', $_REQUEST ['usuario'] );
				// echo $status . "2";
			}
		}
		
		// Ahora a actualizar el estado de los items seleccionados
		$items [] = unserialize ( $_REQUEST ['items'] );
		
		$a = 0;
		

		foreach ( $items as $key => $fila ) {
			
			foreach ( $fila as $columna => $valor ) {
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'mostrarInfoDepreciar_elemento', $valor );
				$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
				$cantidad = 1;
				$meses = intval ( $elemento [0] ['grupo_vidautil'] );
				$precio = $elemento [0] ['valor'];
				$inflacion = $elemento [0] ['ajuste_inflacionario'];
				
				$fecha_salida = new \DateTime ( $elemento [0] ['fecha_registro'] );
				$fecha_corte = new \DateTime ( date ( 'Y-m-d' ) );
				$fecha_limite = new \DateTime ( '1999-12-31' );
				
				$periodos = $fecha_corte->diff ( $fecha_salida );
				$meses_periodo = $periodos->format ( "%m" );
				$años_periodo = $periodos->format ( "%y" ) * 12;
				
				$periodos_fecha1 = $meses_periodo + $años_periodo + 1;
				
				$valor_historico = $precio * $cantidad;
				
				if ($fecha_salida <= $fecha_limite) {
					$inflacion = $inflacion;
				} else {
					$inflacion = 0;
				}
				
				$valor_ajustado = $valor_historico + $inflacion;
				
				// Valor de la Cuota
				if ($meses == 0) {
					$cuota = 0;
					$periodos_fecha = 0;
				} else {
					$cuota = $valor_historico / $meses;
					$periodos_fecha = $periodos_fecha1;
				}
				
				// DEPRECIACION AUMULADA
				if ($meses !== 0) {
					if ($periodos_fecha >= $meses) {
						
						$dep_acumulada = $valor_historico;
					} else {
						$dep_acumulada = $cuota * $periodos_fecha;
					}
				} else {
					$dep_acumulada = 0;
				}
				
				// CIRCULAR 56
				$circular56 = $valor_historico + $inflacion;
				
				// CUOTAS AJUSTES POR INFLACION
				if ($meses == 0) {
					$cuota_inflacion = 0;
				} else {
					if ($inflacion > 0) {
						$cuota_inflacion = $inflacion / $meses;
					} else {
						$cuota_inflacion = 0;
					}
				}
				
				// AJUSTE POR INFLACION A LA DEPRECIACION ACUMULADA
				if ($meses !== 0) {
					if ($periodos_fecha >= $meses) {
						$api_acumulada = $inflacion;
					} else {
						$api_acumulada = $cuota_inflacion * $periodos_fecha;
					}
				} else {
					$api_acumulada = 0;
				}
				
				// CIRCULAR 56 - DEPRECIACIÓN
				$circular_depreciacion = $api_acumulada + $dep_acumulada;
				
				// VALOR A LOS LIBROS
				$valor_libros = $valor_ajustado - $circular_depreciacion;
				
				$datos_calculada = array (
						0 => $elemento [0] ['id_elemento_ind'],
						'id_elemento' => $elemento [0] ['id_elemento_ind'],
						1 => $elemento [0] ['grupo_codigo'],
						'grupo_contable' => $elemento [0] ['grupo_codigo'],
						2 => $meses,
						'meses_depreciar' => $meses,
						3 => $elemento [0] ['fecha_registro'],
						'fechaSalida' => $elemento [0] ['fecha_registro'],
						4 => date ( 'Y-m-d' ),
						'fechaCorte' => date ( 'Y-m-d' ),
						5 => 1,
						'cantidad' => 1,
						6 => $elemento [0] ['valor'],
						'precio' => $elemento [0] ['valor'],
						7 => $valor_historico,
						'valor_historico' => $valor_historico,
						8 => $valor_ajustado,
						'valor_ajustado' => $valor_ajustado,
						9 => $cuota,
						'cuota' => $cuota,
						10 => $periodos_fecha,
						'periodos_fecha' => $periodos_fecha,
						10 => $dep_acumulada,
						'depreciacion_acumulada' => $dep_acumulada,
						11 => $circular56,
						'circular_56' => $circular56,
						12 => $cuota_inflacion,
						'cuota_inflacion' => $cuota_inflacion,
						13 => $api_acumulada,
						'api_acumulada' => $api_acumulada,
						14 => $circular_depreciacion,
						'circular_depreciacion' => $circular_depreciacion,
						15 => $valor_libros,
						'valor_libros' => $valor_libros,
						16 => 1,
						'estado' => 1,
						17 => date ( 'Y-m-d' ),
						'fregistro' => date ( 'Y-m-d' ) 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'registrarDepreciacion', $datos_calculada );
				$registro_depreciacion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $datos_calculada, "registrarDepreciacion" );
			
				$datosAprobar = array (
						'id_aprobacion' => $idAprobacion [0] [0],
						'id_elemento' => $valor,
						'estado' => 1,
						'id_depreciacion' => $registro_depreciacion [0] [0] 
				);
				
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarAprobar', $datosAprobar );
				 
				$asignar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $datosAprobar, "actualizarAprobar" );
				$a ++;
                                
			}
		}
		
		
		if ($asignar == true) {
			$this->miConfigurador->setVariableConfiguracion ( "cache", true );
			redireccion::redireccionar ( 'inserto', array (
					$a,
					$_REQUEST ['usuario'] 
			) );
		} else {
			$cadenaSql = $this->miSql->getCadenaSql ( 'eliminarAprobar', $idAprobacion [0] [0] );
			$asignar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso",$idAprobacion [0] [0],"eliminarAprobar");
			redireccion::redireccionar ( 'noInserto',$_REQUEST['usuario'] );
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