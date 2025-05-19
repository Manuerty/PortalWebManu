<?php


function ConexionBD( $server = "192.168.204.239,23459", $bd = "OnixInteco",$un = "UsuarioProgramaBP", $ps = "^*TMLQLcc11*^")
    {
        //Variables que sacaremos de la BBDD de wordpress
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
            if( $conn === false ) {
                die( print_r( sqlsrv_errors(), true));
    }

            //$conn = new PDO ("sqlsrv:Server=$server,$puerto;Database=$bbdd",$username,$pass);
        } catch (EXCEPTION $e) {
            echo ("No se logró conectar correctamente con la base de datos: $bbdd, error: $e");
        }
        
        return $conn;
    }
?>
