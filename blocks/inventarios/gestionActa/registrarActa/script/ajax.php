<?php
/**
 *
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
 */
// URL base
$url = $this->miConfigurador->getVariableConfiguracion("host");
$url .= $this->miConfigurador->getVariableConfiguracion("site");
$url .= "/index.php?";

// Variables
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= $cadenaACodificar . "&funcion=tablaItems";
$cadenaACodificar .="&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL definitiva
$urlFinal = $url . $cadena;

// Variables
$cadenaACodificar2 = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar2 .= "&procesarAjax=true";
$cadenaACodificar2 .= "&action=index.php";
$cadenaACodificar2 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar2 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar2 .= $cadenaACodificar . "&funcion=AgregarItem";
$cadenaACodificar2.="&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace2 = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena2 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar2, $enlace2);

// URL definitiva
$urlFinal2 = $url . $cadena2;

// Variables
$cadenaACodificar3 = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar3 .= "&procesarAjax=true";
$cadenaACodificar3 .= "&action=index.php";
$cadenaACodificar3 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar3 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar3 .= $cadenaACodificar . "&funcion=EliminarItem";
$cadenaACodificar3 .="&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace3 = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena3 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar3, $enlace3);

// URL definitiva
$urlFinal3 = $url . $cadena3;

// Variables
$cadenaACodificar4 = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar4 .= "&procesarAjax=true";
$cadenaACodificar4 .= "&action=index.php";
$cadenaACodificar4 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar4 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar4 .= $cadenaACodificar . "&funcion=proveedor";
$cadenaACodificar4 .="&tiempo=" . $_REQUEST['tiempo'];

// Codificar las variables
$enlace4 = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena4 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar4, $enlace4);

// URL definitiva
$urlFinal4 = $url . $cadena4;




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
$cadenaACodificar17 .= $cadenaACodificar17 . "&funcion=consultarInfoContrato";
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



// Variables
$cadenaACodificarTipoBien = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificarTipoBien .= "&procesarAjax=true";
$cadenaACodificarTipoBien .= "&action=index.php";
$cadenaACodificarTipoBien .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificarTipoBien .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificarTipoBien .= "&funcion=SeleccionTipoBien";
$cadenaACodificarTipoBien .="&tiempo=".$_REQUEST['tiempo'];


// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificarTipoBien, $enlace );

// URL definitiva
$urlFinalTipoBien = $url . $cadena;




// echo $urlFinal;exit;
// echo $urlFinal2;
// echo $urlFinal3;
?>
<script type='text/javascript'>




function tipo_bien(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinalTipoBien?>",
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




function consultarContrato(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal17?>",
	    dataType: "json",
	    data: { valor:$("#<?php echo $this->campoSeguro('numeroContrato')?>").val()},
	    success: function(data){ 



	        if(data[0]!=" "){

	        	$("#documentoContrato").attr("href",data['documento_ruta']);
	        	$("#documentoContrato").attr("target","_blank");


	        	
	        	$("#<?php echo $this->campoSeguro('nitproveedor')?> option[value="+ data['nombre_contratista'] +"]").attr("selected",true);
	        	$("#<?php echo $this->campoSeguro('nitproveedor')?>").select2();
	        	
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
		            
		            $('#<?php echo $this->campoSeguro('dependencia')?>').width(815);
		            $("#<?php echo $this->campoSeguro('dependencia')?>").select2();
		            
		          
		            
			        }
		    			

		    }
			                    
		   });
		};


function datosInfo(elem, request, response) {

    $("#<?php echo $this->campoSeguro('proveedor') ?>").val();

    $.ajax({
        url: "<?php echo $urlFinal4 ?>",
        dataType: "json",
        data: {proveedor: $("#<?php echo $this->campoSeguro('nitproveedor') ?>").val()},
        success: function (data) {

            if (data[0] != 'null') {

                $("#<?php echo $this->campoSeguro('proveedor') ?>").val(data[0]);
            } else {
                $("#<?php echo $this->campoSeguro('proveedor') ?>").val();
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
							    			
		    		}else{

				


			    		
		    		}

	    }
		                    
	   });
	};
    $(function () {

        $("#tablaContenido").jqGrid({
            url: "<?php echo $urlFinal ?>",
            datatype: "json",
            height: 200,
            width: 930,
            mtype: "GET",
            colNames: ["Item", "Cantidad", "Descripción", "Valor Unitario", "Valor Total"],
            colModel: [
                {name: "item", width: 90, align: "center", editable: true},
                {name: "cantidad", width: 105, align: "center", editable: true, editrules: {number: true}, sorttype: 'number', formatter: 'number'},
                {name: "descripcion", width: 105, align: "center", editable: true},
                {name: "valor_unitario", width: 105, align: "center", editable: true, editrules: {number: true}, sorttype: 'number', formatter: 'number'},
                {name: "valor_total", width: 105, align: "center", editable: false, editrules: {number: true}, sorttype: 'number', formatter: 'number'}
            ],
            pager: "#barraNavegacion",
            rowNum: 10,
            rowList: [10, 20, 30],
            sortname: "id_items",
            sortorder: "desc",
            viewrecords: false,
            loadtext: "Cargando...",
            pgtext: "Pagina {0} de {1}",
            caption: "Descripción Factura ",
        }).navGrid('#barraNavegacion',
                {
                    add: true,
                    addtext: 'Añadir Item',
                    edit: false,
                    del: true,
                    deltext: 'Eliminar Item',
                    alertcap: "Alerta",
                    alerttext: "Seleccione Item",
                    search: false,
                    refresh: true,
                    refreshstate: 'current',
                    refreshtext: 'Refrescar Items',
                },
                {}, //edit
                {
                    caption: "Añadir Item",
                    addCaption: "Adicionar Item",
                    width: 390,
                    height: 252,
                    mtype: 'GET',
                    url: '<?php echo $urlFinal2 ?>',
                    bSubmit: "Agregar",
                    bCancel: "Cancelar",
                    bClose: "Cerrar",
                    saveData: "Los datos han cambiado, ¿desea guardarlos?",
                    bYes: "Sí",
                    bNo: "No",
                    bExit: "Salir",
                    closeOnEscape: true,
                    closeAfterAdd: true,
                    onclickSubmit: function (params, postdata) {
                        //save add
                        var p = params;
                        var pt = postdata;
                    },
                    beforeSubmit: function (postdata, formid) {
                        var p = postdata;
                        var id = id;
                        var success = true;
                        var message = "continue";
                        return[success, message];
                    },
                    afterSubmit: function (response, postdata)
                    {
                        var r = response;
                        var p = postdata;
                        var responseText = jQuery.jgrid.parse(response.responseText);
                        var success = true;
                        var message = "continue";
                        return [success, message]
                    },
                    afterComplete: function (response, postdata, formid) {
                        var responseText = jQuery.jgrid.parse(response.responseText);
                        var r = response;
                        var p = postdata;
                        var f = formid;
                    }}, //add
        {
            url: '<?php echo $urlFinal3 ?>',
            caption: "Eliminar Item",
            width: 425,
            height: 150,
            mtype: 'GET',
            bSubmit: "Eliminar",
            bCancel: "Cancelar",
            bClose: "Cerrar",
            msg: "Desea Eliminar Item ?",
            bYes: "Yes",
            bNo: "No",
            bExit: "Salir",
            closeOnEscape: true,
            closeAfterAdd: true,
            refresh: true,
            onclickSubmit: function (params, postdata, id_items) {
                //save add
                var p = params;
                var pt = postdata;
            },
            beforeSubmit: function (postdata, formid) {
                var p = postdata;
                var id = formid;
                var success = true;
                var message = "continue";
                return[success, message];
            },
            afterSubmit: function (response, postdata)
            {
                var r = response;
                var p = postdata;
                var responseText = jQuery.jgrid.parse(response.responseText);
                var success = true;
                var message = "continue";
                return [success, message]
            },
            afterComplete: function (response, postdata, formid) {
                var responseText = jQuery.jgrid.parse(response.responseText);
                var r = response;
                var p = postdata;
                var f = formid;
            }

        }, //del
        {},
                {}
        );


        $("#<?php echo $this->campoSeguro('numeroContrato')?>").change(function(){
        	if($("#<?php echo $this->campoSeguro('numeroContrato')?>").val()!=''){

        		consultarContrato();
    		}else{
    			
    			}

    	      });


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
        				$("#<?php echo $this->campoSeguro('id_ordenador')?>").val('');
        				}
        		      });


        $("#<?php echo $this->campoSeguro('asignacionOrdenador') ?>").select2();








        


        $( "#<?php echo $this->campoSeguro('nitproveedor')?>" ).keyup(function() {

        	
    	$('#<?php echo $this->campoSeguro('nitproveedor') ?>').val($('#<?php echo $this->campoSeguro('nitproveedor') ?>').val().toUpperCase());




    	
            });



        $( "#<?php echo $this->campoSeguro('nitproveedor')?>" ).change(function() {


            
        	if($('#<?php echo $this->campoSeguro('nitproveedor') ?>').val()==''){

        		$("#<?php echo $this->campoSeguro('id_proveedor') ?>").val('');

            	


            	}
        
        	
                });
        


        $("#<?php echo $this->campoSeguro('nitproveedor') ?>").autocomplete({
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

