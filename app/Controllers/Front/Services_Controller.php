<?php

namespace App\Controllers\Front;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use DateTime;
use DateTimeZone;
use Config\Services;

use App\Controllers\BaseController;
use App\Entities\Services_;

use App\Libraries\Log;
use App\Models\Services_Model;
use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;


class Services_Controller extends BaseController
{
    protected Log $log;
    protected Log $logService;

    protected $serviceModel;
    protected $ordersModel;

    public function __construct()
    {
        $this->log = new Log('Services/');
        $this->serviceModel = model('Services_Model');
        $this->ordersModel = model('OrdersModel');

        helper('form');
    }


    public function index()
    {
        return $this->twig->render('Front/Services/create.html.twig', ['lugar' => 'index']);
    }


    public function create()
    {


        if (!$this->validate(validateServicesCreate())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }


        $services = new Services_($this->request->getPost());
        $services->active = 1;


        try {

            $this->serviceModel->save($services);


            $previousMsg = $this->session->getFlashdata('msg');
            $currentMsg = [
                'type' => 'error',

                'title' => 'Servicio creado !',
                'text' => 'El servicio se ha creado con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('listService');
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION !',
                'text' => 'Error al crear el servicio!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('listService');
        }
    }


    //Lista Contenedores
    public function result()
    {

        $orderServices = $this->ordersModel->select('id_service')->findAll();

        // Crea un array de id_work_locations desde los resultados
        $orderServicesIds = [];
        foreach ($orderServices as $s) {
            $orderServicesIds[] = $s->id_service;
        }


        $services = $this->serviceModel->orderBy('id_service', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Services/list.html.twig', ['orderServicesIds' => $orderServicesIds,'services' => $services, 'pager' => $this->serviceModel->pager->links()]);
    }


    //Muestra el Contenedor seleccionado para luego editar
    public function edit($id_service = null)
    {
        $services = $this->serviceModel->where('id_service', $id_service)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Services/edit.html.twig', ['services' => $services, 'pager' => $this->serviceModel->pager->links()]);
    }


    public function editSave($id_service = null)
    {

        if (!$this->validate(validateServicesEdit())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }


        $db = db_connect();

        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $name = $this->request->getPost('name');
        $code = $this->request->getPost('code');


        $data = [
            'name'  =>  $name,
            'code'  =>  $code,

            'updated_at' => $date->format('Y-m-d'),
        ];

        $builder = $db->table('services');
        $builder->getWhere(['id_service' => $id_service]);
        $builder->set(
            'name',
            'code',

            'updated_at'
        );


        try {
            $builder->where('id_service', $id_service);
            $builder->update($data);

            $previousMsg = $this->session->getFlashdata('msg');
            $currentMsg = [
                'type' => 'error',

                'title' => 'Servicio modificado !',
                'text' => 'El servicio se ha modificado con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('editService',[$id_service]);

        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION !',
                'text' => 'Error al modificar el servicio!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('editService',[$id_service]);
        }
    }

    public function deleteService($id_service = null)
    {

        try {

            $db = db_connect();

            $builder = $db->table('services');
            $builder->where('id_service', $id_service);
            $builder->delete();

            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Servicio eliminado con exito!'
            ];


            return $this->response->setJSON($response);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());



            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Error al eliminar el servicio'
            ];


            return $this->response->setJSON($response);
        }
    }
}
