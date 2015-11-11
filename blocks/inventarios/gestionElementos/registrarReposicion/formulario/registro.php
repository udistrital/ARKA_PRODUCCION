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

// lineas para conectar base de d atos-------------------------------------------------------------------------------------------------
        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "sicapital";
        $esteRecursoDBO = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $seleccion = unserialize(base64_decode($_REQUEST['elemento']));

//        var_dump($seleccion);
        //COnsultar datos de la entrada
        $cadenaSql = $this->miSql->getCadenaSql('consultarEntrada', $seleccion['id_entrada']);
        
        $entrada = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");


        //Consultar datos de la Salida
        $cadenaSql = $this->miSql->getCadenaSql('consultarSalida', $seleccion['id_salida']);
        $salida = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

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
        echo $this->miFormulario->formulario($atributos);
        {
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

            echo $this->miFormulario->agrupacion('fin');

// ---------------- SECCION: Controles del Formulario -----------------------------------------------

            $esteCampo = "marcoDatosBasicos";
            $atributos ['id'] = $esteCampo;
            $atributos ["estilo"] = "jqueryui";
            $atributos ['tipoEtiqueta'] = 'inicio';
            $atributos ["leyenda"] = "Registrar Reposición";
            echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);
            unset($atributos);
            {
                $esteCampo = "marcoDatosBasicos";
                $atributos ['id'] = $esteCampo;
                $atributos ["estilo"] = "jqueryui";
                $atributos ['tipoEtiqueta'] = 'inicio';
                $atributos ["leyenda"] = "Datos para Registrar Nueva Entrada ";
                echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);
                {

//-------------------------------------
                    $esteCampo = 'nombre_proveedor';
                    $atributos ['id'] = $esteCampo;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['tipo'] = 'text';
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['marco'] = true;
                    $atributos ['estiloMarco'] = '';
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['columnas'] = 1;
                    $atributos ['dobleLinea'] = 0;
                    $atributos ['tabIndex'] = $tab;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = '';

                    if (isset($seleccion['nombre_proveedor'])) {
                        $atributos ['valor'] = $seleccion['nombre_proveedor'];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 55;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['ancdhoEtiqueta'] = 135;
                    $tab ++;

                    // Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);

                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);
                    //---------------------------------------------------------------------------------
                    $atributos ["id"] = "proveedor"; // No cambiar este nombre
                    $atributos ["tipo"] = "hidden";
                    $atributos ['estilo'] = '';
                    $atributos ["obligatorio"] = false;
                    $atributos ['marco'] = true;
                    $atributos ["etiqueta"] = "";
                    $atributos ["valor"] = $seleccion['proveedor'];
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    //_-------------------------------------------
                    $esteCampo = 'numero_factura';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[50]';

                    if (isset($entrada[0][$esteCampo])) {
                        $atributos ['valor'] = $entrada[0][$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 15;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 155;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'fecha_factura';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required,custom[date]';

                    if (isset($entrada[0][$esteCampo])) {
                        $atributos ['valor'] = $entrada[0][$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 12;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 135;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);

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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 135;

                    if (isset($entrada[0] [$esteCampo])) {
                        $atributos ['seleccion'] = $entrada[0][$esteCampo];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }

                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("sede");
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;

// Utilizar lo siguiente cuando no se pase un arreglo:
// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);


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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 135;
                    if (isset($entrada[0][$esteCampo])) {
                        $atributos ['seleccion'] = $entrada[0][$esteCampo];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("dependencias");
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;

// Utilizar lo siguiente cuando no se pase un arreglo:
// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);

// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'supervisor';
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['tab'] = $tab ++;
                    $atributos ['anchoEtiqueta'] = 135;
                    $atributos ['evento'] = '';
                    if (isset($entrada[0][$esteCampo])) {
                        $atributos ['seleccion'] = $entrada[0][$esteCampo];
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
                    $atributos ['anchoCaja'] = 52;
                    $atributos ['miEvento'] = '';
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("funcionarios");
                    $matrizItems = array(
                        array(
                            0,
                            ' '
                        )
                    );
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;
// $atributos['miniRegistro']=;
                    $atributos ['baseDatos'] = "inventarios";
// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);

// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'observaciones_entrada';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1]';
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 20;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    if (isset($entrada[0]['observaciones'])) {
                        $atributos ['valor'] = $entrada[0]['observaciones'];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTextArea($atributos);
                    unset($atributos);

                    $atributos ["id"] = "cuadro_ordenador";
                    $atributos ["estiloEnLinea"] = "display:block";
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->division("inicio", $atributos);
                    unset($atributos);
                    {

                        $esteCampo = "ordenadorGasto";
                        $atributos ['id'] = $esteCampo;
                        $atributos ['leyenda'] = $this->lenguaje->getCadena($esteCampo);
                        echo $this->miFormulario->agrupacion('inicio', $atributos);
                        {

// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                            $esteCampo = 'asignacionOrdenador';
                            $atributos ['nombre'] = $esteCampo;
                            $atributos ['id'] = $esteCampo;
                            $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                            $atributos ["etiquetaObligatorio"] = true;
                            $atributos ['tab'] = $tab ++;
                            $atributos ['anchoEtiqueta'] = 180;
                            $atributos ['evento'] = '';
                            if (isset($entrada[0]['ordenador'])) {
                                $atributos ['seleccion'] = $entrada[0]['ordenador'];
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
                            $atributos ['anchoCaja'] = 55;
                            $atributos ['miEvento'] = '';
                            $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("tipoComprador");
                            $matrizItems = array(
                                array(
                                    0,
                                    ' '
                                )
                            );
                            $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                            $atributos ['matrizItems'] = $matrizItems;
// $atributos['miniRegistro']=;
                            $atributos ['baseDatos'] = "inventarios";
// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
// Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroLista($atributos);
                            unset($atributos);

// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                            $esteCampo = 'nombreOrdenador';
                            $atributos ['id'] = $esteCampo;
                            $atributos ['nombre'] = $esteCampo;
                            $atributos ['tipo'] = 'text';
                            $atributos ['estilo'] = 'jqueryui';
                            $atributos ['marco'] = true;
                            $atributos ['estiloMarco'] = '';
                            $atributos ["etiquetaObligatorio"] = false;
                            $atributos ['columnas'] = 1;
                            $atributos ['dobleLinea'] = 0;
                            $atributos ['tabIndex'] = $tab;
                            $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                            $atributos ['validar'] = 'required, minSize[1],maxSize[2000]';

                            if (isset($entrada[0]['ordenador_nombre'])) {
                                $atributos ['valor'] = $entrada[0]['ordenador_nombre'];
                            } else {
                                $atributos ['valor'] = '';
                            }
                            $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                            $atributos ['deshabilitado'] = false;
                            $atributos ['tamanno'] = 55;
                            $atributos ['maximoTamanno'] = '';
                            $atributos ['anchoEtiqueta'] = 180;
                            $tab ++;

// Aplica atributos globales al control
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroTexto($atributos);
                            unset($atributos);

                            $esteCampo = 'id_ordenador';
                            $atributos ["id"] = $esteCampo; // No cambiar este nombre
                            $atributos ["tipo"] = "hidden";
                            $atributos ['estilo'] = '';
                            $atributos ["obligatorio"] = false;
                            $atributos ['marco'] = true;
                            $atributos ["etiqueta"] = "";
                            if (isset($entrada[0]['ordenador'])) {
                                $atributos ['valor'] = $entrada[0]['ordenador'];
                            } else {
                                $atributos ['valor'] = '';
                            }
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroTexto($atributos);
                            unset($atributos);

                            $esteCampo = 'tipo_ordenador';
                            $atributos ["id"] = $esteCampo; // No cambiar este nombre
                            $atributos ["tipo"] = "hidden";
                            $atributos ['estilo'] = '';
                            $atributos ["obligatorio"] = false;
                            $atributos ['marco'] = true;
                            $atributos ["etiqueta"] = "";
                            if (isset($entrada[0]['tipo_ordenador'])) {
                                $atributos ['valor'] = $entrada[0]['tipo_ordenador'];
                            } else {
                                $atributos ['valor'] = '';
                            }
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroTexto($atributos);
                            unset($atributos);

                            $esteCampo = 'identificacion_ordenador';
                            $atributos ["id"] = $esteCampo; // No cambiar este nombre
                            $atributos ["tipo"] = "hidden";
                            $atributos ['estilo'] = '';
                            $atributos ["obligatorio"] = false;
                            $atributos ['marco'] = true;
                            $atributos ["etiqueta"] = "";
                            if (isset($entrada[0]['identificacion_ordenador'])) {
                                $atributos ['valor'] = $entrada[0]['identificacion_ordenador'];
                            } else {
                                $atributos ['valor'] = '';
                            }
                            $atributos = array_merge($atributos, $atributosGlobales);
                            echo $this->miFormulario->campoCuadroTexto($atributos);
                            unset($atributos);
                        }
                    }
                    echo $this->miFormulario->agrupacion('fin');
                    unset($atributos);
                }

                echo $this->miFormulario->marcoAgrupacion('fin');

                $esteCampo = "marcoDatosBasicos";
                $atributos ['id'] = $esteCampo;
                $atributos ["estilo"] = "jqueryui";
                $atributos ['tipoEtiqueta'] = 'inicio';
                $atributos ["leyenda"] = "Detalle Nuevo Elemento ";
                echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);


                unset($atributos);
                {

                    $esteCampo = 'serie';
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

                    if (isset($seleccion[$esteCampo])) {
                        $atributos ['valor'] = $seleccion[$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 15;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 210;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = 'marca';
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

                    if (isset($seleccion[$esteCampo])) {
                        $atributos ['valor'] = $seleccion[$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 15;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = 'descripcion';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1]';
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 20;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    if (isset($seleccion[$esteCampo])) {
                        $atributos ['valor'] = $seleccion[$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTextArea($atributos);
                    unset($atributos);

                    $esteCampo = 'cantidad';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[onlyNumberSp]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $_REQUEST [$esteCampo];
                    } else {
                        $atributos ['valor'] = '1';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = true;
                    $atributos ['tamanno'] = 10;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = 'unidad';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1] ';

                    if (isset($seleccion[$esteCampo])) {
                        $atributos ['valor'] = $seleccion[$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 10;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = 'valor';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[number]';
                    if (isset($seleccion[$esteCampo])) {
                        $atributos ['valor'] = $seleccion[$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 10;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;

                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

// ---------------- CONTROL: Cuadro Lista --------------------------------------------------------

                    $esteCampo = 'iva';
                    $atributos ['columnas'] = 2;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['seleccion'] = $seleccion[$esteCampo];
                    $atributos ['evento'] = '';
                    $atributos ['deshabilitado'] = false;
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['tab'] = $tab;
                    $atributos ['tamanno'] = 1;
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['validar'] = '';
                    $atributos ['limitar'] = true;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 220;
                    if (isset($seleccion[$esteCampo])) {
                        $atributos ['valor'] = $seleccion[$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
// Valores a mostrar en el control
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("consultar_tipo_iva");
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");

                    $atributos ['matrizItems'] = $matrizItems;

// Utilizar lo siguiente cuando no se pase un arreglo:
// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);

// ---------------- CONTROL: Cuadro Lista --------------------------------------------------------
                    /*
                     * $esteCampo = 'bodega'; $atributos ['columnas'] = 1; $atributos ['nombre'] = $esteCampo; $atributos ['id'] = $esteCampo; $atributos ['seleccion'] = - 1; $atributos ['evento'] = ''; $atributos ['deshabilitado'] = false; $atributos ["etiquetaObligatorio"] = true; $atributos ['tab'] = $tab; $atributos ['tamanno'] = 1; $atributos ['estilo'] = 'jqueryui'; $atributos ['validar'] = 'required'; $atributos ['limitar'] = false; $atributos ['etiqueta'] = $this->lenguaje->getCadena ( $esteCampo ); $atributos ['anchoEtiqueta'] = 220; // Valores a mostrar en el control $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "consultar_bodega" ); $matrizItems = $esteRecursoDB->ejecutarAcceso ( $atributos ['cadena_sql'], "busqueda" ); $atributos ['matrizItems'] = $matrizItems; // Utilizar lo siguiente cuando no se pase un arreglo: // $atributos['baseDatos']='ponerAquiElNombreDeLaConexión'; // $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar'; $tab ++; $atributos = array_merge ( $atributos, $atributosGlobales ); echo $this->miFormulario->campoCuadroLista ( $atributos ); unset ( $atributos );
                     */
                    $esteCampo = 'subtotal_sin_iva';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[number]';

                    if (isset($seleccion[$esteCampo])) {
                        $atributos ['valor'] = $seleccion[$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 10;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = 'total_iva';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[number]';

                    if (isset($seleccion[$esteCampo])) {
                        $atributos ['valor'] = $seleccion[$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 10;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

                    $esteCampo = 'total_iva_con';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1],maxSize[30],custom[number]';

                    if (isset($seleccion[$esteCampo])) {
                        $atributos ['valor'] = $seleccion[$esteCampo];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 10;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);
                }
                echo $this->miFormulario->agrupacion("fin");
                unset($atributos);


                $esteCampo = "marcoDatosBasicos";
                $atributos ['id'] = $esteCampo;
                $atributos ["estilo"] = "jqueryui";
                $atributos ['tipoEtiqueta'] = 'inicio';
                $atributos ["leyenda"] = "Datos para Registrar  Salida ";
                echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);
                unset($atributos);
                {

                    $esteCampo = 'vigencia';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[4],maxSize[4],custom[onlyNumberSp]';

                    if (isset($_REQUEST [$esteCampo])) {
                        $atributos ['valor'] = $salida[0]['vigencia'];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 3;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    //echo $this->miFormulario->campoCuadroTexto($atributos);
                    unset($atributos);

// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'sede_salida';
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
                    $atributos ['limitar'] = true;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 130;

                    if (isset($salida[0]['sede'])) {
                        $atributos ['seleccion'] = $salida[0]['sede'];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }

                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("sede");
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;

// Utilizar lo siguiente cuando no se pase un arreglo:
// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);

                    $esteCampo = "dependencia_salida";
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
                    $atributos ['limitar'] = true;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 150;
                    if (isset($salida[0]['dependencia'])) {
                        $atributos ['seleccion'] = $salida[0]['dependencia'];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("dependencias");

                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;

// Utilizar lo siguiente cuando no se pase un arreglo:
// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);



                    $esteCampo = "ubicacion_salida";
                    $atributos ['columnas'] = 1;
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['evento'] = '';
                    $atributos ['deshabilitado'] = false;
                    $atributos ["etiquetaObligatorio"] = false;
                    $atributos ['tab'] = $tab;
                    $atributos ['tamanno'] = 1;
                    $atributos ['estilo'] = 'jqueryui';
                    $atributos ['validar'] = ' ';
                    $atributos ['limitar'] = true;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['anchoEtiqueta'] = 130;
                    if (isset($salida[0]['ubicacion'])) {
                        $atributos ['seleccion'] = $salida[0]['ubicacion'];
                    } else {
                        $atributos ['seleccion'] = - 1;
                    }
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("dependencias");

                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;

// Utilizar lo siguiente cuando no se pase un arreglo:
// $atributos['baseDatos']='ponerAquiElNombreDeLaConexión';
// $atributos ['cadena_sql']='ponerLaCadenaSqlAEjecutar';
                    $tab ++;
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);



                    $esteCampo = "funcionario_salida";
                    $atributos ['nombre'] = $esteCampo;
                    $atributos ['id'] = $esteCampo;
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ["etiquetaObligatorio"] = true;
                    $atributos ['tab'] = $tab ++;
                    $atributos ['anchoEtiqueta'] = 130;
                    $atributos ['evento'] = '';
                    if (isset($salida[0]['funcionario'])) {
                        $atributos ['seleccion'] = $salida[0]['funcionario'];
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
                    $atributos ['anchoCaja'] = 52;
                    $atributos ['miEvento'] = '';
                    $atributos ['cadena_sql'] = $this->miSql->getCadenaSql("funcionarios");
                    $matrizItems = array(
                        array(
                            0,
                            ' '
                        )
                    );
                    $matrizItems = $esteRecursoDB->ejecutarAcceso($atributos ['cadena_sql'], "busqueda");
                    $atributos ['matrizItems'] = $matrizItems;
// $atributos['miniRegistro']=;
                    $atributos ['baseDatos'] = "inventarios";
// $atributos ['cadena_sql'] = $this->miSql->getCadenaSql ( "clase_entrada" );
// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoCuadroLista($atributos);
                    unset($atributos);


// ---------------- CONTROL: Cuadro de Texto --------------------------------------------------------
                    $esteCampo = 'observaciones';
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
                    $atributos ['etiqueta'] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['validar'] = 'required, minSize[1]';
                    $atributos ['titulo'] = $this->lenguaje->getCadena($esteCampo . 'Titulo');
                    $atributos ['deshabilitado'] = false;
                    $atributos ['tamanno'] = 20;
                    $atributos ['maximoTamanno'] = '';
                    $atributos ['anchoEtiqueta'] = 220;
                    if (isset($salida[0]['observaciones'])) {
                        $atributos ['valor'] = $salida[0]['observaciones'];
                    } else {
                        $atributos ['valor'] = '';
                    }
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoTextArea($atributos);
                    unset($atributos);



// ---------------- FIN SECCION: Controles del Formulario -------------------------------------------
// ----------------FINALIZAR EL FORMULARIO ----------------------------------------------------------
// Se debe declarar el mismo atributo de marco con que se inició el formulario.
                }





// ------------------Division para los botones-------------------------
                $atributos ["id"] = "botones";
                $atributos ["estilo"] = "marcoBotones";
                echo $this->miFormulario->division("inicio", $atributos);
                unset($atributos);
                {
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
                    $atributos ["valor"] = $this->lenguaje->getCadena($esteCampo);
                    $atributos ['nombreFormulario'] = $esteBloque ['nombre'];
                    $tab ++;

// Aplica atributos globales al control
                    $atributos = array_merge($atributos, $atributosGlobales);
                    echo $this->miFormulario->campoBoton($atributos);
// -----------------FIN CONTROL: Botón -----------------------------------------------------------
                }
                echo $this->miFormulario->division('fin');

                echo $this->miFormulario->marcoAgrupacion('fin');

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

            $valorCodificado = "actionBloque=" . $esteBloque ["nombre"];
            $valorCodificado .= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
            $valorCodificado .= "&bloque=" . $esteBloque ['nombre'];
            $valorCodificado .= "&bloqueGrupo=" . $esteBloque ["grupo"];
            $valorCodificado .= "&opcion=registrar";
            $valorCodificado .= "&elemento=" . $_REQUEST['elemento']; 
            $valorCodificado .= "&usuario=" . $_REQUEST['usuario'];
// $valorCodificado .= "&funcionario=" . $funcionario ['responsable_ante'];
// $valorCodificado .= "&idfuncionario=" . $funcionario ['id_funcionario'];

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
