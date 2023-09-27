<?php

namespace App\Controllers\Front;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use DateTime;
use DateTimeZone;
use Config\Services;

use App\Controllers\BaseController;
use App\Entities\WorkLocation;
use App\Entities\Customers;
use App\Entities\Containers;
use App\Entities\ActualState;

use App\Libraries\Log;
use App\Models\WorkLocationModel;
use App\Models\CustomersModel;
use App\Models\ContainersModel;
use App\Models\ActualStateModel;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;


class WorkLocationsController extends BaseController
{
    protected Log $log;
    protected Log $logworklocations;
    protected $worklocationModel;
    protected $containersModel;
    protected $actualstateModel;
    protected $idContainerActualState;
    protected $customersModel;
    protected $ordersModel;


    public function __construct()
    {
        $this->log = new Log('WorkLocations/');
        $this->worklocationModel = model('WorkLocationModel');
        $this->customersModel = model('CustomersModel');
        $this->containersModel = model('ContainersModel');
        $this->actualstateModel = model('ActualStateModel');
        $this->ordersModel = model('OrdersModel');

        helper('form');
    }


    public function index()
    {
        $customers = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);



        return $this->twig->render('Front/WorkLocations/create.html.twig', ['lugar' => 'index', 'customers' => $customers, 'pager' => $this->customersModel->pager->links()]);
    }


    public function create()
    {


        if (!$this->validate(validateCreateWorkLocations())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $worklocationModel = new WorkLocation($this->request->getPost());
        $id_customer = $this->request->getPost('id_customer');
        $customers_name = $this->customersModel->getNameCustomers($id_customer);


        try {
            $db = db_connect();
            $worklocationModel->active = 1;
            $worklocationModel->name_customer = $customers_name;
            $this->worklocationModel->save($worklocationModel);
            $id_last_wl_insert = $db->insertID($worklocationModel); //Ultimo id pallet insertado



            $previousMsg = $this->session->getFlashdata('msg');
            $currentMsg = [
                'type' => 'error',

                'title' => 'DIRECCION GUARDADA !',
                'text' => ' Direccion de Obra registrada con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('seeDetailWorkLocations', [$id_last_wl_insert]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION !',
                'text' => 'Error al crear la Direccion de Obra!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('showFormWorkLocations');
        }
    }



    //Lista
    public function result()
    {


        $orderWorkLocations = $this->ordersModel->select('id_work_location')->findAll();

        // Crea un array de id_work_locations desde los resultados
        $orderWorkLocationIds = [];
        foreach ($orderWorkLocations as $orderWorkLocation) {
            $orderWorkLocationIds[] = $orderWorkLocation->id_work_location;
        }


        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);
        $worklocations_all = $this->worklocationModel->orderBy('id_work_locations', 'DESC')->paginate(config('Configuration')->regClientesPage);

        $worklocations = $this->worklocationModel->orderBy('id_work_locations', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/WorkLocations/list.html.twig', ['orderWorkLocationIds' => $orderWorkLocationIds, 'customers_all' => $customers_all, 'worklocations_all' => $worklocations_all, 'worklocations' => $worklocations, 'pager' => $this->worklocationModel->pager->links()]);
    }



    //Ver detalle del contenedor
    public function seeDetailWorkLocations($id_work_locations = null)
    {
        $worklocations = $this->worklocationModel->where('id_work_locations', $id_work_locations)->paginate(config('Configuration')->regClientesPage);
        return $this->twig->render('Front/WorkLocations/seeDetailWorkLocations.html.twig', ['worklocations' => $worklocations, 'pager' => $this->worklocationModel->pager->links()]);
    }



    public function edit($id_work_locations = null)
    {
        $worklocations = $this->worklocationModel->where('id_work_locations', $id_work_locations)->paginate(config('Configuration')->regClientesPage);
        return $this->twig->render('Front/WorkLocations/edit.html.twig', ['worklocations' => $worklocations, 'pager' => $this->worklocationModel->pager->links()]);
    }



    public function editSave($id_work_locations = null)
    {


        if (!$this->validate(validateEditWorkLocations())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $db = db_connect();
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        //Obtenemos los datos del formulario por cada campo
        $data = [
            'location' => $this->request->getPost('location'),
            'province' => $this->request->getPost('province'),
            'address' => $this->request->getPost('address'),
            'zip_code' => $this->request->getPost('zip_code'),
            'observations' => $this->request->getPost('observations'),
            'updated_at' => $date->format('Y-m-d'),
        ];
        $builder = $db->table('work_locations');
        $builder->getWhere(['id_work_locations' => $id_work_locations]);
        $builder->set(

            'location',
            'province',
            'address',
            'zip_code',
            'observations',
            'updated_at'
        );


        try {
            $builder->where('id_work_locations', $id_work_locations);
            $builder->update($data);



            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',

                'title' => 'DIRECCION MODIFICADA!',
                'text' => 'Direccion modificada con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('editWorkLocations', [$id_work_locations]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());

            $previousMsg = $this->session->getFlashdata('msg2');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION!',
                'text' => 'Error al modificar la Direccion!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }
            return redirect()->route('editWorkLocations', [$id_work_locations]);
        }
    }




    public function deleteWorkLocation($id_work_locations = null)
    {


        try {

            $db = db_connect();

            $builder = $db->table('work_locations');
            $builder->where('id_work_locations', $id_work_locations);
            $builder->delete();


            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Direccion de obra eliminada con exito!'
            ];


            return $this->response->setJSON($response);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());



            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Direccion de obra eliminada con exito!'
            ];


            return $this->response->setJSON($response);
        }
    }
    /**
     * Actulizamos el estado actual
     */
    public function UpDateActualStateContainer($id_container = null)
    {

        $idContainerActualState = $this->worklocationModel->where('id_container', $id_container)->find();

        foreach ($idContainerActualState as $id_actual_state) {

            //contenedores
            $id_actual_state = $id_actual_state;

            //Revisa si existe el contenedor en esa direccion
            $worklocations = $this->worklocationModel->where('id_container', $id_container)->paginate(config('Configuration')->regClientesPage);
        }
    }

    //busca por id  pallet 02
    public function searchforWorkLocation($id_work_locations = null)
    {


        $orderWorkLocations = $this->ordersModel->select('id_work_location')->findAll();

        // Crea un array de id_work_locations desde los resultados
        $orderWorkLocationIds = [];
        foreach ($orderWorkLocations as $orderWorkLocation) {
            $orderWorkLocationIds[] = $orderWorkLocation->id_work_location;
        }


        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);

        $worklocations = $this->worklocationModel->where('id_work_locations', $id_work_locations)->paginate(config('Configuration')->regPerPage);
        $worklocations_all = $this->worklocationModel->orderBy('id_work_locations', 'DESC')->paginate(config('Configuration')->regPerPage);


        return $this->twig->render('Front/WorkLocations/list.html.twig', ['orderWorkLocationIds' => $orderWorkLocationIds,'customers_all' => $customers_all, 'worklocations' => $worklocations, 'worklocations_all' => $worklocations_all,  'pager' => $this->worklocationModel->pager->links()]);
    }

    //busca por id  pallet 02
    public function searchforCustomersWorkLocation($id_customer = null)
    {

        $orderWorkLocations = $this->ordersModel->select('id_work_location')->findAll();

        // Crea un array de id_work_locations desde los resultados
        $orderWorkLocationIds = [];
        foreach ($orderWorkLocations as $orderWorkLocation) {
            $orderWorkLocationIds[] = $orderWorkLocation->id_work_location;
        }


        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);

        $worklocations = $this->worklocationModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        $worklocations_all = $this->worklocationModel->orderBy('id_work_locations', 'DESC')->paginate(config('Configuration')->regPerPage);


        return $this->twig->render('Front/WorkLocations/list.html.twig', ['orderWorkLocationIds' => $orderWorkLocationIds,'customers_all' => $customers_all, 'worklocations' => $worklocations, 'worklocations_all' => $worklocations_all,  'pager' => $this->worklocationModel->pager->links()]);
    }
}
