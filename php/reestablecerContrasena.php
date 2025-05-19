<?php
require_once 'consultas.php';
session_start();
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_message = "Error [$errno] en $errfile:$errline: $errstr";
    file_put_contents('errores.txt', $error_message . "\n", FILE_APPEND);
    file_put_contents('errores.txt', '['.date('Y-m-d H:i:s').']' . ' -> ' . $error_message . ' -> ', FILE_APPEND);
});


$Ip = null;
$datosBBDD = comprobarBD($_COOKIE['pinCPortalE']);
if(!isset($datosBBDD[0]["Puerto"])){
    $Ip = $datosBBDD[0]["Servidor"];
}else{
    $Ip = $datosBBDD[0]["Servidor"].','.$datosBBDD[0]["Puerto"];
}
if($_COOKIE['pinCPortalE'] == 123654){
    $Ip = '192.168.204.111';
}


if(isset($_POST['arrayDatos'])){
    try {
        $arratLogin = $_POST['arrayDatos'];
        $UsuarioRecuperacion = comprobarUsuarioSURecuperacionContrasena($Ip,$datosBBDD[0]["BBDD"],$arratLogin[0]);
            if($UsuarioRecuperacion){
                echo "Se ha enviado el mail, por favor revise su correo.";
            }else{
                
                echo "Los datos introducidos no son correctos";
            }
    } catch (Exception $e) {
        echo "Fallo al recibir los datos en el servidor";
    } 
}else{
    echo "Fallo al recibir los datos en el servidor";
}
?>