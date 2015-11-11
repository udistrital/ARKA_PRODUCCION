<?php
/**
 *
* Los datos del bloque se encuentran en el arreglo $esteBloque.
*/

// URL base
$url = $this->miConfigurador->getVariableConfiguracion ( "host" );
$url .= $this->miConfigurador->getVariableConfiguracion ( "site" );
$url .= "/index.php?";





// Variables
$cadenaACodificarPlaca = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificarPlaca .= "&procesarAjax=true";
$cadenaACodificarPlaca .= "&action=index.php";
$cadenaACodificarPlaca .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificarPlaca .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificarPlaca .= "&funcion=consultaPlaca";
$cadenaACodificarPlaca .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificarPlaca, $enlace );

// URL definitiva
$urlFinalPlaca = $url . $cadena;






// Variables
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= $cadenaACodificar . "&funcion=buscarPlaca";
$cadenaACodificar .="&tiempo=".$_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL definitiva
$urlFinal = $url . $cadena;
?>


<script type='text/javascript'>





function consultarPlaca(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal?>",
	    dataType: "json",
	    data: { valor:$("#<?php echo $this->campoSeguro('placaInicial')?>").val()},
	    success: function(data){ 



	        if(data[0]!=" "){

	            $("#<?php echo $this->campoSeguro('placaFinal')?>").html('');
	            $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('placaFinal')?>");
	            $.each(data , function(indice,valor){

	            	$("<option value='"+data[ indice ].id_elemento_ind+"'>"+data[ indice ].placas+"</option>").appendTo("#<?php echo $this->campoSeguro('placaFinal')?>");
	            	
	            });
	            
	            $("#<?php echo $this->campoSeguro('placaFinal')?>").removeAttr('disabled');
	            
	            $('#<?php echo $this->campoSeguro('placaFinal')?>').width(180);
	            $("#<?php echo $this->campoSeguro('placaFinal')?>").select2({
			     	 placeholder: "Seleccione...",
			      	 minimumInputLength: 3,
			      	 });
	            
	          
	            
		        }
	    			

	    }
		                    
	   });
	};


    $(function () {

        $("#<?php echo $this->campoSeguro('placaInicial')?>").change(function(){
        	if($("#<?php echo $this->campoSeguro('placaInicial')?>").val()!=''){
            	consultarPlaca();
    		}else{
    			$("#<?php echo $this->campoSeguro('dependencia')?>").attr('disabled','');
    			}

    	      });
        



        $("#<?php echo $this->campoSeguro('selec_placaInicial') ?>").autocomplete({
        	minChars: 3,
        	serviceUrl: '<?php echo $urlFinalPlaca; ?>',
        	onSelect: function (suggestion) {
            	
        	        $("#<?php echo $this->campoSeguro('placaInicial') ?>").val(suggestion.data);
        	        $("#<?php echo $this->campoSeguro('selec_placaFinal') ?>").attr("class", "ui-widget ui-widget-content ui-corner-all validate[required, min["+$("#<?php echo $this->campoSeguro('selec_placaInicial') ?>").val()+"]]");
           	        
        	    }
                    
        });




        $("#<?php echo $this->campoSeguro('selec_placaFinal') ?>").autocomplete({
        	minChars: 3,
        	serviceUrl: '<?php echo $urlFinalPlaca; ?>',
        	onSelect: function (suggestion) {
            	
        	        $("#<?php echo $this->campoSeguro('placaFinal') ?>").val(suggestion.data);
        	        $("#<?php echo $this->campoSeguro('selec_placaInicial') ?>").attr("class", "ui-widget ui-widget-content ui-corner-all validate[required, max["+$("#<?php echo $this->campoSeguro('selec_placaFinal') ?>").val()+"]]");
           	        
        	    }
                    
        });

    	
    });

</script>

