<?php
/**
 * Los datos del bloque se encuentran en el arreglo $esteBloque.
 */
// URL base

$url = $this->miConfigurador->getVariableConfiguracion("host");
$url.= $this->miConfigurador->getVariableConfiguracion("site");
$url.= "/index.php?";
$urlDirectorio = $url;
$urlDirectorio = $urlDirectorio . "/plugin/scripts/javascript/dataTable/Spanish.json";


//Atributos para cargar elementos
$cadenaACodificar2 = "pagina=" . $this->miConfigurador->getVariableConfiguracion("pagina");
$cadenaACodificar2 .= "&procesarAjax=true";
$cadenaACodificar2 .= "&action=index.php";
$cadenaACodificar2 .= "&bloqueNombre=" . $esteBloque ["nombre"];
$cadenaACodificar2 .= "&bloqueGrupo=" . $esteBloque ["grupo"];
$cadenaACodificar2 .= $cadenaACodificar2 . "&funcion=Consulta";
$cadenaACodificar2 .= "&tiempo=" . $_REQUEST ['tiempo'];

if (isset($_REQUEST['grupo_contable']) && $_REQUEST['grupo_contable'] != '') {
    $grupo = $_REQUEST['grupo_contable'];
} else {
    $grupo = '';
}

if (isset($_REQUEST['fechaCorte']) && $_REQUEST['fechaCorte'] != '') {
    $fechaCorte = $_REQUEST['fechaCorte'];
} else {
    $fechaCorte = date('Y-m-d');
}

if (isset($_REQUEST['cuenta_salida']) && $_REQUEST['cuenta_salida'] != '') {
    $cuenta_contable = $_REQUEST['cuenta_salida'];
} else {
    $cuenta_contable = '';
}

$arreglo = array(
    'grupo' => $grupo,
    'fecha_corte' => $fechaCorte,
    'cuenta_salida' => $cuenta_contable,
);

$arreglo = serialize($arreglo);

$cadenaACodificar2.= "&arreglo=" . $arreglo;
$cadenaACodificar2.= "&usuario=" . $_REQUEST['usuario'];

// Codificar las variables
$enlace2 = $this->miConfigurador->getVariableConfiguracion("enlace");
$cadena2 = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($cadenaACodificar2, $enlace2);

// URL definitiva
$urlFinal2 = $url . $cadena2;
?>
<script type='text/javascript'>
    $(function () {
        $('#tablaTitulos').ready(function () {

            $('#tablaTitulos').dataTable({
                dom: 'Blfrtip',
                buttons: [
                    'csv',
                    'excel',
                ],
                language: {
                    url: "<?php echo $urlDirectorio ?>"
                },
                processing: true,
                searching: true,
                info: true,
                ajax: {
                    url: "<?php echo $urlFinal2 ?>",
                    dataSrc: "data",
                },
                columns: [
                    {data: "cuenta"},
                    {data: "grupo"},
                    {data: "total_elementos"},
                    {data: "total_valor_historico"},
                    {data: "total_ajuste_inflacion"},
                    {data: "total_valor_ajustado"},
                    {data: "total_depreciacion"},
                    {data: "total_ajuste_inflacionario"},
                    {data: "total_depreciacion_circular56"},
                    {data: "total_libros"},
                ]
            });
        });
    });
</script>


