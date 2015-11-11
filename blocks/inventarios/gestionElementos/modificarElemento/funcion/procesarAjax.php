<?php
use inventarios\gestionElementos\modificarElemento\Sql;

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

if ($_REQUEST ['funcion'] == 'Consulta') {
	$arreglo = unserialize ( $_REQUEST ['arreglo'] );
	
	
	
	
	if ($arreglo [5] != 0) {
		
		$cadenaSql = $this->sql->getCadenaSql ( 'consultarElementoCD', $arreglo );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	} else if ($arreglo [5] == 0) {
		$cadenaSql = $this->sql->getCadenaSql ( 'consultarElementoC', $arreglo );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	}
	
	for($i = 0; $i < count ( $resultado ); $i ++) {
		$variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
		$variable .= "&id_elemento=" . $resultado [$i] ['idelemento'];
		$variable .= "&opcion=modificar";
		$variable .= "&usuario=" . $_REQUEST ['usuario'];
		$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
		
		$variable2 = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
		$variable2 .= "&id_elemento=" . $resultado [$i] ['idelemento'];
		$variable2 .= "&opcion=anular";
		$variable2 .= "&tipo_bien=".$resultado[$i]['tipo_bien'];
		$variable2 .= "&usuario=" . $_REQUEST ['usuario'];
		$variable2 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable2, $directorio );
		
		if ($resultado [$i] ['cierrecontable'] == 'f') {
			
			$cierreContable = "<center><a href='" . $variable . "'><u>modificar</u></a></center> ";
			$anulacion ="<center><a href='" . $variable2 . "'><u>anular</u></a></center>";
		}
		if ($resultado [$i] ['cierrecontable'] == 't') {
			
			$cierreContable = "<center>Inhabilitado por Cierre Contable</center>";
			
			$anulacion = "<center>Inhabilitado por Cierre Contable</center>";
		}
		
		$resultadoFinal [] = array (
				'fecharegistro' => "<center>" . $resultado [$i] ['fecharegistro'] . "</center>",
				'entrada' => "<center>" . $resultado [$i] ['entrada'] . "</center>",
				'descripcion' => "<center>" . $resultado [$i] ['descripcion'] . "</center>",
				'placa' => "<center>" . $resultado [$i] ['placa'] . "</center>",
				'modificar' => "<center>" . $cierreContable,
				'anular' => "<center>" . $anulacion 
		)
		;
	}
	
	$total = count ( $resultadoFinal );
	
	$resultado = json_encode ( $resultadoFinal );
	
	$resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
				"data":' . $resultado . '}';
	
	echo $resultado;
}

if ($_REQUEST ['funcion'] == 'consultarDependencia') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'dependenciasConsultadas', $_REQUEST ['valor'] );
	
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$resultado = json_encode ( $resultado );
	
	echo $resultado;
}

if ($_REQUEST ['funcion'] == 'SeleccionTipoBien') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'ConsultaTipoBien', $_REQUEST ['valor'] );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	$resultadoItems = $resultadoItems [0];
	
	echo json_encode ( $resultadoItems );
}

if ($_REQUEST ['funcion'] == 'consultarIva') {
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultar_tipo_iva' );
	
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
