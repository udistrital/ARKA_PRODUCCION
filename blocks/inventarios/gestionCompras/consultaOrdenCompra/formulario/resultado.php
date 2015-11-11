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
	function documento($numero, $tab, $atributosGlobales) {
		$esteBloque = $this->miConfigurador->getVariableConfiguracion ( "esteBloque" );
		// -----------------CONTROL: Botón ----------------------------------------------------------------
		$esteCampo = 'botoncual';
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
		$atributos ["valor"] = "Documento Nro Orden ." . $numero;
		$atributos ['nombreFormulario'] = $esteBloque ['nombre'];
		$tab ++;
		
		// Aplica atributos globales al control
		$atributos = array_merge ( $atributos, $atributosGlobales );
		return $this->miFormulario->campoBoton ( $atributos );
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
		
		$conexion = "sicapital";
		$esteRecursoDBO = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'polizas' );
		$resultado_polizas = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$resultado_polizas = $resultado_polizas [0];
		
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
		
		if (isset ( $_REQUEST ['numero_orden'] ) && $_REQUEST ['numero_orden'] != '') {
			$numeroOrden = $_REQUEST ['numero_orden'];
		} else {
			$numeroOrden = '';
		}
		
		if (isset ( $_REQUEST ['selec_dependencia_Sol'] ) && $_REQUEST ['selec_dependencia_Sol'] != '') {
			$dependencia = $_REQUEST ['selec_dependencia_Sol'];
		} else {
			$dependencia = '';
		}
		
		if (isset ( $_REQUEST ['proveedor_consulta'] ) && $_REQUEST ['proveedor_consulta'] != '') {
			$proveedor = $_REQUEST ['proveedor_consulta'];
		} else {
			$proveedor = '';
		}
		
		$arreglo = array (
				$numeroOrden,
				$proveedor,
				$dependencia,
				$fechaInicio,
				$fechaFinal 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrden1', $arreglo );
		
		$ordenCompra = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrden2', $arreglo );
		// $ordenCompra2 = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// var_dump ( $ordenCompra2 );
		
		// if ($ordenCompra1 == false) {
		
		// $ordenCompra = $ordenCompra2;
		// } else if ($ordenCompra2 == false) {
		
		// $ordenCompra = $ordenCompra1;
		// } else {
		// $ordenCompra = array_merge ( $ordenCompra1, $ordenCompra2 );
		// }
		
		$arreglo = serialize ( $arreglo );
		
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
		echo $this->miFormulario->enlace ( $atributos );
		
		unset ( $atributos );
		
		$esteCampo = "marcoDatosBasicos";
		$atributos ['id'] = $esteCampo;
		$atributos ["estilo"] = "jqueryui";
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ["leyenda"] = "Consultar y Modificar Orden Compra";
		echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
		
		$atributos ["id"] = "div_ordenes";
		$atributos ["estiloEnLinea"] = "display:block";
		// $atributos = array_merge ( $atributos, $atributosGlobales );
		echo $this->miFormulario->division ( "inicio", $atributos );
		unset ( $atributos );
		{
			
			if ($ordenCompra) {
				
				echo "<table id='tablaTitulos'>";
				
				echo "<thead>
                <tr>
                   <th>Fecha Orden Compra</th>
                    <th>Número Orden Compra </th>
                    <th>Nit Proveedor</th>
					<th>Dependencia Solicitante</th>
			        <th>Modificar</th>
	           </tr>
            </thead>
            <tbody>";
				
				for($i = 0; $i < count ( $ordenCompra ); $i ++) {
					
					$variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
					$variable .= "&opcion=modificar";
					// $variable .= "&usuario=" . $miSesion->getSesionUsuarioId ();
					$variable .= "&numero_orden=" . $ordenCompra [$i] [0];
					$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
					
					$cadenaSql = $this->miSql->getCadenaSql ( 'nit_proveedor', $ordenCompra [$i] [2] );
					$nit = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
					$cadenaSql = $this->miSql->getCadenaSql ( 'dependecia_solicitante', $ordenCompra [$i] [3] );
					$dependencia = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
					
					$mostrarHtml = "<tr>
                    <td><center>" . $ordenCompra [$i] [1] . "</center></td>
                    <td><center>" . $ordenCompra [$i] [0] . "</center></td>
                    <td><center>" . $nit [0] [0] . "</center></td>
                    <td><center>" . $dependencia [0] [0] . "</center></td>
                    <td><center>
                    	<a href='" . $variable . "'>
                            <img src='" . $rutaBloque . "/css/images/edit.png' width='15px'>
                        </a>
                  	</center> </td>
                </tr>";
					echo $mostrarHtml;
					unset ( $mostrarHtml );
					unset ( $variable );
				}
				
				echo "</tbody>";
				
				echo "</table>";
				
				
				$atributos ["id"] = "div_documento";
				$atributos ["estiloEnLinea"] = "display:block";
				$atributos ["estiloMarco"] = '';
				// $atributos = array_merge ( $atributos, $atributosGlobales );
				echo $this->miFormulario->division ( "inicio", $atributos );
				unset ( $atributos );
				{
						
					$esteCampo = "AgrupacionGeneral";
					$atributos ['id'] = $esteCampo;
					$atributos ['leyenda'] = "Documento PDF";
					echo $this->miFormulario->agrupacion ( 'inicio', $atributos );
					{
				
						$esteCampo = "orden_compra_consulta";
						$atributos ['nombre'] = $esteCampo;
						$atributos ['id'] = $esteCampo;
						$atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo );
						$atributos ["etiquetaObligatorio"] = false;
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
						$atributos ['anchoCaja'] = 25;
						$atributos ['miEvento'] = '';
						$atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultar_numero_orden" );
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
						echo "<br><br><br>";
						// ------------------Division para los botones-------------------------
						$atributos ["id"] = "botones";
						$atributos ["estilo"] = "marcoBotones";
						echo $this->miFormulario->division ( "inicio", $atributos );
						unset ( $atributos );
						{
							// -----------------CONTROL: Botón ----------------------------------------------------------------
							$esteCampo = 'botonDocumento';
							$atributos ["id"] = $esteCampo;
							$atributos ["tabIndex"] = $tab;
							$atributos ["tipo"] = 'boton';
							$atributos ['columnas'] = 1;
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
							unset ( $atributos );
						}
				
						echo $this->miFormulario->division ( 'fin' );
					}
						
					echo $this->miFormulario->agrupacion ( 'fin' );
				}
				
				echo $this->miFormulario->division ( "fin" );
				unset ( $atributos );
				
				// Fin de Conjunto de Controles
				// echo $this->miFormulario->marcoAgrupacion("fin");
			} else {
				
				$mensaje = "No Se Encontraron<br>Ordenes de Compra";
				
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
		}
		
		echo $this->miFormulario->division ( "fin" );
		unset ( $atributos );
		
	
		
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
		$valorCodificado .= "&opcion=documento";
		// $valorCodificado .= "&numero_orden=".$_REQUEST['numero_orden'];
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
