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
		
		$fechaActual = date ( 'Y-m-d' );
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionActa/registrarActa";
// 		$rutaBloque .= $esteBloque ['nombre'];
		
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionActa/registrarActa";
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

		foreach ( $_FILES as $key => $values ) {
			
			$archivo = $_FILES [$key];
		}
		
		if ($archivo['name']!='') {
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
		}
		
		$fechaActual = date ( 'Y-m-d' );
		// Actualizar Acta de Recibido
		
		if ($archivo['name']!='') {
			
			$datosActa = array (
					'sede' => $_REQUEST ['sede'],
					'dependencia' => $_REQUEST ['dependencia'],
					'fecha_registro' => $fechaActual,
					'tipo_bien' => 0,
					'nit_proveedor' => $_REQUEST ['id_proveedor'],
					'ordenador' => $_REQUEST ['id_ordenador'],
					'fecha_revision' => $_REQUEST ['fecha_revision'],
					'revisor' => NULL,
					'observaciones' => $_REQUEST ['observacionesacta'],
					'estado' => 1,
					'enlace_soporte' => $destino1,
					'nombre_soporte' => $archivo1,
					'id_acta' => $_REQUEST ['id_acta'],
					'identificador_contrato' => ($_REQUEST ['numeroContrato']!='') ? $_REQUEST ['numeroContrato'] : 0, 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarActa_soporte', $datosActa );
			$id_acta = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
			
			
			
		} else {
			$datosActa = array (
					'sede' => $_REQUEST ['sede'],
					'dependencia' => $_REQUEST ['dependencia'],
					'fecha_registro' => $fechaActual,
					'tipo_bien' => 0,
					'nit_proveedor' => $_REQUEST ['id_proveedor'],
					'ordenador' => $_REQUEST ['id_ordenador'],
					'fecha_revision' => $_REQUEST ['fecha_revision'],
					'revisor' => NULL,
					'observaciones' => $_REQUEST ['observacionesacta'],
					'estado' => 1,
					'id_acta' => $_REQUEST ['id_acta'],
					'identificador_contrato' => ($_REQUEST ['numeroContrato']!='') ? $_REQUEST ['numeroContrato'] : 0,
			);
			
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarActa', $datosActa );
			$id_acta = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
			
			
			
		}


		$datos = array (
				$_REQUEST ['id_acta'],
				$fechaActual ,
				$_REQUEST['arreglo']
		);

		

		if ($id_acta) {
			$this->miConfigurador->setVariableConfiguracion("cache",true);
			redireccion::redireccionar ( 'inserto', $datos );
			exit();
		} else {
			
			redireccion::redireccionar ( 'noInserto', $datos );
			exit();
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