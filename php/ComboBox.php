<?php

require_once "Clases/controlador.php";
require_once 'Clases/estado.php';
session_start();
header('Content-Type: application/json');

function devuelveContenido() {
    if (isset($_SESSION["Controlador"]->miEstado->listaConceptos)) {
        return $_SESSION["Controlador"]->miEstado->listaConceptos;
    } else {
        return [];
    }
}

    
    $opciones = $_SESSION["Controlador"] -> miEstado -> listaConceptos;



if (isset($_POST['Accion']) && $_POST['Accion'] == 0) {
    $opciones = devuelveContenido();
    echo json_encode($opciones);
} else {
    echo json_encode([]);  // siempre devolver JSON válido
}
?>