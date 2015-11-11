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
$cadenaACodificar .= $cadenaACodificar . "&funcion=estado";
$cadenaACodificar .="&tiempo=".$_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL definitiva
$urlFinal = $url . $cadena;




// Variables
$cadenaACodificar6 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar6 .= "&procesarAjax=true";
$cadenaACodificar6 .= "&action=index.php";
$cadenaACodificar6 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar6 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar6 .= $cadenaACodificar . "&funcion=SeleccionOrdenador";
$cadenaACodificar6 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace6 = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena6 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar6, $enlace6 );

// URL definitiva
$urlFinal6 = $url . $cadena6;




// Variables
$cadenaACodificar16 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar16 .= "&procesarAjax=true";
$cadenaACodificar16 .= "&action=index.php";
$cadenaACodificar16 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar16 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar16 .= $cadenaACodificar16 . "&funcion=consultarDependencia";
$cadenaACodificar16 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena16 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar16, $enlace );

// URL definitiva
$urlFinal16 = $url . $cadena16;



// Variables
$cadenaACodificar17 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar17 .= "&procesarAjax=true";
$cadenaACodificar17 .= "&action=index.php";
$cadenaACodificar17 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar17 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar17 .= $cadenaACodificar17 . "&funcion=consultarActa";
$cadenaACodificar17 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena17 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar17, $enlace );

// URL definitiva
$urlFinal17 = $url . $cadena17;




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




function consultarActa(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal17?>",
	    dataType: "json",
	    data: { valor:$("#<?php echo $this->campoSeguro('acta_recibido')?>").val()},
	    success: function(data){ 



	        if(data[0]!=" "){

	        	$("#<?php echo $this->campoSeguro('sede')?> option[value="+ data['sede'] +"]").attr("selected",true);
	        	$("#<?php echo $this->campoSeguro('sede')?>").select2();
	        	$("#<?php echo $this->campoSeguro('proveedor')?>").val(data['nom_razon']);
	        	$("#<?php echo $this->campoSeguro('id_proveedor')?>").val(data['proveedor']);	
	        	
	            $("#<?php echo $this->campoSeguro('dependencia')?>").removeAttr('disabled');
	            $('#<?php echo $this->campoSeguro('dependencia')?>').width(300);
	            $("#<?php echo $this->campoSeguro('dependencia')?> option[value="+ data['dependencia'] +"]").attr("selected",true);
	            $("#<?php echo $this->campoSeguro('dependencia')?>").select2();

	            $("#<?php echo $this->campoSeguro('numero_contrato')?>").val(data['numero_contrato']);
	            $("#<?php echo $this->campoSeguro('fecha_contrato')?>").val(data['fecha_contrato']);
	        	
	        	$("#<?php echo $this->campoSeguro('asignacionOrdenador')?> option[value="+ data['ordenador_gasto'] +"]").attr("selected",true);
	        	$("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();
	        	datosOrdenador();
	          
	            
		        }
	    			

	    }
		                    
	   });
	};


function consultarDependencia(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal16?>",
	    dataType: "json",
	    data: { valor:$("#<?php echo $this->campoSeguro('sede')?>").val()},
	    success: function(data){ 




	        if(data[0]!=" "){

	            $("#<?php echo $this->campoSeguro('dependencia')?>").html('');
	            $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('dependencia')?>");
	            $.each(data , function(indice,valor){

	            	$("<option value='"+data[ indice ].ESF_CODIGO_DEP+"'>"+data[ indice ].ESF_DEP_ENCARGADA+"</option>").appendTo("#<?php echo $this->campoSeguro('dependencia')?>");
	            	
	            });
	            
	            $("#<?php echo $this->campoSeguro('dependencia')?>").removeAttr('disabled');
	            
	            $('#<?php echo $this->campoSeguro('dependencia')?>').width(300);
	            $("#<?php echo $this->campoSeguro('dependencia')?>").select2();
	            
	          
	            
		        }

	    }
		                    
	   });
	};


function estado(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal?>",
	    dataType: "json",
	    data: { cls_entr:$("#<?php echo $this->campoSeguro('clase')?>").val()},
	    success: function(data){ 
	        if(data[0]!=" "){

	            $("#<?php echo $this->campoSeguro('tipo_contrato')?>").html('');
	            $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('tipo_contrato')?>");
	            $.each(data , function(indice,valor){

	            	$("<option value='"+data[ indice ].id_tipo+"'>"+data[ indice ].descripcion+"</option>").appendTo("#<?php echo $this->campoSeguro('tipo_contrato')?>");
	            	
	            });
	            $("#<?php echo $this->campoSeguro('tipo_contrato')?>").removeAttr('disabled');
	            $("#<?php echo $this->campoSeguro('tipo_contrato')?>").select2();
	            
	            
		        }

	        


		     }
		                    
	   });
	};


	function datosOrdenador(elem, request, response){
		  $.ajax({
		    url: "<?php echo $urlFinal6?>",
		    dataType: "json",
		    data: { ordenador:$("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").val()},
		    success: function(data){ 

		    		if(data[0]!='null'){

		    			$("#<?php echo $this->campoSeguro('nombreOrdenador')?>").val(data[0]);
		    			$("#<?php echo $this->campoSeguro('id_ordenador')?>").val(data[1]);
		    			$("#<?php echo $this->campoSeguro('tipo_ordenador')?>").val(data[2]);
		    			$("#<?php echo $this->campoSeguro('identificacion_ordenador')?>").val(data[1]);		    			
			    		}else{

					


				    		
			    		}

		    }
			                    
		   });
		};
	
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
        
  

    	

        



    	  $("#<?php echo $this->campoSeguro('acta_recibido')?>").change(function(){
          	if($("#<?php echo $this->campoSeguro('acta_recibido')?>").val()!=''){
              	
          		consultarActa();
      		}else{

      			$("#<?php echo $this->campoSeguro('sede')?> option[value="+''+"]").attr("selected",true);
	        	$("#<?php echo $this->campoSeguro('sede')?>").select2();
	        	$("#<?php echo $this->campoSeguro('proveedor')?> option[value="+''+"]").attr("selected",true);	
	        	

	            
	        	$("#<?php echo $this->campoSeguro('asignacionOrdenador')?> option[value="+''+"]").attr("selected",true);
	        	$("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();

				$("#<?php echo $this->campoSeguro('nombreOrdenador')?>").val('');
    			$("#<?php echo $this->campoSeguro('id_ordenador')?>").val('');
    			$("#<?php echo $this->campoSeguro('tipo_ordenador')?>").val('');
    			$("#<?php echo $this->campoSeguro('identificacion_ordenador')?>").val('');		
      			
      			$("#<?php echo $this->campoSeguro('dependencia')?> option[value="+''+"]").attr("selected",true);
      			$("#<?php echo $this->campoSeguro('dependencia')?>").select2();
          		$("#<?php echo $this->campoSeguro('dependencia')?>").attr('disabled','');
      			}

      	      });

    	  $("#<?php echo $this->campoSeguro('dependencia')?>").select2(); 

        $("#<?php echo $this->campoSeguro('sede')?>").change(function(){
        	if($("#<?php echo $this->campoSeguro('sede')?>").val()!=''){
            	consultarDependencia();
    		}else{
    			$("#<?php echo $this->campoSeguro('dependencia')?>").attr('disabled','');
    			}

    	      });
        
    $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").change(function(){
    	
    	if($("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").val()!=''){
    		datosOrdenador();
		}else{
			$("#<?php echo $this->campoSeguro('nombreOrdenador')?>").val('');
			}
	      });
	
    });

</script>

