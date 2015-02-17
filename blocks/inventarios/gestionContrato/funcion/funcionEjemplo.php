<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

$rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/inventarios/";
$rutaBloque .= $esteBloque ['nombre'];
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/inventarios/" . $esteBloque ['nombre'];

$conexion = "inventarios";
$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

$resultado = '';


//Guardar el archivo
if ($_FILES) {
    foreach ($_FILES as $key => $values) {
        $archivo = $_FILES[$key];
    }
    // obtenemos los datos del archivo
    $tamano = $archivo['size'];
    $tipo = $archivo['type'];
    $archivo1 = $archivo['name'];
    $prefijo = substr(md5(uniqid(rand())), 0, 6);

    if ($archivo1 != "") {
        // guardamos el archivo a la carpeta files
        $destino1 = $rutaBloque . "/archivoSoporte/" . $prefijo . "-" . $archivo1;

        if (copy($archivo['tmp_name'], $destino1)) {
            $status = "Archivo subido: <b>" . $archivo1 . "</b>";
            $destino1 = $host . "/archivoSoporte/" . $prefijo . "-" . $archivo1;

            $parametros = array(
                'nombre_archivo' => $archivo1,
                'id_unico' => $prefijo . "-" . $archivo1,
                'fecha_registro' => date('d/m/Y'),
                'ruta' => $destino1,
                'estado' => TRUE
            );

            $cadenaSql = $this->sql->cadena_sql("registroDocumento", $parametros);
            $resultado = $esteRecursoDB->ejecutarAcceso($cadenaSql, 'insertar');
        } else {
            $status = "<br>Error al subir el archivo1";
        }
    } else {
        $status = "<br>Error al subir archivo2";
    }
} else {
    echo "<br>NO existe el archivo D:!!!";
}

//Crear Variables necesarias en los métodos


$variable = '';

if ($resultado) {
    $this->funcion->Redireccionador('registroDocumento', $variable);
} else {
    $this->funcion->Redireccionador('noregistroDocumento', $variable);
}