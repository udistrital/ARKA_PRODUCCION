<?php
use inventarios\gestionCompras\registrarOrdenCompra\Sql;

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

if ($_REQUEST ['funcion'] == 'SeleccionTipoBien') {

	
	$cadenaSql = $this->sql->getCadenaSql ( 'ConsultaTipoBien', $_REQUEST['valor'] );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	$resultadoItems=$resultadoItems[0];

	echo json_encode($resultadoItems);
}




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


if ($_REQUEST ['funcion'] == 'consultarIva') {




	$cadenaSql = $this->sql->getCadenaSql ( 'consultar_tipo_iva' );

	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

	$resultado = json_encode ( $resultado );

	echo $resultado;
}


  




?>