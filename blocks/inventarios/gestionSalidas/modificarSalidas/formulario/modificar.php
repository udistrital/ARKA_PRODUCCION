<?php
if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class registrarForm {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miSql;
	function __construct($lenguaje, $formulario, $sql) {
		$this->miConfigurador = \Configurador::singleton ();
		
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		
		$this->lenguaje = $lenguaje;
		
		$this->miFormulario = $formulario;
		
		$this->miSql = $sql;
	}
	function miForm() {
		
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
		
		$_REQUEST ['tiempo'] = time ();
		$tiempo = $_REQUEST ['tiempo'];
		
		// lineas para conectar base de d atos-------------------------------------------------------------------------------------------------
		$conexion = "inventarios";
		
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$seccion ['tiempo'] = $tiempo;
		
		$arregloES = array (
				$_REQUEST ['numero_entrada'],
				$_REQUEST ['numero_salida'] 
		);
		
		// if (isset ( $_REQUEST ['salidasAS'] )) {
		
		// $motrar = 'block';
		// $selec = '1';
		
		// {
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultarEntradaParticular', $_REQUEST ['numero_entrada'] );
		
		// $entrada = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consulta_elementos_sin_actualizar', $entrada [0] [12] );
		
		// $elementos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		// }
		// } else {
		// echo("asdasd");
		$motrar = 'none';
		$selec = '0';
		
		// {
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarEntradaParticular', $_REQUEST ['numero_entrada'] );
		
		$entrada = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consulta_elementos', $arregloES );
		
		
		$elementos = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
//		var_dump($elementos);
		// }
		// }
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultarEntradaParticular', $_REQUEST ['numero_entrada'] );
		
		// $entrada = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consulta_elementos', $_REQUEST ['numero_entrada'] );
		
		$cantidaditems = count ( $elementos );
		
		// // ___________________________________________________________________________________
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarSalidaParticular', $_REQUEST ['numero_salida'] );
		
		$salida = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		// var_dump($salida);exit;
		$id_funcionario = $salida [0] [2];
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarFuncionario', $salida [0] [2] );
		
		$funcionario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		if ((is_null ( $salida [0] [5] )) == true || $salida [0] [5] == 'null') {
			$ubicacion = - 1;
		} else {
			
			$ubicacion = $salida [0] [5];
		}
		
		$salida = array (
				
				'dependencia' => $salida [0] [0],
				'sede' => $salida [0] [1],
				'observaciones' => $salida [0] [3],
				'funcionarioP' => $salida [0] [2],
				'vigencia' => $salida [0] [4],
				'ubicacion' => $ubicacion 
		);
		
		$_REQUEST = array_merge ( $_REQUEST, $salida );
		
		
		// ---------------- SECCION: Parámetros Generales del Formulario ----------------------------------
		$esteCampo = $esteBloque ['nombre'];
		$atributos ['id'] = $esteCampo;
		$atributos ['nombre'] = $esteCampo;
		// Si no se coloca, entonces toma el valor predeterminado 'application/x-www-form-urlencoded'
		$atributos ['tipoFormulario'] = 'multipart/form-data';
		// Si no se coloca, entonces toma el valor predeterminado 'POST'
		$atributos ['metodo'] = 'POST';
		// Si no se coloca, entonces toma el valor predeterminado 'index.php' (Recomendado)
		$atributos ['action'] = 'index.php';
		// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo );
		// Si no se coloca, entonces toma el valor predeterminado.
		$atributos ['estilo'] = '';
		$atributos ['marco'] = false;
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		{
			
			$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			
			$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
			$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
			$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
			
			$variable = "pagina=" . $miPaginaActual;
			$variable .= "&usuario=" . $_REQUEST ['usuario'];
			$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
			
			// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
			$esteCampo = 'botonRegresar';
			$atributos ['id'] = $esteCampo;
			$atributos ['enlace'] = $variable;
			$atributos ['tabIndex'] = 1;
			$atributos ['estilo'] = 'textoSubtitulo';
			$atributos ['enlaceTexto'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ['ancho'] = '10%';
			$atributos ['alto'] = '10%';
			$atributos ['redirLugar'] = true;
			echo $this->miFormulario->enlace ( $atributos );
			
			unset ( $atributos );
			
			// ---------------- SECCION: Controles del Formulario -----------------------------------------------
			
			$esteCampo = "AgrupacionGeneral";
			$atributos ['id'] = $esteCampo;
			$atributos ['leyenda'] = "Información General de la Salida";
			echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
			{
				
				$datos = unserialize ( $_REQUEST ['datosGenerales'] );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'informacion_anio';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . "    " . $datos [0];
				$atributos ["etiquetaObligatorio"] = false;
				$atributos ['columnas'] = 1;
				$atributos ['dobleLinea'] = 0;
				$atributos ['tabIndex'] = $tab;
				$atributos ['validar'] = '';
				// $atributos ['etiqueta'] =$this->lenguaje->getCadena ( $esteCampo."Nota" );
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['valor'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['valor'] = '';
				}
				
				$atributos ['titulo'] = '';
				$atributos ['deshabilitado'] = true;
				$atributos ['tamanno'] = 10;
				$atributos ['maximoTamanno'] = '';
				$atributos ['anchoEtiqueta'] = 10;
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'informacion_numero_salida';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . "    " . $datos [1];
				$atributos ["etiquetaObligatorio"] = false;
				$atributos ['columnas'] = 1;
				$atributos ['dobleLinea'] = 0;
				$atributos ['tabIndex'] = $tab;
				$atributos ['validar'] = '';
				// $atributos ['etiqueta'] =$this->lenguaje->getCadena ( $esteCampo."Nota" );
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['valor'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['valor'] = '';
				}
				$atributos ['titulo'] = '';
				$atributos ['deshabilitado'] = true;
				$atributos ['tamanno'] = 10;
				$atributos ['maximoTamanno'] = '';
				$atributos ['anchoEtiqueta'] = 10;
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'informacion_numero_entrada';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . "    " . $datos [2];
				$atributos ["etiquetaObligatorio"] = false;
				$atributos ['columnas'] = 1;
				$atributos ['dobleLinea'] = 0;
				$atributos ['tabIndex'] = $tab;
				$atributos ['validar'] = '';
				// $atributos ['etiqueta'] =$this->lenguaje->getCadena ( $esteCampo."Nota" );
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['valor'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['valor'] = '';
				}
				$atributos ['titulo'] = '';
				$atributos ['deshabilitado'] = true;
				$atributos ['tamanno'] = 10;
				$atributos ['maximoTamanno'] = '';
				$atributos ['anchoEtiqueta'] = 10;
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'informacion_identificacion';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . $datos [3];
				$atributos ["etiquetaObligatorio"] = false;
				$atributos ['columnas'] = 1;
				$atributos ['dobleLinea'] = 0;
				$atributos ['tabIndex'] = $tab;
				$atributos ['validar'] = '';
				// $atributos ['etiqueta'] =$this->lenguaje->getCadena ( $esteCampo."Nota" );
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['valor'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['valor'] = '';
				}
				$atributos ['titulo'] = '';
				$atributos ['deshabilitado'] = true;
				$atributos ['tamanno'] = 10;
				$atributos ['maximoTamanno'] = '';
				$atributos ['anchoEtiqueta'] = 10;
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'informacion_funcionario';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . $datos [4];
				$atributos ["etiquetaObligatorio"] = false;
				$atributos ['columnas'] = 1;
				$atributos ['dobleLinea'] = 0;
				$atributos ['tabIndex'] = $tab;
				$atributos ['validar'] = '';
				// $atributos ['etiqueta'] =$this->lenguaje->getCadena ( $esteCampo."Nota" );
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['valor'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['valor'] = '';
				}
				$atributos ['titulo'] = '';
				$atributos ['deshabilitado'] = true;
				$atributos ['tamanno'] = 10;
				$atributos ['maximoTamanno'] = '';
				$atributos ['anchoEtiqueta'] = 10;
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTexto ( $atributos );
				unset ( $atributos );
			}
			
			echo $this->miFormulario->agrupacion ( 'fin' );
			
			$esteCampo = "marcoDatosBasicos";
			$atributos ['id'] = $esteCampo;
			$atributos ["estilo"] = "jqueryui";
			$atributos ['tipoEtiqueta'] = 'inicio';
			$atributos ["leyenda"] = "Modificar Salida # : " . $datos [1];
			echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
			unset ( $atributos );
			{
				$esteCampo = 'vigencia';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'jqueryui';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['columnas'] = 1;
				$atributos ['dobleLinea'] = 0;
				$atributos ['tabIndex'] = $tab;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['validar'] = 'required, minSize[4],maxSize[4],custom[onlyNumberSp]';
				
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['valor'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['valor'] = '';
				}
				$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
				$atributos ['deshabilitado'] = false;
				$atributos ['tamanno'] = 3;
				$atributos ['maximoTamanno'] = '';
				$atributos ['anchoEtiqueta'] = 220;
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				// echo $this->miFormulario->campoCuadroTexto ( $atributos );
				unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'sede';
				$atributos ['columnas'] = 3;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab;
				$atributos ['tamanno'] = 1;
				$atributos ['estilo'] = 'jqueryui';
				$atributos ['validar'] = 'required';
				$atributos ['limitar'] = true;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['anchoEtiqueta'] = 150;
				
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['seleccion'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['seleccion'] = - 1;
				}
				
				$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "sede" );
				$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
				$atributos ['matrizItems'] = $matrizItems;
				
				// Utilizar lo siguiente cuando no se pase un arreglo:
				// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
				// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
				$tab ++;
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				
				$esteCampo = "dependencia";
				$atributos ['columnas'] = 3;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab;
				$atributos ['tamanno'] = 1;
				$atributos ['estilo'] = 'jqueryui';
				$atributos ['validar'] = 'required';
				$atributos ['limitar'] = true;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['anchoEtiqueta'] = 150;
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['seleccion'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['seleccion'] = - 1;
				}
				$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "dependencias" );
				
				$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
				$atributos ['matrizItems'] = $matrizItems;
				
				// Utilizar lo siguiente cuando no se pase un arreglo:
				// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
				// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
				$tab ++;
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				
				$esteCampo = "ubicacion";
				$atributos ['columnas'] = 3;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab;
				$atributos ['tamanno'] = 1;
				$atributos ['estilo'] = 'jqueryui';
				$atributos ['validar'] = 'required';
				$atributos ['limitar'] = true;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['anchoEtiqueta'] = 165;
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['seleccion'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['seleccion'] = - 1;
				}
				$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "ubicaciones" );
				
				$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
				$atributos ['matrizItems'] = $matrizItems;
				
				// Utilizar lo siguiente cuando no se pase un arreglo:
				// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
				// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
				$tab ++;
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				
				$esteCampo = "funcionarioP";
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['anchoEtiqueta'] = 220;
				$atributos ['evento'] = '';
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['seleccion'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['seleccion'] = - 1;
				}
				$atributos ['deshabilitado'] = false;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['estilo'] = "jqueryui";
				$atributos ['validar'] = "required";
				$atributos ['limitar'] = true;
				$atributos ['anchoCaja'] = 52;
				$atributos ['miEvento'] = '';
				$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "funcionarios" );
				$matrizItems = array (
						array (
								0,
								' ' 
						) 
				);
				$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
				$atributos ['matrizItems'] = $matrizItems;
				// $atributos['miniRegistro']=;
				$atributos ['baseDatos'] = "inventarios";
				// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				
				// $esteCampo = "identificacion";
				// $atributos ['id'] = $esteCampo;
				// $atributos ['nombre'] = $esteCampo;
				// $atributos ['tipo'] = 'text';
				// $atributos ['estilo'] = 'jqueryui';
				// $atributos ['marco'] = true;
				// $atributos ['estiloMarco'] = '';
				// $atributos ['columnas'] = 1;
				// $atributos ['dobleLinea'] = 0;
				// $atributos ['tabIndex'] = $tab;
				// $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				// $atributos ['validar'] = 'required, minSize[5],maxSize[15],custom[onlyNumberSp]';
				
				// if (isset ( $_REQUEST [$esteCampo] )) {
				// $atributos ['valor'] = $_REQUEST [$esteCampo];
				// } else {
				// $atributos ['valor'] = '';
				// }
				// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
				// $atributos ['deshabilitado'] = false;
				// $atributos ['tamanno'] = 15;
				// $atributos ['maximoTamanno'] = '';
				// $atributos ['anchoEtiqueta'] = 220;
				// $tab ++;
				
				// // Aplica atributos globales al control
				// $atributos = array_merge ( $atributos, $atributosGlobales );
				// echo $this->miFormulario->campoCuadroTexto ( $atributos );
				// unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'observaciones';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'jqueryui';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['columnas'] = 105;
				$atributos ['filas'] = 5;
				$atributos ['dobleLinea'] = 0;
				$atributos ['tabIndex'] = $tab;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['validar'] = ' minSize[1]';
				$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
				$atributos ['deshabilitado'] = false;
				$atributos ['tamanno'] = 20;
				$atributos ['maximoTamanno'] = '';
				$atributos ['anchoEtiqueta'] = 220;
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['valor'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['valor'] = '';
				}
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoTextArea ( $atributos );
				unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
				
				$esteCampo = 'actualizar';
				$atributos ['columnas'] = 1;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['seleccion'] = 1;
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ['tab'] = $tab;
				$atributos ['tamanno'] = 1;
				$atributos ['estilo'] = 'jqueryui';
				$atributos ['validar'] = '';
				$atributos ['limitar'] = true;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['anchoEtiqueta'] = 213;
				// Valores a mostrar en el control
				$matrizItems = array (
						array (
								0,
								'NO' 
						),
						array (
								1,
								'SI' 
						) 
				);
				
				$atributos ['matrizItems'] = $matrizItems;
				
				// Utilizar lo siguiente cuando no se pase un arreglo:
				// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
				// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
				$tab ++;
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				
				$atributos ["id"] = "itemsAgr";
				$atributos ["estiloEnLinea"] = "display:block";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "Agrupacion";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Elementos Asignados al Funcionario";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					
					{
						$cantidaditems = count ( $elementos );
						
						if ($elementos) {
							
							echo "<table id='tablaTitulos'>";
							
							echo "<thead>
				                <tr>
				                   <th>Nivel Inventarios</th>
				                    <th>Cantidad</th>
									<th>Nombre</th>
									<th>Elementos Descargar</th>
							    </tr>
					            </thead>
					            <tbody>";
							
							for($i = 0; $i < count ( $elementos ); $i ++) {
								
								$cadenaSql = $this->miSql->getCadenaSql ( 'busqueda_elementos_individuales_cantidad_restante', $elementos [$i] [0] );
								
								$elementos_restantes = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
								
								$cantidad_sobrante = count ( $elementos_restantes );
								
								if ($elementos_restantes) {
									$mostrarHtml = "<tr>
						                    <td><center>" . $elementos [$i] ['item'] . "</center></td>
						                    <td><center>" . $elementos [$i] ['cantidad_asignada'] . "</center></td>";
									
									$mostrarHtml .= "<td><center>" . $elementos [$i] ['descripcion'] . "</center></td>
				   							<td><center>";
									
									// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
									$nombre = 'item' . $i;
									$atributos ['id'] = $nombre;
									$atributos ['nombre'] = $nombre;
									$atributos ['marco'] = true;
									$atributos ['estiloMarco'] = true;
									$atributos ["etiquetaObligatorio"] = true;
									$atributos ['columnas'] = 1;
									$atributos ['dobleLinea'] = 1;
									$atributos ['tabIndex'] = $tab;
									$atributos ['etiqueta'] = '';
									if (isset ( $_REQUEST [$esteCampo] )) {
										$atributos ['valor'] = $_REQUEST [$esteCampo];
									} else {
										$atributos ['valor'] = serialize ( array (
												"identificacion_elemento" => $elementos [$i] ['id_elemento_ind'],
												"identificacion_elemento_general" => $elementos [$i] ['id_elemento'],
												"tipo_bien"=>$elementos[$i]['tipo_bien'],
												"cantidad_por_asignar"=>$elementos[$i]['cantidad_por_asignar'],
												"cantidad_asignada"=>$elementos[$i]['cantidad_asignada'],
										)
										 );
									}
									
									$atributos ['deshabilitado'] = false;
									$tab ++;
									
									// Aplica atributos globales al control
									$atributos = array_merge ( $atributos, $atributosGlobales );
									$mostrarHtml .= $this->miFormulario->campoCuadroSeleccion ( $atributos );
									
									$mostrarHtml .= "</center></td>
                    						</tr>";
									echo $mostrarHtml;
									unset ( $mostrarHtml );
									unset ( $variable );
								}
							}
							
							echo "</tbody>";
							
							echo "</table>";
						}
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
					// $esteCampo = "Agrupacion";
					// $atributos ['id'] = $esteCampo;
					// $atributos ['leyenda'] = "Elementos Referentes a las Entrada";
					// echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					
					// {
					
					// if ($elementos) {
					
					// echo "<table id='tablaTitulos'>";
					
					// echo "<thead>
					// <tr>
					// <th>Item</th>
					// <th>Cantidad</th>
					// <th>Descripción</th>
					// <th>Selección Items</th>
					// </tr>
					// </thead>
					// <tbody>";
					
					// for($i = 0; $i < count ( $elementos ); $i ++) {
					
					// $mostrarHtml = "<tr>
					// <td><center>" . $elementos [$i] [1] . "</center></td>
					// <td><center>" . $elementos [$i] [2] . "</center></td>
					// <td><center>" . $elementos [$i] [3] . "</center></td>
					// <td><center>";
					
					// // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					// $nombre = 'item' . $i;
					// $atributos ['id'] = $nombre;
					// $atributos ['nombre'] = $nombre;
					// $atributos ['marco'] = true;
					// $atributos ['estiloMarco'] = true;
					// $atributos ["etiquetaObligatorio"] = true;
					// $atributos ['columnas'] = 1;
					// $atributos ['dobleLinea'] = 1;
					// $atributos ['tabIndex'] = $tab;
					// $atributos ['etiqueta'] = '';
					// if (isset ( $_REQUEST [$esteCampo] )) {
					// $atributos ['valor'] = $_REQUEST [$esteCampo];
					// } else {
					// $atributos ['valor'] = $elementos [$i] [0];
					// }
					
					// $atributos ['deshabilitado'] = false;
					// $tab ++;
					
					// // Aplica atributos globales al control
					// $atributos = array_merge ( $atributos, $atributosGlobales );
					// $mostrarHtml .= $this->miFormulario->campoCuadroSeleccion ( $atributos );
					
					// $mostrarHtml .= "</center></td>
					// </tr>";
					// echo $mostrarHtml;
					// unset ( $mostrarHtml );
					// unset ( $variable );
					// }
					
					// echo "</tbody>";
					
					// echo "</table>";
					// }
					// }
					// echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				// ------------------Division para los botones-------------------------
				$atributos ["id"] = "botones";
				$atributos ["estilo"] = "marcoBotones";
				echo $this->miFormulario->division ( "inicio", $atributos );
				
				// -----------------CONTROL: Botón ----------------------------------------------------------------
				$esteCampo = 'botonAceptar';
				$atributos ["id"] = $esteCampo;
				$atributos ["tabIndex"] = $tab;
				$atributos ["tipo"] = 'boton';
				// submit: no se coloca si se desea un tipo button genérico
				$atributos ['submit'] = true;
				$atributos ["estiloMarco"] = '';
				$atributos ["estiloBoton"] = 'jqueryui';
				// verificar: true para verificar el formulario antes de pasarlo al servidor.
				$atributos ["verificar"] = '';
				$atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
				$atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
				$tab ++;
				
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoBoton ( $atributos );
				// -----------------FIN CONTROL: Botón -----------------------------------------------------------
				
				echo $this->miFormulario->division ( 'fin' );
				
				echo $this->miFormulario->marcoAgrupacion ( 'fin' );
				
				// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
				
				// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
				// Se debe declarar el mismo atributo de marco con que se inició el formulario.
			}
			
			// -----------------FIN CONTROL: Botón -----------------------------------------------------------
			
			// ------------------Fin Division para los botones-------------------------
			echo $this->miFormulario->division ( "fin" );
			
			// ------------------- SECCION: Paso de variables ------------------------------------------------
			
			/**
			 * En algunas ocasiones es útil pasar variables entre las diferentes páginas.
			 * SARA permite realizar esto a través de tres
			 * mecanismos:
			 * (a). Registrando las variables como variables de sesión. Estarán disponibles durante toda la sesión de usuario. Requiere acceso a
			 * la base de datos.
			 * (b). Incluirlas de manera codificada como campos de los formularios. Para ello se utiliza un campo especial denominado
			 * formsara, cuyo valor será una cadena codificada que contiene las variables.
			 * (c) a través de campos ocultos en los formularios. (deprecated)
			 */
			
			// En este formulario se utiliza el mecanismo (b) para pasar las siguientes variables:
			
			$valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
			$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
			$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
			$valorCodificado .= "&opcion=modificar";
			$valorCodificado .= "&numero_entrada=" . $_REQUEST ['numero_entrada'];
			$valorCodificado .= "&numero_salida=" . $_REQUEST ['numero_salida'];
			$valorCodificado .= "&salida=" . $datos[1];
			$valorCodificado .= "&id_funcionario=" . $id_funcionario;
			$valorCodificado .= "&cantidadItems=" . $cantidaditems;
			$valorCodificado .= "&vigencia=" . $salida ['vigencia'];
			$valorCodificado .= "&usuario=" . $_REQUEST ['usuario'];
			
			if (isset ( $_REQUEST ['salidasAS'] )) {
				$valorCodificado .= "&Re_Actualizacion=1";
			}
			/**
			 * SARA permite que los nombres de los campos sean dinámicos.
			 * Para ello utiliza la hora en que es creado el formulario para
			 * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
			 * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
			 * (b) asociando el tiempo en que se está creando el formulario
			 */
			$valorCodificado .= "&campoSeguro=" . $_REQUEST ['tiempo'];
			$valorCodificado .= "&tiempo=" . time ();
			// Paso 2: codificar la cadena resultante
			$valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar ( $valorCodificado );
			
			$atributos ["id"] = "formSaraData"; // No cambiar este nombre
			$atributos ["tipo"] = "hidden";
			$atributos ['estilo'] = '';
			$atributos ["obligatorio"] = false;
			$atributos ['marco'] = true;
			$atributos ["etiqueta"] = "";
			$atributos ["valor"] = $valorCodificado;
			echo $this->miFormulario->campoCuadroTexto ( $atributos );
			unset ( $atributos );
			
			$atributos ['marco'] = true;
			$atributos ['tipoEtiqueta'] = 'fin';
			echo $this->miFormulario->formulario ( $atributos );
			
			return true;
		}
	}
}

$miSeleccionador = new registrarForm ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->miForm ();
?>
