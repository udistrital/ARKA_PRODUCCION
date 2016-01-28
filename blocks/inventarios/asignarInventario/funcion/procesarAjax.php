<?php
use inventarios\asignarInventarioC\asignarInventario\Sql;

include_once ("core/builder/FormularioHtml.class.php");

$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );

$this->miFormulario = new \FormularioHtml ();

$atributosGlobales ['campoSeguro'] = 'true';
$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );

$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/";
$rutaBloque .= $esteBloque ['grupo'] . '/' . $esteBloque ['nombre'];

$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

if ($_REQUEST ['funcion'] = "ConsultarInventario") {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarElementosSupervisor', $_REQUEST ['funcionario'] );
	
	$elementos_supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$tab = 1;
	
	$i = 0;
	
	

	
	
	foreach ( $elementos_supervisor as $valor ) {
		
		// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
		$nombre = 'item' . $i;
		$atributos ['id'] = $nombre;
		$atributos ['nombre'] = $nombre;
		$atributos ['marco'] = true;
		$atributos ['estiloMarco'] = true;
		$atributos ["etiquetaObligatorio"] = true;
		$atributos ['columnas'] = 1;
		$atributos ['dobleLinea'] = 1;
		$atributos ['tabIndex'] = $tab;
		$atributos ['etiqueta'] = '';
		$atributos ['seleccionado'] = false;
		$atributos ['evento'] = ' ';
		$atributos ['eventoFuncion'] = ' ';
		$atributos ['valor'] = $valor['id_elemento_ind'];
		$atributos ['deshabilitado'] = false;
		$tab ++;
		
		// Aplica atributos globales al control
		$atributos = array_merge ( $atributos, $atributosGlobales );
		
		
		$item =  $this->miFormulario->campoCuadroSeleccion ( $atributos );
		
		
		// { data :"id_elemento" },
		// { data :"nivel" },
		// { data :"marca" },
		// { data :"placa" },
		// { data :"serie" },
		// { data :"valor_unitario" },
		// { data :"sub_total" },
		// { data :"total_iva" },
		// { data :"total_ajustado" },
		// { data :"seleccion" },
		
		
		
		$resultadoFinal [] = array (
				'placa' => "<center>" . $valor ['placa'] . "</center>",
				'descripcion' => "<center>" . $valor ['descripcion'] . "</center>",
				'marca' => "<center>" . $valor ['marca'] . "</center>",
				'serie' => "<center>" . $valor ['serie'] . "</center>",
				'sede' => "<center>" . $valor ['sede'] . "</center>",
				'dependencia' => "<center>" . $valor ['dependencia'] . "</center>",
				'ubicacion' => "<center>" . $valor ['espacio_fisico'] . "</center>",
				'seleccion' => "<center>" . $item . "</center>" 
		);
		
		$i ++;
	}
	
	$total = count ( $resultadoFinal );
	
	$resultado = json_encode ( $resultadoFinal );
	
	$resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
				"data":' . $resultado . '}';
	
	echo $resultado;
}

if ($_REQUEST ['funcion'] == 'tablaItems') {
	$tabla = new stdClass ();
	
	$page = $_GET ['page'];
	
	$limit = $_GET ['rows'];
	
	$sidx = $_GET ['sidx'];
	
	$sord = $_GET ['sord'];
	
	if (! $sidx)
		$sidx = 1;
		
		// ------------------
	
	$cadenaSql = $this->sql->getCadenaSql ( 'items', $_REQUEST ['tiempo'] );
	// echo $cadenaSql;
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	// var_dump($resultadoItems);
	// ---------------------
	$filas = count ( $resultadoItems );
	
	if ($filas > 0 && $limit > 0) {
		$total_pages = ceil ( $filas / $limit );
	} else {
		$total_pages = 0;
	}
	
	if ($page > $total_pages)
		$page = $total_pages;
	
	$start = $limit * $page - $limit;
	
	if ($resultadoItems != false) {
		$tabla->page = $page;
		$tabla->total = $total_pages;
		$tabla->records = $filas;
		
		$i = 0;
		
		foreach ( $resultadoItems as $row ) {
			$tabla->rows [$i] ['id'] = $row ['id_items'];
			$tabla->rows [$i] ['cell'] = array (
					$row ['item'],
					$row ['cantidad'],
					$row ['descripcion'],
					$row ['valor_unitario'],
					$row ['valor_total'] 
			);
			$i ++;
		}
		
		$tabla = json_encode ( $tabla );
		
		echo $tabla;
	} else {
		
		$tabla->page = $page;
		$tabla->total = $total_pages;
		$tabla->records = $filas;
		
		$i = 0;
		
		$tabla->rows [0] ['id'] = '0';
		$tabla->rows [0] ['cell'] = array (
				' ',
				'0.00',
				' ',
				'0.00',
				'0.00' 
		);
		
		$tabla = json_encode ( $tabla );
		
		echo $tabla;
	}
}
if ($_REQUEST ['funcion'] == 'AgregarItem') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'id_items_temporal' );
	$idItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	$id = $idItems [0] [0] + 1;
	
	if ($idItems [0] [0] != null) {
		
		$datos = array (
				$id,
				$_GET ['item'],
				$_GET ['cantidad'],
				$_GET ['descripcion'],
				$_GET ['valor_unitario'],
				$_GET ['valor_total'],
				$_REQUEST ['tiempo'] 
		);
	} else {
		$datos = array (
				'1',
				$_GET ['item'],
				$_GET ['cantidad'],
				$_GET ['descripcion'],
				$_GET ['valor_unitario'],
				$_GET ['valor_total'],
				$_REQUEST ['tiempo'] 
		);
	}
	
	// ------------------
	
	$cadenaSql = $this->sql->getCadenaSql ( 'insertarItem', $datos );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
	echo $resultadoItems;
	// ---------------------
}

if ($_REQUEST ['funcion'] == 'EliminarItem') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'eliminarItem', $_GET ['id'] );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
	
	echo $resultadoItems;
}

?>