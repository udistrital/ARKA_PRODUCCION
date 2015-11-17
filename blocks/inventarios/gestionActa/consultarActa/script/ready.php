<?php

?>

// Asociar el widget de validación al formulario
              $("#consultarActa").validationEngine({
            promptPosition : "centerRight", 
            scroll: false,
            autoHidePrompt: true,
            autoHideDelay: 2000
	         });
	
        
        $(function() {
            $("#consultarActa").submit(function() {
                $resultado=$("#consultarActa").validationEngine("validate");
           
                if ($resultado) {
                
                    return true;
                }
                return false;
            });
        });

                     $('#tablaTitulos').dataTable( {
                "sPaginationType": "full_numbers"
                 } );

  	   
	   
        $('#<?php echo $this->campoSeguro('fecha_recibido')?>').datepicker({
	        dateFormat: 'yy-mm-dd',
	        maxDate: 0,
	        changeYear: true,
	        monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
				'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
				monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
				dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
				dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
				dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa']
        });
        
        
        
  $('#<?php echo $this->campoSeguro('sedeConsulta')?>').width(290);              	 
 $("#<?php echo $this->campoSeguro('sedeConsulta')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });  
 
 $("#<?php echo $this->campoSeguro('dependenciaConsulta')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });              	            
              	 
        
        
        
        
        
        
        
        $("#<?php echo $this->campoSeguro('sede') ?>").select2();
       
		$("#<?php echo $this->campoSeguro('tipoBien') ?>").select2(); 
		
		$('#<?php echo $this->campoSeguro('dependencia')?>').width(815);          
        $("#<?php echo $this->campoSeguro('dependencia') ?>").select2();
        $("#<?php echo $this->campoSeguro('numero_acta') ?>").select2();
        $("#<?php echo $this->campoSeguro('numeroContrato') ?>").select2();
        $("#<?php echo $this->campoSeguro('tipocomprador') ?>").select2();
        $("#<?php echo $this->campoSeguro('asignacionOrdenador') ?>").select2();
          
        <!--  Modificar Elementos -->
        
        
        $('#<?php echo $this->campoSeguro('fecha_inicio')?>').datepicker({
		dateFormat: 'yy-mm-dd',
		changeYear: true,
		changeMonth: true,
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		    monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		    dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		    dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
		    onSelect: function(dateText, inst) {
			var lockDate = new Date($('#<?php echo $this->campoSeguro('fecha_inicio')?>').datepicker('getDate'));
			$('input#<?php echo $this->campoSeguro('fecha_final')?>').datepicker('option', 'minDate', lockDate);
			},
			onClose: function() { 
		 	    if ($('input#<?php echo $this->campoSeguro('fecha_inicio')?>').val()!='')
                    {
                        $('#<?php echo $this->campoSeguro('fecha_final')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required]");
                }else {
                        $('#<?php echo $this->campoSeguro('fecha_final')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all ");
                    }
			  }
			
			
		});
              $('#<?php echo $this->campoSeguro('fecha_final')?>').datepicker({
		dateFormat: 'yy-mm-dd',
		
		changeYear: true,
		changeMonth: true,
		monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
		    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
		    monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'],
		    dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
		    dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
		    dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sa'],
		    onSelect: function(dateText, inst) {
			var lockDate = new Date($('#<?php echo $this->campoSeguro('fecha_final')?>').datepicker('getDate'));
			$('input#<?php echo $this->campoSeguro('fecha_inicio')?>').datepicker('option', 'maxDate', lockDate);
			 },
			 onClose: function() { 
		 	    if ($('input#<?php echo $this->campoSeguro('fecha_final')?>').val()!='')
                    {
                        $('#<?php echo $this->campoSeguro('fecha_inicio')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required]");
                }else {
                        $('#<?php echo $this->campoSeguro('fecha_inicio')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all ");
                    }
			  }
			
	   });
   
        
        
        $("#<?php echo $this->campoSeguro('nivel')?>").select2();
        $("#<?php echo $this->campoSeguro('tipo_registro')?>").select2();
        $("#<?php echo $this->campoSeguro('iva')?>").select2();
        $("#<?php echo $this->campoSeguro('tipo_poliza')?>").select2();  
          
          
          
        $( "#<?php echo $this->campoSeguro('iva')?>" ).change(function() {
        
		     switch($("#<?php echo $this->campoSeguro('iva')?>").val())
            {
                           
                case '1':
                 
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 precio=cantidad * valor;
       			 total=Math.round(precio*100)/100;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val('0');
                 
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                    
                break;
                
                case '2':
                 
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 precio=cantidad * valor;
       			 total=Math.round(precio*100)/100;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val('0');
                 
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                    
                break;
                
                case '3':
                
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 iva = Math.round(((cantidad * valor)* 0.05)*100)/100;
       			 precio=Math.round((cantidad * valor)*100)/100;
       			 total=Math.round((precio+iva)*100)/100;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val(iva);
                 
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                    
                break;
                                
                case '4':
                
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 iva = Math.round(((cantidad * valor)* 0.04)*100)/100;
       			 precio = Math.round((cantidad*valor)*100)/100;
       			 total=Math.round((precio+iva)*100)/100;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val(iva);
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                     
                break;
                
                case '5':
                
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 iva = Math.round(((cantidad * valor)* 0.1)*100)/100;
       			 precio = Math.round((cantidad*valor)*100)/100;
       			 total=Math.round((precio+iva)*100)/100;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val(iva);
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                     
                break;
                
                 case '6':
                
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 iva = Math.round(((cantidad * valor)* 0.16)*100)/100;
       			 precio = Math.round((cantidad*valor)*100)/100;
       			 total=Math.round((precio+iva)*100)/100;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val(iva);
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                     
                break;
                

                default:
                $("#<?php echo $this->campoSeguro('total_iva')?>").val('');
                $("#<?php echo $this->campoSeguro('total_iva_con')?>").val('');
                   
                break;
                
                }
            
          });  
          
          	
	
	
	    

                
                
        $( "#<?php echo $this->campoSeguro('cantidad')?>" ).keyup(function() {
        
            $("#<?php echo $this->campoSeguro('valor')?>").val('');
            $("#<?php echo $this->campoSeguro('subtotal_sin_iva')?>").val('');
            $("#<?php echo $this->campoSeguro('total_iva')?>").val('');
            $("#<?php echo $this->campoSeguro('total_iva_con')?>").val('');
                        resetIva();
            
          });  
	
        $( "#<?php echo $this->campoSeguro('valor')?>" ).keyup(function() {
        	$("#<?php echo $this->campoSeguro('subtotal_sin_iva')?>").val('');
            $("#<?php echo $this->campoSeguro('total_iva')?>").val('');
            $("#<?php echo $this->campoSeguro('total_iva_con')?>").val('');
                        resetIva();  
            cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
            
            precio = Math.round((cantidad * valor)*100)/100;
      
      
            if (precio==0){
            
            
            $("#<?php echo $this->campoSeguro('subtotal_sin_iva')?>").val('');
            
            }else{
            
            $("#<?php echo $this->campoSeguro('subtotal_sin_iva')?>").val(precio);
            
            }

          }); 
          
          
          
          
     $( "#<?php echo $this->campoSeguro('tipo_poliza')?>" ).change(function() {
        
            switch($("#<?php echo $this->campoSeguro('tipo_poliza')?>").val())
            {
                           
                case '0':
                    
                   
                    $("#<?php echo $this->campoSeguro('fechas_polizas')?>").css('display','none');
                   

                   

                break;
                
                
                case '1':
                    
                  $("#<?php echo $this->campoSeguro('fechas_polizas')?>").css('display','block');
       
                break;
                

                default:
                
                $("#<?php echo $this->campoSeguro('fechas_polizas')?>").css('display','none');
                   
                   break;
                
                
             }
          });  
        
        
        
      
        
        
          






