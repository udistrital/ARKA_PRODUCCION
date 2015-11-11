<?php
use inventarios\gestionElementos\radicacionesElementos\Sql;

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );



if ($_REQUEST ['funcion'] == 'placas') {

	

	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );


	$cadenaSql = $this->sql->getCadenaSql ( 'buscar_placa');
	$resultadoItems = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );


	$resultado = json_encode ( $resultadoItems);

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