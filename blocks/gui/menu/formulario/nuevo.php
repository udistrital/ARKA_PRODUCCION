<?php
// var_dump($this->miConfigurador);
$esteBloque = $this->miConfigurador->getVariableConfiguracion("esteBloque");

$rutaBloque = $this->miConfigurador->getVariableConfiguracion("host");
$rutaBloque .= $this->miConfigurador->getVariableConfiguracion("site") . "/blocks/";
$rutaBloque .= $esteBloque ['grupo'] . "/" . $esteBloque ['nombre'];

$directorio = $this->miConfigurador->getVariableConfiguracion("host");
$directorio .= $this->miConfigurador->getVariableConfiguracion("site") . "/index.php?";
$directorio .= $this->miConfigurador->getVariableConfiguracion("enlace");
$miSesion = Sesion::singleton();

// Registro Orden Compra
$enlaceRegistroOrdenCompra ['enlace'] = "pagina=registrarOrdenCompra";
$enlaceRegistroOrdenCompra ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceRegistroOrdenCompra ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceRegistroOrdenCompra ['enlace'], $directorio);
$enlaceRegistroOrdenCompra ['nombre'] = "Registrar Orden de Compra";

// consultar y modificar Orden Compra
$enlaceConsultaOrdenCompra ['enlace'] = "pagina=consultaOrdenCompra";
$enlaceRegistroOrdenCompra ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceConsultaOrdenCompra ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceConsultaOrdenCompra ['enlace'], $directorio);
$enlaceConsultaOrdenCompra ['nombre'] = "Consultar y Modificar Orden de Compra";

// Registro Orden Servicios
$enlaceRegistroOrdenServicios ['enlace'] = "pagina=registrarOrdenServicios";
$enlaceRegistroOrdenServicios ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceRegistroOrdenServicios ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceRegistroOrdenServicios ['enlace'], $directorio);
$enlaceRegistroOrdenServicios ['nombre'] = "Registrar Orden de Servicios";


// Consultar y Modificar Orden Servicios
$enlaceConsultaOrdenServicios ['enlace'] = "pagina=consultaOrdenServicios";
$enlaceConsultaOrdenServicios ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceConsultaOrdenServicios ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceConsultaOrdenServicios ['enlace'], $directorio);
$enlaceConsultaOrdenServicios ['nombre'] = "Consultar y Modificar Orden de Servicios";


// Registro de Contrato

$enlacegestionContrato['enlace'] = "pagina=gestionContrato";
$enlacegestionContrato['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlacegestionContrato['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlacegestionContrato ['enlace'], $directorio);
$enlacegestionContrato['nombre'] = "Contratos Vicerrectoría";

// Gestionar Acta de Recibido

$enlacegestionActa['enlace'] = "pagina=registrarActa";
$enlacegestionActa['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlacegestionActa['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlacegestionActa ['enlace'], $directorio);
$enlacegestionActa['nombre'] = "Registrar Acta de Recibido";

// Gestionar Acta de Recibido

$enlaceconsultaActa['enlace'] = "pagina=consultarActa";
$enlaceconsultaActa['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceconsultaActa['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceconsultaActa['enlace'], $directorio);
$enlaceconsultaActa['nombre'] = "Consultar y Modificar Acta de Recibido";


// Registro Entradas
$enlaceRegistroEntradas ['enlace'] = "pagina=registrarEntradas";
$enlaceRegistroEntradas ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceRegistroEntradas ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceRegistroEntradas ['enlace'], $directorio);
$enlaceRegistroEntradas ['nombre'] = "Registrar Entrada";


// Consultar y Modificar Estado Entradas
$enlaceConsultaEntradas ['enlace'] = "pagina=consultaEntradas";
$enlaceConsultaEntradas ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceConsultaEntradas ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceConsultaEntradas ['enlace'], $directorio);
$enlaceConsultaEntradas ['nombre'] = "Consultar y Modificar Estado Entrada";



// Modificar Entradas

$enlaceModificarEntradas ['enlace'] = "pagina=modificarEntradas";
$enlaceModificarEntradas ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceModificarEntradas ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceModificarEntradas ['enlace'], $directorio);
$enlaceModificarEntradas ['nombre'] = "Modificar Entradas";

// Registro Salidas
$enlaceRegistroSalidas ['enlace'] = "pagina=registrarSalidas";
$enlaceRegistroSalidas ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceRegistroSalidas ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceRegistroSalidas ['enlace'], $directorio);
$enlaceRegistroSalidas ['nombre'] = "Registrar Salida";



// Consultar y Modificar Salidas

$enlaceModificarSalidas ['enlace'] = "pagina=modificarSalidas";
$enlaceModificarSalidas ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceModificarSalidas ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceModificarSalidas ['enlace'], $directorio);
$enlaceModificarSalidas ['nombre'] = "Consultar y Modificar Salidas";



// Gestionar Catalogo

$enlaceGestionarCatalogo ['enlace'] = "pagina=catalogo";
$enlaceGestionarCatalogo ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceGestionarCatalogo ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceGestionarCatalogo ['enlace'], $directorio);
$enlaceGestionarCatalogo ['nombre'] = "Gestionar Catalogo";

// Radicar ASignar

$enlaceRadicarAsignar ['enlace'] = "pagina=radicarAsignar";
$enlaceRadicarAsignar ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceRadicarAsignar ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceRadicarAsignar ['enlace'], $directorio);
$enlaceRadicarAsignar ['nombre'] = "Asignar Entrada";


//Asignar inventarios a Contratistas

$enlaceasignarInventarioC ['enlace'] = "pagina=asignarInventarioC";
$enlaceasignarInventarioC ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceasignarInventarioC ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceasignarInventarioC ['enlace'], $directorio);
$enlaceasignarInventarioC ['nombre'] = "Asignación de  Inventarios a Contratistas";

// Reportico
$enlaceReportico ['enlace'] = "pagina=reportico";
$enlaceReportico ['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceReportico ['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceReportico ['enlace'], $directorio);
$enlaceReportico ['nombre'] = "Reportes Inventarios";

// Fin de la sesión

$enlaceFinSesion['enlace'] = "pagina=index";
$enlaceFinSesion['enlace'] .= "&usuario=" . $miSesion->getSesionUsuarioId();

$enlaceFinSesion['urlCodificada'] = $this->miConfigurador->fabricaConexiones->crypto->codificar_url($enlaceFinSesion['enlace'], $directorio);
$enlaceFinSesion['nombre'] = "Cerrar Sesión";

//-----------------------Inicio del Menú --------------------//
?><div id="dl-menu" class="dl-menuwrapper">
    <button class="dl-trigger">ARKA</button>
    <ul class="dl-menu ">
        <li>
            <a href="#">Compras</a>
            <ul class="dl-submenu">
                <li><a href="<?php echo $enlaceRegistroOrdenCompra['urlCodificada'] ?>"><?php echo $enlaceRegistroOrdenCompra['nombre'] ?></a></li>
                <li><a href="<?php echo $enlaceConsultaOrdenCompra['urlCodificada'] ?>"><?php echo $enlaceConsultaOrdenCompra['nombre'] ?></a></li>
                <li><a href="<?php echo $enlaceRegistroOrdenServicios['urlCodificada'] ?>"><?php echo $enlaceRegistroOrdenServicios['nombre'] ?></a></li>
                <li><a href="<?php echo $enlaceConsultaOrdenServicios['urlCodificada'] ?>"><?php echo $enlaceConsultaOrdenServicios['nombre'] ?></a></li>
                <li><a href="<?php echo $enlacegestionContrato['urlCodificada'] ?>"><?php echo $enlacegestionContrato['nombre'] ?></a></li>
            </ul>
        </li>
        <li>
            <a href="#">Acta Recibido</a>
            <ul class="dl-submenu">
                <li><a href="<?php echo $enlacegestionActa['urlCodificada'] ?>"><?php echo $enlacegestionActa['nombre'] ?></a></li>
                <li><a href="<?php echo$enlaceconsultaActa['urlCodificada'] ?>"><?php echo $enlaceconsultaActa['nombre'] ?></a></li>
            </ul>
        </li>

        <li>
            <a href="#">Entradas</a>
            <ul class="dl-submenu">
                <li><a href="<?php echo $enlaceRegistroEntradas['urlCodificada'] ?>"><?php echo $enlaceRegistroEntradas['nombre'] ?></a></li>
                <li><a href="<?php echo $enlaceConsultaEntradas['urlCodificada'] ?>"><?php echo $enlaceConsultaEntradas['nombre'] ?></a></li>
                <li><a href="<?php echo $enlaceModificarEntradas['urlCodificada'] ?>"><?php echo $enlaceModificarEntradas['nombre'] ?></a></li>   
            </ul>
        </li>
        
        <li>
            <a href="#">Salidas</a>
            <ul class="dl-submenu">
                <li><a href="<?php echo $enlaceRegistroSalidas['urlCodificada'] ?>"><?php echo $enlaceRegistroSalidas['nombre'] ?></a></li>
				<li><a href="<?php echo $enlaceModificarSalidas['urlCodificada'] ?>"><?php echo $enlaceModificarSalidas['nombre'] ?></a></li>   
            </ul>
        </li>

        <li>
            <a href="#">Catálogo</a>
            <ul class="dl-submenu">
                <li><a href="<?php echo $enlaceGestionarCatalogo['urlCodificada'] ?>"><?php echo $enlaceGestionarCatalogo['nombre'] ?></a></li>
            </ul>
        </li>
       <li>
            <a href="#">Radiación Documentos</a>
            <ul class="dl-submenu">
                <li><a href="<?php echo $enlaceRadicarAsignar['urlCodificada'] ?>"><?php echo $enlaceRadicarAsignar['nombre'] ?></a></li>
                <!--li><a href="">Entrada y Salida de Elementos</a></li>
                <li><a href="">Movimientos</a></li>
                <li><a href="">Consultar Radicación</a></li-->
            </ul>
        </li>

        <!--li>
            <a href="#">Asignación Inventarios a Contratistas</a>
            <ul class="dl-submenu">
                <li><a href="<?php echo $enlaceasignarInventarioC['urlCodificada'] ?>"><?php echo $enlaceasignarInventarioC['nombre'] ?></a></li>
            </ul>
        </li-->

 <li>
            <a href="<?php echo$enlaceReportico['urlCodificada'] ?>"><?php echo $enlaceReportico['nombre'] ?></a>
        </li>

        <li>
            <a href="<?php echo$enlaceFinSesion['urlCodificada'] ?>">Cerrar Sesión</a>
        </li>


        <!--li>
           <a href="#">Jewelry &amp; Watches</a>
            <ul class="dl-submenu">
                <li><a href="#">Fine Jewelry</a></li>
                <li><a href="#">Fashion Jewelry</a></li>
                <li><a href="#">Watches</a></li>
                <li>
                    <a href="#">Wedding Jewelry</a>
                    <ul class="dl-submenu">
                        <li><a href="#">Engagement Rings</a></li>
                        <li><a href="#">Bridal Sets</a></li>
                        <li><a href="#">Women's Wedding Bands</a></li>
                        <li><a href="#">Men's Wedding Bands</a></li>
                    </ul>
                </li>
            </ul>
        </li-->
    </ul>
</div><!-- /dl-menuwrapper -->
