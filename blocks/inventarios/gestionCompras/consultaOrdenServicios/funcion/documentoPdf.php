<?php
$ruta = $this->miConfigurador->getVariableConfiguracion ( "raizDocumento" );

$host = $this->miConfigurador->getVariableConfiguracion ( "host" ) . $this->miConfigurador->getVariableConfiguracion ( "site" ) . "/plugin/html2pfd/";

include ($ruta . "/plugin/html2pdf/html2pdf.class.php");

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("../index.php");
	exit ();
}
class EnLetras {
	var $Void = "";
	var $SP = " ";
	var $Dot = ".";
	var $Zero = "0";
	var $Neg = "Menos";
	function ValorEnLetras($x, $Moneda) {
		$s = "";
		$Ent = "";
		$Frc = "";
		$Signo = "";
		
		if (floatVal ( $x ) < 0)
			$Signo = $this->Neg . " ";
		else
			$Signo = "";
		
		if (intval ( number_format ( $x, 2, '.', '' ) ) != $x) // <- averiguar si tiene decimales
			$s = number_format ( $x, 2, '.', '' );
		else
			$s = number_format ( $x, 0, '.', '' );
		
		$Pto = strpos ( $s, $this->Dot );
		
		if ($Pto === false) {
			$Ent = $s;
			$Frc = $this->Void;
		} else {
			$Ent = substr ( $s, 0, $Pto );
			$Frc = substr ( $s, $Pto + 1 );
		}
		
		if ($Ent == $this->Zero || $Ent == $this->Void)
			$s = "Cero ";
		elseif (strlen ( $Ent ) > 7) {
			$s = $this->SubValLetra ( intval ( substr ( $Ent, 0, strlen ( $Ent ) - 6 ) ) ) . "Millones " . $this->SubValLetra ( intval ( substr ( $Ent, - 6, 6 ) ) );
		} else {
			$s = $this->SubValLetra ( intval ( $Ent ) );
		}
		
		if (substr ( $s, - 9, 9 ) == "Millones " || substr ( $s, - 7, 7 ) == "Millón ")
			$s = $s . "de ";
		
		$s = $s . $Moneda;
		
		if ($Frc != $this->Void) {
			$s = $s . " Con " . $this->SubValLetra ( intval ( $Frc ) ) . "Centavos";
			// $s = $s . " " . $Frc . "/100";
		}
		return ($Signo . $s . "");
	}
	function SubValLetra($numero) {
		$Ptr = "";
		$n = 0;
		$i = 0;
		$x = "";
		$Rtn = "";
		$Tem = "";
		
		$x = trim ( "$numero" );
		$n = strlen ( $x );
		
		$Tem = $this->Void;
		$i = $n;
		
		while ( $i > 0 ) {
			$Tem = $this->Parte ( intval ( substr ( $x, $n - $i, 1 ) . str_repeat ( $this->Zero, $i - 1 ) ) );
			If ($Tem != "Cero")
				$Rtn .= $Tem . $this->SP;
			$i = $i - 1;
		}
		
		// --------------------- GoSub FiltroMil ------------------------------
		$Rtn = str_replace ( " Mil Mil", " Un Mil", $Rtn );
		while ( 1 ) {
			$Ptr = strpos ( $Rtn, "Mil " );
			If (! ($Ptr === false)) {
				If (! (strpos ( $Rtn, "Mil ", $Ptr + 1 ) === false))
					$this->ReplaceStringFrom ( $Rtn, "Mil ", "", $Ptr );
				else
					break;
			} else
				break;
		}
		
		// --------------------- GoSub FiltroCiento ------------------------------
		$Ptr = - 1;
		do {
			$Ptr = strpos ( $Rtn, "Cien ", $Ptr + 1 );
			if (! ($Ptr === false)) {
				$Tem = substr ( $Rtn, $Ptr + 5, 1 );
				if ($Tem == "M" || $Tem == $this->Void)
					;
				else
					$this->ReplaceStringFrom ( $Rtn, "Cien", "Ciento", $Ptr );
			}
		} while ( ! ($Ptr === false) );
		
		// --------------------- FiltroEspeciales ------------------------------
		$Rtn = str_replace ( "Diez Un", "Once", $Rtn );
		$Rtn = str_replace ( "Diez Dos", "Doce", $Rtn );
		$Rtn = str_replace ( "Diez Tres", "Trece", $Rtn );
		$Rtn = str_replace ( "Diez Cuatro", "Catorce", $Rtn );
		$Rtn = str_replace ( "Diez Cinco", "Quince", $Rtn );
		$Rtn = str_replace ( "Diez Seis", "Dieciseis", $Rtn );
		$Rtn = str_replace ( "Diez Siete", "Diecisiete", $Rtn );
		$Rtn = str_replace ( "Diez Ocho", "Dieciocho", $Rtn );
		$Rtn = str_replace ( "Diez Nueve", "Diecinueve", $Rtn );
		$Rtn = str_replace ( "Veinte Un", "Veintiun", $Rtn );
		$Rtn = str_replace ( "Veinte Dos", "Veintidos", $Rtn );
		$Rtn = str_replace ( "Veinte Tres", "Veintitres", $Rtn );
		$Rtn = str_replace ( "Veinte Cuatro", "Veinticuatro", $Rtn );
		$Rtn = str_replace ( "Veinte Cinco", "Veinticinco", $Rtn );
		$Rtn = str_replace ( "Veinte Seis", "Veintiseís", $Rtn );
		$Rtn = str_replace ( "Veinte Siete", "Veintisiete", $Rtn );
		$Rtn = str_replace ( "Veinte Ocho", "Veintiocho", $Rtn );
		$Rtn = str_replace ( "Veinte Nueve", "Veintinueve", $Rtn );
		
		// --------------------- FiltroUn ------------------------------
		If (substr ( $Rtn, 0, 1 ) == "M")
			$Rtn = "Un " . $Rtn;
			// --------------------- Adicionar Y ------------------------------
		for($i = 65; $i <= 88; $i ++) {
			If ($i != 77)
				$Rtn = str_replace ( "a " . Chr ( $i ), "* y " . Chr ( $i ), $Rtn );
		}
		$Rtn = str_replace ( "*", "a", $Rtn );
		return ($Rtn);
	}
	function ReplaceStringFrom(&$x, $OldWrd, $NewWrd, $Ptr) {
		$x = substr ( $x, 0, $Ptr ) . $NewWrd . substr ( $x, strlen ( $OldWrd ) + $Ptr );
	}
	function Parte($x) {
		$Rtn = '';
		$t = '';
		$i = '';
		Do {
			switch ($x) {
				Case 0 :
					$t = "Cero";
					break;
				Case 1 :
					$t = "Un";
					break;
				Case 2 :
					$t = "Dos";
					break;
				Case 3 :
					$t = "Tres";
					break;
				Case 4 :
					$t = "Cuatro";
					break;
				Case 5 :
					$t = "Cinco";
					break;
				Case 6 :
					$t = "Seis";
					break;
				Case 7 :
					$t = "Siete";
					break;
				Case 8 :
					$t = "Ocho";
					break;
				Case 9 :
					$t = "Nueve";
					break;
				Case 10 :
					$t = "Diez";
					break;
				Case 20 :
					$t = "Veinte";
					break;
				Case 30 :
					$t = "Treinta";
					break;
				Case 40 :
					$t = "Cuarenta";
					break;
				Case 50 :
					$t = "Cincuenta";
					break;
				Case 60 :
					$t = "Sesenta";
					break;
				Case 70 :
					$t = "Setenta";
					break;
				Case 80 :
					$t = "Ochenta";
					break;
				Case 90 :
					$t = "Noventa";
					break;
				Case 100 :
					$t = "Cien";
					break;
				Case 200 :
					$t = "Doscientos";
					break;
				Case 300 :
					$t = "Trescientos";
					break;
				Case 400 :
					$t = "Cuatrocientos";
					break;
				Case 500 :
					$t = "Quinientos";
					break;
				Case 600 :
					$t = "Seiscientos";
					break;
				Case 700 :
					$t = "Setecientos";
					break;
				Case 800 :
					$t = "Ochocientos";
					break;
				Case 900 :
					$t = "Novecientos";
					break;
				Case 1000 :
					$t = "Mil";
					break;
				Case 1000000 :
					$t = "Millón";
					break;
			}
			
			If ($t == $this->Void) {
				$i = $i + 1;
				$x = $x / 1000;
				If ($x == 0)
					$i = 0;
			} else
				break;
		} while ( $i != 0 );
		
		$Rtn = $t;
		Switch ($i) {
			Case 0 :
				$t = $this->Void;
				break;
			Case 1 :
				$t = " Mil";
				break;
			Case 2 :
				$t = " Millones";
				break;
			Case 3 :
				$t = " Billones";
				break;
		}
		return ($Rtn . $t);
	}
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
	function tipo_orden() {
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrdenDocumento', $_REQUEST ['id_orden'] );
		
		$orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		// var_dump ( $orden );
		$orden = $orden [0];
		
		$tipo_orden = ($orden ['tipo_orden'] == '1') ? "ORDEN DE COMPRA" : (($orden ['tipo_orden']) ? "ORDEN DE SERVICIOS" : " ");
		$tipo_orden .= "  " . $orden ['identificador_consecutivo'];
		
		return $tipo_orden;
	}
	function documento() {
		
		$conexion = "inventarios";
		$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
		
		$directorio = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrdenDocumento', $_REQUEST ['id_orden'] );
		
		$orden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		// var_dump ( $orden );
		$orden = $orden [0];
		
		$tipo_orden = ($orden ['tipo_orden'] == '1') ? "ORDEN DE COMPRA" : (($orden ['tipo_orden']) ? "ORDEN DE SERVICIOS" : " ");
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'informacionPresupuestal', $orden ['info_presupuestal'] );
		$info_presupuestal = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$info_presupuestal = $info_presupuestal [0];
		
		// var_dump ( $info_presupuestal );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarRubro', $orden ['rubro'] );
		$rubro = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$rubro = $rubro [0];
		
		// var_dump ( $rubro );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarSupervisor', $orden ['id_supervisor'] );
		$supervisor = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$supervisor = $supervisor [0];
		
		// var_dump ( $supervisor );
		
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultarDependenciaSupervisor', $supervisor ['dependencia'] );
		// $dependencia_supervisor = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		// $dependencia_supervisor = $dependencia_supervisor [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarCosntraistaServicios', $orden ['id_contratista'] );
		$datosContratista = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		$datosContratista = $datosContratista [0];
		
		// var_dump ( $datosContratista );
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'polizas' );
		$polizas = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$polizas = $polizas [0];
		
		$poliza1 = ($orden ['poliza1'] != 'f') ? 'X' : ' ';
		$poliza2 = ($orden ['poliza2'] != 'f') ? 'X' : ' ';
		$poliza3 = ($orden ['poliza3'] != 'f') ? 'X' : ' ';
		$poliza4 = ($orden ['poliza4'] != 'f') ? 'X' : ' ';
		
		// $arreglo = array (
		// $orden ['id_contratista_encargado'],
		// $orden ['vig_contratista']
		// );
		// $cadenaSql = $this->miSql->getCadenaSql ( 'consultarContratistaDocumento', $arreglo );
		// $contratista = $esteRecursoDBO->ejecutarAcceso ( $cadenaSql, "busqueda" );
		// $contratista = $contratista [0];
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarOrdenador_gasto', array (
				$orden ['id_ordenador_encargado'],
				$orden ['tipo_ordenador'] 
		) );
		
		$ordenador = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		$ordenador = $ordenador [0];
		// var_dump($ordenador);
		
		$cadenaSql = $this->miSql->getCadenaSql ( 'consultarElementosOrden', $_REQUEST ['id_orden'] );
		
		$ElementosOrden = $esteRecursoDB->ejecutarAcceso ( $cadenaSql, "busqueda" );
		
		// var_dump($ElementosOrden);exit;
		
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
			<td style='width:50%;'>" . $tipo_orden . ": " . $orden ['identificador_consecutivo'] . "</td>
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
			<td style='width:50%;'>Dependencia : " . $orden ['nombre_dependencia'] . " </td>
			<td style='width:50%;'>Rubro: " . $rubro [0] . "</td>		
			</tr>
			</table>	

			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Datos Supervisor</b></td>
			</tr>
         	</table>

			<table style='width:100%;'>		
			<tr> 
			<td style='width:50%;'>Nombre : " . $supervisor ['nombre'] . " </td>
			<td style='width:50%;'>Cargo : " . $supervisor ['cargo'] . " </td>
			</tr>
			</table>
					
		    <table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Dependencia : " . rtrim ( $supervisor ['nombre_dependencia'] ) . " </td>
			</tr>						
			</table>	

					
					
					
					
			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Información Contratista</b></td>
			</tr>
         	</table>	

            <table style='width:100%;'>
			<tr> 
			<td style='width:50%;'>Nombre o Razón Social : " . $datosContratista ['nombre_razon_social'] . " </td>
			<td style='width:50%;'>Cedula o Nit : " . $datosContratista ['identificacion'] . " </td>
			</tr>
			<tr> 
			<td style='width:50%;'>Dirección : " . $datosContratista ['direccion'] . " </td>
			<td style='width:50%;'>Telefono : " . $datosContratista ['telefono'] . " </td>
			</tr>		
			</table>	

			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Cargo : " . $datosContratista ['cargo'] . "</td>
			</tr>
         	</table>			
					
			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Información Contrato</b></td>
			</tr>
         	</table>	

			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;text-align:justify;font-size: 8px;font-size-adjust: 0.3;'>" . $orden ['objeto_contrato'] . " </td>
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
			";
		
		$contenidoPagina .= "<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Información Referente Pago</b></td>
			</tr>
         	</table>	             		

		    <table style='width:100%;'>
			<tr> 
			<td style='width:33.31%;'>Fecha Inicio:  " . $orden ['fecha_inicio_pago'] . "</td>
			<td style='width:33.31%;'>Fecha Final:  " . $orden ['fecha_final_pago'] . "</td>
			<td style='width:33.31%;'>Duración (en Dias):  " . $orden ['duracion_pago'] . "</td>		
			</tr>
         	</table>	 

            <table style='width:100%;'>
			<tr> 
			<td style='width:100%;text-align:justify;'>Forma de Pago :  " . $orden ['forma_pago'] . "</td>
			</tr>
         	</table>


			
			<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'><b>Información Respaldo Presupuestal</b></td>
			</tr>
         	</table>

			<table style='width:100%;'>
			<tr> 
			<td style='width:50%;'>Vigencia (Disponibilidad) :   " . $info_presupuestal ['vigencia_dispo'] . "</td>
			<td style='width:50%;'>Número (Disponibilidad) :   " . $info_presupuestal ['numero_dispo'] . "</td>
			</tr>
			<tr> 
			<td style='width:50%;'>Valor (Disponibilidad) :   $  " . $info_presupuestal ['valor_disp'] . "</td>
			<td style='width:50%;'>Fecha (Disponibilidad) :   " . $info_presupuestal ['fecha_dip'] . "</td>
			</tr>		
         	</table>		
					
    		<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Valor en Letras (Disponibilidad) :  " . $info_presupuestal ['letras_dispo'] . "</td>
			</tr>
         	</table>		

			<table style='width:100%;'>
			<tr> 
			<td style='width:50%;'>Vigencia (Registro) :   " . $info_presupuestal ['vigencia_regis'] . "</td>
			<td style='width:50%;'>Número (Registro) :   " . $info_presupuestal ['numero_regis'] . "</td>
			</tr>
			<tr> 
			<td style='width:50%;'>Valor (Registro) :  $  " . $info_presupuestal ['valor_regis'] . "</td>
			<td style='width:50%;'>Fecha (Registro) :   " . $info_presupuestal ['fecha_regis'] . "</td>
			</tr>		
         	</table>		
					
    		<table style='width:100%;'>
			<tr> 
			<td style='width:100%;'>Valor en Letras (Registro) :  " . $info_presupuestal ['letras_regis'] . "</td>
			</tr>
         	</table>		

			<br>
			<br>		
			
					
<page_footer  backleft='10mm' backright='10mm'>
			<table style='width:100%;'>		
			<tr>
			<td style='width:100%;text-align:justify;'><font size='1px'>Observaciones: para el respectivo pago la factura y/o cuenta de cobro debe coincidir en valores, cantidades y razón social, con la presente orden de servicio. igualmente se debe anexar el recibido a satisfacción del servicio, pago de aportes parafiscal y/o seguridad social del mes de facturación y certificación bancaria con el numero de cuenta para realizar la transferencia bancaria.</font></td>	
			</tr>
			</table>
		
</page_footer> 
					
						</page>
				";
		
		$contenidoPagina .= "<page backtop='5mm' backbottom='5mm' backleft='10mm' backright='10mm'>";
		
		$contenidoPagina .= "
		<table style='width:100%;'>
		<tr>
		<td style='width:100%;text-align=center;'>Elementos Orden</td>
		</tr>
		</table>
		<table style='width:100%;'>
		<tr>
		<td style='width:10%;text-align=center;'>Item</td>
		<td style='width:15%;text-align=center;'>Unidad/Medida</td>
		<td style='width:20%;text-align=center;'>Cantidad</td>
		<td style='width:30%;text-align=center;'>Descripción</td>
		<td style='width:8.3%;text-align=center;'>Valor Unitario($)</td>
		<td style='width:8.3%;text-align=center;'>Iva</td>
		<td style='width:8.3%;text-align=center;'>Total</td>
		</tr>
		</table>
		<table style='width:100%;'>";
		
		$sumatoriaTotal = 0;
		
		$sumatoriaIva = 0;
		$sumatoriaSubtotal = 0;
		$j = 1;
		
		// var_dump ( $ElementosOrden );
		// exit ();
		
		foreach ( $ElementosOrden as $valor => $it ) {
			$contenidoPagina .= "<tr>";
			$contenidoPagina .= "<td style='width:10%;text-align=center;'>" . $j . "</td>";
			$contenidoPagina .= "<td style='width:15%;text-align=center;'>" . $it ['unidad'] . "</td>";
			$contenidoPagina .= "<td style='width:20%;text-align=center;'>" . $it ['cantidad'] . "</td>";
			$contenidoPagina .= "<td style='width:30%;text-align=justify;'>" . $it ['descripcion'] . "</td>";
			$contenidoPagina .= "<td style='width:8.3%;text-align=center;'>$ " . $it ['valor'] . "</td>";
			$contenidoPagina .= "<td style='width:8.3%;text-align=center;'>" . $it ['nombre_iva'] . "</td>";
			$contenidoPagina .= "<td style='width:8.3%;text-align=center;'>$ " . $it ['total_iva_con'] . "</td>";
			$contenidoPagina .= "</tr>";
			
			$sumatoriaTotal = $sumatoriaTotal + $it ['total_iva_con'];
			$sumatoriaSubtotal = $sumatoriaSubtotal + $it ['subtotal_sin_iva'];
			$sumatoriaIva = $sumatoriaIva + $it ['total_iva'];
			$j ++;
		}
		
		$contenidoPagina .= "</table>";
		
		$contenidoPagina .= "		<table style='width:100%;'>
		<tr>
		
		<td style='width:91.7%;text-align=left;'><b>SUBTOTAL  : </b></td>
		<td style='width:8.3%;text-align=center;'><b>$" . $sumatoriaSubtotal . "</b></td>
		</tr>
		<tr>
		
		<td style='width:91.7%;text-align=left;'><b>TOTAL IVA  : </b></td>
		<td style='width:8.3%;text-align=center;'><b>$" . $sumatoriaIva . "</b></td>
		</tr>			
				
		<tr>
		
		<td style='width:91.7%;text-align=left;'><b>TOTAL  : </b></td>
		<td style='width:8.3%;text-align=center;'><b>$" . $sumatoriaTotal . "</b></td>
		</tr>
				
				
	</table>			
				";
		
		$funcionLetras = new EnLetras ();
		
		$Letras = $funcionLetras->ValorEnLetras ( $sumatoriaTotal, ' Pesos ' );
		
		$contenidoPagina .= "<table style='width:100%;'>			
		<tr>
		
		<td style='width:100%;text-align=center;text-transform:uppercase;'><b>" . $Letras . "</b></td>
		</tr>		
		
		</table>";
		
		$contenidoPagina .= "<page_footer  backleft='10mm' backright='10mm'>
				
				
						<table style='width:100%; background:#FFFFFF ; border: 0px  #FFFFFF;'>
			<tr>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>_______________________________</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>_______________________________</td>
			</tr>
			<tr>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>FIRMA CONTRATISTA</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>" . $ordenador [1] . "</td>
			</tr>
			<tr>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF; text-transform:capitalize;'>NOMBRE: " . $datosContratista ['nombre_razon_social'] . "</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>ORDENADOR GASTO</td>
			</tr>
			<tr>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>C.C: " . $datosContratista ['identificacion'] . "</td>
			<td style='width:50%;text-align:left;background:#FFFFFF ; border: 0px  #FFFFFF;'>" . $ordenador [0] . "</td>
			</tr>
			</table>
							
				
												<table style='width:100%;'>		
												<tr>
												<td style='width:100%;text-align:justify;'><font size='1px'>Observaciones: para el respectivo pago la factura y/o cuenta de cobro debe coincidir en valores, cantidades y razón social, con la presente orden de servicio. igualmente se debe anexar el recibido a satisfacción del servicio, pago de aportes parafiscal y/o seguridad social del mes de facturación y certificación bancaria con el numero de cuenta para realizar la transferencia bancaria.</font></td>	
												</tr>
												</table>
											</page_footer> 
				</page>";
		
		// echo $contenidoPagina;exit;
		return $contenidoPagina;
	}
}

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$textos = $miRegistrador->documento ();
$tipo_orden = $miRegistrador->tipo_orden ();

ob_start ();
$html2pdf = new \HTML2PDF ( 'P', 'LETTER', 'es', true, 'UTF-8' );
$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
$html2pdf->WriteHTML ( $textos );

$html2pdf->Output ( $tipo_orden . '.pdf', 'D' );

?>





