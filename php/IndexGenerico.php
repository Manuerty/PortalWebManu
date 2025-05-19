<?php
function creacion_actualizacionCookies(){
    $datosConn = file_get_contents("datosInicio.json");
    $datosConn = json_decode($datosConn, true);
    $fechaCaducidad = time() + (365 * 24 * 60 * 60); 
    setcookie("EmpresaPD", $datosConn["DirectorioOrigen"], $fechaCaducidad, "/");
    setcookie("pinCPortalE", $datosConn["pinC"], $fechaCaducidad, "/");
    setcookie("TipoPortalE",  $datosConn["TipoPortal"] , $fechaCaducidad, "/");
    return true;
}
?>