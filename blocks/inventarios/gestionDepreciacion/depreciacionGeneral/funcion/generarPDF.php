<?php

namespace inventarios\gestionDepreciacion\depreciacionGeneral\funcion;

use inventarios\gestionDepreciacion\depreciacionGeneral\funcion\redireccion;

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
      
        $depreciacion = unserialize(base64_decode($_REQUEST['depreciacion']));
          
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

        foreach ($depreciacion as $key => $values) {
            $contenido.= " <tr> ";
            $contenido.= "<td style='text-align:right'  >" .$depreciacion[$key]['cuenta'] . "</td> ";
            $contenido.= "<td style='text-align:right'  >" .wordwrap($depreciacion[$key]['grupo'],20,"<br>") . "</td> ";
            $contenido.= "<td style='text-align:center'  >" .$depreciacion[$key]['total_elementos'] . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . $depreciacion[$key]['total_valor_historico']. "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . $depreciacion[$key]['total_ajuste_inflacion'] . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . $depreciacion[$key]['total_valor_ajustado']. "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . $depreciacion[$key]['total_depreciacion'] . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . $depreciacion[$key]['total_ajuste_inflacionario'] . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . $depreciacion[$key]['total_depreciacion_circular56']. "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . $depreciacion[$key]['total_libros']. "</td> ";
            $contenido.= "</tr> ";
        }
        
        
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
        height: 12px;
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
<page backtop='45mm' backbottom='20mm' backleft='5mm' backright='5mm' pagegroup='new'>
<page_header>
    <table align='center'>
        <thead>
            <tr>
                <th style=\"width:10px;\" colspan=\"1\">
                    <img alt=\"Imagen\" src=" . $rutaBloque . "/css/images/escudo1.png\" />
                </th>
                <th style=\"width:845px;font-size:12px;\" colspan=\"1\">
                    <br>UNIVERSIDAD DISTRITAL FRANCISCO JOSÉ DE CALDAS
                    <br> NIT 899999230-7<br>
                    <br> SISTEMA DE GESTIÓN DE INVENTARIOS Y ALMACÉN<br>
                    <br> DEPRECIACIÓN CONSOLIDADA<br><br>
                    <br> " . $fecha_ps . "<br><br>
                </th>
                            </tr>
        </thead>        
                    <tr></tr>
    </table>  
    <br><br><br>
</page_header>

<page_footer>
    <table align='center' width='100%'>
        <tr>
            <th align='center' style=\"width: 750px;\">
                Universidad Distrital Francisco José de Caldas
                <br>
                Todos los derechos reservados.
                <br>
                Carrera 8 N. 40B53 Piso 6 / PBX 3238400 - 3239300 Ext. 1621 - 1623 - 1624
                <br>

            </th>
        </tr>
    </table>
             <p style='text-align: right; font-size:10px;'>[[page_cu]]/[[page_nb]]</p>
</page_footer> 
   
<table align='center'>
<thead>
    <tr>
        <th>CUENTA</th>
        <th>GRUPO</th>
        <th>No. ELEMENTOS</th>
        <th>TOTAL VALOR HISTÓRICO</th>
        <th>TOTAL AJUSTE<br>INFLACIÓN</th>
        <th>TOTAL VALOR<br>AJUSTADO</th>
        <th>TOTAL<br>DEPRECIACIÓN</th>
        <th>TOTAL AJUSTE<BR>INFLACIONARIO</th>
        <th>TOTAL DEPRECIACIÓN<BR>CIRCULAR 56</th>
        <th>TOTAL<br>LIBROS</th>
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
$html2pdf->Output('depreciacionGeneral.pdf', 'D');
?>