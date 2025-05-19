<?php
require_once 'consultas.php';
if(isset($_POST['arrayDatos'])){
    $NombreE = $_POST['arrayDatos'][0];
    $Pinc = $_POST['arrayDatos'][1];
    $LogoUrl = $_POST['arrayDatos'][2];
    $URLFile = $_POST['arrayDatos'][3];
    $TipoP = $_POST['arrayDatos'][0];
    
    
    
}else{
    echo "Error al crear el portal";
}
?>