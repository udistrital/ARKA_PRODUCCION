<?php
$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

if ($_REQUEST ['funcion'] == 'consultaProveedor') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'buscar_Proveedores', $_GET ['query'] );
	
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

?>
