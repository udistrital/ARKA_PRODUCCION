<?php
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

?>
