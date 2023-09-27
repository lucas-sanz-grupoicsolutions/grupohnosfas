<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Entities\Customers;
use App\Libraries\Log;
use App\Models\CustomersModel;
use DateTime;
use DateTimeZone;
use Config\Services;

use App\Entities\PersonContact;
use App\Models\PersonContactModel;
use CodeIgniter\I18n\Time;

use App\Entities\PaymentMethod;
use App\Models\PaymentMethodModel;


class CustomersController extends BaseController
{
    protected Log $log;
    protected Log $logClientes;

    protected $customersModel;
    protected $personcontactModel;
    protected $paymentMethodModel;
    protected $arrayname = [];
    protected $ordersModel;

    public function __construct()
    {

        $this->log = new Log('Customers/');
        $this->customersModel = model('CustomersModel');
        $this->paymentMethodModel = model('PaymentMethodModel');
        $this->personcontactModel = model('PersonContactModel');
        $this->ordersModel = model('OrdersModel');
    }

    public function index()
    {
        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Customers/create.html.twig', ['lugar' => 'index','payment_method'=>$payment_method]);
    }

    public function create()
    {

      if (!$this->validate(validateCreateCustomers())) {

         return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $db = db_connect();

        $customers = new Customers();

        $name = $this->request->getPost('names');
        $mail = $this->request->getPost('mail');
        $phone = $this->request->getPost('phone');
        $dni = $this->request->getPost('dni');
        $location = $this->request->getPost('location');
        $province = $this->request->getPost('province');
        $payment_method = $this->request->getPost('payment_method');
        $address = $this->request->getPost('address');
        $zip_code = $this->request->getPost('zip_code');

        $iva = $this->request->getPost('iva');

        $bic = $this->request->getPost('bic');
        $iban = $this->request->getPost('iban');
        $bank = $this->request->getPost('bank');
        $office_bank = $this->request->getPost('office_bank');
        $digital_control = $this->request->getPost('digital_control');
        $bank_count = $this->request->getPost('bank_count');

        $date_signing_mandate = $this->request->getPost('date_signing_mandate');
        $recurrent_date = $this->request->getPost('recurrent_date');
        $observations = $this->request->getPost('observations');
        $contact_person = $this->request->getPost('contact_person');


        $customers->date_signing_mandate = $date_signing_mandate;
        $customers->recurrent_date = $recurrent_date;

        $customers->names = $name;
        $customers->mail = $mail;
        $customers->phone = $phone;
        $customers->dni = $dni;
        $customers->location = $location;
        $customers->province = $province;
        $customers->payment_method = $payment_method;
        $customers->address = $address;
        $customers->zip_code =  $zip_code;
        $customers->iva = $iva;

        $customers->bic = $bic;
        $customers->iban = $iban;
        $customers->bank = $bank;
        $customers->office_bank = $office_bank;
        $customers->digital_control = $digital_control;
        $customers->bank_count = $bank_count;

        $customers->observations = $observations;
        $customers->contact_person = $contact_person;
        $customers->active = 1;

        try {

            $this->customersModel->save($customers);
            $id_customer = $db->insertID($customers);



            /*-----Persona de Contacto*/

            $personContact = new PersonContact();
            $name_pc = $this->request->getPost('name_pc_01');

            if (!empty($name_pc)) {

                $name_pc = $this->request->getPost('name_pc_01');
                $position_pc = $this->request->getPost('position_01');
                $phone_pc = $this->request->getPost('phone_pc_01');
                $mail_pc = $this->request->getPost('email_01');
                $arrayPersonaContacto[] = [$name_pc, $position_pc, $phone_pc, $mail_pc];

                $personContact->name = $name_pc;
                $personContact->position = $position_pc;
                $personContact->phone = $phone_pc;
                $personContact->mail = $mail_pc;
                $personContact->active = 1;
                $personContact->id_customer = $id_customer;
                $this->personcontactModel->save($personContact);
            }

            /**
             * Verificamos si existe un segundo contacto
             */
            $personContact2 = new PersonContact();
            $name_pc_02 = $this->request->getPost('name_pc_02');

            if (!empty($name_pc_02)) {

                $name_pc_02 = $this->request->getPost('name_pc_02');
                $position_02 = $this->request->getPost('position_02');
                $phone_pc_02 = $this->request->getPost('phone_pc_02');
                $email_02 = $this->request->getPost('email_02');

                $personContact2->name = $name_pc_02;
                $personContact2->position = $position_02;
                $personContact2->phone = $phone_pc_02;
                $personContact2->mail = $email_02;
                $personContact2->active = 1;

                $personContact2->id_customer = $id_customer;
                $this->personcontactModel->save($personContact2);
            }

            /**
             * Verificamos si existe un tercer contacto
             */
            $personContact3 = new PersonContact();
            $name_pc_03 = $this->request->getPost('name_pc_03');

            if (!empty($name_pc_03)) {

                $name_pc_03 = $this->request->getPost('name_pc_03');
                $position_03 = $this->request->getPost('position_03');
                $phone_pc_03 = $this->request->getPost('phone_pc_03');
                $email_03 = $this->request->getPost('email_03');

                $personContact3->name = $name_pc_03;
                $personContact3->position = $position_03;
                $personContact3->phone = $phone_pc_03;
                $personContact3->mail = $email_03;
                $personContact3->active = 1;
                $personContact3->id_customer = $id_customer;
                $this->personcontactModel->save($personContact3);
            }

           $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',

                'title' => 'CLIENTE REGISTRADO CON EXITO!',
                'text' => 'El cliente se ha registrado correctamente.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }


            return redirect()->route('seeDetailCustomers', [$id_customer]);


        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',

                'title' => 'CLIENTE REGISTRADO CON EXITO!',
                'text' => 'El cliente se ha registrado correctamente.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('showFormCustomers');


        }


        /*-----Persona de Contacto*/

        $customers = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Customers/list.html.twig', ['customers' => $customers, 'pager' => $this->customersModel->pager->links()]);
    }


    public function result()
    {

        $orderCustomers = $this->ordersModel->select('id_customer')->findAll();

        // Crea un array de id_work_locations desde los resultados
        $orderCustomersIds = [];
        foreach ($orderCustomers as $c) {
            $orderCustomersIds[] = $c->id_customer;
        }

        $customers = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regPerPage);
        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);




        return $this->twig->render('Front/Customers/list.html.twig', ['orderCustomersIds' => $orderCustomersIds,'customers' => $customers, 'customers_all' => $customers_all,'pager' => $this->customersModel->pager->links()]);
    }


    //Ver detalle del Pallets provisionales
    public function seeDetailCustomers($id = null)
    {

        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->paginate(config('Configuration')->regPerPage);
        $customers = $this->customersModel->where('id_customer', $id)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Customers/seeDetailCustomers.html.twig', ['customers' => $customers, 'payment_method' => $payment_method,'pager' => $this->customersModel->pager->links()]);
    }


    //Muestra la partida seleccionada para luego editar
    public function edit($id = null)
    {

        $cont = 0;
        $contact_persons = $this->personcontactModel->where('id_customer', $id)->paginate(config('Configuration')->regPerPage);

        $name = null;

        foreach ($contact_persons as $item) {
            $cont++;
            $name = $item->name;
        }

        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->paginate(config('Configuration')->regPerPage);
        $customers = $this->customersModel->where('id_customer', $id)->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Customers/edit.html.twig', ['payment_method' => $payment_method,'customers' => $customers,'name'=>$name, 'personas_contactos' => $contact_persons, 'contador' => $cont, 'pager' => $this->customersModel->pager->links()]);
    }

    //Actualiza los datos BD
    public function editSave($id_customer = null)
    {

        if (!$this->validate(validateUpdateCustomers())) {
            return redirect()->back()
                   ->with('errors', $this->validator->getErrors())
                   ->withInput();
           }


        /**
         * controlamos que no tenga mas de 3 personas de contacto
         */
        $cont = 0;
        $array[] = 0;
        $contact_persons = $this->personcontactModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);


        foreach ($contact_persons as $item) {
            $cont++;
        }




        $db = db_connect();
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        //Obtenemos los datos del formulario por cada campo
        $data = [
            'names'  => $this->request->getPost('names'),
            'mail '  => $this->request->getPost('mail'),
            'phone'  => $this->request->getPost('phone'),

            'dni' => $this->request->getPost('dni'),
            'location' => $this->request->getPost('location'),

            'province' => $this->request->getPost('province'),
            'payment_method' => $this->request->getPost('payment_method'),
            'address' => $this->request->getPost('address'),

            'zip_code' => $this->request->getPost('zip_code'),

            'iva' => $this->request->getPost('iva'),

            'date_signing_mandate' => $this->request->getPost('date_signing_mandate'),

            'recurrent_date' => $this->request->getPost('recurrent_date'),

            'bic' => $this->request->getPost('bic'),
            'iban' => $this->request->getPost('iban'),
            'bank' => $this->request->getPost('bank'),
            'office_bank' => $this->request->getPost('office_bank'),
            'digital_control' => $this->request->getPost('digital_control'),
            'bank_count' => $this->request->getPost('bank_count'),
            'observations' => $this->request->getPost('observations'),
            'updated_at' => $date->format('Y-m-d'),

        ];
        $builder = $db->table('customers');
        $builder->getWhere(['id_customer' => $id_customer]);
        $builder->set(

            'names',
            'mail ',
            'phone',
            'dni',
            'location',
            'province',
            'payment_method',
            'address',
            'zip_code',
            'iva',

            'date_signing_mandate',
            'recurrent_date',
            'bic',
            'iban',
            'bank',
            'office_bank',
            'digital_control' ,
            'bank_count',

            'observations',
            'updated_at'

        );
        $builder->where('id_customer', $id_customer);
        $builder->update($data);




        // Consulta para verificar si existen facturas relacionadas con el cliente
            $builder = $db->table('bills');
            $builder->where('id_customer', $id_customer);
            $result = $builder->get()->getResult();

            if ($result) {
                // Si hay facturas relacionadas con el cliente, actualiza la fecha de mandato
                $newMandateDate = $this->request->getPost('date_signing_mandate');

                // Realiza la actualización en la tabla "bills"
                $builder->set('date_signing_mandate', $newMandateDate);
                $builder->where('id_customer', $id_customer);
                $builder->update();
            }

        if ($cont < 4) {
            /*-----Persona de Contacto*/

            $personContact = new PersonContact();
            $name_pc_01 = $this->request->getPost('name_pc_01');

            if (!empty($name_pc_01)) {

                $name_pc_01 = $this->request->getPost('name_pc_01');
                $position_01 = $this->request->getPost('position_01');
                $phone_pc_01 = $this->request->getPost('phone_pc_01');
                $email_01 = $this->request->getPost('email_01');

                $personContact->name = $name_pc_01;
                $personContact->position = $position_01;
                $personContact->phone = $phone_pc_01;
                $personContact->mail = $email_01;
                $personContact->active = 1;

                $personContact->id_customer = $id_customer;

                $this->personcontactModel->save($personContact);
            }

            /**
             * Verificamos si existe un segundo contacto
             */
            $personContact2 = new PersonContact();
            $name_pc_02 = $this->request->getPost('name_pc_02');
            if (!empty($name_pc_02)) {

                $name_pc_02 = $this->request->getPost('name_pc_02');
                $position_02 = $this->request->getPost('position_02');
                $phone_pc_02 = $this->request->getPost('phone_pc_02');
                $email_02 = $this->request->getPost('email_02');

                $personContact2->name = $name_pc_02;
                $personContact2->position = $position_02;
                $personContact2->phone = $phone_pc_02;
                $personContact2->mail = $email_02;
                $personContact2->active = 1;

                $personContact2->id_customer = $id_customer;
                $this->personcontactModel->save($personContact2);
            }

            /**
             * Verificamos si existe un tercer contacto
             */
            $personContact3 = new PersonContact();
            $name_pc_03 = $this->request->getPost('name_pc_03');


            try {

            if (!empty($name_pc_03)) {
                $name_pc_03 = $this->request->getPost('name_pc_03');
                $position_03 = $this->request->getPost('position_03');
                $phone_pc_03 = $this->request->getPost('phone_pc_03');
                $email_03 = $this->request->getPost('email_03');

                $personContact3->name = $name_pc_03;
                $personContact3->position = $position_03;
                $personContact3->phone = $phone_pc_03;
                $personContact3->mail = $email_03;
                $personContact3->active = 1;

                $personContact3->id_customer = $id_customer;
                $this->personcontactModel->save($personContact3);
            }


            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',

                'title' => 'CLIENTE MODIFICADO CON EXITO!',
                'text' => 'El cliente se ha modificado correctamente.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }


            return redirect()->route('updateCustomers', [$id_customer]);


                } catch (\Throwable $th) {

                    $this->log->setLine('Error', $th->getMessage());

                    $previousMsg = $this->session->getFlashdata('msg2');

                    $currentMsg = [
                        'type' => 'error',

                        'title' => 'ERROR AL MODIFICAR!',
                        'text' => 'El cliente  no se ha modificado.',
                    ];

                    if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                        $this->session->setFlashdata('msg2', $currentMsg);
                    }


                    return redirect()->route('updateCustomers', [$id_customer]);

                }

        } else {


            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ERROR AL MODIFICAR!',
                'text' => 'a superado el maximo de Personas de contactos (3).',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }


            return redirect()->route('updateCustomers', [$id_customer]);

        }


    }

    //Actualiza los datos BD
    public function editDateMandateRemesa($id_customer = null)
    {




        $array[] = 0;
        $db = db_connect();

        try {

                //Obtenemos los datos del formulario por cada campo
                $data = [
                'date_signing_mandate' => $this->request->getPost('date_signing_mandate'),

                ];
                    $builder = $db->table('customers');
                    $builder->getWhere(['id_customer' => $id_customer]);
                    $builder->set(
                    'date_signing_mandate',
                    );
                    $builder->where('id_customer', $id_customer);
                    $builder->update($data);


                    // Consulta para verificar si existen facturas relacionadas con el cliente
                    $builder = $db->table('bills');
                    $builder->where('id_customer', $id_customer);
                    $result = $builder->get()->getResult();

                if ($result) {
                    // Si hay facturas relacionadas con el cliente, actualiza la fecha de mandato
                    $newMandateDate = $this->request->getPost('date_signing_mandate');

                    // Realiza la actualización en la tabla "bills"
                    $builder->set('date_signing_mandate', $newMandateDate);
                    $builder->where('id_customer', $id_customer);
                    $builder->update();
                }

                $previousMsg = $this->session->getFlashdata('msg');

                $currentMsg = [
                    'type' => 'error',
                    'title' => 'Guardado!',
                    'text' => "Los datos han sido guardados correctamente",
                ];

                if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                  $this->session->setFlashdata('msg1', $currentMsg);
                }

               return redirect()->route('remesas');


            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());

                    $previousMsg = $this->session->getFlashdata('msg');

                    $currentMsg = [
                    'type' => 'error',
                    'title' => 'ERROR!',
                    'text' => "Ocurrió un error al guardar la fecha.",
                    ];

                    if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                    $this->session->setFlashdata('msg', $currentMsg);
                    }


                   return redirect()->route('remesas');
            }


    }


    public function borrarPersonaContactoEditarCliente($id = null)
    {
        //Mostramos el mensaje de error o correcto
        $id_customer = null;
        //Buscamos el cliente en la bd de Persona de Contacto
        $personContact_ = $this->personcontactModel->where('id_personC', $id)->find();

        foreach ($personContact_ as $item) {
            $id_customer = $item->id_customer;
        }
        //Conectamos a la BD
        $db = db_connect();
        //Conectamos a la BD, realizamos la consulta
        $builder = $db->table('personcontact');
        $builder->where('id_personC', $id);

        try {
            $builder->delete();

            return redirect()->route('updateCustomers', [$id_customer]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('updateCustomers', [$id_customer])->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al eliminar la persona de contacto']
            ]);
        }

        $contact_persons = $this->personcontactModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Customers/edit.html.twig', ['customers' => $customers, 'contact_persons' => $contact_persons, 'pager' => $this->customersModel->pager->links()]);
    }


    public function preDelete($id = null)
    {
        $customers = $this->customersModel->where('id_customer', $id)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Customers/PreDelete.html.twig', ['customers' => $customers, 'pager' => $this->customersModel->pager->links()]);
    }

    public function deleteCustomers($id = null)
    {
        //Mostramos el mensaje de error o correcto
        try {

            //Conectamos a la BD
            $db = db_connect();
            //Conectamos a la BD, realizamos la consulta
            $builder = $db->table('customers');
            $builder->where('id_customer', $id);
            $builder->delete();

                        // Configurar una respuesta JSON para éxito
                $response = [
                    'success' => true,
                    'message' => 'Cliente eliminado con éxito.'
                ];

                return $this->response->setJSON($response);
            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());

                // Configurar una respuesta JSON para error
                $response = [
                    'success' => false, // Cambiar a false para indicar un error
                    'message' => 'Error al eliminar el Cliente.'
                ];

                return $this->response->setJSON($response);
            }


    }





        public function searchforNameCustomer($id_customer = null)
        {

            $orderCustomers = $this->ordersModel->select('id_customer')->findAll();

            // Crea un array de id_work_locations desde los resultados
            $orderCustomersIds = [];
            foreach ($orderCustomers as $c) {
                $orderCustomersIds[] = $c->id_customer;
            }

              $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);
             $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

            return $this->twig->render('Front/Customers/list.html.twig', ['orderCustomersIds' => $orderCustomersIds,'customers' => $customers,'customers_all' => $customers_all, 'pager' => $this->customersModel->pager->links()]);
        }
}
