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

        if($_GET['action'] == 'cerrarSesion'){ 
            $controllerBase->cerrarSesion();
        }

    }
    else{
        $controllerBase->verPaginaInicio('views/html/home.php');
    }

?>