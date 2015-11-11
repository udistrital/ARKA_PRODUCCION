<?php

namespace inventarios\gestionContrato\funcion;

if (!isset($GLOBALS ["autorizado"])) {
    include ("index.php");
    exit();
}
 

        $miConfigurador = \Configurador::singleton();

        switch ($opcion) {

            case "registroDocumento":
                $variable = "pagina=gestionContrato";
                $variable.="&opcion=mensaje";
                $variable.="&mensaje=confirma";
                break;

            case "noregistroDocumento":
                $variable = "pagina=gestionContrato";
                $variable.="&opcion=mensaje";
                $variable.="&mensaje=error";
                break;

            case "actualizoDocumento":
                $variable = "pagina=gestionContrato";
                $variable.="&opcion=mensaje";
                $variable.="&mensaje=mensajeActualizacion";
                break;

            case "noactualizoDocumento":
                $variable = "pagina=gestionContrato";
                $variable.="&opcion=mensaje";
                $variable.="&mensaje=error";
                break;

            case "paginaPrincipal":
                $variable = "pagina=index";
                break;

            default:
                $variable = '';
        }
        foreach ($_REQUEST as $clave => $valor) {
            unset($_REQUEST [$clave]);
        }

        
        $url = $miConfigurador->configuracion ["host"] . $miConfigurador->configuracion ["site"] . "/index.php?";
        $enlace = $miConfigurador->configuracion ['enlace'];
        $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);
        $_REQUEST [$enlace] = $enlace . '=' . $variable;
        $redireccion = $url . $_REQUEST [$enlace];
        
        echo "<script>location.replace('" . $redireccion . "')</script>";
        
        
        
//         $enlace = $miConfigurador->getVariableConfiguracion("enlace");
//         $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);

//         $_REQUEST [$enlace] = $variable;
//         $_REQUEST ["recargar"] = true;

//         return true;
   

?>