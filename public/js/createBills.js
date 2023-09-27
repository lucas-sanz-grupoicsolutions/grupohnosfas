$(document).ready(function() {
    $('#buttom_bills').click(function() {
        // Verificar si al menos un checkbox está marcado
        if ($('input[name="albaranes[]"]:checked').length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atencion',
                text: 'Debes seleccionar al menos un Albaran.',
            }).then(function() {

                $('input[name="albaranes[]"]').addClass('parpadeo-rojo');

                // Obtener la posición de la tabla
                var tablePosition = $('table.table').offset().top;

                // Desplazar la vista al inicio de la tabla
                $('html, body').animate({
                    scrollTop: tablePosition
                }, 'slow');
            });
            return false; // Evita que el formulario se envíe
        }
    });
});









const checkboxes = document.querySelectorAll('.mi-checkbox[type=checkbox]');
$(document).ready(function() {
    $('#id_customers_selection').on("change", function(event) {
        var opt = $(this).find("option:selected")
        var url = opt.data("url")
        window.location.href = url
    });
    var divSupp = document.getElementById("divSupp");
    divSupp.style.display = "none";
});
var divToShow = document.getElementById("error");
divToShow.style.display = "none";
var divToShow = document.getElementById("error_formas_de_pago");
divToShow.style.display = "none";
var divSuppNoAhi = document.getElementById("divSuppNoAhi");
divSuppNoAhi.style.display = "none";
//Supp
var divSupp = document.getElementById("divSupp");
$('#verSuppOn').on("click", function(event) {
    let seleccionado = false;
    checkboxes.forEach(function(checkbox) {
        if (checkbox.checked) {
            seleccionado = true;
        }
    });
    if (seleccionado) {
        divSupp.style.display = "block";
        divSuppNoAhi.style.display = "none";
    } else {
        divSuppNoAhi.style.display = "block";
    }
});
setTimeout(function() {
    $(".alerta_msg_bills").fadeOut(2000);
}, 2200);


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





