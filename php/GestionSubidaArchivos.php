<?php
session_start();
if(isset($_FILES["Archivo"])){
    $tamanoMaximoArchivo =  4 * 1024 * 1024;
    //$tamanoMaximoArchivo =  1024 * 1024;

    //Comprobaciones de seguridad del tipo de archivo
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $tipo_mime = finfo_file($finfo, $_FILES["Archivo"]["tmp_name"]);
    finfo_close($finfo);

    $tipos_no_permitidos = array('application/x-msdownload',
                                    'application/x-msdos-program', 
                                    'application/x-dosexec', 
                                    'application/bat', 
                                    'application/cmd',
                                    'application/x-sh',
                                    'application/x-php');
                                    
    if (!in_array($tipo_mime, $tipos_no_permitidos)) {    
        $tamanioArchivo = $_FILES["Archivo"]["size"];

        if ($tamanioArchivo <= $tamanoMaximoArchivo) {
            if(!file_exists('subidasTemp')){
                mkdir('subidasTemp/', 0755, true);
                chown('subidasTemp/', 'www-data');
            }
    
            $directorioDestino = 'subidasTemp/'.$_SESSION["pinC"].'/';
            
            // Verificar si el directorio existe o crearlo si es necesario
            if (!file_exists($directorioDestino)) {
                mkdir($directorioDestino, 0755, true);
                chown($directorioDestino, 'www-data');
            }
   
            if (move_uploaded_file($_FILES["Archivo"]["tmp_name"], $directorioDestino . str_replace([' ', '/'] ,['_','_'] ,$_FILES["Archivo"]["name"]) )){
                $host = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '';
                $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
                $urlCompleta = "https://$host$requestUri";
                $ruta = str_replace('GestionSubidaArchivos.php','',$urlCompleta);
                
                echo $ruta.$directorioDestino.str_replace([' ', '/'] ,['_','_'] ,$_FILES["Archivo"]["name"]);
            } else {
                echo 0;
            }
        }else{
            echo 1;
        }
        
    }else{
        echo 0;
    }
}else{
    echo 0;
}
?>
