<?php

namespace inventarios\gestionActa\registrarActa\funcion;

use inventarios\gestionActa\registrarActa\funcion\redireccion;

include_once ('redireccionar.php');

$ruta_1 = $this->miConfigurador->getVariableConfiguracion ( 'raizDocumento' ) . '/plugin/php_excel/Classes/PHPExcel.class.php';
$ruta_2 = $this->miConfigurador->getVariableConfiguracion ( 'raizDocumento' ) . '/plugin/php_excel/Classes/PHPExcel/Reader/Excel2007.class.php';

include_once ($ruta_1);
include_once ($ruta_2);

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class RegistradorActa {
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
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionActa/";
		$rutaBloque .= $esteBloque ['nombre'];
		
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionActa/" . $esteBloque ['nombre'];
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		// ----homologacion Dependencias
		/*
		 * $cadenaSql = $this->miSql->getCadenaSql ( 'ids' ); $id_actas = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" ); foreach ($id_actas as $doc){ $cadenaSql = $this->miSql->getCadenaSql ( 'update_dependencia', $doc); $succeso = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" ); }
		 */
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'items', $_REQUEST ['seccion'] );
		// $items = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// if ($items == false) {
		
		// redireccion::redireccionar ( 'noItems' );
		// }
		
		foreach ( $_FILES as $key => $values ) {
			
			$archivo = $_FILES [$key];
		}
		
		// $archivoImagen = $archivo [1];
		
		// if ($archivoImagen ['error'] == 0) {
		
		// if ($archivoImagen ['type'] != 'image/jpeg') {
		// redireccion::redireccionar ( 'noFormatoImagen' );
		
		// exit ();
		// }
		// }
		
		if ($_FILES ['documentoSoporte'] ['name'] != '') {
			
			// obtenemos los datos del archivo
			$tamano = $archivo ['size'];
			$tipo = $archivo ['type'];
			$archivo1 = $archivo ['name'];
			$prefijo = substr ( md5 ( uniqid ( rand () ) ), 0, 6 );
			
			if ($archivo1 != "") {
				// guardamos el archivo a la carpeta files
				$destino1 = $rutaBloque . "/soportes/" . $prefijo . "_" . $archivo1;
				if (copy ( $archivo ['tmp_name'], $destino1 )) {
					$status = "Archivo subido: <b>" . $archivo1 . "</b>";
					$destino1 = $host . "/soportes/" . $prefijo . "_" . $archivo1;
				} else {
					$status = "Error al subir el archivo";
				}
			} else {
				$status = "Error al subir archivo";
			}
		} else {
			
			$destino1 = NULL;
			$archivo1 = NULL;
		}
		
		if (isset ( $_REQUEST ['tipoOrden'] )) {
			
			switch ($_REQUEST ['tipoOrden']) {
				
				case 1 :
					$tipoOrden = 1;
					break;
				
				case 2 :
					$tipoOrden = 2;
					break;
			}
		} else {
			$tipoOrden = 0;
		}
		
		// Registro del Acta de Recibido
		
		$datosActa = array (
				'sede' => $_REQUEST ['sede'],
				'dependencia' => $_REQUEST ['dependencia'],
				'fecha_registro' => $fechaActual,
				'tipo_bien' => '0',
				'nitproveedor' => ($_REQUEST ['id_proveedor'] != '') ? $_REQUEST ['id_proveedor'] : null,
				'ordenador' => ($_REQUEST ['id_ordenador'] != '') ? $_REQUEST ['id_ordenador'] : null,
				'fecha_revision' => $_REQUEST ['fecha_revision'],
				'revisor' => NUll,
				'observacion' => $_REQUEST ['observacionesActa'],
				'estado' => 1,
				'tipo_orden' => $tipoOrden,
				'numero_orden' => (isset ( $_REQUEST ['numero_orden'] )) ? "'".$_REQUEST ['numero_orden']."'" : "NULL",
				'enlace_soporte' => $destino1,
				'nombre_soporte' => $archivo1,
				'identificador_contrato' => ($_REQUEST ['numeroContrato'] != '') ? $_REQUEST ['numeroContrato'] : NULL 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarActa', $datosActa );
		
		$id_acta = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda",$datosActa,'insertarActa' );
		
		if (isset ( $_REQUEST ['numero_orden'] )) {
			
			// Rescatar los Elementos acta
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'ConsultaElementosOrden', $_REQUEST ['numero_orden'] );
			
			$elementos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			// Registrar al acta lo elementos de la Orden
			
			foreach ( $elementos as $valor ) {
				
				
				$arreglo=array (
						$valor [0],
						$id_acta [0] [0] 
				);
				
				
				
				
				$cadenaSql = $this->miSql->getCadenaSql ( 'RegistrarActaElementos', $arreglo);
				
				$elementos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso",$arreglo,'RegistrarActaElementos');
				
			}
		}
		
		$datos = array (
				$id_acta [0] [0],
				$fechaActual,
				$tipoOrden,
				$_REQUEST['usuario']
		);
		
		if ($id_acta) {
			$this->miConfigurador->setVariableConfiguracion("cache",true);
			redireccion::redireccionar ( 'insertoActa', $datos );
			exit ();
		} else {
			
			redireccion::redireccionar ( 'noInserto', $_REQUEST['usuario'] );
			
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

$miRegistrador = new RegistradorActa ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();
?>