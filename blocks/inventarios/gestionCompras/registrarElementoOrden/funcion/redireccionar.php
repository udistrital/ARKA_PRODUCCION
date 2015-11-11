<?

namespace inventarios\gestionActa\registrarElementoOrden\funcion;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("index.php");
	exit ();
} 
class redireccion {
	public static function redireccionar($opcion, $valor = "", $valor1 = "") {
		$miConfigurador = \Configurador::singleton ();
		$miPaginaActual = $miConfigurador->getVariableConfiguracion ( "pagina" );
		
		switch ($opcion) {
			case "datosVacios" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=datosVacios";
				$variable .= "&id_orden=" . $valor [1];
				$variable .= "&mensaje_titulo=" . $valor [0];
				$variable .= "&fecha_orden=" . $valor [2];
				
				if ($valor [3] == '') {
					$variable .= "&registroOrden=true";
				} else {
					
					$variable .= "&arreglo=" . $valor [3];
				}
				
				break;
			
			case "inserto" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=cargarElemento";
				$variable .= "&mensaje=registro";
				$variable .= "&mensaje_titulo=" . $valor [0];
				$variable .= "&id_orden=" . $valor [1];
				$variable .= "&fecha_orden=" . $valor [2];
				if ($valor [3] == '\'true\'') {
					$variable .= "&registroOrden=true";
				} else {
					
					$variable .= "&arreglo=" . $valor [3];
				}
				$variable .= "&usuario=" . $valor [4];
				
				break;
			
			case "inserto_cargue_masivo" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=confirmaMasivo";
				$variable .= "&id_orden=" . $valor [1];
				$variable .= "&mensaje_titulo=" . $valor [0];
				$variable .= "&fecha_orden=" . $valor [2];
				$variable .= "&usuario=" . $valor [4];
				
				if ($valor [3] == '') {
					$variable .= "&registroOrden=true";
				} else {
					
					$variable .= "&arreglo=" . $valor [3];
				}
				break;
			
			case "noFormatoImagen" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=noFormatoImagen";
				$variable .= "&usuario=".$valor;
				break;
			
			case "noExtension" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=noExtension";
				break;
			
			case "noArchivoCarga" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=noArchivoCarga";
				break;
			
			case "noInsertoMasivo" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=error";
				$variable .= "&id_orden=" . $valor [1];
				$variable .= "&mensaje_titulo=" . $valor [0];
				// $variable .= "&fecha_orden=" . $valor [2];
				
				if ($valor [3] == '') {
					$variable .= "&registroOrden=true";
				} else {
					
					$variable .= "&arreglo=" . $valor [3];
				}
				
				break;
			
			case "noInserto" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=cargarElemento";
				$variable .= "&mensaje=error";
				$variable .= "&mensaje_titulo=" . $valor [0];
				$variable .= "&id_orden=" . $valor [1];
				
				break;
			
			case "noCargarElemento" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=noElemento";
				
				break;
			
			case "notextos" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=otros";
				$variable .= "&errores=notextos";
				
				break;
			
			case "Salir" :
				
				$variable = "pagina=indexAlmacen";
				
				break;
			
			case "SalidaElemento" :
				
				$variable = "pagina=registrarSalidas";
				$variable .= "&opcion=Salida";
				$variable .= "&numero_entrada=" . $valor;
				$variable .= "&datosGenerales=" . $valor1;
				break;
			
			case "RegistrarActa" :
				
				$variable = "pagina=registrarActa";
				$variable .= "&opcion=asociarActa";
				$variable .= "&mensaje_titulo=" . $valor ['mensaje_titulo'];
				$variable .= "&numero_orden=" . $valor ['id_orden'];
				$variable .= "&fecha_orden=" . date ( 'Y-m-d' );
				break;
		}
		
		foreach ( $_REQUEST as $clave => $valor ) {
			unset ( $_REQUEST [$clave] );
		}
		
		$url = $miConfigurador->configuracion ["host"] . $miConfigurador->configuracion ["site"] . "/index.php?";
		$enlace = $miConfigurador->configuracion ['enlace'];
		$variable = $miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
		$_REQUEST [$enlace] = $enlace . '=' . $variable;
		$redireccion = $url . $_REQUEST [$enlace];
		
		echo "<script>location.replace('" . $redireccion . "')</script>";
		
		// $enlace =$miConfigurador->getVariableConfiguracion("enlace");
		// $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);
		// // echo $enlace;
		// // // echo $variable;
		// // exit;
		// $_REQUEST[$enlace] = $variable;
		// $_REQUEST["recargar"] = true;
		// return true;
	}
}

?>