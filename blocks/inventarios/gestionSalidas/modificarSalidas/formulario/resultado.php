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
		
		
		if (isset ( $_REQUEST ['anio'] ) && $_REQUEST ['anio'] != '') {
			$anio = $_REQUEST ['anio'];
		} else {
			$anio = '';
		}
		
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
		
		if (isset ( $_REQUEST ['numero_salida'] ) && $_REQUEST ['numero_salida'] != '') {
			$numeroSalida = $_REQUEST ['numero_salida'];
		} else {
			$numeroSalida = '';
		}
		
		if (isset ( $_REQUEST ['funcionario'] ) && $_REQUEST ['funcionario'] != '') {
			$funcionario = $_REQUEST ['funcionario'];
		} else {
			$funcionario = '';
		}
		
		$arreglo = array (
				$anio,
				$numeroEntrada,
				$numeroSalida,
				$funcionario,
				$fechaInicio,
				$fechaFinal 
		);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarSalida', $arreglo );
		
		$salidas = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
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
		$variable .= "&usuario=".$_REQUEST['usuario'];
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
		$atributos ["leyenda"] = "Consultar y Modificar Salida";
		echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
		
		if ($salidas) {
			
			echo "<table id='tablaTitulos'>";
			
			echo "<thead>
                <tr>
                   <th>Año de Vigencia</th>
                    <th>Número de Salida y/o<br>Vigencia</th>
                    <th>Número de Entrada y/o<br>Vigencia</th>
					<th>Fecha Registro</th>
					<th>Identificación<br>Funcionario</th>
					<th>Nombre<br>Funcionario</th>
					<th>Modificar Salida</th>
                </tr>
            </thead>
            <tbody>";
			
			for($i = 0; $i < count ( $salidas ); $i ++) {
				$variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
				$variable .= "&opcion=modificar";
				$variable .= "&usuario=".$_REQUEST['usuario'];
				// $variable .= "&usuario=" . $miSesion->getSesionUsuarioId ();
				$variable .= "&numero_salida=" . $salidas [$i] ['id_salida'];
				$variable .= "&numero_entrada=" . $salidas [$i] ['id_entrada'];
				
				$arreglo = array (
						$salidas [$i] [0],
						$salidas [$i] [5],
						$salidas [$i] [6],
						$salidas[$i]['identificacion'],
						$salidas[$i] ['nombre_fun'] 
				);
				
				$arreglo = serialize ( $arreglo );
				$variable .= "&datosGenerales=" . $arreglo;
				
				$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
				
				$mostrarHtml = "<tr>
                    <td><center>" . $salidas [$i] ['vigencia'] . "</center></td>
                    <td><center>" . $salidas [$i] ['salidas'] . "</center></td>
                    <td><center>" . $salidas [$i] ['entradas'] . "</center></td>
                    <td><center>" . $salidas [$i] ['fecha_registro'] . "</center></td>
                    <td><center>" . $salidas[$i]['identificacion'] . "</center></td>
                    <td><center>" . $salidas[$i] ['nombre_fun'] . "</center></td>";
				
				if ($salidas [$i] [7] == 'f') {
					
					$mostrarHtml .= "<td><center>
			                    	<a href='" . $variable . "'>
			                            <img src='" . $rutaBloque . "/css/images/edit.png' width='15px'>
			                        </a>
                		  			</center> </td>";
				} else if ($salidas [$i] [7] == 't') {
					
					$mostrarHtml .= "<td><center>Inhabilitado por Cierre Contable</center> </td>";
				}
				$mostrarHtml .= "</tr>";
				
				echo $mostrarHtml;
				unset ( $mostrarHtml );
				unset ( $variable );
			}
			// foreach ($funcionariosErrores as $i){
			
			// $cadenaSql = $this->miSql->getCadenaSql ( 'actualizarFuncionarios',$i );
			
			// $funcionario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
			
			// echo $i."<br>";
			// }
			
			echo "</tbody>";
			
			echo "</table>";
			// // ------------------Division para los botones-------------------------
			// $atributos ["id"] = "botones";
			// $atributos ["estilo"] = "marcoBotones";
			// echo $this->miFormulario->division ( "inicio", $atributos );
			
			// // -----------------CONTROL: Botón ----------------------------------------------------------------
			// $esteCampo = 'botonReporte';
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
			// -----------------FIN CONTROL: Botón -----------------------------------------------------------
			
			// ---------------------------------------------------------
			
			// ------------------Fin Division para los botones-------------------------
			// echo $this->miFormulario->division ( "fin" );
			
			// Fin de Conjunto de Controles
			// echo $this->miFormulario->marcoAgrupacion("fin");
		} else {
			
			$mensaje = "No Se Encontraron<br>Registros de Salidas";
			
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
