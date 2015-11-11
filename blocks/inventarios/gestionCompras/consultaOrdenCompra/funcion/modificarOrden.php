<?php

namespace inventarios\gestionCompras\consultaOrdenServicios\funcion;

use inventarios\gestionCompras\consultaOrdenServicios\funcion;
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
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionCompras/";
		$rutaBloque .= 'registrarOrdenCompra';
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionCompras/registrarOrdenCompra/";
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'items', $_REQUEST ['seccion'] );
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
		
		$Subtotal = 0;
		
		foreach ( $items as $n ) {
			$Subtotal = $Subtotal + $n [6];
		}
		
		if ($_REQUEST ['iva'] == 6) {
			
			$iva = $Subtotal * 0.16;
		} else if ($_REQUEST ['iva'] == 5) {
			
			$iva = $Subtotal * 0.10;
		} else {
			$iva = 0;
		}
		
		$total = $Subtotal + $iva;
		
		if ($_REQUEST ['actualizarCotizacion'] == '1') {
			
			// Archivo de Cotizacion
			foreach ( $_FILES as $key => $values ) {
				
				$archivo = $_FILES [$key];
			}
			
			if ($archivo) {
				// obtenemos los datos del archivo
				$tamano = $archivo ['size'];
				$tipo = $archivo ['type'];
				$archivo1 = $archivo ['name'];
				$prefijo = substr ( md5 ( uniqid ( rand () ) ), 0, 6 );
				
				if ($archivo1 != "") {
					// guardamos el archivo a la carpeta files
					$destino1 = $rutaBloque . "/cotizaciones/" . $prefijo . "_" . $archivo1;
					if (copy ( $archivo ['tmp_name'], $destino1 )) {
						$status = "Archivo subido: <b>" . $archivo1 . "</b>";
						$destino1 = $host . "/cotizaciones/" . $prefijo . "_" . $archivo1;
					} else {
						$status = "Error al subir el archivo";
					}
				} else {
					$status = "Error al subir archivo";
				}
			}
		} else if ($_REQUEST ['actualizarCotizacion'] == '0') {
			
			$destino1 = $_REQUEST ['directorio'];
			$archivo1 = $_REQUEST ['nombreArchivo'];
		}
		
	
			
			$_REQUEST ['selec_proveedor'] = $_REQUEST ['selec_proveedor'];
	
		$arreglo = array (
				$_REQUEST ['vigencia_disponibilidad'],
				$_REQUEST ['diponibilidad'],
				$_REQUEST ['valor_disponibilidad'],
				$_REQUEST ['fecha_diponibilidad'],
				$_REQUEST ['valorLetras_disponibilidad'],
				$_REQUEST ['vigencia_registro'],
				$_REQUEST ['registro'],
				$_REQUEST ['valor_registro'],
				$_REQUEST ['fecha_registro'],
				$_REQUEST ['valorL_registro'],
				$_REQUEST ['infoPresupuestal'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarPresupuestal', $arreglo );
		
		$inf_pre = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		
		// Actualizar Orden
		
		$datosOrden = array (
				$_REQUEST ['rubro'],
				$_REQUEST ['obligacionesProveedor'],
				$_REQUEST ['obligacionesContratista'],
				isset ( $_REQUEST ['polizaA'] ),
				isset ( $_REQUEST ['polizaB'] ),
				isset ( $_REQUEST ['polizaC'] ),
				isset ( $_REQUEST ['polizaD'] ),
				isset ( $_REQUEST ['polizaE'] ),
				$_REQUEST ['lugarEntrega'],
				$_REQUEST ['destino'],
				$_REQUEST ['tiempoEntrega'],
				$_REQUEST ['formaPago'],
				$_REQUEST ['supervision'],
				$_REQUEST ['inhabilidades'],
				$_REQUEST ['selec_proveedor'],
				$destino1,
				$archivo1,
				$_REQUEST ['selec_dependencia'],
				$_REQUEST ['id_ordenador_oculto'],
				$Subtotal,
				$iva,
				$total,
				$_REQUEST ['valorLetras_registro'],
				$_REQUEST ['numero_orden'],
				$_REQUEST ['sede']  ,
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'actualizarOrden', $datosOrden );
		
		
		$id_orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		
		
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'limpiarItems', $_REQUEST ['numero_orden'] );
		
		$limpiar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		foreach ( $items as $contenido ) {
			
			$datosItems = array (
					$_REQUEST ['numero_orden'],
					$contenido ['item'],
					$contenido ['unidad_medida'],
					$contenido ['cantidad'],
					$contenido ['descripcion'],
					$contenido ['valor_unitario'],
					$contenido ['valor_total'],
					$contenido ['descuento'] 
			)
			;
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'insertarItems', $datosItems );
			$items = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		}
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'limpiar_tabla_items', $_REQUEST ['seccion'] );
		$resultado_secuancia = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		
		$datos = array (
				$_REQUEST ['numero_orden'] 
		);
		
		
		if ($items == true) {
			
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