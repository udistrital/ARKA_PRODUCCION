<?php

namespace inventarios\reportesGenerales\reporteSalidas\funcion;

use inventarios\reportesGenerales\reporteSalidas\funcion\redireccion;

$ruta = $this->miConfigurador->getVariableConfiguracion("raizDocumento");
$host = $this->miConfigurador->getVariableConfiguracion("host") . $this->miConfigurador->getVariableConfiguracion("site") . "/plugin/html2pfd/";
include ($ruta . "/plugin/html2pdf/html2pdf.class.php");

include_once ('redireccionar.php');

if (!isset($GLOBALS ["autorizado"])) {
    include ("../index.php");
    exit();
}

class RegistradorActa {

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

    function armarPDF() {

        $conexion = 'inventarios';
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = 'estructura';
        $esteRecursoDB2 = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //consultar la entrada y sus datos iniciales
        $cadenaSql = $this->miSql->getCadenaSql('consultarSalida_pdf', $_REQUEST['id_salida']);
        $datos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");


        //consultar los elementos asociados a la entrada
        $cadenaSql = $this->miSql->getCadenaSql('consultarElementos_pdf', $_REQUEST['id_salida']);
        $datos_elementos = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        date_default_timezone_set('America/Bogota');
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
        $rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
        $rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];

        $fecha = date('d-m-Y');
        $dias = array('Domingo, ', 'Lunes, ', 'Martes, ', 'Miercoles, ', 'Jueves, ', 'Viernes, ', 'Sábado, ');
        $meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
        $fecha_ps = $dias[date('w')] . ' ' . date('d') . ' de ' . $meses[date('n') - 1] . ' de ' . date('Y');

        $contenido = '';

        $grupo = 0;
        $cantidad = 0;
        $subtotal = 0;
        $total = 0;
        $iva = 0;

        $cantidad_entrada = 0;
        $subtotal_entrada = 0;
        $total_entrada = 0;
        $iva_entrada = 0;



        //consultar los elementos asociados a la entrada
        $cadenaSql = $this->miSql->getCadenaSql('consultarJefe', false);
        $jefeAlmacen = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
		//consulta datos del usuario

        $cadenaSql = $this->miSql->getCadenaSql('datosUsuario', $_REQUEST['usuario']);
        $datosUsuario = $esteRecursoDB2->ejecutarAcceso($cadenaSql, "busqueda");

        foreach ($datos_elementos as $key => $values) {
            $grupo_nuevo = $datos_elementos[$key]['grupo_cuentasalida'];
            if ($grupo_nuevo != $grupo) {

                if ($key != 0) {
                    $contenido.= " <tr> ";
                    $contenido.= "<th  style=\"text-align:right;\" colspan=\"2\" > Total Cantidad</th> ";
                    $contenido.= "<td style='text-align:center'>" . number_format($cantidad, 2, ",", ".") . "</td> ";
                    $contenido.= "<th  style=\"text-align:right;\" colspan=\"4\" >Subtotal Grupo</th> ";
                    $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($subtotal, 2, ",", ".") . "</td> ";
                    $contenido.= "<td  style=\"text-align:right;\" colspan=\"2\" >&nbsp;$&nbsp;" . number_format($iva, 2, ",", ".") . "</td> ";
                    $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($total, 2, ",", ".") . "</td> ";
                    $contenido.= "</tr> ";
                }

                $contenido.="    <tr>
        <th>GRUPO</th>
        <th>UNIDAD</th>
        <th>CANTIDAD</th>
        <th>TIPO BIEN</th>
        <th>PLACA</th>
        <th>DESCRIPCIÓN</th>
        <th>MARCA-SERIE</th>
        <th>VALOR UNITARIO</th>
        <th>SUBTOTAL</th>
        <th>% IVA</th>
        <th>TOTAL IVA</th>
        <th>TOTAL</th>
    </tr>";

                $cantidad = 0;
                $subtotal = 0;
                $total = 0;
                $iva = 0;
            }
            $contenido.= " <tr> ";
            $contenido.= "<td style='text-align:right' >" . $datos_elementos[$key]['grupo_cuentasalida'] . "</td> ";
            $contenido.= "<td style='text-align:center'>" . $datos_elementos[$key]['unidad'] . "</td> ";
            $contenido.= "<td style='text-align:center'>" . $datos_elementos[$key]['cantidad'] . "</td> ";
            $contenido.= "<td style='text-align:center'>" . $datos_elementos[$key]['tipo_bien'] . "</td> ";
            $contenido.= "<td style='text-align:center'>" . $datos_elementos[$key]['placa'] . "</td> ";
            $contenido.= "<td style='text-align:center' >" . wordwrap($datos_elementos[$key]['descripcion'], 80, "<br>") . "</td> ";
            $contenido.= "<td style='text-align:center' >" . wordwrap($datos_elementos[$key]['datos_elem'], 80, "<br>") . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($datos_elementos[$key]['valor'], 2, ",", ".") . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($datos_elementos[$key]['subtotal_sin_iva'], 2, ",", ".") . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;&nbsp;" . $datos_elementos[$key]['iva'] * 100 . "&nbsp;% </td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($datos_elementos[$key]['total_iva'], 2, ",", ".") . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($datos_elementos[$key]['total_iva_con'], 2, ",", ".") . "</td> ";
            $contenido.= "</tr> ";

            $cantidad = $cantidad + $datos_elementos[$key]['cantidad'];
            $subtotal = $subtotal + ($datos_elementos[$key]['subtotal_sin_iva']);
            $total = $total + ($datos_elementos[$key]['total_iva_con']);
            $iva = $iva + ($datos_elementos[$key]['total_iva']);

            $cantidad_entrada = $cantidad_entrada + $datos_elementos[$key]['cantidad'];
            $subtotal_entrada = $subtotal_entrada + ($datos_elementos[$key]['subtotal_sin_iva']);
            $total_entrada = $total_entrada + ($datos_elementos[$key]['total_iva_con']);
            $iva_entrada = $iva_entrada + ($datos_elementos[$key]['total_iva']);


            $grupo = $datos_elementos[$key]['grupo_cuentasalida'];
        }

        $contenido.= " <tr> ";
        $contenido.= "<th  style=\"text-align:right;\" colspan=\"2\" > Total Cantidad</th> ";
        $contenido.= "<td style='text-align:center'>" . number_format($cantidad, 2, ",", ".") . "</td> ";
        $contenido.= "<th  style=\"text-align:right;\" colspan=\"5\" >Subtotal Grupo</th> ";
        $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($subtotal, 2, ",", ".") . "</td> ";
        $contenido.= "<td  style=\"text-align:right;\" colspan=\"2\" >&nbsp;$&nbsp;" . number_format($iva, 2, ",", ".") . "</td> ";
        $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($total, 2, ",", ".") . "</td> ";
        $contenido.= "</tr> ";

        $contenido.= " <tr> ";
        $contenido.= "<th  style=\"text-align:right;\" colspan=\"2\" > TOTAL CANTIDAD SALIDA</th> ";
        $contenido.= "<th style='text-align:center'>" . number_format($cantidad_entrada, 2, ",", ".") . "</th> ";
        $contenido.= "<th  style=\"text-align:right;\" colspan=\"5\" >TOTAL SALIDA</th> ";
        $contenido.= "<th style='text-align:right' >&nbsp;$&nbsp;" . number_format($subtotal_entrada, 2, ",", ".") . "</th> ";
        $contenido.= "<th  style=\"text-align:right;\" colspan=\"2\" >&nbsp;$&nbsp;" . number_format($iva, 2, ",", ".") . "</th> ";
        $contenido.= "<th style='text-align:right' >&nbsp;$&nbsp;" . number_format($total_entrada, 2, ",", ".") . "</th> ";
        $contenido.= "</tr> ";

        $ContenidoPdf = "
<style type=\"text/css\">
   table { 
        color:#333; /* Lighten up font color */
        font-family:Helvetica, Arial, sans-serif; /* Nicer font */

        border-collapse:collapse; border-spacing: 3px; 
    }

    table.page_header {width: 100%; border: none; background-color: #DDDDFF; border-bottom: solid 1mm #AAAADD; padding: 2mm; margin-top: 1cm; }
    table.page_footer {width: 100%; border: none; background-color: #DDDDFF; border-top: solid 1mm #AAAADD; padding: 2mm}

    td, th { 
        border: 1px solid #CCC; 
        height: 10px;
    } /* Make cells a bit taller */

    th {
        background: #F3F3F3; /* Light grey background */
        font-weight: bold; /* Make sure they're bold */
        text-align: center;
        font-size:9px
    }

    td {
        background: #FAFAFA; /* Lighter grey background */
        text-align: left;
        font-size:8.5px
    }
</style>

<page backtop='30mm' backbottom='48mm' backleft='5mm' backright='5mm' pagegroup='new'>
<page_header>
    <table align='center'>
        <thead>
            <tr>
                <th style=\"width:10px;\" colspan=\"1\">
                    <img alt=\"Imagen\" src=" . $rutaBloque . "/css/images/escudo1.png\" />
                </th>
                <th style=\"width:845px;font-size:11px;\" colspan=\"1\">
                    <br>UNIVERSIDAD DISTRITAL FRANCISCO JOSÉ DE CALDAS
                    <br> NIT 899999230-7<br>
                    <br> SISTEMA DE GESTIÓN DE INVENTARIOS Y ALMACÉN<br>
                    <br> Reporte Salida de Almacén<br>
                </th>
                            </tr>
        </thead>        
                    <tr></tr>
    </table>  
   </page_header>

<page_footer>
<table align='center'>
    <tr>
        <td style=\"text-align:center; width: 325px;\"><br><br><br><br>
        </td>
        <td style=\"text-align:center; width: 325px;\"><br><br><br><br>
        </td>
    </tr>
    <tr>  
        <td align='center' style=\"text-align:center; width: 275px;\" >
                " . $jefeAlmacen[0][1] . "
            <br><b>Jefe Sección Almacén General e Inventarios</b>
        </td>
          <td align='center' style=\"text-align:center; width: 275px;\" >
                " . $datos[0]['nombre_funcionario'] . "     CC." . $datos[0]['funcionario'] . "
            <br><b>Funcionario que recibe</b>
        </td>
         </tr>
        <tr>
          <td align='left' colspan=\"2\" style=\"text-align:center;font-size:8px; width: 550px;\" >Proyectó y Revisó: " . $datosUsuario[0]['nombre'] . "</td>
        </tr>
          <tr>
          <td align='center' colspan=\"2\" style=\"text-align:center;font-size:8px; width: 550px;\" ><i>Sólo los bienes de Consumo Controlado y Devolutivo serán cargados a su inventario.</i></td>
        </tr>
</table>
             <p style='text-align: right; font-size:10px;'>[[page_cu]]/[[page_nb]]</p>
             <p style='text-align: right; font-size:8px; '><i>Generado: " . $fecha_ps . "</i></p>
</page_footer> 
   
 <table align='center'>
            <tr>
                <th style=\"width:85px;font-size:12px;\" colspan=\"1\">Salida:</th>
                <td style='width:325px;text-align:center;font-size:12px;'>" . $datos[0]['num_salida'] . "</td>
                <th style=\"width:85px;font-size:12px;\" colspan=\"1\">Fecha:</th>
                <td style='width:325px;text-align:center;font-size:12px;'>" . $datos[0]['fecha_salida'] . "</td>
            </tr>
            <tr>
                <th style=\"width:85px;font-size:12px;\" colspan=\"1\">Entrada:</th>
                <td  style='text-align:center;font-size:12px;'>" . $datos[0]['num_entrada'] . "</td>
                <th style=\"width:85px;font-size:12px;\" colspan=\"1\">Fecha:</th>
                <td  style='text-align:center;font-size:12px;'>" . $datos[0]['fecha_entrada'] . "</td>
            </tr>
            <tr>
                <th style=\"width:85px;font-size:12px;\" colspan=\"1\">Factura:</th>
                <td  style='text-align:center;font-size:12px;'>" . $datos[0]['numero_factura'] . "</td>
                <th style=\"width:85px;font-size:12px;\" colspan=\"1\">Fecha:</th>
                <td  style='text-align:center;font-size:12px;'>" . $datos[0]['fecha_factura'] . "</td>
            </tr>
            <tr></tr>
    </table><br>
    
<table align='center'>
            <tr>
                <th style=\"width:75px;font-size:12px;\" colspan=\"1\">Sede:</th>
                <td style='width:220px;text-align:center;font-size:12px;'>" . $datos[0]['sede'] . "</td>
                <th style=\"width:70px;font-size:12px;\" colspan=\"1\">Dependencia:</th>
                <td style='width:220px;text-align:center;font-size:12px;'>" . $datos[0]['dependencia'] . "</td>
                <th style=\"width:60px;font-size:12px;\" colspan=\"1\">Ubicación:</th>
                <td style='width:220px;text-align:center;font-size:12px;'>" . $datos[0]['ubicacion'] . "</td>
            </tr>
            <tr>
                <th style=\"width:85px;font-size:11px;\" colspan=\"1\">Funcionario:</th>
                <td  style='text-align:center;font-size:12px;'>" . $datos[0]['nombre_funcionario'] . "</td>
                <th style=\"width:85px;font-size:11px;\" colspan=\"1\">Cédula:</th>
                <td  style='text-align:center;font-size:12px;' colspan='3'>" . $datos[0]['funcionario'] . "</td>
            </tr>
            <tr>
                <th style=\"width:85px;font-size:12px;\" colspan=\"1\">Proveedor:</th>
                <td  style='text-align:center;font-size:12px;' colspan='5'>" . $datos[0]['proveedor'] . " - " . $datos[0]['nombre_proveedor'] . "</td>
            </tr>
            <tr>
                <th style=\"width:85px;font-size:12px;\" colspan=\"1\">Observaciones:</th>
                <td  style='text-align:center;font-size:12px;' colspan='5'>" . $datos[0]['observaciones'] . "</td>
            </tr>
     
    </table><br>
<table align='center'>
<thead>
    <tr>
        <th style=\"text-align:right;width:940px;font-size:12px;\" colspan=\"12\"></th>
    </tr>

    </thead>
    " . $contenido . "
    
</table>


</page>
             
              
";
        return $ContenidoPdf;
    }

    function procesarFormulario() {
        $esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");
        $miPaginaActual = $this->miConfigurador->getVariableConfiguracion('pagina');

        $directorio = $this->miConfigurador->getVariableConfiguracion("host");
        $directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
        $directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");

        $rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
        $rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
        $rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];
    }

}

$miRegistrador = new RegistradorActa($this->lenguaje, $this->sql, $this->funcion);
$resultado = $miRegistrador->procesarFormulario();

$contenido = $miRegistrador->armarPDF($resultado);


ob_start();
$html2pdf = new \HTML2PDF('L', 'Letter', 'es', true, 'UTF-8', array(1, 5, 1, 5));
$html2pdf->WriteHTML($contenido);
$html2pdf->Output('reportesalida_.pdf', 'D');
?>
