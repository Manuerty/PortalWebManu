<?php
session_start();
header('Content-Type: application/json');

function devuelveContenido() {
    // Asegúrate de que esta ruta exista y sea válida
    if (isset($_SESSION["Controlador"]->miEstado->listaConceptos)) {
        return $_SESSION["Controlador"]->miEstado->listaConceptos;
    } else {
        return []; // Vacío si no hay datos
    }
}

if ($_POST['Accion'] == 0) {
    $opciones = devuelveContenido();
    echo json_encode($opciones); // Enviar como JSON
}
?>