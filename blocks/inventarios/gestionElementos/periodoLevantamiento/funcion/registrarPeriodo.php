<?php

/*
 * To change this license header, choose License Headers in Project Properties. To change this template file, choose Tools | Templates and open the template in the editor.
 */
$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );

$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/";
$rutaBloque .= $esteBloque ['nombre'];
$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/" . $esteBloque ['nombre'];

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );

$resultado = '';

$fechaActual = date ( 'Y-m-d' );

$cadenaSql = $this->sql->cadena_sql ( "Limpiar_Elementos_Individuales" );

$Limpieza_elementos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso', "", "Limpiar_Elementos_Individuales" );

$cadenaSql = $this->sql->cadena_sql ( "Limpiar_Radicados" );

$Limpieza_radicados = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso', "", "Limpiar_Radicados" );

$arreglo = array (
		$_REQUEST ['fecha_inicio_cierre'],
		$_REQUEST ['fecha_fin_cierre'],
		date ( 'Y' ),
		date ( 'Y-m-d' ) 
);

$cadenaSql = $this->sql->cadena_sql ( "Actualizar_Periodos_Anterioes" );

$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso', "", "Actualizar_Periodos_Anterioes" );

$cadenaSql = $this->sql->cadena_sql ( "registroPeriodo", $arreglo );

$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso', $arreglo, "registroPeriodo" );

// Crear Variables necesarias en los métodos

if ($resultado) {
	$this->miConfigurador->setVariableConfiguracion ( "cache", true );
	$this->funcion->Redireccionador ( 'registroPeriodo', array (
			date ( 'Y' ),
			$_REQUEST ['usuario'] 
	) );
	exit ();
} else {
	$this->funcion->Redireccionador ( 'noregistroPeriodo', $_REQUEST ['usuario'] );
	exit ();
}