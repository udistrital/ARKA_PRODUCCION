<?php
use inventarios\gestionActa\registrarActa\funcion\redireccion;

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
		$rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];
		
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
		
		// -------------------------------------------------------------------------------------------------
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$arregloDatos=explode("@",$_REQUEST['documentoContratista']);
		
		if (isset ( $_REQUEST ['documentoContratista'] ) && $_REQUEST ['documentoContratista'] != '') {
			$docContratista = $arregloDatos[0];
		} else {
			$docContratista = '';
		}
		
		
		$arregloDatos=explode("@",$_REQUEST['documentoContratista']);
		
		// COnsultar Elementos Activos del supervisor para asignarlos al contratista
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultarElementosSupervisor', $_REQUEST ['funcionario'] );
		// $elementos_supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		// Consultar Elementos Asignados al contratista
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarElementosContratista', array (
				"funcionario"=>$_REQUEST ['funcionario'],
				"contratista"=>$arregloDatos[0],
				"tipo_contrato"=>$arregloDatos[1],
				"numero_contrato"=>$arregloDatos[2],
				"vigencia"=>$arregloDatos[3],
				 
		) );
		$elementos_contratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql2 = $this->miSql->getCadenaSql ( 'nombreContratista', $docContratista );
		$nombreContratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql2, "busqueda" );
		
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
		
		$_REQUEST ['funcionario'] = $_REQUEST ['usuario'];
		$variable = "pagina=" . $miPaginaActual;
		$variable .= "&usuario=" . $_REQUEST ['usuario'];
		$variable .= "&funcionario=" . $_REQUEST ['funcionario'];
		
		if (isset ( $_REQUEST ['accesoCondor'] ) && $_REQUEST ['accesoCondor'] == 'true') {
			
			$variable .= "&accesoCondor=true";
		}
		
		$variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $variable, $directorio );
		
		// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
		$esteCampo = 'botonRegresar';
		$atributos ['id'] = $esteCampo;
		$atributos ['enlace'] = $variable;
		$atributos ['tabIndex'] = 1;
		$atributos ['estilo'] = 'textoSubtitulo';
		$atributos ['enlaceTexto'] = "<< Regresar";
		$atributos ['ancho'] = '10%';
		$atributos ['alto'] = '10%';
		$atributos ['redirLugar'] = true;
		echo $this->miFormulario->enlace ( $atributos );
		
		unset ( $atributos );
		
		$esteCampo = "marcoDatosBasicos";
		$atributos ['id'] = $esteCampo;
		$atributos ["estilo"] = "jqueryui";
		$atributos ['tipoEtiqueta'] = 'inicio';
		$atributos ["leyenda"] = "Modificar Préstamo de Elementos: " . $nombreContratista[0]['CON_IDENTIFICACION']. " - " . $nombreContratista [0] ['CON_NOMBRE'];
		echo $this->miFormulario->marcoAgrupacion ( 'inicio', $atributos );
		
		if ($elementos_contratista !== false) {
			
			echo "<table id='tablaTitulos'>";
			
			echo "<thead>
                <tr>
                <th>Placa</th>
				<th>Descripción</th>
                <th>Marca</th>
                <th>Serie</th>
				<th>Sede</th>
				<th>Dependencia</th>
				<th>Ubicación<br>Especifica</th>
                <th>Seleccionar</th>
                </tr>
            </thead>
            <tbody>";
			
			// if ($elementos_supervisor !== false) {
			// for($i = 0; $i < count ( $elementos_supervisor ); $i ++) {
			
			// $mostrarHtml = "<tr>
			// <td><center>" . $elementos_supervisor [$i] ['id_elemento_ind'] . "</center></td>
			// <td><center>" . $elementos_supervisor [$i] ['nivel'] . "</center></td>
			// <td><center>" . $elementos_supervisor [$i] ['marca'] . "</center></td>
			// <td><center>" . $elementos_supervisor [$i] ['placa'] . "</center></td>
			// <td><center>" . $elementos_supervisor [$i] ['serie'] . "</center></td>
			// <td><center>" . $elementos_supervisor [$i] ['valor'] . "</center></td>
			// <td><center>" . $elementos_supervisor [$i] ['subtotal_sin_iva'] . "</center></td>
			// <td><center>" . $elementos_supervisor [$i] ['total_iva'] . "</center></td>
			// <td><center>" . $elementos_supervisor [$i] ['total_iva_con'] . "</center></td>
			// <td><center>";
			// // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
			// $nombre = 'item_sup' . $i;
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
			// $atributos ['valor'] = $elementos_supervisor [$i] ['id_elemento_ind'];
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
			// }
			
			
			
			if ($elementos_contratista !== false) {
				
				for($i = 0; $i < count ( $elementos_contratista ); $i ++) {
					
					$mostrarHtml = "
                    <tr>
                    <td><center>" . $elementos_contratista [$i] ['placa'] . "</center></td>
                    <td><center>" . $elementos_contratista [$i] ['descripcion'] . "</center></td>
					<td><center>" . $elementos_contratista [$i] ['marca'] . "</center></td>
                    <td><center>" . $elementos_contratista [$i] ['serie'] . "</center></td>
                    <td><center>" . $elementos_contratista [$i] ['sede'] . "</center></td>
                    <td><center>" . $elementos_contratista [$i] ['dependencia'] . "</center></td>
                    <td><center>" . $elementos_contratista [$i] ['espacio_fisico'] . "</center></td>
                    <td><center>";
					// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
					$nombre = 'item_cont' . $i;
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
						$atributos ['valor'] = $elementos_contratista [$i] ['id_elemento_ind'];
					}
					$atributos ['seleccionado'] = true;
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
			
			echo $this->miFormulario->marcoAgrupacion ( 'fin' );
			
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
			
			
			// -----------------CONTROL: Botón ----------------------------------------------------------------
			$esteCampo = 'botonDocumentoSoporte';
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
			
			
			
			
			// -----------------FIN CONTROL: Botón -----------------------------------------------------------
			
			echo $this->miFormulario->division ( 'fin' );
			
			// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
			// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
			// Se debe declarar el mismo atributo de marco con que se inició el formulario.
			// -----------------FIN CONTROL: Botón -----------------------------------------------------------
			// ------------------Fin Division para los botones-------------------------
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
			$valorCodificado .= "&opcion=asignar";
			$valorCodificado .= "&contratista=" . $arregloDatos[0];
			$valorCodificado .= "&tipo_contrato=" .$arregloDatos[1];
			$valorCodificado .= "&numero_contrato=" . $arregloDatos[2];
			$valorCodificado .= "&vigencia=" . $arregloDatos[3];
			$valorCodificado .= "&funcionario=" . $_REQUEST ['funcionario'];
			$valorCodificado .= "&supervisor=" . $_REQUEST ['funcionario'];
			$valorCodificado .= "&usuario=" . $_REQUEST ['usuario'];
			if (isset ( $_REQUEST ['accesoCondor'] ) && $_REQUEST ['accesoCondor'] == 'true') {
				
				$valorCodificado .= "&accesoCondor=true";
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
		} else {
			
			$mensaje = "No Se Encontraron<br>Elementos Asignados para Modificar";
			
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
}

$miSeleccionador = new registrarForm ( $this->lenguaje, $this->miFormulario, $this->sql );

$miSeleccionador->miForm ();
?>
