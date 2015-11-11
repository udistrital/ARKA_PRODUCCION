<?

namespace inventarios\gestionEntradas\registrarEntradas\funcion;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("index.php");
	exit ();
}
class redireccion {
	public static function redireccionar($opcion, $valor = "") {
		$miConfigurador = \Configurador::singleton ();
		$miPaginaActual = $miConfigurador->getVariableConfiguracion ( "pagina" );
		
		switch ($opcion) {
			case "inserto" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=inserto";
				$variable .= "&consecutivo=" . $valor [1] . " - (" . date ( 'Y' ) . ")";
				$variable .= "&numero_entrada=" . $valor [0];
				$variable .= "&numero_acta=" . $valor [2];
				break;
			
			case "noInserto" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=noactualizo";
				$variable .= "&mensaje=error";
				
				break;
			
			case "NoObservaciones" :
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=mensaje";
				$variable .= "&mensaje=otros";
				$variable .= "&errores=notextos";
				
				break;
			
			case "regresar" :
				$variable = "pagina=" . $miPaginaActual;
				break;
			
			case "paginaPrincipal" :
				$variable = "pagina=" . $miPaginaActual;
				
				break;
			
			case "Registrar" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=Entrada";
				break;
			
			case "Salir" :
				
				$variable = "pagina=" . $miPaginaActual;
				$variable .= "&opcion=indexAlmacen";
				break;
			
			case "CargarElemento" :
				
				$variable = "pagina=registrarElemento";
				$variable .= "&opcion=cargarElemento";
				$variable .= "&entradaDirecta=" . TRUE;
				$variable .= "&numero_entrada=" . $valor;
				break;
			
			case "ActivarElementos" :
				
				$variable = "pagina=activacionElementosActa";
				$variable .= "&opcion=consultarElementosActa";
				$variable .= "&numero_acta=" . $valor[2];
				$variable .= "&numero_entrada=" .$valor[0];
				$variable .= "&consecutivo_entrada=" .$valor[1];
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
	}
}

?>