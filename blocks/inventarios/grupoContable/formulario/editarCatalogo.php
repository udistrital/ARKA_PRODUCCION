<?php

namespace arka\grupoContable\editarCatalogo;

if (!isset($GLOBALS["autorizado"])) {
    include("../index.php");
    exit;
}

class Formulario {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $sql;
    var $esteRecursoDB;
    var $arrayElementos;
    var $arrayDatos;
    var $funcion;

    function __construct($lenguaje, $formulario, $sql, $funcion) {

        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->sql = $sql;

        $this->funcion = $funcion;

        $conexion = "inventarios";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        if (!$this->esteRecursoDB) {
            //Este se considera un error fatal
            exit;
        }
    }

    function formulario() {

        //validar request idCatalogo
        if (!isset($_REQUEST['idCatalogo'])) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'errorId');
            $this->mensaje();
            exit;
        }



        $this->consultarDatosCatalogo();
        $this->principal();
        //$this->consultarElementos();
        //echo '<div id="arbol">';
        $this->funcion->dibujarCatalogo();
        //echo '</div>';
        exit;
    }

    private function consultarElementos() {

        $cadena_sql = $this->sql->getCadenaSql("listarElementos", $_REQUEST['idCatalogo']);
        $registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");


        if (!$registros) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'catalogoVacio');
            $this->mensaje();
            exit;
        }

        $this->arrayElementos = $registros;
    }

    private function consultarDatosCatalogo() {

        $cadena_sql = $this->sql->getCadenaSql("buscarCatalogoId", $_REQUEST['idCatalogo']);
        $registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql, "busqueda");


        if (!$registros) {
            $this->miConfigurador->setVariableConfiguracion('mostrarMensaje', 'catalogoVacio');
            $this->mensaje();
            exit;
        }

        $this->arrayDatos = $registros;
    }

    private function edicionNombreCatalogo() {

        $nombre = $this->lenguaje->getCadena('nombreCatalogo');
        $nombreTitulo = $this->lenguaje->getCadena('nombreTitulo');

        $crearTitulo = $this->lenguaje->getCadena('cambiarNombreTitulo');
        echo '<form id="catalogo_1" name="catalogo" action="index.php" method="post">';
        //echo '<div id="agregar" class="marcoBotones">';
        echo '<fieldset class="ui-corner-all ui-widget ui-widget-content ui-corner-all">';
        echo '<legend>' . $this->lenguaje->getCadena('catalogo') . '</legend>';
        echo '<div style="float:left; width:200px"><label for="nombre">' . $nombre . '</label><span style="white-space:pre;"> </span></div>';
        echo '<input type="text" maxlength="" size="50" value="' . $this->arrayDatos[0][1] . '" class="ui-widget ui-widget-content ui-corner-all  validate[required] " tabindex="1" name="nombreCatalogo" id="nombreCatalogo" title="' . $nombreTitulo . '">';
        echo '</fieldset>';
        echo '</form>';
    }

    private function edicionBotones() {

        $crear = $this->lenguaje->getCadena('cambiarNombre');
        echo '<div id="botones"  class="marcoBotones">';

        echo '<div class="campoBoton">';
        echo '<button  onclick="cambiarNombreCatalogo()" type="button" tabindex="2" id="crearA" value="' . $crear . '" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">' . $crear . '</button>';

        echo '</div>';

        echo '<div class="campoBoton">';
        echo '<button "="" onclick=" agregarElementoCatalogo()" type="button" tabindex="2" id="agregarA"';
        echo 'value="Agregar Elemento" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">Agregar Elemento</button>';
        echo '</div><div class="campoBoton">';
        echo '<button "="" onclick=" reiniciarEdicion(' . $_REQUEST['idCatalogo'] . ')" type="button" tabindex="3" id="reiniciarA"';
        echo 'value="Reiniciar" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">Reiniciar</button>';
        echo '</div>';
        echo '</div>';
    }

    private function campoId() {

        $esteCampo = 'id';
        $atributos ['id'] = $esteCampo;
        $atributos ['nombre'] = $esteCampo;
        $atributos ['tipo'] = 'text';
        $atributos ['estilo'] = 'jqueryui';
        $atributos ['marco'] = true;
        $atributos ['estiloMarco'] = '';
        $atributos ["etiquetaObligatorio"] = true;
        $atributos ['columnas'] = 1;
        $atributos ['dobleLinea'] = 0;
        $atributos ['tabIndex'] = 1;
        $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
        $atributos ['validar'] = 'required, minSize[1],maxSize[25],custom[onlyNumberSp]';

        if (isset($_REQUEST [$esteCampo])) {
            $atributos ['valor'] = $_REQUEST [$esteCampo];
        } else {
            $atributos ['valor'] = '';
        }
        $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
        $atributos ['deshabilitado'] = false;
        $atributos ['tamanno'] = 20;
        $atributos ['maximoTamanno'] = '';
        $atributos ['anchoEtiqueta'] = 200;
        //$tab ++;
// Aplica atributos globales al control
        //$atributos = array_merge($atributos, $atributosGlobales);
        echo $this->miFormulario->campoCuadroTexto($atributos);
        unset($atributos);
    }

    private function campoPadre() {
        $idPadre = $this->lenguaje->getCadena('idPadre');
        echo '<div class="jqueryui  anchoColumna1">';
        echo '<div style="float:left;display:inline; width:200px"><label for="idPadre">' . $idPadre . '</label></div>';
        echo '<input type="text" onchange="cambiarPadre()" onkeyup="autocompletar()" class="ui-widget ui-widget-content ui-corner-all validate[required,custom[valorLista]]"  tabindex="3" size="50" value="0" name="lidPadre" id="lidPadre" title="" class="ui-widget ui-widget-content ui-corner-all"></input>';
        echo '</div>';
    }

    private function campoNombre() {
        $esteCampo = 'nombreElemento';
        $atributos ['id'] = $esteCampo;
        $atributos ['nombre'] = $esteCampo;
        $atributos ['tipo'] = 'text';
        $atributos ['estilo'] = 'jqueryui';
        $atributos ['marco'] = true;
        $atributos ['estiloMarco'] = '';
        $atributos ["etiquetaObligatorio"] = true;
        $atributos ['columnas'] = 1;
        $atributos ['dobleLinea'] = 0;
        $atributos ['tabIndex'] = 3;
        $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
        $atributos ['validar'] = 'required, minSize[1],maxSize[25]';

        if (isset($_REQUEST [$esteCampo])) {
            $atributos ['valor'] = $_REQUEST [$esteCampo];
        } else {
            $atributos ['valor'] = '';
        }
        $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
        $atributos ['deshabilitado'] = false;
        $atributos ['tamanno'] = 30;
        $atributos ['maximoTamanno'] = '';
        $atributos ['anchoEtiqueta'] = 200;
        //$tab ++;
// Aplica atributos globales al control
        //$atributos = array_merge($atributos, $atributosGlobales);
        echo $this->miFormulario->campoCuadroTexto($atributos);
        unset($atributos);
    }

    private function campoSalida() {
        $esteCampo = 'cuentaSalida';
        $atributos ['id'] = $esteCampo;
        $atributos ['nombre'] = $esteCampo;
        $atributos ['tipo'] = 'text';
        $atributos ['estilo'] = 'jqueryui';
        $atributos ['marco'] = true;
        $atributos ['estiloMarco'] = '';
        $atributos ["etiquetaObligatorio"] = true;
        $atributos ['columnas'] = 1;
        $atributos ['dobleLinea'] = 0;
        $atributos ['tabIndex'] = 4;
        $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
        $atributos ['validar'] = 'required, minSize[1],maxSize[25],custom[onlyNumberSp]';

        if (isset($_REQUEST [$esteCampo])) {
            $atributos ['valor'] = $_REQUEST [$esteCampo];
        } else {
            $atributos ['valor'] = '';
        }
        $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
        $atributos ['deshabilitado'] = false;
        $atributos ['tamanno'] = 20;
        $atributos ['maximoTamanno'] = '';
        $atributos ['anchoEtiqueta'] = 200;
        //$tab ++;
// Aplica atributos globales al control
        //$atributos = array_merge($atributos, $atributosGlobales);
        echo $this->miFormulario->campoCuadroTexto($atributos);
        unset($atributos);
    }

    private function campoEntrada() {
        $esteCampo = 'cuentaEntrada';
        $atributos ['id'] = $esteCampo;
        $atributos ['nombre'] = $esteCampo;
        $atributos ['tipo'] = 'text';
        $atributos ['estilo'] = 'jqueryui';
        $atributos ['marco'] = true;
        $atributos ['estiloMarco'] = '';
        $atributos ["etiquetaObligatorio"] = true;
        $atributos ['columnas'] = 1;
        $atributos ['dobleLinea'] = 0;
        $atributos ['tabIndex'] = 5;
        $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
        $atributos ['validar'] = 'required, minSize[1],maxSize[25],custom[onlyNumberSp]';

        if (isset($_REQUEST [$esteCampo])) {
            $atributos ['valor'] = $_REQUEST [$esteCampo];
        } else {
            $atributos ['valor'] = '';
        }
        $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
        $atributos ['deshabilitado'] = false;
        $atributos ['tamanno'] = 20;
        $atributos ['maximoTamanno'] = '';
        $atributos ['anchoEtiqueta'] = 200;
        //$tab ++;
// Aplica atributos globales al control
        //$atributos = array_merge($atributos, $atributosGlobales);
        echo $this->miFormulario->campoCuadroTexto($atributos);
        unset($atributos);
    }

    private function campoTipoBien() {
        $esteCampo = "tipoBien";
        $atributos ['nombre'] = $esteCampo;
        $atributos ['id'] = $esteCampo;
        $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
        $atributos ["etiquetaObligatorio"] = false;
        $atributos ['tab'] = 1;
        $atributos ['seleccion'] = - 1;
        $atributos ['anchoEtiqueta'] = 200;
        $atributos ['evento'] = '';
        if (isset($_REQUEST [$esteCampo])) {
            $atributos ['valor'] = $_REQUEST [$esteCampo];
        } else {
            $atributos ['valor'] = '';
        }
        $atributos ['deshabilitado'] = false;
        $atributos ['columnas'] = 1;
        $atributos ['tamanno'] = 1;
        $atributos ['ajax_function'] = "";
        $atributos ['ajax_control'] = $esteCampo;
        $atributos ['estilo'] = "jqueryui";
        $atributos ['validar'] = "required";
        $atributos ['limitar'] = 1;
        $atributos ['anchoCaja'] = 49;
        $atributos ['miEvento'] = '';
        $atributos ['cadena_sql'] = $this->sql->getCadenaSql("tipoBien");
        $matrizItems = array(
            array(
                0,
                ' '
            )
        );
        $matrizItems = $this->esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
        $atributos ['matrizItems'] = $matrizItems;
        
        echo $this->miFormulario->campoCuadroLista($atributos);
        unset($atributos);
    }

    private function campoDepreciacion() {

        $esteCampo = "depreciacion";
        $atributos ['nombre'] = $esteCampo;
        $atributos ['id'] = $esteCampo;
        $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
        $atributos ["etiquetaObligatorio"] = true;
        $atributos ['tab'] = 6;
        $atributos ['seleccion'] = -1;
        $atributos ['anchoEtiqueta'] = 200;
        $atributos ['evento'] = "3";
        if (isset($_REQUEST [$esteCampo])) {
            $atributos ['valor'] = $_REQUEST [$esteCampo];
        } else {
            $atributos ['valor'] = '';
        }
        $atributos ['deshabilitado'] = false;
        $atributos ['columnas'] = 2;
        $atributos ['tamanno'] = 1;
        $atributos ['ajax_function'] = "cambio()";
        $atributos ['ajax_control'] = $esteCampo;
        $atributos ['estilo'] = "jqueryui";
        $atributos ['validar'] = "required";
        $atributos ['limitar'] = 1;
        $atributos ['anchoCaja'] = 49;
        $atributos ['miEvento'] = 'change';
        //$atributos ['cadena_sql'] = $this->sql->getCadenaSql("boolean");
        $matrizItems = array(
            array(1, 'SI'),
            array(0, 'NO'),
        );
        //$matrizItems = $this->esteRecursoDB2->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
        $atributos ['matrizItems'] = $matrizItems;
        echo $this->miFormulario->campoCuadroLista($atributos);
        unset($atributos);

//---------------  descripción depreciación ------------------ //

        $atributos ["id"] = "descripcionDepreciacion";
        $atributos ["estiloEnLinea"] = "display:none";
        //$atributos = array_merge($atributos, $atributosGlobales);
        echo $this->miFormulario->division("inicio", $atributos);
        unset($atributos);
        {
            $esteCampo = 'vidautil';
            $atributos ['id'] = $esteCampo;
            $atributos ['nombre'] = $esteCampo;
            $atributos ['tipo'] = 'text';
            $atributos ['estilo'] = 'jqueryui';
            $atributos ['marco'] = true;
            $atributos ['estiloMarco'] = '';
            $atributos ["etiquetaObligatorio"] = true;
            $atributos ['columnas'] = 1;
            $atributos ['dobleLinea'] = 0;
            $atributos ['tabIndex'] = 7;
            $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos ['validar'] = 'required, minSize[1],maxSize[4],custom[onlyNumberSp]';

            if (isset($_REQUEST [$esteCampo])) {
                $atributos ['valor'] = $_REQUEST [$esteCampo];
            } else {
                $atributos ['valor'] = '';
            }
            $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
            $atributos ['deshabilitado'] = false;
            $atributos ['tamanno'] = 20;
            $atributos ['maximoTamanno'] = '';
            $atributos ['anchoEtiqueta'] = 200;
            //$tab ++;
// Aplica atributos globales al control
            //$atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTexto($atributos);
            unset($atributos);
            //----
            $esteCampo = 'cuentaCredito';
            $atributos ['id'] = $esteCampo;
            $atributos ['nombre'] = $esteCampo;
            $atributos ['tipo'] = 'text';
            $atributos ['estilo'] = 'jqueryui';
            $atributos ['marco'] = true;
            $atributos ['estiloMarco'] = '';
            $atributos ["etiquetaObligatorio"] = true;
            $atributos ['columnas'] = 1;
            $atributos ['dobleLinea'] = 0;
            $atributos ['tabIndex'] = 8;
            $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos ['validar'] = 'required, minSize[1],maxSize[25],custom[onlyNumberSp]';

            if (isset($_REQUEST [$esteCampo])) {
                $atributos ['valor'] = $_REQUEST [$esteCampo];
            } else {
                $atributos ['valor'] = '';
            }
            $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
            $atributos ['deshabilitado'] = false;
            $atributos ['tamanno'] = 20;
            $atributos ['maximoTamanno'] = '';
            $atributos ['anchoEtiqueta'] = 200;
            //$tab ++;
// Aplica atributos globales al control
            //$atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTexto($atributos);
            unset($atributos);
            //--
            $esteCampo = 'cuentaDebito';
            $atributos ['id'] = $esteCampo;
            $atributos ['nombre'] = $esteCampo;
            $atributos ['tipo'] = 'text';
            $atributos ['estilo'] = 'jqueryui';
            $atributos ['marco'] = true;
            $atributos ['estiloMarco'] = '';
            $atributos ["etiquetaObligatorio"] = true;
            $atributos ['columnas'] = 1;
            $atributos ['dobleLinea'] = 0;
            $atributos ['tabIndex'] = 9;
            $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
            $atributos ['validar'] = 'required, minSize[1],maxSize[25],custom[onlyNumberSp]';

            if (isset($_REQUEST [$esteCampo])) {
                $atributos ['valor'] = $_REQUEST [$esteCampo];
            } else {
                $atributos ['valor'] = '';
            }
            $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
            $atributos ['deshabilitado'] = false;
            $atributos ['tamanno'] = 20;
            $atributos ['maximoTamanno'] = '';
            $atributos ['anchoEtiqueta'] = 200;
            //$tab ++;
// Aplica atributos globales al control
            //$atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->campoCuadroTexto($atributos);
            unset($atributos);
        }
    }

    private function notaUso() {
        echo ' <div class="jqueryui  anchoColumna1">';
        echo "<p>";
        echo "<b>Nota de Uso</b>: Para recuperar un identificador padre ya creado y asignarlo a un nuevo elemento, seleccione un elemento del listado. Click en Reiniciar para dar valor inicial al Identificador Padre.";
        echo "</p>";
        echo ' </div><br>';
    }

    private function principal() {
        // Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        $tab = 0;
        $this->notaUso();
        $this->edicionNombreCatalogo();

        echo '<form id="catalogo" name="catalogo" action="index.php" method="post">';
        echo '<fieldset class="ui-corner-all ui-widget ui-widget-content ui-corner-all">';
        echo '<legend>' . $this->lenguaje->getCadena('elementos') . '</legend>';

        $this->campoPadre();
        $this->campoId();
        $this->campoNombre();
        $this->campoEntrada();
        $this->campoSalida();
        $this->campoTipoBien();
        $this->campoDepreciacion();


        echo '<input id="idCatalogo" type="hidden" value="' . $_REQUEST['idCatalogo'] . '" name="idCatalogo">';
        echo '<input id="idReg" type="hidden" value="0" name="idReg">';
        echo '<input id="idPadre" type="hidden" value="0" name="idPadre">';
        echo '</fieldset>';
        $this->edicionBotones();
        echo "</form>";
    }

    function mensaje() {

        // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion('mostrarMensaje');
        //$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );

        if ($mensaje) {

            $tipoMensaje = $this->miConfigurador->getVariableConfiguracion('tipoMensaje');

            if ($tipoMensaje == 'json') {

                $atributos ['mensaje'] = $mensaje;
                $atributos ['json'] = true;
            } else {
                $atributos ['mensaje'] = $this->lenguaje->getCadena($mensaje);
            }
            // -------------Control texto-----------------------
            $esteCampo = 'divMensaje';
            $atributos ['id'] = $esteCampo;
            $atributos ["tamanno"] = '';
            $atributos ["estilo"] = 'information';
            $atributos ["etiqueta"] = '';
            $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje($atributos);
            unset($atributos);
        }

        return true;
    }

}

$miFormulario = new Formulario($this->lenguaje, $this->miFormulario, $this->sql, $this);


$miFormulario->formulario();
$miFormulario->mensaje();
?>