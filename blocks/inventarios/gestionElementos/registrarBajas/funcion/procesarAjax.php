<?php
use inventarios\gestionElementos\registrarBajas\Sql;

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

if ($_REQUEST ['funcion'] == 'consultarDependencia') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'dependenciasConsultadas', $_REQUEST ['valor'] );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$resultado = json_encode ( $resultado );
	
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

?>

