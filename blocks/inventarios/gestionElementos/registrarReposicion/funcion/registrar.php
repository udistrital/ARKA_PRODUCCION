<?php

namespace inventarios\gestionElementos\registrarReposicion\funcion;

use inventarios\gestionElementos\registrarReposicion\funcion\redireccion;

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
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionElementos/";
		$rutaBloque .= $esteBloque ['nombre'];
		
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionElementos/" . $esteBloque ['nombre'];
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$fechaActual = date ( 'Y-m-d' );
		$fechaReinicio = date ( "Y-m-d", mktime ( 0, 0, 0, 1, 1, date ( 'Y' ) ) );
		
		if ($fechaActual == $fechaReinicio) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultaConsecutivo', $fechaReinicio );
			$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if (isset ( $consecutivo ) && $consecutivo == false) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'reiniciarConsecutivo' );
				$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
			}
		}
		
		$seleccion = unserialize ( base64_decode ( $_REQUEST ['elemento'] ) );
		
		$entrada = array (
				'fecha_registro' => date ( 'Y-m-d' ),
				'vigencia' => date ( 'Y' ),
				'clase_entrada' => 1,
				'tipo_contrato' => 0,
				'numero_contrato' => '',
				'proveedor' => $_REQUEST ['proveedor'],
				'numero_factura' => $_REQUEST ['numero_factura'],
				'fecha_factura' => $_REQUEST ['fecha_factura'],
				'sede' => $_REQUEST ['sede'],
				'dependencia' => $_REQUEST ['dependencia'],
				'supervisor' => $_REQUEST ['supervisor'],
				'observaciones' => $_REQUEST ['observaciones_entrada'],
				'id_ordenador' => $_REQUEST ['id_ordenador'],
				'tipo_ordenador' => $_REQUEST ['tipo_ordenador'],
				'identificacion_ordenador' => $_REQUEST ['identificacion_ordenador'],
				'asignacion_ordenador' => $_REQUEST ['asignacionOrdenador'] 
		);
		
		// Registrar la Entrada Nueva
		$arreglo_clase = array (
				$observacion = 'reposición del elemento ' . $seleccion ['placa'] . ' de la entrada ' . $seleccion ['consecutivoentrada'],
				$seleccion ['id_entrada'],
				$seleccion ['id_salida'],
				$seleccion ['id_elemento_ind'],
				0,
				0,
				'NULL',
				'NULL' 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarInformación', $arreglo_clase );
		$info_clase = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo_clase, "insertarInformación" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'idMaximoEntrada' );
		$idEntradamax = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$idEntradamax = $idEntradamax [0] [0] + 1;
		
		$anio_vigencia = date ( 'Y' );
		
		$arregloDatos = array (
				$fechaActual,
				$anio_vigencia,
				1,
				$info_clase [0] [0],
				0,
				0,
				'0001-01-01',
				$entrada ['proveedor'],
				$entrada ['numero_factura'],
				$entrada ['fecha_factura'],
				$entrada ['observaciones'],
				0,
				$entrada ['id_ordenador'],
				$entrada ['sede'],
				$entrada ['dependencia'],
				$entrada ['supervisor'],
				$entrada ['tipo_ordenador'],
				$entrada ['identificacion_ordenador'],
				$idEntradamax 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarEntrada', $arregloDatos );
		$id_entrada = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arregloDatos, "insertarEntrada" );
		
		// Crear Elemento
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'idElementoMax' );
		$elemento_id_max = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$elemento_id_max = $elemento_id_max [0] [0] + 1;
		
		$elemento = array (
				'fecha_registro' => date ( 'Y-m-d' ),
				'serie' => $_REQUEST ['serie'],
				'marca' => $_REQUEST ['marca'],
				'descripcion' => $_REQUEST ['descripcion'],
				'cantidad' => $_REQUEST ['cantidad'],
				'unidad' => $_REQUEST ['unidad'],
				'valor' => $_REQUEST ['valor'],
				'iva' => $_REQUEST ['iva'],
				'subtotal' => $_REQUEST ['subtotal_sin_iva'],
				'subtotal_sin_iva' => $_REQUEST ['subtotal_sin_iva'],
				'total_iva' => $_REQUEST ['total_iva'],
				'total_iva_con' => $_REQUEST ['total_iva_con'],
				'funcionario_encargado' => $_REQUEST ['funcionario_salida'],
				'tipo_bien' => $seleccion ['tipo_bien'],
				'nivel' => $seleccion ['nivel'] 
		);
		
		$arreglo = array (
				'id_elemento' => $elemento_id_max,
				'fecha_actual' => $fechaActual,
				'nivel' => $seleccion ['nivel'],
				'tipo_bien' => $seleccion ['tipo_bien'],
				'descripcion' => $_REQUEST ['descripcion'],
				'cantidad' => $_REQUEST ['cantidad'],
				'unidad' => $_REQUEST ['unidad'],
				'valor' => $_REQUEST ['valor'],
				'iva' => $_REQUEST ['iva'],
				'ajuste' => 0,
				'bodega' => 0,
				'subtotal_sin_iva' => $_REQUEST ['subtotal_sin_iva'],
				'total_iva' => $_REQUEST ['total_iva'],
				'total_iva_con' => $_REQUEST ['total_iva_con'],
				'poliza' => 0,
				'marca' => $_REQUEST ['marca'],
				'serie' => $_REQUEST ['serie'],
				'id_entrada' => $idEntradamax 
		);
		
		if ($seleccion ['tipo_bien'] == 1) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultar_placa', '1' );
			$placa = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento', $arreglo );
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, "ingresar_elemento" );
		} else if ($seleccion ['tipo_bien'] == 2) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento', $arreglo );
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, "ingresar_elemento" );
		} else if ($seleccion ['tipo_bien'] == 3) {
			$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento', $arreglo );
			$elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo, "ingresar_elemento" );
		}
		
		$placa = date ( 'Ymd' ) . "00000";
		$cadenaSql = $this->miSql->getCadenaSql ( 'buscar_repetida_placa', $placa );
		$num_placa = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$cadenaSql = $this->miSql->getCadenaSql ( 'idElementoMaxIndividual' );
		$elemento_id_max_indiv = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$num_placa [0] [0];
		$sumaplaca = 0;
		
		if ($num_placa [0] [0] == 0) {
			for($i = 0; $i < $_REQUEST ['cantidad']; $i ++) {
				$arregloElementosInv = array (
						$fechaActual,
						($seleccion ['tipo_bien'] == 1) ? NULL : $placa + $sumaplaca,
						($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : null,
						$elemento [0] [0] 
				)
				// $elemento_id_max_indiv[0][0]
				;
				
				$sumaplaca = ($seleccion ['tipo_bien'] == 1) ? $sumaplaca : $sumaplaca ++;
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_individual', $arregloElementosInv );
				$elemento_id [$i] = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arregloElementosInv, "ingresar_elemento_individual" );
			}
		} else if ($num_placa [0] [0] != 0) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'buscar_placa_maxima', $placa );
			$num_placa = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			$placa = $num_placa [0] [0];
			$sumaplaca = 1;
			
			for($i = 1; $i <= $_REQUEST ['cantidad']; $i ++) {
				$arregloElementosInv = array (
						$fechaActual,
						($seleccion ['tipo_bien'] == 1) ? NULL : $placa + $sumaplaca,
						($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : null,
						$elemento [0] [0] 
				)
				// $elemento_id_max_indiv[0][0]
				;
				
				$sumaplaca = ($seleccion ['tipo_bien'] == 1) ? $sumaplaca : $sumaplaca ++;
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_individual', $arregloElementosInv );
				$elemento_id [$i] = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arregloElementosInv, "ingresar_elemento_individual" );
			}
		}
		
		// -------------------------------------- Crear Salida
		
		$salida = array (
				'fecha_registro' => date ( 'Y-m-d' ),
				'vigencia' => date ( 'Y' ),
				'id_entrada' => '',
				'consecutivo_salida' => '',
				'sede' => $_REQUEST ['sede_salida'],
				'dependencia' => $_REQUEST ['dependencia_salida'],
				'ubicacion' => $_REQUEST ['ubicacion_salida'],
				'funcionario' => $_REQUEST ['funcionario_salida'],
				'observaciones' => $_REQUEST ['observaciones'] 
		);
		
		$fechaReinicio = date ( "Y-m-d", mktime ( 0, 0, 0, 1, 1, date ( 'Y' ) ) );
		
		if ($fechaActual == $fechaReinicio) {
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'consultaConsecutivo_salida', $fechaReinicio );
			$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			if (isset ( $consecutivo ) && $consecutivo == false) {
				$cadenaSql = $this->miSql->getCadenaSql ( 'reiniciarConsecutivo_salida' );
				$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
			}
		}
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'id_salida_maximo' );
		
		$max_id_salida = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$max_id_salida = $max_id_salida [0] [0] + 1;
		
		$arreglo = array (
				$fechaActual,
				$_REQUEST ['dependencia_salida'],
				$_REQUEST ['funcionario_salida'],
				$_REQUEST ['observaciones'],
				$idEntradamax,
				$_REQUEST ['sede_salida'] ? $_REQUEST ['sede_salida'] : 'null',
				($_REQUEST ['ubicacion_salida'] != '') ? $_REQUEST ['ubicacion_salida'] : 'null',
				date ( 'Y' ),
				$max_id_salida 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertar_salida', $arreglo );
		$id_salida = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda",$arreglo,"insertar_salida" );
		
		$arreglo = array (
				$elemento_id_max_indiv [0] [0],
				$max_id_salida,
				$_REQUEST ['funcionario_salida'],
				$_REQUEST ['ubicacion_salida'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizar_elementos_individuales', $arreglo );
		$actualizo_elem = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $arreglo, "actualizar_elementos_individuales" );
		
		// ----- Salidas Contables ----------
		
		switch ($seleccion ['tipo_bien']) {
			case '1' :
				$arreglo = array (
						date ( 'Y' ),
						$seleccion ['tipo_bien'] 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'SalidaContableVigencia', $arreglo );
				$max_consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if (is_null ( $max_consecutivo [0] [0] )) {
					$salidaConsecutiva = 1;
				} else {
					$salidaConsecutiva = $max_consecutivo [0] [0] + 1;
				}
				
				$arreglo_salida_contable = array (
						$fechaActual,
						$max_id_salida,
						$seleccion ['tipo_bien'],
						date ( 'Y' ),
						$salidaConsecutiva 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'InsertarSalidaContable', $arreglo_salida_contable );
				$id_salida_contable = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo_salida_contable, "InsertarSalidaContable" );
				
				break;
			
			case '2' :
				
				$arreglo = array (
						$_REQUEST ['vigencia'],
						$elemento ['tipo_bien'] 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'SalidaContableVigencia', $arreglo );
				$max_consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if (is_null ( $max_consecutivo [0] [0] )) {
					
					$salidaConsecutiva = 1;
				} else {
					
					$salidaConsecutiva = $max_consecutivo [0] [0] + 1;
				}
				
				$arreglo_salida_contable = array (
						$fechaActual,
						$max_id_salida,
						$seleccion ['tipo_bien'],
						date ( 'Y' ),
						$salidaConsecutiva 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'InsertarSalidaContable', $arreglo_salida_contable );
				$id_salida_contable = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo_salida_contable, "InsertarSalidaContable" );
				
				break;
			
			case '3' :
				
				$arreglo = array (
						date ( 'Y' ),
						$seleccion ['tipo_bien'] 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'SalidaContableVigencia', $arreglo );
				$max_consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
				
				if (is_null ( $max_consecutivo [0] [0] )) {
					$salidaConsecutiva = 1;
				} else {
					$salidaConsecutiva = $max_consecutivo [0] [0] + 1;
				}
				
				$arreglo_salida_contable = array (
						$fechaActual,
						$max_id_salida,
						$seleccion ['tipo_bien'],
						date ( 'Y' ),
						$salidaConsecutiva 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'InsertarSalidaContable', $arreglo_salida_contable );
				$id_salida_contable = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo_salida_contable, "InsertarSalidaContable" );
				break;
		}
		
		$repo = array (
				'id_estado_elemento' => $seleccion ['id_estado_elemento'],
				'id_info' => $info_clase [0] [0],
				'id_entrada' => $id_entrada [0] [0],
				'id_salida' => $id_salida [0] [0],
				'id_elemento' => $arregloElementosInv [1],
				"usuario" => $_REQUEST ['usuario'] 
		);
	
		$cadenaSql = $this->miSql->getCadenaSql ( 'reposicionRegistro', $repo );
		$reposicion = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $repo, "reposicionRegistro" );
		
		if ($actualizo_elem == true && $reposicion == TRUE) {
			$this->miConfigurador->setVariableConfiguracion ( "cache", true );
			redireccion::redireccionar ( 'inserto', $repo );
		} else {
			redireccion::redireccionar ( 'noInserto' ,$_REQUEST['usuario']);
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