<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["imagen"]) && !empty($_POST["imagen"])) {
        // Obtén la imagen de la firma en formato de datos URI
        $imagen_data_url = $_POST["imagen"];
        
        // Genera un ID único para la firma
        $id_firma = uniqid();
        
        // Establece la ubicación y el nombre de archivo para guardar la firma
        // $nombre_archivo = "firmas/" . $id_firma . ".firma";
        $nombre_archivo = "C:/xampp/htdocs/xampp/PortalWeb/firmas/" . $id_firma . ".jpeg";
        
        // Decodifica la imagen de datos URI y la guarda en el archivo
        $imagen_data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $imagen_data_url));

        
        echo "Imagen comprimida exitosamente.";

        if (file_put_contents($nombre_archivo, $imagen_data)) {
            echo "Firma guardada correctamente.";
        } else {
            echo "Error al guardar la firma.";
        }
    } else {
        echo "No se recibió ninguna imagen de firma.";
    }
} else {
    echo "Acceso no autorizado.";
}
?>