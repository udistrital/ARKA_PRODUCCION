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

$cadenaSql = $this->sql->cadena_sql ( "Verificar_Periodo" );

$periodo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );

$periodo = $periodo [0];

// ------ Historial Placas levatamiento Existencia ---------------
$cadenaSql = $this->sql->cadena_sql ( "Inhabilitar_periodos_anteriores" );

$inhabilitar = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso' );

$cadenaSql = $this->sql->cadena_sql ( "Rescatar_Verificacion_Placas" );

$verificacion_placas = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda' );

foreach ( $verificacion_placas as $valor ) {
	
	$arreglo = array (
			$periodo ['id_periodolevantamiento'],
			$valor ['funcionario'],
			$valor ['placa'],
			$valor ['confirmada_existencia'],
			date ( 'Y-m-d' ),
			$valor ['ubicacion_elemento'] 
	);
	
	$cadenaSql = $this->sql->cadena_sql ( "Registrar_Historial_Placas", $arreglo );
	
	$registro_historial_placas = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso',$arreglo,"Registrar_Historial_Placas" );
}

$cadenaSql = $this->sql->cadena_sql ( "Rescatar_Datos_Levantamiento" );

$datos_levantamiento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'busqueda');

foreach ( $datos_levantamiento as $valor ) {
	$arreglo = array (
			$periodo ['id_periodolevantamiento'],
			$valor ['id_dependencia'],
			$valor ['funcionario'],
			$valor ['num_elementos'],
			$valor ['radicacion'],
			$valor ['aprobacion'],
			date ( 'Y-m-d' ),
			$valor ['id_sede'] 
	);
	
	$cadenaSql = $this->sql->cadena_sql ( "Registrar_Historial_Funcionario", $arreglo );
	
	$registro_historial_Funcionario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso',$arreglo,"Registrar_Historial_Funcionario" );
}

$cadenaSql = $this->sql->cadena_sql ( "Actualizar_Tabla_Periodo" );

$Actualizacion_Periodo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso',"","Actualizar_Tabla_Periodo" );

$cadenaSql = $this->sql->cadena_sql ( "Actualizar_Periodos_Anterioes" );

$Sin_Periodos_Existentes = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, 'acceso',"","Actualizar_Periodos_Anterioes" );

// Crear Variables necesarias en los métodos

$arreglo = array (
		"fecha_inicio_cierre" => $periodo ['fecha_inicio'],
		"fecha_fin_cierre" => $periodo ['fecha_inicio'] 
)
;

$_REQUEST = array_merge ( $_REQUEST, $arreglo );

if ($registro_historial_placas && $registro_historial_Funcionario) {
	$this->miConfigurador->setVariableConfiguracion ( "cache", true );
	$this->funcion->Redireccionador ( 'CerroPeriodo' );
	exit ();
} else {
	$this->funcion->Redireccionador ( 'noCerroPeriodo' );
	exit ();
}