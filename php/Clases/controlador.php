<?php
require_once 'estado.php';
require 'pinta.php';
require_once './consultas.php';
require_once './recursosVarios.php';

class Controlador
{
    public $miEstado;
    //valor por defecto en caso de no recibir
    function __construct($Estado = null){
        $this -> obtenerDatosInicio();
        $_SESSION["pinC"] = $_COOKIE['pinCPortalE'];

        if(isset($_COOKIE['LogoUsuarioPortalE'])){
            $_SESSION["imgLogo"] = $_COOKIE['LogoUsuarioPortalE'];
        }
            
        
        $_SESSION["TipoPortal"] = $_COOKIE['TipoPortalE'];
        $this -> miEstado = new Estado();
        $this -> miEstado -> Estado = 0;
        $this -> miEstado -> Documentos = array();
        $this -> miEstado -> FiltrosDoc = array();
        $this -> miEstado -> acciones = array("archivos"=> 0,
                                        "observaciones" => 0,
                                        "añadir" => 0);
        $this -> miEstado -> tipo_App = $_SESSION["TipoPortal"];
        // if(isset($_SESSION["header"])){
        //     $this -> miEstado -> header = $_SESSION["header"];
        //     $_SESSION["header"] = null;
        // }else{
        //     $header_Empresa = '../html/header.html';
        //     $header = fopen($header_Empresa, "r");
        //     $this -> miEstado -> header = fread($header,filesize($header_Empresa));
        //     fclose($header);
        // }
        
        //$this -> miEstado -> Estado = $IP;
    }
    //al final del script iguala la variable de Sesion al estado
    function __destruct(){
        $_SESSION["Controlador"] = $this;
    }
    function obtenerDatosInicio() {
    if (!isset($_COOKIE['EmpresaPD'])) {
        error_log("Cookie 'EmpresaPD' no está definida.");
        return;
    }

    $dirEmpresa = './../' . $_COOKIE['EmpresaPD'];
    $rutaJson = $dirEmpresa . "/datosInicio.json";

    if (!file_exists($rutaJson)) {
        error_log("No se encuentra el archivo datosInicio.json en: " . $rutaJson);
        return;
    }

    $datosRaw = file_get_contents($rutaJson);
    $datosConn = json_decode($datosRaw, true);

    if (!is_array($datosConn)) {
        error_log("Error al decodificar JSON desde: " . $rutaJson);
        return;
    }

    // Asignación segura a variables de sesión
    $_SESSION["NombreM"] = $datosConn["NombreM"] ?? 'Nombre por defecto';

    if (!empty($datosConn["imgLogo"])) {
        $_SESSION["imgLogo"] = $datosConn["imgLogo"];
    }

    $_SESSION["TipoPortal"] = $datosConn["TipoPortal"] ?? 'Portal';

    // Leer header si existe
    $rutaHeader = $dirEmpresa . '/html/header.html';
    if (file_exists($rutaHeader)) {
        $_SESSION['headerCliente'] = file_get_contents($rutaHeader);
    }

    // Leer footer si existe
    $rutaFooter = $dirEmpresa . '/html/footer.html';
    if (file_exists($rutaFooter)) {
        $_SESSION['footerCliente'] = file_get_contents($rutaFooter);
    }

    
}

    
    function almacenarDocumentos($doc){
        $this -> miEstado["documentos"] = $doc;
    }


    function almacenarDatosSociedad($ls,$ids){
        if($ls!="" && $ls!= null){
            $this -> miEstado["lista_sociedades"] = $ls;
        }
        if($ids!="" && $ids!= null){
            $this -> miEstado["id_sociedad"] = $ids;
        }
    }
    
    //funciones de sql
    function comprobarBBDD($c){
        $datosBBDD = comprobarBD($c);
        if(!isset($datosBBDD[0]["Puerto"])){
            $this -> miEstado -> IP = $datosBBDD[0]["Servidor"];
        }else{
            $this -> miEstado -> IP = $datosBBDD[0]["Servidor"].','.$datosBBDD[0]["Puerto"];
        }
        //pruebas
        if($_SESSION["pinC"] == 123654){
                $this -> miEstado -> IP = '192.168.204.230';
        }elseif ($_SESSION["pinC"] == '65814415D75C') {
            $this -> miEstado -> IP = '85.215.231.65,23459';
        }
        $this -> miEstado -> bbdd = $datosBBDD[0]["BBDD"];
    }
        
    function IniciarSesion($usr, $pass){
        //si no encuentra usuario devuelve 0
        $datosSesion = comprueba_usuario($usr, $pass);
        $IdTipoApp;
        
        if($datosSesion != false && $datosSesion != 0 ){
            $this -> miEstado -> nombre_descriptivo = $datosSesion[2];
            $this -> miEstado -> IdIdentidad = $datosSesion[4];
            $this -> miEstado -> permisosSecciones = comprobarPermisosUsuarios();
            $this -> miEstado -> configuracionesUsuario = comprobarConfiguracionesAdicionalesUsuarios();
            if($this -> miEstado -> tipo_App == 1){
                //PORTAL DEL CLIENTE: Añadir variables de sesion al comprobar usuario
                    $this -> miEstado -> IdCliente = $datosSesion[0];
                    $this -> miEstado -> nombre_cliente = $datosSesion[1];
                    $this -> miEstado -> lista_sociedades = compruebaSociedades($this -> miEstado -> IdCliente);
                    $this -> miEstado -> PersonasContacto = compruebaPersonasContaco();
                    $this -> miEstado -> archivostiposAccesos = extraerArchivosTiposAccesos();
                    $IdTipoApp = 32;
            }elseif($this -> miEstado -> tipo_App == 2){
                //PORTAL DEL EMPLEADO: Añadir variables de sesion al comprobar usuario
                $this -> miEstado -> IdPersonal = $datosSesion[0];
                $this -> miEstado -> lista_sociedades = compruebaSociedades($this -> miEstado -> IdPersonal);
                $this -> miEstado -> EstadoJornada = comprueba_jornada_personal();
                $this -> miEstado -> Documentos = extraerDocPersonal_Masivo();
                $this -> miEstado -> archivostiposAccesos = extraerArchivosTiposAccesos();
                $this -> miEstado -> linkDocumentoSubido = null;
                $IdTipoApp = 33;
                if( in_array(74,$this -> miEstado -> configuracionesUsuario) ){
                    $this -> miEstado -> IdPersonalAdmin = $datosSesion[0];
                    $this -> miEstado -> ListaEmpleadosVerAdministrador = obtenerListaEmpleados();
                }
                $this -> cargarDatosForm();
            }
            
            crearRegistroEntrada_SeguridadUnificada($this -> miEstado -> IdIdentidad,$IdTipoApp);
            return true;
        }elseif($datosSesion == 0){
            return 0;
        }else{
            return false;
        }
        
    }

    function comprobarVersion(){
        return compararVersionBBDD();
    }

    //llama a la funcion que extrae los documentos y llena la clase estado con los mismos
    function cargaDocumentos_Parcial($tipoDoc = null){
        //optimizar para evitar conexión
        $doc = null;
        $filtros  = null;
        $fecha = date('Y-m-d H:i:s');
        $primeraEjecucion = false;
        $this -> miEstado -> puntero_posicion = 30;
        
            switch ($tipoDoc) {
                case 1 :
                    if(empty($this -> miEstado -> FechaPedidos)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaPedidos = $fecha;
                    break;
                case 2 :
                    if(empty($this -> miEstado -> FechaAlbaranes)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaAlbaranes = $fecha;
                    break;
                case 3 :
                    if(empty($this -> miEstado -> FechaFacturas)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaFacturas = $fecha;
                    break;
                case 4 :
                    if(empty($this -> miEstado -> FechaProyectos)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaProyectos = $fecha;
                    break;
                case 5:
                    if(empty($this -> miEstado -> FechaTareas)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaTareas = $fecha;
                    break;
                default:
                    if(empty($this -> miEstado -> FechaPresupuestos)){
                        $primeraEjecucion = true;
                    }
                    $this -> miEstado -> FechaPresupuestos = $fecha;
                    break;
            }
        if($primeraEjecucion){
            $doc = extraerDoc_parcial($tipoDoc, $this -> miEstado -> id_sociedad , $this -> miEstado -> IdCliente);
            $filtros = extraerFiltroDoc_Parcial($tipoDoc, $this -> miEstado -> id_sociedad , $this -> miEstado -> IdCliente);
        }else{
            $doc = extraerDoc_parcial($tipoDoc, $this -> miEstado -> id_sociedad , $this -> miEstado -> IdCliente,$fecha);
        }
        if($doc !== null){
            $this -> miEstado -> Documentos = array_merge($doc, $this -> miEstado -> Documentos );
        }
        if($filtros !== null){
            $this -> miEstado -> FiltrosDoc = array_merge($this -> miEstado -> FiltrosDoc, $filtros);
        }
    }
    //colocar sociedad
    function setSociedad($idS,$nS = null){
        $this -> miEstado -> id_sociedad = $idS;
        $ns = "";
        foreach ($_SESSION["Controlador"] -> miEstado -> lista_sociedades as $valor) {
            if($valor["idSociedad"] == $idS){
                $ns = $valor["NombreFiscal"];
            }
        }
        $this -> miEstado -> NombreSociedad = $ns; 
    }
    // iniciar o finalizar la jornada segun el estadp 
    function iniciarFinalizarJornada($lat = null,$long = null, $horaLocal = null ){
        exec_up_Tiempos_Insert($lat,$long,$horaLocal);
        if($this -> miEstado -> EstadoJornada[0] == 1){
            $this -> miEstado -> EstadoJornada[0] = 0;
        }else{
            $this -> miEstado -> EstadoJornada[0] = 1;
        }
        $this -> miEstado -> EstadoJornada[1] = date('Y-m-d H:i');
    }  
    //Navegación entre pestañas
    function navegarPestanas($ps){
        //volver a la aterior
        if($ps == -1){
            //salir del modo formulario
            
            if($this -> miEstado -> cargarForm == 1){
                $this -> miEstado -> cargarForm = 0;
            }else{
                $estadoAnterior = array_shift($this -> miEstado -> EstadosAnteriores);
                $this -> miEstado -> Estado = $estadoAnterior;
            }
            //reinicializar variables
            
            $this -> miEstado -> nombreDocumentoPadre = null;
            $this -> miEstado -> IdPropietario = null; 
        }else{
            array_unshift($this -> miEstado -> EstadosAnteriores , $this -> miEstado -> Estado);
            $this -> miEstado -> Estado = $ps;
        }
        $this -> miEstado -> CadenaFiltro = null;
        $this -> miEstado -> IdsTiposFiltro = array();
        $this -> miEstado -> adjuntarDocumentoFormAutomatico = 0;
        $this -> cargarPermisosAcciones();
    }

    function cerrarSesion(){
    //Cerrar sesion reinicializando variables
        $this -> miEstado = new Estado();
        $this -> miEstado -> Estado = 0;
        $this -> miEstado -> Documentos = array();
        $this -> miEstado -> FiltrosDoc = array();
        $this -> miEstado -> tipo_App = $_SESSION["TipoPortal"];
        $this -> miEstado -> acciones = array("archivos"=> 0,
                                        "observaciones" => 0,
                                        "añadir" => 0);
        if(isset($_SESSION["header"])){
            $this -> miEstado -> header = $_SESSION["header"];
            $_SESSION["header"] = null;
        }else{
            $header_Empresa = '../html/header.html';
            $header = fopen($header_Empresa, "r");
            $this -> miEstado -> header = fread($header,filesize($header_Empresa));
            fclose($header);
        }
        
    }

    function recargarDatosAdministrador(){
        $nombreSelccionado = array_values(array_filter($this -> miEstado -> ListaEmpleadosVerAdministrador, function ($usu) {
            return $usu["Id"] == $this -> miEstado -> IdPersonal;
        }));
        $this -> miEstado -> nombre_descriptivo = $nombreSelccionado[0]["Nombre"] ;
        
        $this -> miEstado -> lista_sociedades = compruebaSociedades($this -> miEstado -> IdPersonal);
        $this -> miEstado -> EstadoJornada = comprueba_jornada_personal();
        $this -> miEstado -> Documentos = extraerDocPersonal_Masivo();
        $this -> miEstado -> linkDocumentoSubido = null;
        $this -> cargarDatosForm();
    }


    function cargarDatosForm(){
        //generar los formularios dinamicamente
        if(!isset($this -> miEstado -> formularios) || $this -> miEstado -> formularios == null){
            $json = '../php/documentos/Formularios_PE.json';
            $jsonData = file_get_contents($json);
            $formularios = json_decode($jsonData, true);
            $this -> miEstado -> formularios = $formularios['Pantallas'];
            $this -> miEstado -> dropdownsFormularios = extraerDropdownsFormsValores();
        }       
    }

    function cargarPermisosAcciones(){
        //Resetear el acceso a las acciones
        $this -> miEstado -> acciones["archivos"] = 0;
        $this -> miEstado -> acciones["adjunto"] = 0;
        $this -> miEstado -> acciones["descarga"] = 0;
        $this -> miEstado -> acciones["anadirLinea"] = 0;
        $this -> miEstado -> acciones["modalValidaciones"] = 0;
        $this -> miEstado -> acciones["modalSubirDoc"] = 0;
        $this -> miEstado -> acciones["desplegado"] = 0;
        $this -> miEstado -> acciones["SinDesplegable"] = 0;
        $this -> miEstado -> acciones["modalVisualizar"] = 0;
        $this -> miEstado -> acciones["VisualizarDetalles"] = 0;
        $this -> miEstado -> acciones["modificar"] = 0;
        $this -> miEstado -> acciones["modalVisualizarLineaCustom"] = 0;
        $this -> miEstado -> acciones["anadirLineaCustom"] = 0;
        $this -> miEstado -> acciones["accionBuscarMenu"] = 0;
        $this -> miEstado -> acciones["observaciones"] = 0;
        $this -> miEstado -> acciones["ModificarLineaCustom"] = 0;
        //*******************/
                //Acciones personalizadas de cada formulario//
        //******************/ 
        $this -> miEstado -> acciones["accionCustom1"] = 0;
        if($this -> miEstado -> tipo_App == 2){
            //Portal del empleado
            switch ($this -> miEstado -> Estado) {   
                case 4.1 :
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["desplegado"] = 1;
                    $this -> miEstado -> acciones["observaciones"] = 1;
                    $this -> miEstado -> acciones["ModificarLineaCustom"] = 1;
                    
                    //$this -> miEstado -> acciones["accionBuscarMenu"] = 1;
                    break;
                case 4.3 :
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["observaciones"] = 1;
                    //$this -> miEstado -> acciones["accionBuscarMenu"] = 1;
                    break;
                case 4.4 :
                    if(!in_array($this -> miEstado -> IdTipoPropietario,array(24,25))){
                        $this -> miEstado -> acciones["anadirLinea"] = 1;
                    } 
                    
                    //$this -> miEstado -> acciones["modalVisualizar"] = 1;
                    break;
                case 4.45 :
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                
                    //$this -> miEstado -> acciones["modalVisualizar"] = 1;
                    break;
                case 4.5:
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["modalVisualizar"] = 1;
                    $this -> miEstado -> acciones["observaciones"] = 1;
                    //$this -> miEstado -> acciones["accionBuscarMenu"] = 1;
                    break;
                case 4.6:
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["modalVisualizar"] = 1;
                    $this -> miEstado -> acciones["observaciones"] = 1;
                    //$this -> miEstado -> acciones["accionBuscarMenu"] = 1;
                    break;
                case 4.7:
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["observaciones"] = 1;
                    //$this -> miEstado -> acciones["modalVisualizar"] = 1;
                    //$this -> miEstado -> acciones["accionBuscarMenu"] = 1;
                    break;
                case 4.8:
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["observaciones"] = 1;
                    //$this -> miEstado -> acciones["accionBuscarMenu"] = 1;
                    break;
                case 4.9:
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["desplegado"] = 1;
                    $this -> miEstado -> acciones["modalVisualizar"] = 1;
                    $this -> miEstado -> acciones["accionBuscarMenu"] = 1;
                    break;
                case 6:
                    $this -> miEstado -> acciones["desplegado"] = 1;
                    $this -> miEstado -> acciones["accionCustom1"] = 1;
                    $this -> miEstado -> acciones["modalVisualizarLineaCustom"] = 1;
                    $this -> miEstado -> acciones["accionBuscarMenu"] = 1;
                    break;
                case 6.1:
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["anadirLineaCustom"] = 1;
                    $this -> miEstado -> acciones["ModificarLineaCustom"] = 1;
                    break;
                case 6.2:
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["anadirLineaCustom"] = 1;
                    $this -> miEstado -> acciones["ModificarLineaCustom"] = 1;
                    break;
                case 7:
                    $this -> miEstado -> acciones["accionBuscarMenu"] = 1;
                    
                    break;
                default:
                    $this -> miEstado -> IdTipoPropietario = null;
                    break;
            }
        }else{
            //Portal del comercial
            switch ($this -> miEstado -> Estado) { 
                case 6 :
                    $this -> miEstado -> acciones["descarga"] = 1;
                    $this -> miEstado -> acciones["desplegado"] = 1;
                    break;
                case 7 :
                    $this -> miEstado -> acciones["VisualizarDetalles"] = 1;
                    break;
                case 8 :
                    $this -> miEstado -> acciones["modalValidaciones"] = 1;
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["archivos"] = 1;
                    $this -> miEstado -> acciones["modalVisualizar"] = 1;
                    $this -> miEstado -> IdTipoPropietario = null;
                    break;
                case 9 :
                    $this -> miEstado -> acciones["anadirLinea"] = 1;
                    $this -> miEstado -> acciones["modalSubirDoc"] = 1;
                    $this -> miEstado -> acciones["adjunto"] = 1;
                    $this -> miEstado -> IdTipoPropietario = 2;
                    break;
                default:
                    $this -> miEstado -> acciones["descarga"] = 1;
                    break;
            }
        }

    }

    //Subir los archivos mediante llamada al servicio web
    function subirArchivosServicioWeb($pin,$IdtipoPropietario,$idPropietario,$idArchivoTipo,$url,$nombre_archivo){
        $url2 = "http://onixsw.esquio.es:8080/Funciones.aspx?SubirArchivo=1&pin=".$pin.
        "&IdTipoPropietario=".$IdtipoPropietario.'&IdPropietario='.$idPropietario.
        '&IdArchivoTipo='.$idArchivoTipo.'&URL='.urlencode($url).'&NombreArchivo='.$nombre_archivo;
       
        try {
            $response = file_get_contents($url2);
        } catch (\Throwable $th) {
            $platano=1; 
        }
        return true;
    }



    function descargaArchivoServicoWeb($IdP,$nombre_archivo,$tipoDoc = null,$OI = null){
        
        if($tipoDoc == "OPDF"){
            $url_archivo ="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerPDF=1&pin=".$_SESSION["pinC"].'&IdOrigenImpresion='.$OI.'&IdPropietario='.$IdP;
        }else{
            $url_archivo ="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerArchivo=1&pin=".$_SESSION["pinC"].'&IdArchivo='.$IdP;
            
            // print_r($url_archivo);
            // print_r('<br>');
        }
       
        
        $directorio_destino = "subidasTemp/".$_SESSION["pinC"]."/";
        // Verificar si el directorio existe, si no, crearlo
        if (!file_exists($directorio_destino)) {
            mkdir($directorio_destino, 0755, true);
        }

        //Realiza la solicitud HTTP para obtener el contenido del archivo
        $response = file_get_contents($url_archivo);
        if ($response === false) {
            return false;       
        } else {
            // Específica la ruta donde deseas guardar el archivo
            $nombre_archivo = str_replace('/','_',$nombre_archivo);
            $rutaGuardado = $directorio_destino.$nombre_archivo; // Reemplaza esto con la ruta y nombre deseado
        
            // Guarda el contenido en un archivo en el servidor
            if (file_put_contents($rutaGuardado, $response) !== false) {
                //$abrirNuebaPestaña = 1;
                return array('php/'.$rutaGuardado,1,1);
            } else {
                return 0;
            }
        }
    }


    function calcularVacacionesGraf($añoS){
        $arrayAuxiliarHtml2 = array();
        foreach($this -> miEstado -> Documentos as $documento){
            if($documento["tipoDocPortal"] == 8.5 && $documento['Año'] == $añoS){
                array_push($arrayAuxiliarHtml2,array('Año' => $documento['Año'],
                'DiasTotales' => $documento['DiasTotales'],
                'DiasDisfrutados' => $documento['DiasDisfrutados'],
                'DiasConcedidos' => $documento['DiasConcedidos'],
                'DiasPendientes' => $documento['DiasPendientes']));
            }
        }         
        if(empty($arrayAuxiliarHtml2)){
            array_push($arrayAuxiliarHtml2,array('Año' => $documento['Año'],
                'DiasTotales' => 0,
                'DiasDisfrutados' => 0,
                'DiasConcedidos' => 0,
                'DiasPendientes' => 0));
        }
        return $arrayAuxiliarHtml2;      
    }

    //funciones de para generar el contenido
    // optimizar el navegar pestañas
    //$arrayDatos([0]Pestaña a la que navegar,
                    // [1]Accion a realizar,
                    // [2]Datos adicionales, por ejemplo inputs de un form,
                    // )
    function generarContenido($arrayDatos = array()){
        $arrayAuxiliarHtml = array();
        $accionJs = null;
        $msgError = "" ;
        $AccionSinRepintar = 0;
        $c = $this -> miEstado -> Estado;   
        
        $this -> comprobarBBDD($_SESSION["pinC"]);
        $nav = "";
        if($c === 0 && !empty($arrayDatos) && $arrayDatos[0] != -1 && ($this -> miEstado -> tipo_App == 1 || $this -> miEstado -> tipo_App == 2)){
        //********************************************/
        //PORTAL CLIENTE/EMPLEADO
        //inicia sesión y navega entre pestañas del portal del comercial
        //********************************************/
        $nav = 0;
                $InicioS = $this -> IniciarSesion($arrayDatos[0], $arrayDatos[1]);
                if($InicioS === false){
                    $msgError = "No hay conexión con el servidor de " . $_SESSION["NombreM"] . ".<br>Contacte con su administrador del sistema.";

                }elseif($InicioS == 0){
                    
                    $msgError = "Usuario o contraseña Incorrectos" ;

                }else{
                    if($this -> miEstado -> tipo_App == 2){
                        $this -> miEstado -> datosPersonal = comprobarDatosPersonal();
                        
                    }
                    //$programaActualizado = $this -> comprobarVersion();
                    $programaActualizado = true;
                    if(count($this -> miEstado -> lista_sociedades) == 1 && $InicioS && $programaActualizado){
                        $nav = 2;
                        $this -> setSociedad($this -> miEstado -> lista_sociedades[0]["idSociedad"]);
                    }elseif($InicioS && $this -> miEstado -> tipo_App == 1 && $programaActualizado){
                        // LISTA SOCIEDADES
                        $nav = 1;
                    }elseif($programaActualizado == false){
                        $msgError = 'Es necesario actualizar el programa.';
                    }
                }
                $this -> navegarPestanas($nav);
        }elseif($this -> miEstado -> tipo_App == 1.5 && ($c === 0 ||($c === 0.5 && empty($arrayDatos))) ){
        //********************************************/    
        //PORTAL CLIENTE DESCARGAR DOC MEDIANTE CIF
        //********************************************/
            $nav = 0.5;
            $this -> navegarPestanas($nav);
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 10){
        //********************************************/
        //PORTAL CLIENTE/EMPLEADO
        //Abrir la pestaña de cambio de contraseña
        //********************************************/    
            $this -> navegarPestanas($arrayDatos[0]); 
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 10.5){
        //********************************************/
        //PORTAL CLIENTE/EMPLEADO
        //Abrir la pestaña de cambio de contraseña
        //********************************************/    
            $this -> navegarPestanas($arrayDatos[0]);
        }elseif(!empty($arrayDatos) && $this -> miEstado -> Estado == 11){
        //********************************************/
        //PORTAL CLIENTE/EMPLEADO
        //Solicitar el reestablecimiento de contraseña
        //********************************************/
            if(strlen($arrayDatos[0])<5){
                $msgError = 'La nueva contraseña ha de tener una longitud mínima de 5 caracteres.';
            }elseif($arrayDatos[0] === $arrayDatos[1]){
                if(reestablecerContrasenaConfirmar($arrayDatos[0])){ 
                    $msgError = 'Contraseña modificada con exito.';
                    if($_SESSION["pinC"] == 123654){
                        $this -> cerrarSesion();
                        return array('../portaldecliente/Index.html',0,1,$msgError);
                    }else{
                        $this -> cerrarSesion();
                        return array('../Index.html',0,1,$msgError);
                    }
                        
                    
                }else{
                    $msgError = 'No se ha podido modificar la contraseña.';
                }

            }else{
                $msgError = 'La contraseña nueva ha de coincidir';
            }
             
        }elseif(!empty($arrayDatos) && $c == 10 && $arrayDatos[0] != -1){
        //********************************************/
        //PORTAL CLIENTE/EMPLEADO
        //confirmar el cambio de contraseña
        //********************************************/
            if(strlen($arrayDatos[2])<5){
                $msgError = 'La nueva contraseña ha de tener una longitud mínima de 5 caracteres.';
            }elseif($arrayDatos[2] === $arrayDatos[1]){
                if(cambiarContrasena($arrayDatos[0],$arrayDatos[1])){
                    $msgError = 'Contraseña modificada con exito.';
                }else{
                    $msgError = 'No se ha podido modificar la contraseña.';
                }

            }else{
                $msgError = 'La contraseña nueva ha de coincidir';
            }

        }elseif($c === 1 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 1){
        //********************************************************************************************//
        //PORTAL CLIENTE //
        //coloca los datos de la sociedad seleccionada y navega a la siguiente pestaña//
        //********************************************************************************************//
            $this -> setSociedad($arrayDatos[0]);
            $this -> navegarPestanas(2);
        }elseif ($c === 2 && !empty($arrayDatos) && $arrayDatos[0] != -1  && $this -> miEstado -> tipo_App == 1) {
        //********************************************/
        //PORTAL CLIENTE la navegación va en funcion del tipo de documento 
        //********************************************/
            $nav = null;
            $this -> cargaDocumentos_Parcial($arrayDatos[0]);
            //$msgError = "entre";
            switch ($arrayDatos[0]) {
                case 1 :
                    $nav = 4;
                    break;
                case 2 :
                    $nav = 5;
                    break;
                case 3 :
                    $nav = 6;
                    break;
                case 4:
                    $nav = 7;
                    break;
                case 5:
                    $nav = 8;
                    break;
                default:
                    $nav = 3;
                    break;
            }
            $this -> navegarPestanas($nav);
        
        }elseif($c === 8 && !empty($arrayDatos) && $arrayDatos[0] != -1  && $arrayDatos[0] != 0 && $this -> miEstado -> tipo_App == 1) {
        //********************************************/
        //PORTAL CLIENTE Navegacion a la pestaña de archivos de cada documento 
        //********************************************/
            $this -> miEstado -> IdPropietario = $arrayDatos[1];
            $this -> miEstado -> ArchivosDocumento = extraerArchivosCliente_Documento();
            $this -> navegarPestanas($arrayDatos[0]);
        }elseif($c == 9 && !empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3 && isset($arrayDatos[2]) && $this -> miEstado -> tipo_App ==  1){
        //********************************************/
        //PORTAL CLIENTE Subir Archivos 
        //********************************************/
                $subida = $this -> subirArchivosServicioWeb($_SESSION["pinC"],
                                                    2,
                                                    $this -> miEstado -> IdPropietario,
                                                    $arrayDatos[2]['IdAT'],
                                                    $arrayDatos[2]['Archivo'],
                                                    $arrayDatos[2]['Nombre']);
                $this -> miEstado -> ArchivosDocumento = extraerArchivosCliente_Documento();
                $msgError = 'Documento adjuntado con exito.';
        }elseif($c === 7 && !empty($arrayDatos) && $arrayDatos[0] == 7.1  && $arrayDatos[0] != 0 && $this -> miEstado -> tipo_App == 1){
        //********************************************/
        //PORTAL CLIENTE Navegar a la pestaña de detalles del proytecto
        //********************************************/
            $this -> miEstado -> IdPropietario = $arrayDatos[1];
            $this -> miEstado -> datosProyecto = extraerRecursosProyectos($arrayDatos[1]);
            usort($this -> miEstado -> datosProyecto[0], 'compararPorDireccionArbol');
            $this -> navegarPestanas(7.1);
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 13 && in_array(74,$this-> miEstado -> configuracionesUsuario) && $this -> miEstado -> tipo_App == 2){
        //********************************************/
        //PORTAL EMPLEADO Cambiar el usuario con carga de datos en caso de ser superadministrador
        //********************************************/
            $this -> miEstado -> IdPersonal = $arrayDatos[2][0];
            $this -> recargarDatosAdministrador();    
        }elseif ($c === 2 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 2) {
        //********************************************/   
        //PORTAL EMPLEADO la navegación Menu principal 
        //********************************************/   
            $nav = null;
            switch ($arrayDatos[0]) {
                case 1 :
                    $nav = 4;
                    break;
                case 2 :
                    $this -> miEstado -> EstadoJornada = comprueba_jornada_personal();//comprobar el estado actual
                    $this -> miEstado -> HistoricoJornada = extraer_JornadaHistorico();// extraer el historico de la jornada
                    $nav = 5;//Pestaña de jornada
                    break;
                case 3 :
                    if($this -> miEstado -> datosProyectos == null){
                        $this -> miEstado -> datosProyectos = extraerFasesProyectos();
                        $accionJs = 3;
                        foreach ($this -> miEstado -> datosProyectos[0] as $fases) {
                            array_push($arrayAuxiliarHtml,$fases['IdProyectoTarea'],$fases['Descripcion']);
                        }
                        
                        // = [1,2,3,4];
                    }
                    $nav = 6;
                    break;
                case 4 :
                    $nav = 7;
                    
                        foreach($this -> miEstado -> Documentos as $documento){
                            if( !in_array($documento["tipoDocPortal"],array(2,3,4))){
                                $fi;
                                $ff;
                                try{
                                    $fi = ($documento["FechaInicio"] ? $documento["FechaInicio"] -> format('Y-m-d H:i:s') : '1900-01-01 00:00:00');
                                    $ff = ($documento["FechaFin"] ? $documento["FechaFin"] -> format('Y-m-d H:i:s') : '1900-01-01 00:00:00');
                                }catch (e){
                                    
                                }
                                if($ff && $fi){
                                    $dc = array($documento["descripcion"],
                                        $fi,
                                        $ff,
                                        $documento["color"],
                                        $documento["tipoDocPortal"]
                                    );
                                    array_push($arrayAuxiliarHtml,$dc);
            
                                }
                            }

                        }
                        $accionJs = 1;
                    break;
                default:
                    $nav = 3;
                    break;
            }
            
            $this -> miEstado -> puntero_posicion = 30;
            $this -> navegarPestanas($nav);
        }elseif ($c === 4 && !empty($arrayDatos) && $arrayDatos[0] != -1 && $this -> miEstado -> tipo_App == 2) {
        //********************************************/
        //PORTAL EMPLEADO  la navegación Submenu
        //********************************************/
            switch ($arrayDatos[0]) {   
                case 4.1 :
                    $this -> miEstado -> IdTipoPropietario = 143;
                    break;
                case 4.3 :
                    $this -> miEstado -> IdTipoPropietario = 24;
                    break;
                case 4.4 :
                    $this -> miEstado -> IdTipoPropietario = 23;
                    $this -> miEstado -> IdPropietario = $this -> miEstado -> IdPersonal;
                    break;
                case 4.5:
                    $this -> miEstado -> IdTipoPropietario = 144;
                    break;
                case 4.6:
                    $this -> miEstado -> IdTipoPropietario = 142;
                    break;
                case 4.7:
                    $this -> miEstado -> IdTipoPropietario = 145;
                    break;
                case 4.8:
                    $this -> miEstado -> IdTipoPropietario = 25;
                    break;
                case 4.9:
                    //Vacaciones de personal
                    $this -> miEstado -> IdTipoPropietario = 146;
                    $accionJs = 2;
                    $this -> miEstado -> listaAnoFiltroVacaciones = array(date("Y"));
                    foreach($this -> miEstado -> Documentos as $documento){
                        if($documento["tipoDocPortal"] == 8.5){
                            if($documento['Año'] != date("Y")){
                                array_push($this -> miEstado -> listaAnoFiltroVacaciones,$documento['Año']);
                            }
                        }
                    }    
                    $this -> miEstado -> AnioSV = date("Y");
                    $arrayAuxiliarHtml = $this -> calcularVacacionesGraf($this -> miEstado -> AnioSV);  
                    if(empty($arrayAuxiliarHtml)){
                        $arrayAuxiliarHtml = array(0);
                    }             
                    rsort($this -> miEstado -> listaAnoFiltroVacaciones);
                    break;
                default:
                    $this -> miEstado -> IdTipoPropietario = null;
                    break;
            }
           
            $this -> navegarPestanas($arrayDatos[0]);
        }elseif (($c > 4 && $c < 5 && $c != 4.4  || in_array($c,array(6.1,6.2)) ) && !empty($arrayDatos) && $arrayDatos[0] == 4.4 && $this -> miEstado -> tipo_App == 2) {
        //********************************************/
        //PORTAL EMPLEADO al documento Correspondiente
        //********************************************/
            //indicar el propietario
            $this -> miEstado -> IdPropietario = $arrayDatos[1];
            switch ($this -> miEstado -> Estado) {
                case 4.1 :
                    $tipoDocf = 1;
                    break;
                case 4.3 :
                    $tipoDocf = 2;
                    break;
                case 4.4 :
                    $tipoDocf = 3;
                    break;
                case 4.4 :
                    $tipoDocf = 9;
                    break;
                case 4.5:
                    $tipoDocf = 4;
                    break;
                case 4.6:
                    $tipoDocf = 5;
                    break;
                case 4.7:
                    $tipoDocf = 6;
                    break;
                case 4.8:
                    $tipoDocf = 7;
                    break;
                case 4.9:
                    $tipoDocf = 8;
                    break;
                case 6.1:
                    $tipoDocf = 3;
                    $this -> miEstado -> IdTipoPropietario = 147;
                    break;
                case 6.2:
                    $tipoDocf = 3;
                    $this -> miEstado -> IdTipoPropietario = 149;
                    break;
                case 7 :
                    $tipoDocf = 4; 
                    break;
                default:
                    $tipoDocf = 1;
                    break;
            }

            $arrayDoc = array_values(array_filter($this -> miEstado -> Documentos, function ($docF) use($tipoDocf) {
                return $docF["tipoDocPortal"] == $tipoDocf
                && $docF["id"] ==  $this -> miEstado -> IdPropietario;
            }));
            //print_r($arrayDoc[0]['descripcion']);
            $this -> miEstado -> nombreDocumentoPadre = $arrayDoc[0]['descripcion'];
            $this -> navegarPestanas($arrayDatos[0]);
        //}elseif($c == 4.4 && !empty($arrayDatos) && $arrayDatos[1] == 6 && $arrayDatos[2] != null ){
        //********************************************/
        //PORTAL EMPLEADO Navegar a la pestaña de firma desde documentos(DEPRECATED)
        //********************************************/
       //     $this -> miEstado -> IdDocumentoPadre = $arrayDatos[2];
        }elseif (($c > 4 && $c < 5 && $c != 4.45 || in_array($c,array(6.1,6.2)) ) && !empty($arrayDatos) && $arrayDatos[0] == 4.45 && $this -> miEstado -> tipo_App == 2) {
        //********************************************/
        //PORTAL EMPLEADO al Observaciones Correspondiente
        //********************************************/
            //indicar el propietario
            $this -> miEstado -> IdPropietario = $arrayDatos[1];
            switch ($this -> miEstado -> Estado) {
                case 4.1 :
                    $tipoDocf = 1;
                    break;
                case 4.3 :
                    $tipoDocf = 2;
                    break;
                case 4.4 :
                    $tipoDocf = 3;
                    break;
                case 4.5:
                    $tipoDocf = 4;
                    break;
                case 4.6:
                    $tipoDocf = 5;
                    break;
                case 4.7:
                    $tipoDocf = 6;
                    break;
                case 4.8:
                    $tipoDocf = 7;
                    break;
                case 4.9:
                    $tipoDocf = 8;
                    break;
                case 6.1:
                    $tipoDocf = 3;
                    $this -> miEstado -> IdTipoPropietario = 147;
                    break;
                case 6.2:
                    $tipoDocf = 3;
                    $this -> miEstado -> IdTipoPropietario = 149;
                    break;
                case 7 :
                    $tipoDocf = 4; 
                    break;
                default:
                    $tipoDocf = 1;
                    break;
            }

            $arrayDoc = array_values(array_filter($this -> miEstado -> Documentos, function ($docF) use($tipoDocf) {
                return $docF["tipoDocPortal"] == $tipoDocf
                && $docF["id"] ==  $this -> miEstado -> IdPropietario;
            }));
            
            $this -> miEstado -> nombreDocumentoPadre = $arrayDoc[0]['Descripcion'];
            $this -> navegarPestanas($arrayDatos[0]);
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == -1 && $arrayDatos[1] == 0 ){
        //********************************************/
        //PORTAL CLIENTE/EMPLEADO
        //Volver a la anterior pestaña
        //********************************************/
            $this -> navegarPestanas(-1);
        }elseif($c == 6  && ($arrayDatos[0] == 6.1 || $arrayDatos[0] == 6.2) && $this -> miEstado -> tipo_App == 2 ){
        //********************************************/
        //PORTAL EMPLEADO navegación Pestaña proyectos
        //********************************************/
            $arrayDoc = array_values(array_filter($this -> miEstado -> Documentos, function ($docF) use($arrayDatos) {
                return $docF["tipoDocPortal"] == 9
                && $docF["id"] ==  $arrayDatos[1];
            }));

            $this -> miEstado -> nombreDocumentoPadre = $arrayDoc[0]['Descripcion'];
            $tipoMat = 1;
            $this -> miEstado -> IdTipoPropietario = 149;
            if($arrayDatos[0] == 6.1){
                $tipoMat = 0;
                $this -> miEstado -> IdTipoPropietario = 147;
            }
            $this -> miEstado -> IdPropietario = $arrayDatos[1]; 
            $this -> miEstado -> arrayDatosAux = extraerRecursosFaseProyecto($arrayDatos[1],$tipoMat);
            $this -> navegarPestanas($arrayDatos[0]);

        }elseif($this -> miEstado -> Estado == 7  && $this -> miEstado -> tipo_App == 2 ){
        //********************************************/
        //PORTAL EMPLEADO cargar la pestaña de calendario
        //********************************************/
            foreach($this -> miEstado -> Documentos as $documento){
                if( !in_array($documento["tipoDocPortal"],array(2,3,4))){
                    $fi;
                    $ff;
                    //Convertir los nulos a date porque sino peta
                    try{
                       $fi = ($documento["FechaInicio"] ? $documento["FechaInicio"] -> format('Y-m-d H:i:s') : '1900-01-01 00:00:00');
                       $ff = ($documento["FechaFin"] ? $documento["FechaFin"] -> format('Y-m-d H:i:s') : '1900-01-01 00:00:00');
                    }catch (e){
                        $msgError = "Error del servidor en la conversión DateTime.";
                    }
                    if($ff && $fi){
                        $dc = array($documento["descripcion"],
                            $fi,
                            $ff,
                            $documento["color"],
                            $documento["tipoDocPortal"]
                        );
                        array_push($arrayAuxiliarHtml,$dc);
                    }
                }
            }
        
            $accionJs = 1;

        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 1 && in_array($this -> miEstado -> Estado, array(3,4,5,6,7,8,9)) && $this -> miEstado -> tipo_App == 1){
            //PORTAL CLIENTE
        //Sumar de 30  en 30 al puntero de mostrar documentos
            $this -> miEstado -> puntero_posicion += 30;
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 0 && in_array($this -> miEstado -> Estado, array(3,4,5,6,7)) && $this -> miEstado -> tipo_App == 1){
        //********************************************/
        //PORTAL CLIENTE filtrar por cadena de texto
        //********************************************/
            $this -> miEstado -> CadenaFiltro = null;
            $this -> miEstado -> tipofiltro = 1;
            if (!empty($arrayDatos[2])){
                $this -> miEstado -> CadenaFiltro = $arrayDatos[2];
            }
            $this -> miEstado -> puntero_posicion += count($this -> miEstado -> Documentos);
            

        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 2 && !empty($arrayDatos[2]) && in_array($this -> miEstado -> Estado, array(3,4,5,6,7)) && $this -> miEstado -> tipo_App == 1){
        //********************************************/
        //PORTAL CLIENTE filtrar por estado
        //********************************************/
            $this -> miEstado -> tipofiltro = 2;
            $this -> miEstado -> puntero_posicion += count($this -> miEstado -> Documentos);
            $this -> miEstado -> CadenaFiltro = $arrayDatos[2];
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 4 && in_array($this -> miEstado -> Estado, array(3,4,5,6,7,9)) && $this -> miEstado -> tipo_App == 1){
        //********************************************/
        //PORTAL CLIENTE descargar documentos en local cristal report o archivos subidos a onix
        //********************************************/
            $nombre_archivo = "";
            if($arrayDatos[3] == "OPDF"){
                $url_archivo ="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerPDF=1&pin=".$_SESSION["pinC"].'&IdOrigenImpresion='.$arrayDatos[2][1].'&IdPropietario='.$arrayDatos[2][0];
                $nombre_archivo = $arrayDatos[2][2];
            }else{
                $url_archivo ="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerArchivo=1&pin=".$_SESSION["pinC"].'&IdArchivo='.$arrayDatos[2][0];
                $nombre_archivo = $arrayDatos[2][1];
            }
            
            $nombre_archivo = str_replace('/','_',$nombre_archivo);
            
            $directorio_destino = "subidasTemp/".$_SESSION["pinC"]."/";
            // Verificar si el directorio existe, si no, crearlo
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0755, true);
            }

            // Realiza la solicitud HTTP para obtener el contenido del archivo
            $response = file_get_contents($url_archivo);
            if ($response === false) {
                $msgError = 'Error al obtener el archivo desde el servicio web.';
            } else {
                // Específica la ruta donde deseas guardar el archivo
                $rutaGuardado = $directorio_destino.$nombre_archivo; // Reemplaza esto con la ruta y nombre deseado
            
                // Guarda el contenido en un archivo en el servidor
                if (file_put_contents($rutaGuardado, $response) !== false) {
                    //$abrirNuebaPestaña = 1;
                    return array('php/'.$rutaGuardado,1,1);
                } else {
                    $msgError = 'Error al guardar el archivo en el servidor.';
                }
            }
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3  && $this -> miEstado -> tipo_App == 1 && $c == 8){
        //********************************************/
        //PORTAL CLIENTE insertar la solicitud correspondiente
        //********************************************/
            $solicitud = SolicitudTareas_Insert($arrayDatos[2][1]);
            if($solicitud){
                $msgError = 'Solicitud subida correctamente';
            }else{
                $msgError = 'No se ha podido realizar su solicitud';
            }
            $this -> cargaDocumentos_Parcial(5);
                
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 0 && $c == 4.4  && $this -> miEstado -> tipo_App == 2 && isset($arrayDatos[2])){
        //********************************************/
        //PORTAL EMPLEADO marcar como Firmado  documento (optimizar)
        //********************************************/
            foreach($this -> miEstado -> Documentos as $indice => $doc){
                if($doc['id'] == $arrayDatos[2] && $doc['tipoDocPortal'] == 3 ){
                
                    $this->miEstado->Documentos[$indice]['Firmado'] = 1;
                }
            }
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 12 && $c == 4.9  && $this -> miEstado -> tipo_App == 2 && isset($arrayDatos[2])){
        //********************************************/
        //PORTAL EMPLEADO Cambiar El año selecionado en la pestaña de vacaciones
        //********************************************/
            $this -> miEstado -> AnioSV = intval($arrayDatos[2]);
            $arrayAuxiliarHtml = $this -> calcularVacacionesGraf($this -> miEstado -> AnioSV);
            $accionJs = 2;
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3 && isset($arrayDatos[2])  && $this -> miEstado -> tipo_App == 2 && $c == 10.5){
            //print_r($arrayDatos[2]);
            solicitudCambioDatosInsert($arrayDatos[2]);
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3 && isset($arrayDatos[2])  && $this -> miEstado -> tipo_App == 2 && in_array($c, array(6.1,6.2))){
        //********************************************/
        //PORTAL EMPLEADO Insertar Material/Tiempo de proyectos
        //********************************************/
            $tipoMat = 1;
            if($c == 6.1){
                $tipoMat = 0;
            }
            elseif ($c == 6.2 && $this -> miEstado -> Estado == 6.2){
                $this -> miEstado -> adjuntarDocumentoFormAutomatico = 1 ;
                $this -> miEstado -> IdTipoPropietario = 149;
                $accionJs = 3;
            }
            else{
                $this -> miEstado -> adjuntarDocumentoFormAutomatico = 1 ;
                $this -> miEstado -> IdTipoPropietario = 149;
                $accionJs = 3;
            }
            $resultado = insertProyectosTareaMaterial($c,$arrayDatos[2]);
            
            $this -> miEstado -> arrayDatosAux = extraerRecursosFaseProyecto($this -> miEstado -> IdPropietario,$tipoMat);
            $this -> miEstado -> IdPropietarioAnterior = $this -> miEstado -> IdPropietario;
            $ultimoElemento = null;

            //SUBIR ARCHIVOS DIRECTAMTNE DE LA IMAGEN USADA PARA EL ANALISIS DE IA//
            if ($c == 6.2 && $this->miEstado->archivoAdjuntoTemporal != null) {


                $this -> miEstado -> Estado = 4.4;

                foreach (array_reverse($this -> miEstado -> arrayDatosAux) as $item) {
                    if (isset($item["tipoDocPortal"]) && $item["tipoDocPortal"] == 9.2) {
                        $ultimoElemento = $item;
                        break;
                    }
                }
                $this -> miEstado -> IdPropietarioAuxiliar = $ultimoElemento['id'];

                $archivoTemp = $this->miEstado->archivoAdjuntoTemporal[0];
                $nombre_archivo = $this->miEstado->archivoAdjuntoTemporal[1];
                $this->miEstado->archivoAdjuntoTemporal = null;
                $tiposarchivo = "32";

                $subida = $this -> subirArchivosServicioWeb($_SESSION["pinC"],
                                            $this -> miEstado -> IdTipoPropietario,
                                            $this -> miEstado -> IdPropietarioAuxiliar,
                                            $tiposarchivo, 
                                            $archivoTemp,
                                            $nombre_archivo);
            }

            $resultadoEjecucion = exect_Insert_From_DinamicoSubidaArchivos($tiposarchivo,$archivoTemp);
            if($resultadoEjecucion == false ){
                $msgError = "Ha ocurrido un error al insertal el registro";
            }else{
                array_unshift($_SESSION["Controlador"] -> miEstado -> Documentos,$resultadoEjecucion[0]);
            }
        //array_unshift($_SESSION["Controlador"] -> miEstado -> Documentos,$resultadoEjecucion[0]);
            
            $this -> miEstado -> adjuntarDocumentoFormAutomatico = 0; 


            
            
            if($this -> miEstado -> adjuntarDocumentoFormAutomatico == 1 ){
                foreach (array_reverse($this -> miEstado -> arrayDatosAux) as $item) {
                    if (isset($item["tipoDocPortal"]) && $item["tipoDocPortal"] == 9.2) {
                        $ultimoElemento = $item;
                        break;
                    }
                }
                $this -> miEstado -> IdPropietarioAuxiliar = $ultimoElemento['id'];
            }

            
            if($this -> miEstado -> IdPropietarioAnterior != $this -> miEstado -> IdPropietario){
                $this -> miEstado -> IdPropietario = $this -> miEstado -> IdPropietarioAnterior;
            }


            $this -> miEstado -> Estado = 6.2;
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3 && isset($arrayDatos[2])  && $this -> miEstado -> tipo_App == 2 ){
        //********************************************/
        //PORTAL EMPLEADO Insertar Formularios dinamcos
        //********************************************/ 

            if( $this -> miEstado -> Estado == 4.4 ){
                //var_dump($arrayDatos[4]);
                $subida = $this -> subirArchivosServicioWeb($_SESSION["pinC"],
                                                $this -> miEstado -> IdTipoPropietario,
                                                $this -> miEstado -> IdPropietario,
                                                $arrayDatos[2][0],
                                                $arrayDatos[3],
                                                str_replace([' ', '/'] ,['_','_'] ,$arrayDatos[4]) );
            }

            if($arrayDatos[2] != 0){
                $arrayForm = array_filter($this -> miEstado -> formularios, function ($form) {
                    return $form["Estado"] == $this -> miEstado -> Estado;
                });
                
                $arrayIntermedio = array_shift($arrayForm);
                $arraycampos = $arrayIntermedio["Campos"];
                $arrayValores = array();
                foreach($arraycampos as $campo){
                    if($campo["OUTPUT"] == 0 && $campo["Mostrar"] == 0 && $campo["ValorAdicional"] == null){
                        $valorCampo = isset($campo["VariableAlmacenada"]) ? $_SESSION["Controlador"] -> miEstado -> {$campo["VariableAlmacenada"]}  : $campo["ValorPorDefecto"];
                        if($valorCampo == "%now%"){
                            $valorCampo = date('Ymd H:i:s');
                        }elseif($valorCampo == '%randmKey%'){
                            $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                            $claveGenerada = '';

                            for ($i = 0; $i < 9; $i++) {
                                $claveGenerada .= $caracteres[rand(0, strlen($caracteres) - 1)];
                            }
                            $valorCampo = $claveGenerada;
                        }
                        array_push($arrayValores,$valorCampo);
                    }elseif($campo["TipoDatoHtml"] == "file"){
                        $valorCampo = $arrayDatos[3];
                        array_push($arrayValores,$valorCampo);
                    }elseif($campo["Mostrar"] == 1 && $campo["ValorAdicional"] == null){
                        $valorCampo = array_shift($arrayDatos[2]);
                        if (DateTime::createFromFormat('Y-m-d\TH:i', $valorCampo) !== false) {
                            $valorCampo = DateTime::createFromFormat('Y-m-d\TH:i', $valorCampo) -> format('Ymd H:i:s');
                        }elseif(DateTime::createFromFormat('Y-m-d', $valorCampo) !== false){
                            $valorCampo = DateTime::createFromFormat('Y-m-d', $valorCampo) -> format('Ymd');
                        }
                        array_push($arrayValores,$valorCampo);
                    }elseif($campo["Mostrar"] == 2){
                        $valorCampo = array_shift($arrayDatos[2]);
                        array_push($arrayValores,$valorCampo);
                    }
                }
            
                $resultadoEjecucion = true;

            if($arrayIntermedio["Instruccion"] != ""){
                
                $resultadoEjecucion = exect_Insert_From_Dinamico($arrayValores);
            }
                
                if($resultadoEjecucion == false ){
                    $msgError = "Ha ocurrido un error al insertal el registro";
                }else{
                    array_unshift($_SESSION["Controlador"] -> miEstado -> Documentos,$resultadoEjecucion[0]);
                }
                
                
            }
            $this -> miEstado -> cargarForm = 0;
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 3.5 && isset($arrayDatos[2])  && $this -> miEstado -> tipo_App == 2 ){
        //********************************************/
        //PORTAL EMPLEADO adjuntar archivo desde la pestaña que no es archivos (mierdas varias)
        //********************************************/
            $nombre_archivo = str_replace([' ', '/'] ,['_','_'] ,$arrayDatos[4]);
            $subida = $this -> subirArchivosServicioWeb($_SESSION["pinC"],
                                            $this -> miEstado -> IdTipoPropietario,
                                            $this -> miEstado -> IdPropietarioAuxiliar,
                                            $arrayDatos[2][0],
                                            $arrayDatos[3],
                                            $nombre_archivo);

                                
            $resultadoEjecucion = exect_Insert_From_DinamicoSubidaArchivos($arrayDatos[2],$arrayDatos[3]);

            var_dump($arrayDatos[2]);
            var_dump($arrayDatos[3]);
            if($resultadoEjecucion == false ){
                $msgError = "Ha ocurrido un error al insertal el registro";
            }else{
                array_unshift($_SESSION["Controlador"] -> miEstado -> Documentos,$resultadoEjecucion[0]);
            }
        //array_unshift($_SESSION["Controlador"] -> miEstado -> Documentos,$resultadoEjecucion[0]);
            
            $this -> miEstado -> adjuntarDocumentoFormAutomatico = 0;          
            
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 4  && $this -> miEstado -> tipo_App == 2){
        //********************************************/
        //PORTAL EMPLEADO Filtrar por cadena de txt o ids  
        //********************************************/
            $this -> miEstado -> IdsTiposFiltro = array();

            $this -> miEstado -> CadenaFiltro = null;
            if($arrayDatos[2] != null && $arrayDatos[2] != ''){
                $this -> miEstado -> CadenaFiltro = $arrayDatos[2];
            }
            if(!empty($arrayDatos[3])){
                $this -> miEstado -> IdsTiposFiltro = $arrayDatos[3];
            }
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 0 && $this -> miEstado -> Estado == 5 && $this -> miEstado -> tipo_App == 2){
        //********************************************/
        //PORTAL Empleado Iniciar/ finalizar jornada en la pestaña de tiempos
        //********************************************/
            if(isset($arrayDatos[2]) && isset($arrayDatos[3])){
                $this -> iniciarFinalizarJornada($arrayDatos[2],$arrayDatos[3],$arrayDatos[4]);
            }else{
                $this -> iniciarFinalizarJornada();
            }
            //$this -> miEstado -> EstadoJornada = comprueba_jornada_personal();//comprobar el estado actual
            $this -> miEstado -> HistoricoJornada = extraer_JornadaHistorico();// extraer el historico de la jornada
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == 0 && $arrayDatos[1] == 11 && $this -> miEstado -> tipo_App == 2){
        //********************************************/
        //obtener archivo portal empleado
        //********************************************/
            $nombre_archivo = "";
            $ejecucionArchivos;
            if($arrayDatos[3] == "OPDF"){
                
            }else{
                $ejecucionArchivos = $this -> descargaArchivoServicoWeb($arrayDatos[2][0],$arrayDatos[2][1]);
                if($ejecucionArchivos){
                    return($ejecucionArchivos);
                }else{
                    $msgError = 'Error al obtener el archivo desde el servicio web.';
                }
                
            }
        
        }elseif(!empty($arrayDatos) && $arrayDatos[0] == -1 && $arrayDatos[1] == -1){
            $this -> cerrarSesion();
        }
        //navega a la siguiente pestaña segun el estado de la clase estado
        $txtErr = '';
        // //if($_SESSION["pinC"] == 123654){
             //$txtErr = "A:".$this -> miEstado -> tipo_App. "idI :".$this -> miEstado -> IdIdentidad.
             $this -> miEstado -> IdPersonal."pin :".$_SESSION["pinC"]."Estado:".$this -> miEstado -> Estado."tipo:".$nav."ip :".$this -> miEstado -> IP."bbdd :".$this -> miEstado -> bbdd."IdTP :". $this -> miEstado -> IdTipoPropietario."IdPro : ".$this -> miEstado -> IdPropietario;
        //     //}

        $txtErr .= "Id usuario: ".$this -> miEstado -> IdPersonal;
        $txtErr .= " - Id propietario: ".$this -> miEstado -> IdPropietario;
        $txtErr .= " - Id tipo propietario: ".$this -> miEstado -> IdTipoPropietario;
        $txtErr .= "- Estado: ".$this -> miEstado -> Estado;
        
        return array(pinta_contenido($this -> miEstado -> Estado, $this -> miEstado -> tipo_App).$txtErr,$msgError,$AccionSinRepintar,$arrayAuxiliarHtml,$accionJs);
    }
}
    

?>