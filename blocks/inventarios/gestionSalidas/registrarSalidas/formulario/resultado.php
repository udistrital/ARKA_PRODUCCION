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

        if (isset($_REQUEST ['fecha_inicio']) && $_REQUEST ['fecha_inicio'] != '') {
            $fechaInicio = $_REQUEST ['fecha_inicio'];
        } else {
            $fechaInicio = '';
        }

        if (isset($_REQUEST ['fecha_final']) && $_REQUEST ['fecha_final'] != '') {
            $fechaFinal = $_REQUEST ['fecha_final'];
        } else {
            $fechaFinal = '';
        }

        if (isset($_REQUEST ['numero_entrada']) && $_REQUEST ['numero_entrada'] != '') {
            $numeroEntrada = $_REQUEST ['numero_entrada'];
        } else {
            $numeroEntrada = '';
        }

        if (isset($_REQUEST ['clase_entrada_consulta']) && $_REQUEST ['clase_entrada_consulta'] != '') {
            $clase = $_REQUEST ['clase_entrada_consulta'];
        } else {
            $clase = '';
        }

        if (isset($_REQUEST ['id_proveedor']) && $_REQUEST ['id_proveedor'] != '') {
            $proveedor = $_REQUEST ['id_proveedor'];
        } else {
            $proveedor = '';
        }

        $arreglo = array(
            $numeroEntrada,
            $fechaInicio,
            $fechaFinal,
            $clase,
            $proveedor
        );

        $cadenaSql = $this->miSql->getCadenaSql('consultarEntrada_busqueda', $arreglo);
        
        $entrada = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

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
        $variable .= "&usuario=" . $_REQUEST ['usuario'];
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
        $atributos ["leyenda"] = "Registrar Salida";
        echo $this->miFormulario->marcoAgrupacion('inicio', $atributos); {

            $esteCampo = "Agrupacion";
            $atributos ['id'] = $esteCampo;
            $atributos ['leyenda'] = "Información Referente a las Entradas";
            echo $this->miFormulario->agrupacion('inicio', $atributos); {

                if ($entrada) {

                    echo "<table id='tablaTitulos'>";

                    echo "<thead>
                <tr>
                   <th>Número Entrada y/o<br>Vigencia</th>
                    <th>Fecha Registro </th>
					<th>Clase Entrada</th>
					<th>Nit<br>Proveedor</th>
					<th>Razon Social<br>Proveedor</th>
			        <th>Generar Salida</th>
                </tr>
            </thead>
            <tbody>";

                    for ($i = 0; $i < count($entrada); $i ++) {
                        $variable = "pagina=" . $miPaginaActual; // pendiente la pagina para modificar parametro
                        $variable .= "&opcion=Salida";
                        $variable .= "&usuario=" . $_REQUEST ['usuario'];
                        $variable .= "&numero_entrada=" . $entrada [$i] [0];

                        if ($entrada [$i] [3] == 0) {

                            $arreglo = array(
                                $entrada [$i] [4],
                                $entrada [$i] [1],
                                $entrada [$i] [2],
                                '',
                                ''
                            );

                            $proveedor [0] [0] = 'NO APLICA';
                            $proveedor [0] [1] = 'NO APLICA';
                        } else {
                            $cadenaSql = $this->miSql->getCadenaSql('proveedor_informacion', $entrada [$i] [3]);

                            $proveedor = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                            $arreglo = array(
                                $entrada [$i] [4],
                                $entrada [$i] [1],
                                $entrada [$i] [2],
                                $proveedor [0] [0],
                                $proveedor [0] [1],
                                $entrada [$i] ['vigencia']
                            );
                        }

                        $arreglo = serialize($arreglo);
                        $variable .= "&datosGenerales=" . $arreglo;

                        $variable = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($variable, $directorio);

                        $mostrarHtml = "<tr>
                    <td><center>" . $entrada [$i] [4] . "</center></td>
                    <td><center>" . $entrada [$i] [1] . "</center></td>
                    <td><center>" . $entrada [$i] [2] . "</center></td>
                    <td><center>" . $proveedor [0] [0] . "</center></td>
                    <td><center>" . $proveedor [0] [1] . "</center></td>
                    <td><center>
                    	<a href='" . $variable . "'>
                            <img src='" . $rutaBloque . "/css/images/salida2.png' width='15px'>
                        </a>
                  	</center> </td>
           
                </tr>";
                        echo $mostrarHtml;
                        unset($mostrarHtml);
                        unset($variable);
                    }

                    echo "</tbody>";

                    echo "</table>";
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
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->cuadroMensaje($atributos);
                    // --------------- FIN CONTROL : Cuadro de Texto --------------------------------------------------
                }
            }
            echo $this->miFormulario->agrupacion('fin');
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
