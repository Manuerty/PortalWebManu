<?php
require_once 'consultas.php';
//require_once 'Clases/controlador.php';

if(isset($_POST['IdD']) && isset($_POST['TipoArchivo']) && isset($_POST['Pin']) && isset($_POST['ST'])){
    $Ip = null;
    $datosBBDD = comprobarBD($_POST['Pin']); 
    if(!isset($datosBBDD[0]["Puerto"])){
        $Ip = $datosBBDD[0]["Servidor"];
    }else{
        $Ip = $datosBBDD[0]["Servidor"].','.$datosBBDD[0]["Puerto"];
    }
    if($_COOKIE['pinCPortalE'] == 123654){
       $Ip = '192.168.204.111';
    }
    $cadena = $_POST['IdD'].$_POST['TipoArchivo'].$_POST['Pin'];
    //echo $cadena;
    $cadena = md5($cadena);
    $texto = str_replace('=', '', $cadena);
    $primerosCuatro = substr($texto, 0, 4);
    $ultimosCuatro = substr($texto, -4);
    $texto = $ultimosCuatro.$primerosCuatro;
    
    if($texto == $_POST['ST']){
        $nombre_archivo = ExtraerDocumento_Por_CIF($_POST['IdD'],$_POST['TipoArchivo'],$_POST['CIF'],$Ip,$datosBBDD[0]["BBDD"]);
        if(isset($nombre_archivo) && $nombre_archivo!= null){
            $nombre_archivo = $nombre_archivo[0]['codigo'];
            $url_archivo ="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerPDF=1&pin=".$_POST['Pin'].'&IdOrigenImpresion='.$_POST['TipoArchivo'].'&IdPropietario='.$_POST['IdD'];
            $directorio_destino = "archivos/".$_POST['Pin']."/";
            // Verificar si el directorio existe, si no, crearlo
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0755, true);
            }
            // Realiza la solicitud HTTP para obtener el contenido del archivo
            $response = file_get_contents($url_archivo);
            if ($response === false) {
                
            } else {
                // Específica la ruta donde deseas guardar el archivo
                $rutaGuardado = $directorio_destino.$nombre_archivo.'.pdf'; // Reemplaza esto con la ruta y nombre deseado
                
                // Guarda el contenido en un archivo en el servidor
                if (file_put_contents($rutaGuardado, $response) !== false) {
                    echo '../php/'.$rutaGuardado;
                    exit();
                } 
            }
        }
    }

}else{
    
}
echo 0;
//"http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerPDF=1&pin=65814415D75C&IdOrigenImpresion=5&IdPropietario=3830;
//http://localhost/portaldecliente/html/ObtenerDocumentoPC.html?IdD=3830&TipoArchivo=5&Pin=65814415D75C&ST=015106c7
//A36642759
//http://www.areadecliente.de/index.php?IdD=3820&TipoArchivo=5&Pin=65814415D75C&ST=015106c7
?>