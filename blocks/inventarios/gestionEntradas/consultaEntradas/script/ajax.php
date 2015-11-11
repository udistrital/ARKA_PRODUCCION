<?php 


// URL base
$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
$url .= "/index.php?";


// Variables
$cadenaACodificarProveedor = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificarProveedor .= "&procesarAjax=true";
$cadenaACodificarProveedor .= "&action=index.php";
$cadenaACodificarProveedor .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificarProveedor .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificarProveedor .= "&funcion=consultaProveedor";
$cadenaACodificarProveedor .= "&tiempo=" . $_REQUEST ['tiempo'];



// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificarProveedor, $enlace );

// URL definitiva
$urlFinalProveedor = $url . $cadena;




?>




<script type='text/javascript'>

function marcar(obj) {
    elem=obj.elements;
    for (i=0;i<elem.length;i++)
        if (elem[i].type=="checkbox")
            elem[i].checked=true;
} 

function desmarcar(obj) {
    elem=obj.elements;
    for (i=0;i<elem.length;i++)
        if (elem[i].type=="checkbox")
            elem[i].checked=false;
} 



$(function () {





    $( "#<?php echo $this->campoSeguro('proveedor')?>" ).keyup(function() {

    	
	$('#<?php echo $this->campoSeguro('proveedor') ?>').val($('#<?php echo $this->campoSeguro('proveedor') ?>').val().toUpperCase());

	
        });




    $("#<?php echo $this->campoSeguro('proveedor') ?>").autocomplete({
    	minChars: 3,
    	serviceUrl: '<?php echo $urlFinalProveedor; ?>',
    	onSelect: function (suggestion) {
        	
    	        $("#<?php echo $this->campoSeguro('id_proveedor') ?>").val(suggestion.data);
    	    }
                
    });
    
	
	$("#<?php echo $this->campoSeguro('selecc_registros')?>").change(function(){

	
		if($("#<?php echo $this->campoSeguro('selecc_registros')?>").val()==1){
			marcar(this.form);
			

		}else{

			desmarcar(this.form);
			}

	      });



    
});



</script>