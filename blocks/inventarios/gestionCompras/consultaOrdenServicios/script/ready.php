<?php

?>

// Asociar el widget de validación al formulario
              $("#consultaOrdenServicios").validationEngine({
            promptPosition : "centerRight", 
            scroll: false,
            autoHidePrompt: true,
            autoHideDelay: 2000
	         });
	
        
        $(function() {
            $("#consultaOrdenServicios").submit(function() {
                $resultado=$("#consultaOrdenServicios").validationEngine("validate");
                   
                   
                   
                if ($resultado) {
                
                    return true;
                }
                return false;
            });
        });

                     $('#tablaTitulos').dataTable( {
                "sPaginationType": "full_numbers"
                 } );

$('#<?php echo $this->campoSeguro('dependencia_solicitante')?>').width(350);
$("#<?php echo $this->campoSeguro('dependencia_solicitante')?>").select2();                 

$('#<?php echo $this->campoSeguro('sede_super')?>').width(300);
$("#<?php echo $this->campoSeguro('sede_super')?>").select2();

$('#<?php echo $this->campoSeguro('dependencia_supervisor')?>').width(350);
$("#<?php echo $this->campoSeguro('dependencia_supervisor')?>").select2(); 

$('#<?php echo $this->campoSeguro('asignacionOrdenador')?>').width(300);			       
$("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();

$('#<?php echo $this->campoSeguro('nombre_supervisor')?>').width(300);			       
               $("#<?php echo $this->campoSeguro('nombre_supervisor')?>").select2({
			   	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
			   	 minimumInputLength: 3,
			       });
			   


$("#<?php echo $this->campoSeguro('rubro')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });                             
                             
                             
$("#<?php echo $this->campoSeguro('unidad_ejecutora')?>").select2();                             
                             
                             
                             
 		$("#<?php echo $this->campoSeguro('nivel')?>").select2();
 		$("#<?php echo $this->campoSeguro('tipo_poliza')?>").select2(); 
 		$("#<?php echo $this->campoSeguro('iva')?>").select2();
 		
 		

 		
 		
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
 		
 		
          $( "#<?php echo $this->campoSeguro('iva')?>" ).change(function() {
        
		     switch($("#<?php echo $this->campoSeguro('iva')?>").val())
            {
                           
                case '1':
                 
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 precio=cantidad * valor;
       			 total=precio;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val('0');
                 
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                    
                break;
                
                case '2':
                 
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 precio=cantidad * valor;
       			 total=precio;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val('0');
                 
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                    
                break;
                
                case '3':
                
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 iva = (cantidad * valor)* 0.05;
       			 precio=cantidad * valor;
       			 total=precio+iva;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val(iva);
                 
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                    
                break;
                                
                case '4':
                
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 iva = (cantidad * valor)* 0.04;
       			 precio = cantidad*valor;
       			 total=precio+iva;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val(iva);
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                     
                break;
                
                case '5':
                
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 iva = (cantidad * valor)* 0.1;
       			 precio = cantidad*valor;
       			 total=precio+iva;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val(iva);
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                     
                break;
                
                 case '6':
                
                 cantidad=Number($("#<?php echo $this->campoSeguro('cantidad')?>").val());
            	 valor=Number($("#<?php echo $this->campoSeguro('valor')?>").val());
       			 iva = (cantidad * valor)* 0.16;
       			 precio = cantidad*valor;
       			 total=precio+iva;
       			 
                 $("#<?php echo $this->campoSeguro('total_iva')?>").val(iva);
                 $("#<?php echo $this->campoSeguro('total_iva_con')?>").val(total);
                                     
                break;
                

                default:
                $("#<?php echo $this->campoSeguro('total_iva')?>").val('');
                $("#<?php echo $this->campoSeguro('total_iva_con')?>").val('');
                   
                break;
                
                }
            
          });  
 		
 		
 		
 		
 		
        $("#<?php echo $this->campoSeguro('tipo_orden')?>").select2();
		$("#<?php echo $this->campoSeguro('numero_orden')?>").select2();
		                     
		                                          
        $("#<?php echo $this->campoSeguro('sedeConsulta')?>").select2();
		$("#<?php echo $this->campoSeguro('dependenciaConsulta')?>").select2();
		                     
                     
                     
        
                     
        $("#<?php echo $this->campoSeguro('proveedorContratista')?>").select2();
		$("#<?php echo $this->campoSeguro('sede')?>").select2();
		                     
		$('#<?php echo $this->campoSeguro('orden_consulta')?>').select2();
        
        $('#<?php echo $this->campoSeguro('vigencia_disponibilidad')?>').width(100);             
        $("#<?php echo $this->campoSeguro('vigencia_disponibilidad')?>").select2();
        $('#<?php echo $this->campoSeguro('diponibilidad')?>').width(150);
		$('#<?php echo $this->campoSeguro('diponibilidad')?>').select2();
		
		
		$("#<?php echo $this->campoSeguro('vigencia_registro')?>").select2();
		$('#<?php echo $this->campoSeguro('registro')?>').width(150);
		$("#<?php echo $this->campoSeguro('registro')?>").select2(); 
		
		$("#<?php echo $this->campoSeguro('vigencia_contratista')?>").select2();
		$('#<?php echo $this->campoSeguro('nombreContratista')?>').attr("style", "width: 60px '");

        
        
                 
                 
        $('#<?php echo $this->campoSeguro('fecha_inicio')?>').datepicker({
		dateFormat: 'yy-mm-dd',
		maxDate: 0,
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
		maxDate: 0,
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
	   
	     $('#<?php echo $this->campoSeguro('fecha_inicio_pago')?>').datepicker({
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
			var lockDate = new Date($('#<?php echo $this->campoSeguro('fecha_inicio_pago')?>').datepicker('getDate'));
			$('input#<?php echo $this->campoSeguro('fecha_final_pago')?>').datepicker('option', 'minDate', lockDate);
			},
			onClose: function() { 
		 	    if ($('input#<?php echo $this->campoSeguro('fecha_inicio_pago')?>').val()!='')
                    {
                        $('#<?php echo $this->campoSeguro('fecha_final_pago')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required]");
                }else {
                        $('#<?php echo $this->campoSeguro('fecha_final_pago')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all ");
                    }
                    
                    var fechaIn = new Date($('#<?php echo $this->campoSeguro('fecha_inicio_pago')?>').datepicker('getDate'));
                    
                    var fechaFin = new Date($('#<?php echo $this->campoSeguro('fecha_final_pago')?>').datepicker('getDate'));
                    
                    
                    var tiempo = fechaFin.getTime() - fechaIn.getTime();
                    
                    var dias = Math.floor(tiempo / (1000*60*60*24));
                    
                    if($('#<?php echo $this->campoSeguro('fecha_final_pago')?>').val()!=''){
                    
                    $('#<?php echo $this->campoSeguro('duracion')?>').val(dias);
                    
                    $('#<?php echo $this->campoSeguro('numero_dias')?>').val(dias);
                    
                    }
                    
                    
                    
                    
			  }
			
        		
			
		});
		
		
		
		$('#<?php echo $this->campoSeguro('fecha_final_pago')?>').datepicker({
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
			var lockDate = new Date($('#<?php echo $this->campoSeguro('fecha_final_pago')?>').datepicker('getDate'));
			$('input#<?php echo $this->campoSeguro('fecha_inicio_pago')?>').datepicker('option', 'maxDate', lockDate);
			 },
			 onClose: function() { 
		 	    if ($('input#<?php echo $this->campoSeguro('fecha_final_pago')?>').val()!='')
                    {
                        $('#<?php echo $this->campoSeguro('fecha_inicio_pago')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all   validate[required]");
                }else {
                        $('#<?php echo $this->campoSeguro('fecha_inicio_pago')?>').attr("class", "cuadroTexto ui-widget ui-widget-content ui-corner-all ");
                    }
                    
                    
                    var fechaIn = new Date($('#<?php echo $this->campoSeguro('fecha_inicio_pago')?>').datepicker('getDate'));
                    
                    var fechaFin = new Date($('#<?php echo $this->campoSeguro('fecha_final_pago')?>').datepicker('getDate'));
                    
                    
                    var tiempo = fechaFin.getTime() - fechaIn.getTime();
                    
                    var dias = Math.floor(tiempo / (1000*60*60*24));
                    
                    if($('#<?php echo $this->campoSeguro('fecha_inicio_pago')?>').val()!=''){
                    
                    $('#<?php echo $this->campoSeguro('duracion')?>').val(dias);
                                        
                    $('#<?php echo $this->campoSeguro('numero_dias')?>').val(dias);
                    }
			  }
			
	   });
	   
	   
	           $('#<?php echo $this->campoSeguro('fecha_disponibilidad')?>').datepicker({
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
        
        
        	           $('#<?php echo $this->campoSeguro('fecha_registro')?>').datepicker({
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
	   
	   
	   
	   $("#<?php echo $this->campoSeguro('contratista_consulta')?>").select2();
	   
	   	   $("#<?php echo $this->campoSeguro('selec_proveedor')?>").select2({
			   	 placeholder: "Search for a repository",
			   	 minimumInputLength: 3,
			
			       });
	   
	   
	   $('#<?php echo $this->campoSeguro('selec_dependencia_Sol')?>').attr('disabled','');
	   $("#<?php echo $this->campoSeguro('sede_consultar')?>").select2();
	   
	   $("#<?php echo $this->campoSeguro('sede_super')?>").select2();
	    	
	   $("#<?php echo $this->campoSeguro('nombre_supervisor')?>").select2();
	   
	   
	   $("#<?php echo $this->campoSeguro('sede_super')?>").select2();
	   $("#<?php echo $this->campoSeguro('dependencia_solicitante')?>").select2();
	   $("#<?php echo $this->campoSeguro('dependencia_supervisor')?>").select2();
	   
	   
	   $("#<?php echo $this->campoSeguro('rubro')?>").select2({
			   	 placeholder: "Search for a repository",
			   	 minimumInputLength: 3,
			
			       });
	   
	   $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();
	   $("#<?php echo $this->campoSeguro('cargoJefeSeccion')?>").select2();
	   $("#<?php echo $this->campoSeguro('nombreContratista')?>").select2();
	   
	
        
          






