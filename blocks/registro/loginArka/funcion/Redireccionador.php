<?php

namespace registro\loginArka;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("index.php");
	exit ();
}
class Redireccionador {
	public static function redireccionar($opcion, $valor = "") {
		
	    $miConfigurador = \Configurador::singleton ();
		
		switch ($opcion) {
			
			case "indexAlmacen" :
				
				$variable = 'pagina=indexAlmacen';
				$variable .= '&registro=' . $valor[0];				
				break;
				
			case "indexInventarios" :
			
				$variable = 'pagina=indexInventarios';
				$variable .= '&registro=' . $valor[0];
				break;

			case "indexContabilidad" :
			
				$variable = 'pagina=indexContabilidad';
				$variable .= '&registro=' . $valor[0];
				break;
				
			default:
			    $variable = 'pagina=index';
			    break;
			
		}
		foreach ( $_REQUEST as $clave => $valor ) {
			unset ( $_REQUEST [$clave] );
		}
		
		$enlace = $miConfigurador->getVariableConfiguracion ( "enlace" );
		$variable = $miConfigurador->fabricaConexiones->crypto->codificar ( $variable );
		
		$_REQUEST [$enlace] = $variable;
		$_REQUEST ["recargar"] = true;
		
		return true;
	}
}
?>