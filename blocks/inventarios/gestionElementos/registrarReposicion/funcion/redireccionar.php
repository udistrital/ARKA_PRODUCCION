<?php

namespace inventarios\gestionElementos\registrarReposicion\funcion;

if (!isset($GLOBALS ["autorizado"])) {
    include ("index.php");
    exit();
}

class redireccion {

    public static function redireccionar($opcion, $valor = "") {
        $miConfigurador = \Configurador::singleton();
        $miPaginaActual = $miConfigurador->getVariableConfiguracion("pagina");

        switch ($opcion) {
            case "inserto" :

                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=confirma";
                $variable .= "&elemento=" . $valor['id_elemento'];
                $variable .= "&entrada=" . $valor['id_entrada'];
                $variable .= "&salida=" . $valor['id_salida'];
                $variable .= "&usuario=" . $valor['usuario'];
                break;

            case "inserto_M" :

                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=confirmaMasivo";
                break;

            case "noExtension" :

                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=noExtension";
                break;

            case "noInserto" :
                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=error";
                $variable .= "&usuario=" . $valor;  
                break;

            case "noCargarElemento" :

                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=noElemento";

                break;

            case "notextos" :
                $variable = "pagina=" . $miPaginaActual;
                $variable .= "&opcion=mensaje";
                $variable .= "&mensaje=otros";
                $variable .= "&errores=notextos";

                break;

            case "paginaPrincipal" :
                $variable = "pagina=" . $miPaginaActual;
                break;
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

        // $enlace =$miConfigurador->getVariableConfiguracion("enlace");
        // $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);
        // // echo $enlace;
        // // // echo $variable;
        // // exit;
        // $_REQUEST[$enlace] = $variable;
        // $_REQUEST["recargar"] = true;
        // return true;
    }

}

?>