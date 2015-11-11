<?php

namespace inventarios\gestionElementos\funcionarioElemento\funcion;

use inventarios\gestionElementos\funcionarioElemento\funcion\redireccion;

include_once ('redireccionar.php');
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class RegistradorOrden {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql, $funcion) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	function procesarFormulario() {
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		for($i = 0; $i <= 10000; $i ++) {
			if (isset ( $_REQUEST ['item_' . $i] )) {
				$funcionarios [] = unserialize($_REQUEST ['item_' . $i]);
			}
		}
		
		
		
		
		if (! isset ( $funcionarios )) {
			
			redireccion::redireccionar ( 'Nofuncionario' );
			exit ();
		}
		
		foreach ( $funcionarios as $valor ) {
			
			
			$cadenaSql = $this->miSql->getCadenaSql ( 'Registrar_Radicacion', $valor );
			
			$estado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso", $valor, "Registrar_Radicacion" );
			
			
			
		}
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultarFuncionariosaCargoElementos', $valor );
		
		// $estado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		
		if ($estado == true) {
			
			redireccion::redireccionar ( 'Radicado', $_REQUEST ['usuario'] );
			exit ();
		} else {
			
			redireccion::redireccionar ( 'NoRadicado', $_REQUEST ['usuario'] );
			exit ();
		}
	}
}

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$resultado = $miRegistrador->procesarFormulario ();

?>