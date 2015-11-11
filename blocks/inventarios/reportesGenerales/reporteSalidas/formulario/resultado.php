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

        $nombreReporte = "Resultado de la búsqueda";

        $datos_consulta = array(
            // Filtro 1
            'sede' => (isset($_REQUEST['sede']) ? $_REQUEST['sede'] : ''),
            'dependencia' => (isset($_REQUEST['dependencia']) ? $_REQUEST['dependencia'] : ''),
            'ubicacion' => (isset($_REQUEST['ubicacion']) ? $_REQUEST['ubicacion'] : ''),
            'funcionario' => (isset($_REQUEST['nombreFuncionario']) ? $_REQUEST['nombreFuncionario'] : ''),
            //Salida
            'numero_salida' => (isset($_REQUEST['numero_salida']) ? $_REQUEST['numero_salida'] : ''),
            'numero_entrada' => (isset($_REQUEST['numero_entrada']) ? $_REQUEST['numero_entrada'] : ''),
            'fecha_inicio' => (isset($_REQUEST['fecha_inicio']) ? $_REQUEST['fecha_inicio'] : ''),
            'fecha_final' => (isset($_REQUEST['fecha_final']) ? $_REQUEST['fecha_final'] : ''),
        );


        //Salidas
        $cadenaSql = $this->miSql->getCadenaSql('consultarSalida', $datos_consulta);
        $datos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        //Elementos a la entrada
        $nombreReporte = "Reporte de Salidas";

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
        $esteCampo = "marcoDatosBasicos";
        $atributos ['id'] = $esteCampo;
        $atributos ["estilo"] = "jqueryui";
        $atributos ['tipoEtiqueta'] = 'inicio';
        $atributos ["leyenda"] = "Resultado Consulta General - " . $nombreReporte;
        echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);

        if ($datos) {

            echo "<table id='tablaReporte'>";

            echo "<thead>
                <tr>
                   <th>Número Salida</th>
                    <th>Número Entrada</th>
                    <th>Fecha Registro</th>
		    <th>Sede</th>
	            <th>Dependencia</th>
                    <th>Ubicación</th>
                    <th>Funcionario</th>
                    <th>Núm. Elementos</th>
                    <th>Valor Salida</th>
                    <th>Generar PDF</th>
	        </tr>
            </thead>
            <tbody>";
            for ($i = 0; $i < count($datos); $i ++) {

                $variable_documento = "action=" . $esteBloque ["nombre"];
                $variable_documento .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                $variable_documento .= "&bloque=" . $esteBloque ['nombre'];
                $variable_documento .= "&bloqueGrupo=" . $esteBloque ["grupo"];
                $variable_documento .= "&opcion=generarPDF";
                $variable_documento .= "&usuario=" . $_REQUEST['usuario'];
                $variable_documento .= "&id_salida=" . $datos[$i] ['id_salida'];
                $variable_documento = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable_documento, $directorio);

                $mostrarHtml = "<tr>
                    <td><center>" . $datos [$i] ['num_salida'] . "</center></td>
                    <td><center>" . $datos [$i] ['num_entrada'] . "</center></td>
                    <td><center>" . $datos [$i] ['fecha_registro'] . "</center></td>
                    <td><center>" . $datos [$i] ['sede'] . "</center></td>
                    <td><center>" . $datos [$i] ['dependencia'] . "</center></td>
                    <td><center>" . $datos [$i] ['ubicacion'] . "</center></td>
                    <td><center>" . $datos [$i] ['nombre_funcionario'] . "</center></td>
                    <td><center>" . $datos [$i] ['numero_elementos'] . "</center></td>
                        <td><center>" . $datos [$i] ['valor_salida'] . "</center></td>
                        <td><center>
                    	<a href='" . $variable_documento . "'>
                            <img src='" . $rutaBloque . "/css/images/pdf.png' width='15px'>
                        </a>
                  	</center></td>
                       </tr>";
                echo $mostrarHtml;
                unset($mostrarHtml);
                //    unset($variable);
            }
            echo "</tbody>";
            echo "</table>";

            echo $this->miFormulario->division("fin");
            unset($atributos);
            ///   echo $this->miFormulario->tablaReporte($datos, false);
// Fin de Conjunto de Controles
// echo $this->miFormulario->marcoAgrupacion("fin");
        } else {
            $mensaje = "No Se Encontraron<br>Datos Relacionados con la Búsqueda";
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

        $valorCodificado = "action=" . $esteBloque ["nombre"];
        $valorCodificado.= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
        $valorCodificado.= "&bloque=" . $esteBloque ['nombre'];
        $valorCodificado.= "&bloqueGrupo=" . $esteBloque ["grupo"];
        $valorCodificado.= "&opcion=generarPDF";
        $valorCodificado.="&depreciacion=" . base64_encode(serialize($datos));


        /**
         * SARA permite que los nombres de los campos sean dinámicos.
         * Para ello utiliza la hora en que es creado el formulario para
         * codificar el nombre de cada campo. Si se utiliza esta técnica es necesario pasar dicho tiempo como una variable:
         * (a) invocando a la variable $_REQUEST ['tiempo'] que se ha declarado en ready.php o
         * (b) asociando el tiempo en que se está creando el formulario
         */
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
