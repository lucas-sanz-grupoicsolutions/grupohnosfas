$(document).ready(function() {
    $('#buttom_bills').click(function() {
        // Verificar si al menos un checkbox está marcado
        if ($('input[name="supplements_id[]"]:checked').length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Atencion',
                text: 'Debes seleccionar al menos un Suplemento.',
            }).then(function() {

                $('input[name="supplements_id[]"]').addClass('parpadeo-rojo');

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



