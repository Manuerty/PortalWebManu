<?php
//Recursos
    function compararCadenas($cadena1, $cadena2) {
        // Extraer los números entre los caracteres #
        preg_match('/#(\d+)#/', $cadena1, $matches1);
        preg_match('/#(\d+)#/', $cadena2, $matches2);
    
        // Obtener los valores numéricos
        $valor1 = isset($matches1[1]) ? intval($matches1[1]) : 0;
        $valor2 = isset($matches2[1]) ? intval($matches2[1]) : 0;
    
        // Comparar los valores numéricos
        return $valor1 - $valor2;
    }
    
    function compararPorDireccionArbol($e1, $e2) {
        // Extraer los números entre los caracteres #
        $elemento1= $e1['DireccionArbol'];
        $elemento2= $e2['DireccionArbol'];
        $cadena1 = '';
        $cadena2 = '';
        $cadenaParcial1 = '';
        $cadenaParcial2 = '';
        for($i=0;$i<strlen($elemento1);$i++){
            
            if($elemento1[$i]=='#' && $i == 0){
                $cadenaParcial1 = '0000';
            }elseif($elemento1[$i]!='#' && $i<(strlen($elemento1)-1)){
                $cadenaParcial1 .= $elemento1[$i];
            }else{
                $cadena1 .= substr($cadenaParcial1,-5);
                $cadenaParcial1 = '0000';
            }
        }

        for($i=0;$i<strlen($elemento2);$i++){
            
            if($elemento2[$i]=='#' && $i == 0){
                $cadenaParcial2 = '0000';
            }elseif($elemento2[$i]!='#' && $i<(strlen($elemento2)-1)){
                $cadenaParcial2 .= $elemento2[$i];
            }else{
                $cadena2 .= substr($cadenaParcial2,-5);
                $cadenaParcial2 = '0000';
            }
        }
        
        // Comparar los valores numéricos normalmente
        
        return strcmp($cadena1, $cadena2);
    }
    
?>