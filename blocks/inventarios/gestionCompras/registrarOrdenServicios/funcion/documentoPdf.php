<?php

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
		

		$conexion = "sicapital";
		$esteRecursoDBO = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrdenServicios', $_REQUEST ['numero_orden'] );
		$orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$orden = $orden [0];
		
		
		
		
		
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacionPresupuestal', $orden ['info_presupuestal'] );
		$info_presupuestal = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$info_presupuestal = $info_presupuestal [0];
		

		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarRubro', $orden ['rubro'] );
		$rubro = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$rubro = $rubro [0];

		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarDependencia', $orden ['dependencia_solicitante'] );
		$dependencia_solicitante = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$dependencia_solicitante = $dependencia_solicitante [0];

		
		
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarSupervisor', $orden ['id_supervisor'] );
		$supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$supervisor = $supervisor [0];
		
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarDependenciaSupervisor', $supervisor ['dependencia'] );
		$dependencia_supervisor = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$dependencia_supervisor = $dependencia_supervisor [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCosntraistaServicios', $orden['id_contratista'] );
		$datosContratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
	
		$datosContratista=$datosContratista[0];

		
		$cadenaSql = $this->miSql->getCadenaSql ( 'polizas' );
		$polizas = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$polizas = $polizas [0];
		
		$poliza1 = ($orden ['poliza1'] != 'f') ? 'X' : ' ';
		$poliza2 = ($orden ['poliza2'] != 'f') ? 'X' : ' ';
		$poliza3 = ($orden ['poliza3'] != 'f') ? 'X' : ' ';
		$poliza4 = ($orden ['poliza4'] != 'f') ? 'X' : ' ';
		

		
// 		$arreglo = array (
// 				$orden ['id_contratista_encargado'],
// 				$orden ['vig_contratista'] 
// 		);
// 		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarContratista', $arreglo );
// 		$contratista = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
// 		$contratista = $contratista [0];
		
		
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrdenador_gasto', $orden['id_ordenador_encargado'] );
		$ordenador = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$ordenador = $ordenador [0];
		

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
				
				
<page backtop='5mm' backbottom='5mm' backleft='10mm' backright='10mm'>
	

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
			<td style='width:50%;'>ORDEN DE SERVICIOS Nro.  " . $_REQUEST ['numero_orden'] . "</td>
			<td style='width:50%;text-aling=right;'>FECHA DE ORDEN :  " . $orden ['fecha_registro'] . "</td> 			
 		 	</tr>
		    </table>
						
		    <table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Información Solicitante</b></td>
			</tr>
         	</table>


		    <table style='width:100%;'>
			<tr> 
			<td style='width:50%;'>Dependencia : ".$dependencia_solicitante[1]." </td>
			<td style='width:50%;'>Rubro: ".$rubro[0]."</td>		
			</tr>
			</table>	

			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Datos Supervisor</b></td>
			</tr>
         	</table>

			<table style='width:100%;'>		
			<tr> 
			<td style='width:50%;'>Nombre : ".$supervisor['nombre']." </td>
			<td style='width:50%;'>Cargo : ".$supervisor['cargo']." </td>
			</tr>
			</table>
					
		    <table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Dependencia : ".rtrim($dependencia_supervisor[1])." </td>
			</tr>						
			</table>	

					
					
					
					
			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Información Contratista</b></td>
			</tr>
         	</table>	

            <table style='width:100%;'>
			<tr> 
			<td style='width:50%;'>Nombre o Razón Social : ".$datosContratista['nombre_razon_social']." </td>
			<td style='width:50%;'>Cedula o Nit : ".$datosContratista['identificacion']." </td>
			</tr>
			<tr> 
			<td style='width:50%;'>Dirección : ".$datosContratista['direccion']." </td>
			<td style='width:50%;'>Telefono : ".$datosContratista['telefono']." </td>
			</tr>		
			</table>	

			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Cargo : ".$datosContratista['cargo']."</td>
			</tr>
         	</table>			
					
			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Información Contrato</b></td>
			</tr>
         	</table>	

			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Objeto General : </td>
			</tr>
			<tr> 
			<td style='width:100%;text-align:justify;font-size: 8px;font-size-adjust: 0.3;'>".$orden[4]." </td>
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
			</table>	
					
		    <table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Información Referente Pago</b></td>
			</tr>
         	</table>	             		

		    <table style='width:100%;'>
			<tr> 
			<td style='width:33.31%;'>Fecha Inicio:  ".$orden['fecha_inicio_pago']."</td>
			<td style='width:33.31%;'>Fecha Final:  ".$orden['fecha_final_pago']."</td>
			<td style='width:33.31%;'>Duración (en Dias):  ".$orden['duracion_pago']."</td>		
			</tr>
         	</table>	 

            <table style='width:100%;'>
			<tr> 
			<td style='width:100%;text-align:justify;'>Forma de Pago :  ".$orden['forma_pago']."</td>
			</tr>
         	</table>

			<table style='width:100%;'>
			<tr> 
			<td style='width:33.31%;'>Total Preliminar :  $ ".$orden['total_preliminar']."</td>
			<td style='width:33.31%;'>Total Iva :  $ ".$orden['iva']."</td>
			<td style='width:33.31%;'>Total :  $ ".$orden['total']."</td>		
			</tr>
         	</table>	 		
			
			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Información Respaldo Presupuestal</b></td>
			</tr>
         	</table>

			<table style='width:100%;'>
			<tr> 
			<td style='width:50%;'>Vigencia (Disponibilidad) :   ".$info_presupuestal['vigencia_dispo']."</td>
			<td style='width:50%;'>Número (Disponibilidad) :   ".$info_presupuestal['numero_dispo']."</td>
			</tr>
			<tr> 
			<td style='width:50%;'>Valor (Disponibilidad) :   $  ".$info_presupuestal['valor_disp']."</td>
			<td style='width:50%;'>Fecha (Disponibilidad) :   ".$info_presupuestal['fecha_dip']."</td>
			</tr>		
         	</table>		
					
    		<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Valor en Letras (Disponibilidad) :  ".$info_presupuestal['letras_dispo']."</td>
			</tr>
         	</table>		

			<table style='width:100%;'>
			<tr> 
			<td style='width:50%;'>Vigencia (Registro) :   ".$info_presupuestal['vigencia_regis']."</td>
			<td style='width:50%;'>Número (Registro) :   ".$info_presupuestal['numero_regis']."</td>
			</tr>
			<tr> 
			<td style='width:50%;'>Valor (Registro) :  $  ".$info_presupuestal['valor_regis']."</td>
			<td style='width:50%;'>Fecha (Registro) :   ".$info_presupuestal['fecha_regis']."</td>
			</tr>		
         	</table>		
					
    		<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Valor en Letras (Registro) :  ".$info_presupuestal['letras_regis']."</td>
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
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>NOMBRE: " . $datosContratista['nombre_razon_social'] . "</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>ORDENADOR GASTO</td>
			</tr>
			<tr>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>C.C: " .$datosContratista['identificacion']. "</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . $ordenador[0]. "</td>
			</tr>							
			</table>		
					
					
<page_footer  backleft='10mm' backright='10mm'>
			<table style='width:100%;'>		
			<tr>
			<td style='width:100%;text-align:justify;'><font size='1px'>Observaciones: para el respectivo pago la factura y/o cuenta de cobro debe coincidir en valores, cantidades y razón social, con la presente orden de servicio. igualmente se debe anexar el recibido a satisfacción del servicio, pago de aportes parafiscal y/o seguridad social del mes de facturación y certificación bancaria con el numero de cuenta para realizar la transferencia bancaria.</font></td>	
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

$html2pdf->Output ( 'Servicios_Nro_'.$_REQUEST['numero_orden'].'_'.date ( "Y-m-d" ).'.pdf', 'D' );

?>





