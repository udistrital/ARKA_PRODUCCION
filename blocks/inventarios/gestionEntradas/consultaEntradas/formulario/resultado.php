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
		$atributosGlobales ['campoSeguro'] = 'true';
		
		// -------------------------------------------------------------------------------------------------
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		if (isset ( $_REQUEST ['fecha_inicio'] ) && $_REQUEST ['fecha_inicio'] != '') {
			$fechaInicio = $_REQUEST ['fecha_inicio'];
		} else {
			$fechaInicio = '';
		}
		
		if (isset ( $_REQUEST ['fecha_final'] ) && $_REQUEST ['fecha_final'] != '') {
			$fechaFinal = $_REQUEST ['fecha_final'];
		} else {
			$fechaFinal = '';
		}
		
		if (isset ( $_REQUEST ['numero_entrada'] ) && $_REQUEST ['numero_entrada'] != '') {
			$numeroEntrada = $_REQUEST ['numero_entrada'];
		} else {
			$numeroEntrada = '';
		}
		
		if (isset ( $_REQUEST ['estado'] ) && $_REQUEST ['estado'] != '') {
			$estado = $_REQUEST ['estado'];
		} else {
			$estado = '';
		}
		
		if (isset ( $_REQUEST ['id_proveedor'] ) && $_REQUEST ['id_proveedor'] != '') {
			$proveedor = $_REQUEST ['id_proveedor'];
		} else {
			$proveedor = '';
		}
		
		if (isset ( $_REQUEST ['estado_contable'] ) && $_REQUEST ['estado_contable'] != '') {
			$contable = $_REQUEST ['estado_contable'];
		} else {
			$contable = '';
		}
		
		$arreglo = array (
				$numeroEntrada,
				$fechaInicio,
				$fechaFinal,
				$estado,
				$proveedor,
				$contable 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarEntrada', $arreglo );
		
		$entrada = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
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
		
		$esteCampo = "marcoDatosBasicos";
		$atributos ['id'] = $esteCampo;
		$atributos ["estilo"] = "jqueryui";
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ["leyenda"] = "Consultar y Modificar Estado Entrada";
		echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );

		if ($entrada) {
			



			$esteCampo = "selecc_registros";
			$atributos ['nombre'] = $esteCampo;
			$atributos ['id'] = $esteCampo;
			$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
			$atributos ["etiquetaObligatorio"] = false;
			$atributos ['tab'] = $tab ++;
			$atributos ['seleccion'] = 0;
			$atributos ['anchoEtiqueta'] = 150;
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
			$atributos ['anchoCaja'] = 24;
			$atributos ['miEvento'] = '';
			$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "estado_entrada" );
			$matrizItems = array (
					array (
							'0',
							'Ningun Registro'
					),
						
					array (
							'1',
							'Todos Registros'
			
					)
			
					
			);
			$atributos ['matrizItems'] = $matrizItems;
			// $atributos['miniRegistro']=;
			$atributos ['baseDatos'] = "inventarios";
			// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->campoCuadroLista ( $atributos );
			unset ( $atributos );
			
						
			
			echo "<table id='tablaTitulos'>";
			
			echo "<thead>
                <tr>
                   <th># Número Entrada y/o<br>Vigencia</th>
                    <th>Fecha Registro </th>
                    <th>Clase Entrada</th>
					<th>Nit<br>Proveedor</th>
					<th>Razon Social<br>Proveedor</th>
					<th>Estado Entrada</th>
			        <th>Cambiar Estado</th>
                </tr>
            </thead>
            <tbody>";
			
			for($i = 0; $i < count ( $entrada ); $i ++) {
				if ($entrada [$i] [3] == 0) {
					
					$proveedor [0] [0] = 'NO APLICA';
					$proveedor [0] [1] = 'NO APLICA';
					
					$arreglo = array (
							0 => $entrada [$i] [4],
							'entradaVigencia' => $entrada [$i] [4],
							1 => $entrada [$i] [1],
							'fechaRegistro' => $entrada [$i] [1],
							2 => $entrada [$i] [2],
							'claseEntrada' => $entrada [$i] [2],
							3 => '',
							'nitProveedor' => '',
							4 => '',
							'razonSocial' => '',
							5 => $entrada [$i] [0],
							'numeroEntrada' => $entrada [$i] [0],
							6 => $entrada [$i] [5],
							'estado' => $entrada [$i] [0] 
					);
				} else {
					$cadenaSql = $this->miSql->getCadenaSql ( 'proveedor_informacion', $entrada [$i] [3] );
					$proveedor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
					
					$arreglo = array (
							0 => $entrada [$i] [4],
							'entradaVigencia' => $entrada [$i] [4],
							1 => $entrada [$i] [1],
							'fechaRegistro' => $entrada [$i] [1],
							2 => $entrada [$i] [2],
							'claseEntrada' => $entrada [$i] [2],
							3 => $proveedor [0] [0],
							'nitProveedor' => $proveedor [0] [0],
							4 => $proveedor [0] [1],
							'razonSocial' => $proveedor [0] [1],
							5 => $entrada [$i] [0],
							'numeroEntrada' => $entrada [$i] [0],
							6 => $entrada [$i] [5],
							'estado' => $entrada [$i] [0] 
					);
				}
				
				$arreglo = base64_encode ( serialize ( $arreglo ) );
				
				$mostrarHtml = "<tr>
                    <td><center>" . $entrada [$i] [4] . "</center></td>
                    <td><center>" . $entrada [$i] [1] . "</center></td>
                    <td><center>" . $entrada [$i] [2] . "</center></td>
                    <td><center>" . $proveedor [0] [0] . "</center></td>
                    <td><center>" . $proveedor [0] [1] . "</center></td>
                    <td><center>" . $entrada [$i] [5] . "</center></td>
        <td><center>";
				
				if ($_REQUEST ['estado_contable'] == 'f') {
					
					// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					$nombre = 'item_' . $i;
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
						$atributos ['valor'] = $arreglo;
					}
					
					$atributos ['deshabilitado'] = false;
					$tab ++;
					
					// Aplica atributos globales al control
					$atributos = array_merge ( $atributos, $atributosGlobales );
					$mostrarHtml .= $this->miFormulario->campoCuadroSeleccion ( $atributos );
				} else {
					
					$mostrarHtml .= 'Inhabilitado por<br>Cierre Constable ';
				}
				$mostrarHtml .= "</center></td>
                    </tr>";
				echo $mostrarHtml;
				unset ( $mostrarHtml );
				unset ( $atributos );
			}
			
			echo "</tbody>";
			
			echo "</table>";
			
			if ($_REQUEST ['estado_contable'] == 'f') {
				
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
				unset($atributos);
						// ------------------Fin Division para los botones-------------------------
				echo $this->miFormulario->division ( "fin" );
			}
			//
			// Fin de Conjunto de Controles
			echo $this->miFormulario->marcoAgrupacion ( "fin" );
			// $valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
			$valorCodificado = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
			$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
			$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
			$valorCodificado .= "&opcion=modificar";
			
			/*
			 * supervisor
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
		} else {
			
			$mensaje = "No Se Encontraron<br>Registros de Entradas";
			
			// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
			$esteCampo = 'mensajeRegistro';
			$atributos ['id'] = $esteCampo;
			$atributos ['tipo'] = 'error';
			$atributos ['estilo'] = 'textoCentrar';
			$atributos ['mensaje'] = $mensaje;
			
			$tab ++;
			
			// Aplica atributos globales al control
			$atributos = array_merge ( $atributos, $atributosGlobales );
			echo $this->miFormulario->cuadroMensaje ( $atributos );
			// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
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
		
		$valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
		$valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion ( 'pagina' );
		$valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
		$valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
		$valorCodificado .= "&opcion=regresar";
		$valorCodificado .= "&redireccionar=regresar";
		
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
