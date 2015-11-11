
// Asociar el widget de validación al formulario
     $("#registrarOrdenServicios").validationEngine({
            promptPosition : "centerRight", 
            scroll: false,
            autoHidePrompt: true,
            autoHideDelay: 2000
	         });
	

        $(function() {
            $("#registrarOrdenServicios").submit(function() {
                $resultado=$("#registrarOrdenServicios").validationEngine("validate");
                   
                if ($resultado) {
                
                    return true;
                }
                return false;
            });
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
	   
	   

        
        
		$('#<?php echo $this->campoSeguro('duracion')?>').attr('disabled',''); 
		$('#<?php echo $this->campoSeguro('total_iva')?>').attr('disabled',''); 
		$('#<?php echo $this->campoSeguro('total')?>').attr('disabled','');    
		
		$('#<?php echo $this->campoSeguro('nombreOrdenador')?>').attr('disabled',''); 
		$('#<?php echo $this->campoSeguro('nombreJefeSeccion')?>').attr('disabled',''); 
		
		
		
		
		
		
		
		$("#<?php echo $this->campoSeguro('iva')?>").change(function(){ 
		
		switch($("#<?php echo $this->campoSeguro('iva')?>").val())
		
		{
	
			case '0':
		
				$('#<?php echo $this->campoSeguro('total_iva')?>').val(0);
				
				var total =$('#<?php echo $this->campoSeguro('total_preliminar')?>').val();
				var iva =$('#<?php echo $this->campoSeguro('total_iva')?>').val();
				var numero = Number(total) + Number(iva) ;
				
				$('#<?php echo $this->campoSeguro('total')?>').val(numero);
		
		
			break;
		
			case '1':
		
				$('#<?php echo $this->campoSeguro('total_iva')?>').val($('#<?php echo $this->campoSeguro('total_preliminar')?>').val() * 0.16);
		
				var total =$('#<?php echo $this->campoSeguro('total_preliminar')?>').val();
				var iva =$('#<?php echo $this->campoSeguro('total_iva')?>').val();
				var numero = Number(total) + Number(iva) ;
				
				$('#<?php echo $this->campoSeguro('total')?>').val(numero);
		
		
			break;	

		
		}
		
		 });
		 
		 
		 $("#<?php echo $this->campoSeguro('total_preliminar')?>").keyup(function(){ 
		
	
				var total =$('#<?php echo $this->campoSeguro('total_preliminar')?>').val();
				var iva =$('#<?php echo $this->campoSeguro('total_iva')?>').val();
				var numero = Number(total) + Number(iva) 
				
				$('#<?php echo $this->campoSeguro('total')?>').val(numero);

			
		 });
		    
		    
		    

          $("#<?php echo $this->campoSeguro('cargoJefeSeccion')?>").select2();
	      $("#<?php echo $this->campoSeguro('nombreContratista')?>").select2({
			   	 placeholder: "Search for a repository",
			   	 minimumInputLength: 3,
			
			       });
			$('#<?php echo $this->campoSeguro('asignacionOrdenador')?>').width(300);			       
          $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();
          $("#<?php echo $this->campoSeguro('tipo_orden')?>").select2();
		    
        
               
               
 
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
			       
$('#<?php echo $this->campoSeguro('sede')?>').width(300);
$("#<?php echo $this->campoSeguro('sede')?>").select2();


$('#<?php echo $this->campoSeguro('sede_super')?>').width(300);
$("#<?php echo $this->campoSeguro('sede_super')?>").select2();


$("#<?php echo $this->campoSeguro('vigencia_disponibilidad')?>").select2();
$('#<?php echo $this->campoSeguro('diponibilidad')?>').select2();

$('#<?php echo $this->campoSeguro('dependencia_solicitante')?>').width(350);
$("#<?php echo $this->campoSeguro('dependencia_solicitante')?>").select2();



$('#<?php echo $this->campoSeguro('dependencia_supervisor')?>').width(350);
$("#<?php echo $this->campoSeguro('dependencia_supervisor')?>").select2(); 

$("#<?php echo $this->campoSeguro('vigencia_registro')?>").select2();
$("#<?php echo $this->campoSeguro('registro')?>").select2();

$("#<?php echo $this->campoSeguro('vigencia_contratista')?>").select2();
$('#<?php echo $this->campoSeguro('nombreContratista')?>').attr("style", "width: 60px '");


 
        
          






