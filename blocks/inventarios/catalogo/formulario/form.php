<?php 
namespace arka\catalogo\formulario;



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
        
        $conexion = "catalogo";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        if (!$this->esteRecursoDB) {
        	//Este se considera un error fatal
        	exit;
        }

    }

    function formulario() {

    	$textos[0]=utf8_encode($this->lenguaje->getCadena("listaAdicion"));
    	$textos[1]=utf8_encode($this->lenguaje->getCadena("listaVer"));
    	$textos[2]=utf8_encode($this->lenguaje->getCadena("listaEditar"));
    	
    	$textos[5]=utf8_encode($this->lenguaje->getCadena("listaEliminar"));
    	
    	$cadena_sql = $this->sql->getCadenaSql("listarCatalogos",'');
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	
    	
    	
    	
    	$cadena1 = "<br><br><br><br><br><br><br>";
    	$cadena1 .= "<div id='espacioTrabajo'>";
    	
    	//menu 
    	$cadena1 .= "<div id='marcoMenu'>";
    	$cadena1 .= '<div  id="menu" class="menu2">';
    	//$cadena .= '<div class="menu" style="max-width: 80%;margin: 0 auto;color:green;text-align:center;">';
    	$cadena1 .='<a style="text-align: center;" class="agregar" onclick="agregarElementoLista()"  title="'.$textos[0].'">+</a>';
    	//</div>';
    	//$cadena .='</div>';
    	$cadena1 .='</div>';
    	$cadena1 .='<hr >';
    	
    	$cadena1 .='<br>';
    	$cadena1 .= "<div id='marcoTrabajo'>";
    	echo $cadena1; 
    	
    	if(!$registros){
    		
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorLista' );
    		$this->mensaje();
    		echo "</div>";
    		exit;
    	}
    	
    	$cadena = "";
    	foreach ($registros as  $fila){
    		
    		$cadena .= '<div class="listaCatalogo" id="listaParametro'.$fila[0].'">';
    		
    		$cadena .= '<div class="interno"  >';
    		
    		//formulario
    		$cadena .= '<form name="eliminarFormulario'.$fila[0].'" id="formulario'.$fila[0].'">';
    		$cadena .= '<input type="hidden" name="idDel" id="idDel" value="'.$fila[2].'"></input>';
    		$cadena .= '</form>';
    		//$cadena .= '<div style="display:inline">';
    		$cadena .= '<div class="mostrarElemento" onclick="mostrarElementoLista('.$fila[0].')" id="el'.$fila[0].'" title="'.$textos[1].'">v</div>';
    		$cadena .= '<div class="editarElemento2"  onclick="editarElementoLista(this)" id="el'.$fila[0].'" title="'.$textos[2].'">e</div>';
    		$cadena .= '<div class="eliminarElemento" onclick="eliminarElementoLista(this)" id="el'.$fila[0].'" title="'.$textos[5].'">x</div>';
    		$cadena .='</div>';
    		
    		$cadena .= '<div class="interno">';
    		$cadena .= '<a onclick="editarElemento(this)" title="'.$textos[2].'">';
    		$cadena .= $fila[0].'. '.utf8_encode($fila[1])." ".$fila[2].'</a>';
    		$cadena .= '</div>';
    		
    		//$cadena .='</div>';
    		$cadena .= "</div>";
    		$cadena .='<div id="contenido'.$fila[0].'">';
    		$cadena .='</div>';
    		
    	
    		
    	}
    	$cadena .= "</div>";
    	//$cadena .= "</div>";
    	
    	echo $cadena;
    	 
    	 
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
