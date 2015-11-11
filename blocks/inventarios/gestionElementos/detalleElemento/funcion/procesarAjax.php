<?php

namespace inventarios\gestionElementos\detalleElemento;

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

if ($_REQUEST ['funcion'] == 'subeFoto') {
	
	// echo "aqui>".$_REQUEST['elemento'];
	if (empty ( $_FILES ['images'] )) {
		echo json_encode ( [ 
				'error' => 'No files found for upload.' 
		] );
		// or you can throw an exception
		return; // terminate
	}
	
	// get the files posted
	$images = $_FILES ['images'];
	
	// get user id posted
	$userid = empty ( $_POST ['userid'] ) ? '' : $_POST ['userid'];
	
	// get user name posted
	$username = empty ( $_POST ['username'] ) ? '' : $_POST ['username'];
	
	// a flag to see if everything is ok
	$success = null;
	
	// file paths to store
	$paths = [ ];
	
	// get file names
	$filenames = $images ['tmp_name'];
	
	// loop and process files
	for($i = 0; $i < count ( $filenames ); $i ++) {
		// $ext = explode('.', basename($filenames[$i]));
		// $target = "uploads" . DIRECTORY_SEPARATOR . md5(uniqid()) . "." . array_pop($ext);
		
		$data = base64_encode ( file_get_contents ( $filenames [$i] ) );
		$parametro = array (
				'id_elemento' => $_REQUEST ['elemento'],
				'prioridad' => 0,
				'imagen' => $data 
		);
		
		$cadenaSql = $this->sql->getCadenaSql ( 'guardar_foto', $parametro );
		$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'insertar', $parametro, "guardar_foto" );
		
		if ($resultadoItems == true) {
			$output = [ 
					'uploaded' => 'Foto registrada' 
			];
		} else {
			$output = [ 
					'error' => 'Error while uploading images. Contact the system administrator' 
			];
		}
		
		// return a json encoded response for plugin to process successfully
		echo json_encode ( $output );
	}
}

if ($_REQUEST ['funcion'] == 'galeriaFoto') {
	
	$sPhotos = '';
	$cadenaSql = $this->miSql->getCadenaSql ( 'consultar_fotos', $_REQUEST ['elemento'] );
	$aItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	if ($aItems) {
		foreach ( $aItems as $i => $aItemInfo ) {
			
	
			// Decodificamos $Base64Img codificada en base64.
// 			$Base64Img = base64_decode ( $Base64Img );
			
			// escribimos la información obtenida en un archivo llamado
			// unodepiera.png para que se cree la imagen correctamente
// 			file_put_contents ( 'unodepiera.png', $Base64Img );
// 			$sPhotos .= "<img src='unodepiera.png' alt='unodepiera' />";
			
			$sPhotos .= '<img src="data:image/gif;base64,' . trim($aItems[$i][0],"\n") . '">, ';
		}
	} else {
		$sPhotos = [ 
				'error' => 'No files were processed.' 
		];
	}
	
	echo json_encode ( $sPhotos );
}

if ($_REQUEST ['funcion'] == 'eliminaFoto') {
	
	$foto = $_REQUEST ['num_registro'];
	
	$cadenaSql = $this->sql->getCadenaSql ( 'eliminar_fotos', $foto );
	$eliminar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "insertar", $foto, "eliminar_fotos" );
	
	if ($eliminar == true) {
		$output = [ 
				'uploaded' => 'Foto eliminada' 
		];
	} else {
		$output = [ 
				'error' => 'Error eliminando la foto' 
		];
	}
	echo json_encode ( $output );
}

if ($_REQUEST ['funcion'] == 'Consulta') {
	$arreglo = unserialize ( $_REQUEST ['arreglo'] );
	
	$cadenaSql = $this->sql->getCadenaSql ( 'consultarElemento', $arreglo );
	// $cadenaSql = $this->sql->getCadenaSql('consultarElemento', false);
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	foreach ( $resultado as $key => $values ) {
		$variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
		$variable .= "&opcion=detalle";
		$variable .= "&elemento=" . $resultado [$key] ['id_elemento_ind'];
		$variable .= "&usuario=" . $_REQUEST ['usuario'];
		$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
		
		$detalle = "<center><a href='" . $variable . "'><u>Ver Detalle</u></a></center> ";
		
		$resultadoFinal [] = array (
				'placa' => "<center>" . $resultado [$key] ['placa'] . "</center>",
				'descripcion' => "<center>" . $resultado [$key] ['descripcion'] . "</center>",
				'sede' => "<center>" . $resultado [$key] ['sede_nombre'] . "</center>",
				'dependencia' => "<center>" . $resultado [$key] ['dependencia_nombre'] . "</center>",
				'funcionario' => "<center>" . $resultado [$key] ['fun_nombre'] . "</center>",
				'detalle' => "<center>" . $detalle 
		);
	}
	
	$total = count ( $resultadoFinal );
	$resultado = json_encode ( $resultadoFinal );
	$resultado = '{
                "recordsTotal":' . $total . ',
                "recordsFiltered":' . $total . ',
		"data":' . $resultado . '}';
	
	echo $resultado;
}

if ($_REQUEST ['funcion'] == 'placas') {
	$parametro = $_REQUEST ['query'];
	$cadenaSql = $this->sql->getCadenaSql ( 'buscar_placa', $parametro );
	$resultadoItems = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );
	
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
	$cadenaSql = $this->sql->getCadenaSql ( 'ubicacionesConsultadas', $_REQUEST ['valor'] );
	$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
	$resultado = json_encode ( $resultado );
	echo $resultado;
}
//
//if ($_POST['action'] == 'get_info' && (int) $_POST['id'] > 0) {
//    // get photo info ->>> Este es el numero de registro
//    $iPid = (int) $_POST['id'];
//
//  echo  $cadenaSql = $this->sql->getCadenaSql('consultarElemento_foto', $iPid);
//    $aImageInfo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
//    var_dump($aImageInfo);
//    
//    exit;
//    // prepare last 10 comments con el numero de registro
//    //$sCommentsBlock = $GLOBALS['MyComments']->getComments($iPid);
//
//    $cadenaSql = $this->sql->getCadenaSql('consultar_fotos', $aImageInfo[0]['numero_registro']);
//    $aItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda"); // get photos info
//    // Prev & Next navigation
//    $sNext = $sPrev = '';
//
//    $cadenaSql = $this->sql->getCadenaSql('consultarElemento_foto_antes', $aImageInfo[0]['numero_registro']);
//    $iPrev = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda"); // get photos info
//
//    $cadenaSql = $this->miSql->getCadenaSql('consultarElemento_foto_despues', $aImageInfo[0]['numero_registro']);
//    $iNext = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda"); // get photos info
//
//    $iPrev = (int) $iPrev[0][0];
//    $iNext = (int) $iNext[0][0];
//    $sPrevBtn = ($iPrev) ? '<div class="preview_prev" onclick="getPhotoPreviewAjx(\'' . $iPrev . '\')"><img src=' . $rutaBloque . '"/script/sources281/images/prev.png" alt="prev" /></div>' : '';
//    $sNextBtn = ($iNext) ? '<div class="preview_next" onclick="getPhotoPreviewAjx(\'' . $iNext . '\')"><img src=' . $rutaBloque . '"/script/sources281images/next.png" alt="next" /></div>' : '';
//
//    require_once($rutaBloque . '/script/sources281/classes/Services_JSON.php');
//
//    $oJson = new Services_JSON();
//    header('Content-Type:text/javascript');
//    echo $oJson->encode(array(
//        'data1' => '<img class="fileUnitSpacer" src="data:image/gif;base64,' . $aImageInfo[0]['imagen'] . '">' . $sPrevBtn . $sNextBtn,
//            //   'data2' => $sCommentsBlock,
//    ));
//    exit;
//}




