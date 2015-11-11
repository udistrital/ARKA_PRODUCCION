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

$arreglo = array (
		$_REQUEST ['fecha_inicio_cierre'],
		$_REQUEST ['fecha_fin_cierre'] 
);

$cadenaSql = $this->sql->cadena_sql ( "Actualizar_Periodos", $arreglo );

$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso', $arreglo, "Actualizar_Periodos" );

// Crear Variables necesarias en los métodos

if ($resultado) {
	$this->miConfigurador->setVariableConfiguracion ( "cache", true );
	$this->funcion->Redireccionador ( 'actualizoPeriodo' );
	exit ();
} else {
	$this->funcion->Redireccionador ( 'noactualizoPeriodo' );
	exit ();
}