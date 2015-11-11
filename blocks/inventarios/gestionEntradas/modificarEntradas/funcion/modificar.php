<?php

namespace inventarios\gestionEntradas\modificarEntradas;

use inventarios\gestionEntradas\modificarEntradas\funcion;

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
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionEntradas/";
		$rutaBloque .= "registrarEntradas";
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionEntradas/registrarEntradas";
		
		$i = 0;
		
		switch ($_REQUEST ['clase']) {
			
			case '1' :
				
				// $observacion = $_REQUEST ['observaciones_reposicion'];
				$entrada = $_REQUEST ['id_entradaR'];
				$salida = $_REQUEST ['id_salidaR'];
				
				break;
			
			case '2' :
				// $observacion = $_REQUEST ['observaciones_donacion'];
				foreach ( $_FILES as $key => $values ) {
					
					$archivo [$i] = $_FILES [$key];
					$i ++;
				}
				
				$archivo = $archivo [0];
				break;
			
			case '3' :
				
				// // $observacion = $_REQUEST ['observaciones_sobrante'];
				// $entrada = $_REQUEST ['id_entradaS'];
				// $salida = $_REQUEST ['id_salidaS'];
				
				foreach ( $_FILES as $key => $values ) {
					
					$archivo [$i] = $_FILES [$key];
					$i ++;
				}
				
				$archivo = $archivo [1];
				$_REQUEST ['id_proveedor']='';
				
				break;
			
			case '4' :
				// $observacion = $_REQUEST ['observaciones_produccion'];
				foreach ( $_FILES as $key => $values ) {
					
					$archivo [$i] = $_FILES [$key];
					$i ++;
				}
				
				$archivo = $archivo [2];
				
				break;
			
			case '5' :
				
				// $observacion = $_REQUEST ['observaciones_recuperacion'];
				foreach ( $_FILES as $key => $values ) {
					
					$archivo [$i] = $_FILES [$key];
					$i ++;
				}
				
				$archivo = $archivo [3];
				break;
			case '6' :
				
				// $observacion = $_REQUEST ['observaciones_adquisicion'];
				foreach ( $_FILES as $key => $values ) {
					
					$archivo [$i] = $_FILES [$key];
					$i ++;
				}
				
				$archivo = $archivo [4];
				break;
			
			case '7' :
				
				// $observacion = $_REQUEST ['observaciones_avance'];
				foreach ( $_FILES as $key => $values ) {
					
					$archivo [$i] = $_FILES [$key];
					$i ++;
				}
				
				$archivo = $archivo [5];
				break;
		}
		// var_dump($archivo);
		if (isset ( $archivo ) && $archivo ['name'] != '') {
			// obtenemos los datos del archivo
			$tamano = $archivo ['size'];
			$tipo = $archivo ['type'];
			$archivo1 = $archivo ['name'];
			$prefijo = substr ( md5 ( uniqid ( rand () ) ), 0, 6 );
			
			if ($archivo1 != "") {
				// guardamos el archivo a la carpeta files
				$destino1 = $rutaBloque . "/actas/" . $prefijo . "_" . $archivo1;
				if (copy ( $archivo ['tmp_name'], $destino1 )) {
					$status = "Archivo subido: <b>" . $archivo1 . "</b>";
					$destino1 = $host . "/actas/" . $prefijo . "_" . $archivo1;
				} else {
					$status = "Error al subir el archivo 1";
				}
			} else {
				$status = "Error al subir archivo 2	";
			}
			if ($_REQUEST ['clase_info'] != '') {
				
				$arreglo_clase = array (
						$observacion = 'NULL',
						(isset ( $entrada )) ? $entrada : 0,
						(isset ( $salida )) ? $salida : 0,
						($_REQUEST ['clase'] == 1) ? $_REQUEST ['id_hurtoR'] : 0,
						0,
						0,
						(isset ( $destino1 )) ? $destino1 : 'NULL',
						(isset ( $archivo1 )) ? $archivo1 : 'NULL',
						$_REQUEST ['clase_info'] 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarInformacionArchivo', $arreglo_clase );
				
				$info_clase = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo_clase, "actualizarInformacionArchivo" );
			} else {
				
				$arreglo_clase = array (
						$observacion = 'NULL',
						(isset ( $entrada )) ? $entrada : 0,
						(isset ( $salida )) ? $salida : 0,
						($_REQUEST ['clase'] == 1) ? $_REQUEST ['id_hurtoR'] : 0,
						($_REQUEST ['clase'] == 3) ? $_REQUEST ['num_placa'] : 0,
						($_REQUEST ['clase'] == 3) ? $_REQUEST ['valor_sobrante'] : 0,
						(isset ( $destino1 )) ? $destino1 : 'NULL',
						(isset ( $archivo1 )) ? $archivo1 : 'NULL' 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'insertarInformación', $arreglo_clase );
				$info_clase = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo_clase, "insertarInformación" );
				$_REQUEST ['clase_info'] = $info_clase [0] [0];
			}
		} else {
			
			if ($_REQUEST ['clase_info'] != '') {
				
				$arreglo_clase = array (
						$observacion = 'NULL',
						(isset ( $entrada )) ? $entrada : 0,
						(isset ( $salida )) ? $salida : 0,
						($_REQUEST ['clase'] == 1) ? $_REQUEST ['id_hurtoR'] : 0,
						($_REQUEST ['clase'] == 3) ? $_REQUEST ['num_placa'] : 0,
						($_REQUEST ['clase'] == 3) ? $_REQUEST ['valor_sobrante'] : 0,
						$_REQUEST ['clase_info'] 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarInformacion', $arreglo_clase );
				
				$info_clase = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo_clase, "actualizarInformacion" );
			} else {
				
				$arreglo_clase = array (
						$observacion = 'NULL',
						(isset ( $entrada )) ? $entrada : 0,
						(isset ( $salida )) ? $salida : 0,
						($_REQUEST ['clase'] == 1) ? $_REQUEST ['id_hurtoR'] : 0,
						($_REQUEST ['clase'] == 3) ? $_REQUEST ['num_placa'] : 0,
						($_REQUEST ['clase'] == 3) ? $_REQUEST ['valor_sobrante'] : 0,
						(isset ( $destino1 )) ? $destino1 : 'NULL',
						(isset ( $archivo1 )) ? $archivo1 : 'NULL' 
				);
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'insertarInformación', $arreglo_clase );
				$info_clase = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arreglo_clase, "insertarInformación" );
				$_REQUEST ['clase_info'] = $info_clase [0] [0];
			}
		}
		
		$arregloDatos = array (
				$_REQUEST ['vigencia'],
				$_REQUEST ['clase'],
				($_REQUEST ['tipo_contrato'] != '') ? $_REQUEST ['tipo_contrato'] : 0,
				($_REQUEST ['numero_contrato'] != '') ? $_REQUEST ['numero_contrato'] : 0,
				($_REQUEST ['fecha_contrato'] != '') ? "'".$_REQUEST ['fecha_contrato']."'" : "NULL",
				($_REQUEST ['id_proveedor'] != '') ? "'".$_REQUEST ['id_proveedor']."'" : "NULL",
				($_REQUEST ['numero_factura'] != '') ?"'". $_REQUEST ['numero_factura']."'" :"NULL" ,
				($_REQUEST ['fecha_factura'] != '') ? "'".$_REQUEST ['fecha_factura']."'" : "NULL",
				$_REQUEST ['observaciones_entrada'],
				$_REQUEST ['numero_entrada'],
				"Estado_entrada" => '1',
				(isset ( $_REQUEST ['acta_recibido'] ) && $_REQUEST ['acta_recibido'] != '') ? $_REQUEST ['acta_recibido'] : 0,
				($_REQUEST ['id_ordenador'] == '') ? 'NULL' : $_REQUEST ['id_ordenador'],
				$_REQUEST ['sede'],
				$_REQUEST ['dependencia'],
				$_REQUEST ['supervisor'],
				($_REQUEST ['tipo_ordenador'] == '') ? 'NULL' : $_REQUEST ['tipo_ordenador'],
				($_REQUEST ['identificacion_ordenador'] == '') ? 'NULL' : $_REQUEST ['identificacion_ordenador'],
				$_REQUEST ['clase_info'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarEntrada', $arregloDatos );
		
		$id_entrada = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda", $arregloDatos, "actualizarEntrada" );
		 
		if ($id_entrada) {
			
			redireccion::redireccionar ( 'inserto', array (
					$id_entrada [0] [0],
					$_REQUEST ['usuario'] 
			) );
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