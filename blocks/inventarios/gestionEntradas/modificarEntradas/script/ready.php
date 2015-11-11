<?php

?>

// Asociar el widget de validación al formulario
              $("#modificarEntradas").validationEngine({
            promptPosition : "centerRight", 
            scroll: false,
            autoHidePrompt: true,
            autoHideDelay: 2000
	         });
	
        
        $(function() {
            $("#modificarEntradas").submit(function() {
                $resultado=$("#modificarEntradas").validationEngine("validate");
                   
                   
                   
                if ($resultado) {
                
                    return true;
                }
                return false;
            });
        });

                     $('#tablaTitulos').dataTable( {
                "sPaginationType": "full_numbers"
                 } );

                     
                     
                     
         $("#<?php echo $this->campoSeguro('supervisor')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 5,
              	 });

         
         
                  
         $("#<?php echo $this->campoSeguro('id_salidaR')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
         $("#<?php echo $this->campoSeguro('id_entradaR')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
         
         $("#<?php echo $this->campoSeguro('id_hurtoR')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
         
         
         $("#<?php echo $this->campoSeguro('id_salidaS')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
         $("#<?php echo $this->campoSeguro('id_entradaS')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
		 $("#<?php echo $this->campoSeguro('num_placa')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 3,
              	 });
         
              	 
         
                               
          
          $("#<?php echo $this->campoSeguro('sede')?>").select2();
          $("#<?php echo $this->campoSeguro('dependencia')?>").select2();
          $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();
          
          $("#<?php echo $this->campoSeguro('acta_recibido')?>").select2();

      $("#<?php echo $this->campoSeguro('numero_entrada')?>").select2();
      
       $("#<?php echo $this->campoSeguro('clase')?>").select2();
                               
      $("#<?php echo $this->campoSeguro('tipo_contrato')?>").select2();
             
      $('#<?php echo $this->campoSeguro('nombreCotizacion')?>').attr('disabled','');
                  


        $('#<?php echo $this->campoSeguro('nombreCotizacion')?>').attr('disabled','');
        

                  
        $( "#<?php echo $this->campoSeguro('clase')?>" ).change(function() {
        
        	
        	
        	$("#<?php echo $this->campoSeguro('fecha_factura')?>").val('');
        	$("#<?php echo $this->campoSeguro('numero_factura')?>").val('');
        	$("#<?php echo $this->campoSeguro('fecha_contrato')?>").val('');
        	$("#<?php echo $this->campoSeguro('numero_contrato')?>").val('');
        
            switch($("#<?php echo $this->campoSeguro('clase')?>").val())
            {
                           
                case '1':
                   $("#<?php echo $this->campoSeguro('adquisicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('avances')?>").css('display','none');   
                   $("#<?php echo $this->campoSeguro('reposicion')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('donacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('sobrante')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('produccion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('recuperacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('tipo_cotr')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('inf_contr')?>").css('display',' none');
                   $("#<?php echo $this->campoSeguro('inf_provee')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('cuadro_ordenador')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();
    
              	 
                 
         $("#<?php echo $this->campoSeguro('id_salidaR')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
         $("#<?php echo $this->campoSeguro('id_entradaR')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
         
         $("#<?php echo $this->campoSeguro('id_hurtoR')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
             
             
                     
                   estado();
                   

                break;
                
                
                 case '2':
                   $("#<?php echo $this->campoSeguro('adquisicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('avances')?>").css('display','none');   
                   $("#<?php echo $this->campoSeguro('reposicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('donacion')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('sobrante')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('produccion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('recuperacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('tipo_cotr')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('inf_contr')?>").css('display',' none');
                   $("#<?php echo $this->campoSeguro('inf_provee')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('cuadro_ordenador')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();

                   estado();
                break;
                
                case '3':
                   $("#<?php echo $this->campoSeguro('adquisicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('avances')?>").css('display','none');   
           		   $("#<?php echo $this->campoSeguro('reposicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('donacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('sobrante')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('produccion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('recuperacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('tipo_cotr')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('inf_contr')?>").css('display',' none');
                   $("#<?php echo $this->campoSeguro('inf_provee')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('cuadro_ordenador')?>").css('display','none');
                   
   
              	 
         
         $("#<?php echo $this->campoSeguro('id_salidaS')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
         $("#<?php echo $this->campoSeguro('id_entradaS')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 1,
              	 });
		 $("#<?php echo $this->campoSeguro('num_placa')?>").select2({
             	 placeholder: "Search for a repository",
              	 minimumInputLength: 3,
              	 });
         
              	 
              	 
             
                   estado();

                break;
                
                case '4':
  					$("#<?php echo $this->campoSeguro('adquisicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('avances')?>").css('display','none');                    
        		   $("#<?php echo $this->campoSeguro('reposicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('donacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('sobrante')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('produccion')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('recuperacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('tipo_cotr')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('inf_contr')?>").css('display',' none');
                   $("#<?php echo $this->campoSeguro('inf_provee')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('cuadro_ordenador')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();

             
                   estado();

                break;
                
                case '5':
                    $("#<?php echo $this->campoSeguro('adquisicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('avances')?>").css('display','none');  
                   $("#<?php echo $this->campoSeguro('reposicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('donacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('sobrante')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('produccion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('recuperacion')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('tipo_cotr')?>").css('display',' none');
                   $("#<?php echo $this->campoSeguro('inf_contr')?>").css('display',' none');
                   $("#<?php echo $this->campoSeguro('inf_provee')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();

                   
                   estado();
                break;
                
                     case '6':
                    
                   $("#<?php echo $this->campoSeguro('adquisicion')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('avances')?>").css('display','none'); 
                   $("#<?php echo $this->campoSeguro('reposicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('donacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('sobrante')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('produccion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('recuperacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('tipo_cotr')?>").css('display',' block');
                   $("#<?php echo $this->campoSeguro('inf_contr')?>").css('display',' block');
                   $("#<?php echo $this->campoSeguro('inf_provee')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('cuadro_ordenador')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();
	
                   
                   estado();
                break;
                
                    case '7':
                    
                   $("#<?php echo $this->campoSeguro('adquisicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('avances')?>").css('display','block'); 
                   
                   $("#<?php echo $this->campoSeguro('reposicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('donacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('sobrante')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('produccion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('recuperacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('tipo_cotr')?>").css('display',' none');
                   $("#<?php echo $this->campoSeguro('inf_contr')?>").css('display',' none');
                   $("#<?php echo $this->campoSeguro('inf_provee')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('cuadro_ordenador')?>").css('display','block');
                   $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").select2();
			
             
                   
                   estado();
                break;
                
                
                
               default:

                   $("#<?php echo $this->campoSeguro('reposicion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('donacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('sobrante')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('produccion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('recuperacion')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('tipo_cotr')?>").css('display','none');
                   $('#<?php echo $this->campoSeguro('numero_contrato')?>').removeAttr('disabled');
                   $('#<?php echo $this->campoSeguro('fecha_contrato')?>').removeAttr('disabled');
                   $("#<?php echo $this->campoSeguro('tipo_cotr')?>").css('display','none');
                   $("#<?php echo $this->campoSeguro('cuadro_ordenador')?>").css('display','block');
                   break;
                
                
             }
          });  
                 
             $('#<?php echo $this->campoSeguro('fecha_contrato')?>').datepicker({
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
			
			
		});
		
		
       $('#<?php echo $this->campoSeguro('vigencia')?>').datepicker({
		
			changeYear: true,
			maxDate:0,
			monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
			'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			dateFormat: 'yy',
			onClose: function(dateText, inst) {
			//lockDate.setDate(lockDate.getDate() + 1);
			var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
			$(this).datepicker('setDate', new Date(year, 1, 1));
			
			}
		});
		$('#<?php echo $this->campoSeguro('vigencia')?>').focus(function () {
			$(".ui-datepicker-calendar").hide();
			$("#ui-datepicker-div").position({
			my: "center top",
			at: "center bottom",
			of: $(this)
				});
			}); 
		
		
		
		
		   $('#<?php echo $this->campoSeguro('fecha_factura')?>').datepicker({
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
        
        
        
                
         
        