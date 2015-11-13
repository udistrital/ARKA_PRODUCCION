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
		$placas = unserialize ( $_REQUEST ['placas'] );
		$directorio = $this->miConfigurador->getVariableConfiguracion ( 'rutaUrlBloque' );
		
		$contenidoPagina = "
<style type=\"text/css\">
    table { 
        color:#333; /* Lighten up font color */
        font-family:Helvetica, Arial, sans-serif; /* Nicer font */
			border-collapse: separate;
		border-spacing: 11px;	
         
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
</style>";
		
		switch ($_REQUEST ['tipo_impresion']) {
			
			case '1' :
				
				$contadorPagina = 1;
				$contadorSaltoLinea = 1;
				$numeroTotal = count ( $placas );
				
				$iterador = 1;
				
				$numeroVeces = $numeroTotal / 39;
				$numeroVeces = round ( $numeroVeces, 0, PHP_ROUND_HALF_DOWN );
				
				$num = $numeroTotal - (39 * $numeroVeces);
				
				$i = 1;
				foreach ( $placas as $numero ) {
					
					$PLACASTOTALES [$i] = $numero;
					$i ++;
				}
				
				$contenidoPagina .= "<page backtop='0mm' backbottom='0mm' backleft='0mm' backright='0mm' pagegroup='new'>
							    		<table style='width:100%;' cellpadding='1'>";
				
				foreach ( $PLACASTOTALES as $placaSencilla ) {
					
					if ($contadorSaltoLinea == 1) {
						$contenidoPagina .= "<tr>";
					}
					
					$contenidoPagina .= "<td style='width:33.31%; height: 64px; text-align=center;border-spacing: 11px;font-size: 7px;'>UNIVERSIDAD DISTRITAL<BR>FRANCISCO JOSE DE CALDAS<br><barcode type='CODABAR' value='" . $placaSencilla . "' style='width:30mm; height:4mm; font-size: 2mm'></barcode>" . '' . "<img src='" . $directorio . "/css/images/escudo2.jpeg'  width='20' height='20'><br>Inventarios</td>";
					
					if ($contadorSaltoLinea == 3) {
						
						$contenidoPagina .= "</tr>";
						$contadorSaltoLinea = 1;
					} else {
						$contadorSaltoLinea ++;
					}
					
					$contadorPagina ++;
					$iterador ++;
				}
				
				if (($contadorSaltoLinea - 1) == 0) {
					
					$contenidoPagina .= "</table>
								</page >";
				} elseif (($contadorSaltoLinea - 1) < 3) {
					
					$contenidoPagina .= "</tr></table>
								</page >";
				} else {
					
					$contenidoPagina .= "</table>
								</page >";
				}
				return $contenidoPagina;
				
				break;
			
			case '2' :
				
				$arregloposiciones = array (
						isset ( $_REQUEST [1] ),
						isset ( $_REQUEST [2] ),
						isset ( $_REQUEST [3] ),
						isset ( $_REQUEST [4] ),
						isset ( $_REQUEST [5] ),
						isset ( $_REQUEST [6] ),
						isset ( $_REQUEST [7] ),
						isset ( $_REQUEST [8] ),
						isset ( $_REQUEST [9] ),
						isset ( $_REQUEST [10] ),
						isset ( $_REQUEST [11] ),
						isset ( $_REQUEST [12] ),
						isset ( $_REQUEST [13] ),
						isset ( $_REQUEST [14] ),
						isset ( $_REQUEST [15] ),
						isset ( $_REQUEST [16] ),
						isset ( $_REQUEST [17] ),
						isset ( $_REQUEST [18] ),
						isset ( $_REQUEST [19] ),
						isset ( $_REQUEST [20] ),
						isset ( $_REQUEST [21] ),
						isset ( $_REQUEST [22] ),
						isset ( $_REQUEST [23] ),
						isset ( $_REQUEST [24] ),
						isset ( $_REQUEST [25] ),
						isset ( $_REQUEST [26] ),
						isset ( $_REQUEST [27] ),
						isset ( $_REQUEST [28] ),
						isset ( $_REQUEST [29] ),
						isset ( $_REQUEST [30] ),
						isset ( $_REQUEST [31] ),
						isset ( $_REQUEST [32] ),
						isset ( $_REQUEST [33] ),
						isset ( $_REQUEST [34] ),
						isset ( $_REQUEST [35] ),
						isset ( $_REQUEST [36] ),
						isset ( $_REQUEST [37] ),
						isset ( $_REQUEST [38] ),
						isset ( $_REQUEST [39] ) 
				);
				
				$contadorPagina = 1;
				$contadorSaltoLinea = 1;
				$numeroTotal = count ( $placas );
				
				if ($numeroTotal > 39) {
					
					$iterador = 1;
					$contadorPosicion = 1;
					$numeroVeces = $numeroTotal / 39;
					$numeroVeces = round ( $numeroVeces, 0, PHP_ROUND_HALF_DOWN );
					
					$num = $numeroTotal - (39 * $numeroVeces);
					
					$i = 1;
					foreach ( $arregloposiciones as $numero ) {
						
						$POSICIONES [$i] = $numero;
						$i ++;
					}
					
					$i = 1;
					foreach ( $placas as $numero ) {
						
						$PLACASTOTALES [$i] = $numero;
						$i ++;
					}
					
					$contenidoPagina .= "<page backtop='0mm' backbottom='0mm' backleft='0mm' backright='0mm'  pagegroup='new'>
										<table style='width:100%;' cellpadding='1'>";
					
					foreach ( $PLACASTOTALES as $placaSencilla ) {
						
						if ($contadorSaltoLinea == 1) {
							$contenidoPagina .= "<tr>";
						}
						
						if ($POSICIONES != false) {
							
							if ($POSICIONES [$contadorPosicion] == true) {
								
								$contenidoPagina .= "<td style='width:33.31%; height: 64px;text-align=center;border-spacing: 11px;'> </td><br>";
								
								$PLACASFALTANTES [] = "<td style='width:33.31%; height: 64px; text-align=center;border-spacing: 11px;font-size: 7px;'>UNIVERSIDAD DISTRITAL<BR>FRANCISCO JOSE DE CALDAS<BR><barcode type='CODABAR' value='" . $placaSencilla . "' style='width:30mm; height:4mm; font-size: 2mm'></barcode>" . '   ' . "<img src='" . $directorio . "/css/images/escudo2.jpeg'  width='20' height='20'><br>Inventarios</td>";
							} else {
								
								$contenidoPagina .= "<td style='width:33.31%; height: 64px; text-align=center;border-spacing: 11px;font-size: 7px;'>UNIVERSIDAD DISTRITAL<BR>FRANCISCO JOSE DE CALDAS<BR><barcode type='CODABAR' value='" . $placaSencilla . "' style='width:30mm; height:4mm; font-size: 2mm'></barcode>" . '   ' . "<img src='" . $directorio . "/css/images/escudo2.jpeg'  width='20' height='20'><br>Inventarios</td>";
							}
							if ($contadorPosicion == 39) {
								
								$POSICIONES = false;
							}
							$contadorPosicion ++;
						} else {
							
							$contenidoPagina .= "<td style='width:33.31%; height: 64px; text-align=center;border-spacing: 11px;font-size: 7px;'>UNIVERSIDAD DISTRITAL<BR>FRANCISCO JOSE DE CALDAS<BR><barcode type='CODABAR' value='" . $placaSencilla . "' style='width:30mm; height:4mm; font-size: 2mm'></barcode>" . '' . "<img src='" . $directorio . "/css/images/escudo2.jpeg'  width='20' height='20'><br>Inventarios</td>";
						}
						
						if ($contadorSaltoLinea == 3) {
							
							$contenidoPagina .= "</tr>";
							$contadorSaltoLinea = 1;
						} else {
							$contadorSaltoLinea ++;
						}
						
						$contadorPagina ++;
						$iterador ++;
					}
					
					$i = 1;
					foreach ( $PLACASFALTANTES as $numero ) {
						
						$PLACASTOTALES [$i] = $numero;
						$i ++;
					}
					
					foreach ( $PLACASFALTANTES as $placaSencilla ) {
						
						if ($contadorSaltoLinea == 1) {
							$contenidoPagina .= "<tr>";
						}
						
						$contenidoPagina .= $placaSencilla;
						
						if ($contadorSaltoLinea == 3) {
							
							$contenidoPagina .= "</tr>";
							$contadorSaltoLinea = 1;
						} else {
							$contadorSaltoLinea ++;
						}
						
						$contadorPagina ++;
						$iterador ++;
					}
					
					if (($contadorSaltoLinea - 1) == 0) {
						
						$contenidoPagina .= "</table>
								</page >";
					} elseif (($contadorSaltoLinea - 1) < 3) {
						
						$contenidoPagina .= "</tr></table>
								</page >";
					} else {
						
						$contenidoPagina .= "</table>
								</page >";
					}
				} else if ($numeroTotal <= 39) {
					$numeroplacas = count ( $placas );
					
					$contadorPlacas = 0;
					
					$contenidoPagina .= "<page backtop='0mm' backbottom='0mm' backleft='0mm' backright='0mm'  pagegroup='new'>
										<table style='width:100%;' cellpadding='1'>";
					
					$i = 1;
					foreach ( $arregloposiciones as $numero ) {
						
						$POSICIONES [$i] = $numero;
						$i ++;
					}
					
					$i = 1;
					
					$contadorSaltoLinea == 1;
					
					foreach ( $POSICIONES as $valor ) {
						
						if ($contadorSaltoLinea == 1) {
							$contenidoPagina .= "<tr>";
						}
						
						// echo $i . ": ";
						if ($valor == false) {
							
							if ($contadorPlacas < $numeroplacas) {
								
								$contenidoPagina .= "<td style='width:33.31%; height: 64px; text-align=center;border-spacing: 11px;font-size: 7px;'>UNIVERSIDAD DISTRITAL<BR>FRANCISCO JOSE DE CALDAS<BR><barcode type='CODABAR' value='" . $placas [$contadorPlacas] . "' style='width:30mm; height:4mm; font-size: 2mm'></barcode>" . '' . "<img src='" . $directorio . "/css/images/escudo2.jpeg'  width='20' height='20'><br>Inventarios</td>";
								
			
								$contadorPlacas ++;
							}
						} else {
							
							$contenidoPagina .= "<td style='width:33.31%; height: 64px;text-align=center;border-spacing: 11px;'> </td><br>";
						}
						
						if ($contadorSaltoLinea == 3) {
							
							$contenidoPagina .= "</tr>";
							$contadorSaltoLinea = 1;
						} else {
							$contadorSaltoLinea ++;
						}
						
						$i ++;
					}
					
					if (($contadorSaltoLinea - 1) == 0) {
						
						$contenidoPagina .= "</table>
								</page >";
					} elseif (($contadorSaltoLinea - 1) < 3) {
						
						$contenidoPagina .= "</tr></table>
								</page >";
					} else {
						
						$contenidoPagina .= "</table>
								</page >";
					}
					
				}
				
				
// 				if ($contadorPlacas < $numeroplacas) {
// 				echo $contadorPlacas." : ".$numeroplacas
// 				}
// exit;
				return $contenidoPagina;
				
				break;
		}
		
		// $contador = 1;
		// $salidacontador = 1;
		// $salida = count ( $placas );
		// $contadorposicion = 0;
		
		// foreach ( $placas as $p ) {
		// if ($contador == 1) {
		
		// $contenidoPagina .= "<tr style='border-spacing: 11px;'>";
		// }
		// if ($arregloposiciones [$contadorposicion] == true) {
		
		//
		// } else {
		
		// $contenidoPagina .= "<td style='width:33.31%; height: 64px; text-align=center;border-spacing: 11px;'>UNIVERSIDAD DISTRITAL<BR>FRANCISCO JOSE DE CALDAS<BR><barcode type='CODABAR' value='" . $p . "' style='width:30mm; height:6mm; font-size: 2mm'></barcode>" . ' ' . "<img src='" . $directorio . "/css/images/escudo2.jpeg' width='25' height='25'><br>Invetarios</td>";
		// }
		// if ($contador == 3) {
		
		// $contenidoPagina .= "</tr>";
		// $contador = 0;
		// }
		// $contador ++;
		
		// if ($salida + 1 == $salidacontador) {
		
		// $contenidoPagina .= "</tr>";
		// }
		
		// $salidacontador ++;
		// $contadorposicion ++;
		// }
		
		// $contenidoPagina .= "</table>";
		
		// $contenidoPagina .= "</page>";
		
		// echo $contenidoPagina;exit;
		// return $contenidoPagina;
	}
}

$miRegistrador = new RegistradorOrden ( $this->lenguaje, $this->sql, $this->funcion );

$textos = $miRegistrador->documento ();

ob_start ();
$html2pdf = new \HTML2PDF ( 'P', array (
		'156',
		'263' 
), 'es', true, 'UTF-8', array (
		6,
		4,
		6,
		3 
) );
$html2pdf->pdf->SetDisplayMode ( 'fullpage' );
$html2pdf->WriteHTML ( $textos );

$html2pdf->Output ( 'Placas' . '_' . date ( "Y-m-d" ) . '.pdf', 'D' );

?>





