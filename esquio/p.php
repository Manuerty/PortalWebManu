
<?php

function ConexionBD( $server , $bd ,$un = "ClientePrueba", $ps = "contrasena")
{
    //Variables que sacaremos de la BBDD de wordpress"192.168.204.111"= "OnixInteco"
            $bbdd = $bd;
            $username = $un;
            $pass = $ps;
            $server = $server;
    try {
       
        $connectionInfo = array(
            "Database" => $bbdd,
             "UID" => $username,
              "PWD" => $pass,
               "CharacterSet" => "UTF-8",
             "ConnectionPooling" => "1",
              "MultipleActiveResultSets" => '0',
              "Encrypt" => false
              );
        
            $conn = sqlsrv_connect($server, $connectionInfo);
            echo "llegue";
        if ($conn === false) {
            // Hubo un error al conectar
            $errors = sqlsrv_errors();
            die("Error al conectar con la base de datos: " . print_r($errors, true));
        }
        //$conn = new PDO ("sqlsrv:Server=$server,$puerto;Database=$bbdd",$username,$pass);
    } catch (EXCEPTION $e) {
        echo ("No se logró conectar correctamente con la base de datos: $bbdd, error: $e");
    }
    
    return $conn;
}

function extraerDoc_parcial($tipoDoc = null,$ids = null ){
    $conn = ConexionBD();
    $sql = "SELECT id,codigo,Fecha,NombreComercial,Descripcion,Estado,tipo,PinDescargas,OrigenImpresion,importe,PinDescargas 
    FROM vw_PortalCliente_Documentos WHERE tipo=? AND IdSociedad=? ORDER BY tipo,Fecha DESC, Codigo DESC;";
    
    $ListaDoc = array();
    $parm = array($tipoDoc,$ids);
    $stmt = sqlsrv_query($conn, $sql,$parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaDoc, $row);
        }
        return $ListaDoc;
    }
    sqlsrv_close( $conn );
}
function comprobarBD(){
    $conn = ConexionBD("85.214.41.17,23459","OnixPruebaPortal");
    echo "holas";
    //sacar el primero en caso de que coincidan
    $sql = "SELECT TOP 1 CA.BBDD,ISNULL(ca.ServidorExterno,CA.Servidor) AS Servidor ,ISNULL(CA.PuertoExterno,CA.Puerto) AS Puerto FROM Clientes C 
    INNER JOIN dbo.Clientes_Aplicaciones CA ON CA.IdCliente = C.IdCliente WHERE Codigo LIKE '52132145F07C';";
    $DatosBD = array();
    //$parm = array($c);
    //$parm = array($IdCliente);
    $stmt = sqlsrv_query($conn,$sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($DatosBD, $row);
            echo $row['BBDD']."<br>";
        }
        return $DatosBD;
    }
    sqlsrv_close( $conn );
}

function comprobarBBDD($c){
    $datosBBDD = comprobarBD();
    
    
    return  $datosBBDD[0]["BBDD"].$datosBBDD[0]["Servidor"].$datosBBDD[0]["Puerto"];    
}
function checkServerConnectivity($host, $port) {
    $timeout = 5; // Tiempo de espera en segundos para la conexión
    
    // Intentar establecer una conexión con el servidor
    $socket = @fsockopen($host, $port, $errno, $errstr, $timeout);
    
    if (!$socket) {
        // La conexión falló, el servidor no es accesible
        return false;
    } else {
        // La conexión fue exitosa, el servidor es accesible
        fclose($socket);
        

        return true;
    }
}

// Ejemplo de uso






// Obtener y mostrar la versión de PHP
echo "Versión de PHP: " . PHP_VERSION.'<br>';
$host = '85.214.41.17';
$port = 23459; // Puerto HTTP, puedes cambiarlo al puerto que necesites
if (checkServerConnectivity($host, $port)) {
    echo "El servidor $host es accesible en el puerto $port.<br>";
} else {
    echo "No se pudo conectar al servidor $host en el puerto $port.<br>";
}
$conn = ConexionBD("85.214.41.17,23459","OnixPruebaPortal");
echo "me he ejecutado";

// $cur_dir = getcwd();
// $_ENV["imgLogo_aaaaaaaaaaaa"] = $cur_dir;
// echo $_ENV["imgLogo_aaaaaaaaaaaa"];

//echo comprobarBBDD('65814415D75C');
//print_r($bd);
//$conn = ConexionBD("esquio.ddns.net,23459","OnixInteco");

//82.98.171.57

?>1124
