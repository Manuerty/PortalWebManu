<?php

//Asignar la faltaq de valores como error fatal en vez de como warming
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_message = "Error [$errno] en $errfile:$errline: $errstr";
/*     file_put_contents('errores.txt', $error_message . "\n", FILE_APPEND);
    file_put_contents('errores.txt', '['.date('Y-m-d H:i:s').']' . ' -> ' . $error_message . ' -> ', FILE_APPEND); */
});

require_once "Clases/controlador.php";
require_once 'Clases/estado.php';
session_start();

try {
    if (!isset($_SESSION["Controlador"])) {
        $_SESSION["Controlador"] = new Controlador;
    }
    
    function devuelveContenido(){
        if(!isset($_POST['arrayDatos'])){
            return $_SESSION["Controlador"] -> generarContenido();
        }else{
            return $_SESSION["Controlador"] -> generarContenido($_POST['arrayDatos']);   
        }  
    }
    $respuestaJSON = json_encode(devuelveContenido());
    //throw new Exception("Error intencional");
} catch (Exception $e) {
    // $error_message = "Error: " . $e->getMessage();
    // file_put_contents('errores.txt', $error_message . "\n", FILE_APPEND);
    $respuestaJSON = json_encode(array("Ha ocurrido un error inesperado",null,0));
}

// $respuestaJSON = json_encode(array($_SESSION["TipoPortal"],0,0));
echo $respuestaJSON;
?>
