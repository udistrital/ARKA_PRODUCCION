<?php
use inventarios\gestionElementos\registrarReposicion\Sql;

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

if ($_REQUEST ['funcion'] == 'consultarDependencia') {
	
	$conexion = "inventarios";
	
	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	
	$cadenaSql = $this->sql->getCadenaSql ( 'dependencias_encargada', $_REQUEST ['valor'] );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$resultado = json_encode ( $resultado );
	
	echo $resultado;
}

if ($_REQUEST ['funcion'] == 'consultaPlaca') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'ConsultasPlacas', $_GET ['query'] );
	
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	foreach ( $resultadoItems as $key => $values ) {
		$keys = array (
				'value',
				'data' 
		);
		$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
	}
	
	echo '{"suggestions":' . json_encode ( $resultado ) . '}';
}

if ($_REQUEST ['funcion'] == 'SeleccionOrdenador') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'informacion_ordenadorConsultados', $_REQUEST ['ordenador'] );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$resultado = json_encode ( $resultadoItems [0] );
	
	echo $resultado;
}

if ($_REQUEST ['funcion'] == 'consultarUbicacion') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'ubicacionesConsultadas', array (
			$_REQUEST ['valorD'],
			$_REQUEST ['valorS'] 
	) );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$resultado = json_encode ( $resultado );
	
	echo $resultado;
}

if ($_REQUEST ['funcion'] == 'proveedor') {
	$parametro = $_REQUEST ['query'];
	$cadenaSql = $this->sql->getCadenaSql ( 'proveedores', $parametro );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
	
	foreach ( $resultadoItems as $key => $values ) {
		$keys = array (
				'value',
				'data' 
		);
		$resultado [$key] = array_intersect_key ( $resultadoItems [$key], array_flip ( $keys ) );
	}
	
	// var_dump($resultado)
	
	echo '{"suggestions":' . json_encode ( $resultado ) . '}';
}
?>

