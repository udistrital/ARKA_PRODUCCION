<?php

use inventarios\gestionDepreciacion\registrarDepreciacion\funcion\calcularDepreciacion;

//include_once ("gestionDepreciacion/registrarDepreciacion/funcion/calcularDepreciacion.php");

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
        $rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];

        $atributosGlobales ['campoSeguro'] = 'true';

        $_REQUEST ['tiempo'] = time();
        $tiempo = $_REQUEST ['tiempo'];

        // -------------------------------------------------------------------------------------------------
        // -------------------------------------------------------------------------------------------------
        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = "sicapital";
        $esteRecursoDBO = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);


        if (isset($_REQUEST['grupo_contable']) && $_REQUEST['grupo_contable'] != '') {
            $nivel = $_REQUEST['grupo_contable'];
        } else {
            $nivel = '';
        }

        if (isset($_REQUEST['funcionario']) && $_REQUEST['funcionario'] != '') {
            $funcionario = $_REQUEST['funcionario'];
        } else {
            $funcionario = '';
        }

        if (isset($_REQUEST['fechaCorte']) && $_REQUEST['fechaCorte'] != '') {
            $fechaCorte = $_REQUEST['fechaCorte'];
        } else {
            $fechaCorte = date('Y-m-d');
        }

        if (isset($_REQUEST['id_placa']) && $_REQUEST['id_placa'] != '') {
            $placa = $_REQUEST['id_placa'];
        } else {
            $placa = '';
        }

        if (isset($_REQUEST['cuenta_salida']) && $_REQUEST['cuenta_salida'] != '') {
            $cuenta_contable = $_REQUEST['cuenta_salida'];
        } else {
            $cuenta_contable = '';
        }


        $datos = array(
            'grupo' => $nivel,
            'funcionario' => $funcionario,
            'fecha_corte' => $fechaCorte,
            'placa' => $placa,
            'cuenta_salida' => $cuenta_contable,
        );

        //-***************************************************************-//
        $cadenaSql = $this->miSql->getCadenaSql('mostrarInfoDepreciar', $datos);
        $items = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");



//        //Limpiar la tabla de los registros

        $cadenaSql = $this->miSql->getCadenaSql('limpiarDepreciacion', null);
        $limpieza = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");


        if ($items) {
            $a = 0;
            $cuota = 0;
// Calculando la depreciacion
            foreach ($items as $key => $values) {

                $meses = intval($items[$key]['vida_util']);
                $precio = $items[$key]['valor'];
                $inflacion = $items[$key]['ajuste_inflacionario'];

                $fecha_salida = new \DateTime($items[$key]['fecha_salida']);
                $fecha_corte = new \DateTime($_REQUEST['fechaCorte']);
                $fecha_limite = new \DateTime('1999-12-31');

                $periodos = $fecha_corte->diff($fecha_salida);
                $meses_periodo = $periodos->format("%m");
                $años_periodo = $periodos->format("%y") * 12;
                $periodos_fecha = $meses_periodo + $años_periodo + 1;

                $valor_historico = $precio;

                if ($fecha_salida <= $fecha_limite) {
                    $inflacion = $inflacion;
                } else {
                    $inflacion = 0;
                }

                $valor_ajustado = $valor_historico + $inflacion;

//Valor de la Cuota
                if ($meses == 0) {
                    $cuota = 0;
                    $periodos_fecha = 0;
                    $dep_acumulada = 0;
                    $cuota_inflacion = 0;
                    $api_acumulada = 0;
                } else {
                    $cuota = $items[0]['valor_cuota'];

                    if ($inflacion > 0) {
                        $cuota_inflacion = $inflacion / $meses;
                    } else {
                        $cuota_inflacion = 0;
                    }

                    if ($periodos_fecha >= $meses) {
                        $dep_acumulada = $valor_historico;
                        $api_acumulada = $inflacion;
                    } else {
                        $dep_acumulada = $cuota * $periodos_fecha;
                        $api_acumulada = $cuota_inflacion * $periodos_fecha;
                    }
                }

                $circular56 = $valor_historico + $inflacion;
                $circular_depreciacion = $api_acumulada + $dep_acumulada;
                $valor_libros = $valor_ajustado - $circular_depreciacion;

                $depreciacion_calculada[$a] = array(
                    0 => $items[$key]['placa'],
                    'placa' => $items[$key]['placa'],
                    1 => $items[$key]['grupo_codigo'],
                    'cuenta' => $items[$key]['grupo_codigo'],
                    2 => $items[$key]['grupo_nombre'],
                    'grupo' => $items[$key]['grupo_nombre'],
                    3 => $meses,
                    'meses_depreciar' => $meses,
                    4 => $items[$key]['fecha_salida'],
                    'fechaSalida' => $items[$key]['fecha_salida'],
                    5 => $_REQUEST['fechaCorte'],
                    'fechaCorte' => $_REQUEST['fechaCorte'],
                    6 => $periodos_fecha,
                    'periodos_fecha' => $periodos_fecha,
                    7 => $valor_historico,
                    'valor_historico' => $valor_historico,
                    8 => $valor_ajustado,
                    'valor_ajustado' => $valor_ajustado,
                    9 => $cuota,
                    'cuota' => $cuota,
                    10 => $dep_acumulada,
                    'depreciacion_acumulada' => $dep_acumulada,
                    11 => $circular56,
                    'circular_56' => $circular56,
                    12 => $cuota_inflacion,
                    'cuota_inflacion' => $cuota_inflacion,
                    13 => $api_acumulada,
                    'api_acumulada' => $api_acumulada,
                    14 => $circular_depreciacion,
                    'circular_depreciacion' => $circular_depreciacion,
                    15 => $valor_libros,
                    'valor_libros' => $valor_libros,
                );

                $depreciacion_reporte[$a] = array(
                    0 => $items[$key]['grupo_cuentasalida'],
                    'cuenta' => $items[$key]['grupo_cuentasalida'],
                    1 => $items[$key]['grupo_nombre'],
                    'grupo' => $items[$key]['grupo_nombre'],
                    2 => $items[$key]['placa'],
                    'placa' => $items[$key]['placa'],
                    3 => $items[$key]['descripcion'],
                    'nombre_items' => $items[$key]['descripcion'],
                    4 => $meses,
                    'meses_depreciar' => $meses,
                    5 => $items[$key]['fecha_salida'],
                    'fechaSalida' => $items[$key]['fecha_salida'],
                    6 => $_REQUEST['fechaCorte'],
                    'fechaCorte' => $_REQUEST['fechaCorte'],
                    7 => $periodos_fecha,
                    'periodos_fecha' => $periodos_fecha,
                    8 => number_format($valor_historico, 2, ',', '.'),
                    'valor_historico' => number_format($valor_historico, 2, ',', '.'),
                    9 => number_format($valor_ajustado, 2, ',', '.'),
                    'valor_ajustado' => number_format($valor_ajustado, 2, ',', '.'),
                    10 => number_format($cuota, 2, ',', '.'),
                    'cuota' => number_format($cuota, 2, ',', '.'),
                    11 => number_format($dep_acumulada, 2, ',', '.'),
                    'depreciacion_acumulada' => number_format($dep_acumulada, 2, ',', '.'),
                    12 => number_format($circular56, 2, ',', '.'),
                    'circular_56' => number_format($circular56, 2, ',', '.'),
                    13 => number_format($cuota_inflacion, 2, ',', '.'),
                    'cuota_inflacion' => number_format($cuota_inflacion, 2, ',', '.'),
                    14 => number_format($api_acumulada, 2, ',', '.'),
                    'ajuste_inflacionario' => number_format($api_acumulada, 2, ',', '.'),
                    15 => number_format($circular_depreciacion, 2, ',', '.'),
                    'circular_depreciacion' => number_format($circular_depreciacion, 2, ',', '.'),
                    16 => number_format($valor_libros, 2, ',', '.'),
                    'valor_libros' => number_format($valor_libros, 2, ',', '.'),
                );

                $depreciacion_registro[$a] = array(
                    'elemento_individual' => $items[$key]['id_elemento_ind'],
                    'grupo' => $items[$key]['grupo'],
                    'meses_depreciar' => $meses,
                    'fechaSalida' => $items[$key]['fecha_salida'],
                    'fechaCorte' => $_REQUEST['fechaCorte'],
                    'cantidad' => 1,
                    'precio' => $items[$key]['valor'],
                    'valor_historico' => $valor_historico,
                    'valor_ajustado' => $valor_ajustado,
                    'cuota' => $cuota,
                    'periodos_fecha' => $periodos_fecha,
                    'depreciacion_acumulada' => $dep_acumulada,
                    'circular_56' => $circular56,
                    'cuota_inflacion' => $cuota_inflacion,
                    'api_acumulada' => $api_acumulada,
                    'circular_depreciacion' => $circular_depreciacion,
                    'valor_libros' => $valor_libros,
                );

                //Por ahora lo haremos una vez y limpiaremos para verificiar rendimiento
                $cadenaSql = $this->miSql->getCadenaSql('registrarDepreciacion', $depreciacion_registro[$a]);
                $registro_depreciacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");

                //Insertar en la base de datos los resultados de la depreciacion
                $a++;
            }
        }

        
              echo "temrinamos";
        exit;
//        foreach ($depreciacion_registro as $key => $values) {
//            $cadenaSql = $this->miSql->getCadenaSql('registrarDepreciacion', $depreciacion_registro[$key]);
//            $registro_depreciacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
//        }
        //REVISAR EL REQUEST: Si lo muestra o genera PDF simplemente
        // ---------------- SECCION: Parámetros Globales del Formulario ----------------------------------
        /**
         * Atributos que deben ser aplicados a todos los controles de este formulario.
         * Se utiliza un arreglo
         * independiente debido a que los atributos individuales se reinician cada vez que se declara un campo.
         *
         * Si se utiliza esta técnica es necesario realizar un mezcla entre este arreglo y el específico en cada control:
         * $atributos= array_merge($atributos,$atributosGlobales);
         */
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

        $variable = "pagina=" . $miPaginaActual;
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
        $atributos ["leyenda"] = "Depreciación Elementos Realizada a Fecha de Corte " . $_REQUEST['fechaCorte'];
        echo $this->miFormulario->marcoAgrupacion('inicio', $atributos);

        if ($items) {
            //Data table 
            echo $this->miFormulario->tablaReporte($depreciacion_reporte);
            echo $this->miFormulario->marcoAgrupacion('fin');

            // ------------------Division para los botones-------------------------
            $atributos ["id"] = "botones";
            $atributos ["estilo"] = "marcoBotones";
            echo $this->miFormulario->division("inicio", $atributos);

            // -----------------CONTROL: Botón ----------------------------------------------------------------
            $esteCampo = 'botonContinuar';
            $atributos ["id"] = $esteCampo;
            $atributos ["tabIndex"] = $tab;
            $atributos ["tipo"] = '';
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

            echo $this->miFormulario->division('fin');

            echo $this->miFormulario->marcoAgrupacion('fin');

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
            $valorCodificado.= "&pagina=" . $this->miConfigurador->getVariableConfiguracion('pagina');
            $valorCodificado.= "&bloque=" . $esteBloque ['nombre'];
            $valorCodificado.= "&bloqueGrupo=" . $esteBloque ["grupo"];
            $valorCodificado.= "&opcion=generarPDF";
            $valorCodificado.="&depreciacion=" . base64_encode(serialize($depreciacion_calculada));


            /*
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
        } else {

            $mensaje = "No Se Encontraron<br>Elementos asociados a ese nivel de Inventario";

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

}

$miSeleccionador = new registrarForm($this->lenguaje, $this->miFormulario, $this->sql);

$miSeleccionador->miForm();
?>

