<?php
$documentos_completo;
$titulo;
$fitro1;
$filtro2;
$filtro3;
$nombre_cliente;
$UsuarioAdmin = FALSE;
$otroUsuario = FALSE;
$ModalFirma = 0;
function pinta_contenido($estado){
    global $UsuarioAdmin;
    global $otroUsuario;
    
#Declaraciones
    $titulo = ""; 
    $cabecera = "../html/header.html";
    $fileheadertext = "";
    $headerCliente = "";
    $footerCliente = "";
    //Sino se comprueba dentro de la funcion falla
    if(isset($_SESSION["Controlador"]->miEstado->configuracionesUsuario) && in_array(74, $_SESSION["Controlador"] -> miEstado -> configuracionesUsuario )){
        $UsuarioAdmin = TRUE;
    }
    if($_SESSION["Controlador"] -> miEstado -> IdPersonalAdmin != $_SESSION["Controlador"] -> miEstado -> IdPersonal){
        $otroUsuario = TRUE;
    }
    switch ($estado) {
        case 0:
            $cabecera = "";
            $filename = "../html/login.html";
            break;
        case 0.5:   
            $cabecera = "";
            $filename = "../html/campo_cif.html";
    
            break;
        case 1:
            $titulo = "Selecciona Sociedad";
            $filename = "../html/socie  dades.html";
            break;
        case 2:
            $titulo = $_SESSION["Controlador"] -> miEstado -> NombreSociedad;
            break;  
        case 3:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $titulo = "Mis Presupuestos";
                    $filename = "../html/documentos.html";
                    break;
                case 2:
                    break;
            }
            break;
        case 4:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $titulo = "Mis  Pedidos";
                    $filename = "../html/documentos.html";
                    break;
                case 2:
                    $titulo = $_SESSION["Controlador"] -> miEstado -> NombreSociedad;
                    //$filename = "../html/secciones_Empleado_02.html";
                    break;
            }
            break;
        case 4.1:
            $titulo = "Asistencias";
            $filename = "../html/documentos.html";
            break;
        case 4.3:
            $titulo = "Contratos";
            $filename = "../html/documentos.html";
            break;
        case 4.4:
            $titulo = "Documentos";
            $filename = "../html/documentos.html";
            break;
        case 4.45:
            $titulo = "Observaciones";
            $filename = "../html/documentos.html";
            break;
        case 4.5:
            $titulo = "Formación";
            $filename = "../html/documentos.html";
            break;
        case 4.6:
            $titulo = "Incidencias";
            $filename = "../html/documentos.html";
            break;
        case 4.7:
            $titulo = "Material";
            $filename = "../html/documentos.html";
            break;
        case 4.8:
            $titulo = "Nóminas";
            $filename = "../html/documentos.html";
            break;
        case 4.9:
            $titulo = "Vacaciones";
            $filename = "../html/vacacionesTabs.html";
            break;    
        case 5:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $titulo = "Mis Albaranes";
                    $filename = "../html/documentos.html";
                    break;
                case 2:
                    $titulo = $_SESSION["Controlador"] -> miEstado -> NombreSociedad;
                    $filename = "../html/Estado_Jornada_Empleado.html";
                    break;

            }
            
            break;
        case 6:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $titulo = "Mis Facturas";
                    //$_SESSION["tipo_documento"] = 3;
                    $filename = "../html/documentos.html";
                    break;
                case 2:
                    $titulo = "Proyectos";
                    //$_SESSION["tipo_documento"] = 3;
                    $filename = "../html/documentos.html";
                    break;
            }
                
            break;
        case 6.1:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    break;
                case 2:
                    $titulo = "Tiempos";
                    //$_SESSION["tipo_documento"] = 3;
                    $filename = "../html/documentos.html";
                    break;
            }
                
            break;
        case 6.2:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    break;
                case 2:
                    $titulo = "Recurso";
                    //$_SESSION["tipo_documento"] = 3;
                    $filename = "../html/documentos.html";
                    break;
            }
                
            break;
        case 7:
               
                switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                    case 1:
                        $titulo = "Mis Proyectos";
                        $filename = "../html/documentos.html";
                        break;
                    case 2:
                        $titulo = "Calendario";
                        $filename = "../html/calendarioEmpleado.html";
                        break;
                }
            break;
        case 7.1:
            
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:
                    $titulo = "Proyecto %NombreProyecto%";
                    $filename = "../html/tareasFasesDetalle.html";
                    
                    break;
            }
            break;
        case 8:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:    
                    $titulo = "Peticiones";
                    $filename = "../html/documentos.html";
                    break;
            }
            break;
        case 9:
            switch($_SESSION["Controlador"] -> miEstado -> tipo_App){
                case 1:  
                    $titulo = "Archivos";
                    $filename = "../html/documentos.html";
                    break;
            }
            break;
        case 10:        
            $titulo = "Cambiar Contraseña";
            $filename = "../html/cambiarContrasena.html";
            break;
        case 10.5:        
            $titulo = "Mis Datos";
            $filename = "../html/misDatos.html";
            break;
        case 11:        
            $titulo = "Reestablecer Contraseña";
            $filename = "../html/reestablecerContrasenaCampos.html";
            break;
        default:
            $filename = "../html/login.html";
            break;
    
    }
    
    if($UsuarioAdmin && $otroUsuario && !in_array( $_SESSION["Controlador"] -> miEstado ->Estado,array(2,4,6,6.1,6.2))){
        $titulo .= ' de '.$_SESSION["Controlador"] -> miEstado -> nombre_descriptivo;
    }

    if ($cabecera != "") {
        $fileheadertext = '';
        if(!isset($_SESSION["header"])){
            $fileheader = fopen($cabecera, "r");
            $filesizeheader = filesize($cabecera);
            $fileheadertext = fread($fileheader, $filesizeheader);
            fclose($fileheader);
        }else{
            $fileheadertext = $_SESSION["header"];
        }
        
        //$fileheadertext = $_SESSION["Controlador"] -> miEstado -> header;

        if($_SESSION["Controlador"] -> miEstado -> cargarForm == 1){
            $titulo.=' / Nueva';
        }elseif($_SESSION["Controlador"] -> miEstado -> nombreDocumentoPadre != null){
            $titulo.= ' de '.$_SESSION["Controlador"] -> miEstado -> nombreDocumentoPadre;
        }
        $fileheadertext = str_replace("%NombreE%",$titulo,$fileheadertext);
        if($_SESSION["Controlador"] -> miEstado -> Estado == 2){
            $fileheadertext = str_replace('class="col-2 flecha_volver"','class="col-2 flecha_volver d-none"',$fileheadertext);
        }
    }

    //cargar el nombre del usuario
    $nombre_usu = '<hr size="1px" color="grey"/><li class="dropdown-item">'.$_SESSION["Controlador"] -> miEstado -> nombre_descriptivo.'</li>';
    if($_SESSION["Controlador"] -> miEstado -> acciones["accionBuscarMenu"] == 1 ){
        $fileheadertext = str_replace('%accionBuscarHeader%','',$fileheadertext);
        if(in_array($_SESSION["Controlador"] -> miEstado -> Estado,array(7,4.9))) {
            $fileheadertext = str_replace('abrirTxtBoxBuscar','desplegarPanelFiltros',$fileheadertext);
            
        }
    }else{
        $fileheadertext = str_replace('%accionBuscarHeader%','d-none',$fileheadertext);
        
    }
    
    //cargar el permiso de buscar 
    
    if($UsuarioAdmin){
        $fileheadertext = str_replace('%CargarUsuario%','<li class="dropdown-item" onclick="cargarModalFormularioDinamico('."'modalSuperAdmin'".')">Seleccionar Personal</li>',$fileheadertext);
        
    }else{
        $fileheadertext = str_replace('%CargarUsuario%','',$fileheadertext);
    }
    
    $fileheadertext = str_replace('<li class="d-none" id="%NombreUsu%"></li>',$nombre_usu,$fileheadertext);
    


    if(isset($filename) && $filename != "" ){
        $file = fopen($filename, "r");
        $filesize = filesize($filename);
        $filetext = fread($file, $filesize);
        $filetext =  $fileheadertext. $filetext;
        fclose($file);
    }else{
        $filetext = $fileheadertext.cargarSeccionesDinamicas();
    }
    // $footer = fopen("../html/footer.html", "r");
    // $filesizef = filesize($filename);
    // $footer = fread($footer, $filesizef);
    // if($_SESSION["Controlador"] -> miEstado -> Estado == 11){
    //     $filetext = str_replace(['div class="btn-group d-flex" >',' class="flecha_volver col-2'],['div class="btn-group d-none" >','class="col-2 d-none'],$filetext);   
    
    // }
    
    //optimizar aqui
    if (in_array($_SESSION["Controlador"] -> miEstado -> Estado, array(1, 2, 3, 4, 5, 6,6.1,6.2, 7,7.1, 8, 9)) || ($_SESSION["Controlador"] -> miEstado -> Estado < 5 && $_SESSION["Controlador"] -> miEstado -> Estado > 4 )) {
        if ($_SESSION["Controlador"] -> miEstado -> Estado == 1){
            //Pestaña de sociedades del portal del comercial
            $filetext = str_replace('%ListaSociedades%',muestra_sociedades(),$filetext);  
        }elseif(in_array($_SESSION["Controlador"] -> miEstado -> Estado , array(3, 4, 5, 6, 7, 8, 9)) && $_SESSION["Controlador"] -> miEstado -> tipo_App == 1){
            //Documentos portal del comercial
            $btnBuscar = '<li><div class="input-group">
            <input type="text" class="form-control" placeholder="Buscar..." aria-label="Buscar..." id="filtro_txt" >
            <div class="input-group-append">
              <button class="btn btn-outline-secondary" type="button" onclick="%FuncionFiltrar%"><img src="Img/lupa.png" width="17px"></button>
            </div>
          </div></li>';
          
            if(!in_array($_SESSION["Controlador"] -> miEstado -> Estado,array(8, 9))){
                $filetext = str_replace('<li class="d-none" id="%btnBuscar%"></li>',$btnBuscar,$filetext);
            }
            $filetext = str_replace('<span id="filtros_dinamicos">',cargaFiltros(),$filetext);
            $filetext = str_replace('%FuncionFiltrar%','aplicafiltros()',$filetext);
            
            $filetext = str_replace('%LineasE%',muestra_documentos(),$filetext);
        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 7.1 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 1){
            $idpt = $_SESSION["Controlador"] -> miEstado -> IdPropietario;
            $nombreP =  array_filter($_SESSION["Controlador"] -> miEstado -> Documentos, function ($elemento) use ($idpt) {
                return $elemento['id'] == $idpt && $elemento['tipo'] == 4;
            });
            $nombreP = array_values($nombreP);
            $filetext = str_replace('%NombreProyecto%',$nombreP[0]['Descripcion'],$filetext);
            $filetext = pinta_TareasRecursos_Cliente($filetext);
        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 2 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
            // elegir la pestaña de la jornad++++a correspondiente switch ($_SESSION["Controlador"] -> miEstado -> EstadoJornada[0]) {
            switch ($_SESSION["Controlador"] -> miEstado -> EstadoJornada[0]) {
                case 0:
                    $filetext = str_replace('id="pestanaFueraJornada" style="display: none;">','id="pestanaFueraJornada" style="display: flex;">',$filetext);
                    break;
                default:
                    $filetext = str_replace('id="pestanaEnJornada" style="display: none;">','id="pestanaEnJornada" style="display: flex;">',$filetext);
                    break;
            }
        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 3 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
        //añadir los filtros a el calendario No funciona
            // $arrayFiltros = [
            //     ["idTipoDoc" => 1,
            //     "Descripcion" => "Asistencias"],
            //     ["idTipoDoc" => 2,
            //     "Descripcion" => "Contratos"],
            //     ["idTipoDoc" => 4,
            //     "Descripcion" => "Formación"],
            //     ["idTipoDoc" => 5,
            //     "Descripcion" => "Incidencias"],
            //     ["idTipoDoc" => 6,
            //     "Descripcion" => "Material"]
            // ];
        
            // // funcion para reinedxarlo 
            
            // $txt_filtros = '<span id="filtros_dinamicos">';
            // if(count($arrayFiltros)>0){
            //     foreach($arrayFiltros as $valor){ 
            //         $txt_filtros .= '<button onclick="" style="color:black;" class="dropdown-item" id="'.$valor["idTipoDoc"].'" >'.$valor["Descripcion"].'</button>';
            //     }
            // }
            //$filetext = str_replace() $txt_filtros;
        }elseif( 
            (($_SESSION["Controlador"] -> miEstado -> Estado < 5 && $_SESSION["Controlador"] -> miEstado -> Estado > 4 ) || in_array($_SESSION["Controlador"] -> miEstado -> Estado,array(6,6.1,6.2) ))
            && !in_array($_SESSION["Controlador"] -> miEstado -> Estado,array(4.9) ) && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 
            && $_SESSION["Controlador"] -> miEstado -> cargarForm == null  && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2
        ){
        //Pestañas de navegacion
            $filetext = str_replace('%NombreEmpleado%',$_SESSION["Controlador"] -> miEstado -> nombre_descriptivo,$filetext);
            $filetext = str_replace('%LineasE%',DibujaLineas_PortalEmpleado(),$filetext);
            if($ModalFirma ==1){
                
                $fileModal = fopen('../html/ModalFirma.html', "r");
                $filesizeModal = filesize('../html/ModalFirma.html');
                $Modalfirmadoc = fread($fileModal, $filesizeModal);
                fclose($fileModal);
                $filetext = $filetext.$Modalfirmadoc;
            } 
            
            
        }elseif( $_SESSION["Controlador"] -> miEstado -> Estado < 5 && $_SESSION["Controlador"] -> miEstado -> Estado > 4  && in_array($_SESSION["Controlador"] -> miEstado -> Estado,array(4.9) ) && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == null  && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
            $filetext = str_replace('%NombreEmpleado%',$_SESSION["Controlador"] -> miEstado -> nombre_descriptivo,$filetext);
            if($_SESSION["Controlador"] -> miEstado -> AnioSV == 0){
                $filetext = str_replace('class="nav-link" id="tab2"','class="nav-link d-none" id="tab2"', $filetext);
            }
            $filetext = DibujaPestanaVacaciones_Empleado($filetext);
        //}elseif( $_SESSION["Controlador"] -> miEstado -> Estado < 5 && $_SESSION["Controlador"] -> miEstado -> Estado > 4  && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == 1 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
        //Generar los formularios
            //return $filetext.cargaFormularioDinamico();
        }elseif ($_SESSION["Controlador"] -> miEstado -> Estado == 4.4 && $_SESSION["Controlador"] -> miEstado -> cargarFormFirma == 1 ) {
            return $filetext;//.cargarFormularioFirma();
        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 5 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
        //Pestaña de jornada
            $filetext = str_replace('%NombreEmpleado%',$_SESSION["Controlador"] -> miEstado -> nombre_descriptivo,$filetext);
            if($UsuarioAdmin && $otroUsuario){
                $filetext = str_replace('<img src="Img/FlechaJornada.png" alt="BotonIniciarFinalizarJornada">','',$filetext);
            }
            switch ($_SESSION["Controlador"] -> miEstado -> EstadoJornada[0]) {
                case 1:
                    $filetext = str_replace('<b style="color: %color%;"></b>','<b style="color: green;">En jornada</b>',$filetext);
                    $filetext = str_replace('%BotonIniciarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonFinalizarJornada%','block',$filetext);
                    $filetext = str_replace('%BotonPausarJornada%','block',$filetext);
                    $filetext = str_replace('%BotonretomarJornada%','none',$filetext);
                    break;
                case 2:
                    $filetext = str_replace('<b style="color: %color%;"></b>','<b style="color: grey;">En pausa</b>',$filetext);
                    $filetext = str_replace('%BotonIniciarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonFinalizarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonPausarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonretomarJornada%','block',$filetext);
                    break;
                default:
                    $filetext = str_replace('<b style="color: %color%;"></b>','<b style="color: red;">Fuera de jornada</b>',$filetext);
                    $filetext = str_replace('%BotonIniciarJornada%','block',$filetext);
                    $filetext = str_replace('%BotonFinalizarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonPausarJornada%','none',$filetext);
                    $filetext = str_replace('%BotonretomarJornada%','none',$filetext);
                    break;
            }
            
            $filetext .= cargarJornadaHistorico();
            
            //pintar el modal del fichaje
            $filetext .= '<div class="modal fade" id="ModalMapaJornada" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="TituloError">Mapa fichaje</h5>
                </div>
                <div class="modal-body" id="mapaF">
                <div class="col-12 " id="mapafichaje" style="height: 250px;"></div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button> 
                  <!-- <button type="button" class="btn btn-primary">Aceptar</button>-->
                </div>
                </div>
                </div>
            </div>';
            
        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 7 && $_SESSION["Controlador"] -> miEstado -> tipo_App == 2 && $_SESSION["Controlador"] -> miEstado -> cargarForm == 0){
            // PESTAÑA DE CALENDARIO GENERAR MODAL
            $filetext .= '';
        }
    }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 10.5){
    
       
        $arrayPer =  $_SESSION["Controlador"] -> miEstado -> datosPersonal;
        $filetext = str_replace(["%Telf%","%Dir%","%Pob%","%Cp%","%Turno%","%Dep%","%Cargo%","%Compe%","%NomP%","%Bic%","%Iban%","%TZ%","%TP%","%TC%"],
                                    [$arrayPer['telefonoparticular'],$arrayPer['direccion'],$arrayPer['poblacion'],$arrayPer['codpostal'],$arrayPer['Turno'],$arrayPer['Departamento'],$arrayPer['Cargo'],$arrayPer['Competencias'],$arrayPer['NombreCuenta'],$arrayPer['Bic'],$arrayPer['Iban'],$arrayPer['TallaZapatos'],$arrayPer['TallaPantalon'],$arrayPer['TallaCamisa']],$filetext);   


    } else {
        
        if($_SESSION['Controlador'] -> miEstado -> tipo_App == 1){
            $filetext = str_replace(["%NombrePortal%","%TipoUsuario%","%Linkcrear%"],['Área del Cliente','cliente','alta_Usuario'],$filetext);   
        }else{
            $filetext = str_replace(["%NombrePortal%","%TipoUsuario%","%Linkcrear%"],['Portal del Empleado','empleado','alta_Empleado'],$filetext);
        }
        if(isset($_SESSION["imgLogo"])){
            $filetext = str_replace("%logoImg%",$_SESSION["imgLogo"],$filetext);
        }else{
            $filetext = str_replace("%logoImg%",'https://esquio.es/wp-content/uploads/elementor/thumbs/logo-esquio-pq0g3tu6khq6p3k32wrm4q6iwu2nqga9msxptholvk.png',$filetext);
        }
        
    }
    //Adición del header del cliente 
    if($cabecera == "" && isset($_SESSION['headerCliente'])){
        $filetext = $_SESSION['headerCliente'].$filetext;
    }elseif($cabecera != "" && isset($_SESSION['headerCliente'])){
        $filetext = str_replace('%headerCliente%',$_SESSION['headerCliente'],$filetext);
    }elseif($cabecera != ""){
         $filetext = str_replace('%headerCliente%','',$filetext);
    }
    //Adición del footer del cliente
    if(isset($_SESSION['footerCliente'])){
        $filetext .= '<br>'.$_SESSION['footerCliente'];
    }
    
    if($UsuarioAdmin){
        $filetext.=superUsuarioModal();
    }
    return $filetext;
    
}

function muestra_sociedades(){

    //**************************************************************************************/

    //---------------------------------MOSTRAR SOCIEDADES---------------------------------\\

    //**************************************************************************************/
        $tablaSociedades = "";
        $tablaSociedades = "<form class='formulario_sociedades'>";
        foreach ($_SESSION["Controlador"] -> miEstado -> lista_sociedades as $valor) {
            $id_sociedad = $valor["idSociedad"];
            $sociedad = $valor["NombreFiscal"];
                
            $tablaSociedades .= "<div class='form-group mt-4'>";
            $tablaSociedades .= "<div class='form-check'>";
            $tablaSociedades .= "<label class='form-check-label' for='".$id_sociedad."'>".$sociedad."</label>";
            $tablaSociedades .= "<input class='form-check-input' type='radio' name='radio_sociedad' id='".$id_sociedad."' value='".$id_sociedad."' required>";
            $tablaSociedades .= "</div></div><br>";
        }
        $tablaSociedades .= "<input  style='float:right; margin-bottom:1.3em;' class='btn btn-outline-primary' type='submit' name='enviar' value='Siguiente'>";
        $tablaSociedades .= "</form>";
        $tablaSociedades .= "<br>";
        return $tablaSociedades;
}

//dibujar los documentos del portal del comercial segun la pestaña
function muestra_documentos(){
    //optimizar al cambiar la manera de almacenar los documentos y los filtros "array_filter()"
    $arrayDoc = array();
    $listaDoc = "";
    $acciones_linea = '';
    $acciones_globales = '';
    if($_SESSION["Controlador"] -> miEstado -> acciones["descarga"] == 1 ){
        $acciones_linea .= '<a onclick="dibuja_pagina([0,4,[%id%,%Origen_impresion%,'."'%codigo%.pdf'".'],'."'OPDF'".'])" ><img class="pdf_icono" src="Img/descarga_pdf.png"></a>';
    }
    if($_SESSION["Controlador"] -> miEstado -> acciones["archivos"] == 1 ){      
        $acciones_linea .= '<a onclick="dibuja_pagina([9,%id%])" style="all: initial;cursor: pointer;"><img class="pdf_icono" src="%ImgArchivos%"></a>';
        
    }
    if($_SESSION["Controlador"] -> miEstado -> acciones["adjunto"] == 1 ){
        $acciones_linea .= '<a onclick="dibuja_pagina([0,4,[%id%,'."'%codigo%'".'],'."'OA'".'])" style="all: initial;cursor: pointer;"><img class="pdf_icono" src="Img/descarga_generica.png"></a>';    
    
    }
    if($_SESSION["Controlador"] -> miEstado -> acciones["observaciones"] == 1 ){
        $acciones_linea .= '<a onclick="cargarModalDetalles(%LabelMostrar%,%ValorMostar%)" style="all: initial;cursor: pointer;"><img class="pdf_icono" src="Img/IconosAcciones/verDetalles.png"></a>';
    }
    if($_SESSION["Controlador"] -> miEstado -> acciones["modalVisualizar"] == 1 ){
        $acciones_linea .= '<a onclick="cargarModalDetalles(%LabelMostrar%,%ValorMostar%)" style="all: initial;cursor: pointer;"><img class="pdf_icono" src="Img/verDetalles.png"></a>';
        
    }
    if($_SESSION["Controlador"] -> miEstado -> acciones["VisualizarDetalles"] == 1 ){
        $acciones_linea .= '<a onclick="dibuja_pagina([7.1,%id%])" style="all: initial;cursor: pointer;"><img class="pdf_icono" src="Img/verDetalles.png"></a>';
    }

    if($_SESSION["Controlador"] -> miEstado -> acciones["anadirLinea"] == 1){
        $acciones_globales .= '<button onclick="cargarModalValidacion(1,0,0)"  class="btn_acciones"><img src="Img/Portal_Empleado_Nuevo2.png"></button>';    
    }

    


    if (in_array($_SESSION["Controlador"] -> miEstado -> Estado , array(3, 4, 5, 6, 7, 8))) {
        
        if($_SESSION["Controlador"] -> miEstado -> acciones["modalValidaciones"] == 1 ){
            //$acciones_linea .= '<a onclick="cargarModalValidacion(0,%id%,'."'%codigo%'".')" ><img class="pdf_icono" src="Img/bolsa-de-dinero.png"></a>';
            // Optimizar pepe
            $acciones_globales .= '<div class="modal fade" id="modalValidaciones" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="CabeceraModalSolicitud"></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form class="formulario_modal">
                  <div class="mb-3">
                  
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="togglePeticionSwitch" checked >
                    <label class="form-check-label2" for="togglePeticionSwitch" id="LabelPeticionSwitch">Mejora</label>
                  </div>
                </div>
                
                <div class="btn-group">';
 
                // $acciones_globales .= '<ul>';
                // foreach ($_SESSION["Controlador"] -> miEstado -> PersonasContacto as $pc) {
                //     $acciones_globales .= '<li><button  class="btn btn-outline-secondary  lista">' . $pc['Nombre'] . '</button></li>';
                // }
                // $acciones_globales .= '</ul> </ul>
                $acciones_globales .= '</div>';
               
                
              
                $acciones_globales .= '
                
                    <div class="mb-3">
                      <label for="message-text" class="col-form-label">Descripción:</label>
                      <textarea class="form-control" id="message-text" required></textarea>
                    </div>
                  
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  <button type="button" class="btn btn-primary" id="SubmitModalForm" onclick="SubmitModalForm(0)">Solicitar</button>
                </div>
              </div>
            </div>
          </div>';
        }
        
        $tipoDocf = 0;
        switch ($_SESSION["Controlador"] -> miEstado -> Estado) {
            case 4 :
                $tipoDocf = 1;
                break;
            case 5 :
                $tipoDocf = 2;
                break;
            case 6 :
                $tipoDocf = 3;
                break;
            case 7 :
                $tipoDocf = 4; 
                break;
            case 8 :
                $tipoDocf = 5; 
                break;
            default:
                $tipoDocf = 0;
                break;
        }
        
        // funcion para aplicar un filtro a cada elemento del array
            $arrayDoc = array_filter($_SESSION["Controlador"] -> miEstado -> Documentos, function ($docF) use($tipoDocf) {
                return $docF["tipo"] == $tipoDocf;
            });

            //filtro por tipo 1 de filtro es decir string 
            if($_SESSION["Controlador"] -> miEstado -> CadenaFiltro !== null && $_SESSION["Controlador"] -> miEstado -> tipofiltro == 1){
                $arrayDoc = array_filter($arrayDoc, function ($docF) use($arrayDoc) {
                    return str_contains(strtolower($docF["Descripcion"]),strtolower($_SESSION["Controlador"] -> miEstado -> CadenaFiltro));
                });
            }

            
            //filtro por tipo 2 de filtro es estado del doc 
            if($_SESSION["Controlador"] -> miEstado -> CadenaFiltro !== null && $_SESSION["Controlador"] -> miEstado -> tipofiltro == 2 ){
                $arrayDoc = array_filter($arrayDoc, function ($docF) use($arrayDoc) {
                    return $docF["Estado"] == $_SESSION["Controlador"] -> miEstado -> CadenaFiltro;
                });
            }
            

            // funcion para reinedxarlo 
            $arrayDoc = array_values($arrayDoc);
       
        $listaDoc .= "<section>";
            
        if(count($arrayDoc)>0){

            $listaDoc .=  "<table class='table table-striped table-bordered-bottom' id='cuerpo'>";

            $listaDoc .=  "<tbody id='myTable'>";

            for($dc = 0; ($dc < count($arrayDoc)) && ($dc < $_SESSION["Controlador"] -> miEstado -> puntero_posicion) ; $dc++ ){
                $valor = $arrayDoc[$dc]; 
                $id = $valor["id"];
                $codigo = $valor["codigo"];
                $PinDescargas = $valor["PinDescargas"];
                $Origen_impresion = $valor["OrigenImpresion"];
                $importe = $valor["importe"];
                $descripcion = $valor["Descripcion"];
                $newDate = $valor['Fecha'] -> format('d/m/Y');
                $estado = $valor["Estado"];
                $color = $valor["color"];
                $codigo2 ='';
                if($tipoDocf == 4){
                    $codigo2 = $descripcion;
                    $importe = $newDate;
                    $descripcion = $valor["Descripcion2"];
                }else{
                    $codigo2 = $codigo.' - ' . $newDate;
                }
                //optimizar/cambiar
                if (strlen($descripcion) <= 0) {
                    $descripcion = "Sin descripción";
                }

                if($newDate==date("d/m/Y")){
                    $listaDoc .= "<tr data-tipo-servicio='$estado' id='$estado'>";
                }else{
                    $listaDoc .= "<tr data-tipo-servicio='$estado' id='$estado'>";
                }
                $imgRutaCarpeta = '';
                if($_SESSION["Controlador"] -> miEstado -> acciones["archivos"] == 1 ){
                    $imgRutaCarpeta = 'Img/Documentos2.png';
                    //print_r($carperta);
                    if(isset($valor["contadorDoc"]) && $valor["contadorDoc"]>0){
                        $imgRutaCarpeta = 'Img/Documentos_verde2.png';
                    }
                }



                //$acciones_mostrar = str_replace(['%id%','%Origen_impresion%','%codigo%','%ImgArchivos%'], [$id, $Origen_impresion,$codigo,$imgRutaCarpeta], $acciones_linea);

                $acciones_mostrar = str_replace(['%id%','%Origen_impresion%','%codigo%','%ImgArchivos%','%LabelMostrar%','%ValorMostar%'], [$id, $Origen_impresion,$codigo,$imgRutaCarpeta,"['Documento','Estado','Descripción']","['".$codigo . " - " . $newDate."','".$estado."','".$descripcion."']"], $acciones_linea);
                //$listaDoc .= '<td class="col-7"><div class="Identificador" name="Identificador">' . $codigo . ' - ' . $newDate . '<br>';
                $listaDoc .= '<td class="col-7"><div class="Identificador" name="Identificador">' .$codigo2 . '<br>';
                if(isset($_SESSION["Controlador"] -> miEstado -> acciones["desplegado"]) && $_SESSION["Controlador"] -> miEstado -> acciones["desplegado"] == 0){
                    $listaDoc .= '<details>';
                    
                }else{
                    $listaDoc .= '<details open>';
                }
                $listaDoc .='<summary></summary>';
                $listaDoc .='<p><b style="color:'.$color.';">' . $estado . '</b><br>';
                $listaDoc .= '<span class="descripcion_doc">'.$descripcion .'</span></p>';
                if($_SESSION["Controlador"] -> miEstado -> acciones["desplegado"] == 0){
                    $listaDoc .= '</details>';
                }

                $listaDoc .= '</div></td>';
                $listaDoc .= '<td class="col-5"><div class="precio" name="precio" ><b id="importe_documento" style="float:right;color:'.$color.';">' . $importe;
                $listaDoc .= '</b><br>'.$acciones_mostrar.'</div></td></tr>';
                //$listaDoc .= '</b><br><a onclick="dibuja_pagina([0,4,['.$id.','.$Origen_impresion.",'".$codigo.".pdf'".']])" >';
                //$listaDoc .= '<img class="pdf_icono" src="Img/descarga_generica.png"></a></div></td></tr>'; 
                }

                $listaDoc .= "</tbody></table></span>";
                $listaDoc .= "<div class='container text-center'>";
                $listaDoc .= "<div class='row'>";
                $listaDoc .= "<div class='col'>";

                //optimizar moverlo a un documento aparte
                if (count($arrayDoc) > $_SESSION["Controlador"] -> miEstado -> puntero_posicion) {
                    $listaDoc .= '<button type="button" class="btn btn-primary mb-3" onclick="dibuja_pagina([0,1])">Ver más...</button>';
                }

        }else{
            $listaDoc .= '<div class="d-flex justify-content-center mt-5 mb-5"> No hay elementos </div>';
        }
        
        
        
       
    }else{
        //mostrar los archivos con sus acciones propias
        $arrayDoc = $_SESSION["Controlador"] -> miEstado -> ArchivosDocumento;
        $arrayDoc = array_values($arrayDoc);

        //lista de docs con acceso
        $arrayDropdown = array_filter($_SESSION["Controlador"] -> miEstado -> archivostiposAccesos, function ($dupla){
            return $dupla["IdTipoPropietario"] == 2; 
        });
        
        $acciones_globales .= '<button onclick="cargarModalValidacion(2,0,'."''".')"  class="btn_acciones"><img src="Img/Portal_Empleado_Nuevo2.png"></button>';    
        if($_SESSION["Controlador"] -> miEstado -> acciones["modalSubirDoc"] == 1 ){
            //$acciones_linea .= '<a onclick="cargarModalValidacion(0,%id%,'."'%codigo%'".')" ><img class="pdf_icono" src="Img/bolsa-de-dinero.png"></a>';
            // Optimizar pepe

            $acciones_globales .= '<div class="modal fade" id="modalValidaciones" tabindex="-1" aria-labelledby="exampleModalLabel2" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="CabeceraModalSolicitud"></h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form class="formulario_modal">

                    <div class="input-group-lg mb-3">
                    <label for="TipoArchivo" class="form-label">Tipo de archivo</label>
                        <select class="form-select border-secondary" aria-label="Tipo de archivo" id="TipoArchivo">';

            foreach($arrayDropdown as $dupla){
                $acciones_globales .=  '<option value="'.$dupla['IdArchivoTipo'].'">'.$dupla['Nombre'].'</option>';
            }
            $acciones_globales .= '    </select>
                    </div>
                    <div class="input-group-lg mb-3" style="">
                        <label for="Archivo" class="form-label">Archivo</label>
                        <input type="file" class="form-control border-secondary" id="Archivo" aria-describedby="Archivo" value="" required>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                  <button type="button" class="btn btn-primary" id="SubmitModalForm" onclick="SubmitModalForm(0)">Adjuntar</button>
                </div>
              </div>
            </div>
          </div>';
        }
        //print_r($arrayDoc);
        //print_r($arrayDoc);
        $listaDoc .= "<section>";

        if(count($arrayDoc)>0){

            $listaDoc .=  "<table class='table table-striped table-bordered-bottom' id='cuerpo'>";

            $listaDoc .=  "<tbody id='myTable'>";

            for($dc = 0; ($dc < count($arrayDoc)) && ($dc < $_SESSION["Controlador"] -> miEstado -> puntero_posicion) ; $dc++ ){
                $valor = $arrayDoc[$dc]; 
                $id = $valor["IdArchivo"];
                $descripcion = $valor["Descripcion"];
                $newDate = $valor['Fecha'] -> format('d/m/Y');
                
                //optimizar/cambiar
                if (strlen($descripcion) <= 0) {
                    $descripcion = "Sin Nombre";
                }
                

                if($newDate==date("d/m/Y")){
                    $listaDoc .= "<tr  data-tipo-servicio='$id' id='$id'>";
                }else{
                    $listaDoc .= "<tr data-tipo-servicio='$id' id='$id'>";
                }

                $acciones_mostrar = str_replace(['%id%','%codigo%'], [$id,$descripcion], $acciones_linea);


                $listaDoc .= '<td class="col-7"><div class="Identificador" name="Identificador">' . $descripcion . '<br>';
               
                $listaDoc .= '<td class="col-5"><div class="precio" name="precio" ><b id="importe_documento" style="float:right;">' . $newDate;
                $listaDoc .= '</b><br>'.$acciones_mostrar.'</div></td></tr>';
                //$listaDoc .= '</b><br><a onclick="dibuja_pagina([0,4,['.$id.','.$Origen_impresion.",'".$codigo.".pdf'".']])" >';
                //$listaDoc .= '<img class="pdf_icono" src="Img/descarga_generica.png"></a></div></td></tr>'; 
                }
                $listaDoc .= "</tbody></table></span>";
                $listaDoc .= "<div class='container text-center'>";
                $listaDoc .= "<div class='row'>";
                $listaDoc .= "<div class='col'>";
                //optimizar moverlo a un documento aparte
                if (count($arrayDoc) > $_SESSION["Controlador"] -> miEstado -> puntero_posicion) {
                    $listaDoc .= '<button type="button" class="btn btn-primary mb-3" onclick="dibuja_pagina([0,1])">Ver más...</button>';
                }
        }else{
            $listaDoc .= '<div class="d-flex justify-content-center mt-5 mb-5"><h6> No hay Documentos </h6></div>';
        }


    }
    $listaDoc .= "</div></div></div></div></div></section></div></div></div>";
    $listaDoc .= $acciones_globales;
    return $listaDoc;
}

function pinta_TareasRecursos_Cliente($html){
    $pestana1 = '';
    $pestana2 = '';
    $fases = $_SESSION["Controlador"] -> miEstado -> datosProyecto[0];
    if(count($fases)>0){
        $padre=0;
        for($dc = 0; $dc < count($fases) ; $dc++ ){
            
            $offset = substr_count($fases[$dc]['DireccionArbol'], '#')-1;
            if($offset > 1){
                $pestana1 .= '<div class="collapse multi-collapse show" id="multiCollapse'.$padre.'">';
                //$pestana1 .= '<div class="text-decoration-none" style="margin-left:'. 4*$offset.'em;"><img src="./Img/flechaArbol.png" height="25">'. $fases[$dc]['DireccionArbolNombre'].'</div>'; 
                $pestana1 .= '<div class="text-decoration-none">'. $fases[$dc]['DireccionArbolNombre'].'</div>'; 
                $pestana1 .= '</div>';
            }else{
                $padre +=1;
                //$pestana1 .= '<div  data-bs-toggle="collapse" href="#multiCollapse'.$padre.'" role="button" aria-expanded="false" aria-controls="multiCollapse'.$padre.'"
                //class="text-decoration-none" style="margin-left:'. 4*$offset.'em;"> '.$fases[$dc]['Nombre'].'</div>'; 
                $pestana1 .= '<div  data-bs-toggle="collapse" href="#multiCollapse'.$padre.'" role="button" aria-expanded="false" aria-controls="multiCollapse'.$padre.'"
                class="text-decoration-none" "> '.$fases[$dc]['DireccionArbolNombre'].'</div>'; 
            }
            
            
        }
        
    }else{
        $pestana1 .= '<div class="d-flex justify-content-center mt-5 mb-5"><h6> No hay fases asignadas. </h6></div>';
    }
    $recursos = $_SESSION["Controlador"] -> miEstado -> datosProyecto[1];
    $pestana2 .=  "<table class='table table-bordered-bottom' id='cuerpo'>";
    $pestana2 .=  "<tbody id='myTable'>";
    $pestana2 .= '<thead><tr><th scope="col" class="text-center">Tipo</th>
    <th scope="col" class="text-center">Cantidad T.</th>
    <th scope="col" class="text-center">Precio T.</th>
      <th scope="col" class="text-center"></th>
      <th scope="col" class="text-center">Cantidad</th>
      <th scope="col" class="text-center">Precio</th>
    </tr>
  </thead>';

    if(count($recursos)>0){
        for($dc = 0; $dc < count($recursos) ; $dc++ ){
            $pestana2 .= '<tr><td scope="col" class="text-center">'.$recursos[$dc]['Tipo'].'</td>
            <td scope="col" class="text-center">'.$recursos[$dc]['NumeroTotal'].'</td>
            <td scope="col" class="text-center">'.$recursos[$dc]['PrecioTotal'].'</td>
            <td scope="col" class="text-center">'.$recursos[$dc]['TotalTexto'].'</td>
            <td scope="col" class="text-center">'.$recursos[$dc]['NumeroHechas'].'</td>
            <td scope="col" class="text-center">'.$recursos[$dc]['NumeroTotal'].'</td>
            </tr>';
        }
        $pestana2 .= "</tbody></table></span>";
    }else{
        $pestana2 .= '<div class="d-flex justify-content-center mt-5 mb-5"><h6> No hay Documentos </h6></div>';
    }
    
    $html = str_replace(['%FasesArbol%','%GastosResumen%'], [$pestana1,$pestana2], $html);
    return $html;
}


//Dibujar las lineas de cualquier  del portal de empleado
function DibujaLineas_PortalEmpleado(){
    global $UsuarioAdmin;
    global $otroUsuario;
    global $ModalFirma;
    //optimizar al cambiar la manera de almacenar los documentos y los filtros "array_filter()"
        $arrayDoc = array();
        $conArrayExterno = 0;
        $tipoDocf = 0;
        $acciones_linea = '';
        $acciones_globales = '';
        $pseudoIdentificador = '';
        $conAccionesPersonalizadas = 0;
        $accionModalPersonalizadaOnClick = 0;
        $ListadoAccionesMostar = array();
        //visualizador para luego
        // if($_SESSION["Controlador"] -> miEstado -> acciones["modalVisualizar"] == 1 ){
        //     $arrayForm = array_filter($_SESSION["Controlador"] -> miEstado -> formularios, function ($form) {
        //         return $form["Estado"] == $_SESSION["Controlador"] -> miEstado -> Estado;
        //     });
        //     $arrayIntermedio = array_shift($arrayForm);
        //     $arrayCamposForm = $arrayIntermedio["Campos"];
        //     $acciones_linea .= '<button onclick="cargarModalDetalles(    ,%ValorMostar%)" style="all: initial;cursor: pointer;"><img class="pdf_icono" src="Img/verDetalles.png"></button>'; 
        
        // }
        if($_SESSION["Controlador"] -> miEstado -> acciones["accionCustom1"] == 1){
            $acciones_linea .= ObtenAccionesPersonalizadas();
            //$acciones_globales .= cargaModalesCustom();
            $conAccionesPersonalizadas = 1;
        } 
        if($_SESSION["Controlador"] -> miEstado -> acciones["modalVisualizarLineaCustom"] = 1){
            $accionModalPersonalizadaOnClick = 1;
        }
        if($_SESSION["Controlador"] -> miEstado -> acciones["observaciones"] == 1 ){
            $acciones_linea .= '<button onclick="dibuja_pagina([4.45,%IdProp%])"    style="all: initial;cursor: pointer;"><img class="pdf_icono" src="Img/IconosAcciones/verDetalles.png"></button>';    
        }
        if($_SESSION["Controlador"] -> miEstado -> acciones["archivos"] == 1 ){
            $acciones_linea .= '<button onclick="dibuja_pagina([4.4,%IdProp%])"    style="all: initial;cursor: pointer; padding: 0; border: none; margin: 0;"><img class="pdf_icono" src="%ImgArchivos%"></button>';    
        }
        if($_SESSION["Controlador"] -> miEstado -> acciones["anadirLinea"] == 1){
            //dibuja_pagina([0,3])
            $acciones_globales .= '<button onclick="cargarModalFormularioDinamico()" class="btn_acciones"><img src="Img/Portal_Empleado_Nuevo2.png"></button>';    
            $acciones_globales .= cargaFormularioDinamico2();
            
        }
        if($_SESSION["Controlador"] -> miEstado -> acciones["ModificarLineaCustom"] == 1 && $_SESSION["Controlador"] -> miEstado -> acciones["anadirLineaCustom"] == 0){
            //dibuja_pagina([0,3])

            $acciones_linea .= '<button onclick="cargarModalFormularioDinamico(null,%IdProp%,%arrayValoresResgistroModal%)"    style="all: initial;cursor: pointer; padding: 0; border: none; margin: 0;display:%DispAccionC%;"><img class="pdf_icono" src="Img/IconosAcciones/Observaciones.png"></button>';    
            
        }

        if($_SESSION["Controlador"] -> miEstado -> adjuntarDocumentoFormAutomatico == 1 ){
            $acciones_globales .= obtenerFormularioAdjuntos();
        }

        //Visualizador modal custom 
        if($_SESSION["Controlador"] -> miEstado -> acciones["anadirLineaCustom"] == 1){
            $Nombreform = "";
            switch ($_SESSION["Controlador"] -> miEstado -> Estado) {
                case 6.2:
                    $Nombreform = "'modalFormularioMaterialesProyectos'";
                    break;
                
                default:
                    $Nombreform = "'modalFormularioTiemposProyectos'";
                    break;
            };
            if($_SESSION["Controlador"] -> miEstado -> acciones["ModificarLineaCustom"] == 1 ){
                $acciones_linea .= '<button onclick="cargarModalFormularioDinamico('.$Nombreform.',%IdProp%,%arrayValoresResgistroModal%)"    style="all: initial;cursor: pointer; padding: 0; border: none; margin: 0;display:%DispAccionC%;"><img class="pdf_icono" src="Img/IconosAcciones/Observaciones.png"></button>';    
            }
            //modalFormularioMaterialesProyectos
            $acciones_globales .= '<button onclick="cargarAsistenteIa()" class="btn_acciones2"><img src="Img/Portal_Empleado_Nuevo2IA.png" width="61" height="60"></button>';
            $acciones_globales .= '<button onclick="cargarModalFormularioDinamico('.$Nombreform.')" class="btn_acciones"><img src="Img/Portal_Empleado_Nuevo2.png"></button>';    
            $acciones_globales .= cargaModalesCustom();
        }
        

        if($_SESSION["Controlador"] -> miEstado -> Estado == 4.4){
            //$acciones_linea = '<a href="http://onixsw.esquio.es:8080/Funciones.aspx?ObtenerArchivo=1&pin='.$_SESSION["pinC"].'&IdArchivo=%IdProp%" target="_blank">
            //<img class="pdf_icono" src="%imgTipoArchivo%"></a>';
            $acciones_linea .= '<a onclick="dibuja_pagina([0,11,[%id%,'."'%codigo%'".'],'."'O'".'])" ><img class="pdf_icono" src="%imgTipoArchivo%"></a>';
            //$ListadoAccionesMostar = cargaArrayVisibleLineasEmpleado();
        }
        
        
        
        switch ($_SESSION["Controlador"] -> miEstado -> Estado) {
            case 4.1 :
                $tipoDocf = 1;
                break;
            case 4.3 :
                $tipoDocf = 2;
                break;
            case 4.4 :
                $tipoDocf = 3;
                break;
            case 4.45 :
                $tipoDocf = 3.5;
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
                $pseudoIdentificador = 'descripcion3';
                break;
            case 6 :
                $tipoDocf = 9;
                break;
            case 6.1 :
                $tipoDocf = 9.1;
                $conArrayExterno = 1;
                break;
            case 6.2 :
                $tipoDocf = 9.2;
                $conArrayExterno = 1;
                break;
            case 7 :
                $tipoDocf = 4; 
                break;
            default:
                $tipoDocf = 1;
                break;
        }

        // funcion para aplicar un filtro a cada elemento del array
        $CadenaFiltro = $_SESSION["Controlador"] -> miEstado -> CadenaFiltro;
        if($conArrayExterno == 1 && $CadenaFiltro != null){
            $arrayDoc = array_filter($_SESSION["Controlador"] -> miEstado -> arrayDatosAux, function ($docF) use($CadenaFiltro) {
                $resultado2 = "";
                $resultado3 = "";
                $resultado4 = "";
                $resultadoLateral = "";

                if (isset($docF["descripcion2"]) && $docF["descripcion2"] instanceof DateTime) {
                    $resultado2 = $docF["descripcion2"]->format('d/m/Y');
                } else {
                    $resultado2 = isset($docF["descripcion2"]) ? $docF["descripcion2"] : '';
                }
                if (isset($docF["descripcion3"]) && $docF["descripcion3"] instanceof DateTime) {
                    $resultado3 = $docF["descripcion3"]->format('d/m/Y');
                } else {
                    $resultado3 = isset($docF["descripcion3"]) ? $docF["descripcion3"] : '';
                }
                if (isset($docF["descripcion4"]) && $docF["descripcion4"] instanceof DateTime) {
                    $resultado4 = $docF["descripcion4"]->format('d/m/Y');
                } else {
                    $resultado4 = isset($docF["descripcion4"]) ? $docF["descripcion4"] : '';
                }
                if (isset($docF["descripcionLateral"]) && $docF["descripcionLateral"] instanceof DateTime) {
                    $resultadoLateral = $docF["descripcionLateral"]->format('d/m/Y');
                } else {
                    $resultadoLateral = isset($docF["descripcionLateral"]) ? $docF["descripcionLateral"] : '';
                }
            
                // Comprobación de la cadena de filtro en las descripciones
                return str_contains(
                    strtoupper($docF["Descripcion"].'#'.$docF["descripcion2"].'#'.$resultadoLateral.'#'.$resultado2.'#'.$resultado3.'#'.$resultado4),
                    strtoupper($CadenaFiltro)
                );
            });
        }elseif($conArrayExterno == 1){
            $arrayDoc = $_SESSION["Controlador"] -> miEstado -> arrayDatosAux;
        }elseif($CadenaFiltro != null){
                $arrayDoc = array_filter($_SESSION["Controlador"] -> miEstado -> Documentos, function ($docF) use($tipoDocf,$CadenaFiltro) {
                $resultado2 = "";
                $resultado3 = "";
                $resultado4 = "";
                $resultadoLateral = "";

                if (isset($docF["descripcion2"]) && $docF["descripcion2"] instanceof DateTime) {
                    $resultado2 = $docF["descripcion2"]->format('d/m/Y');
                } else {
                    $resultado2 = isset($docF["descripcion2"]) ? $docF["descripcion2"] : '';
                }
                if (isset($docF["descripcion3"]) && $docF["descripcion3"] instanceof DateTime) {
                    $resultado3 = $docF["descripcion3"]->format('d/m/Y');
                } else {
                    $resultado3 = isset($docF["descripcion3"]) ? $docF["descripcion3"] : '';
                }
                if (isset($docF["descripcion4"]) && $docF["descripcion4"] instanceof DateTime) {
                    $resultado4 = $docF["descripcion4"]->format('d/m/Y');
                } else {
                    $resultado4 = isset($docF["descripcion4"]) ? $docF["descripcion4"] : '';
                }
                if (isset($docF["descripcionLateral"]) && $docF["descripcionLateral"] instanceof DateTime) {
                    $resultadoLateral = $docF["descripcionLateral"]->format('d/m/Y');
                } else {
                    $resultadoLateral = isset($docF["descripcionLateral"]) ? $docF["descripcionLateral"] : '';
                    
                }
            
                // Comprobación de la cadena de filtro en las descripciones
                return $docF["tipoDocPortal"] == $tipoDocf && str_contains(
                    strtoupper($docF["Descripcion"].'#'.$docF["descripcion2"].'#'.$resultadoLateral.'#'.$resultado2.'#'.$resultado3.'#'.$resultado4),
                    strtoupper($CadenaFiltro)
                    
                );
            });
        }else{
            $arrayDoc = array_filter($_SESSION["Controlador"] -> miEstado -> Documentos, function ($docF) use($tipoDocf) {
                return $docF["tipoDocPortal"] == $tipoDocf;
            });
        }

        //filtro por tipo 3 de filtro es decir Tipo de documento//Estado
        //print_r($_SESSION["Controlador"] -> miEstado -> IdsTiposFiltro); 
        if(!empty($_SESSION["Controlador"] -> miEstado -> IdsTiposFiltro)){
            //print_r($_SESSION["Controlador"]->miEstado->IdsTiposFiltro);
            //print_r($arrayDoc);
            
            $arrayDoc = array_filter($arrayDoc, function ($docF) use ($idsTiposFiltroLower) {
                return in_array($docF["Estado"],  $_SESSION["Controlador"]->miEstado->IdsTiposFiltro);
            });
            
        }

        //print_r($arrayDoc);
        //print_r($_SESSION["Controlador"] -> miEstado -> IdTipoPropietario);
        //print_r($_SESSION["Controlador"] -> miEstado -> IdPropietario);
        //sacar los documentos por tipo propietario y el propietario
        if (($tipoDocf == 3 || $tipoDocf == 3.5) && $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario != null && $_SESSION["Controlador"] -> miEstado -> IdPropietario != null){
            $arrayDoc = array_filter($arrayDoc, function ($docF) {
                return $docF["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario
                && $docF["IdPropietario"] ==  $_SESSION["Controlador"] -> miEstado -> IdPropietario;
            });
        }elseif(($tipoDocf == 3 || $tipoDocf == 3.5) && $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario != null){
            $arrayDoc = array_filter($arrayDoc, function ($docF) {
                return $docF["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario;
            });
            
        }elseif($tipoDocf == 8){
        //filtrar los documentos de vacaciones
            $anoActual = date("Y");
            if(isset($_SESSION["Controlador"] -> miEstado -> AnioSV)){
                    $anoActual = $_SESSION["Controlador"] -> miEstado -> AnioSV;   
                }
            if($anoActual != 0){
                $arrayDoc = array_filter($arrayDoc, function ($docF) use($anoActual) {  
                    return $docF["AñoV"] == $anoActual;
                });
            }
        }
        
        // funcion para reinedxarlo 
        
        $arrayDoc = array_values($arrayDoc);
        $listaDoc = "";
        $listaDoc .= "<section>";
        $ListadoAccionesMostar = cargaArrayVisibleLineasEmpleado();

        if(count($arrayDoc)>0){
            
            $listaDoc .=  "<table class='table table-striped table-bordered-bottom' id='cuerpo'>";

            $listaDoc .=  "<tbody id='myTable'>";
            for($dc = 0; $dc < count($arrayDoc) ; $dc++ ){
                $valor = $arrayDoc[$dc];
                $descripcion = $valor["Descripcion"];
                $descripcion2 = $valor["descripcion2"];
                $descripcion3 = '';
                $descripcion4 = '';
                $descripcion_icono = '';
                $color = '';
                if(isset($valor["ColorPestana"])){
                    $color =  $valor["ColorPestana"];
                }elseif( isset($valor["color"])){
                    $color =  $valor["color"];
                }

                
                if(isset($valor["descripcion3"])){
                    $descripcion3 =  $valor["descripcion3"].'<br>';
                }
                if(isset($valor["descripcion4"])){
                    $descripcion4 =  $valor["descripcion4"];
                }
            
                $id = $valor["id"];
                
                try {
                    if(isset($valor["descripcionLateral"])){
                        $descripcionLateral = $valor["descripcionLateral"] -> format('d/m/Y');
                    }else{
                        $descripcionLateral = '';
                    }
                } catch (\Throwable $th) {
                    $descripcionLateral = $valor["descripcionLateral"];
                }
                try {
                    if(isset($valor["FechaFin"])){
                        $FechaFin = $valor["FechaFin"] -> format('d/m/Y');
                    }
                } catch (\Throwable $th) {
                    $FechaFin = '';
                }
                    
                //$color = $valor["color"];


                //optimizar/cambiar
                if (strlen($descripcion ?? '') <= 0) {
                    $descripcion = "Sin descripción";
                }
                //añadir a las acciones el id del propietario y el tipo
                $acciones_linea_pintar = str_replace('%IdProp%',$id,$acciones_linea);
                if ($color =='#0e860e' && $_SESSION["Controlador"] -> miEstado -> acciones["ModificarLineaCustom"] == 1){
                    $acciones_linea_pintar = str_replace('%DispAccionC%','none',$acciones_linea_pintar);
                }else{
                    $acciones_linea_pintar = str_replace('%DispAccionC%','',$acciones_linea_pintar);
                    $parametrosValoresMostrar = array();

                    foreach ($ListadoAccionesMostar as $key) {
                        if (isset($valor[$key])) {
                            $valorElemento = $valor[$key];
                            if ($valorElemento instanceof DateTime) {
                                $valorElemento = $valorElemento->format('Y-m-d H:i:s');
                            }

                            //$valorEscapado = str_replace("'", "\\'", strval($valorElemento));
                            array_push($parametrosValoresMostrar, "'" . $valorElemento . "'");


                        }
                    }

                    $llamadaJS = "[" . implode(",", $parametrosValoresMostrar) . "]";
                    $acciones_linea_pintar = str_replace('%arrayValoresResgistroModal%',$llamadaJS,$acciones_linea_pintar);
                    
                }
                //elegir el color de la carpeta segun tenga archivos
                if($_SESSION["Controlador"] -> miEstado -> acciones["archivos"] == 1 ){
                    $imgRutaCarpeta = '';
                    $imgRutaCarpeta = 'Img/Documentos2.png';
                    //print_r($carperta);
                    if(isset($valor["NumeroArchivos"]) && $valor["NumeroArchivos"]>0){
                        $imgRutaCarpeta = 'Img/Documentos_verde2.png';
                    }
                    $acciones_linea_pintar = str_replace('%ImgArchivos%',$imgRutaCarpeta,$acciones_linea_pintar);
                }
                // incluir la accion de abrir el formulario de firma
                if(isset($valor["Firmable"])){
                    
                    $ModalFirma = 1;
                }
                
                if(isset($valor["Firmable"]) && $valor["Firmable"] === 1 && (!isset($valor["Firmado"]) || $valor["Firmado"] == 0 ) && !($UsuarioAdmin && $otroUsuario)){
                    $tipoD;
                    if($_SESSION["Controlador"] -> miEstado -> IdTipoPropietario == 30){
                        $tipoD = 1;
                    }else{
                        $tipoD = 0;
                    }
                    $acciones_linea_pintar .= '<button  onclick="cargarModalFirma('.$valor["id"].','.$tipoD.
                    ','.$_SESSION["Controlador"] -> miEstado -> IdPropietario.
                    ','.$_SESSION["Controlador"] -> miEstado -> IdTipoPropietario.
                    ','.$_SESSION["Controlador"] -> miEstado -> id_sociedad.
                    ','."'".$_SESSION["Controlador"] -> miEstado -> IP."'".
                    ','."'".$_SESSION["Controlador"] -> miEstado -> bbdd."'".
                    ')"  style="all: initial;cursor: pointer;" >
                                        <img  src="Img/firma2.png"></button>';
                }elseif(isset($valor["Firmado"]) && $valor["Firmado"] != 0 ){
                    $iconoFirmado = '<img src="Img/DocumentoFirmado.png" height="25px" style="margin-left:15px;">';
                    $descripcion_icono = $descripcion.$iconoFirmado;
                }
                
                

                //elegir el icono
                if( $_SESSION["Controlador"] -> miEstado -> Estado == 4.4 ){
                    $imgExtension = 'Img/descarga_generica.png';
                    $file_name = $valor["Documento"];
                    $temp = explode('.',$file_name);
                    $extension = strtoupper(end($temp));
                    
                    switch ($extension) {
                        case 'PDF':
                            $imgExtension = 'Img/descarga_pdf.png';
                            break;
                        case 'DOC':
                            $imgExtension = 'Img/descarga_word.PNG';
                            break;
                        case 'DOCX':
                            $imgExtension = 'Img/descarga_word.PNG';
                            break;
                        case 'XLS':
                            $imgExtension = 'Img/descarga_excell.png';
                            break;
                        case 'XLSX':
                            $imgExtension = 'Img/descarga_excell.png';
                            break;
                        default:
                            if(in_array($extension,array('JPG','JPEG','PNG','GIF','WEBP','APNG','AVIF','SVG','WEBB','TIFF','ICO'))){
                                $imgExtension = 'Img/descarga_IMG.png';
                            }else{
                                $imgExtension = 'Img/descarga_generica.png';         
                            }
                            
                            break;
                    } 
                    $acciones_linea_pintar = str_replace('%imgTipoArchivo%',$imgExtension,$acciones_linea_pintar);
                    $acciones_linea_pintar = str_replace('%id%',$id,$acciones_linea_pintar);
                    $acciones_linea_pintar = str_replace('%codigo%',$descripcion,$acciones_linea_pintar);
                }
                
                if($conAccionesPersonalizadas == 1){
                   
                    $acciones_linea_pintar = PintaArrayValuesSelect($id,$acciones_linea_pintar);
                }
                $pseudoI = '';
                
                if($descripcion_icono == ''){
                    $descripcion_icono = $descripcion;
                }
                //data-tipo-servicio="'.$id.'"
                //pintar cada linea
                $listaDoc .= '<tr  id="'.$id.'" data-nombre="'.$pseudoI.'">';
                $listaDoc .= '<td class="col-7"><div class="Identificador" name="Identificador"><h5>'. $descripcion_icono.'<h5>';
                if($_SESSION["Controlador"] -> miEstado -> acciones["SinDesplegable"] == 0){
                    if($_SESSION["Controlador"] -> miEstado -> acciones["desplegado"] == 0){
                        $listaDoc .= '<details>';
                        
                    }else{
                        $listaDoc .= '<details open>';
                        
                    }
                    $listaDoc .='<summary></summary>';
                    $listaDoc .='<p style="font-size: smaller; font-weight: normal;"> <span style="color:'.$color.';">' . $descripcion2 . '</span><br>';
                    $listaDoc .= $descripcion3 . $descripcion4.'</p>';
                    if($_SESSION["Controlador"] -> miEstado -> acciones["desplegado"] == 0){
                        $listaDoc .= '</details>';
                    }
                }

                $listaDoc .= '</div></td><td class="col-5"><div id="fecha_a" class="d-flex align-items-end flex-column" style="float:right;color:'.$color.';"><h5>'. $descripcionLateral.'<h5>';
                $listaDoc .= '<div class="mt-1">' . $acciones_linea_pintar . '</div>';
                $listaDoc .= '</div></td></tr>'; 
            }
            $listaDoc .= "</tbody></table></span>";
            // $listaDoc .= "<div class='container text-center'>";
            // $listaDoc .= "<div class='row'>";
            // $listaDoc .= "<div class='col'>"; this is a continiation
            //
            //optimizar moverlo a un documento aparte
            // if (count($arrayDoc) > $_SESSION["Controlador"] -> miEstado -> puntero_posicion) {
            //     $listaDoc .= '<button type="button" class="btn btn-primary mb-3" onclick="dibuja_pagina([0,1])">Ver más...</button>';
            // }

        }elseif($_SESSION["Controlador"] -> miEstado -> Estado == 4.4){
            $listaDoc .= '<div class="d-flex justify-content-center mt-5 mb-5" ><h5>No hay Documentos</h5></div>';
        }else{
            $listaDoc .= '<div class="d-flex justify-content-center mt-5 mb-5"><h5> No hay elementos </h5></div>';
        }
        
        $listaDoc .= "</section>";
        $listaDoc .= $acciones_globales;
        
        

        return $listaDoc;
}

function DibujaPestanaVacaciones_Empleado($html){
    $anoActual = date("Y");
    $anoActualtxt ='';
    if(isset($_SESSION["Controlador"] -> miEstado -> AnioSV) && $_SESSION["Controlador"] -> miEstado -> AnioSV == 0 ){
        $anoActual = $_SESSION["Controlador"] -> miEstado -> AnioSV;
        $anoActualtxt = 'Todos los años';
    }elseif(isset($_SESSION["Controlador"] -> miEstado -> AnioSV)){
        $anoActual = $_SESSION["Controlador"] -> miEstado -> AnioSV;
        $anoActualtxt = 'Año '.$anoActual;
    }
        
    $lineas_PE =  DibujaLineas_PortalEmpleado();
        $graficoV =  '<div class="chart-container">
                    <canvas id="nav-graficos"></canvas>
                </div>';    
        
    $desplegableAnos = '<button class="btn dropdown-toggle" data-nombre="'.$anoActual.'" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        '.$anoActualtxt.'</button>';
    $desplegableAnos .= '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';
    $desplegableAnos .= '<button class="dropdown-item" onclick="dibuja_pagina([0,12,0])">Todos los años</button>';
    foreach($_SESSION["Controlador"] -> miEstado -> listaAnoFiltroVacaciones as $anoVacacion){
        if($anoVacacion != $anoActual){
            $desplegableAnos .= '<button class="dropdown-item" onclick="dibuja_pagina([0,12,'.$anoVacacion.'])">'.$anoVacacion.'</button>';
        }else{
            //$desplegableAnos .= '<button class="dropdown-item" onclick="generarGrafico_Barras(calendar,'.$anoVacacion.')" selected>'.$anoVacacion.'</button>';
        }
        
    }
    $desplegableAnos .= '</div>';
    $html = str_replace(['%ListaV%','%GraficoV%','%anoActualDropdown%'], [$lineas_PE,$graficoV,$desplegableAnos], $html);
    return $html;
}   

function cargaFiltros(){
    $arrayFiltros = array();
    $tipoDocf = 0;
        switch ($_SESSION["Controlador"] -> miEstado -> Estado) {
            case 4 :
                $tipoDocf = 1;
                break;
            case 5 :
                $tipoDocf = 2;
                break;
            case 6 :
                $tipoDocf = 3;
                break;
            case 7 :
                $tipoDocf = 4;
                break;
            default:
                $tipoDocf = 0;
                break;
        }
    // funcion para aplicar un filtro a cada elemento del array
    
    $arrayFiltros = array_filter($_SESSION["Controlador"] -> miEstado -> FiltrosDoc, function ($docF) use($tipoDocf) {
        return $docF["tipo"] == $tipoDocf;
    });
    
    // funcion para reinedxarlo %headerCliente% 
    $arrayFiltros = array_values($arrayFiltros);
    $txt_filtros = '<span id="filtros_dinamicos">';
    if(count($arrayFiltros)>0){
        foreach($arrayFiltros as $valor){ 
            $txt_filtros .= '<button onclick="aplicafiltros('."'".$valor["Estado"]."'".')" style="color:black;" class="dropdown-item" id="'.$valor["Estado"].'" >'.$valor["Filtro"].'</button>';
        }
    }
    return $txt_filtros;
}

function cargaArrayVisibleLineasEmpleado(){
     $arrayCamposFormMostrar = array();
    if($_SESSION["Controlador"] -> miEstado ->Estado == 6.1){
        $arrayCamposFormMostrar = ['IdTipoTarea',
        'IdProyectoTarea',
        'FechaInicio',
        'FechaFin'];
    }elseif($_SESSION["Controlador"] -> miEstado ->Estado == 6.2){
        $arrayCamposFormMostrar = ['IdProyectoTarea',
        'IdProyectoMaterialTipo', 
        'IdArticulo' ,
        'Descripcion', 
        'Cantidad',
        'Coste',
        'Fecha'];
    }else{
        $arrayForm = array_filter($_SESSION["Controlador"] -> miEstado -> formularios, function ($form) {
            return $form["Estado"] == floatval($_SESSION["Controlador"] -> miEstado -> Estado);
        });
        

        $arrayIntermedio = array_shift($arrayForm);
        $arrayCamposForm = $arrayIntermedio["Campos"];

        //Extraer los dropdowns que se van a usar
        if(!empty($arrayCamposForm)){
            $arraydropdown = array_filter($_SESSION["Controlador"] -> miEstado -> dropdownsFormularios, function ($filtro) use($arrayCamposForm){
                return in_array($filtro["IdTipoDefinicion"],array_column($arrayCamposForm, "IdTipoDefinicion")); 
            });
            
        }
        
        //extraer el dropdown de Archivostipos
        if($_SESSION["Controlador"] -> miEstado -> Estado == 4.4){
            $arraydropdown = array_filter($_SESSION["Controlador"] -> miEstado -> archivostiposAccesos, function ($campos) {
                return $campos["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario;
            });
        }


        $PlantillaModal = $_SESSION["PlantillaModalForm"];
        

        $formularioConArchivos = false;
        foreach($arrayCamposForm as $campo){
            if( $campo["OUTPUT"] == 0 && $campo["Mostrar"] == 1){
                array_push($arrayCamposFormMostrar,$campo["Variable"]);
            }
        }
    }
    //var_dump($arrayCamposFormMostrar);
    return $arrayCamposFormMostrar;
}


function cargarJornadaHistorico(){
    $arrayJ = $_SESSION["Controlador"] -> miEstado -> HistoricoJornada;
    $arrayJ = array_values($arrayJ);
    $listaJ = "";
    $listaJ .= "<div class='row h-100 justify-content-center mt-5 mt-md-5' id='no-margin'>";
    $listaJ .= '<div class=" col-12 col-md-9"  id="no-padding">';
    $listaJ .= '<div class="card shadow-2-strong shadow pb-5 col-12">';
    if(count($arrayJ)>0){
        
        $listaJ .=  "<table class='table table-striped table-bordered-bottom' id='cuerpo'>";
        $listaJ .=  "<tbody id='myTable'>";
        for($dc = 0; $dc < count($arrayJ) ; $dc++ ){
           
            $valor = $arrayJ[$dc];
            $hora = $valor["HoraInicioFin"];
            $duracion = $valor["DuracionHoras"];
            // if($valor["DuracionHoras"] !== null){
            //     // $duracion = round($valor["DuracionHoras"],2) ;
            //     // $duracion_s = $duracion * 3600;
            //     // $horas = floor($duracion_s / 3600);
            //     // $minutos = floor(($duracion_s % 3600) / 60);
            //     $longlat_entrada;
            //     $longlat_salida;
            //     if($minutos<10){
            //         $minutos = '0'.$minutos;
            //     }
            //     $duracion = $horas . ':' . $minutos . ' h'; 
            // }else{
            //     $duracion = 'En Jornada' ;
            // }
           
            try {
                if(isset($valor["FechaInicio"])){
                    $FechaInicio = $valor["FechaInicio"] -> format('d/m/Y');
                }else{
                    $FechaInicio = '';
                }
            } catch (\Throwable $th) {
                $FechaInicio = '';
            }
            try {
                if(isset($valor["DiaIto"])){
                    $dia = $valor["DiaIto"] ;
                }else{
                    $dia = '';
                }
            } catch (\Throwable $th) {
                $dia = '';
            }

            try {
                if(isset($valor["FechaFin"])){
                    $FechaFin = $valor["FechaFin"] -> format('d/m/Y');
                }
            } catch (\Throwable $th) {
                $FechaFin = '';
            }
            $acciones_linea_pintar = '';
            if(isset( $valor['LongLat_Entrada'])){
                $longlat_entrada = explode(",", $valor['LongLat_Entrada']);
                $acciones_linea_pintar .= '<button onclick="mostrarMapaJornadafichaje('.$longlat_entrada[0].','.$longlat_entrada[1].')" class="flecha_volver align-self-end"><img src="Img/UbicacionEntrada.png" alt="BotonVerMapaJornada"></button>';
                //$acciones_linea_pintar .= '<a href="https://www.google.com/maps/place/'.$longlat_entrada[0].','.$longlat_entrada[1].'" target="_blank"><img src="Img/UbicacionEntrada.png"></a>';
            }   
            if(isset( $valor['LongLat_Salida'])){
                $longlat_salida = explode(",", $valor['LongLat_Salida']);
                $acciones_linea_pintar .= '<button onclick="mostrarMapaJornadafichaje('.$longlat_salida[0].','.$longlat_salida[1].')" class="flecha_volver align-self-end"><img src="Img/UbicacionSalida.png" alt="BotonVerMapaJornada"></button>';
                //$acciones_linea_pintar .= '<a href="https://www.google.com/maps/place/'.$longlat_salida[0].','.$longlat_salida[1].'" target="_blank"><img src="Img/UbicacionSalida.png" style="margin-left: 5px;"></a>';
            }
            $color = '';   
            if(str_contains($duracion,'Jornada')){
                $color = 'color:Green;';
            }
            
            
            $listaJ .= '<tr>';
            $listaJ .= '<td class="col-7"><div class="Identificador" name="Identificador">' . $dia. '<br><details><summary></summary>';
            //$listaJ .='<p><b>' . $hora . '</b><br>';
            $listaJ .= '<span class="descripcion_doc">'.$hora .'</span></p></details></div></td></b>';
            $listaJ .= '<td class="col-5"><div id="fecha_a" class="d-flex align-items-end flex-column" style="float:right;'.$color.'"><b>'. $duracion.'</b>';
            $listaJ .= '<div class="mt-1">' . $acciones_linea_pintar . '</div>';
            $listaJ .= '</div></td></tr>';
        }
        $listaJ .= "</tbody></table>";
        }else{
            $listaJ .= '<div class="d-flex justify-content-center mt-5 mb-5"><h4> No hay elementos </h4></div>';
        }
        $listaJ.='</div></div></div>';
        return $listaJ;
}

//pintar los formularios dinamicamente //*DEPRECATED*//
function cargaFormularioDinamico(){

    //extraer los campos dinamicamente del JSON

    $arrayForm = array_filter($_SESSION["Controlador"] -> miEstado -> formularios, function ($form) {
        return $form["Estado"] == $_SESSION["Controlador"] -> miEstado -> Estado;
    });
    $arrayIntermedio = array_shift($arrayForm);
    $arrayCamposForm = $arrayIntermedio["Campos"];
    
    //Extraer los dropdowns que se van a usar
    $arraydropdown = array_filter($_SESSION["Controlador"] -> miEstado -> dropdownsFormularios, function ($filtro) use($arrayCamposForm){
        return in_array($filtro["IdTipoDefinicion"],array_column($arrayCamposForm, "IdTipoDefinicion")); 
    });
    
    //extraer el dropdown de Archivostipos
    if($_SESSION["Controlador"] -> miEstado -> Estado == 4.4){
        $arraydropdown = array_filter($_SESSION["Controlador"] -> miEstado -> archivostiposAccesos, function ($campos) {
            return $campos["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario;
        });
    }
    //print_r($_SESSION["Controlador"] -> miEstado -> Documentos);
    $camposForm = "";
    $camposForm = "<section class='row justify-content-center'>";
    $camposForm .= "<form %tipoForm% >";
    $formularioConArchivos = false;
    foreach($arrayCamposForm as $campo){
        if($campo["TipoDatoHtml"] == 'file'){
            $formularioConArchivos = true;
        }
        
        if($campo["SelectorDesplegable"] == 0 && $campo["OUTPUT"] == 0 && $campo["Mostrar"] == 1){
            // print_r($campo);'.($campo["Mostrar"] == 1 ? "" : 'display:none;').'
            
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style="'.($campo["Mostrar"] == 1 ? "" : 'display:none;').'">';
            $camposForm .= '<label for="'.$campo["Nombre_campo"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<input type="'.$campo["TipoDatoHtml"].'" class="form-control border-secondary" id = "'.$campo["Nombre_campo"].'" aria-describedby="'.$campo["Nombre_campo"].'"'
            .($campo["VariableAlmacenada"] != null ? ' value = "'. $_SESSION["Controlador"] -> miEstado -> {$campo["VariableAlmacenada"]}.'"' : ' value = "'. $campo["ValorPorDefecto"].'"').' '.($campo["Required"] == 1 ? "required" : "").'>';
            //$camposForm .= '<div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>''
            $camposForm .= '</div><br>';
        }elseif($campo["Mostrar"] == 1 && $campo["SelectorDesplegable"] == 1 && isset($campo["IdTipoDefinicion"]) && $campo["Variable"] != "IdArchivosTipo"){
            //filtrar el select
            $arraydropdownfiltrado = array_filter($arraydropdown, function ($valorDropdown) use($campo) {
                return $valorDropdown["IdTipoDefinicion"] == $campo["IdTipoDefinicion"];
            });
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="'.$campo["Nombre_campo"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="'.$campo["Nombre_campo"].'" id="'.$campo["Nombre_campo"].'">';
            $existeSelecion = 0;
            foreach($arraydropdownfiltrado as $valorSelecionable){
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['IdTipo'].'" selected>'.$valorSelecionable['Nombre'].'</option>';
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['IdTipo'].'">'.$valorSelecionable['Nombre'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div><br>';
        }elseif($campo["Variable"] == "IdArchivosTipo" && $campo["Mostrar"] == 1 && $campo["SelectorDesplegable"] == 1){
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="'.$campo["Nombre_campo"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="'.$campo["Nombre_campo"].'" id="'.$campo["Nombre_campo"].'">';
            $existeSelecion = 0;
            foreach($arraydropdown as $valorSelecionable){
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['IdArchivoTipo'].'" selected>'.$valorSelecionable['Nombre'].'</option>';
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['IdArchivoTipo'].'">'.$valorSelecionable['Nombre'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div><br>';
        }
        
    }

    $camposForm .= '<br><div class="bg-light"><br><br><br></div>';
    $camposForm .= '<div class="row col-11 justify-content-center">';
    $camposForm .= '<button class="col-5 col-lg-3 offset-1 btn  mt-3" onclick="dibuja_pagina([0,3,0])"  onmouseover="this.style.backgroundColor='."'#d6d5d3'".'" onmouseout="this.style.backgroundColor='."'#ffffff'".'";><h3>Cancelar</h3></button>';
    $camposForm .= '<button class="col-5 col-lg-3 offset-1 btn  mt-3" onclick="" type="submit" style="color:#0265bd;" onmouseover="this.style.backgroundColor='."'#d6d5d3'".'" onmouseout="this.style.backgroundColor='."'#ffffff'".'";><h3>Guardar</h3></button>';
    //$camposForm .= '<DIV>'.$valor['FechaInicio'].'</div>';
    //$camposForm .= '<button class="btn btn-primary" type="submit">Submit form</button>';
    $camposForm .= "</div><br>";
    $camposForm .= "</form></section></div></div></div>";

    if($formularioConArchivos == true){
        $camposForm = str_replace("%tipoForm%",'enctype="multipart/form-data" class="formulario_Dinamico_Archivos col-10 col-md-12 mt-3"',$camposForm);
    }else{
        $camposForm = str_replace("%tipoForm%","class=' formulario_Dinamico col-10 col-md-12 mt-3'",$camposForm);
        
    }

    return $camposForm;
    /////////////////////
}


//Pintar los formularios modales dinamicamente
function cargaFormularioDinamico2(){
    $TituloModal = '';
    $TituloModalModificacion = '';
    switch ($_SESSION["Controlador"] -> miEstado -> Estado) {
        case 4.1 :
            $TituloModal = 'Nueva Asistencia';
            $TituloModalModificacion = 'Modificar Asistencia';
            break;
        case 4.4 :
            $TituloModal = 'Nuevo Documento';
            $TituloModalModificacion = 'Modificar Documento';
            break;
        case 4.5:
            $TituloModal = 'Nueva Formación';
            $TituloModalModificacion = 'Modificar Formación';
            break;
        case 4.6:
            $TituloModal = 'Nueva Incidencia';
            $TituloModalModificacion = 'Modificar Incidencia';
            break;
        case 4.7:
            $TituloModal = 'Nuevo Material';
            $TituloModalModificacion = 'Modificar Material';
            break;
        case 4.9:
            $TituloModal = 'Solicitud Vacaciones';
            break;
        default:
            $TituloModal = '';
            break;
    }

    //print_r($TituloModal);

    $arrayForm = array_filter($_SESSION["Controlador"] -> miEstado -> formularios, function ($form) {
        return $form["Estado"] == floatval($_SESSION["Controlador"] -> miEstado -> Estado);
    });

    $arrayIntermedio = array_shift($arrayForm);
    $arrayCamposForm = $arrayIntermedio["Campos"];

    //Extraer los dropdowns que se van a usar
    if(!empty($arrayCamposForm)){
        $arraydropdown = array_filter($_SESSION["Controlador"] -> miEstado -> dropdownsFormularios, function ($filtro) use($arrayCamposForm){
            return in_array($filtro["IdTipoDefinicion"],array_column($arrayCamposForm, "IdTipoDefinicion")); 
        });
        
    }
    
    //extraer el dropdown de Archivostipos
    if($_SESSION["Controlador"] -> miEstado -> Estado == 4.4){
        $arraydropdown = array_filter($_SESSION["Controlador"] -> miEstado -> archivostiposAccesos, function ($campos) {
            return $campos["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario;
        });
    }
    $camposForm = '';
    //GENERAR LA VARIABLE DE MODAL
    if(!isset($_SESSION["PlantillaModalForm"])){
        $fileModal = fopen('../html/PlantillaModalFormulario.html', "r");
        $filesizeModal = filesize('../html/PlantillaModalFormulario.html');
        $_SESSION["PlantillaModalForm"] = fread($fileModal, $filesizeModal);
        fclose($fileModal);
    }
    $PlantillaModal = $_SESSION["PlantillaModalForm"];
    

    $formularioConArchivos = false;
    foreach($arrayCamposForm as $campo){
        if($campo["TipoDatoHtml"] == 'file'){
            $formularioConArchivos = true;
        }
        
        if($campo["SelectorDesplegable"] == 0 && $campo["OUTPUT"] == 0 && $campo["Mostrar"] == 1){
            // print_r($campo);'.($campo["Mostrar"] == 1 ? "" : 'display:none;').'
            
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style="'.($campo["Mostrar"] == 1 ? "" : 'display:none;').'">';
            $camposForm .= '<label for="'.$campo["Variable"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<input type="'.$campo["TipoDatoHtml"].'" class="form-control border-secondary" id = "'.$campo["Variable"].'" aria-describedby="'.$campo["Nombre_campo"].'"'
            .($campo["VariableAlmacenada"] != null ? ' value = "'. $_SESSION["Controlador"] -> miEstado -> {$campo["VariableAlmacenada"]}.'"' : ' value = "'. $campo["ValorPorDefecto"].'"').' '.($campo["Required"] == 1 ? "required" : "").'>';
            //tremenda fumada
            if(isset($campo["Condicion"])&& $campo["Condicion"] != null){
            $camposForm .='<div class=" d-none" id="'.$campo["Variable"].'_condiciones">'.$campo["Condicion"].'</div>';
            }
            
            $camposForm .='<div class="text-danger d-none" id="'.$campo["Variable"].'_msgError">'.$campo["MsgError"].'</div>';
            $camposForm .= '</div><br>';
        }elseif($campo["Mostrar"] == 1 && $campo["SelectorDesplegable"] == 1 && isset($campo["IdTipoDefinicion"]) && $campo["Variable"] != "IdArchivosTipo"){
            //filtrar el select
            $arraydropdownfiltrado = array_filter($arraydropdown, function ($valorDropdown) use($campo) {
                return $valorDropdown["IdTipoDefinicion"] == $campo["IdTipoDefinicion"];
            });
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="'.$campo["Variable"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="'.$campo["Nombre_campo"].'" id="'.$campo["Variable"].'">';
            $existeSelecion = 0;
            foreach($arraydropdownfiltrado as $valorSelecionable){
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['IdTipo'].'" selected>'.$valorSelecionable['Nombre'].'</option>';
                        
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['IdTipo'].'">'.$valorSelecionable['Nombre'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div><br>';
        }elseif($campo["Variable"] == "IdArchivosTipo" && $campo["Mostrar"] == 1 && $campo["SelectorDesplegable"] == 1){
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="'.$campo["Variable"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="'.$campo["Nombre_campo"].'" id="'.$campo["Variable"].'">';
            $existeSelecion = 0;
            foreach($arraydropdown as $valorSelecionable){
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['IdArchivoTipo'].'" selected>'.$valorSelecionable['Nombre'].'</option>';
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['IdArchivoTipo'].'">'.$valorSelecionable['Nombre'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div><br>';
        }elseif($campo["OUTPUT"] == 0 && $campo["Mostrar"] == 2){

            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style="display:none;">';
            $camposForm .= '<label for="'.$campo["Variable"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<input type="'.$campo["TipoDatoHtml"].'" class="form-control border-secondary" id = "'.$campo["Variable"].'" aria-describedby="'.$campo["Nombre_campo"].'"'
            .($campo["VariableAlmacenada"] != null ? ' value = "'. $_SESSION["Controlador"] -> miEstado -> {$campo["VariableAlmacenada"]}.'"' : ' value = "'. $campo["ValorPorDefecto"].'"').' '.($campo["Required"] == 1 ? "required" : "").'>';
            //tremenda fumada
      
            
            $camposForm .='<div class="text-danger d-none" id="'.$campo["Variable"].'_msgError">'.$campo["MsgError"].'</div>';
            $camposForm .= '</div><br>';
        }
    

        }

        if($formularioConArchivos == true){
        $camposForm = str_replace(["%tipoForm%",'%BodyModal%','%TituloModal%'],['enctype="multipart/form-data"',$camposForm,$TituloModal],$PlantillaModal);
        //$camposForm = str_replace("%tipoForm%",'enctype="multipart/form-data" class="formulario_Dinamico_Archivos col-10 col-md-12 mt-3"',$camposForm);
    }else{
        $camposForm = str_replace(["%tipoForm%",'%BodyModal%','%TituloModal%','%TituloModalModificacion%'],['',$camposForm,$TituloModal,$TituloModalModificacion],$PlantillaModal);
        
    }

    return $camposForm;
}

//funcion para poder adjuntar sin rayarme mucho la cabez
function obtenerFormularioAdjuntos(){
    $TituloModal = 'Nuevo Adjunto';
    $arrayForm = array_filter($_SESSION["Controlador"] -> miEstado -> formularios, function ($form) {
        return $form["Estado"] == 4.4;
    });
    $arrayIntermedio = array_shift($arrayForm);
    $arrayCamposForm = $arrayIntermedio["Campos"];

    
    $arraydropdown = array_filter($_SESSION["Controlador"] -> miEstado -> archivostiposAccesos, function ($campos) {
        return $campos["IdTipoPropietario"] == $_SESSION["Controlador"] -> miEstado -> IdTipoPropietario;
    });


    
    $camposForm = '';
    //GENERAR LA VARIABLE DE MODAL
    if(!isset($_SESSION["PlantillaModalForm"])){
        $fileModal = fopen('../html/PlantillaModalFormulario.html', "r");
        $filesizeModal = filesize('../html/PlantillaModalFormulario.html');
        $_SESSION["PlantillaModalForm"] = fread($fileModal, $filesizeModal);
        fclose($fileModal);
    }
    $PlantillaModal = $_SESSION["PlantillaModalForm"];
    $PlantillaModal = str_replace("modalFormularioDinamico","modalFormularioDinamicoAdjuntar",$PlantillaModal);
    

    $formularioConArchivos = false;
    foreach($arrayCamposForm as $campo){
        if($campo["TipoDatoHtml"] == 'file'){
            $formularioConArchivos = true;
        }
        
        if($campo["SelectorDesplegable"] == 0 && $campo["OUTPUT"] == 0 && $campo["Mostrar"] == 1){
            // print_r($campo);'.($campo["Mostrar"] == 1 ? "" : 'display:none;').'
            
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style="'.($campo["Mostrar"] == 1 ? "" : 'display:none;').'">';
            $camposForm .= '<label for="'.$campo["Variable"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<input type="'.$campo["TipoDatoHtml"].'" class="form-control border-secondary" id = "'.$campo["Variable"].'" aria-describedby="'.$campo["Nombre_campo"].'"'
            .($campo["VariableAlmacenada"] != null ? ' value = "'. $_SESSION["Controlador"] -> miEstado -> {$campo["VariableAlmacenada"]}.'"' : ' value = "'. $campo["ValorPorDefecto"].'"').' '.($campo["Required"] == 1 ? "required" : "").'>';
            //tremenda fumada
            if(isset($campo["Condicion"])&& $campo["Condicion"] != null){
                $camposForm .='<div class=" d-none" id="'.$campo["Variable"].'_condiciones">'.$campo["Condicion"].'</div>';
            }
            
            $camposForm .='<div class="text-danger d-none" id="'.$campo["Variable"].'_msgError">'.$campo["MsgError"].'</div>';
            $camposForm .= '</div><br>';
        }elseif($campo["Variable"] == "IdArchivosTipo" && $campo["Mostrar"] == 1 && $campo["SelectorDesplegable"] == 1){
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="'.$campo["Variable"].'" class="form-label">'.$campo["Nombre_campo"].'</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="'.$campo["Nombre_campo"].'" id="'.$campo["Variable"].'">';
            $existeSelecion = 0;
            foreach($arraydropdown as $valorSelecionable){
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['IdArchivoTipo'].'" selected>'.$valorSelecionable['Nombre'].'</option>';
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['IdArchivoTipo'].'">'.$valorSelecionable['Nombre'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div><br>';
        }
    

        }

        
        $camposForm = str_replace(["%tipoForm%",'%BodyModal%','%TituloModal%'],['enctype="multipart/form-data"',$camposForm,$TituloModal],$PlantillaModal);
        //$camposForm = str_replace("%tipoForm%",'enctype="multipart/form-data" class="formulario_Dinamico_Archivos col-10 col-md-12 mt-3"',$camposForm);
        $camposForm = str_replace('SubmitModalForm(1)','SubmitModalForm(1,null,1,modalFormularioDinamicoAdjuntar)',$camposForm);

    return $camposForm;
}



function cargaModalesCustom(){
    global $UsuarioAdmin;
    global $otroUsuario;
    if(!isset($_SESSION["PlantillaModalForm"])){
        $fileModal = fopen('../html/PlantillaModalFormulario.html', "r");
        $filesizeModal = filesize('../html/PlantillaModalFormulario.html');
        $_SESSION["PlantillaModalForm"] = fread($fileModal, $filesizeModal);
        fclose($fileModal);
    }
    if(!isset($_SESSION["PlantillaForm"])){
        $fileModal = fopen('../html/PlantillaModal.html', "r");
        $filesizeModal = filesize('../html/PlantillaModal.html');
        $_SESSION["PlantillaForm"] = fread($fileModal, $filesizeModal);
        fclose($fileModal);
    }
    
    $PlantillaModal = $_SESSION["PlantillaModalForm"];
    $PlantillaModalListado = $_SESSION["PlantillaForm"];
    $nombre_Form = "";
    $nombre_Form_Modificacion = "";
    switch ($_SESSION["Controlador"] -> miEstado -> Estado){
        case 6.1:
            //DESPLEGABLE CUSTOM
            $CModal = fopen('../html/selectBuscar.html', "r");
            $CsizeModal = filesize('../html/selectBuscar.html');
            $cDesplegable = fread($CModal, $CsizeModal);
            fclose($CModal);



            $nombre_Form = "Nuevo tiempo de fase";
            $nombre_Form_Modificacion = "Modificar tiempo de fase";
            if($UsuarioAdmin && $otroUsuario){
                $nombre_Form .= ' de '.$_SESSION["Controlador"] -> miEstado -> nombre_descriptivo;
            }
            $Modal = str_replace('modalFormularioDinamico','modalFormularioTiemposProyectos',$PlantillaModal);

            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="IdTipoTarea" class="form-label">Tipo de tarea</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="Fase" id="IdTipoTarea">';
            $existeSelecion = 1;
            foreach($_SESSION["Controlador"] -> miEstado -> datosProyectos[1] as $valorSelecionable){
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['IdProduccionTipoTarea'].'" selected>'.$valorSelecionable['Nombre'].'</option>';
                        
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['IdProduccionTipoTarea'].'">'.$valorSelecionable['Nombre'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div>';
            
            
            // $camposForm .=  '<div class="form-check form-switch">
            //                 <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
            //                 <label class="form-check-label" for="flexSwitchCheckDefault">Mostrar solo tareas con OF</label>
            //                 </div>';
                                        
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="IdProyectoTarea" class="form-label">Fase</label>';
            //$camposForm .= '<select class="form-select border-secondary" aria-label="Fase" id="IdProyectoTarea">';
            //$existeSelecion = 1;
            //$camposForm .= '<option value="" selected></option>';

            $ContenidoCModal = "";

            $arrayDc = array_filter($_SESSION["Controlador"] -> miEstado -> datosProyectos[0], function ($valorDropdown){
                return $valorDropdown["IdProyecto"] ==  $_SESSION["Controlador"] -> miEstado -> IdPropietario;
            });
            // foreach($arrayDc as $valorSelecionable){
            //     switch ($existeSelecion){
            //         case 0:
            //             $camposForm .= '<option value="'.$valorSelecionable['IdProyectoTarea'].'"   cuenta-Of="'.$valorSelecionable['CuentaOF'].'" >'.$valorSelecionable['Descripcion'].'</option>';
                        
            //             break;
            //         default:
            //             $camposForm .= '<option value="'.$valorSelecionable['IdProyectoTarea'].'  cuenta-Of='.$valorSelecionable['CuentaOF'].' ">'.$valorSelecionable['Descripcion'].'</option>';
            //             break;
            //     }
            //     $existeSelecion = 1;
            // }
            
            // $camposForm .= '</select></div>';
            foreach($arrayDc as $valorSelecionable){
                $ContenidoCModal .= '<div  class="selectBuscar-item" data-value="'.$valorSelecionable['IdProyectoTarea'].'">'.$valorSelecionable['Descripcion'].'</div>';
            }
            
            $camposForm .= str_replace("%Options%",$ContenidoCModal,$cDesplegable);
            // $camposForm .= '<div class="text-danger d-none" id="modalFormularioTiemposProyectos_cadenas">';
            // foreach($_SESSION["Controlador"] -> miEstado -> datosProyectos[0] as $valorSelecionable){                    
            //     $camposForm .= '(#)'.$valorSelecionable['IdProyectoTarea'].'(#)'.$valorSelecionable['Descripcion'].'(#)';
            // }
             $camposForm .= '</div>';
            

            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style="">
                                <label for="FechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="datetime-local" class="form-control border-secondary" id="FechaInicio" aria-describedby="Fecha Inicio" value="" required>
                                <div class="text-danger d-none" id="FechaInicio_msgError">Fecha Inicio necesaria.</div></div>';
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style=""><label for="FechaFin" class="form-label">Fecha Fin</label>
                            <input type="datetime-local" class="form-control border-secondary" id="FechaFin" aria-describedby="Fecha Fin" value="" required>
                            <div class=" d-none" id="FechaFin_condiciones">';
            $camposForm .=  "$('#FechaFin').val() &gt; $('#FechaInicio').val() </div>";
            $camposForm .=   '<div class="text-danger d-none" id="FechaFin_msgError">La fecha de fin no puede ser menor que la fecha de inicio</div></div>';
            $camposForm .=  '<div id="DiferenciaHorasCampo" class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg d-none">
                                <label for="DiferenciaHoras" class="form-label">Total en Horas</label>
                                <input type="text" class="form-control border-secondary" id="DiferenciaHoras" readonly>
                            </div>';
            break;
        case 6.2:
            /*****************************************/
            /*****MODAL DE MATERIALES DE PROYECTOS****/
            /*****************************************/
            //DESPLEGABLE CUSTOM
            $CModal = fopen('../html/selectBuscar.html', "r");
            $CsizeModal = filesize('../html/selectBuscar.html');
            $cDesplegable = fread($CModal, $CsizeModal);
            fclose($CModal);

            $nombre_Form = "Nuevo recurso de fase";
            $nombre_Form_Modificacion = "Modificar recurso de fase";
            $Modal = str_replace('modalFormularioDinamico','modalFormularioMaterialesProyectos',$PlantillaModal);
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="IdProyectoTarea" class="form-label">Fase</label>';
            //$camposForm .= '<select class="form-select border-secondary" aria-label="Fase" id="IdProyectoTarea">';
            $ContenidoCModal = "";

            $arrayDc = array_filter($_SESSION["Controlador"] -> miEstado -> datosProyectos[0], function ($valorDropdown){
                return $valorDropdown["IdProyecto"] ==  $_SESSION["Controlador"] -> miEstado -> IdPropietario;
            });
            
            foreach($arrayDc as $valorSelecionable){
                $ContenidoCModal .= '<div  class="selectBuscar-item" data-value="'.$valorSelecionable['IdProyectoTarea'].'">'.$valorSelecionable['Descripcion'].'</div>';
            }
            $camposForm .= str_replace("%Options%",$ContenidoCModal,$cDesplegable);
            $camposForm .= '</div>';
            
            // $existeSelecion = 1;
            // $camposForm .= '<option value="" selected></option>';

            
            // $arrayDc = array_filter($_SESSION["Controlador"] -> miEstado -> datosProyectos[0], function ($valorDropdown){
            //     return $valorDropdown["IdProyecto"] ==  $_SESSION["Controlador"] -> miEstado -> IdPropietario;
            // });
            // foreach($arrayDc as $valorSelecionable){
            //     switch ($existeSelecion){
            //         case 0:
            //             $camposForm .= '<option value="'.$valorSelecionable['IdProyectoTarea'].'">'.$valorSelecionable['Descripcion'].'</option>';
                        
            //             break;
            //         default:
            //             $camposForm .= '<option value="'.$valorSelecionable['IdProyectoTarea'].'">'.$valorSelecionable['Descripcion'].'</option>';
            //             break;
            //     }
            //     $existeSelecion = 1;
            // }
            
            // $camposForm .= '</select></div>';
                

            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="IdProyectoMaterialTipo" class="form-label">Tipo de Artículo</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="IdProyectoMaterialTipo" id="IdProyectoMaterialTipo">';
            $existeSelecion = 1;
            
            foreach($_SESSION["Controlador"] -> miEstado -> datosProyectos[2] as $valorSelecionable){
                
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['IdProyectoMaterialTipo'].'" selected>'.$valorSelecionable['Nombre'].'</option>';
                        
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['IdProyectoMaterialTipo'].'">'.$valorSelecionable['Nombre'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div>';

            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
            $camposForm .= '<label for="IdArticulo" class="form-label">Artículo de gasto</label>';
            $camposForm .= '<select class="form-select border-secondary" aria-label="IdArticulo" id="IdArticulo">';
            $existeSelecion = 1;
            foreach($_SESSION["Controlador"] -> miEstado -> datosProyectos[3] as $valorSelecionable){
                switch ($existeSelecion){
                    case 0:
                        $camposForm .= '<option value="'.$valorSelecionable['idArticulo'].'" selected>'.$valorSelecionable['Descripcion'].'</option>';
                        
                        break;
                    default:
                        $camposForm .= '<option value="'.$valorSelecionable['idArticulo'].'">'.$valorSelecionable['Descripcion'].'</option>';
                        break;
                }
                $existeSelecion = 1;
            }
            $camposForm .= '</select></div>';

            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style=""><label for="Descripcion" class="form-label">Descripción</label><input type="text" class="form-control border-secondary" id="Descripcion" aria-describedby="Descripción" value="" required=><div class="text-danger d-none" id="Descripcion_msgError">El campo no puede estar vacío.</div></div>';
            
            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style=""><label for="Cantidad" class="form-label">Cantidad</label><input type="Number" class="form-control border-secondary" id="Cantidad" aria-describedby="Cantidad" value="0" step="1" required>';
            $camposForm .= '<div class=" d-none" id="Cantidad_condiciones">'."$('#Cantidad').val() &gt; 0</div>".'<div class="text-danger d-none" id="Cantidad_msgError">La cantidad ha de ser mayor que 0.</div></div>';

            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style=""><label for="Coste" class="form-label">Coste</label><input type="Number" class="form-control border-secondary" id="Coste" aria-describedby="Coste" value="0" step="1" required>';
            $camposForm .= '<div class=" d-none" id="Coste_condiciones">'."$('#Coste').val() &gt; 0</div>".'<div class="text-danger d-none" id="Coste_msgError">El coste ha de ser mayor que 0.</div></div>';


            $camposForm .= '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style="">
                                <label for="Fecha" class="form-label">Fecha</label>
                                <input type="datetime-local" class="form-control border-secondary" id="Fecha" aria-describedby="Fecha Inicio" value="" required>
                                <div class="text-danger d-none" id="Fecha_msgError">Fecha necesaria.</div></div><br>';
           
            break;
        default:
            break;    
    }

    $camposForm .=  '<div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style="display:none;">';
    $camposForm .= '<input type="Number" class="form-control border-secondary" id ="IdModificar" value ="0" >';
    $camposForm .= '</div><br>';
    //$ModalListado = str_replace('%descForm%','modalListadoProyectos',$PlantillaModalListado);
            

    return str_replace(["%tipoForm%",'%BodyModal%','%TituloModal%','%TituloModalModificacion%'],['',$camposForm,$nombre_Form, $nombre_Form_Modificacion],$Modal);
}

function PintaArrayValuesSelect($idLinea,$acciones_linea_pintar){
    switch ($_SESSION["Controlador"] -> miEstado -> Estado){
        case 6:
            $datosProyectos =  $_SESSION["Controlador"] -> miEstado -> datosProyectos[0];
            $tareasProyectos =  $_SESSION["Controlador"] -> miEstado -> datosProyectos[1];
            $strValores = "[[";
        $ponerComa = 0;
        for($i = 0; $i<count($datosProyectos)-1; $i++ ){
            
            if($ponerComa == 0 && $datosProyectos[$i]["IdProyecto"] == $idLinea ){
                $strValores .= strval($datosProyectos[$i]["IdProyectoTarea"]);
                $ponerComa = 1;
            }else if($datosProyectos[$i]["IdProyecto"] == $idLinea){
                $strValores .= ','. strval($datosProyectos[$i]["IdProyectoTarea"])  ;
            }
        }
        $ponerComa = 0;
            
            $strValores .= "],";
            $strValores .= "[],[]]";
            return str_replace('%ListaValuesForm%',$strValores,$acciones_linea_pintar);
            break;
        default:
            break;    
    }
}

function cargarSeccionesDinamicas(){
    //fragmento comun
    $rutaImg="";
    $pestanaSecciones = "";
    $pestanaSecciones .= '<br><br><div class="row h-75 justify-content-center" id="no-margin">';
    $pestanaSecciones .= '<div class="col-12 col-md-9"  id="no-padding">';
    $pestanaSecciones .= '<div class="card shadow-2-strong shadow">';
    $pestanaSecciones .= '<div class="button-container  justify-content-center pb-5 ">';
    if($_SESSION["Controlador"] -> miEstado -> tipo_App == 2){
        $rutaImg = "PortalEmpleado/PestanaMenu/";
    }
    
    if ($_SESSION["Controlador"] -> miEstado -> Estado == 2) {
        $pestanaSecciones .= '<div class="row col-10 col-lg-6 offset-1 offset-lg-3  justify-content-center">';
    } else {
        $pestanaSecciones .= '<div class="row  justify-content-center col-10 offset-1">';
    }
    
    //cargar secciones con acceso
    if ($_SESSION["Controlador"] -> miEstado -> permisosSecciones != null){
        $arraySecciones = array_filter($_SESSION["Controlador"] -> miEstado -> permisosSecciones, function ($seccion) {
            return $seccion["OrdenEstado"] == $_SESSION["Controlador"] -> miEstado -> Estado;
        });
    
    
    
        if(count($arraySecciones)>0){
            foreach($arraySecciones as $seccion){
                
                if($seccion['IdFormulario'] == 2612){
                    $pestanaSecciones .=    '<div class=" col-6 col-sm-6 mt-5" id="pestanaEnJornada" style="display: none;">
                                            <button id="boton_secciones" onclick="dibuja_pagina([2])"
                                            class="btn btn-md border shadow bg-body rounded d-block mx-auto" >';
                    $pestanaSecciones .=  '<img class="img-fluid" src="Img/'.$rutaImg.'portal-empleado-jornada-entrada-f.png" />';
                    $pestanaSecciones .= '</button>';
                    $pestanaSecciones .= '</div>';
                     
                    $pestanaSecciones .=  '<div class=" col-6 col-sm-6 mt-5" id="pestanaFueraJornada" style="display: none;">';
                    $pestanaSecciones .= '<button id="boton_secciones" onclick="dibuja_pagina([2])"';
                    $pestanaSecciones .= 'class="btn btn-md border shadow bg-body rounded d-block mx-auto" >';
                    $pestanaSecciones .= '<img class="img-fluid" src="Img/'.$rutaImg.'portal-empleado-jornada-salida-f2.png"/>';
                    $pestanaSecciones .= '</button>';
                    $pestanaSecciones .= '</div>';
                }elseif($seccion['IdFormulario'] == 2640){
                    $pestanaSecciones .= '<div class="'.$seccion['EstiloPestaña'].'">';
                    $pestanaSecciones .= '<button id="boton_secciones" onclick="dibuja_pagina(['.$seccion['ValorAccion'].'])"';
                    $pestanaSecciones .= 'class="btn btn-md border shadow bg-body rounded d-block mx-auto" >';
                    $pestanaSecciones .= '<img class="img-fluid" src="Img/'.$rutaImg.''.$seccion['Imagen'].'"/>
                                            <p class="h5 PestanaMenu" style="color:'.$seccion['ColorPestana'].';">'.$seccion['Nombre'].'</p>';
                    $pestanaSecciones .= '</button>';
                    $pestanaSecciones .= '</div>';

                }else{
                    $pestanaSecciones .= '<div class="'.$seccion['EstiloPestaña'].'">';
                    $pestanaSecciones .= '<button id="boton_secciones" onclick="dibuja_pagina(['.$seccion['ValorAccion'].'])"';
                    $pestanaSecciones .= 'class="btn btn-md border shadow bg-body rounded d-block mx-auto" >';
                    $pestanaSecciones .= '<img class="img-fluid" src="Img/'.$rutaImg.''.$seccion['Imagen'].'"/>
                                            <p class="h4 PestanaMenu" style="color:'.$seccion['ColorPestana'].';">'.$seccion['Nombre'].'</p>';
                    $pestanaSecciones .= '</button>';
                    $pestanaSecciones .= '</div>';
                }
            
                
            }
        }else{
            $pestanaSecciones = "No tienes permisos para acceder a la siguiente pestaña";
        }
    
    }else{
        $pestanaSecciones = "No tienes permisos para acceder a la siguiente pestaña";
    }
    //cierre fragmento comun
    $pestanaSecciones .= '</div></div></div></div></div>';
    return $pestanaSecciones;
}

function cargarCustomGridModal($arrayPintar){
    $tabla = '<table class="table table-striped table-bordered-bottom" id="cuerpo"><tbody id="myTable">';
    foreach($arrayPintar as $elemento){
        $tabla .= '<tr id="67" data-nombre="">';
        $tabla .= '<td class="col-7">';
        $tabla .= '<div class="Identificador" name="Identificador">';
        $tabla .= '<h5>24300119 prueba</h5>';
        $tabla .= '<h5><details open=""><summary></summary>';
        $tabla .= '<p> <span style="color:GREEN;">19/01/2024</span><br></p>';
        $tabla .= '</details></h5></div></td><td class="col-5">';
        $tabla .= '<div id="fecha_a" class="d-flex align-items-end flex-column" style="float:right;color:GREEN;">';
        $tabla .= '<h5>Iniciado</h5>';
        $tabla .= '<h5><div class="mt-1">';
        $tabla .= '<button  style="all: initial;width:54px;cursor: pointer;">';
        $tabla .= '<img class="pdf_icono" src="Img/IconosAcciones/CodigoBarras.png"></button>';
        $tabla .= '</div></h5></div></td></tr>';
    }
    $tabla .= '</tbody></table>';
    return $tabla;
}

// //cargar el formulario de firma
// function cargarFormularioFirma(){
//     $documentoFirmable = array_filter($_SESSION["Controlador"] -> miEstado -> Documentos, function ($documento) {
//         return $documento["id"] == $_SESSION["Controlador"] -> miEstado -> IdDocumentoPadre;
//     });
//     $documentoFirmable = array_shift($documentoFirmable);
//     print_r($documentoFirmable["Documento"]);
// }

function ObtenAccionesPersonalizadas(){
    $accionesPersonalizada = "";
    switch ($_SESSION["Controlador"] -> miEstado -> Estado){
        case 6:
            $accionesPersonalizada .= '<button onclick="dibuja_pagina([6.1,%IdProp%])" style="all: initial;width:54px;cursor: pointer;"><img class="pdf_icono" src="Img/IconosAcciones/TiemposUsuario.png"></button>';    
            //$accionesPersonalizada .= '<button onclick="cargarModalFormularioDinamico('."'modalFormularioTiemposProyectos'".',%ListaValuesForm%,%IdProp%)" style="all: initial;width:54px;cursor: pointer;"><img class="pdf_icono" src="Img/IconosAcciones/TiemposUsuario.png"></button>';    
            $accionesPersonalizada .= '<button onclick="dibuja_pagina([6.2,%IdProp%])" style="all: initial;width:54px;cursor: pointer;"><img class="pdf_icono" src="Img/IconosAcciones/CodigoBarras.png"></button>';    
            break;
        default:
            break;    
    }
    return $accionesPersonalizada;
}

function superUsuarioModal(){
    $modalSuperUsuario = 
        '<div class="modal fade" id="modalSuperAdmin" tabindex="-1" aria-labelledby="modalDinamico" aria-modal="true" role="dialog" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                <h5 class="modal-title">Seleccionar Personal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form class="formulario_modal  col-12 col-md-12 mt-3" enctype="multipart/form-data" Id="FormularioCambioUsuario">
                        <div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg" style="">
                            <div class="mb-3 col-12 col-md-10 offset-md-1 input-group-lg">';
    $CModal = fopen('../html/selectBuscar.html', "r");
    $CsizeModal = filesize('../html/selectBuscar.html');
    $cDesplegable = fread($CModal, $CsizeModal);
    fclose($CModal);

    $ContenidoCModal = "";
    foreach($_SESSION["Controlador"] -> miEstado -> ListaEmpleadosVerAdministrador as $valorSelecionable){
        $ContenidoCModal .= '<div  class="selectBuscar-item" data-value="'.$valorSelecionable['Id'].'">'.$valorSelecionable['Nombre'].'</div>';
    }
  
    $modalSuperUsuario .= str_replace(["%Options%",'class="selectBuscar"'],[$ContenidoCModal,'class="selectBuscar"'],$cDesplegable);
                      
    $modalSuperUsuario .= '</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="SubmitModalForm" onclick="CU()">Guardar</button>
                </div>
            </div>
        </div>
    </div>';
    return $modalSuperUsuario;
    
}