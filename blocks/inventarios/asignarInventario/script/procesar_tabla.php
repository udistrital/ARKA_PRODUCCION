<?php

/**
 *
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
 */

// Variables
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= "&funcion=ConsultarInventario";
$cadenaACodificar .= "&usuario=" . $_REQUEST ['usuario'];
$cadenaACodificar .= "&funcionario=" . $_REQUEST ['usuario'];
$cadenaACodificar .= "&tiempo=" . $_REQUEST ['tiempo'];

if (isset ( $_REQUEST ['accesoCondor'] ) && $_REQUEST ['accesoCondor'] == 'true') {
	
	$_REQUEST ['funcionario'] = $_REQUEST ['usuario'];
	$cadenaACodificar .= "&accesoCondor='true'";
}

if (isset ( $_REQUEST ['funcionario'] ) && $_REQUEST ['funcionario'] != '') {
	$funcionario = $_REQUEST ['funcionario'];
} else {
	$funcionario = '';
}



// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificar, $enlace );

// URL definitiva
$urlFinal = $url . $cadena;


$urlDirectorio="http://10.20.0.38/arka/plugin/scripts/javascript/dataTable/Spanish.json";

?>
<script type='text/javascript'>




$(function() {
         	$('#tablaTitulos').ready(function() {

             $('#tablaTitulos').dataTable( {
//              	 serverSide: true,
				language: {
                url: "<?php echo $urlDirectorio?>"
            			},
             	processing: true,
		"aLengthMenu": [[10,25, 50,100,300,500,1000,-1], [10,25, 50,100,300,500,1000,'Todos']],
//                   ordering: true,
                  searching: true,
//                deferRender: true,
      //             sScrollY: 500	,
        //          bScrollCollapse: true,
                  info:true,
//                   lengthChange:true,
   		    "pagingType": "full_numbers",
//                   stateSave: true,
         //          renderer: "bootstrap",
         //          retrieve: true,
                  ajax:{
                      url:"<?php echo $urlFinal?>",
                      dataSrc:"data"                                                                  
                  },
                  columns: [
				  { data :"placa" },
				  { data :"descripcion" },
	              { data :"marca" },
                  { data :"serie" },
                  { data :"sede" },
                  { data :"dependencia" },
                  { data :"ubicacion" },
                  { data :"seleccion" },
                            ]
             });
                  
         		});

});




</script>
