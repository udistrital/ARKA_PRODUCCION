<?php

?>

// Asociar el widget de validación al formulario
              $("#modificarElemento").validationEngine({
            promptPosition : "centerRight", 
            scroll: false,
            autoHidePrompt: true,
            autoHideDelay: 2000
	         });
	
        
        $(function() {
            $("#modificarElemento").submit(function() {
                $resultado=$("#modificarElemento").validationEngine("validate");
                   
                   
                   
                if ($resultado) {
                
                    return true;
                }
                return false;
            });
        });



    
             
 $('#<?php echo $this->campoSeguro('numero_entrada_c')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('fecha_entrada')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('clase_entrada')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('razon_social')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('nit_proveedor')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('numero_factura')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('fecha_factura')?>').attr('disabled','');
 

 $('#<?php echo $this->campoSeguro('serie1')?>').width(261);              	 
 $("#<?php echo $this->campoSeguro('serie1')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });

 $('#<?php echo $this->campoSeguro('registro_tipo')?>').width(390);  
  $("#<?php echo $this->campoSeguro('registro_tipo')?>").select2();
 
              	 
 $("#<?php echo $this->campoSeguro('funcionario')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });
$('#<?php echo $this->campoSeguro('sede')?>').width(380);              	 
 $("#<?php echo $this->campoSeguro('sede')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });  
 
 $("#<?php echo $this->campoSeguro('dependencia')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });              	            
              	 
              	 
              	 
$('#<?php echo $this->campoSeguro('numero_entrada')?>').width(380);              	 	 
 $("#<?php echo $this->campoSeguro('numero_entrada')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });           
              	 
              	 
              	            	            	 
              	               	 
              	 
              	 $("#<?php echo $this->campoSeguro('tipo_poliza')?>").select2();
 $("#<?php echo $this->campoSeguro('nivel')?>").select2();
 $("#<?php echo $this->campoSeguro('tipo_registro')?>").select2();
 $("#<?php echo $this->campoSeguro('tipo_bien')?>").select2();
 $("#<?php echo $this->campoSeguro('iva')?>").select2();
 $("#<?php echo $this->campoSeguro('bodega')?>").select2();
 
                  
     
     $( "#<?php echo $this->campoSeguro('tipo_registro')?>" ).change(function() {
        
            switch($("#<?php echo $this->campoSeguro('tipo_registro')?>").val())
            {
                           
                case '1':
                    
                   
                    $("#<?php echo $this->campoSeguro('cargar_elemento')?>").css('display','block');
                    $("#<?php echo $this->campoSeguro('cargue_elementos')?>").css('display','none');

                   

                break;
                
                
                       case '2':
                    
                    $("#<?php echo $this->campoSeguro('cargar_elemento')?>").css('display','none');
                    $("#<?php echo $this->campoSeguro('cargue_elementos')?>").css('display','block');
       
                break;
                

                default:
                
                    $("#<?php echo $this->campoSeguro('cargar_elemento')?>").css('display','block');
                    $("#<?php echo $this->campoSeguro('cargue_elementos')?>").css('display','none');
                   
                   break;
                
                
             }
          });  
        
		  
     	
	
	
	     $( "#<?php echo $this->campoSeguro('tipo_bien')?>" ).change(function() {
        
            switch($("#<?php echo $this->campoSeguro('tipo_bien')?>").val())
            {
                           
                case '2':
                    
                   
                    $("#<?php echo $this->campoSeguro('devolutivo')?>").css('display','none');
                    $("#<?php echo $this->campoSeguro('consumo_controlado')?>").css('display','block');
                   

                   

                break;
                
                
                case '3':
                    
                  $("#<?php echo $this->campoSeguro('devolutivo')?>").css('display','block');
                  $("#<?php echo $this->campoSeguro('consumo_controlado')?>").css('display','none');
       
                break;
                

                default:
                
                $("#<?php echo $this->campoSeguro('devolutivo')?>").css('display','none');
                $("#<?php echo $this->campoSeguro('consumo_controlado')?>").css('display','none');   
                   break;
                
                
             }
          });  
	

                
                
 
        
