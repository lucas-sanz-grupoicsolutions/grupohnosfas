<?php
function validateLogin(): array
{
    return [
        'mail' => [
            'label' => 'email',
            'rules' => 'required|valid_email|ensureUserExist[mail]'
        ],
        'password' => [
            'label' => 'contraseña',
            'rules' => 'required|validatePassword[mail,password]'
        ]
    ];
}

function valiateCreateUser(): array
{
    return [
        'name' => [
            'label'=> 'Nombre',
            'rules' =>'required|alpha_space'
        ],
        'mail' => [
            'label' => 'Correo electrónico',
            'rules' => 'required|valid_email|ensureUserNotExist[mail]'
        ],
        'password' => [
            'label' => 'Contraseña',
            'rules' => 'required|matches[c-password]'
        ],
    ];
}

//Contenedores
function validateCreateContenedores(): array
{
    return [
        'residue' => [
            'label'=> 'Residuo',
            'rules' =>'required|max_length[50]|alpha_numeric'
        ],
        'price' => [
            'label' => 'Precio',
            'rules' => 'required|numeric|max_length[8]'
        ],

    ];
}


//Clientes
function validateCreateCustomers(): array
{
    return [
        'names' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[65]'
        ],
        'phone' => [
            'label' => 'Telefono',
            'rules' => 'required|numeric'
        ],
        'mail' => [
            'label'=> 'Mail',
            'rules' =>'required|max_length[50]|valid_email'
        ],
        'dni' => [
            'label' => 'Dni',
            'rules' => 'required|alpha_numeric|max_length[10]'
        ],
        'address' => [
            'label'=> 'Direccion',
            'rules' =>'required|max_length[60]'
        ],
        'location' => [
            'label' => 'Localidad',
            'rules' => 'required|max_length[20]'
        ],
        'province' => [
            'label'=> 'Provincia',
            'rules' =>'required|max_length[50]'
        ],
        'zip_code' => [
            'label' => 'Codigo Postal',
            'rules' => 'required|numeric|max_length[6]'
        ],
        'iban' => [
            'label'=> 'Iban',
            'rules' =>'required|max_length[8]'
        ],
        'bank' => [
            'label' => 'Banco',
            'rules' => 'required|numeric|max_length[4]'
        ],
        'office_bank' => [
            'label'=> 'Oficina Banco',
            'rules' =>'required|max_length[4]|numeric'
        ],
        'digital_control' => [
            'label' => 'Digito de Control',
            'rules' => 'required|numeric|max_length[8]'
        ],
        'bank_count' => [
            'label' => 'Cuenta Bancaria',
            'rules' => 'required|numeric|max_length[12]'
        ],

        //Contacto
        'name_pc_01' => [
            'label' => 'Nombre Contacto',
            'rules' => 'required|alpha_numeric|max_length[30]'
        ],
        'position_01' => [
            'label'=> 'Cargo',
            'rules' =>'required|max_length[50]|alpha_numeric'
        ],
        'phone_pc_01' => [
            'label' => 'Telefono',
            'rules' => 'required|numeric|max_length[10]'
        ],
        'email_01' => [
            'label' => 'Email',
            'rules' => 'required|valid_email|max_length[30]'
        ],

    ];
}


function validateUpdateCustomers(): array
{
    return [
        'names' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[65]'
        ],
        'phone' => [
            'label' => 'Telefono',
            'rules' => 'required|numeric|max_length[9]'
        ],
        'mail' => [
            'label'=> 'Mail',
            'rules' =>'required|max_length[50]|valid_email'
        ],
        'dni' => [
            'label' => 'Dni',
            'rules' => 'required|alpha_numeric|max_length[10]'
        ],
        'address' => [
            'label'=> 'Direccion',
            'rules' =>'required|max_length[60]'
        ],
        'location' => [
            'label' => 'Localidad',
            'rules' => 'required|max_length[20]'
        ],
        'province' => [
            'label'=> 'Provincia',
            'rules' =>'required|max_length[50]'
        ],
        'zip_code' => [
            'label' => 'Codigo Postal',
            'rules' => 'required|numeric|max_length[6]'
        ],
        'iban' => [
            'label'=> 'Iban',
            'rules' =>'required|max_length[8]'
        ],
        'bank' => [
            'label' => 'Banco',
            'rules' => 'required|numeric|max_length[4]'
        ],
        'office_bank' => [
            'label'=> 'Oficina Banco',
            'rules' =>'required|max_length[4]|numeric'
        ],
        'digital_control' => [
            'label' => 'Digito de Control',
            'rules' => 'required|numeric|max_length[8]'
        ],
        'bank_count' => [
            'label' => 'Cuenta Bancaria',
            'rules' => 'required|numeric|max_length[12]'
        ],


    ];
}

function validateCreateAlbaran(): array
{
    return [
        'preprinted' => [
            'label'=> 'Pre Impreso',
            'rules' => 'required|is_unique[albaranes.preprinted]|max_length[15]',
            'errors' => [
                'is_unique' => 'No se debe repetir el numero de Pre-Impreso del Albaran.'
            ]
        ],

        'discount' => [
            'label'=> 'Descuento',
            'rules' =>'max_length[9]'
        ],


    ];
}

function validateEditAlbaran(): array
{
    return [
        'preprinted' => [
            'label'=> 'Pre Impreso',
            'rules' =>'required|max_length[50]|alpha_numeric'
        ],
        'retainer_amount' => [
            'label' => 'Anticipo',
            'rules' => 'max_length[9]'
        ],
        'discount' => [
            'label'=> 'Descuento',
            'rules' =>'max_length[9]'
        ],


    ];
}

//Facturas


function validateCreateBills(): array
{
    return [

        'retainer_amount' => [
            'label' => 'Anticipo',
            'rules' => 'max_length[9]'
        ],


    ];
}

function validateCreateBillsAutomatic(): array
{
    return [
        'id_bills' => [
            'label'=> 'Numero de Factura',
            'rules' => 'required|is_unique[bills.id_bills]',
            'errors' => [
                'is_unique' => 'No se debe repetir el numero de Factura.'
            ]
        ],



    ];
}

//Direccion de Obra
function validateCreateWorkLocations(): array
{
    return [

        'address' => [
            'label'=> 'Direccion',
            'rules' =>'required|max_length[60]'
        ],
        'location' => [
            'label' => 'Localidad',
            'rules' => 'required|max_length[20]'
        ],
        'province' => [
            'label'=> 'Provincia',
            'rules' =>'required|max_length[50]'
        ],
        'zip_code' => [
            'label' => 'Codigo Postal',
            'rules' => 'required|numeric|max_length[6]'
        ],

    ];
}



function validateEditWorkLocations(): array
{
    return [

        'address' => [
            'label'=> 'Direccion',
            'rules' =>'required|max_length[60]'
        ],
        'location' => [
            'label' => 'Localidad',
            'rules' => 'required|max_length[20]|alpha'
        ],
        'province' => [
            'label'=> 'Provincia',
            'rules' =>'required|max_length[50]|alpha'
        ],
        'zip_code' => [
            'label' => 'Codigo Postal',
            'rules' => 'required|numeric|max_length[6]'
        ],

    ];
}

//Conductores
function validateCreateDriver(): array
{
    return [

        'name' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[30]'
        ],
        'province' => [
            'label'=> 'Provincia',
            'rules' =>'required|max_length[50]|alpha'
        ],
        'phone' => [
            'label' => 'Telefono',
            'rules' => 'required|numeric|max_length[9]'
        ],
        'observations' => [
            'label'=> 'Observaciones',
            'rules' =>'max_length[255]'
        ],

    ];
}

function validateEditDriver(): array
{
    return [

        'name' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[30]'
        ],
        'province' => [
            'label'=> 'Provincia',
            'rules' =>'required|max_length[50]|alpha'
        ],
        'phone' => [
            'label' => 'Telefono',
            'rules' => 'required|numeric|max_length[9]'
        ],
        'observations' => [
            'label'=> 'Observaciones',
            'rules' =>'max_length[255]'
        ],

    ];
}

//Vehiculos
function validateVehicleCreate(): array
{
    return [

        'name' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[50]'
        ],
        'make' => [
            'label'=> 'Marca',
            'rules' =>'required|max_length[50]'
        ],
        'model' => [
            'label' => 'Modelo',
            'rules' => 'required|max_length[9]'
        ],
        'car_registration' => [
            'label'=> 'Matricula',
            'rules' =>'required|max_length[20]'
        ],
        'observations' => [
            'label'=> 'Observaciones',
            'rules' =>'max_length[255]'
        ],


    ];
}

function validateVehicleEditar(): array
{
    return [

        'name' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[50]'
        ],
        'make' => [
            'label'=> 'Marca',
            'rules' =>'required|max_length[50]'
        ],
        'model' => [
            'label' => 'Modelo',
            'rules' => 'required|max_length[9]'
        ],
        'car_registration' => [
            'label'=> 'Matricula',
            'rules' =>'required|max_length[20]'
        ],
        'observations' => [
            'label'=> 'Observaciones',
            'rules' =>'max_length[255]'
        ],


    ];
}


//Servicios
function validateServicesCreate(): array
{
    return [

        'name' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[50]'
        ],
        'code' => [
            'label'=> 'Codigo',
            'rules' =>'required|numeric|max_length[50]'
        ],



    ];
}

function validateServicesEdit(): array
{
    return [

        'name' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[50]'
        ],
        'code' => [
            'label'=> 'Codigo',
            'rules' =>'required|numeric|max_length[50]'
        ],

    ];
}

//Suplementos
function validateSupplementsCreate(): array
{
    return [

        'name' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[20]'
        ],
        'pvp' => [
            'label'=> 'Precio',
            'rules' =>'required|numeric|max_length[20]'
        ],



    ];
}

function validateSupplementsEdit(): array
{
    return [

        'name' => [
            'label'=> 'Nombre',
            'rules' =>'required|max_length[20]'
        ],
        'pvp' => [
            'label'=> 'Precio',
            'rules' =>'required|numeric|max_length[10]'
        ],

    ];
}



