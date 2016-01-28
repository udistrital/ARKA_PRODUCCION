<?php

namespace inventarios\gestionSalidas\registrarSalidas\funcion;

use inventarios\gestionSalidas\registrarSalidas\funcion\redireccion;

include_once ('redireccionar.php');
if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class RegistradorOrden {

    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miFuncion;
    var $miSql;
    var $conexion;

    function __construct($lenguaje, $sql, $funcion) {
        $this->miConfigurador = \Configurador::singleton();
        $this->miConfigurador->fabricaConexiones->setRecursoDB('principal');
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miFuncion = $funcion;
    }

    function procesarFormulario() {


        $_REQUEST ['vigencia'] = date('Y');

        $fechaActual = date('Y-m-d');

        $fechaReinicio = date("Y-m-d", mktime(0, 0, 0, 1, 1, date('Y')));

        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

//         if ($fechaActual == $fechaReinicio) {

//             $cadenaSql = $this->miSql->getCadenaSql('consultaConsecutivo', $fechaReinicio);
//             $consecutivo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

//             if (isset($consecutivo) && $consecutivo == false) {
//                 $cadenaSql = $this->miSql->getCadenaSql('reiniciarConsecutivo');
//                 $consecutivo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso");
//             }
//         }

        
        
        
        $AnioActual = date ( 'Y' );
        $cadenaSql = $this->miSql->getCadenaSql ( 'consultaConsecutivo', $AnioActual );
        
        $consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
        
        if ($consecutivo == false) {
        		
        	$cadenaSql = $this->miSql->getCadenaSql ( 'reiniciarConsecutivo' );
        		
        	$consecutivo = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "acceso" );
        }
        
        
        
        
        
        
        $cadenaSql = $this->miSql->getCadenaSql('id_salida_maximo');

        $max_id_salida = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $max_id_salida = $max_id_salida [0] [0] + 1;

        $arreglo = array(
            $fechaActual,
            $_REQUEST ['dependencia'],
            $_REQUEST ['funcionario'],
            ($_REQUEST ['observaciones'] == '') ? "NULL" : "'" . $_REQUEST ['observaciones'] . "'",
            $_REQUEST ['numero_entrada'],
            $_REQUEST ['sede'],
            ($_REQUEST ['ubicacion'] != '') ? $_REQUEST ['ubicacion'] : 'null',
            $_REQUEST ['vigencia'],
            $max_id_salida
        );

        $cadenaSql = $this->miSql->getCadenaSql('insertar_salida', $arreglo);

        $id_salida = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo, "insertar_salida");
        $items = unserialize($_REQUEST ['items']);

        // Actualización de los funcionarios y ubicaciones de los elementos individuales.
        foreach ($items as $valor) {

            // Si es Diferente a Consumo, actualizar el elemento_individual
            //Encontrar información del elemento individual
        $cadenaSql = $this->miSql->getCadenaSql('busqueda_elementos_individuales', $valor['id_elemento']);
            $id_elem_ind = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

            //Si NO son consumo, entonces debe actualizar su información
            if ($id_elem_ind[0]['tipo_bien'] != 1) {

                $cadenaSql = $this->miSql->getCadenaSql('consultaNivel', $id_elem_ind [0] [0]);
                $nivel = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                if ($nivel [0] ['codigo_elemento'] == '3890' || $nivel [0] ['codigo_elemento'] == '3898') {

                    $arreglo = array(
                        'id_elemento' => $id_elem_ind [0] [0],
                        'id_salida' => $id_salida [0] [0],
                        'funcionario' => 899999230,
                        'ubicacion' => $_REQUEST ['ubicacion'],
                        'cantidad' => $valor['cantidad']
                    );
                } else {

                    $arreglo = array(
                        'id_elemento' => $id_elem_ind [0] [0],
                        'id_salida' => $id_salida [0] [0],
                        'funcionario' => $_REQUEST ['funcionario'],
                        'ubicacion' => $_REQUEST ['ubicacion'],
                        'cantidad' => $valor['cantidad']
                    );
                }

                //Aquí si debemos actualizar la información del elemento
                $cadenaSql = $this->miSql->getCadenaSql('actualizar_elementos_individuales', $arreglo);
                $actualizo_elem = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso", $arreglo, "actualizar_elementos_individuales");

                //Aquí se debe actualizar el elemento general
                $cadenaSql = $this->miSql->getCadenaSql('actualizar_elemento_general', $valor['id_elemento']);
                $actualizo_elem_gen = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso", $valor['id_elemento'], "actualizar_elemento_general");
            } else {

                //Si SI son consumo, entonces debe registrar el elemento individual
                $cadenaSql = $this->miSql->getCadenaSql('consultaNivel', $id_elem_ind [0] [0]);
                $nivel = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                $cadenaSql = $this->miSql->getCadenaSql('idElementoMaxIndividual');

                $elemento_id_max_indiv = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                $elemento_id_max_indiv = $elemento_id_max_indiv [0] [0] + 1;

                if ($nivel [0] ['codigo_elemento'] == '3890' || $nivel [0] ['codigo_elemento'] == '3898') {
                    $arreglo = array(
                        'id_elemento' => $elemento_id_max_indiv,
                        'fecha_registro' => $fechaActual,
                        //'placa', si es más de uno es porque es un consumo
                        //'serie', si es más de uno es porque es un consumo
                        'id_elemento_gen' => $valor ['id_elemento'],
                        //'estado_elemento',
                        'id_salida' => $id_salida [0] [0],
                        //estado_registro boolean DEFAULT true,
                        //observaciones_traslados character varying,
                        //estado_asignacion boolean DEFAULT false,
                        //fecha_asignacion date,
                        'ajuste_inflacionario' => 0,
                        'funcionario' => 899999230,
                        'ubicacion_elemento' => $_REQUEST ['ubicacion'],
                        //confirmada_existencia boolean DEFAULT false, -- Levantamiento
                        //tipo_confirmada integer DEFAULT 0, -- Levantamiento
                        'cantidad_asignada' => $valor['cantidad']
                    );
                } else {

                    $arreglo = array(
                        'id_elemento' => $elemento_id_max_indiv,
                        'fecha_registro' => $fechaActual,
                        //'placa', si es más de uno es porque es un consumo
                        //'serie', si es más de uno es porque es un consumo
                        'id_elemento_gen' => $valor ['id_elemento'],
                        // 'estado_elemento',
                        'id_salida' => $id_salida [0] [0],
                        //estado_registro boolean DEFAULT true,
                        //observaciones_traslados character varying,
                        //estado_asignacion boolean DEFAULT false,
                        //fecha_asignacion date,
                        'ajuste_inflacionario',
                        'funcionario' => $_REQUEST ['funcionario'],
                        'ubicacion_elemento' => $_REQUEST ['ubicacion'],
                        //confirmada_existencia boolean DEFAULT false, -- Levantamiento
                        //tipo_confirmada integer DEFAULT 0, -- Levantamiento
                        'cantidad_asignada' => $valor['cantidad']
                    );
                }

                $array = array(
                    'id_elemento' => $valor ['id_elemento'],
                    'cantidad' => $valor['cantidad']
                );

                $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_individual', $arreglo);
                $registro_elem = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso", $arreglo, "registrar_elementos_individuales");

                //Actualizar la cantidad de los elementos en la tabla de elementos individuales
                $cadenaSql = $this->miSql->getCadenaSql('actualizar_elemento_general_consumo', $array);
                $actualizar_elem_general = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso", $array, "actualizar_elemento_general_consumo");
            }
        }


//        // GENERAR LOS DATOS DE LA DEPRECIACIÓN PARA CADA ELEMENTO
//        // Consultar los elementos asociados a la salida
//        $cadenaSql = $this->miSql->getCadenaSql('elementos_salida', $id_salida [0] [0]);
//        $elementos_salida = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
//
//
//        foreach ($elementos_salida as $key => $values) {
//            // Consultar los datos para la generación
//            // Cálculos
//            $precio = $elementos_salida[$key]['total_iva_con'];
//            $vida_util = $elementos_salida[$key]['grupo_vidautil'];
//            $valor_cuota = round($precio / $vida_util, 4);
//
//            // Registro de la depreciación 
//            $cadena_depreciacion = array(
//                'id_elemento_ind' => $elementos_salida[$key]['id_elemento_ind'],
//                'id_salida' => $elementos_salida[$key]['id_salida'],
//                'fecha_salida' => $elementos_salida[$key]['fecha_salida'],
//                'grupo_contable' => $elementos_salida[$key]['grupo_id'],
//                'vida_util' => $elementos_salida[$key]['grupo_vidautil'],
//                'valor' => $elementos_salida[$key]['total_iva_con'],
//                'valor_cuota' => $valor_cuota,
//            );
//
//            $cadenaSql = $this->miSql->getCadenaSql('registro_detalle_depreciacion', $cadena_depreciacion);
//            $registro_data_depreciacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "insertar");
//        }

        // ----- Salidas Contables ----------

        $cadenaSql = $this->miSql->getCadenaSql('busqueda_elementos_bienes', $id_salida [0] [0]);
        $elementos_tipo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        foreach ($elementos_tipo as $elemento) {

            switch ($elemento ['tipo_bien']) {
                case '1' :
                    $arreglo = array(
                        $_REQUEST ['vigencia'],
                        $elemento ['tipo_bien']
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('SalidaContableVigencia', $arreglo);

                    $max_consecutivo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                    if (is_null($max_consecutivo [0] [0])) {

                        $salidaConsecutiva = 1;
                    } else {

                        $salidaConsecutiva = $max_consecutivo [0] [0] + 1;
                    }

                    $arreglo_salida_contable = array(
                        $fechaActual,
                        $id_salida [0] [0],
                        $elemento ['tipo_bien'],
                        $_REQUEST ['vigencia'],
                        $salidaConsecutiva
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('InsertarSalidaContable', $arreglo_salida_contable);

                    $id_salida_contable = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo_salida_contable, "InsertarSalidaContable");

                    break;

                case '2' :

                    $arreglo = array(
                        $_REQUEST ['vigencia'],
                        $elemento ['tipo_bien']
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('SalidaContableVigencia', $arreglo);
                    
                    $max_consecutivo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                    if (is_null($max_consecutivo [0] [0])) {

                        $salidaConsecutiva = 1;
                    } else {

                        $salidaConsecutiva = $max_consecutivo [0] [0] + 1;
                    }

                    $arreglo_salida_contable = array(
                        $fechaActual,
                        $id_salida [0] [0],
                        $elemento ['tipo_bien'],
                        $_REQUEST ['vigencia'],
                        $salidaConsecutiva
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('InsertarSalidaContable', $arreglo_salida_contable);

                    $id_salida_contable = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo_salida_contable, "InsertarSalidaContable");

                    break;

                case '3' :

                    $arreglo = array(
                        $_REQUEST ['vigencia'],
                        $elemento ['tipo_bien']
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('SalidaContableVigencia', $arreglo);
                    
                    $max_consecutivo = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                    if (is_null($max_consecutivo [0] [0])) {

                        $salidaConsecutiva = 1;
                    } else {

                        $salidaConsecutiva = $max_consecutivo [0] [0] + 1;
                    }

                    $arreglo_salida_contable = array(
                        $fechaActual,
                        $id_salida [0] [0],
                        $elemento ['tipo_bien'],
                        $_REQUEST ['vigencia'],
                        $salidaConsecutiva
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('InsertarSalidaContable', $arreglo_salida_contable);

                    $id_salida_contable = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo_salida_contable, "InsertarSalidaContable");

                    break;
            }
        }

        $arreglo = array(
            "salida" => $id_salida [0] [1],
            "entrada" => $_REQUEST ['numero_entrada'],
            "fecha" => $fechaActual
        );

        if ($actualizo_elem = true) {

            $this->miConfigurador->setVariableConfiguracion("cache", true);
            redireccion::redireccionar('inserto', $arreglo);
            exit();
        } else {
            redireccion::redireccionar('noInserto');
            exit();
        }
    }

    function resetForm() {
        foreach ($_REQUEST as $clave => $valor) {

            if ($clave != 'pagina' && $clave != 'development' && $clave != 'jquery' && $clave != 'tiempo') {
                unset($_REQUEST [$clave]);
            }
        }
    }

}

$miRegistrador = new RegistradorOrden($this->lenguaje, $this->sql, $this->funcion);

$resultado = $miRegistrador->procesarFormulario();
?>