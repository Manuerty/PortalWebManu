<?php
require_once "Conexion.php";

//---------Definicion de consultas genericas usadas en multiples funciones-----------------------------//
// $sqlAsistencias = "SELECT IdPersonalAsistencia AS id,tipo AS descripcion,Justificada AS descripcion2,FechaInicio,FechaFin  
// FROM dbo.vw_PEAsistencias 
// WHERE IdPersonal = ?";

// $sqlFormacion = "SELECT IdPersonalFormacion AS id,Curso AS descripcion,validada AS descripcion2,Fecha AS FechaInicio 
// FROM dbo.vw_PEFormacion
// WHERE IdPersonal = ? ORDER BY Fecha DESC";



function comprueba_usuario($usuario, $contrasena) {
    try {
        $conn = ConexionBD($_SESSION["Controlador"]->miEstado->IP, $_SESSION["Controlador"]->miEstado->bbdd);
        
        if (!$conn) {
            return false; 
        }

        $IdTipoUsu = ($_SESSION["Controlador"]->miEstado->tipo_App == 1) ? 3 : 1;

        $sql = "EXECUTE seguridadunificada_identidad_select @Usuario=?, @Contrasena=?, @IdTipoUsuario=?;";
        $params = array($usuario, $contrasena, $IdTipoUsu);
        $stmt = sqlsrv_prepare($conn, $sql, $params);

        if (!$stmt || !sqlsrv_execute($stmt)) {
            sqlsrv_close($conn);
            return false; 
        }


        if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            sqlsrv_close($conn);
            return array($row['IdUsuario'], $row["Usuario"], $row["Nombre"], $row["IdTipoUsuario"], $row['IdIdentidad']);
        }
        sqlsrv_close($conn);
        return 0;

    } catch (Exception $e) {
        return false; 
    }
}
function comprobarDatosPersonal(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $arrayPermisos = array();
    $sql = "SELECT 
       FinalC,
       DiasVacaciones,
       telefonoparticular,
       direccion,
       codpostal,
       poblacion,
       NombreCuenta,
       CCC,
       Iban,
       Bic,
       TallaCamisa,
       TallaPantalon,
       TallaZapatos,
       Turno,
       Departamento,
       Cargo,
       FirmaNominaPreviaDescarga,
       --Uf_DameCadena_CompetenciasPersonal @@idpersonal = P.idpersonal AS Competencias
    FROM vw_PEDatosInicio  
    WHERE IdPersonal = ? ;";
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
    
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
   
    if (!sqlsrv_execute($stmt)) {
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($arrayPermisos, $row);
        }
        return $arrayPermisos[0];
    }
    sqlsrv_close( $conn );
} 

function comprobarPermisosUsuarios(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $arrayPermisos = array();
    $tipoUsr = $_SESSION["TipoPortal"] == 1 ? 1 : 2;

    $sql = "SELECT IdFormulario,Imagen,OrdenEstado,TipoApp,ValorAccion,EstiloPestaña,Nombre,ColorPestana
    FROM vw_PW_PermisosPestanas  
    WHERE IdIdentidad = ? AND TipoApp = ?;";
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdIdentidad,$tipoUsr);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($arrayPermisos, $row);
        }
        return $arrayPermisos;
    }
    sqlsrv_close( $conn );
}


#Seccion Superusuarios
function comprobarConfiguracionesAdicionalesUsuarios(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $arrayConfiguraciones = array();
    $sql = "SELECT IdValor
    FROM SeguridadUnificada_ValorConfiguracionEnlace  
    WHERE IdIdentidad = ?;";
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdIdentidad);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($arrayConfiguraciones, $row['IdValor']);
        }
        return $arrayConfiguraciones;
    }
    sqlsrv_close( $conn );
}
function obtenerListaEmpleados(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $listaUsuaros = array();
    $sql = "EXECUTE SeguridadUnificada_Personal_Select ";
    $stmt = sqlsrv_prepare($conn, $sql);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($listaUsuaros, $row);
        }
        return $listaUsuaros;
    }
    sqlsrv_close($conn);
}


#Sección Sociedades
//Mostrar las sociedades a la que este vinculado el cliente
function compruebaSociedades($Id){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    
    if($_SESSION["Controlador"] -> miEstado ->  tipo_App == 1){
        $sql = "SELECT S.idSociedad,S.NombreFiscal FROM Sociedades S 
        INNER JOIN dbo.Clientes_Sociedades CS ON CS.idSociedad = S.idSociedad WHERE idCliente =?;";
    }elseif($_SESSION["Controlador"] -> miEstado ->  tipo_App == 2){
        
        $sql = "SELECT S.idSociedad,S.NombreFiscal FROM Personal P 
        INNER JOIN dbo.Sociedades S ON P.idSociedad = S.idSociedad WHERE idpersonal =?;";

    }
    $ListaSociedades = array();
    $parm = array($Id);
    $stmt = sqlsrv_query($conn, $sql,$parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaSociedades, $row);
        }
        return $ListaSociedades;
    }
    sqlsrv_close( $conn );
}
//Comprobar la version de la BBDD si esta actualizada
function compararVersionBBDD(){
    $versionBBDD = '';
    $versionID = '';
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql =  "SELECT TOP 1 (IdActualizacionBD) AS IdActualizacionBD FROM dbo.ActualizacionBD ORDER BY IdActualizacionBD DESC";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        $versionBBDD = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);    
    }
    sqlsrv_close( $conn );
    $conn2 = ConexionBD('85.214.41.17,23459','IntecoDistribucion',"sa","Iiaslgv52d");
    $sql =  "SELECT TOP 1 (IdActualizacionBD) AS IdActualizacionBD FROM dbo.ActualizacionBD ORDER BY IdActualizacionBD DESC";
    $stmt = sqlsrv_query($conn2, $sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        $versionID = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
    sqlsrv_close( $conn2 );

    if($versionBBDD == $versionID || $_SESSION["pinC"] == 123654){
        return true;
    }else{
        return false;
    }
}
//cambiar contraseña
function cambiarContrasena( $CVieja, $Cnueva){
    $conn = ConexionBD($_SESSION["Controlador"]->miEstado->IP, $_SESSION["Controlador"]->miEstado->bbdd);
    $sql = "SELECT IdUsuario FROM dbo.SeguridadUnificada_Identidad 
    WHERE IdIdentidad = ? AND pwdcompare(?,ContrasenaEnc) = 1";  
    //$actualizado;
    $parm = array($_SESSION["Controlador"]->miEstado->IdIdentidad,$CVieja);
    $stmt = sqlsrv_query($conn, $sql,$parm);
    //var_dump($parm);
    $usu = array();
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($usu, $row);
        }
    }
    if(Isset($usu[0])){
        $sql = "EXECUTE dbo.SeguridadUnificada_Identidad_Update_Contrasena @IdIdentidad = ?,
		@Contrasena = ?";
        
        $parm = array($_SESSION["Controlador"]->miEstado->IdIdentidad, $Cnueva);
        //var_dump($parm);
        $stmt = sqlsrv_prepare($conn, $sql,$parm);

        //print_r($stmt);
        if (!sqlsrv_execute($stmt)) {
            print_r(sqlsrv_errors());
            return false;
            die;
        } else {
            return true;
        }
        

    } 
    //var_dump($usu);
    sqlsrv_close( $conn );
}

function reestablecerContrasenaConfirmar($Cnueva){
    $conn = ConexionBD($_SESSION["Controlador"]->miEstado->IP, $_SESSION["Controlador"]->miEstado->bbdd);
    $sql = "EXECUTE dbo.SeguridadUnificada_Identidad_Update_Contrasena @IdIdentidad = ?,
		@Contrasena = ?";
        
        $parm = array($_SESSION["Controlador"]->miEstado->IdIdentidad, $Cnueva);
        //var_dump($parm);
        $stmt = sqlsrv_prepare($conn, $sql,$parm);

        //print_r($stmt);
        if (!sqlsrv_execute($stmt)) {
            print_r(sqlsrv_errors());
            return false;
            die;
        } else {
            return true;
        }
}
function pruebaC(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = "SELECT IdUsuario FROM dbo.EXECUTE dbo.SeguridadUnificada_Identidad_Update_Contrasena @IdIdentidad = ?,
    @Contrasena = ?;";
    $parm = array(62,'Abel2');
    $ListaPC = array();
    $stmt = sqlsrv_query($conn, $sql,$parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {

        }
        
    }
    sqlsrv_close( $conn );
    
}



//Extraer las personas de contacto del cliente
function compruebaPersonasContaco(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = "SELECT IdPersonaContacto,Nombre FROM vw_PCPersonasContacto_Propietarios WHERE IdPropietario = ?;";
    $ListaPC = array();
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdCliente);
    $stmt = sqlsrv_query($conn, $sql,$parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaPC, $row);
        }
        return $ListaPC;
    }
    sqlsrv_close( $conn );
}

//extraer los documentos del cliente
function extraerDoc_parcial($tipoDoc = null,$ids = null,$c = null,$fecha = '19981201 00:00:00'){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $fechaFormateada = date('Ymd H:i:s', strtotime($fecha));
    $ListaDoc = array();
    $sql;
    $parm;
    //Estraer los documentos
    // if($tipoDoc == 4){
    //     // tareas del cliente
    //     $sql = "SELECT IdProyecto as id,Estado,Codigo as codigo,ContadorTareas,ContadorTareasAbiertas,FechaCreacion,Nombre_estado,4 as tipo 
    //     FROM vw_PWProyectosClientes 
    //     WHERE IdCliente = ? AND FechaCreacion> ?
    //     ORDER BY FechaCreacion DESC;";
    //      $parm = array($_SESSION["Controlador"] -> miEstado -> IdCliente,$fechaFormateada);
    // }else{
        $sql = "SELECT id,codigo,Fecha,NombreComercial,Descripcion,Estado,tipo,PinDescargas,OrigenImpresion,importe,PinDescargas,color,contadorDoc,Descripcion2
        FROM     
        vw_PortalCliente_Documentos
        WHERE tipo=? AND IdSociedad=? AND IdCliente=? AND Fecha> ?
        ORDER BY Fecha DESC, Codigo DESC;";
        
        $parm = array($tipoDoc,$ids, $c,$fechaFormateada);
    //}
    $stmt = sqlsrv_query($conn, $sql, $parm);
    if ($stmt === false){
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                array_push($ListaDoc, $row);    
        }
        return $ListaDoc;
    }
    sqlsrv_close( $conn );
}

//extraer el contador de documentos del cliente
function extraerFiltroDoc_Parcial($tipoDoc = null,$ids = null,$c = null){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Estraer los documentos
    $sql = "SELECT tipo,IdSociedad,Estado,Filtro
    FROM vw_PCDocumentos_Web_Agrupados 
    WHERE tipo=? AND IdSociedad=? AND IdCliente=?;";
    $ListaF = array();
    $parm = array($tipoDoc,$ids, $c );
    $stmt = sqlsrv_query($conn, $sql, $parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaF, $row);
        }
        return $ListaF;
    }
    sqlsrv_close( $conn );
    
}

//Insertar la solicitud de tarea
function SolicitudTareas_Insert($desc){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $IdPer = 118;
    $IdTp = 10;
    if($_SESSION["pinC"] == 123654){
        $IdPer = 93;
        $IdTp = 16;
    }
    $sql = "DECLARE @IdTarea INT,
            @FECHA SMALLDATETIME = GETDATE();
            EXECUTE dbo.up_bp_Tareas_Insert @IdTarea = @IdTarea OUTPUT,
                                    @FechaCreacion = @FECHA,
                                    @Descripcion = ?,
                                    @IdCliente = ?,              
                                    @IdSociedad = ?,
                                    @IdPersonalCreador = ?,
                                    @IdPersonalAsignado = ?,
                                    @IdTareaTipo= ?";
    $parm = array($desc,$_SESSION["Controlador"] -> miEstado -> IdCliente,
            $_SESSION["Controlador"] -> miEstado -> id_sociedad,
            $IdPer,
            $IdPer,
            $IdTp);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        print_r(sqlsrv_errors());
        return false;
        die;
    } else {
        return true;
    }
    sqlsrv_close( $conn );
}

function ExtraerDocumento_Por_CIF($Id,$Tipo,$cif,$Ip,$bd){
    $conn = ConexionBD($Ip,$bd);
    //Extraer los documentos
    $DocCif = array();
    $sql = "SELECT codigo
    FROM dbo.vw_PortalCliente_Documentos WHERE OrigenImpresion=? AND id=? AND cif=?";
    $parm = array($Tipo,$Id,$cif);
    $stmt = sqlsrv_query($conn, $sql, $parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($DocCif, $row);
        }
        return $DocCif;
    }
    sqlsrv_close( $conn );
    
}

//extraer el contador de documentos del cliente
function extraerRecursosProyectos($idp = null){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Comprueba los datos de la tabla de clientes
    $sql = "EXECUTE up_proyectos_FasesResumen_Select_PW  @IdProyecto=?;";
    $ListaPT = array();
    $ListaRec = array();
    $parm = array($idp);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaPT, $row);
        }
        sqlsrv_next_result($stmt);
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaRec, $row);
        }
        return array($ListaPT,$ListaRec);
    }
    sqlsrv_close($conn);
    
    
}

//funciones de extraer datos de la jornada, actualizar para juntarlos
function comprueba_jornada_personal(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Comprueba los datos de la tabla de clientes
    $sql = "SELECT Fecha,Estado,IdCentroTrabajo FROM  dbo.vw_Personal_Estado_Appsheet WHERE idpersonal = ?;";
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            return array($row["Estado"],$row['Fecha'],$row['IdCentroTrabajo']);
        }
    }
    sqlsrv_close( $conn );
} 

function extraer_JornadaHistorico(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    //Comprueba los datos de la tabla de clientes
    $sql = "SELECT FechaInicio,FechaFin,HoraInicioFin,DiaIto,DuracionHoras,LongLat_Entrada,LongLat_Salida
    FROM  dbo.vw_PW_TiemposPersonal_historico_html WHERE idpersonal = ? ORDER BY FechaInicio DESC ;";
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
    $DatosBD = array();
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    //print_r($stmt);
    if (!sqlsrv_execute($stmt)) {
        return false;
        die;
    } else {
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($DatosBD, $row);
        }
        return $DatosBD;
    }
    sqlsrv_close( $conn );
} 

//extraer todos los doc del Cliente
function extraerArchivosCliente_Documento(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    
    $DatosBD = array();
    $sql ="SELECT * FROM vw_PW_ArchivosClientes 
    WHERE IdIdentidad = ? AND IdPropietario = ?";
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdIdentidad, $_SESSION["Controlador"] -> miEstado -> IdPropietario);
    $stmt = sqlsrv_query($conn,$sql,$parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            
            array_push($DatosBD, $row);
        }
        return $DatosBD;
    }
    
    sqlsrv_close( $conn );
}
//extraer todos los doc del personal
function extraerDocPersonal_Masivo(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $DatosBD = array();
    //1 - Asistencias
        $sql1 = "SELECT IdPersonalAsistencia AS id,descripcion AS Descripcion,Justificada AS descripcion2,FechaInicio AS descripcionLateral,FechaFin ,NumeroArchivos,color, tipo AS descripcion3,
        FechaInicio,FechaFin, duracion AS descripcion4,ColorPestana,IdTipo
        FROM dbo.vw_PEAsistencias 
        WHERE IdPersonal = ?  ORDER BY IdPersonalAsistencia DESC";
        $DatosBD = array();
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql1,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 1;
                array_push($DatosBD, $row);
            }
        }
    //2 - Contratos
        $sql2 = "SELECT idpersonalcontrato AS id,tipo AS Descripcion,EstadoContrato AS descripcion2,NumeroArchivos,color,InicioFin AS descripcionLateral, 
        FechaInicio, FechaInicio AS FechaFin,observaciones,color as ColorPestana  FROM dbo.vw_PEContratos 
        WHERE idpersonal = ? ORDER BY fechainicio DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql2,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 2;
                array_push($DatosBD, $row);
            }
        }
    //3 - Documentos
        $sql2 = "SELECT IdArchivo AS id, Documento AS Descripcion, tipoArchivo AS descripcion2,FechaCreacionRegistro AS descripcionLateral,
        IdTipoPropietario,IdPropietario,Documento, Firmable ,Firmado
        FROM dbo.vw_PEArchivosPersonal 
        WHERE idpersonal = ? ORDER BY FechaCreacionRegistro DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql2,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 3;
                array_push($DatosBD, $row);
            }
        }
    //3.5 - Observaciones
        $sql2 = "SELECT IdObservacion AS id, 
        Descripcion AS Descripcion,
        Fecha AS descripcionLateral,
        IdTipoPropietario,IdPropietario
        FROM dbo.vw_PEObservaciones 
        WHERE idpersonal = ? ORDER BY Fecha DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql2,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 3.5;
                array_push($DatosBD, $row);
            }
        }
    //4 - Formacion
        $sql2 = "SELECT IdPersonalFormacion AS id,Curso AS Descripcion,validada AS descripcion2,Fecha AS descripcionLateral,color,Horas  AS descripcion3,color as ColorPestana
        FROM dbo.vw_PEFormacion
        WHERE IdPersonal = ? ORDER BY Fecha DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql2,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 4;
                array_push($DatosBD, $row);
            }
        }
    //5 - Incidencias
        $sql5 = "SELECT IdPersonalIncidencia AS id,descripcion AS Descripcion,Justificada AS descripcion2,Fecha AS descripcionLateral,color,TipoIncidencia AS descripcion3,
        Fecha AS FechaInicio, Fecha AS FechaFin,color as ColorPestana
        FROM dbo.vw_PEIncidencias
        WHERE IdPersonal = ? ORDER BY Fecha DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql5,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 5;
                array_push($DatosBD, $row);
            }
        }

    //6 - Material
        $sql6 = "SELECT IdPersonalMaterial AS id,Material AS Descripcion,Cantidad AS descripcion3,Fecha AS descripcionLateral, color,Validado AS descripcion2,
        Fecha AS FechaInicio, Fecha AS FechaFin,color as ColorPestana
        FROM dbo.vw_PEMaterial
        WHERE IdPersonal = ? ORDER BY Fecha DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql6,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 6;
                array_push($DatosBD, $row);
            }
        }
    //7 - Nominas
        $sql7 = "SELECT IdPersonalSalario AS id ,Fecha  AS Descripcion,Pagado  AS descripcion2, CAST(Liquido AS VARCHAR)+'€' AS descripcionLateral,color,'Bruto :'+CAST(SalarioBruto AS VARCHAR)+'€' AS descripcion3,
        FechaRegistro AS FechaInicio, FechaRegistro AS FechaFin,IdArchivo as NumeroArchivos,color as ColorPestana
        FROM dbo.vw_selectSalariosAppsheet
        WHERE IdPersonal = ? ORDER BY FechaRegistro DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql7,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 7;
                array_push($DatosBD, $row);
            }
        }
    //8 - Vacaciones
        $sql8 = "SELECT IdPersonalVacaciones AS id,
        EstadoV  AS Descripcion, 
        RangoFechas  AS descripcionLateral,  
        Estado2 AS descripcion2, 
        color2 as color,
        FechaInicio,FechaFin,Año as AñoV,
        Estado,color as ColorPestana
        FROM dbo.vw_PEVacaciones
        WHERE IdPersonal = ? ORDER BY FechaInicio DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql8,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 8;
                array_push($DatosBD, $row);
            }
        }
    //8_5 resumen anual vacaciones
        $sql8_5 = "SELECT IdPersonal AS id,DiasTotales,DiasDisfrutados,DiasDenegados,DiasConcedidos,DiasPendientes,Año
        FROM dbo.vw_PEVacacionesResumenAnual
        WHERE IdPersonal = ? ORDER BY Año DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql8_5,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 8.5;
                array_push($DatosBD, $row);
            }
        }
    
    //9 Proyectos a los que tiene acceso el personal
        $sql9 = "SELECT IdProyecto AS id,
        ISNULL(Codigo,'')+' '+ISNULL(Nombre,'')  AS Descripcion,
        case
            when Estado = 0 THEN 'Iniciado'
            --when Estado=2 Then 'Cerrado'
            Else 'en espera'
        End  AS descripcionLateral,
        CONVERT(VARCHAR(50),FechaCreacion,103) AS descripcion2,
        'GREEN' AS color
        FROM dbo.Proyectos
        where Estado = 0 AND IdProyecto IN (SELECT IdProyecto FROM vw_Fases_select_appsheet GROUP BY IdProyecto)
        ORDER BY FechaCreacion DESC";
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        //$parm = array($IdCliente);
        $stmt = sqlsrv_query($conn,$sql9,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 9;
                array_push($DatosBD, $row);
            }
        }

        
    
    sqlsrv_close( $conn );
    return $DatosBD;
    

}

//Extraer los tipos de archivo a los que tiene acceso el personal 
function extraerArchivosTiposAccesos(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = "SELECT IdArchivoTipo,Nombre,IdTipoPropietario FROM dbo.vw_PWArchivosTipos_Acceso WHERE IdIdentidad  = ?;";
    $ListaTipos = array();
    $parm = array($_SESSION["Controlador"] -> miEstado -> IdIdentidad);
    $stmt = sqlsrv_query($conn, $sql, $parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($ListaTipos, $row);
        }
        return $ListaTipos;
    }
    sqlsrv_close( $conn );
}
//Extraer los dropdonws de los forms
function extraerDropdownsFormsValores(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $DatosBD = array();
    $sql = "SELECT IdTipoDefinicion,CodigoPestaña,Nombre,IdTipo
    FROM dbo.vw_camposFormDropdown";
    $stmt = sqlsrv_query($conn,$sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($DatosBD, $row);
        }
        return $DatosBD;
    }
    sqlsrv_close( $conn );
}

function extraerFasesProyectos(){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    
    $Fases = array();
    $TiposTareas = array();
    $ProyectosMaterialesTipos = array();
    $Articulos = array();
    $sql = "SELECT IdProyectoTarea,IdProyecto,DireccionArbolNombre AS Descripcion, CuentaOF
            from vw_Fases_select_appsheet 
            ";
            //where CuentaOF <> 0
    // $sql = "SELECT pt.IdProyectoTarea,p.IdProyecto,pt.Codigo +' - '+ pt.Nombre AS Descripcion FROM ProyectosTareas pt 
    //                 INNER JOIN dbo.Proyectos p ON p.IdProyecto = pt.IdProyecto 
    //                 WHERE p.Estado = 0 AND	pt.FechaFin IS NULL
    //                 ORDER BY pt.Codigo";

    $stmt = sqlsrv_query($conn,$sql);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($Fases, $row);
        }
    }
    $sql2 = "SELECT IdProduccionTipoTarea,
            Nombre 
            FROM dbo.ProduccionTiposTareas
            WHERE TareaMontaje = 1";

    $stmt2 = sqlsrv_query($conn,$sql2);
    if ($stmt2 === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
            array_push($TiposTareas, $row);
        }
    }
    $sql3 = "SELECT IdProyectoMaterialTipo,Nombre FROM dbo.ProyectosMaterialesTipos";
    $stmt3 = sqlsrv_query($conn,$sql3);
    if ($stmt3 === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt3, SQLSRV_FETCH_ASSOC)) {
            array_push($ProyectosMaterialesTipos, $row);
        }
    }

    $sql4 = "SELECT idArticulo,Descripcion FROM dbo.Articulos WHERE Gasto = 1 AND MovimientoStock = 0";

    $stmt4 = sqlsrv_query($conn,$sql4);
    if ($stmt4 === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt4, SQLSRV_FETCH_ASSOC)) {
            array_push($Articulos, $row);
        }
    }
    
    return array($Fases,$TiposTareas,$ProyectosMaterialesTipos,$Articulos);
}


function extraerRecursosFaseProyecto($idProyecto,$FaseMaterial){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $DatosBD = array();

    if($FaseMaterial == 0) {
        $sql= "SELECT T.IdProyectoRecurso AS id,T.IdProyectoTarea,T.IdProduccionTipoTarea AS IdTipoTarea,t.FechaInicio AS FechaInicio,t.FechaFin AS FechaFin,
                CONVERT(VARCHAR(50), t.FechaInicio, 103)+ ' ' + CONVERT(VARCHAR(5), t.FechaInicio, 108)  AS descripcion2,
                CONVERT(VARCHAR(5),ROUND(T.Duracion,2)) +' horas' AS descripcionLateral,
                Duracion,
				IsNULL(ptt.Nombre,pr.Nombre) AS Descripcion,
				pt.Nombre AS descripcion3,
				t.Fecha AS fecha 
                FROM dbo.ProyectosRecursos T
                INNER JOIN proyectos Pr ON Pr.IdProyecto = T.IdProyecto
                left JOIN dbo.ProyectosTareas pt ON pt.IdProyectoTarea=t.IdProyectoTarea
				LEFT JOIN dbo.ProduccionTiposTareas ptt ON ptt.IdProduccionTipoTarea = T.IdProduccionTipoTarea

                WHERE T.IdProyecto = ? AND T.idpersonal = ?";
        $parm = array($idProyecto,$_SESSION["Controlador"] -> miEstado -> IdPersonal);
        $stmt = sqlsrv_query($conn,$sql,$parm);


        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 9.1;
                array_push($DatosBD, $row);
            }
        }
    }else{

        $sql= " SELECT PM.IdProyectoMaterial AS id,PM.IdProyectoTarea,
                CONVERT(VARCHAR(50), PM.Fecha, 103) AS descripcion2,
                'Cant: ' + CONVERT(VARCHAR(50),PM.Cantidad) AS descripcionLateral,
				PM.Descripcion AS Descripcion,PM.Fecha AS fecha ,
                PM.IdProyectoTarea,
                IdProyectoMaterialTipo, 
                IdArticulo,
                PM.Cantidad,
                Coste,
                Fecha
                FROM dbo.ProyectosMateriales PM
                INNER JOIN proyectos Pr ON Pr.IdProyecto = PM.IdProyecto
                INNER JOIN dbo.ProyectosTareas pt ON pt.IdProyectoTarea=PM.IdProyectoTarea
                WHERE PM.IdProyecto = ? AND PM.idpersonal = ?";
        $parm = array($idProyecto,$_SESSION["Controlador"] -> miEstado -> IdPersonal);
        $stmt = sqlsrv_query($conn,$sql,$parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = 9.2;
                array_push($DatosBD, $row);
            }
        }

    }
    return($DatosBD);
    sqlsrv_close( $conn );
}

//iniciar o finalizar jornada
function exec_up_Tiempos_Insert($lat = '',$long = '' , $horaLocal = null){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = "DECLARE @IdTiempo INT,
    @FechaVisualizacion DATETIME;
    EXEC up_Tiempos_Insert
    @IdTiempo = @IdTiempo OUTPUT,
	@IdSociedad = ?,
	@IdCentroTrabajo = ?,
	@IdPersonal = ?,
    @FechaInicio = ?,
	@InicioFin= ?,
    @FechaImputacion = ?,
    @Latitud = ?,                     
    @Longitud = ?,
    @Observaciones = ?,
    @FechaVisualizacion = @FechaVisualizacion OUTPUT";
    $horaFormateada = '';
    if($horaLocal != null){
        $horaFormateada = 'Hora Geolocalizacion : '.date('H:i', strtotime($horaLocal));
    }
    $parm = array($_SESSION["Controlador"] -> miEstado -> id_sociedad, 
            $_SESSION["Controlador"] -> miEstado ->EstadoJornada[2],
            $_SESSION["Controlador"] -> miEstado -> IdPersonal,
            date('Ymd H:i:s'),
            $_SESSION["Controlador"] -> miEstado ->EstadoJornada[0],
            date('Ymd H:i:s'),
            $lat,
            $long,
            $horaFormateada);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    } 
    sqlsrv_close( $conn );
}

function exect_Insert_From_Dinamico($arrayValores){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = "";
    $arrayForm = array_filter($_SESSION["Controlador"] -> miEstado -> formularios, function ($form) {
        return $form["Estado"] == $_SESSION["Controlador"] -> miEstado -> Estado;
    });
 

    $arrayEjecutable = array_shift($arrayForm);

    if ($arrayEjecutable["Instruccion"] == "INSERT_UP") {
        $sql .= "INSERT INTO ".$arrayEjecutable["Pro_Tabla"].' (';
        $sql2 ='VALUES (';
        for ($i = 0; $i < count($arrayEjecutable['Campos']); $i++){
            if($i == (count($arrayEjecutable['Campos']) - 1) ){
                $sql .= $arrayEjecutable['Campos'][$i]["Variable"].')';
                $sql2 .= ' ?)';
            }elseif($arrayEjecutable['Campos'][$i]["OUTPUT"] == 0){
                $sql .= $arrayEjecutable['Campos'][$i]["Variable"].',';
                $sql2 .= ' ?,';
            }
        }
        $sql .= $sql2;
    }elseif($arrayEjecutable["Instruccion"] == "EXECUTE"){
        $sql .= "DECLARE @".$arrayEjecutable['Campos'][0]["Variable"]." INT ";
        $sql .= "EXECUTE ".$arrayEjecutable["Pro_Tabla"].' ';

        for ($i = 0; $i < count($arrayEjecutable['Campos']); $i++){
            if($i == (count($arrayEjecutable['Campos']) - 1) ){
                $sql .= '@'.$arrayEjecutable['Campos'][$i]["Variable"].' = ?';
            }elseif($arrayEjecutable['Campos'][$i]["OUTPUT"] == 0){
                $sql .= '@'.$arrayEjecutable['Campos'][$i]["Variable"].' = ?,';
                
            }else{
                $sql .= '@'.$arrayEjecutable['Campos'][$i]["Variable"]." = ".'@'.$arrayEjecutable['Campos'][$i]["Variable"]." OUTPUT," ;
            }
        }
    }

        $parm = $arrayValores;
        // print_r($parm);
        // print_r($sql);
        $stmt = sqlsrv_prepare($conn, $sql, $parm);
        
        if (!sqlsrv_execute($stmt)) {
            die(print_r(sqlsrv_errors(), true));
            return false;
            die;
        }else{
            //SOLUCION TEMPORAL esto esta mal esto esta mal
            $valoresInsertados = array();
            $sqlReturn = "";
            $tipoDoc = 0;
            switch ($_SESSION["Controlador"] -> miEstado -> Estado) {
                case 4.1:
                    $sqlReturn = "SELECT TOP 1 IdPersonalAsistencia AS id,Descripcion AS descripcion,Justificada AS descripcion2,FechaInicio AS descripcionLateral,FechaFin ,NumeroArchivos,color, tipo AS descripcion3,
                    FechaInicio,FechaFin, duracion AS descripcion4
                    FROM dbo.vw_PEAsistencias 
                    WHERE IdPersonal = ? ORDER BY IdPersonalAsistencia DESC" ;
                    $tipoDoc = 1;
                    break;
                case 4.4:
                    $sqlReturn = "SELECT TOP 1 IdArchivo AS id, Documento AS Descripcion, tipoArchivo AS descripcion2,FechaCreacionRegistro AS descripcionLateral,
                    IdTipoPropietario,IdPropietario,Documento, Firmable ,Firmado
                    FROM dbo.vw_PEArchivosPersonal 
                    WHERE idpersonal = ? ORDER BY IdArchivo DESC" ;
                    $tipoDoc = 3;
                    break;
                case 4.45:
                    $sqlReturn = "SELECT TOP 1 IdObservacion AS id, 
                                Descripcion AS Descripcion,
                                Fecha AS descripcionLateral,
                                IdTipoPropietario,IdPropietario
                                FROM dbo.vw_PEObservaciones 
                                WHERE idpersonal = ? ORDER BY IdObservacion DESC" ;
                    $tipoDoc = 3.5;
                    break;
                    
                   
                case 4.5:
                    
                    $sqlReturn = "SELECT TOP 1  IdPersonalFormacion AS id,Curso AS Descripcion,validada AS descripcion2,Fecha AS descripcionLateral,color,Horas  AS descripcion3
                    FROM dbo.vw_PEFormacion
                    WHERE IdPersonal = ? ORDER BY IdPersonalFormacion DESC" ;
                    $tipoDoc = 4;
                    break;
                    
                case 4.6:
                    $sqlReturn = "SELECT TOP 1 IdPersonalIncidencia AS id,descripcion AS Descripcion,Justificada AS descripcion2,Fecha AS descripcionLateral,color,TipoIncidencia AS descripcion3,
                    Fecha AS FechaInicio, Fecha AS FechaFin
                    FROM dbo.vw_PEIncidencias
                    WHERE IdPersonal = ?  ORDER BY IdPersonalIncidencia DESC";
                    
                    $tipoDoc = 5;
                    break;
                case 4.7:
                    $sqlReturn = "SELECT TOP 1 IdPersonalMaterial AS id,Material AS Descripcion,Cantidad AS descripcion3,Fecha AS descripcionLateral, color,Validado AS descripcion2,
                    Fecha AS FechaInicio, Fecha AS FechaFin
                    FROM dbo.vw_PEMaterial
                    WHERE IdPersonal = ? ORDER BY IdPersonalMaterial DESC";
                    
                    $tipoDoc = 6;
                    break;
                case 4.9:
                
                    $sqlReturn = "SELECT top 1 IdPersonalVacaciones AS id,EstadoV  AS Descripcion, RangoFechas  AS descripcionLateral,  Estado2 AS descripcion2, color2 as color,'Año : ' + CAST(Año AS VARCHAR) AS descripcion3,
                    FechaInicio,FechaFin,Año as AñoV
                    FROM dbo.vw_PEVacaciones
                    WHERE IdPersonal = ? ORDER BY IdPersonalVacaciones DESC";
                    $tipoDoc = 8;
                    break;
                default:
                    
                    break;
            }
            $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
            sqlsrv_free_stmt($stmt);
            $stmt = sqlsrv_query($conn, $sqlReturn, $parm);
            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }else{
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $row["tipoDocPortal"] = $tipoDoc;
                    array_push($valoresInsertados, $row);
                }
                return $valoresInsertados;
            }
        }
    sqlsrv_close( $conn );
}

function exect_Insert_From_DinamicoSubidaArchivos($arrayValores,$ruta){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = 'INSERT INTO dbo.PEArchivosAuxiliar
    (
        IdArchivoAppsheet,
        IdIdentidad,
        Link,
        IdPropietario,
        IdTipoPropietario,
        IdArchivosTipo   
    )
    VALUES
    (   ?,
        ?,?,?,?,?
        )';

    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $claveGenerada = '';

    for ($i = 0; $i < 9; $i++) {
        $claveGenerada .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    $claveGenerada;
    
    $parm = array($claveGenerada, 
            $_SESSION["Controlador"] -> miEstado -> IdIdentidad,
            $ruta,
            $_SESSION["Controlador"] -> miEstado -> IdPropietarioAuxiliar,
            $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario,
            $arrayValores[0]);
    // print_r($parm);
    // print_r($sql);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);

    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    }else{
        $valoresInsertados = array();
        $sqlReturn = "";
        $tipoDoc = 0;
        
        $sqlReturn = "SELECT TOP 1 IdArchivo AS id, Documento AS Descripcion, tipoArchivo AS descripcion2,FechaCreacionRegistro AS descripcionLateral,
        IdTipoPropietario,IdPropietario,Documento, Firmable ,Firmado
        FROM dbo.vw_PEArchivosPersonal 
        WHERE idpersonal = ? ORDER BY IdArchivo DESC" ;
        $tipoDoc = 3;
        $parm = array($_SESSION["Controlador"] -> miEstado -> IdPersonal);
        sqlsrv_free_stmt($stmt);
        $stmt = sqlsrv_query($conn, $sqlReturn, $parm);
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        }else{
            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                $row["tipoDocPortal"] = $tipoDoc;
                array_push($valoresInsertados, $row);
            }
            return $valoresInsertados;
        }
    }
     sqlsrv_close( $conn );
        
}

function insertProyectosTareaMaterial($MaterialProyecto,$arrayDatos){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = "";
    $parm = array();
    $idProyectoMaterial = 0;
    if($MaterialProyecto == 6.1){
            $sql = " INSERT INTO dbo.PETiemposProyectosAppSheet
        (IdPETiemposProyectosAppSheet,
        IdProyecto,
        IdTipoTarea,
        IdProyectoTarea,      
        FechaInicio,
        FechaFin,
        IdTipoHora,
        Fecha,
        IdPersonal,
        IdIdentidad,
        IdModificar
        )
        VALUES
        (?,?,?,?,?,?,?,GETDATE(),?,?,?)";

        $parm = array(
        $randomNumber = rand(10000, 999999),
        $_SESSION["Controlador"] -> miEstado -> IdPropietario,
        $arrayDatos[0],
        $arrayDatos[1],
        date('Ymd H:i:s', strtotime($arrayDatos[2]) ),
        date('Ymd H:i:s', strtotime($arrayDatos[3]) ),
        69,
        $_SESSION["Controlador"] -> miEstado -> IdPersonal,
        $_SESSION["Controlador"] -> miEstado -> IdIdentidad,
        $arrayDatos[5]
    );
 
    }else{

        if($arrayDatos[7] == 0 || $arrayDatos[7] == NULL || $arrayDatos[7] == ''){
            $sql = "
                    SET NOCOUNT ON;

                    DECLARE @IdProyectoMaterial INT;

                    EXEC dbo.up_ProyectosMateriales_Insert
                        @IdProyectoMaterial = @IdProyectoMaterial OUTPUT,
                        @IdProyecto = ?,
                        @IdProyectoTarea = ?,
                        @IdProyectoMaterialTipo = ?,
                        @IdArticulo = ?,
                        @Descripcion = ?,
                        @Cantidad = ?,
                        @Coste = ?,
                        @Fecha = ?,
                        @IdPersonal = ?,
                        @IdIdentidad = ?;

                    SELECT @IdProyectoMaterial AS IdProyectoMaterial;
                ";

                $parm = array(
                    $_SESSION["Controlador"]->miEstado->IdPropietario,   // @IdProyecto
                    $arrayDatos[0],                                      // @IdProyectoTarea
                    $arrayDatos[1],                                      // @IdProyectoMaterialTipo
                    $arrayDatos[2],                                      // @IdArticulo
                    $arrayDatos[3],                                      // @Descripcion
                    ConvertirAFloat($arrayDatos[4]),                     // @Cantidad
                    ConvertirAFloat($arrayDatos[5]),                     // @Coste
                    date('Ymd H:i:s', strtotime($arrayDatos[7])),        // @Fecha
                    $_SESSION["Controlador"]->miEstado->IdPersonal,      // @IdPersonal
                    $_SESSION["Controlador"]->miEstado->IdIdentidad      // @IdIdentidad
                );
        }else{

            $sql = "DECLARE @FECHA SMALLDATETIME = GETDATE()
                EXECUTE up_ProyectosMateriales_Update_From_PE
                @IdProyectoMaterial  = ?,
                @IdProyecto = ?,
                @IdProyectoTarea = ?,
                @IdProyectoMaterialTipo = ?, 
                @IdArticulo = ?, 
                @Descripcion = ?, 
                @Cantidad  = ?,
                @Coste = ?,
                @Fecha = ?,
                @IdPersonal = ?,
                @IdIdentidad = ?";


           $parm = array(
            $arrayDatos[7],
            $_SESSION["Controlador"] -> miEstado -> IdPropietario,
            $arrayDatos[0],
            $arrayDatos[1],
            $arrayDatos[2],
            $arrayDatos[3],
            ConvertirAFloat($arrayDatos[4]),
            ConvertirAFloat($arrayDatos[5]),
            date('Ymd H:i:s', strtotime($arrayDatos[6]) ),
            $_SESSION["Controlador"] -> miEstado -> IdPersonal,
            $_SESSION["Controlador"] -> miEstado -> IdIdentidad);
 
        }
    }
    
    $stmt = sqlsrv_query($conn, $sql, $parm);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Ya no avanzamos al siguiente resultado porque el SELECT está en el primer resultado después del EXEC.
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row && isset($row['IdProyectoMaterial'])) {
        return $row['IdProyectoMaterial'];
    } else {
        return false;  // o true, según prefieras para indicar fallo
    }

}

function comprobarBD($c){
    $conn = ConexionBD("85.214.41.17,23459","IntecoDistribucion","sa","Iiaslgv52d");
    //sacar el primero en caso de que coincidan
    $sql = "SELECT TOP 1 CA.BBDD,ISNULL(ca.ServidorExterno,CA.Servidor) AS Servidor ,ISNULL(CA.PuertoExterno,CA.Puerto) AS Puerto FROM Clientes C 
    INNER JOIN dbo.Clientes_Aplicaciones CA ON CA.IdCliente = C.IdCliente WHERE Codigo LIKE ?;";
    $DatosBD = array();
    $parm = array($c);
    //$parm = array($IdCliente);
    $stmt = sqlsrv_query($conn,$sql,$parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            array_push($DatosBD, $row);
        }
        return $DatosBD;
    }
    sqlsrv_close( $conn );
}





// function comprobarDatosCliente($Ip,$bd,$NumeroCliente,$cif,$email){
//     $conn = ConexionBD($Ip,$bd);
//     $sql = "SELECT TOP 1 idCliente,SUI.IdIdentidad AS IdIdentidad ,SUI.Usuario  FROM dbo.Clientes C
//     LEFT JOIN dbo.SeguridadUnificada_Identidad SUI ON C.idCliente = SUI.IdUsuario AND SUI.IdTipoUsuario = 3
//     WHERE Numero = ? AND cif = ? ";
//     $parm = array($NumeroCliente,$cif,$email);
//     $stmt = sqlsrv_query($conn,$sql,$parm);
//     if ($stmt === false) {
//         die(print_r(sqlsrv_errors(), true));
//     }else{
//         $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
//         if($row){
//             return $row;
//         }else{
//             return false;
//         }   
        
//     }
//     sqlsrv_close( $conn );
// }
//$Ip,$bd,$NumeroCliente,$Email,$Contrasena,$Ndoc = null, $tipoUsr = null
function execSeguridadUnificada_Identidad_Insert($Ip,$bd,$Email,$Contrasena,$Ndoc = null,$NumeroCliente = null, $tipoUsr = null){
    $conn = ConexionBD($Ip,$bd);
    $sql = "EXECUTE SeguridadUnificada_Usuario_Insert_From_PC @NumeroCliente = ?,
    @Ndoc = ?,
    @usuario = ?,
    @Contrasena = ?,
    @TipoUsu = ?";  
    $DatosBD = array();
    $parm = array($NumeroCliente,$Ndoc,$Email,$Contrasena,$tipoUsr) ;

    $stmt = sqlsrv_query($conn, $sql, $parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    }else{
        while (sqlsrv_next_result($stmt)) {
            do {
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    array_push($DatosBD, $row);
                }
            } while (sqlsrv_next_result($stmt));
        }
        if(isset($DatosBD[0]['IdIdentidad'])){
            return $DatosBD[0]['IdIdentidad'];
        }else{
            return 0;
        }
        
    }
    sqlsrv_close( $conn );

}

function confirmarUsuarioSeguridadUnificada($Ip,$bd,$IdIdent,$usu){
    $conn = ConexionBD($Ip,$bd);
    $sql = "UPDATE dbo.SeguridadUnificada_Identidad 
    SET Activo = 1 
    WHERE IdIdentidad = ? AND Usuario = ?";
    $parm = array($IdIdent,$usu);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    }else{
        return 1;
    }   
    sqlsrv_close( $conn );
}
function solicitudCambioDatosInsert($datos = array()){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = "INSERT INTO dbo.PE_DatosPersonalesModificar
(
    IdAppsheetDatosPersonal_Modificaciones,
    TelefonoParticular,
    Direccion,
    Poblacion,
    CodigoPostal,
    NombreCuentaBancaria,
    IBAN,
    BIC,
    TallaCamisa,
    TallaPantalon,
    TallaZapatos,
    IdPersonal,
    FechaInsert,
    IdIdentidad
)
VALUES
( 
    ?,
    ?,
    ?, 
    ?,
    ?, 
    ?,
    ?, 
    ?, 
    ?, 
    ?, 
    ?, 
    ?, 
    GETDATE(), 
    ?  
    )";
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $claveGenerada = '';

    for ($i = 0; $i < 9; $i++) {
        $claveGenerada .= $caracteres[rand(0, strlen($caracteres) - 1)];
    }
    $claveGenerada;
    $parm = $datos;
    array_unshift($parm,$claveGenerada);
    array_push($parm, $_SESSION["Controlador"] -> miEstado -> IdPersonal,$_SESSION["Controlador"] -> miEstado -> IdIdentidad);
    // print_r($parm );
    // print_r($sql);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    }else{
        return 1;
    }   


    
    
}

function comprobarUsuarioSURecuperacionContrasena($Ip,$bd,$email){
    $link = '';
    $msg = '';
    $row;
    $tipousu;
    $conn = ConexionBD($Ip,$bd);
    $sql = "SELECT IdIdentidad FROM dbo.SeguridadUnificada_Identidad 
    WHERE Usuario = ?";


    $parm = array($email);
    $stmt = sqlsrv_query($conn,$sql,$parm);
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }else{
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    }
    if(isset($row) && $row != null && $row != ''){
        if($_COOKIE['TipoPortalE'] = 1){
            $link = 'https://www.areadecliente.de/php/reestablecerContrasenaCompletar.php?a='.$row['IdIdentidad'].'&b='.$_COOKIE['EmpresaPD'].'&c='.$_COOKIE['TipoPortalE'];
            $msg = 'Reestablecimiento contraseña del Area de cliente de '.$_SESSION["NombreM"].'.';
        }elseif($_COOKIE['TipoPortalE'] = 2){
            $link = 'https://www.tuportal.de/empleado/php/reestablecerContrasenaCompletar.php?a='.$row['IdIdentidad'].'&b='.$_COOKIE['EmpresaPD'].'&c='.$_COOKIE['TipoPortalE'];
            $msg = 'Reestablecimiento contraseña del Portal del empleado de '.$_SESSION["NombreM"].'.';
        }
            if($_COOKIE['pinCPortalE'] == 123654){
                $link = 'http://localhost/portaldecliente/php/reestablecerContrasenaCompletar.php?a='.$row['IdIdentidad'].'&b='.$_COOKIE['EmpresaPD'].'&c='.$_COOKIE['TipoPortalE'];
            }
            //$link = 'localhost/portaldecliente/php/ActivadorUsuariosCliente.php?a='.$UsuarioCreado.'&c='.$Ip.'&d='. $datosBBDD[0]["BBDD"].'&b='.$arratLogin[2];
           // echo $link;
            $plantillaHtml = '../html/plantillas/plantilla_mailRecuperacion.html';
            $file = fopen($plantillaHtml, "r");
            $filesize = filesize($plantillaHtml);
            $plantilla = fread($file, $filesize);
            $htmlCorreo = str_replace(["%Usuario%","%LinkActivacionPortal%"],[$email,$link],$plantilla);
        
        if(execute_EnviarMail_CreacionUsuario($email,$htmlCorreo,$msg)){
            return true;
        }
    }

    sqlsrv_close( $conn );
}


function crearRegistroEntrada_SeguridadUnificada($idIdentidad,$idApp){
    $conn = ConexionBD($_SESSION["Controlador"] -> miEstado -> IP, $_SESSION["Controlador"] -> miEstado -> bbdd);
    $sql = "DECLARE @IdControlAcceso INT;
            EXECUTE dbo.SeguridadUnificada_ControlAcceso_Insert 
            @IdControlAcceso = @IdControlAcceso OUTPUT,
            @IdIdentidad = ?,                          
            @IdAplicacion = ?,                          
            @Ip = '' "  ;
    $parm = array($idIdentidad,$idApp);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    }


    
    sqlsrv_close( $conn );
}

function execute_EnviarMail_CreacionUsuario($destinatario,$html,$Asunto = 'Activación usuario Area de cliente'){
    $conn = ConexionBD("85.214.41.17,23459","IntecoDistribucion","sa","Iiaslgv52d");
    $sql = "EXEC msdb.dbo.sp_send_dbmail
    @profile_name = 'AreaClienteNimo',
    @recipients = ?,
    @body = ?,
    @subject = ?,
    @body_format = 'HTML',
    @from_address = 'areadecliente@nimoerp.com'"  ;
    $parm = array($destinatario,$html,$Asunto);
    $stmt = sqlsrv_prepare($conn, $sql,$parm);
    
    if (!sqlsrv_execute($stmt)) {
        $errors = sqlsrv_errors();
        if($errors[0]['SQLSTATE'] == '01000'){
            return true;
        }else{
            return false;
        }
    }else{
        return true;
    }
    sqlsrv_close( $conn );
}
    
function PEfirmaInsert($IdDoc,$IdTipoDoc,$NArchivo,$IdA,$LinkA,$IP,$BBDD){
    $conn = ConexionBD($IP,$BBDD);
    $sql = " INSERT INTO dbo.PEFirma
    (
        IdDocumento,
        IdTipoDocumento,
        Archivo,
        IdArchivo,
        LinkArchivo
    )
    VALUES
    (   ?,
        ?,
        ?,
        ?,
        ? 
        )";
    $parm = array($IdDoc,$IdTipoDoc,$NArchivo,$IdA,$LinkA);
    $stmt = sqlsrv_prepare($conn, $sql, $parm);
    if (!sqlsrv_execute($stmt)) {
        die(print_r(sqlsrv_errors(), true));
        return false;
        die;
    }else{
        return true;
    }   
    sqlsrv_close( $conn );
}

function ConvertirAFloat($val) {
    return is_numeric($val) ? (float)$val : null;
}

?>

