<?php

namespace inventarios\gestionActa\activacionElementosActa;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}

include_once ("core/manager/Configurador.class.php");
class Frontera {
	var $ruta;
	var $sql;
	var $funcion;
	var $lenguaje;
	var $formulario;
	var $miConfigurador;
	function __construct() {
		$this->miConfigurador = \Configurador::singleton ();
	}
	public function setRuta($unaRuta) {
		$this->ruta = $unaRuta;
	}
	public function setLenguaje($lenguaje) {
		$this->lenguaje = $lenguaje;
	}
	public function setFormulario($formulario) {
		$this->formulario = $formulario;
	}
	function frontera() {
		$this->html ();
	}
	function setSql($a) {
		$this->sql = $a;
	}
	function setFuncion($funcion) {
		$this->funcion = $funcion;
	}
	function html() {
		include_once ("core/builder/FormularioHtml.class.php");
		
		$this->ruta = $this->miConfigurador->getVariableConfiguracion ( "rutaBloque" );
		
		$this->miFormulario = new \FormularioHtml ();
		
		if (isset ( $_REQUEST ['opcion'] )) {
			
			switch ($_REQUEST ['opcion']) {
				case "mensaje" :
					include_once ($this->ruta . "/formulario/mensaje.php");
					break;
				
				case "ConsultarActa" :
					include_once ($this->ruta . "/formulario/resultado.php");
					break;
				
				case "consultarElementosActa" :
					include_once ($this->ruta . "/formulario/resultadoElementos.php");
					break;
				
				case "modificarElementos" :
					include_once ($this->ruta . "/formulario/modificarElementos.php");
					break;
				
				case "eliminarElementos" :
					include_once ($this->ruta . "/formulario/eliminarElementos.php");
					break;
				
				case "modificar" :
					include_once ($this->ruta . "/formulario/modificar.php");
					break;
				
				case "eliminarActa" :
					include_once ($this->ruta . "/formulario/eliminar.php");
					break;
			}
		} else {
			$_REQUEST ['opcion'] = "mostrar";
			include_once ($this->ruta . "/formulario/consulta.php");
		}
	}
}

?>
