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
		
		
		// Limpia Items Tabla temporal
		$cadenaSql = $this->miSql->getCadenaSql ( 'limpiar_tabla_items' );
		$resultado_secuencia = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarItems', $_REQUEST ['numero_acta'] );
		// echo $cadenaSql;
		$items = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// var_dump($items);;exit;
		
		$seccion ['tiempo'] = $tiempo;
		
		foreach ( $items as $valor => $key ) {
			$key = array_merge ( $key, $seccion );
			$cadenaSql = $this->miSql->getCadenaSql ( 'insertarItemTemporal', $key );
			
			$insertados = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
		}
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarActaM', $_REQUEST ['numero_acta'] );
		
		$Acta = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$Acta = $Acta [0];
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacion_ordenador', $Acta ['ordenador_gasto'] );
		$ordenador = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$ordenadorGasto = array (
				"asignacionOrdenador" => $ordenador [0] [1],
				"nombreOrdenador" => $ordenador [0] [0],
				"id_ordenador" => $ordenador [0] [1] 
		);
		
		$arreglo = array (
				'tipoBien' => $Acta ['tipo_bien'],
				'nitproveedor' => $Acta ['nom_razon'],
				'id_proveedor' => $Acta ['proveedor'] 
		);
		
		$_REQUEST = array_merge ( $_REQUEST, $Acta, $arreglo, $ordenadorGasto );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'tipoBien' );
		$tipoBien = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql1 = $this->miSql->getCadenaSql ( 'tipoComprador' );
		$tipoComprador = $esteRecursoDB->ejecutarAcceso ( $cadenaSql1, "busqueda" );
		
		// ___________________________________________________________________________________
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
			
			$esteCampo = "marcoDatosBasicos";
			$atributos ['id'] = $esteCampo;
			$atributos ["estilo"] = "jqueryui";
			$atributos ['tipoEtiqueta'] = 'inicio';
			$atributos ["leyenda"] = "Modificar Acta Recibido No." . $_REQUEST ['numero_acta'];
			echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
			unset ( $atributos );
			{
				$enlace = '';
				if ($Acta ['id_contrato'] != 0) {
					
					$mostrarContrato = 'block';
					
					$cadenaSql = $this->miSql->getCadenaSql ( 'consultarInfoContrato', $Acta ['id_contrato'] );
					$InfoContrato = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					
					$InfoContrato = $InfoContrato [0];
					
					$arreglo = array (
							'numeroContrato' => $Acta ['id_contrato'] 
					);
					
					$enlace = $InfoContrato ['documento_ruta'];
					
					$_REQUEST = array_merge ( $_REQUEST, $arreglo );
				} else {
					
					$mostrarContrato = 'none';
				}
				
				$atributos ["id"] = "contratoDiv";
				$atributos ["estiloEnLinea"] = "display:" . $mostrarContrato;
				$atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				
				$esteCampo = "AgrupacionContrato";
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Datos Contrato";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				{
					
					{
						
						if (isset ( $_REQUEST ['datos'] )) {
							
							$datosArreglo = unserialize ( $_REQUEST ['datos'] );
						} else {
							
							$datosArreglo = array (
									'',
									'' 
							);
						}
						
						$esteCampo = "numeroContrato";
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
						$atributos ['columnas'] = 2;
						$atributos ['tamanno'] = 1;
						$atributos ['ajax_function'] = "";
						$atributos ['ajax_control'] = $esteCampo;
						$atributos ['estilo'] = "jqueryui";
						$atributos ['validar'] = " ";
						$atributos ['limitar'] = true;
						$atributos ['anchoCaja'] = 35;
						$atributos ['miEvento'] = '';
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultarContratos", $datosArreglo );
						$matrizItems = array (
								array (
										0,
										' ' 
								) 
						);
						$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
						$atributos ['matrizItems'] = $matrizItems;
						// $atributos['miniRegistro']=;
						$atributos ['baseDatos'] = "sicapital";
						// $atributos ['baseDatos'] = "inventarios";
						
						// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroLista ( $atributos );
						unset ( $atributos );
						
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'documentoContrato';
						$atributos ['id'] = $esteCampo;
						$atributos ['enlace'] = $enlace;
						$atributos ['columnas'] = 2;
						$atributos ['tabIndex'] = $tab ++;
						$atributos ['estilo'] = 'textoSubrayado';
						$atributos ['enlaceTexto'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['ancho'] = '10%';
						$atributos ['alto'] = '10%';
						$atributos ['redirLugar'] = true;
						echo $this->miFormulario->enlace ( $atributos );
						
						unset ( $atributos );
					}
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				
				echo $this->miFormulario->division ( "fin" );
				unset ( $atributos );
				
				$esteCampo = "AgrupacionActa";
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Datos Iniciales";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				{
					
					// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					$esteCampo = 'sede';
					$atributos ['columnas'] = 1;
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
					$atributos ['anchoEtiqueta'] = 180;
					
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
					$atributos ['columnas'] = 1;
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
					$atributos ['anchoEtiqueta'] = 180;
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
					// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
					
					// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					$esteCampo = 'fecha_recibido';
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
					$atributos ['validar'] = 'required, minSize[1],maxSize[200]';
					
					if (isset ( $Acta [0] [$esteCampo] )) {
						$atributos ['valor'] = $Acta [0] [$esteCampo];
					} else {
						$atributos ['valor'] = date ( 'd/m/Y' );
					}
					$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
					$atributos ['deshabilitado'] = true;
					$atributos ['tamanno'] = 20;
					$atributos ['maximoTamanno'] = '';
					$atributos ['anchoEtiqueta'] = 180;
					$tab ++;
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTexto ( $atributos );
					unset ( $atributos );
					// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
					
					// /------------------CONTROL: Lista desplegable -----------------------------------------------------
					$esteCampo = 'tipoBien';
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
					$atributos ['anchoEtiqueta'] = 180;
					if (isset ( $_REQUEST [$esteCampo] )) {
						$atributos ['seleccion'] = $_REQUEST [$esteCampo];
					} else {
						$atributos ['seleccion'] = - 1;
					}
					
					$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( 'tipoBien' );
					$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
					$atributos ['matrizItems'] = $matrizItems;
					
					// Utilizar lo siguiente cuando no se pase un arreglo:
					// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
					// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
					$tab ++;
					$atributos = array_merge ( $atributos, $atributosGlobales );
					// echo $this->miFormulario->campoCuadroLista ( $atributos );
					unset ( $atributos );
					
					// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					$esteCampo = 'nitproveedor';
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
					$atributos ['tamanno'] = 80;
					$atributos ['maximoTamanno'] = '';
					$atributos ['anchoEtiqueta'] = 180;
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
					
					echo $this->miFormulario->agrupacion ( 'fin' );
					
					// ----------------------------------------------- Items ------------------------------------//
					
// 					$esteCampo = "descripcionFactura";
// 					$atributos ['id'] = $esteCampo;
// 					$atributos ['leyenda'] = $this->lenguaje->getCadena ( $esteCampo );
// 					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
// 					{
// 						?>
<!--  <center>
	<table id="tablaContenido">
		<tr>
			<td>&nbsp</td>
		</tr>
	</table>
	<div id="barraNavegacion"></div>
</center> -->
<?php
// 					}
// 					echo $this->miFormulario->agrupacion ( 'fin' );
// 					unset ( $atributos );
					
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
					}
					
					echo $this->miFormulario->agrupacion ( 'fin' );
					unset ( $atributos );
					
					// --------------- FIN CONTROL : Cuadro Lista --------------------------------------------------
					// ------------------------------ Nueva Agrupación -------------------------------------------------//
					$esteCampo = "AgrupacionActa";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Validación de la Información";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
						// /------------------CONTROL: Lista desplegable -----------------------------------------------------
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'fecha_revision';
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
						$atributos ['validar'] = 'required, minSize[1],maxSize[200]';
						
						if (isset ( $Acta [0] [$esteCampo] )) {
							$atributos ['valor'] = $Acta [0] [$esteCampo];
						} else {
							$atributos ['valor'] = date ( 'd/m/Y' );
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = true;
						$atributos ['tamanno'] = 20;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'revisor';
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
						$atributos ['tamanno'] = 20;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						// --------------- FIN CONTROL : Cuadro de Texto ---------------------------------------------------//
						$esteCampo = "documentoSoporte";
						$atributos ["id"] = $esteCampo; // No cambiar este nombre
						$atributos ["nombre"] = $esteCampo;
						$atributos ["tipo"] = "file";
						$atributos ["obligatorio"] = true;
						$atributos ["etiquetaObligatorio"] = false;
						$atributos ["tabIndex"] = $tab ++;
						$atributos ["columnas"] = 1;
						$atributos ["estilo"] = "textoIzquierda";
						$atributos ["anchoEtiqueta"] = 220;
						$atributos ["tamanno"] = 500000;
						$atributos ["validar"] = "";
						$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
						// $atributos ["valor"] = $valorCodificado;
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroTexto ( $atributos );
						unset ( $atributos );
						
						echo "<br>";
						
						// --------------- FIN CONTROL : Cuadro de Texto ---------------------------------------------------//
						
						$esteCampo = 'aprobacionCheck';
						$atributos ['id'] = $esteCampo;
						$atributos ['nombre'] = $esteCampo;
						$atributos ['estilo'] = 'campoCuadroSeleccionCorta';
						$atributos ['marco'] = true;
						$atributos ['estiloMarco'] = '';
						$atributos ["etiquetaObligatorio"] = true;
						$atributos ['columnas'] = 1;
						$atributos ['dobleLinea'] = 0;
						$atributos ['tabIndex'] = $tab;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ['validar'] = 'required';
						
						if (isset ( $_REQUEST [$esteCampo] )) {
							$atributos ['valor'] = $_REQUEST [$esteCampo];
						} else {
							$atributos ['valor'] = '';
						}
						$atributos ['titulo'] = $this->lenguaje->getCadena ( $esteCampo . 'Titulo' );
						$atributos ['deshabilitado'] = false;
						$atributos ['tamanno'] = 20;
						$atributos ['maximoTamanno'] = '';
						$atributos ['anchoEtiqueta'] = 220;
						$tab ++;
						
						// Aplica atributos globales al control
						$atributos = array_merge ( $atributos, $atributosGlobales );
						echo $this->miFormulario->campoCuadroSeleccion ( $atributos );
						unset ( $atributos );
						// ----------------------- FIN CONTROL CHECKBOX--------------------//
						// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
						$esteCampo = 'observacionesacta';
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
						$atributos ['validar'] = 'minSize[1],maxSize[2000]';
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
						
						// ----------------------- FIN CONTROL AREA TEXTOO---------------//
						
						echo $this->miFormulario->agrupacion ( 'fin' );
						
						// ---- FIN DE LA AGRUPACIÓN-----
					}
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
					// // -----------------CONTROL: Botón ----------------------------------------------------------------
					// $esteCampo = 'botonCancelar';
					// $atributos ["id"] = $esteCampo;
					// $atributos ["tabIndex"] = $tab;
					// $atributos ["tipo"] = 'boton';
					// // submit: no se coloca si se desea un tipo button genérico
					// $atributos ['submit'] = true;
					// $atributos ["estiloMarco"] = '';
					// $atributos ["estiloBoton"] = 'jqueryui';
					// // verificar: true para verificar el formulario antes de pasarlo al servidor.
					// $atributos ["verificar"] = '';
					// $atributos ["tipoSubmit"] = 'jquery'; // Dejar vacio para un submit normal, en este caso se ejecuta la función submit declarada en ready.js
					// $atributos ["valor"] = $this->lenguaje->getCadena ( $esteCampo );
					// $atributos ['nombreFormulario'] = $esteBloque ['nombre'];
					// $tab ++;
					// // Aplica atributos globales al control
					// $atributos = array_merge ( $atributos, $atributosGlobales );
					// echo $this->miFormulario->campoBoton ( $atributos );
					
					echo $this->miFormulario->marcoAgrupacion ( 'fin' );
					
					// ---------------- SECCION: División ----------------------------------------------------------
					$esteCampo = 'division1';
					$atributos ['id'] = $esteCampo;
					$atributos ['estilo'] = 'general';
					echo $this->miFormulario->division ( "inicio", $atributos );
					
					// ---------------- FIN SECCION: División ----------------------------------------------------------
					echo $this->miFormulario->division ( 'fin' );
					
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
				// Paso 1: crear el listado de variables
				
				$valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
				$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
				$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
				$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
				$valorCodificado .= "&opcion=modificarActa";
				$valorCodificado .= "&seccion=" . $tiempo;
				$valorCodificado .= "&id_acta=" . $_REQUEST ['numero_acta'];
				
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
}

$miSeleccionador = new registrarForm ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->miForm ();
?>
