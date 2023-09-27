

$(document).ready(function() {
    $('#id_customers_selection').on("change", function(event) {
        var opt = $(this).find("option:selected")
        var url = opt.data("url")
        window.location.href = url
    });

    // Ocultar el mensaje de error inicialmente
    $('#error_message').hide();

    setTimeout(function() {
        $(".alerta_msg_albaranes").fadeOut(2000);
    }, 2200);

    const checkboxes = document.querySelectorAll('.mi-checkbox[type=checkbox]');

    $('#preview_button').on("click", function(event) {
        let seleccionado = false;
        checkboxes.forEach(function(checkbox) {
            if (checkbox.checked) {
                seleccionado = true;
            }
        });
        if (seleccionado) {
            console.log("entr"); // Prevenir el comportamiento por defecto del formulario
            event.preventDefault();
            // Crear un objeto FormData con los datos del formulario
            var formData = new FormData(document.getElementById('mi-formulario'));
            console.log(formData.entries());
            // Enviar la consulta AJAX utilizando fetch()
            fetch("https://contenedores.grupohnosfas.com/Facturas/PreFactura", {
                method: "POST",
                body: formData
            }).then(function(response) { // La consulta fue exitosa
                console.log("Datos enviados correctamente");
                response.text().then(text => {
                    $('.modal-body').html(text);
                    $('#exampleModal').modal('show'); // Abre la modal después de cargar el contenido
                });
            }).catch(function(error) { // La consulta falló
                console.log("Error al enviar los datos");
                console.log(error);
            });
        } else {
            // Mostrar el mensaje de error
            $('#error_message').show();
            setTimeout(function() {
                $("#error_message").fadeOut(2000);
            }, 2200);
        }
    });

    // ...
});
