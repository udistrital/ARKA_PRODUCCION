<?php

namespace inventarios\gestionElementos\detalleElemento\funcion;

use inventarios\gestionElementos\detalleElemento\funcion\redireccion;

include_once ('redireccionar.php');
if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class RegistradorOrden {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miFuncion;
    var $miSql;
    var $conexion;

    function __construct($lenguaje, $sql, $funcion) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
    }

    function procesarFormulario() {


        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);


        if ($_POST['action'] == 'get_info' && (int) $_POST['id'] > 0) {

            require_once($rutaBloque . '/script/sources281/classes/CMyComments.php');

            // get photo info ->>> Este es el numero de registro
            $iPid = (int) $_POST['id'];

            $cadenaSql = $this->sql->getCadenaSql('consultarElemento_foto', $iPid);
            $aImageInfo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
            // prepare last 10 comments con el numero de registro
            $sCommentsBlock = $GLOBALS['MyComments']->getComments($iPid);

            $cadenaSql = $this->miSql->getCadenaSql('consultar_fotos', $aImageInfo[0][]);
            $aItems = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda"); // get photos info
            // Prev & Next navigation
            $sNext = $sPrev = '';
            $iPrev = (int) $GLOBALS['MySQL']->getOne("SELECT `id` FROM `s281_photos` WHERE `id` < '{$iPid}' ORDER BY `id` DESC LIMIT 1");
            $iNext = (int) $GLOBALS['MySQL']->getOne("SELECT `id` FROM `s281_photos` WHERE `id` > '{$iPid}' ORDER BY `id` ASC LIMIT 1");
            $sPrevBtn = ($iPrev) ? '<div class="preview_prev" onclick="getPhotoPreviewAjx(\'' . $iPrev . '\')"><img src="images/prev.png" alt="prev" /></div>' : '';
            $sNextBtn = ($iNext) ? '<div class="preview_next" onclick="getPhotoPreviewAjx(\'' . $iNext . '\')"><img src="images/next.png" alt="next" /></div>' : '';

            require_once($rutaBloque . '/script/sources281/classes/Services_JSON.php');
            $oJson = new Services_JSON();
            header('Content-Type:text/javascript');
            echo $oJson->encode(array(
                'data1' => '<img class="fileUnitSpacer" src="data:image/gif;base64,' . $aItems[$i][0] . '">' . $sPrevBtn . $sNextBtn,
                'data2' => $sCommentsBlock,
            ));
            exit;
        }
    }

    function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST [$clave]);
            }
        }
    }

}

$miRegistrador = new RegistradorOrden($this->lenguaje, $this->sql, $this->funcion);

$resultado = $miRegistrador->procesarFormulario();
?>