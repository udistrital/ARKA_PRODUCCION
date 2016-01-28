<?
$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );

$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

include ($ruta . "/plugin/html2pdf/html2pdf.class.php");

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class RegistradorOrden {
	var $miConfigurador;
	var $lenguaje;
	var $miFormulario;
	var $miFuncion;
	var $miSql;
	var $conexion;
	function __construct($lenguaje, $sql, $funcion) {
		$this->miConfigurador = \Configurador::singleton ();
		$this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
		$this->lenguaje = $lenguaje;
		$this->miSql = $sql;
		$this->miFuncion = $funcion;
	}
	function documento() {
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$funcionario = $_REQUEST ['funcionario'];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'nombreContratista', $_REQUEST ['contratista'] );
		
		$contratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'datosFuncionario', $_REQUEST ['funcionario'] );
		
		$funcionario = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarElementosContratista', array (
				"funcionario" => $_REQUEST ['supervisor'],
				"contratista" => $_REQUEST ['contratista'],
				"tipo_contrato" => $_REQUEST ['tipo_contrato'],
				"numero_contrato" => $_REQUEST ['numero_contrato'],
				"vigencia" => $_REQUEST ['vigencia']
		) );
		$elementos_contratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		// var_dump($elementos_contratista);
		$cadenaSql = $this->miSql->getCadenaSql ( 'jefe_recursos_fisicos' );
		// echo $cadenaSql;
		$jefe = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$jefe = $jefe [0];
		// var_dump($jefe);
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );
		
		$contenidoPagina = '';
		
		$contenidoPagina .= "
<style type=\"text/css\">
    table { 
        
        font-family:Helvetica, Arial, sans-serif; /* Nicer font */
		
        border-collapse:collapse; border-spacing: 3px; 
    }

    td, th { 
        border: 1px solid #CCC; 
        height: 13px;
    } /* Make cells a bit taller */

	col{
	width=50%;
	
	}			
	th {
        
        font-weight: bold; /* Make sure they're bold */
        text-align: center;
        font-size:10px
    }

    td {
        
        text-align: left;
        font-size:10px
    }
</style>				
				
				
<page backtop='1mm' backbottom='1mm' backleft='10mm' backright='10mm' footer='page'>
	

        <table align='left' style='width:100%;' >
            <tr>
                <td align='center' style='width:12%;border=none;' >
                    <img src='" . $directorio . "/css/images/escudo.png'  width='80' height='100'>
                </td>
                <td align='center' style='width:88%;border=none;' >
                    <font size='9px'><b>UNIVERSIDAD DISTRITAL FRANCISCO JOSÉ DE CALDAS </b></font>
                     <br>
                    <font size='7px'><b>NIT: 899.999.230-7</b></font>
                     <br>
                      <br>
                     <font size='9px'><b>Almacén General e Inventarios</b></font>
                    <br>		
                    <font size='7px'><b>Asignación de Inventario Contratista </b></font>
                    <br>									
                    <font size='3px'>www.udistrital.edu.co</font>
                     <br>
                	<br>
                    <font size='4px'>" . date ( "Y-m-d" ) . "</font>
                    
                   			
                </td>
            </tr>
        </table>
                <br> <br>  <br>
          Yo , " . $contratista [0] ['CON_NOMBRE'] . " con identificación número " . $contratista [0] ['CON_IDENTIFICACION'] . ", recibo de conformidad con el Contrato ".$_REQUEST['tipo_contrato']." Número " . $_REQUEST['numero_contrato'] . "  y Vigencia " . $_REQUEST['vigencia'] . " por parte del supervisor y/o funcionario " . $funcionario [0] ['FUN_NOMBRE'] . " los siguientes Elementos: <br><br> ";
		
		$contenidoPagina .= "<br>
						
			<table style='width:100%;'>
			<tr> 
			<td style='width:10%;text-align=center;'>Placa</td>
			<td style='width:30%;text-align=center;'>Descripción</td>
			<td style='width:10%;text-align=center;'>Marca Serie</td> 
			<td style='width:10%;text-align=center;'>Sede</td>
			<td style='width:20%;text-align=center;'>Dependencia</td>
			<td style='width:20%;text-align=center;'>Ubicación<br>Especifica</td>
					</tr>";
		
		foreach ( $elementos_contratista as $valor ) {
			
			$contenidoPagina .= " 
								<tr>
                    			<td style='width:10%;text-align=center;'>" . $valor ['placa'] . "</td>
                    			<td style='width:30%;text-align=center;'>" . $valor ['descripcion'] . "</td>
                    			<td style='width:10%;text-align=center;'>" . $valor ['marca'] . " " . $valor ['serie'] . "</td>
                    			<td style='width:10%;text-align=center;'>" . $valor ['sede'] . "</td>
                    			<td style='width:20%;text-align=center;'>" . $valor ['dependencia'] . "</td>
                    		    <td style='width:20%;text-align=center;'>" . $valor ['espacio_fisico'] . "</td>
                    			</tr>";
		}
		
		$contenidoPagina .= "</table> ";
		
		$contenidoPagina .= "
		
			<page_footer  '1mm' backbottom='1mm' backleft='10mm' backright='10mm'>
			
				
			<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
			<tr>
			<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>    ____________________________________</td>
			<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>  ____________________________________</td>
			</tr>
			<tr>
			<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . $contratista [0] ['CON_NOMBRE'] . "</td>		
			<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . $funcionario [0] ['FUN_NOMBRE'] . "</td>
			
			</tr>
			<tr>
			<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>Contratista</td>
			<td style='width:50%;text-align:center;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>Funcionario y/o Supervisor</td>
			</tr>
			</table>
			</page_footer>
					";
		
		$contenidoPagina .= "</page>";
		
		// echo $contenidoPagina;exit;
		return $contenidoPagina;
	}
}

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$textos = $miRegistrador->documento ();

ob_start ();
$html2pdf = new \HTML2PDF ( 'L', 'LETTER', 'es', true, 'UTF-8', array (
		1,
		2,
		2,
		10 
) );
$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
$html2pdf->WriteHTML ( $textos );

$html2pdf->Output ( 'Constancia Inventario Contratista' . date ( 'Y-m-d' ) . '.pdf', 'D' );

?>





