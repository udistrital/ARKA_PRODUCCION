<?php 
namespace arka\grupoContable\AgregarForm;



if(!isset($GLOBALS["autorizado"])) {
	include("../index.php");
	exit;
}


class Formulario {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $sql;
    var $esteRecursoDB;

    function __construct($lenguaje, $formulario , $sql) {

        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;
        
        $this->sql = $sql;
        
        $conexion="inventarios";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        if (!$this->esteRecursoDB) {
        	//Este se considera un error fatal
        	exit;
        }

    }
    
    function formulario(){
    	$nombre =  $this->lenguaje->getCadena ( 'nombreCatalogo' );
    	$nombreTitulo =  $this->lenguaje->getCadena ( 'nombreTitulo' );
    	$crear =  $this->lenguaje->getCadena ( 'crear' );
    	$crearTitulo =  $this->lenguaje->getCadena ( 'crearTitulo' );
    	$crearLabel =  $this->lenguaje->getCadena ( 'crearLabel' );
    	
    	echo '<form name="catalogo" action="index.php" method="post" id="catalogo">';
    	echo '<div id="agregar" class="marcoBotones">';
    	echo '<fieldset class="ui-corner-all ui-widget ui-widget-content ui-corner-all">';
    	
    	
    	echo '<div style="float:left; width:150px"><label for="nombre">'.$nombre.'</label><span style="white-space:pre;"> </span></div>';
    	echo '<input type="text" maxlength="" size="50" value="" class="ui-widget ui-widget-content ui-corner-all  validate[required] " tabindex="1" name="nombre" id="nombre" title="'.$nombreTitulo.'">';
    	
    	echo '</fieldset>';
    	echo '</div>';
    	
    	echo '<div id="botones"  class="marcoBotones">';
    	echo '<div class="campoBoton">';
    	echo '<button  onclick=" crearCatalogo()" type="button" tabindex="2" id="crearA" value="'.$crear.'" class="ui-button-text ui-state-default ui-corner-all ui-button-text-only">'.$crear.'</button>';
        echo '<input type="hidden" value="false" id="crear" name="crear">';
    	echo '</div>';
    	echo '</div>';
    	
    }


    function mensaje() {

        // Si existe algun tipo de error en el login aparece el siguiente mensaje
        $mensaje = $this->miConfigurador->getVariableConfiguracion ( 'mostrarMensaje' );
        //$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', null );

        if ($mensaje) {

            $tipoMensaje = $this->miConfigurador->getVariableConfiguracion ( 'tipoMensaje' );

            if ($tipoMensaje == 'json') {

                $atributos ['mensaje'] = $mensaje;
                $atributos ['json'] = true;
            } else {
                $atributos ['mensaje'] = $this->lenguaje->getCadena ( $mensaje );
            }
            // -------------Control texto-----------------------
            $esteCampo = 'divMensaje';
            $atributos ['id'] = $esteCampo;
            $atributos ["tamanno"] = '';
            $atributos ["estilo"] = 'information';
            $atributos ["etiqueta"] = '';
            $atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
            echo $this->miFormulario->campoMensaje ( $atributos );
            unset ( $atributos );

             
        }
        return true;
    }
    
}

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario,$this->sql );

$miFormulario->formulario ();
$miFormulario->mensaje ();

?>