
$('#preview_button').on("click", function(event) {


    let seleccionado = false;
   const checkboxesAlbaranes = document.querySelectorAll('.mi-checkbox[type=checkbox]');
    checkboxesAlbaranes.forEach(function(checkbox) {
        if (checkbox.checked) {
            seleccionado = true;
        }
    });
    if (seleccionado) {


         console.log("entr"); // Prevenir el comportamiento por defecto del formulario
        // Prevenir el comportamiento por defecto del formulario
        event.preventDefault();
        // Crear un objeto FormData con los datos del formulario
        var formData = new FormData(document.getElementById('mi-formulario'));
        console.log(formData.entries());
        // Enviar la consulta AJAX utilizando fetch()


    fetch("https://contenedores.grupohnosfas.com/Facturas/PreFacturaDesdeListaAlbaranes", {
            method: "POST",
            body: formData
        }).then(function(response) { // La consulta fue exitosa
            console.log("Datos enviados correctamente");
            response.text().then(text => {
                $('.modal-body').html(text);
                $('#prev_factura').click();
            });
        }).catch(function(error) { // La consulta falló
            console.log("Error al enviar los datos");
            console.log(error);
        });
    } else {
        var divToShow = document.getElementById("error");
        divToShow.style.display = "block";
        setTimeout(function() {
            $("#error").fadeOut(2000);
        }, 2200);
    }
});





//Para cehquear que haya albaranes seleccioandos
//Boton facturar
$('#buttom_bills').on("click", function(event) {
let seleccionado = false;
var checkboxes = document.querySelectorAll('input[type=checkbox]:checked');
checkboxes.forEach(function(checkbox) {
    if (checkbox.checked) {
        seleccionado = true;
    }
});
if (!seleccionado) {
    event.preventDefault();
    var divToShow = document.getElementById("error");
    divToShow.style.display = "block";
    setTimeout(function() {
        $("#error").fadeOut(2000);
    }, 2200);
}
});




$(document).ready(function() {
// Configurar Select2 para el select de clientes
$('#customer-select').select2({
    placeholder: 'Seleccione ',
    allowClear: false
});
// Configurar llamada AJAX para obtener las direcciones de obra de un cliente seleccionado
$('#customer-select').on('change', function() {
    var customerId = $(this).val();
    $('#work-location-select').prop('disabled', false);
    $('#work-location-select').html("");
    if (customerId != '') {
        $.ajax({
            url:'https://contenedores.grupohnosfas.com/Albaranes/FiltrarClientesAlbaranes/' +
                customerId,
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data.length > 0) {
                    $('#work-location-select').append(
                        '<option value="">Seleccione una dirección de obra</option>'
                    );
                    $.each(data, function(index, element) {
                        $('#work-location-select').append(
                            '<option value="' + element
                            .id_work_locations + '">' + element
                            .address + '</option>');
                    });
                    $('#work-location-select').prop('disabled', false);
                }
            }
        });
    }
});
$('#work-location-select').select2({
    allowClear: false
});
$('#id_drivers_selection').select2({
    allowClear: false
});
});
