const sweetAlert = {

    alertNewCustomerParse: document.getElementById('data-msg'),

    init(){
        if (this.alertNewCustomerParse && this.alertNewCustomerParse.getAttribute('data-msg') != 'null') {
            this.show(JSON.parse(document.getElementById('data-msg').getAttribute('data-msg')), 'data-msg');
        }
    },

    show(element, attribute = null){
        Swal.fire({
            title: element.title,
            text: element.body,
            icon: element.icon,
            buttonsStyling: false,
            confirmButtonText: element.btnConfirmText,
            customClass: {
                confirmButton: element.btnConfirmStyle
            }
        }).then((result) => {
            if (result.isConfirmed) {
                if (attribute!=null) {
                    document.getElementById(attribute).setAttribute(attribute, null);
                }
            }
        })
    }

}
