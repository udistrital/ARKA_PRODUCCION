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
        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        $conexion = 'estructura';
        $esteRecursoDB2 = $this->miConfigurador->fabricaConexiones->getRecursoDB($conexion);

        //consultar los elementos asociados a la entrada
        $cadenaSql = $this->miSql->getCadenaSql('consultarJefe', false);
        $jefeAlmacen = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");
// consulta datos del usuario
        $cadenaSql = $this->miSql->getCadenaSql('datosUsuario', $_REQUEST['usuario']);
        $datosUsuario = $esteRecursoDB2->ejecutarAcceso($cadenaSql, "busqueda");
//consultar los datos de la depreciación a descargar
        $cadenaSql = $this->miSql->getCadenaSql('consultarGeneral', null);
        $consulta = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

//actualizar estado de la depreciacion
        $cadenaSql = $this->miSql->getCadenaSql('updateGeneral', $consulta[0]['dep_id']);
        $actualizar = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");

        //eliminar las que no fueron ejecutadas a PDF
        $cadenaSql = $this->miSql->getCadenaSql('removeGeneral', null);
        $remove = $esteRecursoDB->ejecutarAcceso($cadenaSql, "busqueda");


        $depreciacion = json_decode($consulta[0]['dep_json'], true);

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

        $total_elementos = 0;
        $total_histórico = 0;
        $total_ainflacion = 0;
        $total_ajustado = 0;
        $total_depreciacion = 0;
        $total_inflacionario = 0;
        $total_circular56 = 0;
        $total_libros = 0;

        foreach ($depreciacion as $key => $values) {
            $contenido.= " <tr> ";
            $contenido.= "<td style='text-align:right'  >" . $depreciacion[$key]['cuenta'] . "</td> ";
            $contenido.= "<td style='text-align:right'  >" . wordwrap($depreciacion[$key]['grupo'], 20, "<br>") . "</td> ";
            $contenido.= "<td style='text-align:center'  >" . $depreciacion[$key]['total_elementos'] . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($depreciacion[$key]['total_valor_historico'], 2, ',', '.') . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($depreciacion[$key]['total_ajuste_inflacion'], 2, ',', '.') . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($depreciacion[$key]['total_valor_ajustado'], 2, ',', '.') . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($depreciacion[$key]['total_depreciacion'], 2, ',', '.') . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($depreciacion[$key]['total_ajuste_inflacionario'], 2, ',', '.') . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($depreciacion[$key]['total_depreciacion_circular56'], 2, ',', '.') . "</td> ";
            $contenido.= "<td style='text-align:right' >&nbsp;$&nbsp;" . number_format($depreciacion[$key]['total_libros'], 2, ',', '.') . "</td> ";
            $contenido.= "</tr> ";

            $total_elementos = $total_elementos + $depreciacion[$key]['total_elementos'];
            $total_histórico = $total_histórico + $depreciacion[$key]['total_valor_historico'];
            $total_ainflacion = $total_ainflacion + $depreciacion[$key]['total_ajuste_inflacion'];
            $total_ajustado = $total_ajustado + $depreciacion[$key]['total_valor_ajustado'];
            $total_depreciacion = $total_depreciacion + $depreciacion[$key]['total_depreciacion'];
            $total_inflacionario = $total_inflacionario + $depreciacion[$key]['total_ajuste_inflacionario'];
            $total_circular56 = $total_circular56 + $depreciacion[$key]['total_depreciacion_circular56'];
            $total_libros = $total_libros + $depreciacion[$key]['total_libros'];
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
<page backtop='45mm' backbottom='25mm' backleft='5mm' backright='5mm' pagegroup='new'>
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
<table align='center'>
    <tr>
        <td style=\"text-align:center; width: 650px;\"><br><br><br>
        </td>
    </tr>
    <tr>  
        <td align='center' style=\"text-align:center; width: 550px;\" >
                " . $jefeAlmacen[0][1] . "
                            <br><b>Jefe Sección Almacén General e Inventarios</b>
        </td>
         </tr>
        <tr>
          <td align='left' style=\"text-align:center;font-size:8px; width: 550px;\" >Proyectó y Revisó: " . $datosUsuario[0]['nombre'] . "</td>
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
    <tr>
        <th colspan=\"2\" >TOTAL</th>
        <th>&nbsp;&nbsp;" . $total_elementos . "</th>
        <th>&nbsp;$&nbsp;" . number_format($total_histórico, 2, ',', '.') . "</th>
        <th>&nbsp;$&nbsp;" . number_format($total_ainflacion, 2, ',', '.') . "</th>
        <th>&nbsp;$&nbsp;" . number_format($total_ajustado, 2, ',', '.') . "</th>
        <th>&nbsp;$&nbsp;" . number_format($total_depreciacion, 2, ',', '.') . "</th>
        <th>&nbsp;$&nbsp;" . number_format($total_inflacionario, 2, ',', '.') . "</th>
        <th>&nbsp;$&nbsp;" . number_format($total_circular56, 2, ',', '.') . "</th>
        <th>&nbsp;$&nbsp;" . number_format($total_libros, 2, ',', '.') . "</th>
    </tr>
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