<?php 
namespace arka\catalogo\editarCatalogo;



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
    var $arrayElementos ;
    var $arrayDatos ;
    var $funcion;

    function __construct($lenguaje, $formulario , $sql, $funcion) {

        $this->miConfigurador = \Configurador::singleton ();

        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;
        
        $this->sql = $sql;
        
        $this->funcion = $funcion;
        
        $conexion = "catalogo";
        $this->esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);
        if (!$this->esteRecursoDB) {
        	//Este se considera un error fatal
        	exit;
        }

    }

    function formulario() {
		
    	//validar request idCatalogo
    	if(!isset($_REQUEST['idCatalogo'])){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'errorId' );
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
    
    private function consultarElementos(){
    	
    	$cadena_sql = $this->sql->getCadenaSql("listarElementos",$_REQUEST['idCatalogo']);
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	
    	
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'catalogoVacio' );
    		$this->mensaje();
    		exit;
    	}
    	
    	$this->arrayElementos = $registros;
    	
    }
    
    private function consultarDatosCatalogo(){
    	
    	$cadena_sql = $this->sql->getCadenaSql("buscarCatalogoId",$_REQUEST['idCatalogo']);
    	$registros = $this->esteRecursoDB->ejecutarAcceso($cadena_sql,"busqueda");
    	 
    	 
    	if(!$registros){
    		$this->miConfigurador->setVariableConfiguracion ( 'mostrarMensaje', 'catalogoVacio' );
    		$this->mensaje();
    		exit;
    	}
    	 
    	$this->arrayDatos = $registros;
    }
    
    
    private function  principal(){
    	/**
    	 * IMPORTANTE: Este formulario está utilizando jquery.
    	 * Por tanto en el archivo ready.php se delaran algunas funciones js
    	 * que lo complementan.
    	 */
    	
    	// Rescatar los datos de este bloque
    	$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
    	
    	// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
    	/**
    	 * Atributos que deben ser aplicados a todos los controles de este formulario.
    	 * Se utiliza un arreglo
    	 * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
    	 *
    	 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
    	 * $atributos= array_merge($atributos,$atributosGlobales);
    	*/
    	$atributosGlobales ['campoSeguro'] = 'true';
    	$_REQUEST['tiempo']=time();
    	
    	// -------------------------------------------------------------------------------------------------
    	
    	// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
    	
    	
    	// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
    	
    	
    	// Si no se coloca, entonces toma el valor predeterminado 'POST'
    	
    	
    	// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
    	
    	
    	// Si no se coloca, entonces toma el valor predeterminado.
    	
    	// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
    	$tab = 1;
    	// ----------------INICIAR EL FORMULARIO de nombre catalogo ------------------------------------------------------------
    	$esteCampo = $esteBloque ['nombre']."_1";
    	$atributos ['action'] = 'index.php';
    	$atributos ['tipoFormulario'] = '';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipoEtiqueta'] = 'inicio';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['estilo'] = '';
    	$atributos ['marco'] = true;
    	
    	$atributos ['metodo'] = 'POST';
    	echo $this->miFormulario->formulario ( $atributos );

    	/*
    	// ------------------Division para los botones-------------------------
    	$atributos ["id"] = "nombreCat";
    	$atributos ["estilo"] = "marcoBotones";
    	echo $this->miFormulario->division ( "inicio", $atributos );
    	*/
    	 
    	 
    	// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    	$esteCampo = 'nombreCatalogo';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipo'] = 'text';
    	$atributos ['estilo'] = 'jqueryui';
    	$atributos ['marco'] = true;
    	$atributos ['columnas'] = 1;
    	$atributos ['dobleLinea'] = false;
    	$atributos ['tabIndex'] = $tab;
    	$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['validar'] = 'required,onlyLetterNumber';
    	$atributos ['valor'] = $this->arrayDatos[0]['lista_nombre'];
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    	$atributos ['deshabilitado'] = false;
    	$atributos ['tamanno'] = 50;
    	$atributos ['maximoTamanno'] = '';
    	$tab ++;
    	
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	

    	// ------------------Fin Division para los botones-------------------------
    	//echo $this->miFormulario->division ( "fin" );
    	
    	
    	echo "<br><br>";
    	
    	
    	/*
    	// ------------------Division para los botones-------------------------
    	 
    	$atributos ["id"] = "botones_1";
    	$atributos ["estilo"] = "marcoBotones";
    	echo $this->miFormulario->division ( "inicio", $atributos );
    	 */
    	// -----------------CONTROL: Botón ----------------------------------------------------------------
    	$esteCampo = 'cambiarNombre';
    	$atributos ["id"] = $esteCampo;
    	$atributos ["tabIndex"] = $tab;
    	$atributos ["tipo"] = 'boton';
    	// submit: no se coloca si se desea un tipo button genérico
    	//$atributos ['submit'] = true;
    	$atributos ["estiloMarco"] = '';
    	$atributos ["estiloBoton"] = 'jqueryui';
    	// verificar: true para verificar el formulario antes de pasarlo al servidor.
    	$atributos ["verificar"] = 'true';
    	//$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
    	$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
    	$atributos ['onClick'] = 'cambiarNombreCatalogo()';
    	$tab ++;
    	 
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoBoton ( $atributos );
    	// -----------------FIN CONTROL: Botón -----------------------------------------------------------
    	   
    	
    	// ------------------Fin Division para los botones-------------------------
    	//echo $this->miFormulario->division ( "fin" );
    	 
    	
    	// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
    	// Se debe declarar el mismo atributo de marco con que se inició el formulario.
    	$atributos ['marco'] = true;
    	$atributos ['tipoEtiqueta'] = 'fin';
    	echo $this->miFormulario->formulario ( $atributos );
    	 
    	
    	// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
    	$esteCampo = $esteBloque ['nombre'];
    	$atributos ['action'] = 'index.php';
    	$atributos ['tipoFormulario'] = '';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipoEtiqueta'] = 'inicio';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['estilo'] = '';
    	$atributos ['marco'] = true;
    	
    	$atributos ['metodo'] = 'POST';
    	echo $this->miFormulario->formulario ( $atributos );
    	
    	
    	// ---------------- SECCION: Controles del Formulario -----------------------------------------------
    	/*
    	// ------------------Division para los botones-------------------------
    	$atributos ["id"] = "agregar";
    	$atributos ["estilo"] = "marcoBotones";
    	echo $this->miFormulario->division ( "inicio", $atributos );
    	 */
    	
    	// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    	$esteCampo = 'id';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipo'] = 'text';
    	$atributos ['estilo'] = 'jqueryui';
    	$atributos ['marco'] = true;
    	$atributos ['columnas'] = 1;
    	$atributos ['dobleLinea'] = false;
    	$atributos ['tabIndex'] = $tab;
    	$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['validar'] = 'required,number';
    	$atributos ['valor'] = '';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    	$atributos ['deshabilitado'] = false;
    	$atributos ['tamanno'] = 50;
    	$atributos ['maximoTamanno'] = '';
    	$tab ++;
    	
    	
    	
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	
    	/*
    	// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    	$esteCampo = 'idPadre';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipo'] = 'text';
    	$atributos ['estilo'] = 'jqueryui';
    	$atributos ['marco'] = true;
    	$atributos ['columnas'] = 1;
    	$atributos ['dobleLinea'] = false;
    	$atributos ['tabIndex'] = $tab;
    	$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['validar'] = 'required';
    	$atributos ['valor'] = '0';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    	$atributos ['deshabilitado'] = false;
    	$atributos ['tamanno'] = 50;
    	$atributos ['maximoTamanno'] = '';
    	$tab ++;
    	 
    	 
    	 
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	 */
    	
    	echo '<div class="jqueryui  anchoColumna1">';
    	echo '<div style="float:left; width:150px"><label for="idPadre">Identificador Padre</label></div>';
    	echo '<div style="float:left; width:536px;"><div  tabindex="4" size="50" value="0" name="lidPadre" id="lidPadre" title="Ingrese Identificador padre, coloque 0 si no tiene" class="ui-widget ui-widget-content ui-corner-all">0</div></div>';
    	echo '<input type="hidden" tabindex="4" size="50" value="0" name="idPadre" id="idPadre" title="Ingrese Identificador padre, coloque 0 si no tiene" class="ui-widget ui-widget-content ui-corner-all  validate[required] ">';
    	echo '</div>';
    	
    	// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
    	$esteCampo = 'nombreElemento';
    	$atributos ['id'] = $esteCampo;
    	$atributos ['nombre'] = $esteCampo;
    	$atributos ['tipo'] = 'text';
    	$atributos ['estilo'] = 'jqueryui';
    	$atributos ['marco'] = true;
    	$atributos ['columnas'] = 1;
    	$atributos ['dobleLinea'] = false;
    	$atributos ['tabIndex'] = $tab;
    	$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['validar'] = 'required,onlyLetterNumber';
    	$atributos ['valor'] = '';
    	$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
    	$atributos ['deshabilitado'] = false;
    	$atributos ['tamanno'] = 50;
    	$atributos ['maximoTamanno'] = '';
    	$tab ++;
    	 
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	 
    	
    	echo "<br><br>";
    	
    	// ------------------Fin Division para los botones-------------------------
    	//echo $this->miFormulario->division ( "fin" );
    	 
    	
    	// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
    	
    	// ------------------Division para los botones-------------------------
    	/*
    	$atributos ["id"] = "botones";
    	$atributos ["estilo"] = "marcoBotones";
    	echo $this->miFormulario->division ( "inicio", $atributos );
    	*/
    	// -----------------CONTROL: Botón ----------------------------------------------------------------
    	$esteCampo = 'agregar';
    	$atributos ["id"] = $esteCampo;
    	$atributos ["tabIndex"] = $tab;
    	$atributos ["tipo"] = 'boton';
    	// submit: no se coloca si se desea un tipo button genérico
    	//$atributos ['submit'] = true;
    	$atributos ["estiloMarco"] = '';
    	$atributos ["estiloBoton"] = 'jqueryui';
    	// verificar: true para verificar el formulario antes de pasarlo al servidor.
    	$atributos ["verificar"] = 'true';
    	//$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
    	$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
    	$atributos ['onClick'] = 'agregarElementoCatalogo()';
    	$tab ++;
    	
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoBoton ( $atributos );
    	// -----------------FIN CONTROL: Botón -----------------------------------------------------------
    	
    	
    	// -----------------CONTROL: Botón ----------------------------------------------------------------
    	$esteCampo = 'reiniciar';
    	$atributos ["id"] = $esteCampo;
    	$atributos ["tabIndex"] = $tab;
    	$atributos ["tipo"] = 'boton';
    	// submit: no se coloca si se desea un tipo button genérico
    	//$atributos ['submit'] = true;
    	$atributos ["estiloMarco"] = '';
    	$atributos ["estiloBoton"] = 'jqueryui';
    	// verificar: true para verificar el formulario antes de pasarlo al servidor.
    	$atributos ["verificar"] = 'true';
    	//$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
    	$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
    	$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
    	$atributos ['onClick'] = 'reiniciarEdicion('.$_REQUEST['idCatalogo'].')';
    	$tab ++;
    	 
    	// Aplica atributos globales al control
    	//$atributos = array_merge ( $atributos, $atributosGlobales );
    	echo $this->miFormulario->campoBoton ( $atributos );
    	// -----------------FIN CONTROL: Botón -----------------------------------------------------------
    	   
    	
    	// ------------------Fin Division para los botones-------------------------
    	//echo $this->miFormulario->division ( "fin" );
    	
    	
    	$atributos ["id"] = "idCatalogo"; // No cambiar este nombre
    	$atributos ["tipo"] = "hidden";
    	$atributos ['estilo'] = '';
    	$atributos ["obligatorio"] = false;
    	$atributos ['marco'] = true;
    	$atributos ["etiqueta"] = "";
    	$atributos ["valor"] = $_REQUEST['idCatalogo'];
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );

    	$atributos ["id"] = "idReg"; // No cambiar este nombre
    	$atributos ["tipo"] = "hidden";
    	$atributos ['estilo'] = '';
    	$atributos ["obligatorio"] = false;
    	$atributos ['marco'] = true;
    	$atributos ["etiqueta"] = "";
    	$atributos ["valor"] = 0;
    	echo $this->miFormulario->campoCuadroTexto ( $atributos );
    	
    	// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
    	// Se debe declarar el mismo atributo de marco con que se inició el formulario.
    	$atributos ['marco'] = true;
    	$atributos ['tipoEtiqueta'] = 'fin';
    	echo $this->miFormulario->formulario ( $atributos );
    	
    	/*
    	$atributos ["id"] = "arbol";
    	$atributos ["estilo"] = "marcoBotones";
    	echo $this->miFormulario->division ( "inicio", $atributos );

    	// ------------------Fin Division para los botones-------------------------
    	echo $this->miFormulario->division ( "fin" );
    	 */
    	 
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

$miFormulario = new Formulario ( $this->lenguaje, $this->miFormulario,$this->sql, $this );


$miFormulario->formulario ();
$miFormulario->mensaje ();

?>