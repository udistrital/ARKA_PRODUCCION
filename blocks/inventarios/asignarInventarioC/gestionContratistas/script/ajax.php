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
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= "&funcion=SeleccionTipoBien";
$cadenaACodificar .="&tiempo=".$_REQUEST['tiempo'];


// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL definitiva
$urlFinal = $url . $cadena;




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



// Variables
$cadenaACodificariva = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificariva .= "&procesarAjax=true";
$cadenaACodificariva .= "&action=index.php";
$cadenaACodificariva .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificariva .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificariva .= "&funcion=consultarIva";
$cadenaACodificariva .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadenaiva = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificariva, $enlace );

// URL definitiva
$urlFinaliva = $url . $cadenaiva;








?>
<script type='text/javascript'>


function resetIva(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinaliva?>",
	    dataType: "json",
	    success: function(data){ 




	        if(data[0]!=" "){

	            $("#<?php echo $this->campoSeguro('iva')?>").html('');
	            $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('iva')?>");
	            $.each(data , function(indice,valor){

	            	$("<option value='"+data[ indice ].id_iva+"'>"+data[ indice ].descripcion+"</option>").appendTo("#<?php echo $this->campoSeguro('iva')?>");
	            	
	            });
	            
	            
	            $('#<?php echo $this->campoSeguro('iva')?>').width(150);
	            $("#<?php echo $this->campoSeguro('iva')?>").select2();
	            
	          
	            
		        }
	    			

	    }
		                    
	   });
	}; 


function tipo_bien(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal?>",
	    dataType: "json",
	    data: { valor:$("#<?php echo $this->campoSeguro('nivel')?>").val()},
	    success: function(data){ 


	    			$("#<?php echo $this->campoSeguro('id_tipo_bien')?>").val(data[0]);
	    			$("#<?php echo $this->campoSeguro('tipo_bien')?>").val(data[1]);

	    			  switch($("#<?php echo $this->campoSeguro('id_tipo_bien')?>").val())
	    	            {
	    	                           
	    	                
	    	                case '2':


	    	                    $("#<?php echo $this->campoSeguro('devolutivo')?>").css('display','none');
	    	                    $("#<?php echo $this->campoSeguro('consumo_controlado')?>").css('display','block');   
	    	                 $("#<?php echo $this->campoSeguro('cantidad')?>").val('1');
	    	                 $('#<?php echo $this->campoSeguro('cantidad')?>').attr('disabled','');

	    	                 break;
	    	                
	    	                case '3':

	    	                    $("#<?php echo $this->campoSeguro('devolutivo')?>").css('display','block');
	    	                    $("#<?php echo $this->campoSeguro('consumo_controlado')?>").css('display','none');
	    	                    $("#<?php echo $this->campoSeguro('tipo_poliza')?>").select2();
	    	         
	    	                 $("#<?php echo $this->campoSeguro('cantidad')?>").val('1');
	    	                 $('#<?php echo $this->campoSeguro('cantidad')?>').attr('disabled','');
	    	                    
	    	                break;
	    	                                
	    	           
	    	                break;
	    	                

	    	                default:

	    	                    $("#<?php echo $this->campoSeguro('devolutivo')?>").css('display','none');
	    	                    $("#<?php echo $this->campoSeguro('consumo_controlado')?>").css('display','none');   
	    	                    
	    	                 
	    	                 $("#<?php echo $this->campoSeguro('cantidad')?>").val('');
	    	                 $('#<?php echo $this->campoSeguro('cantidad')?>').removeAttr('disabled');
	    	                 
	    	                break;
	    	                
	    	                }






	    			

	    }
		                    
	   });
	};


$(function() {




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
    
	

	
    $("#<?php echo $this->campoSeguro('nivel')?>").change(function() {
    	
		if($("#<?php echo $this->campoSeguro('nivel')?>").val()!=''){

			tipo_bien();	

		}else{}


 });



	
  
    
});

</script>

