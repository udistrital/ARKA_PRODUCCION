<?php

namespace inventarios\asignarInventarioC\gestionContratista\funcion;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("index.php");
	exit ();
}
class redireccion {
	public static function redireccionar($opcion, $valor = "", $valor1 = "") {
		$miConfigurador = \Configurador::singleton ();
		$miPaginaActual = $miConfigurador->getVariableConfiguracion ( "pagina" );
	
                switch ($opcion) {
			case "inserto" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&mensaje=registro";
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
				$variable .= "&log_error=" . $valor;
				$variable .= "&num_exito=" . $valor1;
				
				break;
			
			case "Actualizo" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&mensaje=actualizo";
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
				
				break;
			
			case "noExtension" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&mensaje=noExtension";
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
				break;
			case "datosVacios" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&mensaje=datosVacios";
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
				break;
			
			case "noInserto" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&mensaje=error";
                          	$variable .= "&log_error=" . $valor;
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
				
				break;
			
			case "NoActualizo" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&mensaje=errorActualizacion";
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
				
				break;
			
			case "ErrorTipoContrato" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&mensaje=errorTipoContrato";
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
				
				break;
			
			case "Consulta" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&vigencia=" . $_REQUEST ['vigencia'];
				$variable .= "&usuario=" . $_REQUEST ['usuario'];
				
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