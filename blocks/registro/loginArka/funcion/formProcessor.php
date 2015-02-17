<?php

namespace registro\loginArka;


include_once('Redireccionador.php');
//  var_dump($_REQUEST);exit;
class FormProcessor {
    
    var $miConfigurador;
    var $lenguaje;
    var $miFormulario;
    var $miSql;
    var $conexion;
    
    function __construct($lenguaje, $sql) {
        
        $this->miConfigurador = \Configurador::singleton ();
        $this->miConfigurador->fabricaConexiones->setRecursoDB ( 'principal' );
        $this->lenguaje = $lenguaje;
        $this->miSql = $sql;
        $this->miSesion= \Sesion::singleton();
    }
    
    function procesarFormulario() {    

 
        /**
         * @todo lógica de procesamiento
         */
    	$conexion = "estructura";
    	$esteRecursoDB = $this->miConfigurador->fabricaConexiones->getRecursoDB ( $conexion );
	$arregloLogin=array();

    	 
    	if (! $esteRecursoDB) {
    	 
    		// Este se considera un error fatal
    		exit ();
    	}
    	
    	
    		/**
    		 *
    		 * @todo En entornos de producción la clave debe codificarse utilizando un objeto de la clase Codificador
    		*/
    		$variable ['usuario'] = $_REQUEST ["usuario"] ;
    		$variable ['clave'] = $this->miConfigurador->fabricaConexiones->crypto->codificarClave ( $_REQUEST ["clave"] );
    		
    		// Verificar que el tiempo registrado en los controles no sea superior al tiempo actual + el tiempo de expiración
    		if ($_REQUEST ['tiempo'] <= time () + $this->miConfigurador->getVariableConfiguracion ( 'expiracion' )) {
    				
    			// Verificar que el usuario esté registrado en el sistema
    				
    			$cadena_sql = $this->miSql->getCadenaSql ( "buscarUsuario", $variable );
    			$registro = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "busqueda" );
    			
    			if($registro){
    				
    				if ($registro [0] ['clave'] == $variable ["clave"]) {
    						
    					// 1. Crear una sesión de trabajo
    					$estaSesion = $this->miSesion->crearSesion ( $registro [0] ["id_usuario"] );
    					    						
    					$arregloLogin = array (
    							'autenticacionExitosa',
    							$registro [0] ["id_usuario"],
    							$_SERVER ['REMOTE_ADDR'],
    							$_SERVER ['HTTP_USER_AGENT']
    					);
    						
    					$argumento = json_encode ( $arregloLogin );
    					$arreglo = array($registro [0] ["id_usuario"],$argumento);
    						
    					$cadena_sql = $this->miSql->getCadenaSql ( "registrarEvento", $arreglo );
    					$registroAcceso = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "acceso" );
    					
    					if ($estaSesion) {
    	
    						switch ($registro [0] ["tipo"]) {
    								
    							case '1' :
    								//Al final se ejecuta la redirección la cual pasará el control a otra página
    								Redireccionador::redireccionar('indexAlmacen',$registro [0]);
    								break;
    									
    							case '2' :
    								//Al final se ejecuta la redirección la cual pasará el control a otra página
    								Redireccionador::redireccionar('indexInventarios',$registro [0]);
    								break;
    	
    							case '3' :
    								//Al final se ejecuta la redirección la cual pasará el control a otra página
    								Redireccionador::redireccionar('indexContabilidad',$registro [0]);
    								break;
    						}
    					}
    					// Redirigir a la página principal del usuario, en el arreglo $registro se encuentran los datos de la sesion:
    					// $this->funcion->redireccionar("indexUsuario", $registro[0]);
    					return true;
    				}
    			} else {
    					
    				// Registrar el error por clave no válida
    				$arregloLogin = array (
    						'claveNoValida',
    						$variable ['usuario'],
    						$_SERVER ['REMOTE_ADDR'],
    						$_SERVER ['HTTP_USER_AGENT']
    				);
                                
                                Redireccionador::redireccionar('index','');
    			}
    		} else {
    			// Registrar el error por usuario no valido
    			$arregloLogin = array (
    					'usuarioNoValido',
    					$variable ['usuario'],
    					$_SERVER ['REMOTE_ADDR'],
    					$_SERVER ['HTTP_USER_AGENT']
    			);
    		}
    	
    	
    	
    	$argumento = json_encode ($arregloLogin);
    	$arreglo = array($registro [0] ["id_usuario"],$argumento);
    	
    	$cadena_sql = $this->miSql->getCadenaSql ( "registrarEvento", $arreglo );
    	$registroAcceso = $esteRecursoDB->ejecutarAcceso ( $cadena_sql, "acceso" );
    }
    
}

$miProcesador = new FormProcessor ( $this->lenguaje, $this->sql );

$resultado= $miProcesador->procesarFormulario ();



