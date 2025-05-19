//Cookies
/****************  Filtrado JQUERY por INPUTS  en el SEARCH BAR y DROPDOWN LIST ********************/

function comprueba_filtros() {
    $("#form1").ready(function () {
        var value = $("#form1").val().toLowerCase();
        console.log(value);
        $("#myTable tr").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
}


//****************Filtrado por botones, captura el ID del TD********************/

function filtro(filtrado) {
    var value = filtrado.toLowerCase();
    console.log(value);
    $("#myTable tr").each(function() {
        
        var tipoServicio = $(this).data("tipo-servicio").toLowerCase();
        if (tipoServicio !== value) {
            $( ).hide();
        } else {
            $(this).show();
        }
    });
}

//------------------------------------------------
