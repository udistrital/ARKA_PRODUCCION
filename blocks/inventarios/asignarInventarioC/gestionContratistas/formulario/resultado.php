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
		$miPaginaActual = $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/index.php?";
		$directorio .= $this->miConfigurador->getVariableConfiguracion ( "enlace" );
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "host" );
		$rutaBloque .= $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/";
		$rutaBloque .= $esteBloque ['grupo'] . $esteBloque ['nombre'];
		
		// ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
		/**
		 * Atributos que deben ser aplicados a todos los controles de este formulario.
		 * Se utiliza un arreglo
		 * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
		 *
		 * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
		 * $atributos= array_merge($atributos,$atributosGlobales);
		 */
		
		$rutaBloque = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" ) . "/blocks/inventarios/gestionElementos/";
		$rutaBloque .= $esteBloque ['nombre'];
		$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/blocks/inventarios/asignarInventarioC/" . $esteBloque ['nombre'] . "/plantilla/archivo_contratista.xlsx";
		
		$atributosGlobales ['campoSeguro'] = 'true';
		
		// -------------------------------------------------------------------------------------------------
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		if (isset ( $_REQUEST ['vigencia'] ) == true) {
			
			$vigencia = $_REQUEST ['vigencia'];
		} else {
			
			$vigencia = date ( 'Y' );
		}
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'Consultar_Contratistas', $vigencia );
		
		$resultado = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
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
		$atributos ['marco'] = true;
		$tab = 1;
		// ---------------- FIN SECCION: de Parámetros Generales del Formulario ----------------------------
		
		// ----------------INICIAR EL FORMULARIO ------------------------------------------------------------
		$atributos ['tipoEtiqueta'] = 'inicio';
		echo $this->miFormulario->formulario ( $atributos );
		// ---------------- SECCION: Controles del Formulario -----------------------------------------------
		
		if (isset ( $_REQUEST ['mensaje'] )) {
			
			switch ($_REQUEST ['mensaje']) {
				case 'registro' :
					$atributos ['mensaje'] = "<center>SE REGISTRO N#" . $_REQUEST ['num_exito'] . " CON EXITO LOS CONTRATISTAS VALIDOS </center>";
					$atributos ["estilo"] = 'success';
					
					break;
				
				case 'error' :
					$atributos ['mensaje'] = "<center>Error al Procesar Archivo Verifique Los Datos.</center>";
					$atributos ["estilo"] = 'error';
					
					break;
				
				case 'actualizo' :
					$atributos ['mensaje'] = "<center>SE ACTUALIZO CONTRATO CON EXITO</center>";
					$atributos ["estilo"] = 'success';
					
					break;
				
				case 'noinsertoRegistro' :
					$atributos ['mensaje'] = "<center>ERROR AL REGISTRAR CONTRATISTAS<BR>Verifique los Datos</center>";
					$atributos ["estilo"] = 'error';
					break;
				
				case 'datosVacios' :
					$atributos ['mensaje'] = "<center>ERROR EXISTEN CELDAS VACIAS EN EL ARCHIVO<BR>Todos los Campos son Obligatorios.Verifique los Datos</center>";
					$atributos ["estilo"] = 'error';
					break;
				
				case 'noExtension' :
					$atributos ['mensaje'] = "<center>ERROR EN LA EXTENSION DEL ACRHIVO A CARGAR<br>Verifique los Datos que se xls o xlsx.</center>";
					$atributos ["estilo"] = 'error';
					break;
				
				case 'errorTipoContrato' :
					$atributos ['mensaje'] = "<center>ERROR AL ACTUALIZAR EL TIPO DE CONTRATO.<BR>YA EXISTE UN CONTRATO VIGENTE DE ORDEN DE PRESTACIÓN DE SERVICIOS (OPS) CON EL CONTRATISTA.</center>";
					$atributos ["estilo"] = 'error';
					break;
				
				case 'errorActualizacion' :
					$atributos ['mensaje'] = "<center>ERROR AL ACTUALIZAR EL CONTRATO.<BR>VERIFIQUE LOS DATOS</center>";
					$atributos ["estilo"] = 'error';
					break;
			}
			
			if ($resultado != false) {
				
				if (isset ( $_REQUEST ['log_error'] ) == true && $_REQUEST ['log_error'] != false) {
					
					$Log_errores = unserialize ( $_REQUEST ['log_error'] );
					
					// var_dump($Log_errores);exit;
					
					foreach ( $Log_errores as $valor => $key ) {
						
						$atributos ['mensaje'] .= "<br>" . $valor . " en la(s) siguiente(s) fila(s):<br> ";
						$atributos ['mensaje'] .= "";
						
						$filas = implode ( ",", $key );
						$atributos ['mensaje'] .= $filas;
						
						$atributos ['mensaje'] .= "<br>";
					}
				}
				
				// -------------Control texto-----------------------
				$esteCampo = 'divMensaje';
				$atributos ['id'] = $esteCampo;
				$atributos ["tamanno"] = '';
				$atributos ["etiqueta"] = '';
				$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
				echo $this->miFormulario->campoMensaje ( $atributos );
				unset ( $atributos );
			}
		}
		
		$variable = "pagina=" . $miPaginaActual;
		$variable .= "&usuario=" . $_REQUEST ['usuario'];
		$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
		
		$esteCampo = "marcoDatosBasicos";
		$atributos ['id'] = $esteCampo;
		$atributos ["estilo"] = "jqueryui";
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ["leyenda"] = "Gestión Contratistas";
		echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
		{
			
			echo "<button id=\"mostrar\">Registrar Contratistas</button>";
			
			if ($resultado == false) {
				
				$atributos ['mensaje'] = "<center>No Existen Contratistas Registrados<br><br>Seleccione la Opcion Registrar Contratistas</center>";
				if (isset ( $_REQUEST ['mensaje'] ) == true) {
					switch ($_REQUEST ['mensaje']) {
						
						case 'noExtension' :
							$atributos ['mensaje'] = "<center>No Existen Contratistas Registrados<br><br>Seleccione la Opcion Registrar Contratistas</center><br>";
							$atributos ['mensaje'] .= "<center>ERROR EN LA EXTENSION DEL ACRHIVO A CARGAR<br>Verifique los Datos que se xls o xlsx.</center>";
							
							break;
						
						case 'noinsertoRegistro' :
							$atributos ['mensaje'] = "<center>No Existen Contratistas Registrados<br><br>Seleccione la Opcion Registrar Contratistas</center><br>";
							$atributos ['mensaje'] .= "<center>ERROR AL REGISTRAR CONTRATISTAS<BR>Verifique los Datos</center>";
							
							break;
						
						case 'error' :
							$atributos ['mensaje'] = "<center>No Existen Contratistas Registrados<br><br>Seleccione la Opcion Registrar Contratistas</center><br>";
							$atributos ['mensaje'] .= "<center>ERROR EXISTEN CELDAS VACIAS EN EL ARCHIVO<BR>Todos los Campos son Obligatorios.Verifique los Datos</center>";
							
							break;
					}
				}
				// -------------Control texto-----------------------
				$esteCampo = 'divMensajeNoDatos';
				$atributos ['id'] = $esteCampo;
				$atributos ["tamanno"] = '';
				$atributos ["etiqueta"] = '';
				$atributos ["columnas"] = ''; // El control ocupa 47% del tamaño del formulario
				
				$atributos ["estilo"] = 'warning';
				echo $this->miFormulario->campoMensaje ( $atributos );
			}
			
			// ------------------Division para los botones-------------------------
			$atributos ["id"] = "VentanaA";
			$atributos ["estilo"] = " ";
			$atributos ["estiloEnLinea"] = "display:none";
			echo $this->miFormulario->division ( "inicio", $atributos );
			{
				
				$esteCampo = "AgrupacionInformacion";
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Cargue Masivo de Contratistas";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				{
					
					$mensaje = "- El Archivo Tiene que Ser Tipo Excel.
								<br>- Solo Se Cargaran de forma Correcta de Acuerdo al Plantilla Preedeterminada.
								<br>- Para Verificar El Cargue Masivo Consulte los Constratistas.
								<br>- Enlace de Archivo Plantilla : <A id='salida'  target=\"_blank\"HREF=" . $host . "  > Archivo Plantilla</A>";
					
					// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					$esteCampo = 'mensajeRegistro';
					$atributos ['id'] = $esteCampo;
					$atributos ['tipo'] = 'warning';
					$atributos ['estilo'] = 'textoCentrar';
					$atributos ['mensaje'] = $mensaje;
					
					$tab ++;
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->cuadroMensaje ( $atributos );
					unset ( $atributos );
					
					$esteCampo = "documentos_elementos";
					$atributos ["id"] = $esteCampo; // No cambiar este nombre
					$atributos ["nombre"] = $esteCampo;
					$atributos ["tipo"] = "file";
					$atributos ["obligatorio"] = true;
					$atributos ["etiquetaObligatorio"] = true;
					$atributos ["tabIndex"] = $tab ++;
					$atributos ["columnas"] = 2;
					$atributos ["estilo"] = "textoIzquierda";
					$atributos ["anchoEtiqueta"] = 190;
					$atributos ["tamanno"] = 500000;
					$atributos ["validar"] = "required";
					$atributos ["etiqueta"] = $this->lenguaje->getCadena ( $esteCampo );
					// $atributos ["valor"] = $valorCodificado;
					$atributos = array_merge ( $atributos, $atributosGlobales );
					echo $this->miFormulario->campoCuadroTexto ( $atributos );
					unset ( $atributos );
					
					echo "<br>";
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
					$atributos ['submit'] = 'true';
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
					
					echo $this->miFormulario->division ( "fin" );
					
					// -----------------FIN CONTROL: Botón -----------------------------------------------------------
				}
				echo $this->miFormulario->agrupacion ( 'fin' );
			}
			echo $this->miFormulario->division ( "fin" );
			// ---------------------------------------------------------
			
			// ------------------Fin Division para los botones-------------------------
			
			if ($resultado != false) {
				
				$esteCampo = "AgrupacionInformacion";
				$atributos ['id'] = $esteCampo;
				$atributos ['leyenda'] = "Información Referente a Contratistas";
				echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
				
				// ------------------Division para los botones-------------------------
				$atributos ["id"] = "botonesConsulta";
				$atributos ["estilo"] = " ";
				echo $this->miFormulario->division ( "inicio", $atributos );
				
				$esteCampo = "vigencia";
				$atributos ['nombre'] = $esteCampo;
				$atributos ['id'] = $esteCampo;
				$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
				$atributos ["etiquetaObligatorio"] = false;
				$atributos ['tab'] = $tab ++;
				$atributos ['anchoEtiqueta'] = 100;
				$atributos ['evento'] = '';
				if (isset ( $_REQUEST [$esteCampo] )) {
					$atributos ['seleccion'] = $_REQUEST [$esteCampo];
				} else {
					$atributos ['seleccion'] = '2015';
				}
				$atributos ['deshabilitado'] = false;
				$atributos ['columnas'] = 1;
				$atributos ['tamanno'] = 1;
				$atributos ['ajax_function'] = "";
				$atributos ['ajax_control'] = $esteCampo;
				$atributos ['estilo'] = "jqueryui";
				$atributos ['validar'] = "required";
				$atributos ['limitar'] = 1;
				$atributos ['anchoCaja'] = 27;
				$atributos ['miEvento'] = '';
				$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultarvigencias" );
				$matrizItems = array (
						array (
								0,
								'' 
						) 
				);
				$matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" );
				
				$atributos ['matrizItems'] = $matrizItems;
				// $atributos['miniRegistro']=;
				$atributos ['baseDatos'] = "sicapital";
				// $atributos ['baseDatos'] = "inventarios";
				
				// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
				
				// Aplica atributos globales al control
				
				echo $this->miFormulario->campoCuadroLista ( $atributos );
				unset ( $atributos );
				echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
				// -----------------CONTROL: Botón ----------------------------------------------------------------
				$esteCampo = 'botonConsultar';
				$atributos ["id"] = $esteCampo;
				$atributos ["tabIndex"] = $tab;
				$atributos ["tipo"] = 'boton';
				// submit: no se coloca si se desea un tipo button genérico
				$atributos ['submit'] = 'true';
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
				
				echo $this->miFormulario->division ( 'fin' );
				
				echo "<table id='tablaTitulos'>
					<thead>
		                <tr>
		                   <th>Vigencia</th>
		                    <th>Tipo de Contrato</th>
							<th>Número de Contrato</th>
		                    <th>Identificación<br>Contratista</th>
							<th>Nombre y Apellidos<br>Contratistas</th>
							<th>Fecha de Inicio<br>Contrato</th>
					        <th>Fecha de Final<br>Contrato</th>
							<th>Modificar</th>
		                </tr>
		            </thead>
            	</table>";
				
				echo $this->miFormulario->agrupacion ( 'fin' );
			}
		}
		echo $this->miFormulario->marcoAgrupacion ( 'fin' );
		
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
		
		$valorCodificado = "action=" . $esteBloque ["nombre"];
		$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
		$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
		$valorCodificado .= "&opcion=registrar";
		$valorCodificado .= "&usuario=" . $_REQUEST ['usuario'];
		
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
	}
}

$miSeleccionador = new registrarForm ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->miForm ();
?>
