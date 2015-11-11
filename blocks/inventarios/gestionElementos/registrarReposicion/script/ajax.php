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
$cadenaACodificar4 = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar4 .= "&procesarAjax=true";
$cadenaACodificar4 .= "&action=index.php";
$cadenaACodificar4 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar4 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar4 .= $cadenaACodificar4 . "&funcion=consultarUbicacion";
$cadenaACodificar4 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena4 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar4, $enlace);

// URL definitiva
$urlFinal4 = $url . $cadena4;

// Variables
$cadenaACodificar = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar .= "&procesarAjax=true";
$cadenaACodificar .= "&action=index.php";
$cadenaACodificar .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar .= $cadenaACodificar . "&funcion=tablaItems";
$cadenaACodificar .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar, $enlace);

// URL definitiva
$urlFinal = $url . $cadena;



// Variables
$cadenaACodificar6 = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar6 .= "&procesarAjax=true";
$cadenaACodificar6 .= "&action=index.php";
$cadenaACodificar6 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar6 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar6 .= $cadenaACodificar . "&funcion=SeleccionOrdenador";
$cadenaACodificar6 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace6 = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena6 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar6, $enlace6);

// URL definitiva
$urlFinal6 = $url . $cadena6;

// Variables
$cadenaACodificar16 = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar16 .= "&procesarAjax=true";
$cadenaACodificar16 .= "&action=index.php";
$cadenaACodificar16 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar16 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar16 .= $cadenaACodificar16 . "&funcion=consultarDependencia";
$cadenaACodificar16 .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena16 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar16, $enlace);

// URL definitiva
$urlFinal16 = $url . $cadena16;


//Cadena codificada para listar Catalogos
$cadenaACodificar1 = $cadenaACodificar . "&funcion=proveedor";
$cadena1 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar1, $enlace);

$urlFinal = $url . $cadena1;

// Variables
$cadenaACodificarPlaca = "pagina=" . $this->miConfigurador->getVariableConfiguracion ( "pagina" );
$cadenaACodificarPlaca .= "&procesarAjax=true";
$cadenaACodificarPlaca .= "&action=index.php";
$cadenaACodificarPlaca .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificarPlaca .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificarPlaca .= "&funcion=consultaPlaca";
$cadenaACodificarPlaca .= "&tiempo=" . $_REQUEST ['tiempo'];

// Codificar las variables
$enlace = $this->miConfigurador->getVariableConfiguracion ( "enlace" );
$cadena = $this->miConfigurador->fabricaConexiones->crypto->codificar_url ( $cadenaACodificarPlaca, $enlace );

// URL definitiva
$urlFinalPlaca = $url . $cadena;



?>
<script type='text/javascript'>
    $(document).ready(function () {
        $("#<?php echo $this->campoSeguro('nombre_proveedor') ?>").devbridgeAutocomplete({
            minLength: 2,
            serviceUrl: '<?php echo $urlFinal; ?>',
            onSelect: function (suggestion) {
    	        $("#<?php echo $this->campoSeguro('proveedor') ?>").val(suggestion.data);
    	    }
        });
    });
</script>

<script type='text/javascript'>



    function consultarDependencia(elem, request, response) {
        $.ajax({
            url: "<?php echo $urlFinal16 ?>",
            dataType: "json",
            data: {valor: $("#<?php echo $this->campoSeguro('sede') ?>").val()},
            success: function (data) {
                if (data[0] != " ") {

                    $("#<?php echo $this->campoSeguro('dependencia') ?>").html('');
                    $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('dependencia') ?>");
                    $.each(data, function (indice, valor) {

                        $("<option value='" + data[ indice ].ESF_CODIGO_DEP + "'>" + data[ indice ].ESF_DEP_ENCARGADA + "</option>").appendTo("#<?php echo $this->campoSeguro('dependencia') ?>");

                    });
                    $("#<?php echo $this->campoSeguro('dependencia') ?>").removeAttr('disabled');
                    $('#<?php echo $this->campoSeguro('dependencia') ?>').width(300);
                    $("#<?php echo $this->campoSeguro('dependencia') ?>").select2();
                }
            }

        });
    }
    ;

    function consultarDependenciaSalida(elem, request, response) {
        $.ajax({
            url: "<?php echo $urlFinal16 ?>",
            dataType: "json",
            data: {valor: $("#<?php echo $this->campoSeguro('sede_salida') ?>").val()},
            success: function (data) {
                if (data[0] != " ") {

                    $("#<?php echo $this->campoSeguro('dependencia_salida') ?>").html('');
                    $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('dependencia_salida') ?>");
                    $.each(data, function (indice, valor) {

                        $("<option value='" + data[ indice ].ESF_CODIGO_DEP + "'>" + data[ indice ].ESF_DEP_ENCARGADA + "</option>").appendTo("#<?php echo $this->campoSeguro('dependencia_salida') ?>");

                    });

                    $("#<?php echo $this->campoSeguro('dependencia_salida') ?>").removeAttr('disabled');
                    $('#<?php echo $this->campoSeguro('dependencia_salida') ?>').width(300);
                    $("#<?php echo $this->campoSeguro('dependencia_salida') ?>").select2();
                }
            }

        });
    }
    ;



    function consultarEspacio(elem, request, response) {
        $.ajax({
            url: "<?php echo $urlFinal4 ?>",
            dataType: "json",
            data: {valorD: $("#<?php echo $this->campoSeguro('dependencia_salida') ?>").val(),
            	valorS: $("#<?php echo $this->campoSeguro('sede_salida') ?>").val()},
            success: function (data) {

                if (data[0] != " ") {

                    $("#<?php echo $this->campoSeguro('ubicacion_salida') ?>").html('');
                    $("<option value=''>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('ubicacion_salida') ?>");
                    $.each(data, function (indice, valor) {
                        $("<option value='" + data[ indice ].ESF_ID_ESPACIO + "'>" + data[ indice ].ESF_NOMBRE_ESPACIO + "</option>").appendTo("#<?php echo $this->campoSeguro('ubicacion_salida') ?>");

                    });

                    $("#<?php echo $this->campoSeguro('ubicacion_salida') ?>").removeAttr('disabled');
                    $('#<?php echo $this->campoSeguro('ubicacion_salida') ?>').width(210);
                    $("#<?php echo $this->campoSeguro('ubicacion_salida') ?>").select2();
                }
            }

        });
    }
    ;


    $(function () {


    	 $("#<?php echo $this->campoSeguro('selec_placa') ?>").autocomplete({
    	    	minChars: 3,
    	    	serviceUrl: '<?php echo $urlFinalPlaca; ?>',
    	    	onSelect: function (suggestion) {
    	        	
    	    	        $("#<?php echo $this->campoSeguro('placa') ?>").val(suggestion.data);

    	       	        
    	    	    }
    	                
    	    });

        
        $("#<?php echo $this->campoSeguro('sede') ?>").change(function () {
            if ($("#<?php echo $this->campoSeguro('sede') ?>").val() != '') {
                consultarDependencia();
            } else {
                $("#<?php echo $this->campoSeguro('dependencia') ?>").attr('disabled', '');
            }

        });

        $("#<?php echo $this->campoSeguro('dependencia_salida') ?>").change(function () {
            if ($("#<?php echo $this->campoSeguro('dependencia_salida') ?>").val() != '') {
                consultarEspacio();
            } else {
                $("#<?php echo $this->campoSeguro('ubicacion_salida') ?>").attr('disabled', '');
            }

        });

        $("#<?php echo $this->campoSeguro('sede_salida') ?>").change(function () {
            if ($("#<?php echo $this->campoSeguro('sede_salida') ?>").val() != '') {
             $("#<?php echo $this->campoSeguro('ubicacion_salida') ?>").html('');
                $("<option value='0'>Seleccione  ....</option>").appendTo("#<?php echo $this->campoSeguro('ubicacion_salida') ?>");
                $("#<?php echo $this->campoSeguro('ubicacion_salida') ?>").val('');
                $("#<?php echo $this->campoSeguro('ubicacion_salida') ?>").prop('selectedIndex', 0);

                consultarDependenciaSalida();
            } else {
                $("#<?php echo $this->campoSeguro('dependencia_salida') ?>").attr('disabled', '');
            }

        });


        $("#<?php echo $this->campoSeguro('asignacionOrdenador') ?>").change(function () {

            if ($("#<?php echo $this->campoSeguro('asignacionOrdenador') ?>").val() != '') {
                datosOrdenador();
            } else {
                $("#<?php echo $this->campoSeguro('nombreOrdenador') ?>").val('');
            }
        });

    });

    function datosOrdenador(elem, request, response) {
        $.ajax({
            url: "<?php echo $urlFinal6 ?>",
            dataType: "json",
            data: {ordenador: $("#<?php echo $this->campoSeguro('asignacionOrdenador') ?>").val()},
            success: function (data) {

                if (data[0] != 'null') {

                    $("#<?php echo $this->campoSeguro('nombreOrdenador') ?>").val(data[0]);
                    $("#<?php echo $this->campoSeguro('id_ordenador') ?>").val(data[1]);
                    $("#<?php echo $this->campoSeguro('tipo_ordenador') ?>").val(data[2]);
                    $("#<?php echo $this->campoSeguro('identificacion_ordenador') ?>").val(data[1]);
                } else {





                }

            }

        });
    }
    ;
</script>

