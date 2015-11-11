<?php
/**
 *
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
 */
//URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url.=$this->miConfigurador->getVariableConfiguracion("site");
$url.="/index.php?";

//Variables
$pagina = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar = $pagina;
$cadenaACodificar.="&procesarAjax=true";
$cadenaACodificar.="&action=index.php";
$cadenaACodificar.="&bloqueNombre=" . $esteBloque["nombre"];
$cadenaACodificar.="&bloqueGrupo=" . $esteBloque["grupo"];

//Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");

//Cadena codificada para listar Catalogos

$cadena0 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($pagina, $enlace);

//Cadena codificada para listar Catalogos
$cadenaACodificar1 = $cadenaACodificar . "&funcion=placas";
$cadena1 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar1, $enlace);

$urlFinal = $url . $cadena1;
?>
<script type='text/javascript'>
    $(document).ready(function () {
        $("#<?php echo $this->campoSeguro('placa') ?>").devbridgeAutocomplete({
            minLength: 2,
            serviceUrl: '<?php echo $urlFinal; ?>',
            onSelect: function (suggestion) {
    	        $("#<?php echo $this->campoSeguro('id_placa') ?>").val(suggestion.data);
    	    }
        });
    });
</script>

