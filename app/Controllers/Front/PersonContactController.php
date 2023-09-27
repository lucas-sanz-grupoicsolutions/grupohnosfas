<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Entities\PersonContact;
use App\Entities\Customers;
use App\Libraries\Log;

use App\Models\PersonContactModel;
use App\Models\CustomersModel;

use DateTime;
use DateTimeZone;
use Config\Services;

use CodeIgniter\I18n\Time;



class PersonContactController extends BaseController
{
    protected Log $log;
    protected Log $logPersonaContacto;

    protected $personcontactModel;
    protected $customersModel;

    public function __construct()
    {

        $this->log = new Log('PersonaContacto/');
        $this->personcontactModel = model('personcontactModel');
        $this->customersModel = model('CustomersModel');
    }

    public function index()
    {
        return $this->twig->render('Front/Clientes/create.html.twig', ['lugar' => 'index']);
    }

    public function create()
    {

        /*
     if (!$this->validate(validateCreateCustomers())) {

         return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }
       */

      $customers = new PersonContact();

      $name = $this->request->getPost('name');
      $mail = $this->request->getPost('mail');
      $phone = $this->request->getPost('phone');
      $dni = $this->request->getPost('dni');
      $poblacion = $this->request->getPost('poblacion');
      $provincia = $this->request->getPost('provincia');
      $forma_pago_defecto = $this->request->getPost('forma_pago_defecto');
      $direccion = $this->request->getPost('direccion');
      $codigo_postal = $this->request->getPost('codigo_postal');
      $iva = $this->request->getPost('iva');
      $iban = $this->request->getPost('iban');
      $bic = $this->request->getPost('bic');
      $banco = $this->request->getPost('banco');
      $observaciones = $this->request->getPost('observaciones');
      $persona_contacto = $this->request->getPost('persona_contacto');
      $activo = $this->request->getPost('activo');


      $customers->name = $name;
      $customers->mail = $mail;
      $customers->phone = $phone;
      $customers->dni = $dni;
      $customers->poblacion = $poblacion;
      $customers->provincia = $provincia;
      $customers->forma_pago_defecto = $forma_pago_defecto;
      $customers->direccion = $direccion;
      $customers->codigo_postal =  $codigo_postal;
      $customers->iva = $iva;
      $customers->iban = $iban;
      $customers->bic = $bic;
      $customers->banco = $banco;
      $customers->observaciones = $observaciones;
      $customers->persona_contacto = $persona_contacto;
      $customers->active = 1;

     /*-----Persona de Contacto*/

     $name = $this->request->getPost('name');
     $mail = $this->request->getPost('mail');
     $phone = $this->request->getPost('phone');
     $dni = $this->request->getPost('dni');


     $customers->name = $name;
     $customers->mail = $mail;
     $customers->phone = $phone;
     $customers->dni = $dni;

        try {

            $this->customersModel->save($customers);



            return redirect()->route('showFormCustomers')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Cliente registrado con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('showFormCustomers')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al crear el cliente']
            ]);
        }

        $customers = $this->customersModel->orderBy('id_cliente', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Clientes/list.html.twig', ['customers' => $customers, 'pager' => $this->customersModel->pager->links()]);
    }


    public function result()
    {
        $customers = $this->customersModel->orderBy('id_cliente', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Clientes/list.html.twig', ['customers' => $customers, 'pager' => $this->customersModel->pager->links()]);
    }


    //Ver detalle del Pallets provisionales
    public function seeDetailCustomers($id = null)
    {
        /*
        if (!$this->validate(validateUpdatePartida())) {
            return redirect()->back()
                   ->with('errors', $this->validator->getErrors())
                   ->withInput();
           }
        */

        $customers = $this->customersModel->where('id_cliente', $id)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Clientes/seeDetailCustomers.html.twig', ['customers' => $customers, 'pager' => $this->customersModel->pager->links()]);
    }



       //Muestra la Persona de Contato seleccionada para luego editar
    public function edit($id_personC = null)
    {

        $id_customer = 0;
        $personContact = $this->personcontactModel->where('id_personC', $id_personC)->paginate(config('Configuration')->regPerPage);

        foreach ($personContact as $item){
            $id_customer = $item->id_customer;
        }

        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

        foreach ($customers as $row){
            $name = $row->name;
        }

        return $this->twig->render('Front/PersonContact/edit.html.twig', ['personcontacts' => $personContact,'id_customer'=>$id_customer,'name'=>$name,'pager' => $this->personcontactModel->pager->links()]);

    }


    //Actualiza los datos BD
    public function editSave($id_personC = null)
    {
/*
        if (!$this->validate(validateUpdateCustomers())) {
            return redirect()->back()
                   ->with('errors', $this->validator->getErrors())
                   ->withInput();
           }
*/

            /**
             * Buscar en Persona contacto model el numero de cliente
             */
            $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

            $id_customer = 0;
            $person_contact = $this->personcontactModel->where('id_personC', $id_personC)->paginate(config('Configuration')->regPerPage);

            foreach ($person_contact as $item){
                $id_customer = $item->id_customer;
            }


        try {
            //Conectamos a la BD
            $db = db_connect();

            //Obtenemos los datos del formulario por cada campo
            $data = [
                'name' => $this->request->getPost('name'),
                'position' => $this->request->getPost('position'),
                'phone' => $this->request->getPost('phone'),
                'mail' => $this->request->getPost('mail'),

                'updated_at' => $date->format('Y-m-d'),
            ];

            $builder = $db->table('personcontact');
            $builder->getWhere(['id_personC' => $id_personC]);
            $builder->set('name', 'position', 'phone', 'mail');
            $builder->where('id_personC', $id_personC);
            $builder->update($data);

            //Muestra mensaje
            return redirect()->route('editPersonContact',[$id_personC])->with('msg', [
                'type' => 'alert-success',
                'body' => ['Persona de Contacto modificada con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('editPersonContact',[$id_personC])->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al modificar la Persona de Contacto']
            ]);
        }
        //Mostramos los datos actualizados
        $personcontacts = $this->personcontactModel->where('id_personC', $id_personC)->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/PersonContact/edit.html.twig', ['personcontacts' => $personcontacts, 'pager' => $this->personcontactModel->pager->links()]);
    }


    public function preDelete($id = null)
    {
        $customers = $this->customersModel->where('id_customers', $id)->paginate(config('Configuration')->regPerPage);
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
            $builder->where('id_customers', $id);
            $builder->delete();

            return redirect()->route('listCustomers')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Cliente eliminado con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listCustomers')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al eliminar el cliente']
            ]);
        }

        $customers = $this->customersModel->orderBy('id_customers', 'ASC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Customers/list.html.twig', ['customers' => $customers, 'pager' => $this->customersModel->pager->links()]);
    }

}
