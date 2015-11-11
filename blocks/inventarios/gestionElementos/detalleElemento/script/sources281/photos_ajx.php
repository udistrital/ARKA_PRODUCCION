<?php

use inventarios\gestionElementos\detalleElemento\Sql;

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

$rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
$rutaBloque .= $esteBloque ['grupo'] . '/' . $esteBloque ['nombre'];

// disable warnings

if ($_POST['action'] == 'get_info' && (int) $_POST['id'] > 0) {

    require_once($rutaBloque . '/script/sources281/classes/CMyComments.php');
    // get photo info ->>> Este es el numero de registro
    $iPid = (int) $_POST['id'];

    $cadenaSql = $this->miSql->getCadenaSql('consultarElemento_foto', $iPid);
    $aImageInfo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

    // prepare last 10 comments con el numero de registro
    $sCommentsBlock = $GLOBALS['MyComments']->getComments($iPid);

    $cadenaSql = $this->miSql->getCadenaSql('consultar_fotos', $aImageInfo[0]['numero_registro']);
    $aItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda"); // get photos info
    // Prev & Next navigation
    $sNext = $sPrev = '';

    $cadenaSql = $this->miSql->getCadenaSql('consultarElemento_foto_antes', $aImageInfo[0]['numero_registro']);
    $iPrev = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda"); // get photos info

    $cadenaSql = $this->miSql->getCadenaSql('consultarElemento_foto_despues', $aImageInfo[0]['numero_registro']);
    $iNext = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda"); // get photos info

    $iPrev = (int) $iPrev[0][0];
    $iNext = (int) $iNext[0][0];
    $sPrevBtn = ($iPrev) ? '<div class="preview_prev" onclick="getPhotoPreviewAjx(\'' . $iPrev . '\')"><img src=' . $rutaBloque . '"/script/sources281/images/prev.png" alt="prev" /></div>' : '';
    $sNextBtn = ($iNext) ? '<div class="preview_next" onclick="getPhotoPreviewAjx(\'' . $iNext . '\')"><img src=' . $rutaBloque . '"/script/sources281images/next.png" alt="next" /></div>' : '';

    require_once($rutaBloque . '/script/sources281/classes/Services_JSON.php');

    $oJson = new Services_JSON();
    header('Content-Type:text/javascript');
    echo $oJson->encode(array(
        'data1' => '<img class="fileUnitSpacer" src="data:image/gif;base64,' . $aImageInfo[0]['imagen'] . '">' . $sPrevBtn . $sNextBtn,
        'data2' => $sCommentsBlock,
    ));
    exit;
}