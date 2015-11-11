<?php

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class registrarForm {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;

    function __construct($lenguaje, $formulario, $sql) {
        $this->miConfigurador = \Configurador::singleton();

        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');

        $this->lenguaje = $lenguaje;

        $this->miFormulario = $formulario;

        $this->miSql = $sql;
    }

    function miForm() {

// Rescatar los datos de este bloque
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
        $rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
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
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "sicapital";
        $esteRecursoDBO = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        if (isset($_REQUEST ['responsable']) && $_REQUEST ['responsable'] != '') {
            $funcionario = $_REQUEST ['responsable'];
        } else {
            $funcionario = '';
        }

        if (isset($_REQUEST ['placa']) && $_REQUEST ['placa'] != '') {
            $placa = $_REQUEST ['placa'];
        } else {
            $placa = '';
        }

        if (isset($_REQUEST ['serial']) && $_REQUEST ['serial'] != '') {
            $serial = $_REQUEST ['serial'];
        } else {
            $serial = '';
        }

        if (isset($_REQUEST ['dependencia']) && $_REQUEST ['dependencia'] != '') {
            $dependencia = $_REQUEST ['dependencia'];
        } else {
            $dependencia = '';
        }

        if (isset($_REQUEST ['sede']) && $_REQUEST ['sede'] != '') {
            $sede = $_REQUEST ['sede'];
        } else {
            $sede = '';
        }


        if (isset($_REQUEST ['numero_entrada']) && $_REQUEST ['numero_entrada'] != '') {
            $entrada = $_REQUEST ['numero_entrada'];
        } else {
            $entrada = '';
        }

        if (isset($_REQUEST ['fecha_inicio']) && $_REQUEST ['fecha_inicio'] != '') {
            $finicio = $_REQUEST ['fecha_inicio'];
        } else {
            $finicio = '';
        }

        if (isset($_REQUEST ['fecha_final']) && $_REQUEST ['fecha_final'] != '') {
            $ffinal = $_REQUEST ['fecha_final'];
        } else {
            $ffinal = '';
        }

        $arreglo = array(
            'funcionario' => $funcionario,
            'serie' => $serial,
            'placa' => $placa,
            'dependencia' => $dependencia,
            'fecha_inicio' => $finicio,
            'fecha_final' => $ffinal,
            'numero_entrada' => $entrada,
            'sede' => $sede
        );

         $cadenaSql = $this->miSql->getCadenaSql('consultarAprobadas', $arreglo);
         
                  
        $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
        

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
        echo $this->miFormulario->formulario($atributos);
// ---------------- SECCION: Controles del Formulario -----------------------------------------------

        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $variable = "pagina=" . $miPaginaActual;
        $variable .= "&usuario=".$_REQUEST['usuario']; 
        $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);

// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
        $esteCampo = 'botonRegresar';
        $atributos ['id'] = $esteCampo;
        $atributos ['enlace'] = $variable;
        $atributos ['tabIndex'] = 1;
        $atributos ['estilo'] = 'textoSubtitulo';
        $atributos ['enlaceTexto'] = $this->lenguaje->getCadena($esteCampo);
        $atributos ['ancho'] = '10%';
        $atributos ['alto'] = '10%';
        $atributos ['redirLugar'] = true;
        echo $this->miFormulario->enlace($atributos);

        unset($atributos);

        $esteCampo = "marcoDatosBasicos";
        $atributos ['id'] = $esteCampo;
        $atributos ["estilo"] = "jqueryui";
        $atributos ['tipoEtiqueta'] = 'inicio';
        $atributos ["leyenda"] = "Consultar Elementos";
        echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);

        $esteCampo = "AgrupacionInformacion";
        $atributos ['id'] = $esteCampo;
        $atributos ['leyenda'] = "Información Referente a Elementos para Reponer";
        echo $this->miFormulario->agrupacion('inicio', $atributos);
// 		var_dump($elemento);
        if ($elemento) {

            echo "<table id='tablaTitulos'>";

            echo "<thead>
                        <tr>
                        <th>Dependencia</th>
                            <th>Sede</th>
                            <th>Ubicacion</th>
                            <th>ID Funcionario</th>
			    <th>Nombre<br>Funcionario</th>
			    <th>Número Salida</th>	
                            <th>Número Entrada</th>	
			    <th># Número Placa</th>
                            <th> Descripción </th>
                            <th>Seleccionar</th>
                        </tr>
                        </thead>
                        <tbody>";

            for ($i = 0; $i < count($elemento); $i ++) {
                $variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                $variable .= "&opcion=registrarReposicion";
                $variable .= "&usuario=" . $_REQUEST['usuario'];
                $variable .= "&elemento=" . base64_encode(serialize($elemento [$i]));
                $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);



                $mostrarHtml = "<tr>
                                <td><center>" . $elemento [$i] ['dependencia'] . "</center></td>
                                <td><center>" . $elemento [$i] ['sede'] . "</center></td>
                                <td><center>" . $elemento [$i] ['ubicacion'] . "</center></td>
                                <td><center>" . $elemento [$i] ['funcionario'] . "</center></td>
                                <td><center>" . $elemento [$i] ['funcionario_nombre'] . "</center></td>
                                <td><center>" . $elemento [$i] ['consecutivosalida'] . "</center></td>
                                <td><center>" . $elemento [$i] ['consecutivoentrada'] . "</center></td>
                                <td><center>" . $elemento [$i] ['placa'] . "</center></td>
		  		<td><center>" . $elemento [$i] ['descripcion'] . "</center></td>
                                   <td><center>
                    	<a href='" . $variable . "'>
                            <img src='" . $rutaBloque . "/css/images/edit.png' width='15px'>
                        </a>
                  	</center> </td>
                    </tr>";
                echo $mostrarHtml;
                unset($mostrarHtml);
                unset($atributos);
            }

            echo "</tbody>";

            echo "</table>"; // ------------------Division para los botones-------------------------

            echo $this->miFormulario->marcoAgrupacion('fin');

            $atributos ['marco'] = true;
            $atributos ['tipoEtiqueta'] = 'fin';
            echo $this->miFormulario->formulario($atributos);
        } else {

            $mensaje = "No Se Encontraron<br>Elementos Asociados.";

// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
            $esteCampo = 'mensajeRegistro';
            $atributos ['id'] = $esteCampo;
            $atributos ['tipo'] = 'error';
            $atributos ['estilo'] = 'textoCentrar';
            $atributos ['mensaje'] = $mensaje;

            $tab ++;

// Aplica atributos globales al control
            $atributos = array_merge($atributos, $atributosGlobales);
            echo $this->miFormulario->cuadroMensaje($atributos);
// --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
        }

        echo $this->miFormulario->agrupacion('fin');

        echo $this->miFormulario->marcoAgrupacion('fin');

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
        $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
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
        $valorCodificado .= "&tiempo=" . time();
// Paso 2: codificar la cadena resultante
        $valorCodificado = $this->miConfigurador->fabricaConexiones->crypto->codificar($valorCodificado);

        $atributos ["id"] = "formSaraData"; // No cambiar este nombre
        $atributos ["tipo"] = "hidden";
        $atributos ['estilo'] = '';
        $atributos ["obligatorio"] = false;
        $atributos ['marco'] = true;
        $atributos ["etiqueta"] = "";
        $atributos ["valor"] = $valorCodificado;
        echo $this->miFormulario->campoCuadroTexto($atributos);
        unset($atributos);

        $atributos ['marco'] = true;
        $atributos ['tipoEtiqueta'] = 'fin';
        echo $this->miFormulario->formulario($atributos);
    }

}

$miSeleccionador = new registrarForm($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->miForm();
?>
