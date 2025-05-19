<?php
require_once 'consultas.php';
if(isset($_GET['a']) && isset($_GET['b'])&& isset($_GET['c'])){
    require_once "Clases/controlador.php";
    require_once 'Clases/estado.php';
    session_start();

// try {
    if (!isset($_SESSION["Controlador"])) {
        $_SESSION["Controlador"] = new Controlador;
    }
    
    $_SESSION["Controlador"] -> miEstado -> Estado = 11;
    $_SESSION["Controlador"] -> miEstado -> IdIdentidad = $_GET['a'];
    $fechaCaducidad = time() + (365 * 24 * 60 * 60);
    setcookie("EmpresaPD", $_GET['b'], $fechaCaducidad, "/");
header('Location:../Index.html');
exit;
}



//http://www.areadecliente.de/index.php?IdD=3820&TipoArchivo=5&Pin=65814415D75C&ST=015106c7
//confirmarUsuarioSeguridadUnificada
?>