<?php

namespace inventarios\asignarInventarioC\generarPazSalvo\funcion;

use inventarios\asignarInventarioC\generarPazSalvo\funcion\redireccion;

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

        
        
//         var_dump($_REQUEST);
        $conexion = "inventarios";
        $esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
        
        
       	$cadenaSql2 = $this->miSql->getCadenaSql('datosContratista', $_REQUEST['documentoContratista']);
        $datos_contratista = $esteRecursoDB->ejecutarAcceso($cadenaSql2, "busqueda");
        
        
//         var_dump($datos_contratista);

        $cadenaSql = $this->miSql->getCadenaSql ( 'datosFuncionario',$_REQUEST['funcionario'] );
        
        $funcionario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
        
//         var_dump($funcionario);

        $cadenaSql = $this->miSql->getCadenaSql ( 'jefe_recursos_fisicos' );

        $jefe = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
        $jefe = $jefe [0];
        
//         var_dump($jefe);exit;
        
        $ContenidoPdf = "
<style type=\"text/css\">
    table { 
        color:#333; /* Lighten up font color */
        font-family:Helvetica, Arial, sans-serif; /* Nicer font */

        border-collapse:collapse; border-spacing: 3px; 
    }

    table.page_header {width: 100%; border: none; background-color: #DDDDFF; border-bottom: solid 1mm #AAAADD; padding: 2mm }
    table.page_footer {width: 100%; border: none; background-color: #DDDDFF; border-top: solid 1mm #AAAADD; padding: 2mm}

    th { 
        border: 0px solid #CCC; 
        height: 13px;
    } /* Make cells a bit taller */

    th {
        background: none; /* Light grey background */
        font-weight: bold; /* Make sure they're bold */
        text-align: center;
        font-size:10px
    }

    td {
        background: none; /* Lighter grey background */
        text-align: left;
        font-size:10px
        
    }
</style>
<page backtop='55mm' backbottom='20mm' backleft='10mm' backright='10mm' pagegroup='new'>
<page_header>
    <table>
        <thead>
            <tr>
                <th style=\"width:50px;text-align:center;\" colspan=\"1\">
                    <img alt=\"Imagen\" src=" . $rutaBloque . "/css/images/escudo1.png\" />
                </th>
                <th style=\"width:600px;font-size:15px;\" colspan=\"1\">
                    <br><br><br>UNIVERSIDAD DISTRITAL FRANCISCO JOSÉ DE CALDAS
                    <br> NIT 899999230-7<br>
                    <br>
                     " . $fecha_ps . "<br><br>
                </th>
                            </tr>
        </thead>        
                    <tr></tr>
    </table>  
    <br>
</page_header>



             
            <table align='right'>
                    <tr>
                    <td align:center style=\"font-size:16px; text-align:center; \" >
                            <b>LA SECCIÓN DE ALMACEN E INVENTARIOS INFORMA<br>
                            </b>
                            <br><br><br><br>
                            </td>
                    </tr>
                    <tr>
                     <td  style=\"text-align:justify;font-size:12px;\">
                            Que el contratista <B>".$datos_contratista[0]["CON_NOMBRE"]."</B> con cédula de ciudadanía No. <B>".$datos_contratista[0]["CON_IDENTIFICACION"]."</B> con contrato ".$_REQUEST['tipo_contrato']." Número <B>".$_REQUEST['numero_contrato']."</B> y Vigencia <B>".$_REQUEST['vigencia']."</B>,  
                            ha entregado los elementos correspondientes a su relación contractual y quien certifica es el Supervisor y/o Funcionario ".$funcionario [0]['FUN_NOMBRE']." por lo tanto se encuentra
                            a <b>PAZ Y SALVO</b> con la Universidad Distrital Francisco José de Caldas.
                            <br><br><br><br><br><br><br>
                            Se firma el día " . $fecha_ps . ".
                            <br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br><br>
                            </td>
                    </tr>
                    <tr>
                        <td   align=justify style=\"font-size:12px;\" >
                            <br><br><br><br>
                        </td>
                    </tr>
               
                </table>


             
              
";
        
        
        
        

        $ContenidoPdf.="
    	
			<page_footer  '1mm' backbottom='1mm' backleft='10mm' backright='10mm'>
			
				
			<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
			<tr>
			
			<td style='width:100%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>  ____________________________________</td>
			</tr>
			<tr>
					
			<td style='width:100%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . $funcionario [0]['FUN_NOMBRE'] . "</td>
			
			</tr>
			<tr>
			
			<td style='width:100%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>Funcionario y/o Supervisor</td>
			</tr>
			</table>
		  <br><br><br><br><br>
					
					
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
					

				
		
			</page_footer> </page>";  
        
        
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
$html2pdf = new \HTML2PDF('P', 'Letter', 'es', true, 'UTF-8', 3);
$html2pdf->WriteHTML($contenido);
$html2pdf->Output('Paz y Salvo.pdf', 'D');
?>