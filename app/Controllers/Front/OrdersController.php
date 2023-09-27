<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

use App\Libraries\Log;
use DateTime;
use DateTimeZone;
use Config\Services;
use CodeIgniter\I18n\Time;

use App\Entities\Orders;


class OrdersController extends BaseController
{
    protected Log $log;

    protected $albaranesModel;
    protected $personContactModel;
    protected $customersModel;
    protected $driversModel;
    protected $servicesModel;
    protected $workLocationModel;
    protected $vehiclesModel;
    protected $containerModel;

    protected $paymentMethodModel;
    protected $ratesModel;
    protected $supplementsModel;
    protected $actualstateModel;
    protected $driver;
    protected $retainerModel;
    protected $ordersModel;
    protected $pager;


    protected $arrayNombre = [];

    public function __construct()
    {
        $this->ordersModel = model('OrdersModel');
        $this->log = new Log('Albaranes/');
        $this->albaranesModel = model('AlbaranesModel');
        $this->personContactModel = model('PersonContactModel');
        $this->customersModel = model('CustomersModel');
        $this->driversModel = model('Drivers_Model');
        $this->servicesModel = model('Services_Model');
        $this->workLocationModel = model('WorkLocationModel');
        $this->vehiclesModel = model('VehiclesModel');
        $this->containerModel = model('ContainersModel');

        $this->paymentMethodModel = model('PaymentMethodModel');
        $this->ratesModel = model('RatesModel');
        $this->supplementsModel = model('SupplementsModel');
        $this->actualstateModel = model('ActualStateModel');
        $this->retainerModel = model('RetainerModel');

        $this->pager = \Config\Services::pager();
    }

    public function index()
    {

        $withoutCustomer = false;

        $services_ = $this->servicesModel->orderBy('id_service', 'ASC')->findAll();
        $supplements = $this->supplementsModel->orderBy('id_supplements', 'ASC')->findAll();
        $rates = $this->ratesModel->orderBy('id_rates', 'DESC')->findAll();
        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'DESC')->findAll();
        $vehicles = $this->vehiclesModel->orderBy('id_vehicle', 'DESC')->findAll();

        $worklocations = $this->workLocationModel->orderBy('id_work_locations', 'DESC')->findAll();
        $containers = $this->containerModel->orderBy('id_container', 'DESC')->findAll();
        $services = $this->servicesModel->orderBy('id_service', 'DESC')->findAll();
        $drivers = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();
        $customers = $this->customersModel->orderBy('id_customer', 'ASC')->findAll();
        $all_customers = $this->customersModel->orderBy('id_customer', 'ASC')->paginate(config('Configuration')->regPerPage);
        $orders = $this->ordersModel->orderBy('id_order','DESC')->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Orders/create.html.twig', ['lugar' => 'index','customers'=>$customers, 'services_' => $services_, 'withoutCustomer' => $withoutCustomer, 'orders' => $orders, 'supplements' => $supplements, 'rates' => $rates, 'containers' => $containers, 'vehicles' => $vehicles, 'worklocations' => $worklocations, 'all_customers' => $all_customers, 'services' => $services, 'drivers' => $drivers, 'payment_method' => $payment_method, 'pager' => $this->ordersModel->pager->links()]);
    }

    public function getIdWorkLocationCustomersOrders($id_customer = null)
    {

        $countWorkLocacion = false;
        $actual_state_container_customer = null;
        $id_last_albaran_insert = 1;
        $payment_method_customer = null;

        $c_select = true;

        $containers = $this->containerModel->orderBy('id_container', 'DESC')->findAll();

        $vehicles = $this->vehiclesModel->orderBy('id_vehicle', 'DESC')->findAll();
        $drivers = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        $services_ = $this->servicesModel->orderBy('id_service', 'ASC')->findAll();
        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->paginate(config('Configuration')->regPerPage);

        $all_customers = $this->customersModel->orderBy('id_customer','ASC')->paginate(config('Configuration')->regPerPage);

        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

        $customersId = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);



        /**
         * Si no ahi direciones de obras con esos albaranes
         */
        if (empty($worklocations) || $worklocations === NULL || $worklocations === 0) {
            $c_select = false;
        }


        return $this->twig->render('Front/Orders/create.html.twig', [

            'lugar' => 'index',
            'actual_state_container_customer' => $actual_state_container_customer,
            'id_last_albaran_insert' => $id_last_albaran_insert,

            'customersId' => $customersId,
            'containers'=>$containers,

            'all_customers' => $all_customers,
            'countWorkLocacion' => $countWorkLocacion,

            'c_select' => $c_select,

            'worklocations' => $worklocations,

            'services_' => $services_,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
            'payment_method' => $payment_method,

            'pager' => $this->customersModel->pager->links()
        ]);
    }


    public function getIdWorkLocationOrders($id_work_location = null)
    {

        $countWorkLocacion = false;
        $actual_state_container_customer = null;
        $id_last_albaran_insert = null;
        $payment_method_customer = null;

        $c_select = true;


        //obtener
        $worklocations_selected = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);
        foreach ($worklocations_selected as $id) {
            $id_customer = $id->id_customer;
        }

        $containers = $this->containerModel->orderBy('id_container', 'DESC')->findAll();

        $vehicles = $this->vehiclesModel->orderBy('id_vehicle', 'DESC')->findAll();
        $drivers = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        $services_ = $this->servicesModel->orderBy('id_service', 'ASC')->findAll();
        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->paginate(config('Configuration')->regPerPage);

        $all_customers = $this->customersModel->orderBy('id_customer', 'ASC')->findAll();

        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        $customersId = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);



        /**
         * Si no ahi direciones de obras con esos albaranes
         */
        if (empty($worklocations_selected) || $worklocations_selected === NULL || $worklocations_selected === 0) {
            $c_select = false;
        }

        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->findAll();


        return $this->twig->render('Front/Orders/create.html.twig', [

            'lugar' => 'index',
            'actual_state_container_customer' => $actual_state_container_customer,
            'id_last_albaran_insert' => $id_last_albaran_insert,
            'customersId' => $customersId,
            'all_customers' => $all_customers,
            'countWorkLocacion' => $countWorkLocacion,

            'worklocations' => $worklocations,
            'containers' => $containers,

            'worklocations_selected' => $worklocations_selected,

            'services_' => $services_,
            'drivers' => $drivers,
            'vehicles' => $vehicles,
            'payment_method' => $payment_method,
            'c_select' => $c_select,
            'pager' => $this->customersModel->pager->links()
        ]);
    }



    public function create()
    {


       $name_work_location = null;
       $name_customer = null;
       $location = null;
       $province = null;
       $address = null;
       $zip_code =null;
       $iDpayment_method = 0;
       $name_payment_method = 0;

       $_SESSION['idUser'];
       $idUser = session()->get('idUser');
       $name_user = session()->get('name');



        $db = db_connect();
        $orders = new Orders($this->request->getPost());

        $id_customer = $this->request->getPost('id_customer');

        if(!$id_customer){


            $previousMsg = $this->session->getFlashdata('msg1');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION!',
                'text' => 'Debe seleccionar un cliente.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg1', $currentMsg);
            }


            return redirect()->route('showFormOrders')->with('id_customer', $id_customer);

        }




        $id_work_locations = $this->request->getPost('id_work_locations');
        if(!$id_work_locations){

            $previousMsg = $this->session->getFlashdata('msg3');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION!',
                'text' => 'Debe seleccionar una direccion de obra.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg3', $currentMsg);
            }


            return redirect()->route('getIdWorkLocationCustomersOrders',[$id_customer]);

        //   return redirect()->route('getIdWorkLocationCustomersOrders',[$id_customer])->with('msg', [
        //         'type' => 'alert-danger',
        //         'body' => ['Debe seleccionar una direccion de obra.']
        //     ]);
        }

        $id_container = $this->request->getPost('id_container');
        if(!$id_container){



            $previousMsg = $this->session->getFlashdata('msg4');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION!',
                'text' => 'Debe seleccionar un contenedor.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg4', $currentMsg);
            }


            return redirect()->route('getIdWorkLocationCustomersOrders',[$id_customer]);

        //   return redirect()->route('getIdWorkLocationCustomersOrders',[$id_customer])->with('msg', [
        //         'type' => 'alert-danger',
        //         'body' => ['Debe seleccionar un contenedor.']
        //     ]);
        }

        $id_service = $this->request->getPost('id_service');
        if(!$id_service){

            $previousMsg = $this->session->getFlashdata('msg5');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION!',
                'text' => ' Debe seleccionar un servicio.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg5', $currentMsg);
            }

            return redirect()->route('getIdWorkLocationCustomersOrders',[$id_customer]);

            // return redirect()->route('getIdWorkLocationCustomersOrders',[$id_customer])->with('msg', [
            //     'type' => 'alert-danger',
            //     'body' => ['Debe seleccionar un servicio.']
            // ]);
        }

        $planned_date = $this->request->getPost('planned_date');
        if(!$planned_date){

            $previousMsg = $this->session->getFlashdata('msg6');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION!',
                'text' => ' Debe seleccionar una fecha de planificacion o solicitud.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg6', $currentMsg);
            }

            return redirect()->route('getIdWorkLocationCustomersOrders',[$id_customer]);

        //   return redirect()->route('getIdWorkLocationCustomersOrders',[$id_customer])->with('msg', [
        //         'type' => 'alert-danger',
        //         'body' => ['Debe seleccionar una fecha de planificacion o solicitud.']
        //     ]);
        }

        $notas = $this->request->getPost('notas');

        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

        foreach ($customers as $item) {
            $name_customer = $item->names;
            $iDpayment_method = $item->payment_method;
        }

        $payment_method = $this->paymentMethodModel->where('id_payment_method', $iDpayment_method)->findAll();
        foreach($payment_method as $item2){
            $name_payment_method = $item2->name;
        }


        $work_locations = $this->workLocationModel->where('id_work_locations', $id_work_locations)->paginate(config('Configuration')->regPerPage);

        foreach ($work_locations as $rows) {

            $location = $rows->location;
            $province = $rows->province;
            $address = $rows->address;
            $zip_code = $rows->zip_code;

        }

        $name_work_location = 'Calle/Adva ' . $address . ' ' . $location . ' ' . $province . ' cp' . $zip_code;


        $orders->active = 1;
        $orders->state = "Pendiente";
        $orders->id_customer = $id_customer;
        $orders->id_container = $id_container;
        $orders->id_work_location = $id_work_locations;

        $orders->id_service = $id_service;
        $orders->name_customer = $name_customer;
        $orders->name_work_location = $name_work_location;
        $orders->planned_date = $planned_date;
        $orders->notas = $notas;

        $orders->payment_method = $name_payment_method;


        $orders->id_user = $idUser;
        $orders->name_user = $name_user;


        try {

        $this->ordersModel->save($orders);
        $id_last_orders_insert = $db->insertID($orders); //Ultimo id pallet insertado
        // dd($supplements);


            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',

                'title' => 'PEDIDO GUARDADO!',
                'text' => ' Pedido registrado con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('seeDetailOrder',[$id_last_orders_insert]);
            // return redirect()->route('seeDetailOrder', [$id_last_orders_insert])->with('msg', [
            //     'type' => 'alert-success',
            //     'body' => ['Pedido registrado con exito!']
            // ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());


            $previousMsg = $this->session->getFlashdata('msg2');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ERROR!',
                'text' => ' Pedido no se ha registrado.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('showFormOrders');


        }
    }


    public function result()
    {
        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();

        $drivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        $services = $this->servicesModel->orderBy('id_service', 'DESC')->findAll();

        $containers = $this->containerModel->orderBy('id_container', 'DESC')->findAll();


        $id_customer = $this->request->getGet('customer_id');
        $id_work_locations = $this->request->getGet('id_work_locations');
        $driver = $this->request->getGet('driver');
        $created_at = $this->request->getGet('created_at');
        $state = $this->request->getGet('state');
        $planned_date = $this->request->getGet('planned_date');


       if (count($_GET) > 0) {

            $orders = $this->ordersModel->select('*');

            if (!empty($id_customer)) {
                $orders->where('id_customer', $id_customer);
            }

            if (!empty($id_work_locations)) {
                $orders->where('id_work_location', $id_work_locations);
            }

            if (!empty($driver)) {
                $orders->where('id_driver', $driver);
            }

            if (!empty($created_at)) {
                $orders->where('created_at', $created_at);
            }
            if (!empty($state)) {
                $orders->where('state', $state);

                if ($state === 'Pendiente') {
                    $orders->where('state', 'Pendiente');
                    // Hacer algo si se seleccionó la opción 1
                } if ($state === 'Asignado') {
                    $orders->where('state', 'Asignado');
                    // Hacer algo si se seleccionó la opción 2
                } if ($state === 'Facturado') {
                    $orders->where('state', 'Facturado');
                    // Hacer algo si se seleccionó la opción 3
                }
            }

            if (!empty($planned_date)) {
                $orders->where('planned_date', $planned_date);
            }

            $orders = $this->ordersModel->paginate(config('Configuration')->regPerPage);



        } else {
            $orders = $this->ordersModel->orderBy('id_order', 'DESC')->paginate(config('Configuration')->regPerPage);
        }

 //dd($orders);

        return $this->twig->render('Front/Orders/list.html.twig', [
        'orders' => $orders,
        'services'=>$services,
        'containers'=>$containers,


        'drivers_all' => $drivers_all,
         'customers_all' => $customers_all,
         'pager' => $this->ordersModel->pager->links()]);
    }




    //Ver detalle del
    public function seeDetailOrder($id_order = null)
    {


        $id_customer = null;
        $id_vehicle = null;
        $id_driver = null;
        $id_service = null;
        $id_work_locations = null;
        $notas = null;

        $orders = $this->ordersModel->where('id_order', $id_order)->paginate(config('Configuration')->regPerPage);

        foreach ($orders as $rows) {
            $id_work_locations = $rows->id_work_location;
            $id_container = $rows->id_container;
            $id_service = $rows->id_service;
            $id_vehicle = $rows->id_vehicle;
            $id_driver = $rows->id_driver;
        }

        $containers = $this->containerModel->where('id_container', $id_container)->paginate(config('Configuration')->regPerPage);
        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_locations)->paginate(config('Configuration')->regPerPage);
        $services_ = $this->servicesModel->where('id_service', $id_service)->paginate(config('Configuration')->regPerPage);
        $vehicles = $this->vehiclesModel->where('id_vehicle', $id_vehicle)->paginate(config('Configuration')->regPerPage);
        $drivers = $this->driversModel->where('id_driver', $id_driver)->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Orders/seeDetailOrder.html.twig', [
            'lugar' => 'index',
            'worklocations' => $worklocations,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'services_' => $services_,
            'containers' => $containers,
            'orders' => $orders,
            'id_order'=>$id_order,
            'pager' => $this->ordersModel->pager->links()
        ]);
    }


    public function edit($id_order = null)
    {

        $withoutCustomer = false;
        $countWorkLocacion = false;
        $actual_state_container_customer = null;
        $wordklocation_customers = true;
        $id_service = null;



        $orders = $this->ordersModel->where('id_order', $id_order)->paginate(config('Configuration')->regPerPage);

        foreach ($orders as $rows) {
            $id_work_locations = $rows->id_work_location;
            $id_customer = $rows->id_customer;
            $id_service = $rows->id_service;
        }

        $containers = $this->containerModel->orderBy('id_container','ASC')->findAll();

        $vehicles = $this->vehiclesModel->orderBy('id_vehicle', 'DESC')->findAll();
        $drivers = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();
        $services_ = $this->servicesModel->orderBy('id_service', 'ASC')->findAll();

        $all_customers = $this->customersModel->orderBy('id_customer','ASC')->findAll();

        $worklocations_selected = $this->workLocationModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

        //ok
        $customersId = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);


        /**
         * Si no ahi direciones de obras con esos albaranes
         */
        if (empty($worklocations) || $worklocations === NULL || $worklocations === 0) {
            $wordklocation_customers = false;
        }


        return $this->twig->render('Front/Orders/edit.html.twig', [


            'actual_state_container_customer' => $actual_state_container_customer,

            'customersId' => $customersId,
            'containers'=> $containers,

            'all_customers' => $all_customers,
            'countWorkLocacion' => $countWorkLocacion,
            'orders' => $orders,

            'id_order' => $id_order,
            'id_service'=>$id_service,

            'services_' => $services_,
            'drivers' => $drivers,
            'vehicles' => $vehicles,

            'worklocations' => $worklocations,

            'wordklocation_customers' => $wordklocation_customers,
            'worklocations_selected' => $worklocations_selected,

            'pager' => $this->ordersModel->pager->links()
        ]);

    }


    public function editSaveOrders()
    {

        $db = db_connect();

        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $id_customer = $this->request->getPost('id_customer');




        $id_work_locations = $this->request->getPost('id_work_locations');
        $id_service = $this->request->getPost('id_service');
        $id_driver = $this->request->getPost('id_driver');
        $planned_date = $this->request->getPost('planned_date');
        $notas = $this->request->getPost('notas');
        $id_order = $this->request->getPost('id_order');

        $id_container = $this->request->getPost('id_container');

        if($id_container){
            $id_customer = $this->request->getPost('id_customer');
        }else{
            $id_container = $this->request->getPost('id_container_old');
        }




           $data = [
               'id_customer'  =>  $id_customer,
               'id_container'  =>  $id_container,
               'id_work_location' => $id_work_locations,
               'id_service'  =>  $id_service,
               'id_driver' =>   $id_driver,
               'planned_date' => $planned_date,
               'notas' =>  $notas,
               'updated_at' => $date->format('Y-m-d'),

           ];
           $builder = $db->table('orders');
           $builder->getWhere(['id_order' => $id_order]);
           $builder->set(
               'id_customer',
               'id_container',
               'id_work_location',
               'id_service',
               'id_driver',
               'planned_date',
               'notas',

               'updated_at'

           );


        try {

            $builder->where('id_order', $id_order);
            $builder->update($data);


            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',

                'title' => 'PEDIDO MODIFICADO!',
                'text' => 'Pedido modificado con  exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('editOrders',[$id_order]);



        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());

            $previousMsg = $this->session->getFlashdata('msg2');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION!',
                'text' => 'Error al modificar el Pedido!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }
            return redirect()->route('editOrders',[$id_order]);

        }
    }

    public function deleteOrders($id_order = null)
    {



       try {

        $db = db_connect();

        $builder = $db->table('orders');
        $builder->where('id_order', $id_order);
        $builder->delete();

                   // Configurar una respuesta JSON para éxito
                   $response = [
                    'success' => true,
                    'message' => 'Pedido eliminado con éxito.'
                ];

                return $this->response->setJSON($response);
            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());

                // Configurar una respuesta JSON para error
                $response = [
                    'success' => false, // Cambiar a false para indicar un error
                    'message' => 'Error al eliminar el pedido.'
                ];

                return $this->response->setJSON($response);
            }


    //     //Muestra mensaje


    }

    public function asignDriverVehicle($id_order = null)
    {

        $withoutCustomer = false;
        $countWorkLocacion = false;
        $actual_state_container_customer = null;
        $wordklocation_customers = true;
        $id_service = null;

        $orders = $this->ordersModel->where('id_order', $id_order)->paginate(config('Configuration')->regPerPage);

        foreach ($orders as $rows) {
            $id_work_locations = $rows->id_work_location;
            $id_customer = $rows->id_customer;
            $id_service = $rows->id_service;
        }


        $vehicles = $this->vehiclesModel->orderBy('id_vehicle', 'DESC')->findAll();
        $drivers = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        $all_customers = $this->customersModel->orderBy('id_customer','ASC')->paginate(config('Configuration')->regPerPage);
        $worklocations_selected = $this->workLocationModel->where('id_work_locations', $id_work_locations)->paginate(config('Configuration')->regPerPage);
        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        //ok
        $customersId = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);



        /**
         * Si no ahi direciones de obras con esos albaranes
         */
        if (empty($worklocations) || $worklocations === NULL || $worklocations === 0) {
            $wordklocation_customers = false;
        }



        return $this->twig->render('Front/Orders/asignDriverVehicle.html.twig', [


            'actual_state_container_customer' => $actual_state_container_customer,

            'customersId' => $customersId,

            'all_customers' => $all_customers,
            'countWorkLocacion' => $countWorkLocacion,
            'orders' => $orders,

            'id_order' => $id_order,
            'id_service'=>$id_service,

            'drivers' => $drivers,
            'vehicles' => $vehicles,

            'worklocations' => $worklocations,

            'wordklocation_customers' => $wordklocation_customers,
            'worklocations_selected' => $worklocations_selected,

            'pager' => $this->ordersModel->pager->links()
        ]);

    }



    public function asignDriverVehicleSave()
    {


        $state = "Asignado";
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $db = db_connect();

        $id_driver = $this->request->getPost('id_driver');
        $id_vehicle = $this->request->getPost('id_vehicle');
        $id_order = $this->request->getPost('id_order');


           $data = [

               'id_driver' =>   $id_driver,
               'id_vehicle' => $id_vehicle,
               'state' => $state,

               'updated_at' => $date->format('Y-m-d'),

           ];
           $builder = $db->table('orders');
           $builder->getWhere(['id_order' => $id_order]);
           $builder->set(
               'id_driver',
               'id_vehicle',
               'state',

               'updated_at'

           );


        try {

            $builder->where('id_order', $id_order);
            $builder->update($data);

            $previousMsg = $this->session->getFlashdata('msg');
            $currentMsg = [
                'type' => 'error',

                'title' => 'CONDUCTOR Y VEHICULO ASIGANDOS!',
                'text' => 'Se han asigando correctamente al pedido.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('seeDetailOrder',[$id_order]);


        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());

            $previousMsg = $this->session->getFlashdata('msg2');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION!',
                'text' => 'Error al modificar Conductor y Vehiculo.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }
            return redirect()->route('seeDetailOrder',[$id_order]);
      }

    }
        //busca por id  pallet 02
        public function searchforCustomerOrders($id_customer = null)
        {

            $orders = $this->ordersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
            $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regPerPage);

            return $this->twig->render('Front/Orders/list.html.twig', ['orders' => $orders, 'customers_all' => $customers_all, 'pager' => $this->ordersModel->pager->links()]);

        }

          //busca por id
          public function searchforStateOrdersPen()
          {
             $state = "Pendiente";

              $orders = $this->ordersModel->where('state', $state)->paginate(config('Configuration')->regOrdersPage);
              $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);

              return $this->twig->render('Front/Orders/list.html.twig', ['orders' => $orders, 'customers_all' => $customers_all, 'pager' => $this->ordersModel->pager->links()]);

          }


            //busca por id
            public function searchforStateOrdersAsi()
            {
                $state = "Asignado";

                $orders = $this->ordersModel->where('state', $state)->paginate(config('Configuration')->regOrdersPage);
                $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);

                return $this->twig->render('Front/Orders/list.html.twig', ['orders' => $orders, 'customers_all' => $customers_all, 'pager' => $this->ordersModel->pager->links()]);

            }

              //busca por id
              public function searchforStateOrdersFac()
              {
                  $state = "Facturado";

                  $orders = $this->ordersModel->where('state', $state)->paginate(config('Configuration')->regOrdersPage);
                  $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);

                  return $this->twig->render('Front/Orders/list.html.twig', ['orders' => $orders, 'customers_all' => $customers_all, 'pager' => $this->ordersModel->pager->links()]);

              }

            //busca por id  pallet 02
            public function searchforDateOrders()
            {


            try {
                $created_at = $this->request->getPost('created_at');

                $orders = $this->ordersModel->where('created_at', $created_at)->paginate(config('Configuration')->regOrdersPage);
                $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);

                return $this->twig->render('Front/Orders/list.html.twig', ['orders' => $orders, 'customers_all' => $customers_all, 'pager' => $this->ordersModel->pager->links()]);

            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());
                return redirect()->route('listOrders')->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['Debe seleccionar una fecha ']
                ]);
            }



            }

              //busca por id  pallet 02
              public function searchforDateOrdersPlanned()
              {


              try {
                  $planned_date = $this->request->getPost('planned_date');

                  $orders = $this->ordersModel->where('planned_date', $planned_date)->paginate(config('Configuration')->regOrdersPage);
                  $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);



                  return $this->twig->render('Front/Orders/list.html.twig', ['orders' => $orders, 'customers_all' => $customers_all, 'pager' => $this->ordersModel->pager->links()]);

              } catch (\Throwable $th) {
                  $this->log->setLine('Error', $th->getMessage());
                  return redirect()->route('listOrders')->with('msg', [
                      'type' => 'alert-danger',
                      'body' => ['Debe seleccionar una fecha ']
                  ]);
              }


              }






}
