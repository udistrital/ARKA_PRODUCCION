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
		
		// ___________________________________________________________________________________
		// -------------------------------------------------------------------------------------------------
		
		$conexion = "inventarios";
		
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( "consultaActaParticular", $_REQUEST ['numero_acta'] );
		$acta = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		
		$acta = $acta [0];
		$arreglo = array (
				"numero_contrato" => $acta ['numero_contrato'],
				"fecha_contrato" => $acta ['fecha_contrato'],
				"proveedor" => $acta ['nit_nombre'],
				"id_proveedor" => $acta ['proveedor'],
				"asignacionOrdenador" => $acta ['ordenador_gasto'],
				"nombreOrdenador" => $acta ['nombre_ordenador'],
				"id_ordenador" => $acta ['ordenador_gasto'],
				"sede" => $acta ['sede'],
				"dependencia" => $acta ['dependencia'],
				"tipo_ordenador" => $acta ['tipo_ordenador'],
				"identificacion_ordenador"=>$acta['ordenador_gasto'] 
		);
		
		
		
		
		$_REQUEST = array_merge ( $_REQUEST, $arreglo );
		
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
			$variable .= "&usuario=" . $_REQUEST['usuario'];
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
			
			
			$datos = unserialize ( $_REQUEST ['datosGenerales'] );
			
			$esteCampo = "AgrupacionGeneral";
			$atributos ['id'] = $esteCampo;
			$atributos ['leyenda'] = "Información General Acta de Recibido";
			echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
			{
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'informacion_numero';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . " " . $datos [0];
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
				$esteCampo = 'informacion_fecha';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . " " . $datos [1];
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
				
				$nit = (is_null ( $datos [2] ) == true) ? "No Aplica" : $datos [2];
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'informacion_nit';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . " " . $nit;
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
				
				$razon_social = (is_null ( $datos [3] ) == true) ? "No Aplica" : $datos [3];
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'informacion_proveedor';
				$atributos ['id'] = $esteCampo;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['tipo'] = 'text';
				$atributos ['estilo'] = 'textoSubtituloCursiva';
				$atributos ['marco'] = true;
				$atributos ['estiloMarco'] = '';
				$atributos ['texto'] = $this->lenguaje->getCadena ( $esteCampo ) . $razon_social;
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
			// ---------------- SECCION: Controles del Formulario -----------------------------------------------
			
			$esteCampo = "marcoDatosBasicos";
			$atributos ['id'] = $esteCampo;
			$atributos ["estilo"] = "jqueryui";
			$atributos ['tipoEtiqueta'] = 'inicio';
			$atributos ["leyenda"] = "Registrar Entrada";
			echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
			unset ( $atributos );
			{
				/*
				 * $esteCampo = 'vigencia'; $atributos ['id'] = $esteCampo; $atributos ['nombre'] = $esteCampo; $atributos ['tipo'] = 'text'; $atributos ['estilo'] = 'jqueryui'; $atributos ['marco'] = true; $atributos ['estiloMarco'] = ''; $atributos ["etiquetaObligatorio"] = true; $atributos ['columnas'] = 2; $atributos ['dobleLinea'] = 0; $atributos ['tabIndex'] = $tab; $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo ); $atributos ['validar'] = 'required, minSize[4],maxSize[4],custom[onlyNumberSp]'; if (isset ( $_REQUEST [$esteCampo] )) { $atributos ['valor'] = $_REQUEST [$esteCampo]; } else { $atributos ['valor'] = ''; } $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' ); $atributos ['deshabilitado'] = false; $atributos ['tamanno'] = 3; $atributos ['maximoTamanno'] = ''; $atributos ['anchoEtiqueta'] = 220; $tab ++; // Aplica atributos globales al control $atributos = array_merge ( $atributos, $atributosGlobales ); echo $this->miFormulario->campoCuadroTexto ( $atributos ); unset ( $atributos );
				 */
				$esteCampo = "clase";
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['seleccion'] = - 1;
				$atributos ['anchoEtiqueta'] = 220;
				$atributos ['evento'] = '';
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['valor'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['valor'] = '';
				}
				$atributos ['deshabilitado'] = false;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['estilo'] = "jqueryui";
				$atributos ['validar'] = "required";
				$atributos ['limitar'] = false;
				$atributos ['miEvento'] = '';
				$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
				$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
				
				
				$arreglo = array (
						array (
								'',
								'Sin Entradas Registradas' 
						) 
				);
				
				$matrizItems = $matrizItems [0] [0] != '' ? $matrizItems : $arreglo;
				$atributos ['matrizItems'] = $matrizItems;
				// $atributos['miniRegistro']=;
				$atributos ['baseDatos'] = "inventarios";
				// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				
				$esteCampo = "acta_recibido";
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = false;
				$atributos ['tab'] = $tab ++;
				$atributos ['seleccion'] = - 1;
				$atributos ['anchoEtiqueta'] = 220;
				$atributos ['evento'] = '';
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['valor'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['valor'] = '';
				}
				$atributos ['deshabilitado'] = false;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['estilo'] = "jqueryui";
				$atributos ['validar'] = "";
				$atributos ['limitar'] = true;
				$atributos ['anchoCaja'] = 30;
				$atributos ['miEvento'] = '';
				$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "actasRecibido" );
				
				$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
				$arreglo = array (
						array (
								'',
								'Sin Entradas Registradas' 
						) 
				);
				
				$matrizItems = $matrizItems [0] [0] != '' ? $matrizItems : $arreglo;
				$atributos ['matrizItems'] = $matrizItems;
				// $atributos['miniRegistro']=;
				$atributos ['baseDatos'] = "inventarios";
				// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
				// Aplica atributos globales al control
				$atributos = array_merge ( $atributos, $atributosGlobales );
				// echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				
				$atributos ["id"] = "reposicion";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "AgrupacionReposicion";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Información Reposición";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						$esteCampo = 'id_entradaR';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['seleccion'] = - 1;
						$atributos ['anchoEtiqueta'] = 250;
						$atributos ['evento'] = '';
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['deshabilitado'] = false;
						$atributos ['columnas'] = 2;
						$atributos ['tamanno'] = 1;
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['estilo'] = "jqueryui";
						$atributos ['validar'] = "required";
						$atributos ['limitar'] = true;
						$atributos ['miEvento'] = '';
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultarEntradas" );
						$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
						$atributos ['matrizItems'] = $matrizItems;
						// $atributos['miniRegistro']=;
						$atributos ['baseDatos'] = "inventarios";
						// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroLista ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'id_salidaR';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['seleccion'] = - 1;
						$atributos ['anchoEtiqueta'] = 250;
						$atributos ['evento'] = '';
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['deshabilitado'] = false;
						$atributos ['columnas'] = 2;
						$atributos ['tamanno'] = 1;
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['estilo'] = "jqueryui";
						$atributos ['validar'] = "required";
						$atributos ['limitar'] = true;
						$atributos ['miEvento'] = '';
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultarSalidas" );
						$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
						$atributos ['matrizItems'] = $matrizItems;
						// $atributos['miniRegistro']=;
						$atributos ['baseDatos'] = "inventarios";
						// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroLista ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'id_hurtoR';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['seleccion'] = - 1;
						$atributos ['anchoEtiqueta'] = 250;
						$atributos ['evento'] = '';
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['deshabilitado'] = false;
						$atributos ['columnas'] = 2;
						$atributos ['tamanno'] = 1;
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['estilo'] = "jqueryui";
						$atributos ['validar'] = "required";
						$atributos ['limitar'] = true;
						$atributos ['miEvento'] = '';
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultarHurtos" );
						$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
						$atributos ['matrizItems'] = $matrizItems;
						// $atributos['miniRegistro']=;
						$atributos ['baseDatos'] = "inventarios";
						// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroLista ( $atributos );
						unset ( $atributos );
						
						// // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						// $esteCampo = 'observaciones_reposicion';
						// $atributos ['id'] = $esteCampo;
						// $atributos ['nombre'] = $esteCampo;
						// $atributos ['tipo'] = 'text';
						// $atributos ['estilo'] = 'jqueryui';
						// $atributos ['marco'] = true;
						// $atributos ['estiloMarco'] = '';
						// $atributos ["etiquetaObligatorio"] = true;
						// $atributos ['columnas'] = 105;
						// $atributos ['filas'] = 5;
						// $atributos ['dobleLinea'] = 0;
						// $atributos ['tabIndex'] = $tab;
						// $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ['validar'] = 'required, minSize[1]';
						// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						// $atributos ['deshabilitado'] = false;
						// $atributos ['tamanno'] = 20;
						// $atributos ['maximoTamanno'] = '';
						// $atributos ['anchoEtiqueta'] = 220;
						// if (isset ( $_REQUEST [$esteCampo] )) {
						// $atributos ['valor'] = $_REQUEST [$esteCampo];
						// } else {
						// $atributos ['valor'] = '';
						// }
						// $tab ++;
						// // Aplica atributos globales al control
						// $atributos = array_merge ( $atributos, $atributosGlobales );
						// echo $this->miFormulario->campoTextArea ( $atributos );
						// unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				
				$atributos ["id"] = "donacion";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "AgrupacionDonacion";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Información Donación";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						// // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						// $esteCampo = 'observaciones_donacion';
						// $atributos ['id'] = $esteCampo;
						// $atributos ['nombre'] = $esteCampo;
						// $atributos ['tipo'] = 'text';
						// $atributos ['estilo'] = 'jqueryui';
						// $atributos ['marco'] = true;
						// $atributos ['estiloMarco'] = '';
						// $atributos ["etiquetaObligatorio"] = true;
						// $atributos ['columnas'] = 105;
						// $atributos ['filas'] = 5;
						// $atributos ['dobleLinea'] = 0;
						// $atributos ['tabIndex'] = $tab;
						// $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ['validar'] = 'required, minSize[1]';
						// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						// $atributos ['deshabilitado'] = false;
						// $atributos ['tamanno'] = 20;
						// $atributos ['maximoTamanno'] = '';
						// $atributos ['anchoEtiqueta'] = 220;
						// if (isset ( $_REQUEST [$esteCampo] )) {
						// $atributos ['valor'] = $_REQUEST [$esteCampo];
						// } else {
						// $atributos ['valor'] = '';
						// }
						// $tab ++;
						// // Aplica atributos globales al control
						// $atributos = array_merge ( $atributos, $atributosGlobales );
						// echo $this->miFormulario->campoTextArea ( $atributos );
						// unset ( $atributos );
						// ------------------Division para los botones-------------------------
						
						$esteCampo = "actoAdministrativo";
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["nombre"] = $esteCampo;
						$atributos ["tipo"] = "file";
						$atributos ["obligatorio"] = true;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ["tabIndex"] = $tab ++;
						$atributos ["columnas"] = 1;
						$atributos ["estilo"] = "textoIzquierda";
						$atributos ["anchoEtiqueta"] = 207;
						$atributos ["tamanno"] = 500000;
						$atributos ["validar"] = "required";
						$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ["valor"] = $valorCodificado;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				
				$atributos ["id"] = "sobrante";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "AgrupacionSobrante";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Información Sobrante";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						/*
						 * $esteCampo = 'id_entradaS'; $atributos ['nombre'] = $esteCampo; $atributos ['id'] = $esteCampo; $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo ); $atributos ["etiquetaObligatorio"] = true; $atributos ['tab'] = $tab ++; $atributos ['seleccion'] = - 1; $atributos ['anchoEtiqueta'] = 250; $atributos ['evento'] = ''; if (isset ( $_REQUEST [$esteCampo] )) { $atributos ['valor'] = $_REQUEST [$esteCampo]; } else { $atributos ['valor'] = ''; } $atributos ['deshabilitado'] = false; $atributos ['columnas'] = 2; $atributos ['tamanno'] = 1; $atributos ['ajax_function'] = ""; $atributos ['ajax_control'] = $esteCampo; $atributos ['estilo'] = "jqueryui"; $atributos ['validar'] = "required"; $atributos ['limitar'] = true; $atributos ['miEvento'] = ''; $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultarEntradas" ); $matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" ); $atributos ['matrizItems'] = $matrizItems; // $atributos['miniRegistro']=; $atributos ['baseDatos'] = "inventarios"; // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" ); // Aplica atributos globales al control $atributos = array_merge ( $atributos, $atributosGlobales ); echo $this->miFormulario->campoCuadroLista ( $atributos ); unset ( $atributos ); $esteCampo = 'id_salidaS'; $atributos ['nombre'] = $esteCampo; $atributos ['id'] = $esteCampo; $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo ); $atributos ["etiquetaObligatorio"] = true; $atributos ['tab'] = $tab ++; $atributos ['seleccion'] = - 1; $atributos ['anchoEtiqueta'] = 250; $atributos ['evento'] = ''; if (isset ( $_REQUEST [$esteCampo] )) { $atributos ['valor'] = $_REQUEST [$esteCampo]; } else { $atributos ['valor'] = ''; } $atributos ['deshabilitado'] = false; $atributos ['columnas'] = 2; $atributos ['tamanno'] = 1; $atributos ['ajax_function'] = ""; $atributos ['ajax_control'] = $esteCampo; $atributos ['estilo'] = "jqueryui"; $atributos ['validar'] = "required"; $atributos ['limitar'] = true; $atributos ['miEvento'] = ''; $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultarSalidas" ); $matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" ); $atributos ['matrizItems'] = $matrizItems; // $atributos['miniRegistro']=; $atributos ['baseDatos'] = "inventarios"; // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" ); // Aplica atributos globales al control $atributos = array_merge ( $atributos, $atributosGlobales ); echo $this->miFormulario->campoCuadroLista ( $atributos ); unset ( $atributos ); $esteCampo = 'num_placa'; $atributos ['nombre'] = $esteCampo; $atributos ['id'] = $esteCampo; $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo ); $atributos ["etiquetaObligatorio"] = true; $atributos ['tab'] = $tab ++; $atributos ['seleccion'] = - 1; $atributos ['anchoEtiqueta'] = 250; $atributos ['evento'] = ''; if (isset ( $_REQUEST [$esteCampo] )) { $atributos ['valor'] = $_REQUEST [$esteCampo]; } else { $atributos ['valor'] = ''; } $atributos ['deshabilitado'] = false; $atributos ['columnas'] = 2; $atributos ['tamanno'] = 1; $atributos ['ajax_function'] = ""; $atributos ['ajax_control'] = $esteCampo; $atributos ['estilo'] = "jqueryui"; $atributos ['validar'] = "required"; $atributos ['limitar'] = true; $atributos ['miEvento'] = ''; $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultarPlacas" ); $matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" ); $atributos ['matrizItems'] = $matrizItems; // $atributos['miniRegistro']=; $atributos ['baseDatos'] = "inventarios"; // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" ); // Aplica atributos globales al control $atributos = array_merge ( $atributos, $atributosGlobales ); echo $this->miFormulario->campoCuadroLista ( $atributos ); unset ( $atributos ); $esteCampo = 'valor_sobrante'; $atributos ['id'] = $esteCampo; $atributos ['nombre'] = $esteCampo; $atributos ['tipo'] = 'text'; $atributos ['estilo'] = 'jqueryui'; $atributos ['marco'] = true; $atributos ['estiloMarco'] = ''; $atributos ["etiquetaObligatorio"] = true; $atributos ['columnas'] = 2; $atributos ['dobleLinea'] = 0; $atributos ['tabIndex'] = $tab; $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo ); $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[onlyNumberSp]'; if (isset ( $_REQUEST [$esteCampo] )) { $atributos ['valor'] = $_REQUEST [$esteCampo]; } else { $atributos ['valor'] = ''; } $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' ); $atributos ['deshabilitado'] = false; $atributos ['tamanno'] = 10; $atributos ['maximoTamanno'] = ''; $atributos ['anchoEtiqueta'] = 250; $tab ++; // Aplica atributos globales al control $atributos = array_merge ( $atributos, $atributosGlobales ); echo $this->miFormulario->campoCuadroTexto ( $atributos ); unset ( $atributos );
						 */
						
						$atributos ["id"] = "acta_sobra";
						$atributos ["estiloEnLinea"] = "display:block";
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							$esteCampo = "acta_sobrante";
							$atributos ["id"] = $esteCampo; // No cambiar este nombre
							$atributos ["nombre"] = $esteCampo;
							$atributos ["tipo"] = "file";
							$atributos ["obligatorio"] = true;
							$atributos ["etiquetaObligatorio"] = true;
							$atributos ["tabIndex"] = $tab ++;
							$atributos ["columnas"] = 1;
							$atributos ['tab'] = $tab ++;
							$atributos ["estilo"] = "textoIzquierda";
							$atributos ["anchoEtiqueta"] = 205;
							$atributos ["tamanno"] = 500000;
							$atributos ["validar"] = "required";
							$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
							// $atributos ["valor"] = $valorCodificado;
							$atributos = array_merge ( $atributos, $atributosGlobales );
							echo $this->miFormulario->campoCuadroTexto ( $atributos );
							unset ( $atributos );
						}
						echo $this->miFormulario->division ( "fin" );
						unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				
				$atributos ["id"] = "produccion";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "AgrupacionSobrante";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Información Producción Propia";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						// // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						// $esteCampo = 'observaciones_produccion';
						// $atributos ['id'] = $esteCampo;
						// $atributos ['nombre'] = $esteCampo;
						// $atributos ['tipo'] = 'text';
						// $atributos ['estilo'] = 'jqueryui';
						// $atributos ['marco'] = true;
						// $atributos ['estiloMarco'] = '';
						// $atributos ["etiquetaObligatorio"] = true;
						// $atributos ['columnas'] = 105;
						// $atributos ['filas'] = 5;
						// $atributos ['dobleLinea'] = 0;
						// $atributos ['tabIndex'] = $tab;
						// $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ['validar'] = 'required, minSize[1]';
						// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						// $atributos ['deshabilitado'] = false;
						// $atributos ['tamanno'] = 20;
						// $atributos ['maximoTamanno'] = '';
						// $atributos ['anchoEtiqueta'] = 220;
						// if (isset ( $_REQUEST [$esteCampo] )) {
						// $atributos ['valor'] = $_REQUEST [$esteCampo];
						// } else {
						// $atributos ['valor'] = '';
						// }
						// $tab ++;
						// // Aplica atributos globales al control
						// $atributos = array_merge ( $atributos, $atributosGlobales );
						// echo $this->miFormulario->campoTextArea ( $atributos );
						// unset ( $atributos );
						// ------------------Division para los botones-------------------------
						
						$esteCampo = "acta_produccion";
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["nombre"] = $esteCampo;
						$atributos ["tipo"] = "file";
						$atributos ["obligatorio"] = true;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ["tabIndex"] = $tab ++;
						$atributos ["columnas"] = 1;
						$atributos ["estilo"] = "textoIzquierda";
						$atributos ["anchoEtiqueta"] = 207;
						$atributos ["tamanno"] = 500000;
						$atributos ["validar"] = "required";
						$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ["valor"] = $valorCodificado;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				
				$atributos ["id"] = "recuperacion";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "AgrupacionSobrante";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Información Recuperación";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						// $esteCampo = 'observaciones_recuperacion';
						// $atributos ['id'] = $esteCampo;
						// $atributos ['nombre'] = $esteCampo;
						// $atributos ['tipo'] = 'text';
						// $atributos ['estilo'] = 'jqueryui';
						// $atributos ['marco'] = true;
						// $atributos ['estiloMarco'] = '';
						// $atributos ["etiquetaObligatorio"] = true;
						// $atributos ['columnas'] = 105;
						// $atributos ['filas'] = 5;
						// $atributos ['dobleLinea'] = 0;
						// $atributos ['tabIndex'] = $tab;
						// $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ['validar'] = 'required, minSize[1]';
						// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						// $atributos ['deshabilitado'] = false;
						// $atributos ['tamanno'] = 20;
						// $atributos ['maximoTamanno'] = '';
						// $atributos ['anchoEtiqueta'] = 220;
						// if (isset ( $_REQUEST [$esteCampo] )) {
						// $atributos ['valor'] = $_REQUEST [$esteCampo];
						// } else {
						// $atributos ['valor'] = '';
						// }
						// $tab ++;
						// // Aplica atributos globales al control
						// $atributos = array_merge ( $atributos, $atributosGlobales );
						// echo $this->miFormulario->campoTextArea ( $atributos );
						// unset ( $atributos );
						
						$esteCampo = "acta_recuperacion";
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["nombre"] = $esteCampo;
						$atributos ["tipo"] = "file";
						$atributos ["obligatorio"] = true;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ["tabIndex"] = $tab ++;
						$atributos ["columnas"] = 1;
						$atributos ["estilo"] = "textoIzquierda";
						$atributos ["anchoEtiqueta"] = 207;
						$atributos ["tamanno"] = 500000;
						$atributos ["validar"] = "required";
						$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ["valor"] = $valorCodificado;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				unset ( $atributos );
				
				$atributos ["id"] = "adquisicion";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "AgrupacionSobrante";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Información Adquisición";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						// // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						// $esteCampo = 'observaciones_adquisicion';
						// $atributos ['id'] = $esteCampo;
						// $atributos ['nombre'] = $esteCampo;
						// $atributos ['tipo'] = 'text';
						// $atributos ['estilo'] = 'jqueryui';
						// $atributos ['marco'] = true;
						// $atributos ['estiloMarco'] = '';
						// $atributos ["etiquetaObligatorio"] = true;
						// $atributos ['columnas'] = 105;
						// $atributos ['filas'] = 5;
						// $atributos ['dobleLinea'] = 0;
						// $atributos ['tabIndex'] = $tab;
						// $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ['validar'] = 'required, minSize[1]';
						// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						// $atributos ['deshabilitado'] = false;
						// $atributos ['tamanno'] = 20;
						// $atributos ['maximoTamanno'] = '';
						// $atributos ['anchoEtiqueta'] = 220;
						// if (isset ( $_REQUEST [$esteCampo] )) {
						// $atributos ['valor'] = $_REQUEST [$esteCampo];
						// } else {
						// $atributos ['valor'] = '';
						// }
						// $tab ++;
						// // Aplica atributos globales al control
						// $atributos = array_merge ( $atributos, $atributosGlobales );
						// echo $this->miFormulario->campoTextArea ( $atributos );
						// unset ( $atributos );
						
						$esteCampo = "acta_adquisicion";
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["nombre"] = $esteCampo;
						$atributos ["tipo"] = "file";
						$atributos ["obligatorio"] = true;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ["tabIndex"] = $tab ++;
						$atributos ["columnas"] = 1;
						$atributos ["estilo"] = "textoIzquierda";
						$atributos ["anchoEtiqueta"] = 207;
						$atributos ["tamanno"] = 500000;
						$atributos ["validar"] = "required";
						$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ["valor"] = $valorCodificado;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				unset ( $atributos );
				
				$atributos ["id"] = "avances";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "AgrupacionSobrante";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Información Avances";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						// // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						// $esteCampo = 'observaciones_avance';
						// $atributos ['id'] = $esteCampo;
						// $atributos ['nombre'] = $esteCampo;
						// $atributos ['tipo'] = 'text';
						// $atributos ['estilo'] = 'jqueryui';
						// $atributos ['marco'] = true;
						// $atributos ['estiloMarco'] = '';
						// $atributos ["etiquetaObligatorio"] = true;
						// $atributos ['columnas'] = 105;
						// $atributos ['filas'] = 5;
						// $atributos ['dobleLinea'] = 0;
						// $atributos ['tabIndex'] = $tab;
						// $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ['validar'] = 'required, minSize[1]';
						// $atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						// $atributos ['deshabilitado'] = false;
						// $atributos ['tamanno'] = 20;
						// $atributos ['maximoTamanno'] = '';
						// $atributos ['anchoEtiqueta'] = 220;
						// if (isset ( $_REQUEST [$esteCampo] )) {
						// $atributos ['valor'] = $_REQUEST [$esteCampo];
						// } else {
						// $atributos ['valor'] = '';
						// }
						// $tab ++;
						// // Aplica atributos globales al control
						// $atributos = array_merge ( $atributos, $atributosGlobales );
						// echo $this->miFormulario->campoTextArea ( $atributos );
						// unset ( $atributos );
						
						$esteCampo = "acta_avance";
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["nombre"] = $esteCampo;
						$atributos ["tipo"] = "file";
						$atributos ["obligatorio"] = true;
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ["tabIndex"] = $tab ++;
						$atributos ["columnas"] = 1;
						$atributos ["estilo"] = "textoIzquierda";
						$atributos ["anchoEtiqueta"] = 207;
						$atributos ["tamanno"] = 500000;
						$atributos ["validar"] = "required";
						$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ["valor"] = $valorCodificado;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				echo $this->miFormulario->division ( "fin" );
				unset ( $atributos );
				
				$atributos ["id"] = "tipo_cotr";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					$esteCampo = "tipo_contrato";
					$atributos ['nombre'] = $esteCampo;
					$atributos ['id'] = $esteCampo;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['tab'] = $tab ++;
					$atributos ['seleccion'] = - 1;
					$atributos ['anchoEtiqueta'] = 218;
					$atributos ['evento'] = '';
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['deshabilitado'] = false;
					$atributos ['columnas'] = "1";
					$atributos ['tamanno'] = 1;
					$atributos ['ajax_function'] = "";
					$atributos ['ajax_control'] = $esteCampo;
					$atributos ['estilo'] = "jqueryui";
					$atributos ['validar'] = "required";
					$atributos ['limitar'] = true;
					$atributos ['anchoCaja'] = 50;
					$atributos ['miEvento'] = '';
					$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipo_contrato" );
					$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
					$atributos ['matrizItems'] = $matrizItems;
					// $atributos['miniRegistro']=;
					$atributos ['baseDatos'] = "inventarios";
					// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroLista ( $atributos );
					unset ( $atributos );
				}
				
				echo $this->miFormulario->division ( "fin" );
				
				$atributos ["id"] = "inf_contr";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = 'numero_contrato';
					$atributos ['id'] = $esteCampo;
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = 'text';
					$atributos ['estilo'] = 'jqueryui';
					$atributos ['marco'] = true;
					$atributos ['estiloMarco'] = '';
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['columnas'] = 2;
					$atributos ['dobleLinea'] = 0;
					$atributos ['tabIndex'] = $tab;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[onlyNumberSp]';
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
					$atributos ['deshabilitado'] = false;
					$atributos ['tamanno'] = 15;
					$atributos ['maximoTamanno'] = '';
					$atributos ['anchoEtiqueta'] = 220;
					$tab ++;
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTexto ( $atributos );
					unset ( $atributos );
					
					// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					$esteCampo = 'fecha_contrato';
					$atributos ['id'] = $esteCampo;
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = 'text';
					$atributos ['estilo'] = 'jqueryui';
					$atributos ['marco'] = true;
					$atributos ['estiloMarco'] = '';
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['columnas'] = 2;
					$atributos ['dobleLinea'] = 0;
					$atributos ['tabIndex'] = $tab;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ['validar'] = 'required,custom[date]';
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
					$atributos ['deshabilitado'] = false;
					$atributos ['tamanno'] = 8;
					$atributos ['maximoTamanno'] = '';
					$atributos ['anchoEtiqueta'] = 220;
					$tab ++;
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTexto ( $atributos );
				}
				echo $this->miFormulario->division ( "fin" );
				unset ( $atributos );
				
				$atributos ["id"] = "inf_provee";
				$atributos ["estiloEnLinea"] = "display:none";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "proveedor";
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
					$atributos ['validar'] = 'required';
					$atributos ['textoFondo'] = 'Ingrese Mínimo 3 Caracteres de Búsqueda';
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
					$atributos ['deshabilitado'] = false;
					$atributos ['tamanno'] = 60;
					$atributos ['maximoTamanno'] = '';
					$atributos ['anchoEtiqueta'] = 220;
					$tab ++;
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTexto ( $atributos );
					unset ( $atributos );
					
					$esteCampo = 'id_proveedor';
					$atributos ["id"] = $esteCampo; // No cambiar este nombre
					$atributos ["tipo"] = "hidden";
					$atributos ['estilo'] = '';
					$atributos ["obligatorio"] = false;
					$atributos ['marco'] = true;
					$atributos ["etiqueta"] = "";
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTexto ( $atributos );
					unset ( $atributos );
					
					$esteCampo = 'numero_factura';
					$atributos ['id'] = $esteCampo;
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = 'text';
					$atributos ['estilo'] = 'jqueryui';
					$atributos ['marco'] = true;
					$atributos ['estiloMarco'] = '';
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['columnas'] = 2;
					$atributos ['dobleLinea'] = 0;
					$atributos ['tabIndex'] = $tab;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ['validar'] = 'required, minSize[1],maxSize[50]';
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
					$atributos ['deshabilitado'] = false;
					$atributos ['tamanno'] = 15;
					$atributos ['maximoTamanno'] = '';
					$atributos ['anchoEtiqueta'] = 220;
					$tab ++;
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTexto ( $atributos );
					unset ( $atributos );
					
					// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					$esteCampo = 'fecha_factura';
					$atributos ['id'] = $esteCampo;
					$atributos ['nombre'] = $esteCampo;
					$atributos ['tipo'] = 'text';
					$atributos ['estilo'] = 'jqueryui';
					$atributos ['marco'] = true;
					$atributos ['estiloMarco'] = '';
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ['columnas'] = 2;
					$atributos ['dobleLinea'] = 0;
					$atributos ['tabIndex'] = $tab;
					$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
					$atributos ['validar'] = 'required,custom[date]';
					
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['valor'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['valor'] = '';
					}
					$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
					$atributos ['deshabilitado'] = false;
					$atributos ['tamanno'] = 8;
					$atributos ['maximoTamanno'] = '';
					$atributos ['anchoEtiqueta'] = 220;
					$tab ++;
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTexto ( $atributos );
				}
				echo $this->miFormulario->division ( "fin" );
				unset ( $atributos );
				
				$atributos ["id"] = "cuadro_ordenador";
				$atributos ["estiloEnLinea"] = "display:block";
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
					
					$esteCampo = "ordenadorGasto";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = $this->lenguaje->getCadena ( $esteCampo );
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'asignacionOrdenador';
						$atributos ['nombre'] = $esteCampo;
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['tab'] = $tab ++;
						$atributos ['anchoEtiqueta'] = 180;
						$atributos ['evento'] = '';
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['seleccion'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['seleccion'] = - 1;
						}
						$atributos ['deshabilitado'] = false;
						$atributos ['columnas'] = 2;
						$atributos ['tamanno'] = 1;
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['estilo'] = "jqueryui";
						$atributos ['validar'] = "required";
						$atributos ['limitar'] = true;
						$atributos ['anchoCaja'] = 25;
						$atributos ['miEvento'] = '';
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "tipoComprador" );
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
						
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'nombreOrdenador';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['tipo'] = 'text';
						$atributos ['estilo'] = 'jqueryui';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = false;
						$atributos ['columnas'] = 2;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required, minSize[1],maxSize[2000]';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 25;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 190;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'id_ordenador';
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["tipo"] = "hidden";
						$atributos ['estilo'] = '';
						$atributos ["obligatorio"] = false;
						$atributos ['marco'] = true;
						$atributos ["etiqueta"] = "";
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'tipo_ordenador';
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["tipo"] = "hidden";
						$atributos ['estilo'] = '';
						$atributos ["obligatorio"] = false;
						$atributos ['marco'] = true;
						$atributos ["etiqueta"] = "";
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						$esteCampo = 'identificacion_ordenador';
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["tipo"] = "hidden";
						$atributos ['estilo'] = '';
						$atributos ["obligatorio"] = false;
						$atributos ['marco'] = true;
						$atributos ["etiqueta"] = "";
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
					}
					
					echo $this->miFormulario->agrupacion ( 'fin' );
					unset ( $atributos );
				}
				echo $this->miFormulario->division ( "fin" );
				unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'sede';
				$atributos ['columnas'] = 2;
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['evento'] = '';
				$atributos ['deshabilitado'] = false;
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab;
				$atributos ['tamanno'] = 1;
				$atributos ['estilo'] = 'jqueryui';
				$atributos ['validar'] = 'required';
				$atributos ['limitar'] = false;
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
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'dependencia';
				$atributos ['columnas'] = 2;
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
				$atributos ['anchoEtiqueta'] = 200;
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['seleccion'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['seleccion'] = - 1;
				}
				$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "dependencias" ,$_REQUEST['sede'] );
				$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
				$atributos ['matrizItems'] = $matrizItems;
				
				// Utilizar lo siguiente cuando no se pase un arreglo:
				// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
				// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
				$tab ++;
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'supervisor';
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = true;
				$atributos ['tab'] = $tab ++;
				$atributos ['anchoEtiqueta'] = 150;
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
				
				// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
				$esteCampo = 'observaciones_entrada';
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
				$atributos ['validar'] = '';
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
			$valorCodificado .= "&opcion=registrar";
			$valorCodificado .= "&numero_acta=" . $_REQUEST ['numero_acta'];
			$valorCodificado .= "&usuario=".$_REQUEST['usuario']; 
			
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

