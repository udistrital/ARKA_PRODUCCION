<?php

namespace inventarios\gestionElementos\registrarFaltantesSobrantes\funcion;

use inventarios\gestionElementos\registrarFaltantesSobrantes\funcion\redireccion;

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


        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("raizDocumento") . "/blocks/inventarios/gestionElementos/";
        $rutaBloque .= $esteBloque ['nombre'];

        $host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/inventarios/gestionElementos/" . $esteBloque ['nombre'];

        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $fechaActual = date('Y-m-d');



        $cadenaSql = $this->miSql->getCadenaSql('max_estado_elemento');
        $max_estado_elemento = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        $max_estado_elemento = $max_estado_elemento[0][0] + 1;


        switch ($_REQUEST ['inexistencia']) {

            case '1' :

                $cadenaSql = $this->miSql->getCadenaSql('id_sobrante');
                $sobrante = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                $id_sobrante = $sobrante [0] [0] + 1;

                $arreglo = array(
                    $_REQUEST['elemento_ind'],
                    0,
                    $id_sobrante,
                    0,
                    $_REQUEST ['observaciones'],
                    'NULL',
                    'NULL',
                    '0001-01-01',
                    '0001-01-01',
                    $fechaActual,
                    $_REQUEST ['inexistencia'],
                    $max_estado_elemento
                );

                break;

            case '2' :

                $cadenaSql = $this->miSql->getCadenaSql('id_hurto');
                $hurto = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                $id_hurto = $hurto [0] [0] + 1;

                $i = 0;
                foreach ($_FILES as $key => $values) {

                    $archivo [$i] = $_FILES [$key];
                    $i ++;
                }
                $archivo = $archivo[0];


                if (isset($archivo)) {
                    // obtenemos los datos del archivo
                    $tamano = $archivo ['size'];
                    $tipo = $archivo ['type'];
                    $archivo1 = $archivo ['name'];
                    $prefijo = substr(md5(uniqid(rand())), 0, 6);

                    if ($archivo1 != "") {
                        // guardamos el archivo a la carpeta files
                        $destino1 = $rutaBloque . "/documento_denuncia/" . $prefijo . "_" . $archivo1;
                        if (copy($archivo ['tmp_name'], $destino1)) {
                            $status = "Archivo subido: <b>" . $archivo1 . "</b>";
                            $destino1 = $host . "/documento_denuncia/" . $prefijo . "_" . $archivo1;
                        } else {
                            $status = "Error al subir el archivo";
                            echo $status;
                        }
                    } else {
                        $status = "Error al subir archivo";
                        echo $status . "2";
                    }

                    $arreglo = array(
                        $destino1,
                        $archivo1
                    );
                }

                $arreglo = array(
                    $_REQUEST['elemento_ind'],
                    0,
                    0,
                    $id_hurto,
                    $_REQUEST ['observaciones'],
                    $destino1,
                    $archivo1,
                    $_REQUEST ['fecha_denuncia'],
                    $_REQUEST ['fecha_hurto'],
                    $fechaActual,
                    $_REQUEST ['inexistencia'],
                    $max_estado_elemento
                );

                break;

            case '3' :

                $cadenaSql = $this->miSql->getCadenaSql('id_faltante');
                $faltante = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

                $id_faltante = $faltante [0] [0] + 1;

                $arreglo = array(
                    $_REQUEST['elemento_ind'],
                    $id_faltante,
                    0,
                    0,
                    $_REQUEST ['observaciones'],
                    'NULL',
                    'NULL',
                    '0001-01-01',
                    '0001-01-01',
                    $fechaActual,
                    $_REQUEST ['inexistencia'],
                    $max_estado_elemento
                );

                break;
        }




        $cadenaSql = $this->miSql->getCadenaSql('insertar_faltante_sobrante', $arreglo);
        $registro = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda",$arreglo,"insertar_faltante_sobrante");


        $arreglo = array(
            $_REQUEST['elemento_ind'],
            $registro[0][3],
        );


        $cadenaSql = $this->miSql->getCadenaSql('actualizacion_estado_elemento', $arreglo);
        $actualizacion = $esteRecursoDB->ejecutarAcceso($cadenaSql, "acceso",$arreglo,"actualizacion_estado_elemento"); 

        if ($registro) {


            $arreglo = array(
                $registro[0][0],
                $registro[0][1],
                $registro[0][2]
            );

            $registro = serialize($arreglo);
            $this->miConfigurador->setVariableConfiguracion('prueba', 'true');
            redireccion::redireccionar('inserto', array($registro,$_REQUEST['usuario']));
            exit();
        } else {

            redireccion::redireccionar('noInserto',$_REQUEST['usuario']); 
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