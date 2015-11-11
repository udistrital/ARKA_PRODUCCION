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
$cadenaACodificar .= $cadenaACodificar . "&funcion=tablaItems";
$cadenaACodificar .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL definitiva
$urlFinal = $url . $cadena;

// Variables
$cadenaACodificar2 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar2 .= "&procesarAjax=true";
$cadenaACodificar2 .= "&action=index.php";
$cadenaACodificar2 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar2 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar2 .= $cadenaACodificar . "&funcion=AgregarItem";
$cadenaACodificar2 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace2 = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena2 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar2, $enlace2 );

// URL definitiva
$urlFinal2 = $url . $cadena2;

// Variables
$cadenaACodificar3 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar3 .= "&procesarAjax=true";
$cadenaACodificar3 .= "&action=index.php";
$cadenaACodificar3 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar3 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar3 .= $cadenaACodificar . "&funcion=EliminarItem";
$cadenaACodificar3 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace3 = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena3 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar3, $enlace3 );

// URL definitiva
$urlFinal3 = $url . $cadena3;

// Variables
$cadenaACodificar4 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar4 .= "&procesarAjax=true";
$cadenaACodificar4 .= "&action=index.php";
$cadenaACodificar4 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar4 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar4 .= $cadenaACodificar . "&funcion=SeleccionProveedor";
$cadenaACodificar4 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace4 = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena4 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar4, $enlace4 );

// URL definitiva
$urlFinal4 = $url . $cadena4;

// Variables
$cadenaACodificar5 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar5 .= "&procesarAjax=true";
$cadenaACodificar5 .= "&action=index.php";
$cadenaACodificar5 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar5 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar5 .= $cadenaACodificar . "&funcion=SeleccionDependencia";
$cadenaACodificar5 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace5 = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena5 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar5, $enlace5 );

// URL definitiva
$urlFinal5 = $url . $cadena5;

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
$cadenaACodificar7 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar7 .= "&procesarAjax=true";
$cadenaACodificar7 .= "&action=index.php";
$cadenaACodificar7 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar7 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar7 .= $cadenaACodificar . "&funcion=SeleccionCargo";
$cadenaACodificar7 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace7 = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena7 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar7, $enlace7 );

// URL definitiva
$urlFinal7 = $url . $cadena7;

// Variables
$cadenaACodificar8 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar8 .= "&procesarAjax=true";
$cadenaACodificar8 .= "&action=index.php";
$cadenaACodificar8 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar8 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar8 .= $cadenaACodificar . "&funcion=CalItem";
$cadenaACodificar8 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace8 = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena8 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar8, $enlace8 );

// URL definitiva
$urlFinal8 = $url . $cadena8;

// Variables
$cadenaACodificar9 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar9 .= "&procesarAjax=true";
$cadenaACodificar9 .= "&action=index.php";
$cadenaACodificar9 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar9 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar9 .= $cadenaACodificar9 . "&funcion=letrasNumeros";
$cadenaACodificar9 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena9 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar9, $enlace );

// URL definitiva
$urlFinal9 = $url . $cadena9;

// Variables
$cadenaACodificar10 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar10 .= "&procesarAjax=true";
$cadenaACodificar10 .= "&action=index.php";
$cadenaACodificar10 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar10 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar10 .= $cadenaACodificar10 . "&funcion=disponibilidades";
$cadenaACodificar10 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena10 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar10, $enlace );

// URL definitiva
$urlFinal10 = $url . $cadena10;

// Variables
$cadenaACodificar12 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar12 .= "&procesarAjax=true";
$cadenaACodificar12 .= "&action=index.php";
$cadenaACodificar12 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar12 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar12 .= $cadenaACodificar12 . "&funcion=Infodisponibilidades";
$cadenaACodificar12 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena12 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar12, $enlace );

// URL definitiva
$urlFinal12 = $url . $cadena12;


// Variables
$cadenaACodificar13 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar13 .= "&procesarAjax=true";
$cadenaACodificar13 .= "&action=index.php";
$cadenaACodificar13 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar13 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar13 .= $cadenaACodificar13 . "&funcion=registroPresupuestal";
$cadenaACodificar13 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena13 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar13, $enlace );

// URL definitiva
$urlFinal13 = $url . $cadena13;



// Variables
$cadenaACodificar14 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar14 .= "&procesarAjax=true";
$cadenaACodificar14 .= "&action=index.php";
$cadenaACodificar14 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar14 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar14 .= $cadenaACodificar14 . "&funcion=Inforegistro";
$cadenaACodificar14 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena14 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar14, $enlace );

// URL definitiva
$urlFinal14 = $url . $cadena14;


// Variables
$cadenaACodificar15 = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar15 .= "&procesarAjax=true";
$cadenaACodificar15 .= "&action=index.php";
$cadenaACodificar15 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar15 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar15 .= $cadenaACodificar15 . "&funcion=consultarContratistas";
$cadenaACodificar15 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena15 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar15, $enlace );

// URL definitiva
$urlFinal15 = $url . $cadena15;





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
$cadenaACodificarDependencia = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificarDependencia .= "&procesarAjax=true";
$cadenaACodificarDependencia .= "&action=index.php";
$cadenaACodificarDependencia .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificarDependencia .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificarDependencia .= $cadenaACodificarDependencia . "&funcion=consultarDependencia";
$cadenaACodificarDependencia .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadenaDependencia = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificarDependencia, $enlace );

// URL definitiva
$urlFinalDependencia = $url . $cadenaDependencia;

// Variables
$cadenaACodificarUbicacion = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificarUbicacion .= "&procesarAjax=true";
$cadenaACodificarUbicacion .= "&action=index.php";
$cadenaACodificarUbicacion .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificarUbicacion .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificarUbicacion .= $cadenaACodificarUbicacion . "&funcion=consultarUbicacion";
$cadenaACodificarUbicacion .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadenaUbicacion = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificarUbicacion, $enlace );

// URL definitiva
$urlFinalUbicacion = $url . $cadenaUbicacion;







?>
<script type='text/javascript'>

function valorLetras(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal9?>",
	    dataType: "json",
	    data: { valor:$("#<?php echo $this->campoSeguro('total')?>").val()},
	    success: function(data){ 


	    			$("#<?php echo $this->campoSeguro('valorLetras_registro')?>").val(data);

	    }
		                    
	   });
	};





	function consultarDependencia(elem, request, response){
		  $.ajax({
		    url: "<?php echo $urlFinalDependencia?>",
		    dataType: "json",
		    data: { valor:$("#<?php echo $this->campoSeguro('sede')?>").val()},
		    success: function(data){ 



		        if(data[0]!=" "){

		            $("#<?php echo $this->campoSeguro('selec_dependencia')?>").html('');
		            $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('selec_dependencia')?>");
		            $.each(data , function(indice,valor){

		            	$("<option value='"+data[ indice ].ESF_CODIGO_DEP+"'>"+data[ indice ].ESF_DEP_ENCARGADA+"</option>").appendTo("#<?php echo $this->campoSeguro('selec_dependencia')?>");
		            	
		            });
		            
		            $("#<?php echo $this->campoSeguro('selec_dependencia')?>").removeAttr('disabled');
		            
		            $('#<?php echo $this->campoSeguro('selec_dependencia')?>').width(250);
		            $("#<?php echo $this->campoSeguro('selec_dependencia')?>").select2();
		            
		          
		            
			        }
		    			

		    }
			                    
		   });
		};





		function consultarEspacio(elem, request, response){
			  $.ajax({
			    url: "<?php echo $urlFinalUbicacion?>",
			    dataType: "json",
			    data: { valor:$("#<?php echo $this->campoSeguro('selec_dependencia')?>").val()},
			    success: function(data){ 



			        if(data[0]!=" "){

			            $("#<?php echo $this->campoSeguro('ubicacion')?>").html('');
			            $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('ubicacion')?>");
			            $.each(data , function(indice,valor){

			            	$("<option value='"+data[ indice ].ESF_ID_ESPACIO+"'>"+data[ indice ].ESF_NOMBRE_ESPACIO+"</option>").appendTo("#<?php echo $this->campoSeguro('ubicacion')?>");
			            	
			            });
			            
			            $("#<?php echo $this->campoSeguro('ubicacion')?>").removeAttr('disabled');
			            
			            $('#<?php echo $this->campoSeguro('ubicacion')?>').width(200);
			            $("#<?php echo $this->campoSeguro('ubicacion')?>").select2();
			            
			          
			            
				        }
			    			

			    }
				                    
			   });
			};
		

	


	


	function valorLetrasDis(elem, request, response){
		  $.ajax({
		    url: "<?php echo $urlFinal9?>",
		    dataType: "json",
		    data: { valor:$("#<?php echo $this->campoSeguro('valor_disponibilidad')?>").val()},
		    success: function(data){ 


		    			$("#<?php echo $this->campoSeguro('valorLetras_disponibilidad')?>").val(data);

		    }
			                    
		   });
		};




		function valorLetrasReg(elem, request, response){
			  $.ajax({
			    url: "<?php echo $urlFinal9?>",
			    dataType: "json",
			    data: { valor:$("#<?php echo $this->campoSeguro('valor_registro')?>").val()},
			    success: function(data){ 


			    			$("#<?php echo $this->campoSeguro('valorL_registro')?>").val(data);

			    }
				                    
			   });
			};


		

	

function CalItem(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal8?>",
	    dataType: "json",
	    data: { tiempo:"<?php echo $_REQUEST['tiempo']?>"},
	    success: function(data){ 

	    		if(data[0]!='null'){

	    			$("#<?php echo $this->campoSeguro('total_preliminar')?>").val(data[0]);

	    			$("#<?php echo $this->campoSeguro('total_iva')?>").val(0);
	    			
	    			$('#<?php echo $this->campoSeguro('iva')?>').val(0);
	    				    			
	    			$("#<?php echo $this->campoSeguro('total')?>").val(Number(data[0])+Number($("#<?php echo $this->campoSeguro('total_iva')?>").val()));

	    			$("#<?php echo $this->campoSeguro('valorLetras_registro')?>").val(data[1]);
	    			
	    			
	    			
		    		}else{

				


			    		
		    		}

	    }
		                    
	   });
	};




function datosInfo(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal4?>",
	    dataType: "json",
	    data: { tiempo:$("#<?php echo $this->campoSeguro('selec_proveedor')?>").val()},
	    success: function(data){ 

	    		if(data[0]!='null'){

	    			$("#<?php echo $this->campoSeguro('proveedor')?>").val(data[0]);
					$("#<?php echo $this->campoSeguro('nitProveedor')?>").val(data[1]);
					$("#<?php echo $this->campoSeguro('direccionProveedor')?>").val(data[2]);
					$("#<?php echo $this->campoSeguro('telefonoProveedor')?>").val(data[3]);

	    			
		    		}else{

				


			    		
		    		}

	    }
		                    
	   });
	};




function datosInfo(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal4?>",
	    dataType: "json",
	    data: { personaje:$("#<?php echo $this->campoSeguro('selec_proveedor')?>").val()},
	    success: function(data){ 

	    		if(data[0]!='null'){

	    			$("#<?php echo $this->campoSeguro('proveedor')?>").val(data[0]);
					$("#<?php echo $this->campoSeguro('nitProveedor')?>").val(data[1]);
					$("#<?php echo $this->campoSeguro('direccionProveedor')?>").val(data[2]);
					$("#<?php echo $this->campoSeguro('telefonoProveedor')?>").val(data[3]);

		    		}else{
			    		
		    		}

	    }
		                    
	   });
	};



	function datosCargo(elem, request, response){
		  $.ajax({
		    url: "<?php echo $urlFinal7?>",
		    dataType: "json",
		    data: { cargo:$("#<?php echo $this->campoSeguro('cargoJefeSeccion')?>").val()},
		    success: function(data){ 

		    		if(data[0]!='null'){

		    			$("#<?php echo $this->campoSeguro('nombreJefeSeccion')?>").val(data[0]);
		    			$("#<?php echo $this->campoSeguro('id_jefe')?>").val(data[1]);
		    			
								    			
			    		}else{

					


				    		
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





		function datosDependencia(elem, request, response){
			  $.ajax({
			    url: "<?php echo $urlFinal5?>",
			    dataType: "json",
			    data: { dependencia:$("#<?php echo $this->campoSeguro('selec_dependencia')?>").val()},
			    success: function(data){ 

			    		if(data[0]!='null'){

			    			$("#<?php echo $this->campoSeguro('direccionDependencia')?>").val(data[0]);
							$("#<?php echo $this->campoSeguro('telefonoDependencia')?>").val(data[1]);
			

			    			
				    		}else{

						


					    		
				    		}

			    }
				                    
			   });
			};


$(function() {

    $("#tablaContenido").jqGrid({
        url: "<?php echo $urlFinal?>",
        datatype: "json",
        height: 200,
        width: 930,
        mtype: "GET",
        colNames: [ "Nombre Item", "Unidad de Medida", "Cantidad", "Descripción", "($)Valor Unitario","($)Valor Total","($)Descuento"],
        colModel: [
            
            { name: "item", width: 90,align: "center", editable:true },
            { name: "unidad_medida", width: 100, align: "center", editable:true },
            { name: "cantidad", width: 80, align: "center" ,editable:true,editrules:{number:true},sorttype:'number',formatter:'number' },
            { name: "descripcion", width: 80, align: "center",editable:true },
            { name: "valor_unitario", width: 80, align: "center",editable:true,editrules:{number:true},sorttype:'number',formatter:'number' },
            { name: "valor_total", width: 80, align: "center",editable:false,editrules:{number:true},sorttype:'number',formatter:'number' },
            { name: "descuento", width: 80, align: "center",editable:true,editrules:{number:true},sorttype:'number',formatter:'number' },
            ],

        pager: "#barraNavegacion",
        rowNum: 10,
        rowList: [10, 20, 30],
        sortname: "id_items",
        sortorder: "desc",
        viewrecords: false,
        loadtext: "Cargando...",
        pgtext : "Pagina {0} de {1}",
        caption: "Solicitud Items ",
        
        
               
    }).navGrid('#barraNavegacion',
    	    {	
	    add:true,
	    addtext:'Añadir Item',
		edit:false,	    		
		del:true ,
		deltext:'Eliminar Item',
		alertcap:"Alerta",
        alerttext:"Seleccione Item",
		search:false ,
		refresh:true,
		refreshstate: 'current',
		refreshtext:'Refrescar Items',
		},

    { },//edit
    {

        caption:"Añadir Item",
        addCaption: "Adicionar Item",
        width: 425, 
        height: 325,
        mtype:'GET',
        url:'<?php echo $urlFinal2?>',
        bSubmit: "Agregar",
        bCancel: "Cancelar",
        bClose: "Close",
        saveData: "Data has been changed! Save changes?",
        bYes : "Yes",
        bNo : "No",
        bExit : "Cancel",
        closeOnEscape:true,
        closeAfterAdd:true,
        onclickSubmit:function(params, postdata){
            //save add
            var p=params;
            var pt=postdata;
        },
        beforeSubmit : function(postdata, formid) { 
            var p = postdata;
            var id=id;
            var success=true;
            var message="continue";
            return[success,message]; 
        },    
        afterSubmit : function(response, postdata) 
        { 
            var r=response; 
            var p=postdata;
            var responseText=jQuery.jgrid.parse(response.responseText);
            var success=true;
            var message="continue";
            return [success,message] 
        },
        afterComplete : function (response, postdata, formid) {        
            var responseText=jQuery.jgrid.parse(response.responseText);
            var r=response;
            var p=postdata;
            var f=formid;
            CalItem();

            
        } },//add
     {
			
             
            url:'<?php echo $urlFinal3?>',
            caption: "Eliminar Item",
            width: 425, 
            height: 150,
            mtype:'GET',
            bSubmit: "Eliminar",
            bCancel: "Cancelar",
            bClose: "Close",
            msg:"Desea Eliminar Item ?",
            bYes : "Yes",
            bNo : "No",
            bExit : "Cancel",
            closeOnEscape:true,
            closeAfterAdd:true,
            refresh:true,
            onclickSubmit:function(params, postdata,id_items){
                //save add
                var p=params;
                var pt=postdata;
                
                
            },
            beforeSubmit : function(postdata, formid) { 
                var p = postdata;
                var id=formid;
                var success=true;
                var message="continue";
                return[success,message]; 
            }, 
            afterSubmit : function(response, postdata) 
            { 
                var r=response; 
                var p=postdata;
                var responseText=jQuery.jgrid.parse(response.responseText);
                var success=true;
                var message="continue";
                return [success,message] 
            },
            afterComplete : function (response, postdata, formid) {        
                var responseText=jQuery.jgrid.parse(response.responseText);
                var r=response;
                var p=postdata;
                var f=formid;
                CalItem();
                
            } 

            },//del
     {},
     {}
   	);




    $("#<?php echo $this->campoSeguro('sede')?>").change(function(){
    	if($("#<?php echo $this->campoSeguro('sede')?>").val()!=''){
        	consultarDependencia();
		}else{
			$("#<?php echo $this->campoSeguro('selec_dependencia')?>").attr('disabled','');
			}

	      });






    $("#<?php echo $this->campoSeguro('selec_dependencia')?>").change(function(){ datosDependencia(); });
    
	      
    
    

    $("#<?php echo $this->campoSeguro('selec_proveedor')?>").select2({
      	 placeholder: "Search for a repository",
      	 minimumInputLength: 3,

          });

    
    $("#<?php echo $this->campoSeguro('rubro')?>").select2({
   	 placeholder: "Search for a repository",
   	 minimumInputLength: 3,

       });




    $("#<?php echo $this->campoSeguro('vigencia_contratista')?>").change(function() {
    	
		if($("#<?php echo $this->campoSeguro('vigencia_contratista')?>").val()!=''){

			contratistasC();	

		}else{}


 		});
    
    $("#<?php echo $this->campoSeguro('vigencia_disponibilidad')?>").change(function() {
	
				if($("#<?php echo $this->campoSeguro('vigencia_disponibilidad')?>").val()!=''){

					disponibilidades();	
		
				}else{}

	
         });





$("#<?php echo $this->campoSeguro('diponibilidad')?>").change(function() {

		if($("#<?php echo $this->campoSeguro('diponibilidad')?>").val()!=''){
		
			infodisponibilidades();	
		
		}else{}
		
		
		});




			$("#<?php echo $this->campoSeguro('vigencia_registro')?>").change(function() {
				
				if($("#<?php echo $this->campoSeguro('vigencia_registro')?>").val()!=''){
			
					registrosP();	
			
				}else{}
			
			
			});
			
			
			
			$("#<?php echo $this->campoSeguro('registro')?>").change(function() {
			
				if($("#<?php echo $this->campoSeguro('registro')?>").val()!=''){
			
					inforegistrosP();	
			
				}else{}
			
			
			});
			
					    
    


    
    $("#<?php echo $this->campoSeguro('iva')?>").select2();

    $("#<?php echo $this->campoSeguro('selec_proveedor')?>").change(function() {datosInfo(); });


    


    $("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").change(function(){

    	if($("#<?php echo $this->campoSeguro('asignacionOrdenador')?>").val()!=''){
    		datosOrdenador();
		}else{
			$("#<?php echo $this->campoSeguro('nombreOrdenador')?>").val('');
			}



	      });

    $("#<?php echo $this->campoSeguro('cargoJefeSeccion')?>").change(function(){


		if($("#<?php echo $this->campoSeguro('cargoJefeSeccion')?>").val()!=''){
			datosCargo();
		}else{
			$("#<?php echo $this->campoSeguro('nombreJefeSeccion')?>").val('');
			}


	      });
  
       	$("#<?php echo $this->campoSeguro('iva')?>").change(function(){ 
		
		switch($("#<?php echo $this->campoSeguro('iva')?>").val())
		
		{
	
			case '0':
		
				$('#<?php echo $this->campoSeguro('total_iva')?>').val(0);
				
				var total =$('#<?php echo $this->campoSeguro('total_preliminar')?>').val();
				var iva =$('#<?php echo $this->campoSeguro('total_iva')?>').val();
				var numero = Number(total) + Number(iva) ;
				
				$('#<?php echo $this->campoSeguro('total')?>').val(numero);

				if ($('#<?php echo $this->campoSeguro('total_preliminar')?>').val()!=0){

					valorLetras();

					}else{


						$("#<?php echo $this->campoSeguro('valorLetras_registro')?>").val('');


						}
				
		
		
			break;
		
			case '5':
		
				$('#<?php echo $this->campoSeguro('total_iva')?>').val($('#<?php echo $this->campoSeguro('total_preliminar')?>').val() * 0.10);
		
				var total =$('#<?php echo $this->campoSeguro('total_preliminar')?>').val();
				var iva =$('#<?php echo $this->campoSeguro('total_iva')?>').val();
				var numero = Number(total) + Number(iva) ;
				
				$('#<?php echo $this->campoSeguro('total')?>').val(numero);

				if ($('#<?php echo $this->campoSeguro('total_preliminar')?>').val()!=0){

					valorLetras();

					}else{


						$("#<?php echo $this->campoSeguro('valorLetras_registro')?>").val('');


						}
		
		
			break;	

			case '6':
				
				$('#<?php echo $this->campoSeguro('total_iva')?>').val($('#<?php echo $this->campoSeguro('total_preliminar')?>').val() * 0.16);
		
				var total =$('#<?php echo $this->campoSeguro('total_preliminar')?>').val();
				var iva =$('#<?php echo $this->campoSeguro('total_iva')?>').val();
				var numero = Number(total) + Number(iva) ;
				
				$('#<?php echo $this->campoSeguro('total')?>').val(numero);

				if ($('#<?php echo $this->campoSeguro('total_preliminar')?>').val()!=0){

					valorLetras();

					}else{


						$("#<?php echo $this->campoSeguro('valorLetras_registro')?>").val('');


						}
		
		
			break;	

		
		}
		
		 });

    
});

function disponibilidades(elem, request, response){
	  $.ajax({
	    url: "<?php echo $urlFinal10?>",
	    dataType: "json",
	    data: { vigencia:$("#<?php echo $this->campoSeguro('vigencia_disponibilidad')?>").val()},
	    success: function(data){ 
	        if(data[0]!=" "){

	            $("#<?php echo $this->campoSeguro('diponibilidad')?>").html('');
	            $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('diponibilidad')?>");
	            $.each(data , function(indice,valor){

	            	$("<option value='"+data[ indice ].IDENTIFICADOR+"'>"+data[ indice ].NUMERO+"</option>").appendTo("#<?php echo $this->campoSeguro('diponibilidad')?>");
	            	
	            });
	            $("#<?php echo $this->campoSeguro('diponibilidad')?>").removeAttr('disabled');
	            $('#<?php echo $this->campoSeguro('diponibilidad')?>').attr("class", " validate[required]");
	            $("#<?php echo $this->campoSeguro('diponibilidad')?>").select2({
	          		 placeholder: "Search for a repository",
	           		 minimumInputLength: 1	,
	               });
	            
	            
		        }

	        


		     }
		                    
	   });
	};


	function infodisponibilidades(elem, request, response){
		  $.ajax({
		    url: "<?php echo $urlFinal12?>",
		    dataType: "json",
		    data: { vigencia:$("#<?php echo $this->campoSeguro('vigencia_disponibilidad')?>").val(),
			    disponibilidad:$("#<?php echo $this->campoSeguro('diponibilidad')?>").val()},
		    success: function(data){ 
			    
		        if(data[0]!="null"){
		        	$("#<?php echo $this->campoSeguro('fecha_diponibilidad')?>").val(data[0]);
			    	$("#<?php echo $this->campoSeguro('valor_disponibilidad')?>").val(data[1]);
				
			    	valorLetrasDis();
	
		            
			        }

		        


			     }
			                    
		   });
		};


		function registrosP(elem, request, response){
			
			  $.ajax({
			    url: "<?php echo $urlFinal13?>",
			    dataType: "json",
			    data: { vigencia:$("#<?php echo $this->campoSeguro('vigencia_registro')?>").val()},
			    success: function(data){ 
			        if(data[0]!=" "){

			            $("#<?php echo $this->campoSeguro('registro')?>").html('');
			            $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('registro')?>");
			            $.each(data , function(indice,valor){

			            	$("<option value='"+data[ indice ].IDENTIFICADOR+"'>"+data[ indice ].NUMERO+"</option>").appendTo("#<?php echo $this->campoSeguro('registro')?>");
			            	
			            });
			            
			            $("#<?php echo $this->campoSeguro('registro')?>").removeAttr('disabled');
			            $('#<?php echo $this->campoSeguro('registro')?>').attr("class", " validate[required]");
			            $("#<?php echo $this->campoSeguro('registro')?>").select2({
			          		 placeholder: "Search for a repository",
			           		 minimumInputLength: 1	,
			               });
			          
			            
				        }

			        


				     }
				                    
			   });
			};


			function inforegistrosP(elem, request, response){
				  $.ajax({
				    url: "<?php echo $urlFinal14?>",
				    dataType: "json",
				    data: { vigencia:$("#<?php echo $this->campoSeguro('vigencia_registro')?>").val(),
					    disponibilidad:$("#<?php echo $this->campoSeguro('registro')?>").val()},
				    success: function(data){ 
					    
				        if(data[0]!="null"){
				        	$("#<?php echo $this->campoSeguro('fecha_registro')?>").val(data[0]);
					    	$("#<?php echo $this->campoSeguro('valor_registro')?>").val(data[1]);
						
					    	valorLetrasReg();
			
				            
					        }

				        


					     }
					                    
				   });
				};

		
				function contratistasC(elem, request, response){
					
					  $.ajax({
					    url: "<?php echo $urlFinal15?>",
					    dataType: "json",
					    data: { vigencia:$("#<?php echo $this->campoSeguro('vigencia_contratista')?>").val()},
					    success: function(data){ 
					        if(data[0]!=" "){

					            $("#<?php echo $this->campoSeguro('nombreContratista')?>").html('');
					            $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('nombreContratista')?>");
					            $.each(data , function(indice,valor){

					            	$("<option value='"+data[ indice ].IDENTIFICADOR+"'>"+data[ indice ].CONTRATISTA+"</option>").appendTo("#<?php echo $this->campoSeguro('nombreContratista')?>");
					            	
					            });
					            
					            $("#<?php echo $this->campoSeguro('nombreContratista')?>").removeAttr('disabled');
					            $('#<?php echo $this->campoSeguro('nombreContratista')?>').attr("class", "  validate[]");
					            
					            $('#<?php echo $this->campoSeguro('nombreContratista')?>').attr("style", "width: 70 ; '");
					            					            
					            $("#<?php echo $this->campoSeguro('nombreContratista')?>").select2({
					          		 placeholder: "Search for a repository",
					           		 minimumInputLength: 3	,
					               });
					          
					            
						        }
					        

					        


						     }
						                    
					   });
					};










</script>

