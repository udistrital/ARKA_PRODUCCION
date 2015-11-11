<?php

?>

// Asociar el widget de validación al formulario
              $("#consultaOrdenCompra").validationEngine({
            promptPosition : "centerRight", 
            scroll: false,
            autoHidePrompt: true,
            autoHideDelay: 2000
	         });
	
        
        $(function() {
            $("#consultaOrdenCompra").submit(function() {
                $resultado=$("#consultaOrdenCompra").validationEngine("validate");
                   
                   
                   
                if ($resultado) {
                
                    return true;
                }
                return false;
            });
        });

                     $('#tablaTitulos').dataTable( {
                "sPaginationType": "full_numbers"
                 } );

		$("#<?php echo $this->campoSeguro('sede')?>").select2();
		$("#<?php echo $this->campoSeguro('sede_consultar')?>").select2();
		$('#<?php echo $this->campoSeguro('selec_dependencia_Sol')?>').attr('disabled','');                     
        
        $("#<?php echo $this->campoSeguro('selec_dependencia')?>").select2();
        
        
        $("#<?php echo $this->campoSeguro('orden_compra_consulta')?>").select2();
         
         
         
          $("#<?php echo $this->campoSeguro('rubro')?>").select2();
          $("#<?php echo $this->campoSeguro('cargoJefeSeccion')?>").select2();
	      $("#<?php echo $this->campoSeguro('nombreContratista')?>").select2();
          $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();
          
                 $("#<?php echo $this->campoSeguro('destino')?>").select2();
                 $("#<?php echo $this->campoSeguro('formaPago')?>").select2();
            
         
	    	  $('#<?php echo $this->campoSeguro('nombreJefeSeccion')?>').attr('disabled','');
	          $('#<?php echo $this->campoSeguro('nombreOrdenador')?>').attr('disabled','');     
	          $('#<?php echo $this->campoSeguro('proveedor')?>').attr('disabled','');
	          $('#<?php echo $this->campoSeguro('nitProveedor')?>').attr('disabled','');
	          $('#<?php echo $this->campoSeguro('direccionProveedor')?>').attr('disabled','');
      	  	  $('#<?php echo $this->campoSeguro('telefonoProveedor')?>').attr('disabled','');
        	  $('#<?php echo $this->campoSeguro('direccionDependencia')?>').attr('disabled','');
        	  $('#<?php echo $this->campoSeguro('telefonoDependencia')?>').attr('disabled','');
        	  $('#<?php echo $this->campoSeguro('total_iva')?>').attr('disabled',''); 
		    $('#<?php echo $this->campoSeguro('total')?>').attr('disabled','');  
		    $('#<?php echo $this->campoSeguro('total_preliminar')?>').attr('disabled','');
        		
        	

             $("#<?php echo $this->campoSeguro('dependencia_soli')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 3,
              	 });
             
                 
               $("#<?php echo $this->campoSeguro('supervision')?>").select2({
			   	 placeholder: "Search for a repository",
			   	 minimumInputLength: 3,
			
			       }); 	
             
             
                  $('#<?php echo $this->campoSeguro('nombreCotizacion')?>').attr('disabled','');
                  
        $( "#<?php echo $this->campoSeguro('actualizarCotizacion')?>" ).change(function() {
        
            switch($("#<?php echo $this->campoSeguro('actualizarCotizacion')?>").val())
            {
                case '0':
                    $("#<?php echo $this->campoSeguro('cargaCotizacion')?>").css('display','none');
                    
                break;
            
                case '1':
                    
                   $("#<?php echo $this->campoSeguro('cargaCotizacion')?>").css('display','block');

                break;
             }
          });  
                 
                 
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
	   
	   
        $('#<?php echo $this->campoSeguro('fecha_diponibilidad')?>').datepicker({
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
        
        
        
                
         
        
            $( "#<?php echo $this->campoSeguro('reg_proveedor')?>" ).change(function() {
        
        
        if($( "#<?php echo $this->campoSeguro('reg_proveedor')?>" ).val()==0){
        
      
        
        $('#<?php echo $this->campoSeguro('proveedorDiv')?>').css('display','block');
        
        $('#<?php echo $this->campoSeguro('proveedor')?>').val('');
        $('#<?php echo $this->campoSeguro('nitProveedor')?>').val('');
        $('#<?php echo $this->campoSeguro('direccionProveedor')?>').val('');
        $('#<?php echo $this->campoSeguro('telefonoProveedor')?>').val('');
        	
        $('#<?php echo $this->campoSeguro('proveedor')?>').attr('disabled','');
        $('#<?php echo $this->campoSeguro('nitProveedor')?>').attr('disabled','');
        $('#<?php echo $this->campoSeguro('direccionProveedor')?>').attr('disabled','');
        $('#<?php echo $this->campoSeguro('telefonoProveedor')?>').attr('disabled','');
        	

        	
        	
        
        }else if($( "#<?php echo $this->campoSeguro('reg_proveedor')?>" ).val()==1){
        
        
        $('#<?php echo $this->campoSeguro('proveedorDiv')?>').css('display','none');
        $('#<?php echo $this->campoSeguro('proveedor')?>').val('');
        $('#<?php echo $this->campoSeguro('nitProveedor')?>').val('');
        $('#<?php echo $this->campoSeguro('direccionProveedor')?>').val('');
        $('#<?php echo $this->campoSeguro('telefonoProveedor')?>').val('');
        $('#<?php echo $this->campoSeguro('proveedor')?>').removeAttr('disabled');
        $('#<?php echo $this->campoSeguro('nitProveedor')?>').removeAttr('disabled');
        $('#<?php echo $this->campoSeguro('direccionProveedor')?>').removeAttr('disabled');
        $('#<?php echo $this->campoSeguro('telefonoProveedor')?>').removeAttr('disabled');
        
        }
       
        }); 
        
        
        $("#<?php echo $this->campoSeguro('vigencia_disponibilidad')?>").select2();
		$('#<?php echo $this->campoSeguro('diponibilidad')?>').select2();
		
		$("#<?php echo $this->campoSeguro('vigencia_registro')?>").select2();
		$("#<?php echo $this->campoSeguro('registro')?>").select2(); 
		
		$("#<?php echo $this->campoSeguro('vigencia_contratista')?>").select2();
		$('#<?php echo $this->campoSeguro('nombreContratista')?>').attr("style", "width: 60px '");

        
        