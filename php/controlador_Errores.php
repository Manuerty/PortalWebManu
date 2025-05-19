<?php
function miManejadorErrores($nivel, $mensaje, $archivo, $linea) {
    // crear mensaje de error
    $error = date('Y-m-d H:i:s') . " [$nivel] $mensaje en $archivo línea $linea" . PHP_EOL;

    // Guardar el error en el archivo de registro (error.log)
    error_log($error, 3, 'error.log');

    // Opcional: mostrar el error en el navegador (solo para fines de depuración)
    print_r("Ha ocurrido un error. Por favor, inténtalo de nuevo más tarde.");

    //  Opcional: detener la ejecución del script en caso de error fatal
    if ($nivel === E_USER_ERROR) {
         exit();
    }
}
// Establecer la función personalizada como el manejador de errores
//set_error_handler('miManejadorErrores');
?>
