<?php

if (!isset($GLOBALS["autorizado"])) {
    include("index.php");
    exit;
} else {

    $variable = '';
    $miPaginaActual = $this->miConfigurador->getVariableConfiguracion("pagina");

    switch ($opcion) {

        case "registroDocumento":
            $variable = "pagina=" . $miPaginaActual;
            $variable.="&opcion=mensaje";
            $variable.="&mensaje=confirma";
            $variable .= "&usuario=".$_REQUEST['usuario'];
            break;

        case "noregistroDocumento":
            $variable = "pagina=" . $miPaginaActual;
            $variable.="&opcion=mensaje";
            $variable.="&mensaje=error";
            $variable .= "&usuario=".$_REQUEST['usuario'];
            break;
        
        case "actualizoDocumento":
            $variable = "pagina=" . $miPaginaActual;
            $variable.="&opcion=mensaje";
            $variable.="&mensaje=confirma";
            $variable .= "&usuario=".$_REQUEST['usuario'];
            break;

        case "noactualizoDocumento":
            $variable = "pagina=" . $miPaginaActual;
            $variable.="&opcion=mensaje";
            $variable.="&mensaje=error";
            $variable .= "&usuario=".$_REQUEST['usuario'];
            break;

        case "paginaPrincipal":
            $variable = "pagina=index";
            break;
    }

    foreach ($_REQUEST as $clave => $valor) {
        unset($_REQUEST[$clave]);
    }

    $enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
    $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar($variable);

    $_REQUEST[$enlace] = $variable;
    $_REQUEST["recargar"] = true;
}
?>