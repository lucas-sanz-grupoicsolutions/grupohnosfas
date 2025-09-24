




$(document).ready(function() {
    // Configurar Select2 para el select de clientes
    $('#customer-select').select2({
        placeholder: 'Seleccione un cliente',
        allowClear: false
    });

    // Configurar Select2 para el select de direcciones de obra
    $('#work-location-select').select2({
        allowClear: false
    });

    // Configurar Select2 para el select de conductores
    $('#id_drivers_selection').select2({
        allowClear: false
    });

    // Evento de cambio para el select de clientes
    $('#customer-select').on('change', function() {
        var customerId = $(this).val();
        var workLocationSelect = $('#work-location-select');

        // Desactivar el select de direcciones de obra mientras se cargan los datos
        workLocationSelect.prop('disabled', true);

        if (customerId != '') {
            $.ajax({
                url: 'https://contenedores.grupohnosfas.com/Facturas/FiltrarClientesFacturas/' +
                customerId,
                type: 'GET',
                dataType: 'json',
                data: { customerId: customerId },
                success: function(data) {
                    // Eliminar todas las opciones actuales del select de direcciones de obra
                    workLocationSelect.empty();
                    // Agregar la opción predeterminada
                    workLocationSelect.append('<option value="">Seleccione una dirección de obra</option>');
                    // Agregar las nuevas opciones al select de direcciones de obra
                    $.each(data, function(index, element) {
                        workLocationSelect.append('<option value="' + element.id_work_locations + '">' + element.address + '</option>');
                    });
                    // Habilitar el select de direcciones de obra después de completar la carga de datos
                    workLocationSelect.prop('disabled', false);
                },
                error: function() {
                    // Manejar errores de solicitud AJAX
                    // Por ejemplo, mostrar un mensaje de error o realizar otra acción apropiada
                }
            });
        } else {
            // Si no se selecciona ningún cliente, vaciar y desactivar el select de direcciones de obra
            workLocationSelect.empty().append('<option value="">Seleccione una dirección de obra</option>');
            workLocationSelect.prop('disabled', true);
        }
    });
});



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

