<?php

namespace inventarios\gestionCompras\registrarOrdenCompra\funcion;

use inventarios\gestionCompras\registrarOrdenCompra\funcion\redireccion;

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
		foreach ( $_FILES as $key => $values ) {
			
			$archivo = $_FILES [$key];
		}
		
		$fechaActual = date ( 'Y-m-d' );
		
		$miSesion = \Sesion::singleton ();
		
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionCompras/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/gestionCompras/" . $esteBloque ['nombre'];
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'items', $_REQUEST ['seccion'] );
		
		$items = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		if ($items == 0) {
			
			redireccion::redireccionar ( 'noItems' );
			exit();
		}
		if ($_REQUEST ['obligacionesProveedor'] == '') {
			
			redireccion::redireccionar ( 'noObligaciones' );
			exit();
		}
		
		if ($_REQUEST ['obligacionesContratista'] == '') {
			
			redireccion::redireccionar ( 'noObligaciones' );
			exit();
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
		
		// Archivo de Cotizacion
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
		
		$_REQUEST ['selec_proveedor'] = $_REQUEST ['selec_proveedor'];
		
		// Registro Orden
		
		$arreglo = array (
				$fechaActual,
				$_REQUEST ['vigencia_disponibilidad'],
				$_REQUEST ['diponibilidad'],
				$_REQUEST ['valor_disponibilidad'],
				$_REQUEST ['fecha_diponibilidad'],
				$_REQUEST ['valorLetras_disponibilidad'],
				$_REQUEST ['vigencia_registro'],
				$_REQUEST ['registro'],
				$_REQUEST ['valor_registro'],
				$_REQUEST ['fecha_registro'],
				$_REQUEST ['valorL_registro'] 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'insertarInformacionPresupuestal', $arreglo );
		$info_presupuestal = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$datosOrden = array (
				$fechaActual,
				$info_presupuestal [0] [0],
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
				$_REQUEST ['id_ordenador'],
				$Subtotal,
				$iva,
				$total,
				$_REQUEST ['valorLetras_registro'],
				'TRUE',
				$_REQUEST ['sede'] 
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
					$contenido ['valor_total'],
					$contenido ['descuento'] 
			);
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'insertarItems', $datosItems );
			$items = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		}
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'limpiar_tabla_items', $_REQUEST ['seccion'] );
		$resultado_secuancia = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		
		$datos = array (
				$id_orden [0] [0],
				$fechaActual 
		);
		
		// //----- Registrar Evento
		
		// $arregloLogEvento = array (
		// 'insertar_orden_compra',
		// $datosOrden,
		// $miSesion->getSesionUsuarioId (),
		// $_SERVER ['REMOTE_ADDR'],
		// $_SERVER ['HTTP_USER_AGENT']
		// );
		// $argumento = json_encode ( $arregloLogEvento );
		// $arregloFinalLogEvento = array (
		// $miSesion->getSesionUsuarioId (),
		// $argumento
		// );
		// $cadena_sql = $this->sql->cadena_sql ( "registrarEvento", $arregloFinalLogEvento );
		// $registroAcceso = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "acceso" );
		
		// //---------
		
		if ($items == true) {
			
			redireccion::redireccionar ( 'inserto', $datos );
			exit ();
		} else {
			
			redireccion::redireccionar ( 'noInserto', $datos );
			
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