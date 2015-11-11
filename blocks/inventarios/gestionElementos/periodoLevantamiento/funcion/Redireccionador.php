<?php

namespace inventarios\gestionElementos\periodoLevantamiento\funcion;

if (! isset ( $GLOBALS ["autorizado"] )) {
	include ("index.php");
	exit ();
}


$miConfigurador = \Configurador::singleton ();

switch ($opcion) {
	
	
	
	case "CerroPeriodo" :
		$variable = "pagina=periodoLevantamiento";
		$variable .= "&opcion=mensaje";
		$variable .= "&mensaje=CerroPeriodo";
		$variable .= "&fecha_inicio=" . $_REQUEST ['fecha_inicio_cierre'];
		$variable .= "&fecha_final=" . $_REQUEST ['fecha_fin_cierre'];
		$variable .= "&usuario=".$_REQUEST['usuario'];
		break;
	
	case "noCerroPeriodo" :
		$variable = "pagina=periodoLevantamiento";
		$variable .= "&opcion=mensaje";
		$variable .= "&mensaje=noCerroPeriodo";
		$variable .= "&usuario=".$_REQUEST['usuario'];
		break;
	
	
	
	
	case "actualizoPeriodo" :
		$variable = "pagina=periodoLevantamiento";
		$variable .= "&opcion=mensaje";
		$variable .= "&mensaje=actualizoPeriodo";
		$variable .= "&fecha_inicio=" . $_REQUEST ['fecha_inicio_cierre'];
		$variable .= "&fecha_final=" . $_REQUEST ['fecha_fin_cierre'];
		$variable .= "&usuario=".$_REQUEST['usuario'];
		break;
	
	case "noactualizoPeriodo" :
		$variable = "pagina=periodoLevantamiento";
		$variable .= "&opcion=mensaje";
		$variable .= "&mensaje=noactualizoPeriodo";
		$variable .= "&usuario=".$_REQUEST['usuario'];
		break;
		
	case "registroPeriodo" :
		$variable = "pagina=periodoLevantamiento";
		$variable .= "&opcion=mensaje";
		$variable .= "&mensaje=registroPeriodo";
		$variable .= "&fecha_inicio=" . $_REQUEST ['fecha_inicio_cierre'];
		$variable .= "&fecha_final=" . $_REQUEST ['fecha_fin_cierre'];
		$variable .= "&usuario=".$_REQUEST['usuario'];
		break;
	
	case "noregistroPeriodo" :
		$variable = "pagina=periodoLevantamiento";
		$variable .= "&opcion=mensaje";
		$variable .= "&mensaje=noregistroPeriodo";
		$variable .= "&usuario=".$_REQUEST['usuario'];
		break;
	
	case "actualizoDocumento" :
		$variable = "pagina=gestionContrato";
		$variable .= "&opcion=mensaje";
		$variable .= "&mensaje=mensajeActualizacion";
		break;
	
	case "noactualizoDocumento" :
		$variable = "pagina=gestionContrato";
		$variable .= "&opcion=mensaje";
		$variable .= "&mensaje=error";
		break;
	
	case "paginaPrincipal" :
		$variable = "pagina=index";
		break;
	
	default :
		$variable = '';
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

// $enlace = $miConfigurador->getVariableConfiguracion("enlace");
// $variable = $miConfigurador->fabricaConexiones->crypto->codificar($variable);

// $_REQUEST [$enlace] = $variable;
// $_REQUEST ["recargar"] = true;

// return true;

?>