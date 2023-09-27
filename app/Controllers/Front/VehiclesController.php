<?php

namespace App\Controllers\Front;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use DateTime;
use DateTimeZone;
use Config\Services;

use App\Controllers\BaseController;
use App\Entities\Vehicles;

use App\Libraries\Log;
use App\Models\VehiclesModel;
use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;


class VehiclesController extends BaseController
{
    protected Log $log;
    protected Log $logVehiculos;

    protected $vehicleModel;
    protected $ordersModel;

    public function __construct()
    {
        $this->log = new Log('Vehicles/');
        $this->vehicleModel = model('VehiclesModel');
        $this->ordersModel = model('OrdersModel');

        helper('form');
    }


    public function index()
    {
        return $this->twig->render('Front/Vehicles/create.html.twig', ['lugar' => 'index']);
    }




    public function create()
    {


        if (!$this->validate(validateVehicleCreate())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }


        $vehicles = new Vehicles($this->request->getPost());




        $registration_date = $this->request->getPost('registration_date');
        $date_itv = $this->request->getPost('date_itv');



        if (!$registration_date) {
            $registration_date = "0000-00-00";
        }

        if (!$date_itv) {
            $date_itv = "0000-00-00";
        }

        try {
            $db = db_connect();
            $vehicles->registration_date = $registration_date;
            $vehicles->date_itv = $date_itv;
            $vehicles->active = 1;

            $this->vehicleModel->save($vehicles);
            $id_last_vehicles_insert = $db->insertID($vehicles); //Ultimo id pallet insertado



            $previousMsg = $this->session->getFlashdata('msg');
            $currentMsg = [
                'type' => 'error',

                'title' => 'VEHICULO GUARDADO !',
                'text' => ' Vehiculo registrado con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('seeDetailVehicle', [$id_last_vehicles_insert]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION !',
                'text' => 'Error al registrar el vehiculo!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('showFormVehicle');
        }
    }



    //Lista Contenedores
    public function result()
    {
        $vehicles_all = $this->vehicleModel->orderBy('id_vehicle', 'DESC')->paginate(config('Configuration')->regPerPage);
        $vehicles = $this->vehicleModel->orderBy('id_vehicle', 'DESC')->paginate(config('Configuration')->regPerPage);

        $orderVehicle = $this->ordersModel->select('id_vehicle')->findAll();


        // Crea un array de id_work_locations desde los resultados
        $VehiclesIds = [];
        foreach ($orderVehicle as $v) {
            $VehiclesIds[] = $v->id_vehicle;
        }


        return $this->twig->render('Front/Vehicles/list.html.twig', ['VehiclesIds' => $VehiclesIds,'vehicles_all' => $vehicles_all, 'vehicles' => $vehicles, 'pager' => $this->vehicleModel->pager->links()]);
    }


    //Ver detalle del contenedor
    public function seeDetailVehicle($id_vehicle = null)
    {
        $vehicles = $this->vehicleModel->where('id_vehicle', $id_vehicle)->paginate(config('Configuration')->regClientesPage);
        return $this->twig->render('Front/Vehicles/seeDetailVehicle.html.twig', ['vehicles' => $vehicles, 'pager' => $this->vehicleModel->pager->links()]);
    }



    //Muestra el Contenedor seleccionado para luego editar
    public function editVehicle($id = null)
    {

        $vehicles = $this->vehicleModel->where('id_vehicle', $id)->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Vehicles/edit.html.twig', ['vehicles' => $vehicles, 'pager' => $this->vehicleModel->pager->links()]);
    }

    public function editSaveVehicle($id_vehicle = null)
    {

        if (!$this->validate(validateVehicleEditar())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }


        $db = db_connect();

        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $name = $this->request->getPost('name');
        $car_registration = $this->request->getPost('car_registration');
        $make = $this->request->getPost('make');
        $registration_date = $this->request->getPost('registration_date');
        $date_itv = $this->request->getPost('date_itv');
        $observations = $this->request->getPost('observations');


        $data = [
            'name'  =>  $name,
            'car_registration'  =>  $car_registration,
            'make'  =>  $make,
            'registration_date' =>   $registration_date,
            'observations'  =>  $observations,
            'date_itv' =>   $date_itv,

            'updated_at' => $date->format('Y-m-d'),

        ];
        $builder = $db->table('vehicles');
        $builder->getWhere(['id_vehicle' => $id_vehicle]);
        $builder->set(
            'name',
            'car_registration',
            'make',
            'registration_date',
            'observations',
            'date_itv',
            'updated_at'

        );


        try {

            $builder->where('id_vehicle', $id_vehicle);
            $builder->update($data);


            $previousMsg = $this->session->getFlashdata('msg');
            $currentMsg = [
                'type' => 'error',

                'title' => 'VEHICULO MODIFICADO !',
                'text' => ' Vehiculo modificado con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('editVehicle', [$id_vehicle]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());

            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION !',
                'text' => 'Error al modificar el vehiculo!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('editVehicle', [$id_vehicle]);
        }
    }
    public function deleteV($id_vehicle = null)
    {


        try {

            $db = db_connect();

            $builder = $db->table('vehicles');
            $builder->where('id_vehicle', $id_vehicle);
            $builder->delete();


            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Vehiculo eliminado!'
            ];


            return $this->response->setJSON($response);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());



            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Error, vehiculo no eliminado!'
            ];


            return $this->response->setJSON($response);
        }
    }

    //busca por id  pallet 02
    public function searchforVehicle($id_vehicle = null)
    {

        $orderVehicle = $this->ordersModel->select('id_vehicle')->findAll();


        // Crea un array de id_work_locations desde los resultados
        $VehiclesIds = [];
        foreach ($orderVehicle as $v) {
            $VehiclesIds[] = $v->id_vehicle;
        }


        $vehicles = $this->vehicleModel->where('id_vehicle', $id_vehicle)->paginate(config('Configuration')->regPerPage);
        $vehicles_all = $this->vehicleModel->orderBy('id_vehicle', 'DESC')->paginate(config('Configuration')->regPerPage);


        return $this->twig->render('Front/Vehicles/list.html.twig', ['VehiclesIds' => $VehiclesIds,'vehicles' => $vehicles, 'vehicles_all' => $vehicles_all,  'pager' => $this->vehicleModel->pager->links()]);
    }
}
