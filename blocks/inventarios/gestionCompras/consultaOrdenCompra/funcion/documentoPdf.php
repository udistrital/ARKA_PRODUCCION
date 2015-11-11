<?

namespace inventarios\gestionCompras\registrarOrdenCompra\funcion;

use inventarios\gestionCompras\registrarOrdenCompra\funcion\redireccion;

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
// 	var_dump($_REQUEST);
		$conexion = "sicapital";
		$esteRecursoDBO = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrdenCompra_documento', $_REQUEST ['orden_compra_consulta'] );
		$ordenCompra = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$ordenCompra = $ordenCompra [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacionPresupuestal', $ordenCompra ['info_presupuestal'] );
		$info_presupuestal = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$info_presupuestal = $info_presupuestal [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacion_proveedor', $ordenCompra ['id_proveedor'] );
		$proveedor = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$proveedor = $proveedor [0];
		
		$cotizacion = ($ordenCompra ['nombre_cotizacion'] != '') ? 'SI' : 'NO';
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarRubro', $ordenCompra ['rubro'] );
		$rubro = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$rubro = $rubro [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarDependencia_consulta', $ordenCompra ['id_dependencia'] );
		$dependencia = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$dependencia = $dependencia [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarItems_consulta', $_REQUEST ['orden_compra_consulta'] );
		$items = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'polizas' );
		$polizas = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$polizas = $polizas [0];
		
		$poliza1 = ($ordenCompra ['poliza1'] != 'f') ? 'X' : ' ';
		$poliza2 = ($ordenCompra ['poliza2'] != 'f') ? 'X' : ' ';
		$poliza3 = ($ordenCompra ['poliza3'] != 'f') ? 'X' : ' ';
		$poliza4 = ($ordenCompra ['poliza4'] != 'f') ? 'X' : ' ';
		$poliza5 = ($ordenCompra ['poliza5'] != 'f') ? 'X' : ' ';
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarDestino', $ordenCompra ['destino'] );
		$destino = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$destino = $destino [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultar_forma_pago', $ordenCompra ['forma_pago'] );
		$forma_pago = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$forma_pago = $forma_pago [0];
		
// 		$arreglo = array (
// 				$ordenCompra ['id_contratista'],
// 				$ordenCompra ['vig_contratista'] 
// 		);
// 		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarContratista_consulta', $arreglo );
// 		$contratista = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
// 		$contratista = $contratista [0];
		
		
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrdenador_gasto', $ordenCompra['id_ordenador'] );
		$ordenador = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$ordenador = $ordenador [0];
		
		
// 		var_dump($ordenador); 
// 		var_dump($contratista); 
// 		var_dump($forma_pago); 
// 		var_dump($destino); 
// 		var_dump($items);
// 		var_dump ( $ordenCompra ); 
// 		var_dump($info_presupuestal);
// 		var_dump($proveedor);
// 		var_dump($dependencia); exit;
		$contenidoPagina = "
<style type=\"text/css\">
    table { 
        color:#333; /* Lighten up font color */
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
        background: #F3F3F3; /* Light grey background */
        font-weight: bold; /* Make sure they're bold */
        text-align: center;
        font-size:10px
    }

    td {
        background: #FAFAFA; /* Lighter grey background */
        text-align: left;
        font-size:10px
    }
</style>				
				
				
<page backtop='8mm' backbottom='8mm' backleft='8mm' backright='8mm'>
	

        <table align='left' style='width:100%;' >
            <tr>
                <td align='center' >
                    <img src='" . $directorio . "/css/images/escudo.png'  width='80' height='100'>
                </td>
                <td align='center' style='width:88%;' >
                    <font size='9px'><b>UNIVERSIDAD DISTRITAL FRANCISCO JOSÉ DE CALDAS </b></font>
                     <br>
                    <font size='7px'><b>NIT: 899.999.230-7</b></font>
                     <br>
                    <font size='3px'>CARRERA 7 No. 40-53 PISO 7. TELEFONO 3239300 EXT. 2609 -2605</font>
                     <br>		
                    <font size='5px'>www.udistrital.edu.co</font>
                     <br>
                    <font size='4px'>" . date ( "Y-m-d" ) . "</font>
                </td>
            </tr>
        </table>
	
	                  		
       		<table style='width:100%;'>
            <tr> 
			<td style='width:50%;'>ORDEN DE COMPRA Nro.  " . $_REQUEST ['orden_compra_consulta'] . "</td>
			<td style='width:50%;text-aling=right;'>FECHA DE ORDEN :  " . $ordenCompra ['fecha_registro'] . "</td> 			
 		 	</tr>
			<tr> 
			<td >Disponibilidad Presupuestal : Nro. " . $info_presupuestal ['numero_dispo'] . "</td>
			<td>Vigencia Disponibilidad :  " . $info_presupuestal ['vigencia_dispo'] . "</td> 			
 		 	</tr>	
		 	<tr> 
			<td >Proveedor : " . $proveedor [0] . "</td>
			<td>Nit :  " . $info_presupuestal ['1'] . "</td> 			
 		 	</tr>
		    </table>	
					                 		
		    <table style='width:100%;'>
			<tr> 
			<td style='width:33.31%;'>Dirección : " . $proveedor [2] . "</td>
			<td style='width:33.31%;'>Telefono :  " . $proveedor [3] . "</td>
			<td style='width:33.31%;'>Cotización Adjunta:  " . $cotizacion . "</td> 			
 		 	</tr> 		          		
 			</table>	    
					             		
			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Rubro :  " . $rubro [0] . "</td>
			</tr> 		          		
 			</table>
			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Dependencia Solicitante :  " . $dependencia [1] . "</td>
			</tr> 		          		
 			</table>	
			<table style='width:100%;'>
			<tr> 
			<td style='width:50%;'>Dirección :  " . $dependencia [2] . "</td>
			<td style='width:50%;'>Telefono :  " . $dependencia [3] . "</td>
			</tr> 		          		
 			</table>	
			 		
			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Descripción Solicitante : </td>
			</tr> 		          		
 			</table>		
			<table style='width:100%;'>
			<tr> 
			<td style='width:10%;text-align=center;'>Item</td>
			<td style='width:15%;text-align=center;'>Unidad/Medida</td>
			<td style='width:20%;text-align=center;'>Cantidad</td>
			<td style='width:30%;text-align=center;'>Descripción</td>
			<td style='width:8.3%;text-align=center;'>Valor Unitario($)</td>
			<td style='width:8.3%;text-align=center;'>Valor Total($)</td>
			<td style='width:8.3%;text-align=center;'>Descuento</td>
			</tr> 		          		
 			</table>		
			<table style='width:100%;'>";
		
		foreach ( $items as $it ) {
			$contenidoPagina .= "<tr>";
			$contenidoPagina .= "<td style='width:10%;text-align=center;'>" . $it [0] . "</td>";
			$contenidoPagina .= "<td style='width:15%;text-align=center;'>" . $it [1] . "</td>";
			$contenidoPagina .= "<td style='width:20%;text-align=center;'>" . $it [2] . "</td>";
			$contenidoPagina .= "<td style='width:30%;text-align=justify;'>" . $it [3] . "</td>";
			$contenidoPagina .= "<td style='width:8.3%;text-align=center;'>$ " . $it [4] . "</td>";
			$contenidoPagina .= "<td style='width:8.3%;text-align=center;'>$ " . $it [5] . "</td>";
			$contenidoPagina .= "<td style='width:8.3%;text-align=center;'>$ " . $it [6] . "</td>";
			$contenidoPagina .= "</tr>";
		}
		
		$contenidoPagina .= "</table>
			<table style='width:100%;'>
			<tr> 
			<td style='width:50%;text-align=left;'><b>SUBTOTAL</b></td>
			<td style='width:50%;text-align=center;'>$ " . $ordenCompra ['subtotal'] . "</td>	
			</tr>
			<tr> 
			<td style='width:50%;text-align=left;'><b>APLICA IVA</b></td>
			<td style='width:50%;text-align=center;'>$ " . $ordenCompra ['iva'] . "</td>	
			</tr>
			<tr> 
			<td style='width:50%;text-align=left;'><b>TOTAL</b></td>
			<td style='width:50%;text-align=center;'>$ " . $ordenCompra ['total'] . "</td>	
			</tr>
			</table>

			<table style='width:100%;'>		
			<tr> 
			<td style='width:100%;text-align:center;text-transform:uppercase;'><b>" . $ordenCompra ['valor_letras'] . "</b></td>	
			</tr>	
			</table>	
			 	
			<table style='width:100%;'>		
			<tr> 
			<td style='width:100%;text-align:left;'>Obligaciones Proveedor : </td>	
			</tr>
			<tr> 
			<td style='width:100%;text-align:left;'>" . $ordenCompra ['obligaciones_proveedor'] . "</td>	
			</tr>
			<tr> 
			<td style='width:100%;text-align:left;'>Obligaciones Constratista : </td>	
			</tr>
			<tr> 
			<td style='width:100%;text-align:left;'>" . $ordenCompra ['obligaciones_contratista'] . "</td>	
			</tr>					
			</table>	
        	<table style='width:100%;'>		
			<tr> 
			<td style='width:90%;text-align:left;'>" . $polizas [1] . "</td>	
			<td style='width:10%;text-align:center;'>" . $poliza1 . "</td>		
			</tr>
			<tr> 
			<td style='width:90%;text-align:left;'>" . $polizas [2] . "</td>	
			<td style='width:10%;text-align:center;'>" . $poliza2 . "</td>		
			</tr>
			<tr> 
			<td style='width:90%;text-align:left;'>" . $polizas [3] . "</td>	
			<td style='width:10%;text-align:center;'>" . $poliza3 . "</td>		
			</tr>								
			<tr> 
			<td style='width:90%;text-align:left;'>" . $polizas [4] . "</td>	
			<td style='width:10%;text-align:center;'>" . $poliza4 . "</td>		
			</tr>					
			<tr> 
			<td style='width:90%;text-align:left;'>" . $polizas [5] . "</td>	
			<td style='width:10%;text-align:center;'>" . $poliza5 . "</td>		
			</tr>					
			</table>	
			
			<table style='width:100%;'>		
			<tr>
			<td style='width:50%;text-align:left;'>Lugar Entrega : " . $ordenCompra ['lugar_entrega'] . "</td>	
			<td style='width:50%;text-align:left;'>Destino :  " . $destino [0] . "</td>				
			</tr>
			<tr>
			<td style='width:50%;text-align:left;'>Tiempo Entrega : " . $ordenCompra ['tiempo_entrega'] . " dias </td>	
			<td style='width:50%;text-align:left;'>Forma de Pago :  " . $forma_pago [0] . "</td>				
			</tr>							
			</table>
			<table style='width:100%;'>		
			<tr>
			<td style='width:100%;text-align:left;'>Supervisor :  " . $ordenCompra ['supervision'] . "</td>				
			</tr>
			<tr>
			<td style='width:100%;text-align:left;'> Inhabilidades y/o Incompatibilidades :  " . $ordenCompra ['inhabilidades'] . "</td>				
			</tr>
			<tr>
			<td style='width:100%;text-align:left;'><b>NOTA: SI DENTRO DE LOS TRES (3) DIAS HABILES SIGUIENTES AL RECIBO DE LA PRESENTE ORDEN DE COMPRA, ESTA UNIVERSIDAD NO RECIBE OBSERVACIONES POR PARTE DEL PROVEEDOR, SE ENTENDERAN ACEPTADAS TODAS Y CADA UNA DE LAS OBLIGACIONES Y CONDICIONES AQUÍ PACTADAS</b></td>				
			</tr>		
			</table>
			<br>
			<br>
			<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>		
			<tr>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>_______________________________</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>_______________________________</td>						
			</tr>
			<tr>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>FIRMA CONTRATISTA</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>" . $ordenador[1]. "</td>
			</tr>
			<tr>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>NOMBRE: " . $proveedor[0] . "</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>ORDENADOR GASTO</td>
			</tr>
			<tr>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>C.C: " . $proveedor[1]. "</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . $ordenador[0]. "</td>
			</tr>							
			</table>
<page_footer  backleft='10mm' backright='10mm'>
			<table style='width:100%;'>		
			<tr>
			<td style='width:100%;text-align:left;'><font size='1px'>Para el respectivo pago deberá radicar en la sección de compras la factura o cuenta de cobro, pago de aportes para fiscales o planilla de pago si es el caso, certificación bancaria indicando tipo y numero de cuenta y el cumplido a satisfacción del bien debidamente firmado por el supervisor del contrato o el funcionario quien recibe el bien.</font></td>	
			</tr>
			</table>
			<table style='width:100%;'>		
			<tr>
			<td style='width:50%;text-align:left;'>Proveedor : </td>
			<td style='width:50%;text-align:left;'>Nombre de quien recibe : </td>			
			</tr>
			</table>
			<table style='width:100%;'>		
			<tr>
			<td style='width:33.31%;'>Firma : </td>
			<td style='width:33.31%;'>Fecha : </td>
			<td style='width:33.31%;'>Sello : </td>			
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
$html2pdf = new \HTML2PDF ( 'P', 'LETTER', 'es', true, 'UTF-8' );
$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
$html2pdf->WriteHTML ( $textos );

$html2pdf->Output ( 'Compra_Nro_'.$_REQUEST['orden_compra_consulta'].'_'.date ( "Y-m-d" ).'.pdf', 'D' );

?>





