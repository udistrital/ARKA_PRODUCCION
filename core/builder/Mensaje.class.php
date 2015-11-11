<?php

/**
 * Mensaje.class.php
 *
 * Encargado de mostrar mensajes de texto cuando ocurrer errores fatales en la aplicacion
 *
 * @author  Paulo Cesar Coronado
 * @version     1.0.0.0
 * @package     config/builder
 * @copyright   Universidad Distrital Francisco Jose de Caldas - Grupo de Trabajo Academico GNU/Linux GLUD
 * @license     GPL Version 3 o posterior
 *
 */

/**
 *
 * @todo Implementar el plugin php-gettext, de tal manera que el archivo de localización esté en formato .mo
 *       Los archivos correspondientes se encuuentran en la carpeta plugin/php-gettext
 */

/**
 * Mejoras ,author: Carlos Romero
 *
 * Esta clase maneja mensajes en el sistema
 * se pueden manejar diferentes tipos salidas de mensajes como
 * html, json, soap, texto, objeto , id (el codigo del mensaje), entrada (cadena de entrada)
 * y se pueden categorizar tal y como se definen en el objeto de formularioHtml
 * information, warning, error,etc....
 * uso:
 * Mensaje->addMensaje("cod",'usuario','error');
 *echo Mensaje->getLastMensaje();
 *si se quiere especificar el tipo de salida. ej:
 *echo Mensaje->getLastMensaje('json');
 * mesnajes al vuelo:
 * $cadenaAlVuelo = 'mensaje al vuelo';
 * Mensaje->addMensaje("cod",':'.$cadenaAlVuelo,'information');
 * echo Mensaje->getLastMensaje();//retornara el mensaje en formato html o el predefinido en la creaci�n del objeto
 */

use \SoapFault;

include_once ("core/builder/FormularioHtml.class.php");
include_once ("core/locale/Lenguaje.class.php");

class Mensaje {
    
    private static $instance;
    
    private $salidaTipo;
    private $mensajeArray = array();
    private $lastId ;
    private $lenguaje;
    private $formulario;
    private $debug;
    
    
    /**
     *
     * @deprecated Arreglo que contiene las variables de configuración
     * @var String
     */
    private $miConfigurador;
    
function __construct($salidaTipo = '', $lenguaje = '') {
		
	
	    if ($salidaTipo!='') $this->salidaTipo = $salidaTipo;
		else $this->salidaTipo = 'html';
		
		$this->debug =  false;
	
	
		if(!is_object($lenguaje))$this->lenguaje = \Lenguaje::singleton();
		else $this->lenguaje = $lenguaje;
        
		$this->formulario = new \FormularioHtml ();
	}
    
    public static function singleton($salidaTipo = '', $lenguaje = '') {
        
        if (! isset ( self::$instance )) {
            $className = __CLASS__;
            self::$instance = new $className ($salidaTipo,$lenguaje);
        }
        return self::$instance;
    
    }
    
    function mostrarMensaje($mensaje, $tipoMensaje = "warning") {
        echo $mensaje;exit;
        if ($mensaje != '' && $tipoMensaje != '') {
            include_once ('Mensaje.page.php');
        }
    
    }
    
    function mostrarMensajeRedireccion($mensaje, $tipoMensaje = "warning", $url) {
        echo $mensaje;exit;
        if ($mensaje != '' && $tipoMensaje != '' && $url != '') {
            include_once ('Mensaje.page.php');
        }
    
    }
    
    /*
     *
    * funcion para configurar el tipo de salida
    * html, json, soap, texto � objeto
    */
    public function setDefaultSalidaTipo($salidaTipo = ''){
    
    	if ($salidaTipo!='') $this->salidaTipo = $salidaTipo;
    	else $this->salidaTipo = 'html';
    }
    
    public function debug($debug = false){
    	if(is_bool($debug)) $this->debug = $debug;
    	$this->debug = false;
    
    }
    
    public function getListaMensajes(){
    	return $mensajeArray;
    }
    
    public function getMensaje($id = 1 , $salidaTipo = ''){
    
    	if ($salidaTipo!='') $this->salidaTipo = $salidaTipo;
    	if(isset($this->mensajeArray[$id]))	return $this->procesarSalida($this->mensajeArray[$id]);
    	return false;
    }
    
    public function getObjetoExepcion($id = 1 ){
    
    	$mensaje = $this->mensajeArray[$id];
    	$this->procesarSalida($mensaje);
    	if(isset($mensaje->exepcion))return $mensaje->exepcion;
    	return false;
    }
    
    
    public function getLastMensaje($salidaTipo = ''){
    	if ($salidaTipo!='') $this->salidaTipo = $salidaTipo;
    	if(isset($this->mensajeArray[$this->lastId]))	return $this->procesarSalida($this->mensajeArray[$this->lastId]);
    	return false;
    }
    
    private function recuperarCadena($cadena = ''){
    	if($cadena != '')return ($this->lenguaje->getCadena($cadena));
    	else return false;
    
    }
    
    private function setLastId($id){
    	$this->lastId = $id;
    }
    
    public function addMensaje($id = 1 , $cadena = '' , $tipoMensaje = 'information' ,$objExepcion = false ){
    
    	//set ultimo id
    	$this->setLastId($id);
    
    	//crea clase std
    	$mensaje = new \stdClass;
    	//set id mensaje
    	$mensaje->id = $id;
    	//set cadena de entrada en el obje
    	$mensaje->entrada = $cadena;
    	//recupera cadena de la clase lenguaje
    	if($cadena[0]==':') {
    		$mensaje->texto = substr($cadena,1,strlen($cadena)-2);
    	}elseif ($cadena[0]!=':'&&count(explode(':',trim($cadena)))>1){
    		$listasCadenas = explode(':',trim($cadena));
    		$mensaje->texto = $this->recuperarCadena($listasCadenas[0]).$listasCadenas[1];
    	}else $mensaje->texto = $this->recuperarCadena($cadena);
    	//set tipo de mensaje
    	$mensaje->tipoMensaje = $tipoMensaje;
    	//set objeto de error
    	if($objExepcion) $mensaje->exepcion = $objExepcion;
    
    	$this->mensajeArray[$id] = $mensaje;
    	 
    	if($this->debug) echo $this->getLastMensaje();
    
    	return true;
    
    
    }
    
    public function limpiarMensajes(){
    	unset($this->mensajeArray);
    	$this->mensajeArray =  array();
    }
    
    
    private function procesarSalida(&$mensaje){
    
    	switch($this->salidaTipo){
    		default:
    		case 'html':
    
    			// -------------Control texto-----------------------
    			$atributos ['mensaje'] = utf8_encode($mensaje->texto);
    			$esteCampo = 'divMensaje';
    			$atributos ['id'] = $esteCampo;
    			$atributos ["tamanno"] = '';
    			$atributos ["estilo"] = $mensaje->tipoMensaje;
    			$atributos ["etiqueta"] = '';
    			$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
    			return $this->formulario->campoMensaje ( $atributos );
    			break;
    		case 'json':
    			return json_encode($mensaje);
    			break;
    		case 'soap':
    			$sMensaje = utf8_encode($mensaje->texto);
    
    			return new SoapFault($mensaje->id, $sMensaje);
    
    			break;
    		case 'texto':
    			return $mensaje->texto;
    			break;
    		case 'objeto':
    			return $mensaje;
    			break;
    		case 'id':
    			return $mensaje->id;
    			break;
    		case 'entrada':
    			return $mensaje->entrada;
    			break;
    		default:
    			return false;
    			break;
    	}
    
    	return false;
    }
    
    public function setLenguaje($lenguaje) {
    	$this->lenguaje = $lenguaje;
    }
    
    public function setFormulario($formulario) {
    	$this->formulario = $formulario;
    }
    

}
?>