<?php

namespace App\Controllers\Front;

use App\Controllers\Auth\Login;
use App\Controllers\BaseController;

use App\Entities\Orders;
use App\Models\OrdersModel;
use App\Models\AlbaranesModel;
use App\Entities\Albaranes;

use App\Entities\PersonContact;
use App\Models\PersonContactModel;

use App\Entities\Customers;
use App\Models\CustomersModel;

use App\Entities\DDrivers;
use App\Models\Drivers_Model;

use App\Entities\Services_;
use App\Models\Services_Model;

use App\Entities\Containers;
use App\Models\ContainersModel;

use App\Entities\Vehicles;
use App\Models\VehiclesModel;

use App\Entities\Warehouses;
use App\Models\WarehousesModel;

use App\Entities\PaymentMethod;
use App\Models\PaymentMethodModel;

use App\Entities\ActualState;
use App\Models\ActualStateModel;

use App\Entities\Supplements;
use App\Models\SupplementsModel;

use App\Entities\Retainer;
use App\Models\RetainerModel;





class Home extends BaseController
{
    protected $ordersModel;
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
    protected $pager;

    public function __construct()
    {
        $this->ordersModel = model('OrdersModel');

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
        $this->ordersModel = model('OrdersModel');
    }

    public function index()
    {

        $customers = null;

        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();
        $drivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();
        $services = $this->servicesModel->orderBy('id_service', 'DESC')->findAll();
        $containers = $this->containerModel->orderBy('id_container', 'DESC')->findAll();
        $orders = $this->ordersModel->orderBy('id_order', 'DESC')->paginate(config('Configuration')->regPerPage);

        $orders = $this->ordersModel->orderBy('id_order', 'DESC')->paginate(config('Configuration')->regPerPage);


        return $this->twig->render('Front/Orders/list.html.twig', [
            'orders' => $orders,
            'services'=>$services,
            'customers'=>$customers,
            'containers'=>$containers,
            'drivers_all' => $drivers_all,
             'customers_all' => $customers_all,
            'pager' => $this->ordersModel->pager->links()
        ]);
    }

    public function notPermission()
    {
        return $this->twig->render('Front/notPermission.html.twig', ['name' => session()->name]);
    }

    public function prueba1()
    {

        return $this->twig->render('Front/index.html.twig', []);
    }

    public function prueba2()
    {
        return $this->twig->render('Front/index.html.twig', []);
    }
}
