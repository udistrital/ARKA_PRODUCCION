<?php

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

/*
 * To change this license header, choose License Headers in Project Properties. To change this template file, choose Tools | Templates and open the template in the editor.
 */


$esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

$rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/inventarios/";
$rutaBloque .= $esteBloque ['nombre'];
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/inventarios/" . $esteBloque ['nombre'];

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

$resultado = '';


// Guardar el archivo
if ($_FILES) {
    foreach ($_FILES as $key => $values) {
        $archivo = $_FILES [$key];
    }
    // obtenemos los datos del archivo
    $tamano = $archivo ['size'];
    $tipo = $archivo ['type'];
    $archivo1 = $archivo ['name'];
    $prefijo = substr(md5(uniqid(rand())), 0, 6);

    if ($archivo1 != "") {
        // guardamos el archivo a la carpeta files
        $destino1 = $rutaBloque . "/archivoSoporte/" . $prefijo . "-" . $archivo1;

        if (copy($archivo ['tmp_name'], $destino1)) {
            $status = "Archivo subido: <b>" . $archivo1 . "</b>";
            $destino1 = $host . "/archivoSoporte/" . $prefijo . "-" . $archivo1;

            $parametros = array(
                'nombre_archivo' => $archivo1,
                'id_unico' => $prefijo . "-" . $archivo1,
                'fecha_registro' => date('d/m/Y'),
                'ruta' => $destino1,
                'estado' => TRUE,
                'id_doc' => $_REQUEST ['identificador_documento']
            );

            $cadenaSql = $this->sql->cadena_sql("actualizarDocumento", $parametros);
            $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'acceso');
        } else {
            $status = "<br>Error al subir el archivo1";
        }
    } else {
        $status = "<br>Error al subir archivo2";
    }
}



$arreglo = array(
    $_REQUEST ['id_contratista'],
    $_REQUEST ['num_contrato'],
    $_REQUEST ['fecha_contrato'],
    $_REQUEST ['identificador_contrato']
);

$cadenaSql = $this->sql->cadena_sql("actualizarContrato", $arreglo);
$resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'acceso');

// Crear Variables necesarias en los métodos

$variable = '';

if ($resultado) {
    $this->miConfigurador->setVariableConfiguracion("cache", true);
    $this->funcion->Redireccionador('actualizoDocumento', $variable);
    exit();
} else {
    $this->funcion->Redireccionador('noactualizoDocumento', $variable);
    exit();
}