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
        $_REQUEST ['tiempo'] = time();
        $tiempo = $_REQUEST ['tiempo'];
        $seccion ['tiempo'] = $tiempo;

        // ___________________________________________________________________________________
        // -------------------------------------------------------------------------------------------------

        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('consultarEntradaParticular', $_REQUEST ['numero_entrada']);
        $entrada = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");


        // $cadenaSql = $this->miSql->getCadenaSql ( 'clase_entrada_descrip', $entrada [0] [2] );
        // $Clase = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
        // $cadenaSql = $this->miSql->getCadenaSql ( 'consulta_proveedor', $entrada [0] [7] );
        // $proveedor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );

        $cadenaSql = $this->miSql->getCadenaSql('consulta_elementos', $_REQUEST ['numero_entrada']);
        $elementos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $cadenaSql = $this->miSql->getCadenaSql('consulta_elementos_validar', $_REQUEST ['numero_entrada']);
        $elementos_validacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $datosgenerales = unserialize($_REQUEST ['datosGenerales']);

        $entrada = array(
            'numero_entrada_c' => $datosgenerales [0],
            'fecha_entrada' => $datosgenerales [1],
            'clase' => $datosgenerales [2]
        );


        $_REQUEST = array_merge($_REQUEST, $entrada);
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
        echo $this->miFormulario->formulario($atributos); {
            $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

            $directorio = $this->miConfigurador->getVariableConfiguracion("host");
            $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
            $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

            $variable = "pagina=" . $miPaginaActual;
            $variable .= "&usuario=" . $_REQUEST['usuario'];
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

            // ---------------- SECCION: Controles del Formulario -----------------------------------------------

            $esteCampo = "marcoDatosBasicos";
            $atributos ['id'] = $esteCampo;
            $atributos ["estilo"] = "jqueryui";
            $atributos ['tipoEtiqueta'] = 'inicio';
            $atributos ["leyenda"] = "Registrar Salida";
            echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);

            if ($elementos_validacion [0] [0] == false) {

                $mensaje = "No Existen Elementos Asociados a la Entrada";

                // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                $esteCampo = 'mensajeError';
                $atributos ['id'] = $esteCampo;
                $atributos ['tipo'] = 'error';
                $atributos ['estilo'] = 'textoCentrar';
                $atributos ['mensaje'] = $mensaje;

                $tab ++;

                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->cuadroMensaje($atributos);
                unset($atributos);

                $atributos ["id"] = "botones";
                $atributos ["estilo"] = "marcoBotones";
                echo $this->miFormulario->division("inicio", $atributos);

                // -----------------CONTROL: Botón ----------------------------------------------------------------
                $esteCampo = 'botonRegresar';
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
                $atributos ["valor"] = $this->lenguaje->getCadena($esteCampo);
                $atributos ['nombreFormulario'] = $esteBloque ['nombre'];
                $tab ++;

                // Aplica atributos globales al control
                $atributos = array_merge($atributos, $atributosGlobales);
                echo $this->miFormulario->campoBoton($atributos);
                unset($atributos);

                echo $this->miFormulario->division('fin');

                $valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
                $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                $valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
                $valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
                $valorCodificado .= "&opcion=regresar";
                $valorCodificado .= "&redireccionar=regresar";
            } else {



// 				var_dump(unserialize($_REQUEST['datosGenerales']));


                unset($atributos); {

                    $esteCampo = 'numero_entrada_c';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ['columnas'] = 1;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    // $atributos ['validar'] = 'required, minSize[4],maxSize[4],custom[onlyNumberSp]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 10;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 250;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "fecha_entrada";
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ['columnas'] = 2;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    // $atributos ['validar'] = 'required, minSize[4],maxSize[4],custom[onlyNumberSp]';
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = true;
                    $atributos ['tamanno'] = 8;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "clase";
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ['columnas'] = 2;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = '';
                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = true;
                    $atributos ['tamanno'] = 15;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = "Agrupacion";
                    $atributos ['id'] = $esteCampo;
                    $atributos ['leyenda'] = "Elementos Referentes a las Entrada";
                    echo $this->miFormulario->agrupacion('inicio', $atributos);






                    $esteCampo = "selecc_registros";
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['tab'] = $tab ++;
                    $atributos ['seleccion'] = 0;
                    $atributos ['anchoEtiqueta'] = 150;
                    $atributos ['evento'] = '';
                    if (isset($_REQUEST [$esteCampo])) {
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
                    // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "estado_entrada" );
                    $matrizItems = array(
                        array(
                            '0',
                            'Ningun Elementos'
                        ),
                        array(
                            '1',
                            'Todos Elementos'
                        )
                    );
                    $atributos ['matrizItems'] = $matrizItems;
                    // $atributos['miniRegistro']=;
                    $atributos ['baseDatos'] = "inventarios";
                    // $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos); {
                        $cantidaditems = count($elementos);

                        if ($elementos) {

                            echo "<table id='tablaTitulos'>";

                            echo "<thead>
				                                <tr>
                                                                    <th>Nivel Inventarios</th>
                                                                    <th>Cantidad</th>
                                                                    <th>Cantidad Asignar</th>			
                                                                    <th>Nombre</th>
								    <th>Selección Items</th>
							        </tr>
                                                              </thead>
					                      <tbody>";

                            for ($i = 0; $i < count($elementos); $i ++) {

                                $arreglo_nombreItems [] = $elementos [$i] [1];

                                $mostrarHtml = "<tr>
						                    <td><center> " . $elementos [$i] ['item'] . "</center></td>
						                    <td><center> " . $elementos[$i]['cantidad_por_asignar'] . "</center></td>
						                    <td><center>";

                                $atributos ["id"] = "botones";
                                $atributos ["estilo"] = "marcoBotones";
                                $mostrarHtml .= $this->miFormulario->division("inicio", $atributos);

                                $esteCampo = "cantidadAsignar" . $i;
                                $atributos ['id'] = $esteCampo;
                                $atributos ['nombre'] = $esteCampo;
                                $atributos ['tipo'] = 'text';
                                $atributos ['estilo'] = 'center';
                                $atributos ['marco'] = true;
                                $atributos ['estiloMarco'] = '';
                                $atributos ['columnas'] = 1;
                                $atributos ['textoFondo'] = "Cantidad";
                                $atributos ['dobleLinea'] = 0;
                                $atributos ['tabIndex'] = $tab;
                                $atributos ['validar'] = 'custom[number]';
                                if (isset($_REQUEST [$esteCampo])) {
                                    $atributos ['valor'] = $_REQUEST [$esteCampo];
                                } else {
                                    $atributos ['valor'] = '';
                                }
                                $atributos ['deshabilitado'] = false;
                                $atributos ['tamanno'] = 10;
                                $atributos ['maximoTamanno'] = '';
                                $atributos ['anchoEtiqueta'] = 0;
                                $tab ++;

                                // Aplica atributos globales al control
                                $atributos = array_merge($atributos, $atributosGlobales);
                                $mostrarHtml .= ($elementos[$i]['cantidad_por_asignar'] == 1) ? ' ' : $this->miFormulario->campoCuadroTexto($atributos);

                                $mostrarHtml .= $this->miFormulario->division('fin');
                                $mostrarHtml .= "</center></td>
			     							 <td><center>" . $elementos [$i] ['descripcion'] . "</center></td>
				   							<td><center>";

                                // ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                                $nombre = 'item' . $i;
                                $atributos ['id'] = $nombre;
                                $atributos ['nombre'] = $nombre;
                                $atributos ['marco'] = true;
                                $atributos ['estiloMarco'] = true;
                                $atributos ["etiquetaObligatorio"] = true;
                                $atributos ['columnas'] = 1;
                                $atributos ['dobleLinea'] = 1;
                                $atributos ['tabIndex'] = $tab;
                                $atributos ['etiqueta'] = '';
                                if (isset($_REQUEST [$esteCampo])) {
                                    $atributos ['valor'] = $_REQUEST [$esteCampo];
                                } else {
                                    $atributos ['valor'] = $elementos [$i] [0];
                                }

                                $atributos ['deshabilitado'] = false;
                                $tab ++;

                                // Aplica atributos globales al control
                                $atributos = array_merge($atributos, $atributosGlobales);
                                $mostrarHtml .= $this->miFormulario->campoCuadroSeleccion($atributos);

                                $mostrarHtml .= "</center></td>
                    						</tr>";
                                echo $mostrarHtml;
                                unset($mostrarHtml);
                                unset($variable);
                                //}
                            }

                            echo "</tbody>";

                            echo "</table>";
                        }
                    }
                    echo $this->miFormulario->agrupacion('fin');

                    // ------------------Division para los botones-------------------------
                    $atributos ["id"] = "botones";
                    $atributos ["estilo"] = "marcoBotones";
                    echo $this->miFormulario->division("inicio", $atributos);

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
                    $atributos ["valor"] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['nombreFormulario'] = $esteBloque ['nombre'];
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoBoton($atributos);
                    unset($atributos);
                    // -----------------FIN CONTROL: Botón -----------------------------------------------------------

                    echo $this->miFormulario->division('fin');

                    echo $this->miFormulario->marcoAgrupacion('fin');






                    $valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
                    $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
                    $valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
                    $valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
                    $valorCodificado .= "&opcion=Registrar";
                    $valorCodificado .= "&usuario=" . $_REQUEST['usuario'];
                    $valorCodificado .= "&numero_entrada=" . $_REQUEST ['numero_entrada'];
                    $valorCodificado .= "&datosGenerales=" . $_REQUEST ['datosGenerales'];
                    $valorCodificado .= "&cantidadItems=" . $cantidaditems;
                    if (isset($arreglo_nombreItems)) {
                        $valorCodificado.="&nombreItems=" . serialize($arreglo_nombreItems);
                    }
                }

                // ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
                // ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
                // Se debe declarar el mismo atributo de marco con que se inició el formulario.
            }

            // -----------------FIN CONTROL: Botón -----------------------------------------------------------
            // ------------------Fin Division para los botones-------------------------
            echo $this->miFormulario->division("fin");

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

            return true;
        }
    }

}

$miSeleccionador = new registrarForm($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->miForm();
?>

