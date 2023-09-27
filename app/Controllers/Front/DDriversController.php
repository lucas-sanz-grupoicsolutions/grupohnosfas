<?php

namespace App\Controllers\Front;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use DateTime;
use DateTimeZone;
use Config\Services;

use App\Entities\WorkLocation;

use App\Controllers\BaseController;
use App\Entities\DDrivers;

use App\Libraries\Log;
use App\Models\DDriversModel;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;


class DDriversController extends BaseController
{
    protected Log $log;
    protected Log $logdriver;

    protected $driversModel;
    protected $ordersModel;

    public function __construct()
    {
        $this->log = new Log('DDrivers/');
        $this->driversModel = model('Drivers_Model');
        $this->ordersModel = model('OrdersModel');

        helper('form');
    }


    public function index()
    {
        return $this->twig->render('Front/DDrivers/create.html.twig', ['lugar' => 'index']);
    }




    public function create()
    {


        if (!$this->validate(validateCreateDriver())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        try {

            $ddrivers = new DDrivers($this->request->getPost());
            $db = db_connect();
            $ddrivers->active = 1;
            $this->driversModel->save($ddrivers);


            $previousMsg = $this->session->getFlashdata('msg');
            $currentMsg = [
                'type' => 'error',

                'title' => 'CONDUCTOR GUARDADO !',
                'text' => ' Conductor registrado con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('showFormDrivers');

        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION !',
                'text' => 'Error al registrar el conductor.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('showFormDrivers');
        }
    }
    //Lista Contenedores
    public function result()
    {

        $orderDrivers = $this->ordersModel->select('id_driver')->findAll();

        // Crea un array de id_work_locations desde los resultados
        $orderDriversIds = [];
        foreach ($orderDrivers as $orderDriver) {
            $orderDriversIds[] = $orderDriver->id_driver;
        }

        $ddrivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->paginate(config('Configuration')->regPerPage);

        $ddrivers = $this->driversModel->orderBy('id_driver', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/DDrivers/list.html.twig', ['orderDriversIds' => $orderDriversIds, 'ddrivers_all' => $ddrivers_all, 'ddrivers' => $ddrivers,  'pager' => $this->driversModel->pager->links()]);
    }

    //Muestra el Contenedor seleccionado para luego editar
    public function editDrivers($id = null)
    {
        $ddrivers = $this->driversModel->where('id_driver', $id)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/DDrivers/edit.html.twig', ['ddrivers' => $ddrivers, 'pager' => $this->driversModel->pager->links()]);
    }

    public function editSaveDrivers($id_driver = null)
    {

        if (!$this->validate(validateEditDriver())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }


        $db = db_connect();

        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        $observations = $this->request->getPost('observations');
        $province = $this->request->getPost('province');



        $data = [
            'name'  =>  $name,
            'phone'  =>  $phone,
            'observations'  =>  $observations,
            'province' =>   $province,

            'updated_at' => $date->format('Y-m-d'),

        ];
        $builder = $db->table('ddrivers');
        $builder->getWhere(['id_driver' => $id_driver]);
        $builder->set(
            'name',
            'phone',
            'observations',
            'province',
            'updated_at'

        );


        try {

            $builder->where('id_driver', $id_driver);
            $builder->update($data);

            //Muestra mensaje
            return redirect()->route('listDrivers')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Conductor modificado con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listDrivers')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al modificadar el Conductor ']
            ]);
        }
    }

    public function deleteDriver($id_driver = null)
    {


        try {

            $db = db_connect();

            $builder = $db->table('ddrivers');
            $builder->where('id_driver', $id_driver);
            $builder->delete();


            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Conductor eliminado con exito!'
            ];


            return $this->response->setJSON($response);

        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());



            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Conductor eliminado con exito!'
            ];


            return $this->response->setJSON($response);
        }
    }

    //busca por id  pallet 02
    public function searchforDriver($id_driver = null)
    {


        $orderDrivers = $this->ordersModel->select('id_driver')->findAll();

        // Crea un array de id_work_locations desde los resultados
        $orderDriversIds = [];
        foreach ($orderDrivers as $orderDriver) {
            $orderDriversIds[] = $orderDriver->id_driver;
        }

        $ddrivers = $this->driversModel->where('id_driver', $id_driver)->paginate(config('Configuration')->regPerPage);
        $ddrivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->paginate(config('Configuration')->regPerPage);


        return $this->twig->render('Front/DDrivers/list.html.twig', ['orderDriversIds' => $orderDriversIds,'ddrivers' => $ddrivers, 'ddrivers_all' => $ddrivers_all,  'pager' => $this->driversModel->pager->links()]);
    }
}
