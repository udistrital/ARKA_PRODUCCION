<?php

/* ----------------------------------------------------------------------------------------
  |				Control Versiones				       	   |
  -----------------------------------------------------------------------------------------
  | fecha      |        Autor            | version     |              Detalle              |
  -----------------------------------------------------------------------------------------
  |       2014 | Stiv Verdugo            | 0.0.0.1     |                                   |
  -----------------------------------------------------------------------------------------
  | 01/08/2015 | Stiv Verdugo            | 0.0.0.2     |                                   |
  -----------------------------------------------------------------------------------------
  | 15/10/2015 | Violet Sosa             | 0.0.0.3     | Registro único individual consumo |
  -----------------------------------------------------------------------------------------
 */

namespace inventarios\gestionElementos\registrarElemento\funcion;

use inventarios\gestionElementos\registrarElemento\funcion\redireccion;

include_once ('redireccionar.php');

$ruta_1 = $this->miConfigurador->getVariableConfiguracion('raizDocumento') . '/plugin/php_excel/Classes/PHPExcel.class.php';
$ruta_2 = $this->miConfigurador->getVariableConfiguracion('raizDocumento') . '/plugin/php_excel/Classes/PHPExcel/Reader/Excel2007.class.php';

include_once ($ruta_1);
include_once ($ruta_2);

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
        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $cadenaSql = $this->miSql->getCadenaSql('consultar_entrada_acta', $_REQUEST ['entrada']);
        $acta = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $acta = $acta [0] [0];

        $cadenaSql = $this->miSql->getCadenaSql('consultar_elementos_acta', $acta);
        $elementos_acta = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $numero_elementos_acta = count($elementos_acta);

        $cadenaSql = $this->miSql->getCadenaSql('consultar_elementos_entrada', $_REQUEST ['entrada']);
        $elementos_entrada = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $numero_elementos_entrada = count($elementos_entrada);

        // if($numero_elementos_acta==$numero_elementos_entrada){
        // redireccion::redireccionar ( 'noCargarElemento' );
        // }

        $fechaActual = date('Y-m-d');

        $_REQUEST ['bodega'] = 0;

        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/inventarios/gestionElementos/";
        $rutaBloque .= $esteBloque ['nombre'];
        $host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/inventarios/gestionEntradas/" . $esteBloque ['nombre'];


        $_REQUEST ['valor'] = round($_REQUEST ['valor'], 2);
        $_REQUEST ['subtotal_sin_iva'] = round($_REQUEST ['subtotal_sin_iva'], 2);
        $_REQUEST ['total_iva'] = round($_REQUEST ['total_iva'], 2);
        $_REQUEST ['total_iva_con'] = round($_REQUEST ['total_iva_con'], 2);

        $cadenaSql = $this->miSql->getCadenaSql('idElementoMax');

        $elemento_id_max = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $elemento_id_max = $elemento_id_max [0] [0] + 1;

        switch ($_REQUEST ['tipo_registro']) {

            case '1' :

                if ($_REQUEST ['id_tipo_bien'] == 1) {

                    $cadenaSql = $this->miSql->getCadenaSql('consultar_placa', '1');
                    $placa = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                    $arreglo = array(
                        $fechaActual,
                        $_REQUEST ['nivel'],
                        $_REQUEST ['id_tipo_bien'],
                        $_REQUEST ['descripcion'],
                        //Acontinuación se modifica para crear un único registro
                        $_REQUEST ['cantidad'],
                        $_REQUEST ['unidad'],
                        $_REQUEST ['valor'],
                        $_REQUEST ['iva'],
                        $_REQUEST ['ajuste'] = 0,
                        $_REQUEST ['bodega'],
                        $_REQUEST ['subtotal_sin_iva'],
                        $_REQUEST ['total_iva'],
                        $_REQUEST ['total_iva_con'],
                        ($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : 'null',
                        ($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : 'null',
                        $_REQUEST ['entrada'],
                        $elemento_id_max
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_tipo_1', $arreglo);

                    $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo, "ingresar_elemento_tipo_1");
                } else if ($_REQUEST ['id_tipo_bien'] == 2) {

                    $arreglo = array(
                        $fechaActual,
                        $_REQUEST ['nivel'],
                        $_REQUEST ['id_tipo_bien'],
                        $_REQUEST ['descripcion'],
                        $_REQUEST ['cantidad'] = 1,
                        $_REQUEST ['unidad'],
                        $_REQUEST ['valor'],
                        $_REQUEST ['iva'],
                        $_REQUEST ['ajuste'] = 0,
                        $_REQUEST ['bodega'],
                        $_REQUEST ['subtotal_sin_iva'],
                        $_REQUEST ['total_iva'],
                        $_REQUEST ['total_iva_con'],
                        ($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : 'null',
                        ($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : 'null',
                        $_REQUEST ['entrada'],
                        $elemento_id_max
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_tipo_1', $arreglo);

                    $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo, "ingresar_elemento_tipo_1");
                } else if ($_REQUEST ['id_tipo_bien'] == 3) {

                    if ($_REQUEST ['tipo_poliza'] == 0) {
                        $arreglo = array(
                            $fechaActual,
                            $_REQUEST ['nivel'],
                            $_REQUEST ['id_tipo_bien'],
                            $_REQUEST ['descripcion'],
                            $_REQUEST ['cantidad'] = 1,
                            $_REQUEST ['unidad'],
                            $_REQUEST ['valor'],
                            $_REQUEST ['iva'],
                            $_REQUEST ['ajuste'] = 0,
                            $_REQUEST ['bodega'],
                            $_REQUEST ['subtotal_sin_iva'],
                            $_REQUEST ['total_iva'],
                            $_REQUEST ['total_iva_con'],
                            $_REQUEST ['tipo_poliza'],
                            '0001-01-01',
                            '0001-01-01',
                            ($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : 'null',
                            ($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : 'null',
                            $_REQUEST ['entrada'],
                            $elemento_id_max
                        );
                    } else if ($_REQUEST ['tipo_poliza'] == 1) {
                        $arreglo = array(
                            $fechaActual,
                            $_REQUEST ['nivel'],
                            $_REQUEST ['id_tipo_bien'],
                            $_REQUEST ['descripcion'],
                            $_REQUEST ['cantidad'] = 1,
                            $_REQUEST ['unidad'],
                            $_REQUEST ['valor'],
                            $_REQUEST ['iva'],
                            $_REQUEST ['ajuste'] = 0,
                            $_REQUEST ['bodega'],
                            $_REQUEST ['subtotal_sin_iva'],
                            $_REQUEST ['total_iva'],
                            $_REQUEST ['total_iva_con'],
                            $_REQUEST ['tipo_poliza'],
                            $_REQUEST ['fecha_inicio'],
                            $_REQUEST ['fecha_final'],
                            ($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : 'null',
                            ($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : 'null',
                            $_REQUEST ['entrada'],
                            $elemento_id_max
                        );
                    }

                    $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_tipo_2', $arreglo);

                    $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo, "ingresar_elemento_tipo_2");
                }

                //Revisando las placas después de consultar el elemento general
                $placa = date('Ymd') . "00000";

                $cadenaSql = $this->miSql->getCadenaSql('buscar_repetida_placa', $placa);
                $num_placa = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                $cadenaSql = $this->miSql->getCadenaSql('idElementoMaxIndividual');
                $elemento_id_max_indiv = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                $elemento_id_max_indiv = $elemento_id_max_indiv [0] [0] + 1;

                $sumaplaca = 0;

                if ($_REQUEST ['id_tipo_bien'] != 1) {
                    if ($num_placa [0] [0] == 0) {
//AQUI FUE MODIFICADO PARA SER UNICA CANTIDAD
                        $arregloElementosInv = array(
                            $fechaActual,
                            ($_REQUEST ['id_tipo_bien'] == 1) ? NULL : $placa + $sumaplaca,
                            ($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : null,
                            $elemento [0] [0],
                            $elemento_id_max_indiv
                        );

                        $sumaplaca = ($_REQUEST ['id_tipo_bien'] == 1) ? $sumaplaca : $sumaplaca ++;

                        $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_individual', $arregloElementosInv);
                        $elemento_id = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arregloElementosInv, "ingresar_elemento_individual");
                        $elemento_id_max_indiv = $elemento_id_max_indiv + 1;
                    } else if ($num_placa [0] [0] != 0) {

                        $cadenaSql = $this->miSql->getCadenaSql('buscar_placa_maxima', $placa);

                        $num_placa = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                        $placa = $num_placa [0] [0];
                        $sumaplaca = 1;

                        //AQUI FUE MODIFICADO PARA SER UNICA CANTIDAD
                        $arregloElementosInv = array(
                            $fechaActual,
                            ($_REQUEST ['id_tipo_bien'] == 1) ? NULL : $placa + $sumaplaca,
                            ($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : null,
                            $elemento [0] [0],
                            $elemento_id_max_indiv
                        );

                        $sumaplaca = ($_REQUEST ['id_tipo_bien'] == 1) ? $sumaplaca : $sumaplaca ++;
                        $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_individual', $arregloElementosInv);
                        $elemento_id = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arregloElementosInv, "ingresar_elemento_individual");
                        $elemento_id_max_indiv = $elemento_id_max_indiv + 1;
                    }
                }

                foreach ($_FILES as $key => $values) {

                    $archivo = $_FILES [$key];
                }

                if ($archivo ['type'] == 'image/jpeg') {

                    $data = base64_encode(file_get_contents($archivo ['tmp_name']));
                    // Prioridad 1 ('Principal') 0 ('No Principal')
                    $arreglo = array(
                        "elemento" => $elemento_id_max,
                        "prioridad" => "1",
                        "imagen" => $data
                    );

                    $cadenaSql = $this->miSql->getCadenaSql('ElementoImagen', $arreglo);

                    $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo, "ElementoImagen");
                }

                $datos = array(
                    $elemento [0] [0],
                    $fechaActual,
                    $_REQUEST ['entrada']
                );

                if ($elemento) {
                    $this->miConfigurador->setVariableConfiguracion("cache", true);
                    redireccion::redireccionar('inserto', $datos, $_REQUEST ['datosGenerales']);
                    exit();
                } else {

                    redireccion::redireccionar('noInserto', $datos);

                    exit();
                }

                break;
            case '2' :
                $ingreso = 0;

                $ruta_eliminar_xlsx = $rutaBloque . "/archivo/*.xlsx";

                $ruta_eliminar_xls = $rutaBloque . "/archivo/*.xls";

                foreach (glob($ruta_eliminar_xlsx) as $filename) {
                    unlink($filename);
                }
                foreach (glob($ruta_eliminar_xls) as $filename) {
                    unlink($filename);
                }

                $i = 0;
                foreach ($_FILES as $key => $values) {

                    $archivo [$i] = $_FILES [$key];
                    $i ++;
                }

                $archivo = $archivo [0];

                $trozos = explode(".", $archivo ['name']);
                $extension = end($trozos);

                if ($extension == 'xlsx' || $extension == 'xls') {

                    if ($archivo) {
                        // obtenemos los datos del archivo
                        $tamano = $archivo ['size'];
                        $tipo = $archivo ['type'];
                        $archivo1 = $archivo ['name'];
                        $prefijo = "archivo";

                        if ($archivo1 != "") {
                            // guardamos el archivo a la carpeta files
                            $ruta_absoluta = $rutaBloque . "/archivo/" . $prefijo . "_" . $archivo1;

                            if (copy($archivo ['tmp_name'], $ruta_absoluta)) {
                                $status = "Archivo subido: <b>" . $archivo1 . "</b>";
                                $destino1 = $host . "/archivo/" . $prefijo . "_" . $archivo1;
                            } else {
                                $status = "Error al subir el archivo";
                                echo $status;
                            }
                        } else {
                            $status = "Error al subir archivo";
                            echo $status;
                        }
                    }

                    $arreglo = array(
                        $destino1,
                        $archivo1
                    );

                    if (file_exists($ruta_absoluta)) {

                        // Cargando la hoja de cálculo

                        $objReader = new \PHPExcel_Reader_Excel2007 ();

                        $objPHPExcel = $objReader->load($ruta_absoluta);

                        $objFecha = new \PHPExcel_Shared_Date ();

                        // Asignar hoja de excel activa

                        $objPHPExcel->setActiveSheetIndex(0);

                        $objWorksheet = $objPHPExcel->setActiveSheetIndex(0);

                        $highestRow = $objWorksheet->getHighestRow();

                        for ($i = 2; $i <= $highestRow; $i ++) {

                            $datos [$i] ['Nivel'] = $objPHPExcel->getActiveSheet()->getCell('A' . $i)->getCalculatedValue();

                            if (is_null($datos [$i] ['Nivel']) == true) {

                                redireccion::redireccionar('datosVacios', $fechaActual);
                                exit();
                            }

                            $datos [$i] ['Tipo_Bien'] = $objPHPExcel->getActiveSheet()->getCell('B' . $i)->getCalculatedValue();

                            if (is_null($datos [$i] ['Tipo_Bien']) == true) {

                                redireccion::redireccionar('datosVacios', $fechaActual);
                                exit();
                            }

                            $datos [$i] ['Descripcion'] = $objPHPExcel->getActiveSheet()->getCell('C' . $i)->getCalculatedValue();

                            if (is_null($datos [$i] ['Descripcion']) == true) {

                                redireccion::redireccionar('datosVacios', $fechaActual);
                                exit();
                            }
                            $datos [$i] ['Cantidad'] = $objPHPExcel->getActiveSheet()->getCell('D' . $i)->getCalculatedValue();

                            if (is_null($datos [$i] ['Cantidad']) == true) {

                                redireccion::redireccionar('datosVacios', $fechaActual);
                                exit();
                            }
                            $datos [$i] ['Unidad_Medida'] = $objPHPExcel->getActiveSheet()->getCell('E' . $i)->getCalculatedValue();

                            if (is_null($datos [$i] ['Unidad_Medida']) == true) {

                                redireccion::redireccionar('datosVacios', $fechaActual);
                                exit();
                            }

                            $datos [$i] ['Valor_Precio'] = $objPHPExcel->getActiveSheet()->getCell('F' . $i)->getCalculatedValue();

                            if (is_null($datos [$i] ['Valor_Precio']) == true) {

                                redireccion::redireccionar('datosVacios', $fechaActual);
                                exit();
                            }
                            $datos [$i] ['Iva'] = $objPHPExcel->getActiveSheet()->getCell('G' . $i)->getCalculatedValue();

                            if (is_null($datos [$i] ['Iva']) == true) {

                                redireccion::redireccionar('datosVacios', $fechaActual);
                                exit();
                            }

                            $datos [$i] ['Tipo_poliza'] = $objPHPExcel->getActiveSheet()->getCell('H' . $i)->getCalculatedValue();

                            $datos [$i] ['Fecha_Inicio_Poliza_Anio'] = $objPHPExcel->getActiveSheet()->getCell('I' . $i)->getCalculatedValue();

                            $datos [$i] ['Fecha_Inicio_Poliza_Mes'] = $objPHPExcel->getActiveSheet()->getCell('J' . $i)->getCalculatedValue();

                            $datos [$i] ['Fecha_Inicio_Poliza_Dia'] = $objPHPExcel->getActiveSheet()->getCell('K' . $i)->getCalculatedValue();

                            $datos [$i] ['Fecha_Final_Poliza_Anio'] = $objPHPExcel->getActiveSheet()->getCell('L' . $i)->getCalculatedValue();

                            $datos [$i] ['Fecha_Final_Poliza_Mes'] = $objPHPExcel->getActiveSheet()->getCell('M' . $i)->getCalculatedValue();

                            $datos [$i] ['Fecha_Final_Poliza_Dia'] = $objPHPExcel->getActiveSheet()->getCell('N' . $i)->getCalculatedValue();

                            $datos [$i] ['Marca'] = $objPHPExcel->getActiveSheet()->getCell('O' . $i)->getCalculatedValue();

                            $datos [$i] ['Serie'] = $objPHPExcel->getActiveSheet()->getCell('P' . $i)->getCalculatedValue();
                        }

                        for ($i = 2; $i <= $highestRow; $i ++) {

                            switch ($datos [$i] ['Iva']) {

                                case "1" :

                                    $IVA = 0;

                                    break;

                                case "2" :

                                    $IVA = 0;

                                    break;

                                case "3" :

                                    $IVA = 0.05;

                                    break;

                                case "4" :

                                    $IVA = 0.04;

                                    break;

                                case "5" :

                                    $IVA = 0.10;

                                    break;

                                case "6" :

                                    $IVA = 0.16;

                                    break;
                            }

                            $cadenaSql = $this->miSql->getCadenaSql('idElementoMax');

                            $elemento_id_max = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                            $elemento_id_max = $elemento_id_max [0] [0] + 1;

                            // $arreglo = array (
                            // $fechaActual,
                            // $datos [$i] ['Nivel'],
                            // $datos [$i] ['Tipo_Bien'],
                            // trim ( $datos [$i] ['Descripcion'], "'" ),
                            // $datos [$i] ['Cantidad'],
                            // trim ( $datos [$i] ['Unidad_Medida'], "'" ),
                            // $datos [$i] ['Valor_Precio'],
                            // $datos [$i] ['Ajuste'],
                            // $datos [$i] ['Bodega'],
                            // $datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio'],
                            // $datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio'] * $datos [$i] ['Iva'],
                            // round ( $datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio'] * $datos [$i] ['Iva'] ) + ($datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio']),
                            // $datos [$i] ['Tipo_poliza'],
                            // trim ( $datos [$i] ['Fecha_Inicio_Poliza'], "'" ),
                            // trim ( $datos [$i] ['Fecha_Final_Poliza'], "'" ),
                            // trim ( $datos [$i] ['Marca'], "'" ),
                            // trim ( $datos [$i] ['Serie'], "'" ),
                            // $datos [$i] ['Entrada']
                            // );

                            if ($datos [$i] ['Tipo_Bien'] == 1) {


                                // "1";0;"Exento";
                                // "2";0;"Tarifa de Cero";
                                // "3";0.05;"5%";
                                // "4";0.04;"4%";
                                // "5";0.1;"10%";
                                // "6";0.16;"16%";

                                $arreglo = array(
                                    $fechaActual,
                                    $datos [$i] ['Nivel'],
                                    $datos [$i] ['Tipo_Bien'],
                                    trim($datos [$i] ['Descripcion'], "'"),
                                    $datos [$i] ['Cantidad'],
                                    trim($datos [$i] ['Unidad_Medida'], "'"),
                                    $datos [$i] ['Valor_Precio'],
                                    $datos [$i] ['Iva'],
                                    $_REQUEST ['ajuste'] = 0,
                                    $_REQUEST ['bodega'],
                                    $datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio'],
                                    $datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio'] * $IVA,
                                    ($datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio'] * $IVA ) + ($datos [$i] ['Cantidad'] * $datos [$i] ['Valor_Precio']),
                                    (is_null($datos [$i] ['Marca']) == true) ? 'null' : trim($datos [$i] ['Marca'], "'"),
                                    (is_null($datos [$i] ['Serie']) == true) ? 'null' : trim($datos [$i] ['Serie'], "'"),
                                    $_REQUEST ['entrada'],
                                    $elemento_id_max
                                );

                                $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_tipo_1', $arreglo);
                                $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo, "ingresar_elemento_tipo_1");
                            } else if ($datos [$i] ['Tipo_Bien'] == 2) {
                                $datos [$i] ['Cantidad'] = 1;

                                // $arreglo = array (
                                // $fechaActual,
                                // $_REQUEST ['nivel'],
                                // $_REQUEST ['id_tipo_bien'],
                                // $_REQUEST ['descripcion'],
                                // $_REQUEST ['cantidad'] = 1,
                                // $_REQUEST ['unidad'],
                                // $_REQUEST ['valor'],
                                // $_REQUEST ['iva'],
                                // $_REQUEST ['ajuste'] = 0,
                                // $_REQUEST ['bodega'],
                                // $_REQUEST ['subtotal_sin_iva'],
                                // $_REQUEST ['total_iva'],
                                // $_REQUEST ['total_iva_con'],
                                // ($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : 'null',
                                // ($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : 'null',
                                // $_REQUEST ['entrada'],
                                // $elemento_id_max
                                // );

                                $arreglo = array(
                                    $fechaActual,
                                    $datos [$i] ['Nivel'],
                                    $datos [$i] ['Tipo_Bien'],
                                    trim($datos [$i] ['Descripcion'], "'"),
                                    1,
                                    trim($datos [$i] ['Unidad_Medida'], "'"),
                                    $datos [$i] ['Valor_Precio'],
                                    $datos [$i] ['Iva'],
                                    $_REQUEST ['ajuste'] = 0,
                                    $_REQUEST ['bodega'],
                                    1 * $datos [$i] ['Valor_Precio'],
                                    1 * $datos [$i] ['Valor_Precio'] * $IVA,
                                    ( 1 * $datos [$i] ['Valor_Precio'] * $IVA ) + (1 * $datos [$i] ['Valor_Precio']),
                                    (is_null($datos [$i] ['Marca']) == true) ? 'null' : trim($datos [$i] ['Marca'], "'"),
                                    (is_null($datos [$i] ['Serie']) == true) ? 'null' : trim($datos [$i] ['Serie'], "'"),
                                    $_REQUEST ['entrada'],
                                    $elemento_id_max
                                );

                                $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_tipo_1', $arreglo);

                                $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo, "ingresar_elemento_tipo_1");
                            } else if ($datos [$i] ['Tipo_Bien'] == 3) {

                                $datos [$i] ['Cantidad'] = 1;
                                if ($datos [$i] ['Tipo_poliza'] == 0) {

                                    $arreglo = array(
                                        $fechaActual,
                                        $datos [$i] ['Nivel'],
                                        $datos [$i] ['Tipo_Bien'],
                                        trim($datos [$i] ['Descripcion'], "'"),
                                        1,
                                        trim($datos [$i] ['Unidad_Medida'], "'"),
                                        $datos [$i] ['Valor_Precio'],
                                        $datos [$i] ['Iva'],
                                        $_REQUEST ['ajuste'] = 0,
                                        $_REQUEST ['bodega'],
                                        1 * $datos [$i] ['Valor_Precio'],
                                        1 * $datos [$i] ['Valor_Precio'] * $IVA,
                                        ( 1 * $datos [$i] ['Valor_Precio'] * $IVA ) + (1 * $datos [$i] ['Valor_Precio']),
                                        $datos [$i] ['Tipo_poliza'],
                                        'NULL',
                                        'NULL',
                                        (is_null($datos [$i] ['Marca']) == true) ? 'null' : trim($datos [$i] ['Marca'], "'"),
                                        (is_null($datos [$i] ['Serie']) == true) ? 'null' : trim($datos [$i] ['Serie'], "'"),
                                        $_REQUEST ['entrada'],
                                        $elemento_id_max
                                    );

                                    // $arreglo = array (
                                    // $fechaActual,
                                    // $_REQUEST ['nivel'],
                                    // $_REQUEST ['id_tipo_bien'],
                                    // $_REQUEST ['descripcion'],
                                    // $_REQUEST ['cantidad'] = 1,
                                    // $_REQUEST ['unidad'],
                                    // $_REQUEST ['valor'],
                                    // $_REQUEST ['iva'],
                                    // $_REQUEST ['ajuste'] = 0,
                                    // $_REQUEST ['bodega'],
                                    // $_REQUEST ['subtotal_sin_iva'],
                                    // $_REQUEST ['total_iva'],
                                    // $_REQUEST ['total_iva_con'],
                                    // $_REQUEST ['tipo_poliza'],
                                    // 'NULL',
                                    // 'NULL',
                                    // ($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : 'null',
                                    // ($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : 'null',
                                    // $_REQUEST ['entrada'],
                                    // $elemento_id_max
                                    // );
                                } else if ($datos [$i] ['Tipo_poliza'] == 1) {

                                    $arreglo = array(
                                        $fechaActual,
                                        $datos [$i] ['Nivel'],
                                        $datos [$i] ['Tipo_Bien'],
                                        trim($datos [$i] ['Descripcion'], "'"),
                                        1,
                                        trim($datos [$i] ['Unidad_Medida'], "'"),
                                        $datos [$i] ['Valor_Precio'],
                                        $datos [$i] ['Iva'],
                                        $_REQUEST ['ajuste'] = 0,
                                        $_REQUEST ['bodega'],
                                        1 * $datos [$i] ['Valor_Precio'],
                                        1 * $datos [$i] ['Valor_Precio'] * $IVA,
                                        ( 1 * $datos [$i] ['Valor_Precio'] * $IVA ) + (1 * $datos [$i] ['Valor_Precio']),
                                        $datos [$i] ['Tipo_poliza'],
                                        $datos [$i] ['Fecha_Inicio_Poliza_Anio'] . "-" . $datos [$i] ['Fecha_Inicio_Poliza_Mes'] . "-" . $datos [$i] ['Fecha_Inicio_Poliza_Dia'],
                                        $datos [$i] ['Fecha_Final_Poliza_Anio'] . "-" . $datos [$i] ['Fecha_Final_Poliza_Mes'] . "-" . $datos [$i] ['Fecha_Final_Poliza_Dia'],
                                        (is_null($datos [$i] ['Marca']) == true) ? 'null' : trim($datos [$i] ['Marca'], "'"),
                                        (is_null($datos [$i] ['Serie']) == true) ? 'null' : trim($datos [$i] ['Serie'], "'"),
                                        $_REQUEST ['entrada'],
                                        $elemento_id_max
                                    );

                                    // $arreglo = array (
                                    // $fechaActual,
                                    // $_REQUEST ['nivel'],
                                    // $_REQUEST ['id_tipo_bien'],
                                    // $_REQUEST ['descripcion'],
                                    // $_REQUEST ['cantidad'] = 1,
                                    // $_REQUEST ['unidad'],
                                    // $_REQUEST ['valor'],
                                    // $_REQUEST ['iva'],
                                    // $_REQUEST ['ajuste'] = 0,
                                    // $_REQUEST ['bodega'],
                                    // $_REQUEST ['subtotal_sin_iva'],
                                    // $_REQUEST ['total_iva'],
                                    // $_REQUEST ['total_iva_con'],
                                    // $_REQUEST ['tipo_poliza'],
                                    // $_REQUEST ['fecha_inicio'],
                                    // $_REQUEST ['fecha_final'],
                                    // ($_REQUEST ['marca'] != '') ? $_REQUEST ['marca'] : 'null',
                                    // ($_REQUEST ['serie'] != '') ? $_REQUEST ['serie'] : 'null',
                                    // $_REQUEST ['entrada'],
                                    // $elemento_id_max
                                    // );
                                }

                                $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_tipo_2', $arreglo);
                                $elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arreglo, "ingresar_elemento_tipo_2");
                            }

                            // $cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_masivo', $arreglo );
                            // $elemento = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
                            // $_REQUEST ['cantidad'] = $datos [$i] ['Cantidad'];
                            // $_REQUEST ['serie'] = trim ( $datos [$i] ['Serie'], "'" );
                            // $ingreso = 1;

                            $placa = date('Ymd') . "00000";

                            $cadenaSql = $this->miSql->getCadenaSql('buscar_repetida_placa', $placa);
                            $num_placa = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                            $cadenaSql = $this->miSql->getCadenaSql('idElementoMaxIndividual');
                            $elemento_id_max_indiv = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
                            $elemento_id_max_indiv = $elemento_id_max_indiv [0] [0] + 1;

                            $sumaplaca = 0;


                            //------------------AQUI EMPIEZAN A VERSE LAS CANTIDADES!!!!!--------------------------//
                            switch ($datos [$i] ['Tipo_Bien']) {

                                case '1' :
                                    //A continuación se modificó para obtener un único registro
                                    $_REQUEST ['cantidad'] = 1;

                                    break;
                                case '2' :
                                    $_REQUEST ['cantidad'] = 1;

                                    break;
                                case '3' :
                                    $_REQUEST ['cantidad'] = 1;

                                    break;
                            }

                            if ($datos [$i] ['Tipo_Bien'] != 1) {
                                if ($num_placa [0] [0] == 0) {

                                    for ($j = 0; $j < $_REQUEST ['cantidad']; $j ++) {
                                        $arregloElementosInv = array(
                                            $fechaActual,
                                            ($datos [$i] ['Tipo_Bien'] == 1) ? NULL : $placa + $sumaplaca,
                                            (is_null($datos [$i] ['Serie']) == true) ? 'null' : trim($datos [$i] ['Serie'], "'"),
                                            $elemento [0] [0],
                                            $elemento_id_max_indiv
                                        );

                                        $sumaplaca = ($datos [$i] ['Tipo_Bien'] == 1) ? $sumaplaca : $sumaplaca ++;

                                        $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_individual', $arregloElementosInv);

                                        $elemento_id [$j] = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arregloElementosInv, "ingresar_elemento_individual");

                                        $elemento_id_max_indiv = $elemento_id_max_indiv + 1;
                                    }
                                } else if ($num_placa [0] [0] != 0) {

                                    $cadenaSql = $this->miSql->getCadenaSql('buscar_placa_maxima', $placa);

                                    $num_placa = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                                    $placa = $num_placa [0] [0];
                                    $sumaplaca = 1;

                                    for ($j = 1; $j <= $_REQUEST ['cantidad']; $j ++) {
                                        $arregloElementosInv = array(
                                            $fechaActual,
                                            ($datos [$i] ['Tipo_Bien'] == 1) ? NULL : $placa + $sumaplaca,
                                            (is_null($datos [$i] ['Serie']) == true) ? 'null' : trim($datos [$i] ['Serie'], "'"),
                                            $elemento [0] [0],
                                            $elemento_id_max_indiv
                                        );

                                        $sumaplaca = ($datos [$i] ['Tipo_Bien'] == 1) ? $sumaplaca : $sumaplaca ++;

                                        $cadenaSql = $this->miSql->getCadenaSql('ingresar_elemento_individual', $arregloElementosInv);

                                        $elemento_id [$j] = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda", $arregloElementosInv, "ingresar_elemento_individual");

                                        $elemento_id_max_indiv = $elemento_id_max_indiv + 1;
                                    }
                                }
                            }
                            // $cadenaSql = $this->miSql->getCadenaSql ( 'buscar_repetida_placa', $placa );
                            // $num_placa = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
                            // if ($num_placa [0] [0] == 0) {
                            // for($i = 0; $i < $_REQUEST ['cantidad']; $i ++) {
                            // $arregloElementosInv = array (
                            // $fechaActual,
                            // $placa + $i,
                            // $_REQUEST ['serie'],
                            // $elemento [0] [0]
                            // );
                            // $cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_individual', $arregloElementosInv );
                            // $elemento_id [$i] = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
                            // }
                            // } else if ($num_placa [0] [0] != 0) {
                            // $cadenaSql = $this->miSql->getCadenaSql ( 'buscar_placa_maxima', $placa );
                            // $num_placa = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
                            // $placa = $num_placa [0] [0];
                            // for($i = 1; $i <= $_REQUEST ['cantidad']; $i ++) {
                            // $arregloElementosInv = array (
                            // $fechaActual,
                            // $placa + $i,
                            // $_REQUEST ['serie'],
                            // $elemento [0] [0]
                            // );
                            // $cadenaSql = $this->miSql->getCadenaSql ( 'ingresar_elemento_individual', $arregloElementosInv );
                            // $elemento_id [$i] = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
                            // }
                            // }
                        }

                        if ($elemento) {
                            $this->miConfigurador->setVariableConfiguracion("cache", true);
                            redireccion::redireccionar('inserto_M', $fechaActual);
                            exit();
                        } else {

                            redireccion::redireccionar('noInserto', $datos);
                            exit();
                        }
                    }
                } else {

                    redireccion::redireccionar('noExtension');
                }

                break;
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