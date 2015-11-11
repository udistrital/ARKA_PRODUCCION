<?php

?>

// Asociar el widget de validación al formulario
              $("#modificarSalidas").validationEngine({
            promptPosition : "centerRight", 
            scroll: false,
            autoHidePrompt: true,
            autoHideDelay: 2000
	         });
	
        
        $(function() {
            $("#modificarSalidas").submit(function() {
                $resultado=$("#modificarSalidas").validationEngine("validate");
                   
                   
                   
                if ($resultado) {
                
                    return true;
                }
                return false;
            });
        });

                     $('#tablaTitulos').dataTable( {
                      "scrollY":"350px",
        			  "scrollCollapse": true,
        			  "paging":false
			        	          } );

                     
                 $("#<?php echo $this->campoSeguro('funcionarioP')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });

             
 $('#<?php echo $this->campoSeguro('numero_entrada_c')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('fecha_entrada')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('clase_entrada')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('razon_social')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('nit_proveedor')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('numero_factura')?>').attr('disabled','');
 $('#<?php echo $this->campoSeguro('fecha_factura')?>').attr('disabled','');
 
 $("#<?php echo $this->campoSeguro('sede')?>").select2();
 
 $("#<?php echo $this->campoSeguro('numero_salida')?>").select2();
 $("#<?php echo $this->campoSeguro('numero_entrada')?>").select2();
 $("#<?php echo $this->campoSeguro('dependencia')?>").select2();
 
 
      $("#<?php echo $this->campoSeguro('funcionario')?>").select2({
             	 placeholder: "Ingrese Mínimo 3 Caracteres de Búsqueda",
              	 minimumInputLength: 3,
              	 });
 
 
 $("#<?php echo $this->campoSeguro('ubicacion')?>").select2();
                  
     
      
             
                 
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
	   
	   
         $( "#<?php echo $this->campoSeguro('actualizar')?>" ).change(function() {
        
            switch($("#<?php echo $this->campoSeguro('actualizar')?>").val())
            {
                           
                case '0':
                    
                   $("#<?php echo $this->campoSeguro('itemsAgr')?>").css('display','none');

                   

                break;
                
                
                       case '1':
                    
                    $("#<?php echo $this->campoSeguro('itemsAgr')?>").css('display','block');
       
                break;
                

                default:
                
                         $("#<?php echo $this->campoSeguro('itemsAgr')?>").css('display','none');
                   
                   break;
                
                
             }
          });  
          
             $('#<?php echo $this->campoSeguro('anio')?>').datepicker({
		
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
		$('#<?php echo $this->campoSeguro('anio')?>').focus(function () {
			$(".ui-datepicker-calendar").hide();
			$("#ui-datepicker-div").position({
			my: "center top",
			at: "center bottom",
			of: $(this)
				});
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
		
        
                
         
        
        
        
          






