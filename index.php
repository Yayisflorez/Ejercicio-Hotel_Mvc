<?php
    session_start();

    require_once 'controller/loginController.php';
    require_once 'config/config.php';
    require_once 'model/conexion.php';

    $controllerBase = new ControllerBase();

    if(isset($_GET['action'])){
        
        if($_GET['action'] == 'getFormRegisterUser'){
            $controllerBase->verPaginaInicio('views/html/auth/register.php');
        }
        
        if($_GET['action'] == 'registerUser'){ 
            $controllerBase->registerUser();
        }

        if($_GET['action'] == 'getFormLoginUser'){
            $controllerBase->verPaginaInicio('views/html/auth/login.php');
        }
        if($_GET['action'] == 'loginUser'){ 
            $controllerBase->loginUser();
        }

        if($_GET['action'] == 'getFormInicioExitoso'){ 
            $controllerBase->verPaginaInicio('views/html/home2.php');
        }
        if($_GET['action'] == 'getFormInicioExitosoServicios'){ 
            $controllerBase->getFormInicioExitosoSecciones('index.php?action=getFormInicioExitoso#servicios');
        }
        
        if($_GET['action'] == 'getFormInicioExitosoHabitaciones'){ 
            $controllerBase->getFormInicioExitosoSecciones('index.php?action=getFormInicioExitoso#habitaciones');
        }
        if($_GET['action'] == 'getFormInicioExitosoReservas'){ 
            $controllerBase->verPaginaInicio('views/html/auth/reservas.php');
        }

        if($_GET['action'] == 'cerrarSesion'){ 
            $controllerBase->cerrarSesion();
        }
        if($_GET['action'] == 'reservas'){ 
            $controllerBase->verPaginaInicio('views/html/auth/reservas.php');
        }

    }
    else{
        $controllerBase->verPaginaInicio('views/html/home.php');
    }

?>