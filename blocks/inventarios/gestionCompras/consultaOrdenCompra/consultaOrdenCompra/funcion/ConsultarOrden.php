<?php

namespace inventarios\gestionCompras\consultaOrdenCompra\funcion;

use inventarios\gestionCompras\consultaOrdenCompra\funcion\redireccion;

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
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'items' );
		$items = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		if ($items == 0) {
			
			redireccion::redireccionar ( 'noItems' );
		}
		if ($_REQUEST ['obligacionesProveedor'] == '') {
			
			redireccion::redireccionar ( 'noObligaciones' );
		}
		
		if ($_REQUEST ['obligacionesContratista'] == '') {
			
			redireccion::redireccionar ( 'noObligaciones' );
		}
		
		// Archivo de Cotizacion
		if ($_FILES) {
			// obtenemos los datos del archivo
			$tamano = $_FILES ["proveedorCotizacion"] ['size'];
			$tipo = $_FILES ["proveedorCotizacion"] ['type'];
			$archivo1 = $_FILES ["proveedorCotizacion"] ['name'];
			$prefijo = substr ( md5 ( uniqid ( rand () ) ), 0, 6 );
			
			if ($archivo1 != "") {
				// guardamos el archivo a la carpeta files
				$destino1 = $rutaBloque . "/cotizaciones/" . $prefijo . "_" . $archivo1;
				if (copy ( $_FILES ['proveedorCotizacion'] ['tmp_name'], $destino1 )) {
					$status = "Archivo subido: <b>" . $archivo1 . "</b>";
					$destino1 = $host . "/cotizaciones/" . $prefijo . "_" . $archivo1;
				} else {
					$status = "Error al subir el archivo";
				}
			} else {
				$status = "Error al subir archivo";
			}
		}
		
		$datosProveedor = array (
				$_REQUEST ['proveedor'],
				$_REQUEST ['nitProveedor'],
				$_REQUEST ['direccionProveedor'],
				$_REQUEST ['telefonoProveedor'],
				$destino1,
				$archivo1 
		);
		
		// Registro Proveedor
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarProveedor', $datosProveedor );
		$id_proveedor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$datosDependencia = array (
				$_REQUEST ['dependencia'],
				$_REQUEST ['direccionDependencia'],
				$_REQUEST ['telefonoDependencia'] 
		);
		
		// Registro Dependencia
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarDependencia', $datosDependencia );
		$id_dependencia = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$datosContratista = array (
				'3',
				$_REQUEST ['nombreContratista'],
				$_REQUEST ['identificacionContratista'],
				'NULL',
				'NULL' 
		);
		
		$datosjefe = array (
				'2',
				$_REQUEST ['nombreJefeSeccion'],
				'NULL',
				$_REQUEST ['cargoJefeSeccion'],
				'NULL' 
		);
		
		$datosOrdenador = array (
				'1',
				$_REQUEST ['nombreOrdenador'],
				'NULL',
				'NULL',
				$_REQUEST ['asignacionOrdenador'] 
		);
		
		// Registro Encargados
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarEncargado', $datosContratista );
		$id_contratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarEncargado', $datosjefe );
		$id_jefe = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarEncargado', $datosOrdenador );
		$id_ordenador = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// Registro Orden
		
		$datosOrden = array (
				$fechaActual,
				$_REQUEST ['diponibilidad'],
				$_REQUEST ['fecha_diponibilidad'],
				$_REQUEST ['rubro'],
				$_REQUEST ['obligacionesProveedor'],
				$_REQUEST ['obligacionesContratista'],
				isset ( $_REQUEST ['poliza1'] ),
				isset ( $_REQUEST ['poliza2'] ),
				isset ( $_REQUEST ['poliza3'] ),
				isset ( $_REQUEST ['poliza4'] ),
				isset ( $_REQUEST ['poliza5'] ),
				$_REQUEST ['lugarEntrega'],
				$_REQUEST ['destino'],
				$_REQUEST ['tiempoEntrega'],
				$_REQUEST ['formaPago'],
				$_REQUEST ['supervision'],
				$_REQUEST ['inhabilidades'],
				$id_proveedor [0] [0],
				$id_dependencia [0] [0],
				$id_contratista [0] [0],
				$id_jefe [0] [0],
				$id_ordenador [0] [0] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarOrden', $datosOrden );
		$id_orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		foreach ( $items as $contenido ) {
			
			$datosItems = array (
					$id_orden [0] [0],
					$contenido ['item'],
					$contenido ['unidad_medida'],
					$contenido ['cantidad'],
					$contenido ['descripcion'],
					$contenido ['valor_unitario'],
					$contenido ['valor_total'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'insertarItems', $datosItems );
			$items = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		}
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'limpiar_tabla_items' );
		$resultado_secuancia = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		
		$datos = array (
				$id_orden [0] [0],
				$fechaActual 
		);
		
		if ($items == 1) {
			
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