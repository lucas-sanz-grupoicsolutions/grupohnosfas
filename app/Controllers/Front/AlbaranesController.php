<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;

use App\Libraries\Log;
use CodeIgniter\I18n\Time;
use App\Entities\Albaranes;
use App\Entities\ActualState;
use App\Entities\Supplements;
use App\Entities\Orders;


use stdClass;


class AlbaranesController extends BaseController
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

    protected $arrayNombre = [];

    public function __construct()
    {
        $this->session = \Config\Services::session();

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
        $this->ordersModel = model('OrdersModel');
    }

    public function index($id_order = null)
    {

        $withoutCustomer = false;
        $form_add = 2;

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

        $albaranes = $this->albaranesModel->orderBy('id_albaran', 'DESC')->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Albaranes/create.html.twig', ['lugar' => 'index', 'services_' => $services_, 'withoutCustomer' => $withoutCustomer, 'form_add' => $form_add, 'albaranes' => $albaranes, 'supplements' => $supplements, 'rates' => $rates, 'containers' => $containers,  'vehicles' => $vehicles, 'worklocations' => $worklocations, 'customers' => $customers, 'services' => $services, 'drivers' => $drivers, 'payment_method' => $payment_method, 'pager' => $this->albaranesModel->pager->links()]);
    }


    public function addAlb($id_order = null)
    {
        $withoutCustomer = false;

        $id_customer = null;
        $id_work_location = null;
        $id_container = null;
        $id_service = null;
        $id_driver = null;
        $id_vehicle = null;
        $price_con = null;

        $customers_iva = null;
        $customers_names = null;

        $residue = null;

        $orders = $this->ordersModel->where('id_order', $id_order)->paginate(config('Configuration')->regPerPage);

        foreach ($orders as $ids) {

            $id_customer = $ids->id_customer;
            $id_work_location = $ids->id_work_location;
            $id_container = $ids->id_container;
            $id_service = $ids->id_service;
            $id_driver = $ids->id_driver;
            $id_vehicle = $ids->id_vehicle;
        }


        $container = $this->containerModel->where('id_container', $id_container)->paginate(config('Configuration')->regPerPage);
        foreach ($container as $item2) {
            $price_con = $item2->price;
            $residue = $item2->residue;
        }


        $services_ = $this->servicesModel->where('id_service', 'id_service')->paginate(config('Configuration')->regPerPage);
        $customers_selected = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        $worklocations_selected = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);

        $supplements = $this->supplementsModel->orderBy('id_supplements', 'ASC')->findAll();
        $rates = $this->ratesModel->orderBy('id_rates', 'DESC')->findAll();
        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->findAll();
        $vehicles = $this->vehiclesModel->orderBy('id_vehicle', 'DESC')->findAll();
        $drivers = $this->driversModel->orderBy('id_driver', 'DESC')->paginate(config('Configuration')->regPerPage);
        $albaranes = $this->albaranesModel->orderBy('id_albaran', 'DESC')->paginate(config('Configuration')->regPerPage);


        foreach ($customers_selected as $item2_customers_selected) {
            $customers_iva = $item2_customers_selected->iva;
            $customers_names = $item2_customers_selected->names;
        }


        return $this->twig->render('Front/Albaranes/create.html.twig', [


            'residue' => $residue,
            'id_order' => $id_order,
            'id_driver' => $id_driver,
            'id_vehicle' => $id_vehicle,
            'id_customer' => $id_customer,
            'id_work_location' => $id_work_location,
            'id_order' => $id_order,
            'id_container' => $id_container,
            'id_service' => $id_service,

            'price_con' => $price_con,

            'customers_iva' => $customers_iva,
            'customers_names' => $customers_names,

            'orders' => $orders,
            'services' => $services_,
            'withoutCustomer' => $withoutCustomer,
            'albaranes' => $albaranes,
            'supplements' => $supplements,
            'rates' => $rates,
            'worklocations_selected' => $worklocations_selected,
            'customers_selected' => $customers_selected,
            'vehicles' => $vehicles,  'drivers' => $drivers, 'payment_method' => $payment_method, 'pager' => $this->albaranesModel->pager->links()
        ]);
    }




    public function create($id_order)
    {

        if (!$this->validate(validateCreateAlbaran())) {

            $this->session->setFlashdata('msg', [
                'type' => 'error',
                'title' => 'Atencion!',
                'text' => 'Revise los campos.'
            ]);


            return redirect()->route('addAlb',[$id_order])
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $_SESSION['idUser'];
        $idUser = session()->get('idUser');
        $name_user = session()->get('name');


        $price = null;
        $id_supplements_obj = null;
        $id_service_obj = null;
        $array = [];
        $array_service = [];
        $services_price_final = null;
        $id_work_locations_selected = null;

        $sum_price_supplements_select = 0;

        $myJSON = null;
        $myJSON2 = null;

        $name_customer = null;

        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        //Suplementos
        $selected_supplements = [];
        $array_supplements = [];

        $total_sum_supplementos = 0;

        $price_dto = 0;
        $price_total = 0;
        $price_total_dtos = 0;
        $sum_total_dtos = 0;

        $anticipos = 0;
        $cargos = 0;
        $saldos = 0;
        $bank_count = 0;
        $price_total_all_dtos_euros = 0;
        $price_total_all_sin_dtos_base = 0;
        $amount_tax_base_discount = 0;
        $price_discount = 0;

        $total_sum_pvp_edit = 0;
        $total_sum_dto = 0;

        $albaran_suppl_sum_pvp_edit = 0;
        $albaran_suppl_sum_dto = 0;
        $albaran_suppl_sum_price_dto = 0;
        $albaran_suppl_sum_price_total = 0;

        $price_total_supp = 0;
        $price_total_all_suppl = 0;
        $tax_base_original = 0;

        $customer_name = null;
        $customer_mail = null;
        $customer_address = null;
        $customer_location = null;
        $customer_province = null;
        $customer_zip_code = null;
        $customer_dni = null;
        $customer_phone = null;
        $customer_bic = null;
        $customer_iva = null;
        $customer_iban = null;
        $customer_bank = null;
        $customer_office_bank = null;
        $customer_digital_control = null;
        $customer_bank_count = null;

        $payment_method = null;

        $container_residue = null;
        $container_m3 = null;
        $container_price = null;

        $work_location_address = null;
        $work_location_location = null;
        $work_location_province = null;
        $work_location_zip_code = null;

        $driver_name = null;
        $driver_phone = null;

        $rates_name = null;
        $id_rates =  null;

        $service_name = null;
        $service_code = null;

        $vehicle_name = null;
        $vehicle_make = null;
        $vehicle_model = null;
        $vehicle_car_registration = null;


        $db = db_connect();
        $albaranes = new Albaranes($this->request->getPost());


        $id_order = $this->request->getPost('id_order');
        $id_rates = $this->request->getPost('id_rates');
        $id_customer = $this->request->getPost('id_customer');
        $id_service = $this->request->getPost('id_service');
        $id_driver = $this->request->getPost('id_driver');
        $id_vehicle = $this->request->getPost('id_vehicle');
        $id_container = $this->request->getPost('id_container');
        $id_work_location = $this->request->getPost('id_work_location');

        $discount = $this->request->getPost('discount');

        $supplements_id = $this->request->getPost('supplements_id');
        $supplements_name = $this->request->getPost('supplements_name');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $supplement_dto = $this->request->getPost('supplement_dto');

        $retainer_amount = $this->request->getPost('retainer_amount');
        $id_payment_method = $this->request->getPost('id_payment_method');

        $supplements_obj_array_id = 0;
        //Importe del Anticipo


        //Importe del Base imponible
        $iva = $this->request->getPost('iva');

        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);


        foreach ($customers as $rows_customers) {

            $customer_name = $rows_customers->names;
            $customer_mail = $rows_customers->mail;
            $customer_dni = $rows_customers->dni;
            $customer_phone = $rows_customers->phone;
            $customer_address = $rows_customers->address;
            $customer_location = $rows_customers->location;
            $customer_province = $rows_customers->province;
            $customer_zip_code = $rows_customers->zip_code;

            $customer_bic = $rows_customers->bic;
            $customer_iban = $rows_customers->iban;
            $customer_bank = $rows_customers->bank;
            $customer_office_bank = $rows_customers->office_bank;
            $customer_digital_control = $rows_customers->digital_control;
            $customer_bank_count = $rows_customers->bank_count;
        }



        //   $rates = $this->ratesModel->orderBy('id_rates', 'DESC')->paginate(config('Configuration')->regPerPage);


        $payment_method = $this->paymentMethodModel->where('id_payment_method', $id_payment_method)->paginate(config('Configuration')->regPerPage);
        foreach ($payment_method as $rows_payment_method) {
            $payment_method = $rows_payment_method->name;
        }

        $rates = $this->ratesModel->where('id_rates', $id_rates)->paginate(config('Configuration')->regPerPage);
        foreach ($rates as $rows_rates) {
            $rates_name = $rows_rates->name;
            $id_rates = $rows_rates->id_rates;
        }


        $vehicles = $this->vehiclesModel->where('id_vehicle', $id_vehicle)->paginate(config('Configuration')->regPerPage);
        foreach ($vehicles as $rows_vehicles) {

            $vehicle_name = $rows_vehicles->name;
            $vehicle_make = $rows_vehicles->make;
            $vehicle_model = $rows_vehicles->model;
            $vehicle_car_registration = $rows_vehicles->car_registration;
        }


        $drivers = $this->driversModel->where('id_driver', $id_driver)->paginate(config('Configuration')->regPerPage);
        foreach ($drivers as $rows_drivers) {

            $driver_name = $rows_drivers->name;
            $driver_phone = $rows_drivers->phone;
        }


        $services_ = $this->servicesModel->where('id_service', $id_service)->paginate(config('Configuration')->regPerPage);
        foreach ($services_ as $rows_services_) {

            $service_name = $rows_services_->name;
            $service_code = $rows_services_->code;
        }


        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);
        foreach ($worklocations as $rows_worklocations) {

            $work_location_address = $rows_worklocations->address;
            $work_location_location = $rows_worklocations->location;
            $work_location_province = $rows_worklocations->province;
            $work_location_zip_code = $rows_worklocations->zip_code;
        }


        $container = $this->containerModel->where('id_container', $id_container)->paginate(config('Configuration')->regPerPage);
        foreach ($container as $rows_container) {
            $price = $rows_container->price;
            $container_residue = $rows_container->residue;
            $container_m3 = $rows_container->cubic_meters;
            $container_price = $rows_container->price;

        }

        $price = intval($price);



        if ($id_rates === "1") {

            if ($id_container === "2") {
                $price = 200;
            }
            if ($id_container === "7") {
                $price = 145;
            }
            if ($id_container === "10") {
                $price = 175;
            }
            if ($id_container === "5") {
                $price = 260;
            }

        }


        if ($id_rates === "2") {

            if ($id_container === "2") {
                $price = 180;
            }
            if ($id_container === "7") {
                $price = 135;
            }
            if ($id_container === "10") {
                $price = 165;
            }
            if ($id_container === "5") {
                $price = 240;
            }
        }


        $container_price = $price;
        $tax_base_original = $price;

        $retainer_amount = (float) $retainer_amount;
        $tax_base_original = (float) $tax_base_original;
        $iva = (int) $iva;

        $tax_base = $tax_base_original;


        if ($discount === "") {
            $discount = 0;
        }

        if ($discount !== 0 || $discount > 0) {
            $price_discount = $tax_base * $discount / 100;
            $amount_tax_base_discount = $tax_base - $price_discount;
        }



        /**
         * Suplementos -----------------------
         */
        // Trae los albaranes seleccionados
        //Si ahi suplementos seleccionados
        if ($supplements_id) {

            for ($i = 0; $i < count($pvp_edit); $i++) {
                if ($pvp_edit[$i] === null || $pvp_edit[$i] === "0.00" || $pvp_edit[$i] === "" || $pvp_edit[$i] === "0") {
                    $pvp_edit = 0;
                    $pvp_edit[$i] = $pvp_edit;
                }
            }

            for ($i = 0; $i < count($supplement_dto); $i++) {
                if ($supplement_dto[$i] === null || $supplement_dto[$i] === "0.00" || $supplement_dto[$i] === "" || $supplement_dto[$i] === "0") {
                    $dto = 0;
                    $supplement_dto[$i] = $dto;
                }
            }

            for ($i = 0; $i < count($supplements_id); $i++) {
                $supplemen_id_obj = new Supplements();
                $supplemen_id_obj->id_supplements = $supplements_id[$i];
                $supplemen_id_obj->pvp_edit = $pvp_edit[$i];
                $supplemen_id_obj->dto = $supplement_dto[$i];

                //ejemplo 10 10 10 = 30
                $price_total_all_dtos_euros += $supplement_dto[$i];

                //ejemplo 100 100 100 = 300
                $price_total_all_sin_dtos_base += $pvp_edit[$i];

                //porcentaje de descuento de cada suplemento en euros eje 10 €
                //100 * 10% = 10E
                $price_dto = $pvp_edit[$i] * $supplement_dto[$i] / 100;

                //Sumamos el total de esos valores de dtos 10 + 10 +15 = 45
                $sum_total_dtos += $price_dto;

                //Precio total base menos dtos 270
                $price_total_width_dto = $pvp_edit[$i] - $price_dto;

                $supplemen_id_obj->price_dto = $price_dto; //45

                //Suma total del valor de los supl de base menos dtos
                // 100 - 10 = 90->$price_total_width_dto , 100 - 10 = 90 , 100 - 10 = 90 -> 90+90+90 = 270
                //270
                $price_total += $price_total_width_dto;
                array_push($array_supplements, $supplemen_id_obj);
            }


            $this->supplementsModel->editPvp($array_supplements);

            $supplements_obj_array = $this->supplementsModel->whereIn('id_supplements', $supplements_id)->findAll();


            //lee los selecionados 4
            foreach ($supplements_obj_array as $key => $id_supplements) {

                $supplemen = new Supplements();
                $supplemen->id_supplements = $id_supplements->id_supplements;

                $total_sum_pvp_edit += $pvp_edit[$key];
                $total_sum_dto += $pvp_edit[$key] * $supplement_dto[$key] / 100;

                /**
                 * Sacamos el price_total (PVP - Dto)
                 */
                $spl_pvp_edit = 0;
                $spl_dto = 0;

                $spl_pvp_edit = $pvp_edit[$key];
                $spl_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;

                $supplemen->pvp = $pvp_edit[$key];
                $supplemen->pvp_edit = $pvp_edit[$key];
                $supplemen->dto = $supplement_dto[$key];
                $supplemen->price_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;

                $price_total_supp = $spl_pvp_edit - $spl_dto; //90
                $supplemen->price_total =  $price_total_supp;

                //Se utiliza estas variable para guardar en la tabla de albaranes
                $price_total_all_suppl += $price_total_supp; //270

                $supplemen->name = $supplements_name[$id_supplements->id_supplements];

                if ($total_sum_pvp_edit === null || $total_sum_dto  === null) {
                    $total_sum_dto = 0;
                }

                $total_sum_supplementos = $total_sum_pvp_edit - $total_sum_dto;
                $supplemen->price_total_all_supp =  $total_sum_supplementos;


                $array[] = $supplemen;
                $myJSON = json_encode($array);
            }
        } else {
            $myJSON = null;
        }

        $this->supplementsModel->CleanSupplementDto($array_supplements);


        //Anticipos
        if (!isset($retainer_amount) || empty($retainer_amount) || $retainer_amount === 0  || $retainer_amount === "") {
            $retainer_amount = 0;
        }

        if (!isset($price_total) || empty($price_total) || $price_total === 0  || $price_total === "") {
            $price_total = 0;
        }

        /**
         * El valor total del precio de los suplementos seleccionados es la suma del precio total
         */
        $sum_price_supplements_select = $price_total;

        if ($amount_tax_base_discount !== 0) {
            $tax_base = $amount_tax_base_discount;
        } else {
            $amount_tax_base_discount = $tax_base;
        }



        // $price_total  --> es el precio total final con dtos de los suplememntos
        $total = $tax_base + $sum_price_supplements_select;
        $subtotal = $tax_base + $sum_price_supplements_select;



        //base imponible mas iva depende del iva 4% 10 % 21 %
        $total_con_iva = $this->getTaxBaseIva($total, $iva);

        //importe del iva se resta el total con iva menos el valor del iva
        $value_iva = $this->ImportOfIva($total_con_iva, $total);

        $albaranes->active = 1;
        $albaranes->albaran_status = "Pendiente";

        $albaranes->id_user = $idUser;
        $albaranes->name_user = $name_user;

        $albaranes->customer_name = $customer_name;
        $albaranes->customer_mail = $customer_mail;
        $albaranes->customer_address = $customer_address;
        $albaranes->customer_location = $customer_location;
        $albaranes->customer_province = $customer_province;
        $albaranes->customer_zip_code = $customer_zip_code;
        $albaranes->customer_dni = $customer_dni;
        $albaranes->customer_phone = $customer_phone;

        $albaranes->customer_bic = $customer_bic;


        $albaranes->customer_iva = $iva;
        $albaranes->customer_iban = $customer_iban;
        $albaranes->customer_bank = $customer_bank;
        $albaranes->customer_office_bank = $customer_office_bank;
        $albaranes->customer_digital_control = $customer_digital_control;
        $albaranes->customer_bank_count = $customer_bank_count;

        $albaranes->payment_method = $payment_method;
        $albaranes->id_payment_method = $id_payment_method;

        $albaranes->container_residue = $container_residue;
        $albaranes->container_m3 = $container_m3;
        $albaranes->container_price = $container_price;

        $albaranes->work_location_address = $work_location_address;
        $albaranes->work_location_location = $work_location_location;
        $albaranes->work_location_province = $work_location_province;
        $albaranes->work_location_zip_code = $work_location_zip_code;

        $albaranes->driver_name = $driver_name;
        $albaranes->driver_phone = $driver_phone;

        $albaranes->rates_name = $rates_name;

        $albaranes->service_name = $service_name;
        $albaranes->service_code = $service_code;

        $albaranes->vehicle_name = $vehicle_name;
        $albaranes->vehicle_make = $vehicle_make;
        $albaranes->vehicle_model = $vehicle_model;
        $albaranes->vehicle_car_registration = $vehicle_car_registration;

        $albaranes->rates_name = $rates_name;

        $albaranes->id_order = $id_order;
        $albaranes->id_customer = $id_customer;
        $albaranes->id_service = $id_service;
        $albaranes->id_work_location = $id_work_location;
        $albaranes->id_container = $id_container;
        $albaranes->id_rates = $id_rates;
        $albaranes->id_driver = $id_driver;
        $albaranes->id_vehicle = $id_vehicle;

        $albaranes->discount = $discount;
        $albaranes->price_discount = $price_discount;
        $albaranes->amount_tax_base_discount = $amount_tax_base_discount;
        $albaranes->retainer_amount = $retainer_amount;
        $albaranes->iva = $value_iva;
        $albaranes->subtotal = $subtotal;
        $albaranes->total = $total_con_iva;
        $albaranes->tax_base = $tax_base_original;
        //Suplementos
        $albaranes->supplements = $myJSON;
        //suma de los desceuntos en precios euros ejemplo 10 + 10 +10 € ( 10%) = 30€
        $albaranes->price_total_supp = $price_total_supp;
        //Suma de total de los suplementos 45
        $albaranes->subtotal_sum_supplements = $sum_price_supplements_select;

        try {

            $this->albaranesModel->save($albaranes);
            $id_last_albaran_insert = $db->insertID($albaranes); //Ultimo id pallet insertado


            $id_customer = (int)$id_customer;
            $id_work_location = (int)$id_work_location;
            $id_container = (int)$id_container;
            $work_location_zip_code = (int)$work_location_zip_code;


            $orders = $this->ordersModel->where('id_order', $id_order)->first();
            $id_services =  $orders->id_service;
            $idContainer = (int)$orders->id_container;
            $idWorkLocation = (int) $orders->id_work_location;

            $id_services = (int)$id_services;
            $idServices = $this->servicesModel->where('id_service', $id_services)->first();


            if($idServices->name === "CAMBIO"){


                  // Consulta para verificar si existe un registro
                $actualStateExists = $this->actualstateModel
                ->where('id_container', $idContainer)
                ->where('id_work_locations', $idWorkLocation)
                ->countAllResults() > 0;

                if ($actualStateExists) {

                    $actualState = $this->actualstateModel
                    ->select('id_actual_state')
                    ->where('id_container', $idContainer)
                    ->where('id_work_locations', $idWorkLocation)
                    ->first();


                    $db = db_connect();
                    $builder = $db->table('actual_state');
                    $builder->where('id_actual_state', $actualState->id_actual_state);
                    $builder->delete();

                } else {

                        $actualState = new ActualState();
                        $actualState->id_work_locations = $id_work_location;
                        $actualState->id_container = $id_container;
                        $actualState->id_customer = $id_customer;
                        $actualState->container_residue = $container_residue;
                        $actualState->name_service = $idServices->name;

                    // $actualState->id_albaran = $id_last_albaran_insert;
                        $actualState->work_location_address = $work_location_address;
                        $actualState->work_location_location = $work_location_location;
                        $actualState->work_location_province = $work_location_province;
                        $actualState->work_location_zip_code = $work_location_zip_code;

                        $actualState->customer_name = $customer_name;
                        $actualState->cubic_meters = $container_m3;

                        $this->actualstateModel->save($actualState);
                }


            }

            else if($idServices->name === "RETIRADA"){


                  // Consulta para verificar si existe un registro
                  $actualStateExists = $this->actualstateModel
                  ->where('id_container', $idContainer)
                  ->where('id_work_locations', $idWorkLocation)
                  ->countAllResults() > 0;

                  if ($actualStateExists) {

                      $actualState = $this->actualstateModel
                      ->select('id_actual_state')
                      ->where('id_container', $idContainer)
                      ->where('id_work_locations', $idWorkLocation)
                      ->first();


                      $db = db_connect();
                      $builder = $db->table('actual_state');
                      $builder->where('id_actual_state', $actualState->id_actual_state);
                      $builder->delete();

                  } else {

                          $actualState = new ActualState();
                          $actualState->id_work_locations = $id_work_location;
                          $actualState->id_container = $id_container;
                          $actualState->id_customer = $id_customer;
                          $actualState->container_residue = $container_residue;
                          $actualState->name_service = $idServices->name;

                      // $actualState->id_albaran = $id_last_albaran_insert;
                          $actualState->work_location_address = $work_location_address;
                          $actualState->work_location_location = $work_location_location;
                          $actualState->work_location_province = $work_location_province;
                          $actualState->work_location_zip_code = $work_location_zip_code;

                          $actualState->customer_name = $customer_name;
                          $actualState->cubic_meters = $container_m3;

                          $this->actualstateModel->save($actualState);
                  }
            }

            else{


                $actualState = new ActualState();
                $actualState->id_work_locations = $id_work_location;
                $actualState->id_container = $id_container;
                $actualState->id_customer = $id_customer;
                $actualState->container_residue = $container_residue;
                $actualState->name_service = $idServices->name;

               // $actualState->id_albaran = $id_last_albaran_insert;
                $actualState->work_location_address = $work_location_address;
                $actualState->work_location_location = $work_location_location;
                $actualState->work_location_province = $work_location_province;
                $actualState->work_location_zip_code = $work_location_zip_code;

                $actualState->customer_name = $customer_name;
                $actualState->cubic_meters = $container_m3;

                $this->actualstateModel->save($actualState);

            }






            $order = new Orders();
            $order->id_order = $id_order;
            $order->state = "Realizado";
            $this->ordersModel->save($order);




            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',

                'title' => 'ALBARAN CREADO CON EXITO!',
                'text' => 'El Albaran se ha generado correctamente.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }


            return redirect()->route('seeDetailAlbaran', [$id_last_albaran_insert]);


        } catch (\Throwable $th) {

            $this->log->setLine('Error', $th->getMessage());

            $this->session->setFlashdata('msg', [
                'type' => 'error',
                'title' => 'Atencion!',
                'text' => 'Revise los campos',
            ]);


            return redirect()->route('addAlb',[$id_order])
                ->with('errors', $this->validator->getErrors())
                ->withInput();

        }
    }




    public function editAlbaran($id_albaran = null)
    {
        $withoutCustomer = false;

        $id_order = null;
        $id_customer = null;
        $id_work_location = null;
        $id_container = null;
        $id_service = null;
        $id_driver = null;
        $id_vehicle = null;
        $price_cont = null;
        $id_vehicle_selected = null;
        $id_driver_selected = null;
        $id_rate_selected = null;
        $id_payment_method_selected = null;
        $id_payment_method = null;
        $payment_method = null;
        $no_supplements = false;
        $supplementsObjectArray = [];


        $supplements = [];
        $containers = $this->containerModel->orderBy('id_container', 'DESC')->findAll();

        $services_ = $this->servicesModel->orderBy('id_service', 'DESC')->paginate(config('Configuration')->regPerPage);
        $customers = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regPerPage);
        $worklocations = $this->workLocationModel->orderBy('id_work_locations', 'DESC')->paginate(config('Configuration')->regPerPage);
        $supplements_all = $this->supplementsModel->orderBy('id_supplements', 'ASC')->paginate(config('Configuration')->regPerPage);
        $rates = $this->ratesModel->orderBy('id_rates', 'ASC')->findAll();

        $payment_method_all = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->findAll();

        $vehicles = $this->vehiclesModel->orderBy('id_vehicle', 'ASC')->findAll();
        $drivers = $this->driversModel->orderBy('id_driver', 'ASC')->paginate(config('Configuration')->regPerPage);

        $albaranes = $this->albaranesModel->where('id_albaran', $id_albaran)->paginate(config('Configuration')->regPerPage);


        foreach ($albaranes as $item2) {

            $id_order = $item2->id_order;
            $customer_name = $item2->customer_name;
            $customer_mail = $item2->customer_mail;

            $customer_address = $item2->customer_address;
            $customer_location = $item2->customer_location;
            $customer_province = $item2->customer_province;
            $customer_zip_code = $item2->customer_zip_code;
            $customer_dni = $item2->customer_dni;
            $customer_phone = $item2->customer_phone;
            $customer_iva = $item2->customer_iva;
            $customer_iban = $item2->customer_iban;

            $customer_bank = $item2->customer_bank;
            $customer_office_bank = $item2->customer_office_bank;
            $customer_digital_control = $item2->customer_digital_control;
            $customer_bank_count = $item2->customer_bank_count;

            $payment_method = $item2->payment_method;
            $id_payment_method_selected = $item2->id_payment_method;

            $container_residue = $item2->container_residue;
            $container_m3 = $item2->container_m3;
            $container_price = $item2->container_price;


            $work_location_address = $item2->work_location_address;
            $work_location_location = $item2->work_location_location;
            $work_location_province = $item2->work_location_province;
            $work_location_zip_code = $item2->work_location_zip_code;

            $id_driver_selected = $item2->id_driver;
            $driver_name = $item2->driver_name;
            $driver_phone = $item2->driver_phone;


            $id_rate_selected = $item2->id_rates;
            $rates_name = $item2->rates_name;

            $id_service = $item2->id_service;
            $service_name = $item2->service_name;
            $service_code = $item2->service_code;

            $id_vehicle_selected = $item2->id_vehicle;
            $vehicle_name = $item2->vehicle_name;
            $vehicle_make = $item2->vehicle_make;
            $vehicle_model = $item2->vehicle_model;
            $vehicle_car_registration = $item2->vehicle_car_registration;

            $retainer_amount = $item2->retainer_amount;
            $amount = $item2->amount;
            $notas = $item2->notas;

            $albaran_status = $item2->albaran_status;
            $tax_base = $item2->tax_base;

            $iva = $item2->iva;

            $subtotal = $item2->subtotal;
            $total = $item2->total;

            $no_fee = $item2->no_fee;
            $billable = $item2->billable;
            $preprinted = $item2->preprinted;
            $supplements = $item2->supplements;

            $subtotal_sum_supplements = $item2->subtotal_sum_supplements;
            $price_total_supp = $item2->price_total_supp;
            $created_by = $item2->created_by;
            $planned_date_realization = $item2->planned_date_realization;

            $driver_assignment_date = $item2->driver_assignment_date;
            $date_performance_service = $item2->date_performance_service;
            $discount = $item2->discount;
            $price_discount = $item2->price_discount;

            $amount_tax_base_discount = $item2->amount_tax_base_discount;
            $price_discount = $item2->price_discount;
        }



        if ($supplements !== null) {

            $albaran = $this->albaranesModel->where('id_albaran', $id_albaran)->first();
            $json_supplements = json_decode($albaran->supplements);

            $formatedSupplements = [];
            foreach ($supplements_all as $x) {
                $stdClass = new stdClass();
                $stdClass->id_supplements = $x->id_supplements;
                $stdClass->name = $x->name;
                $stdClass->pvp_edit = $x->pvp_edit;
                $stdClass->price_dto = $x->price_dto;
                $stdClass->price_total = $x->price_total;
                $stdClass->active = false;

                $formatedSupplements[$x->name] = $stdClass;
            }


            foreach ($json_supplements as $key => $item) {

                $supplementsObject = new stdClass();
                $supplementsObject->id_supplements = $item->id_supplements;
                $supplementsObject->name = $item->name;
                $supplementsObject->pvp_edit = $item->pvp_edit;
                $supplementsObject->price_dto =  $item->price_dto;
                $supplementsObject->price_total = $item->price_total;
                $supplementsObject->active = true;

                $supplementsObjectArray[$item->name] = $supplementsObject;
            }

            $supplements_edit = array_merge($formatedSupplements, $supplementsObjectArray);
        } else {
            $no_supplements = true;
        }
        if ($no_supplements === true) {

            $supplements_edit = $this->supplementsModel->orderBy('id_supplements', 'ASC')->paginate(config('Configuration')->regPerPage);
        }


        return $this->twig->render('Front/Albaranes/edit.html.twig', [

            'customer_name' => $customer_name,
            'customer_mail' => $customer_mail,
            'customer_address' => $customer_address,
            'customer_location' => $customer_location,
            'customer_province' => $customer_province,
            'customer_zip_code' => $customer_zip_code,

            'customer_dni' => $customer_dni,
            'customer_phone' => $customer_phone,
            'customer_iva' => $customer_iva,
            'customer_iban' => $customer_iban,
            'customer_bank' => $customer_bank,
            'customer_office_bank' => $customer_office_bank,
            'customer_digital_control' => $customer_digital_control,
            'customer_bank_count' => $customer_bank_count,

            'payment_method' => $payment_method,
            'payment_method_all' => $payment_method_all,
            'id_payment_method_selected' => $id_payment_method_selected,


            'container_residue' => $container_residue,
            'container_m3' => $container_m3,
            'container_price' => $container_price,

            'work_location_address' => $work_location_address,
            'work_location_location' => $work_location_location,
            'work_location_province' => $work_location_province,
            'work_location_zip_code' => $work_location_zip_code,

            'id_driver_selected' => $id_driver_selected,
            'driver_name' => $driver_name,
            'driver_phone' => $driver_phone,


            'id_rate_selected' => $id_rate_selected,
            'rates_name' => $rates_name,

            'service_name' => $service_name,
            'service_code' => $service_code,


            'id_vehicle_selected' => $id_vehicle_selected,
            'vehicle_name' => $vehicle_name,
            'vehicle_make' => $vehicle_make,
            'vehicle_model' => $vehicle_model,
            'vehicle_car_registration' => $vehicle_car_registration,

            'retainer_amount' => $retainer_amount,
            'amount' => $amount,
            'notas' => $notas,

            'tax_base' => $tax_base,
            'iva' => $iva,
            'subtotal' => $subtotal,
            'total' => $total,

            'no_fee' => $no_fee,
            'billable' => $billable,
            'preprinted' => $preprinted,
            'supplements' => $supplements,

            'subtotal_sum_supplements' => $subtotal_sum_supplements,
            'price_total_supp' => $price_total_supp,
            'created_by' => $created_by,
            'planned_date_realization' => $planned_date_realization,

            'driver_assignment_date' => $driver_assignment_date,
            'date_performance_service' => $date_performance_service,
            'discount' => $discount,
            'price_discount' => $price_discount,

            'amount_tax_base_discount' => $amount_tax_base_discount,
            'price_discount' => $price_discount,


            /** Todos los valores  */
            'supplementsObjectArray' => $supplementsObjectArray,

            'id_albaran' => $id_albaran,
            'id_service' => $id_service,

            'supplements' => $supplements,
            'containers' => $containers,
            'services_' => $services_,
            'supplements_all' => $supplements_edit,
            'rates' => $rates,
            'worklocations' => $worklocations,
            'customers' => $customers,
            'vehicles' => $vehicles,
            'drivers' => $drivers,
            'payment_method_all' => $payment_method_all,
            'pager' => $this->albaranesModel->pager->links()
        ]);
    }



    public function editSaveAlbaran()
    {

        if (!$this->validate(validateEditAlbaran())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }


        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));



        //Suplementos
        $selected_supplements = [];
        $array_supplements = [];

        $total_sum_supplementos = 0;

        $price = null;
        $id_supplements_obj = null;
        $id_service_obj = null;
        $array = [];
        $array_service = [];
        $services_price_final = null;
        $id_work_locations_selected = null;

        $sum_price_supplements_select = 0;

        $myJSON = null;
        $myJSON2 = null;

        $name_customer = null;

        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        //Suplementos
        $selected_supplements = [];
        $array_supplements = [];

        $total_sum_supplementos = 0;


        $price_dto = 0;
        $price_total = 0;
        $price_total_dtos = 0;
        $sum_total_dtos = 0;

        $anticipos = 0;
        $cargos = 0;
        $saldos = 0;
        $bank_count = 0;
        $price_total_all_dtos_euros = 0;
        $price_total_all_sin_dtos_base = 0;
        $amount_tax_base_discount = 0;
        $price_discount = 0;

        $total_sum_pvp_edit = 0;
        $total_sum_dto = 0;

        $albaran_suppl_sum_pvp_edit = 0;
        $albaran_suppl_sum_dto = 0;
        $albaran_suppl_sum_price_dto = 0;
        $albaran_suppl_sum_price_total = 0;

        $price_total_supp = 0;
        $price_total_all_suppl = 0;
        $tax_base_original = 0;

        $driver_name = null;
        $driver_phone = null;

        $rates_name = null;

        $service_name = null;
        $service_code = null;

        $payment_method_name = null;

        $container_residue = null;
        $container_m3 = null;
        $container_price = null;

        $driver_name = null;
        $driver_phone = null;

        $rates_name = null;

        $service_name = null;
        $service_code = null;

        $vehicle_name = null;
        $vehicle_make = null;
        $vehicle_model = null;
        $vehicle_car_registration = null;
        $billable_alb = null;
        $id_container_alb = null;
        $id_payment_method_alb = null;
        $id_rates_alb = null;
        $id_vehicle_alb = null;
        $id_driver_alb = null;
        $id_service_alb = null;
        $discount_alb = null;
        $retainer_amount_alb = null;
        $iva_alb = null;
        $preprinted_alb = null;
        $albaran_status_alb = null;

        $db = db_connect();

        $id_albaran = $this->request->getPost('id_albaran');

        $id_container = $this->request->getPost('id_container');
        $id_service = $this->request->getPost('id_service');
        $id_rates = $this->request->getPost('id_rates');
        $id_driver = $this->request->getPost('id_driver');
        $id_vehicle = $this->request->getPost('id_vehicle');
        $notas = $this->request->getPost('notas');
        $id_payment_method = $this->request->getPost('id_payment_method');

        $supplements_id = $this->request->getPost('supplements_id');
        $supplements_name = $this->request->getPost('supplements_name');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $supplement_dto = $this->request->getPost('supplement_dto');

        $discount = $this->request->getPost('discount');
        $retainer_amount = $this->request->getPost('retainer_amount');

        $preprinted = $this->request->getPost('preprinted');

        $planned_date_realization = $this->request->getPost('planned_date_realization');
        $supplements_obj_array_id = 0;

        $iva = $this->request->getPost('iva');
        //$rates = $this->ratesModel->orderBy('id_rates', 'DESC')->paginate(config('Configuration')->regPerPage);

        $billable = $this->request->getPost('billable');


        $albaranes = $this->albaranesModel->where('id_albaran', $id_albaran)->paginate(config('Configuration')->regPerPage);
        foreach ($albaranes as $item2) {

            $billable_alb = $item2->billable;
            $id_customer = $item2->id_customer;

            $payment_method = $item2->payment_method;
            $id_payment_method_selected = $item2->id_payment_method;
            $payment_method = $item2->payment_method;


            $id_container_alb = $item2->id_container;
            $container_residue = $item2->container_residue;
            $container_m3 = $item2->container_m3;
            $container_price = $item2->container_price;

            $id_driver_selected = $item2->id_driver;
            $driver_name = $item2->driver_name;
            $driver_phone = $item2->driver_phone;

            $id_rate_selected = $item2->id_rates;
            $rates_name = $item2->rates_name;

            $id_service = $item2->id_service;
            $service_name = $item2->service_name;
            $service_code = $item2->service_code;

            $id_vehicle_selected = $item2->id_vehicle;
            $vehicle_name = $item2->vehicle_name;
            $vehicle_make = $item2->vehicle_make;
            $vehicle_model = $item2->vehicle_model;
            $vehicle_car_registration = $item2->vehicle_car_registration;


            $retainer_amount_alb = $item2->retainer_amount;
            $amount = $item2->amount;
            $notas = $item2->notas;

            $albaran_status_alb = $item2->albaran_status;
            $tax_base = $item2->tax_base;

            $iva_alb = $item2->iva;


            $subtotal = $item2->subtotal;
            $total = $item2->total;

            $no_fee = $item2->no_fee;
            $billable = $item2->billable;
            $preprinted_alb = $item2->preprinted;
            $supplements = $item2->supplements;

            $subtotal_sum_supplements = $item2->subtotal_sum_supplements;
            $price_total_supp = $item2->price_total_supp;
            $created_by = $item2->created_by;
            $planned_date_realization = $item2->planned_date_realization;

            $driver_assignment_date = $item2->driver_assignment_date;
            $date_performance_service = $item2->date_performance_service;

            $discount_alb = $item2->discount;

            $price_discount = $item2->price_discount;

            $amount_tax_base_discount = $item2->amount_tax_base_discount;
            $price_discount = $item2->price_discount;
        }


        if ($id_rates === null) {
            $id_rates = $id_rates_alb;
        } else {

            $id_rates = $this->request->getPost('id_rates');

            $rates = $this->ratesModel->where('id_rates', $id_rates)->paginate(config('Configuration')->regPerPage);
            foreach ($rates as $rows_rates) {
                $rates_name = $rows_rates->name;
            }
        }





        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

        foreach ($customers as $item2) {
            $iva = $item2->iva;
        }

        if ($billable === null) {
            $billable = $billable_alb;
        } else {
            $billable = $this->request->getPost('billable');
        }

        //Contenedores
        if ($id_container === null) {
            $id_container = $id_container_alb;
            $tax_base_original = $tax_base;
        } else {
            $id_container = $this->request->getPost('id_container');

            $container = $this->containerModel->where('id_container', $id_container)->paginate(config('Configuration')->regPerPage);
            foreach ($container as $rows_container) {
                $price = $rows_container->price;
                $container_residue = $rows_container->residue;
                $container_m3 = $rows_container->cubic_meters;
                $container_price = $rows_container->price;
            }

            $price = intval($price);



            if ($id_rates === "1") {
                if ($id_container === "2") {
                    $price = 180;
                }
            }

            if ($id_rates === "2") {

                if ($id_container === "5") {
                    $price = 240;
                }
            }

            if ($id_rates === "3") {
                if ($id_container === "7") {
                    $price = 135;
                }
            }

            if ($id_rates === "4") {
                if ($id_container === "10") {
                    $price = 165;
                }
            }

            $container_price = $price;



            $tax_base_original = $price;
        }


        if ($id_payment_method === null) {
            $id_payment_method = $id_payment_method_alb;
        } else {

            $id_payment_method = $this->request->getPost('id_payment_method');

            $payment_method = $this->paymentMethodModel->where('id_payment_method', $id_payment_method)->paginate(config('Configuration')->regPerPage);
            foreach ($payment_method as $rows_payment_method) {
                $payment_method = $rows_payment_method->name;
            }
        }




        if ($id_vehicle === null) {
            $id_vehicle = $id_vehicle_alb;
        } else {

            $id_vehicle = $this->request->getPost('id_vehicle');

            $vehicles = $this->vehiclesModel->where('id_vehicle', $id_vehicle)->paginate(config('Configuration')->regPerPage);
            foreach ($vehicles as $rows_vehicles) {

                $vehicle_name = $rows_vehicles->name;
                $vehicle_make = $rows_vehicles->make;
                $vehicle_model = $rows_vehicles->model;
                $vehicle_car_registration = $rows_vehicles->car_registration;
            }
        }


        if ($id_driver === null) {
            $id_driver = $id_driver_alb;
        } else {

            $id_driver = $this->request->getPost('id_driver');
            $drivers = $this->driversModel->where('id_driver', $id_driver)->paginate(config('Configuration')->regPerPage);
            foreach ($drivers as $rows_drivers) {

                $driver_name = $rows_drivers->name;
                $driver_phone = $rows_drivers->phone;
            }
        }

        if ($id_service === null) {
            $id_service = $id_service_alb;
        } else {

            $id_service = $this->request->getPost('id_service');

            $services_ = $this->servicesModel->where('id_service', $id_service)->paginate(config('Configuration')->regPerPage);
            foreach ($services_ as $rows_services_) {

                $service_name = $rows_services_->name;
                $service_code = $rows_services_->code;
            }
        }



        $retainer_amount = (float) $retainer_amount;
        $tax_base_original = (float) $tax_base_original;
        $iva = (int) $iva;

        $tax_base = $tax_base_original;


        if ($discount === null) {
            $discount = $discount_alb;
        } else {

            $discount = $this->request->getPost('discount');
        }


        /**
         * preprinted
         */



        if ($preprinted === null || $preprinted === "") {

            $preprinted = $preprinted_alb;
            $albaran_status = $albaran_status_alb;
        } else {
            $preprinted = $this->request->getPost('preprinted');
            $albaran_status = "Realizado";
        }



        /**
         * Anticipos
         */
        if ($retainer_amount === null) {
            $retainer_amount = $retainer_amount_alb;
        } else {
            $retainer_amount = $this->request->getPost('retainer_amount');
        }

        /**
         * Suplementos -----------------------
         */
        // Trae los albaranes seleccionados
        //Si ahi suplementos seleccionados
        if ($supplements_id) {

            for ($i = 0; $i < count($pvp_edit); $i++) {
                if ($pvp_edit[$i] === null || $pvp_edit[$i] === "0.00" || $pvp_edit[$i] === "" || $pvp_edit[$i] === "0") {
                    $pvp_edit = 0;
                    $pvp_edit[$i] = $pvp_edit;
                }
            }

            for ($i = 0; $i < count($supplement_dto); $i++) {
                if ($supplement_dto[$i] === null || $supplement_dto[$i] === "0.00" || $supplement_dto[$i] === "" || $supplement_dto[$i] === "0") {
                    $dto = 0;
                    $supplement_dto[$i] = $dto;
                }
            }

            for ($i = 0; $i < count($supplements_id); $i++) {
                $supplemen_id_obj = new Supplements();
                $supplemen_id_obj->id_supplements = $supplements_id[$i];
                $supplemen_id_obj->pvp_edit = $pvp_edit[$i];
                $supplemen_id_obj->dto = $supplement_dto[$i];

                //ejemplo 10 10 10 = 30
                $price_total_all_dtos_euros += $supplement_dto[$i];

                //ejemplo 100 100 100 = 300
                $price_total_all_sin_dtos_base += $pvp_edit[$i];

                //porcentaje de descuento de cada suplemento en euros eje 10 €
                //100 * 10% = 10E
                $price_dto = $pvp_edit[$i] * $supplement_dto[$i] / 100;

                //Sumamos el total de esos valores de dtos 10 + 10 +15 = 45
                $sum_total_dtos += $price_dto;

                //Precio total base menos dtos 270
                $price_total_width_dto = $pvp_edit[$i] - $price_dto;

                $supplemen_id_obj->price_dto = $price_dto; //45

                //Suma total del valor de los supl de base menos dtos
                // 100 - 10 = 90->$price_total_width_dto , 100 - 10 = 90 , 100 - 10 = 90 -> 90+90+90 = 270
                //270
                $price_total += $price_total_width_dto;


                array_push($array_supplements, $supplemen_id_obj);
            }


            $this->supplementsModel->editPvp($array_supplements);

            $supplements_obj_array = $this->supplementsModel->whereIn('id_supplements', $supplements_id)->findAll();


            //lee los selecionados 4
            foreach ($supplements_obj_array as $key => $id_supplements) {

                $supplemen = new Supplements();
                $supplemen->id_supplements = $id_supplements->id_supplements;

                $total_sum_pvp_edit += $pvp_edit[$key];
                $total_sum_dto += $pvp_edit[$key] * $supplement_dto[$key] / 100;

                /**
                 * Sacamos el price_total (PVP - Dto)
                 */
                $spl_pvp_edit = 0;
                $spl_dto = 0;

                $spl_pvp_edit = $pvp_edit[$key];
                $spl_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;

                $supplemen->pvp = $pvp_edit[$key];
                $supplemen->pvp_edit = $pvp_edit[$key];
                $supplemen->dto = $supplement_dto[$key];
                $supplemen->price_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;

                $price_total_supp = $spl_pvp_edit - $spl_dto; //90
                $supplemen->price_total =  $price_total_supp;

                //Se utiliza estas variable para guardar en la tabla de albaranes
                $price_total_all_suppl += $price_total_supp; //270

                $supplemen->name = $supplements_name[$id_supplements->id_supplements];

                if ($total_sum_pvp_edit === null || $total_sum_dto  === null) {
                    $total_sum_dto = 0;
                }

                $total_sum_supplementos = $total_sum_pvp_edit - $total_sum_dto;
                $supplemen->price_total_all_supp =  $total_sum_supplementos;


                $array[] = $supplemen;
                $myJSON = json_encode($array);
            }


            if (!isset($price_total) || empty($price_total) || $price_total === 0  || $price_total === "") {
                $price_total = 0;
            }

            /**
             * El valor total del precio de los suplementos seleccionados es la suma del precio total
             */
            $sum_price_supplements_select = $price_total;

            if ($amount_tax_base_discount !== 0) {
                $tax_base = $amount_tax_base_discount;
            } else {
                $amount_tax_base_discount = $tax_base;
            }



            // $price_total  --> es el precio total final con dtos de los suplememntos
            $total = $tax_base + $sum_price_supplements_select;
            $subtotal = $tax_base + $sum_price_supplements_select;

            //base imponible mas iva depende del iva 4% 10 % 21 %
            $total_con_iva = $this->getTaxBaseIva($total, $iva);

            //importe del iva se resta el total con iva menos el valor del iva
            $value_iva = $this->ImportOfIva($total_con_iva, $total);

            $iva = $value_iva;
            $total = $total_con_iva;
        } else {

            $albaranes = $this->albaranesModel->where('id_albaran', $id_albaran)->paginate(config('Configuration')->regPerPage);
            foreach ($albaranes as $item2) {

                $price = $item2->container_price;

                $retainer_amount = $item2->retainer_amount;
                $amount = $item2->amount;

                $albaran_status = $item2->albaran_status;
                $tax_base = $item2->tax_base;

                $iva = $item2->iva;

                $subtotal = $item2->subtotal;
                $total = $item2->total;

                $no_fee = $item2->no_fee;

                $subtotal_sum_supplements = $item2->subtotal_sum_supplements;
                $price_total_supp = $item2->price_total_supp;

                $discount = $item2->discount;
                $price_discount = $item2->price_discount;

                $amount_tax_base_discount = $item2->amount_tax_base_discount;
                $price_discount = $item2->price_discount;
            }
        }

        //dd($preprinted);



        try {

            $data = [

                'id_container' => $id_container,
                'id_service'  => $id_service,
                'id_driver' => $id_driver,
                'id_vehicle' => $id_vehicle,
                'id_payment_method' => $id_payment_method,
                'id_rates' => $id_rates,
                'id_albaran' => $id_albaran,

                'albaran_status' => $albaran_status,


                'payment_method' => $payment_method,
                'container_residue' => $container_residue,
                'container_m3' => $container_m3,
                'container_price' => $container_price,

                'billable' => $billable,

                'driver_name' => $driver_name,
                'driver_phone' => $driver_phone,

                'rates_name' => $rates_name,

                'service_name' => $service_name,
                'service_code' => $service_code,

                'vehicle_name' => $vehicle_name,
                'vehicle_make' => $vehicle_make,
                'vehicle_model' => $vehicle_model,
                'vehicle_car_registration' => $vehicle_car_registration,

                'retainer_amount' => $retainer_amount,
                'amount' => $amount,
                'notas' => $notas,

                'tax_base' => $tax_base,
                'iva' => $iva,
                'subtotal' => $subtotal,
                'total' => $total,

                'no_fee' => $no_fee,
                'billable' => $billable,
                'preprinted' => $preprinted,
                'supplements' => $myJSON,

                'subtotal_sum_supplements' => $subtotal_sum_supplements,
                'price_total_supp' => $price_total_supp,
                'created_by' => $created_by,
                'planned_date_realization' => $planned_date_realization,

                'driver_assignment_date' => $driver_assignment_date,
                'date_performance_service' => $date_performance_service,
                'discount' => $discount,
                'price_discount' => $price_discount,

                'amount_tax_base_discount' => $amount_tax_base_discount,
                'price_discount' => $price_discount,

                'planned_date_realization' => $planned_date_realization,
                'notas' =>  $notas,
                'updated_at' => $date->format('Y-m-d'),

            ];
            $builder = $db->table('albaranes');
            $builder->getWhere(['id_albaran' => $id_albaran]);
            $builder->set(

                'id_container',
                'id_service',
                'id_driver',
                'id_vehicle',
                'id_payment_method',
                'id_rates',
                'id_albaran',
                'albaran_status',


                'payment_method',
                'container_residue',
                'container_m3',
                'container_price',

                'billable',

                'driver_phone',

                'service_code',

                'vehicle_name',
                'vehicle_make',
                'vehicle_model',
                'vehicle_car_registration',

                'retainer_amount',
                'amount',
                'tax_base',
                'iva',
                'subtotal',
                'total',

                'no_fee',
                'preprinted',
                'supplements',

                'subtotal_sum_supplements',
                'price_total_supp',

                'created_by',

                'planned_date_realization',
                'driver_assignment_date',
                'date_performance_service',
                'discount',
                'price_discount',
                'amount_tax_base_discount',
                'price_discount',
                'planned_date_realization',

                'notas',
                'updated_at'

            );


            $builder->where('id_albaran', $id_albaran);
            $builder->update($data);


            // $actualState = new ActualState();
            // $actualState->id_work_locations = $id_work_location;
            // $actualState->id_container = $id_container;
            // $actualState->id_customer = $id_customer;
            // $actualState->container_residue = $container_residue;

            // $actualState->work_location_address = $work_location_address;
            // $actualState->work_location_location = $work_location_location;
            // $actualState->work_location_province = $work_location_province;
            // $actualState->work_location_zip_code = $work_location_zip_code;

            // $actualState->customer_name = $customer_name;
            // $this->actualstateModel->save($actualState);

            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',
                'title' => 'ALBARAN MODIFICADO CON EXITO!',
                'text' => 'El albaran se ha modificado correctamente.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('editAlbaran', [$id_albaran]);


        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');

            $currentMsg = [
                'type' => 'error',
                'title' => 'ATENCION!',
                'text' => 'Error al modificar el albaran.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('editAlbaran', [$id_albaran]);
        }
    }






    /**
     * Funcion que realiza multiplicacion para obtener el valor del iva ej. 20€
     */
    public function ImportOfIva($total_con_iva, $total)
    {

        $valueIva = 0;

        if (!isset($total_con_iva) || empty($total_con_iva) || $total_con_iva === 0 || !isset($total) || empty($total) || $total === 0) {
            $valueIva;
        } else {

            $valueIva = $total_con_iva - $total;
        }

        return $valueIva;
    }

    /**
     * Funcion que realiza multiplicacion para obtener el iva)
     */
    public function getTaxBaseIva($tax_base, $iva)
    {

        $result = 0;
        $iva_select = 0;

        switch ($iva) {

            case 4:
                $iva_select = 1.04;
                break;
            case 10:
                $iva_select = 1.10;
                break;
            case 21:
                $iva_select = 1.21;
                break;
        }

        $result = $tax_base * $iva_select;


        return $result;
    }


    /**
     * Funcion que realiza multiplicacion para obtener el iva)
     */
    public function TaxBaseIva($subtotal, $iva)
    {

        $result = 0;
        $iva_select = 0;

        switch ($iva) {

            case 4:
                $iva_select = 1.04;
                break;
            case 10:
                $iva_select = 1.10;
                break;
            case 21:
                $iva_select = 1.21;
                break;
        }

        if (!isset($subtotal) || empty($subtotal) || $subtotal === 0) {
            $result;
        } else {
            $result = $subtotal * $iva_select;
        }
        return $result;
    }

    /**
     * Funcion que realiza multiplicacion para obtener el  total si hubiera un anticipo se descontaria del subtotal
     */
    public function getWithRetainerAmount($subtotal, $retainer_amount)
    {

        $result_total = null;
        if (!isset($subtotal) || empty($subtotal) || !isset($retainer_amount) || empty($retainer_amount)) {
            $result_total = 0;
        }
        $result_total = $subtotal - $retainer_amount;

        return $result_total;
    }


    /**
     * Funcion suma suplementos si los hubiera
     */
    public function sumTotalSupplement($tax_base, $supplements)
    {

        $result_total = 0;

        if (!isset($tax_base) || empty($tax_base) || !isset($tax_base) || empty($tax_base)) {
            $result_total = 0;
        }
        $result_total = $tax_base + $supplements;

        return $result_total;
    }

    /**
     * Funcion que realiza multiplicacion para obtener el  total)
     */
    public function total($subtotal, $retainer_amount)
    {

        $result_total = null;

        if (!isset($subtotal) || empty($subtotal) || !isset($retainer_amount) || empty($retainer_amount)) {
            //La variable esta vacia o null
            echo "subtotal esta vacia";
        }

        $result_total = $subtotal - $retainer_amount;

        return $result_total;
    }




    public function result()
    {
        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();
        $drivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        $id_customer = $this->request->getGet('customer_id');
        $id_work_locations = $this->request->getGet('id_work_locations');
        $driver = $this->request->getGet('driver');
        $created_at = $this->request->getGet('created_at');
        $albaran_status = $this->request->getGet('albaran_status');





        //  dd($id_work_locations);

        if (count($_GET) > 0) {

            $albaranes = $this->albaranesModel->select('*');


            if (!empty($id_customer)) {
                $albaranes->where('id_customer', $id_customer);
            }

            if (!empty($id_work_locations)) {
                $albaranes->where('id_work_location', $id_work_locations);
            }

            if (!empty($driver)) {
                $albaranes->where('id_driver', $driver);
            }

            if (!empty($created_at)) {
                $albaranes->where('created_at', $created_at);
            }

            if (!empty($albaran_status)) {
                $albaranes->where('albaran_status', $albaran_status);
            }

            $albaranes = $this->albaranesModel->paginate(config('Configuration')->regListAlbaranesPage);
            // dd($albaranes );

        } else {
            $albaranes = $this->albaranesModel->orderBy('id_albaran', 'DESC')->paginate(config('Configuration')->regListAlbaranesPage);
        }



        return $this->twig->render('Front/Albaranes/list.html.twig', [
            'drivers_all' => $drivers_all,
            'customers_all' => $customers_all,
            'albaranes' => $albaranes,

            'pager' => $this->albaranesModel->pager->links()

        ]);
    }


    //Ver detalle del
    public function seeDetailAlbaran($id = null)
    {


        $pvp = null;
        $price = null;
        $concept = null;
        $id_s = null;
        $id_sp = null;
        $supplements = [];

        $array_id_supplements = [];
        $supplements_pvp = [];
        $services_pvp = [];

        $supplements_pvp_item_sum = null;
        $sum_price_supplements_select = 0;

        $id_customer = null;
        $id_vehicle = null;
        $id_driver = null;
        $id_service = null;
        $id_customer = null;
        $payment_method = null;
        $id_rates = null;
        $id_work_location = null;
        $id_payment_method = null;
        $billable = null;

        $service_name = null;
        $service_code = null;
        $name_payment_method = null;
        $supplements_array = null;

        $json_supplements = null;

        $albaran = $this->albaranesModel->where('id_albaran', $id)->first();

        $supplements_obj = $this->supplementsModel->orderBy('id_supplements', 'ASC')->find();

        if ($albaran->supplements !== null) {
            $json_supplements = json_decode($albaran->supplements);
            foreach ($json_supplements as $item) {
                $id_sp = $item->id_supplements;

                $supplements_array[] = $this->supplementsModel->find($id_sp);
            }

            //Si coincide el id selrvicio con los selecionados suma los valores de los campos pvp
            foreach ($supplements_pvp as $key => $supplements_id_) { //id seleccionados
                foreach ($supplements_obj as $key2 => $supplements_pvp_2) {
                    if ($key === $key2) {
                        $sum_price_supplements_select += $supplements_id_;
                    }
                }
            }
        } else {
            $json_supplements = null;
        }


        $albaran = $this->albaranesModel->where('id_albaran', $id)->paginate(config('Configuration')->regPerPage);
        foreach ($albaran as $item2) {

            $id_customer = $item2->id_customer;
            $id_container = $item2->id_container;
            $id_vehicle = $item2->id_vehicle;
            $id_driver = $item2->id_driver;
            $id_service = $item2->id_service;
            $id_customer = $item2->id_customer;
            $id_payment_method = $item2->id_payment_method;
            $id_rates = $item2->id_rates;
            $id_work_location = $item2->id_work_location;
            $billable = $item2->billable;


            $id_order = $item2->id_order;
            $customer_name = $item2->customer_name;
            $customer_mail = $item2->customer_mail;

            $customer_address = $item2->customer_address;
            $customer_location = $item2->customer_location;
            $customer_province = $item2->customer_province;
            $customer_zip_code = $item2->customer_zip_code;
            $customer_dni = $item2->customer_dni;
            $customer_phone = $item2->customer_phone;
            $customer_iva = $item2->customer_iva;
            $customer_iban = $item2->customer_iban;

            $customer_bank = $item2->customer_bank;
            $customer_office_bank = $item2->customer_office_bank;
            $customer_digital_control = $item2->customer_digital_control;
            $customer_bank_count = $item2->customer_bank_count;

            $payment_method = $item2->payment_method;
            $id_payment_method_selected = $item2->id_payment_method;

            $container_residue = $item2->container_residue;
            $container_m3 = $item2->container_m3;
            $container_price = $item2->container_price;


            $work_location_address = $item2->work_location_address;
            $work_location_location = $item2->work_location_location;
            $work_location_province = $item2->work_location_province;
            $work_location_zip_code = $item2->work_location_zip_code;

            $id_driver_selected = $item2->id_driver;
            $driver_name = $item2->driver_name;
            $driver_phone = $item2->driver_phone;


            $id_rate_selected = $item2->id_rates;
            $rates_name = $item2->rates_name;

            $id_service = $item2->id_service;
            $service_name = $item2->service_name;
            $service_code = $item2->service_code;

            $id_vehicle_selected = $item2->id_vehicle;
            $vehicle_name = $item2->vehicle_name;
            $vehicle_make = $item2->vehicle_make;
            $vehicle_model = $item2->vehicle_model;
            $vehicle_car_registration = $item2->vehicle_car_registration;

            $retainer_amount = $item2->retainer_amount;
            $amount = $item2->amount;
            $notas = $item2->notas;

            $albaran_status = $item2->albaran_status;
            $tax_base = $item2->tax_base;

            $iva = $item2->iva;

            $subtotal = $item2->subtotal;
            $total = $item2->total;

            $no_fee = $item2->no_fee;
            $billable = $item2->billable;
            $preprinted = $item2->preprinted;
            $supplements = $item2->supplements;



            $subtotal_sum_supplements = $item2->subtotal_sum_supplements;
            $price_total_supp = $item2->price_total_supp;
            $created_by = $item2->created_by;
            $planned_date_realization = $item2->planned_date_realization;

            $driver_assignment_date = $item2->driver_assignment_date;
            $date_performance_service = $item2->date_performance_service;
            $discount = $item2->discount;
            $price_discount = $item2->price_discount;

            $amount_tax_base_discount = $item2->amount_tax_base_discount;
            $price_discount = $item2->price_discount;
        }



        $services = $this->servicesModel->where('id_service', $id_service)->paginate(config('Configuration')->regPerPage);

        foreach ($services as $item) {

            $service_name = $item->name;
            $service_code = $item->code;
        }


        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        $rates = $this->ratesModel->where('id_rates', $id_rates)->paginate(config('Configuration')->regPerPage);
        $vehicles = $this->vehiclesModel->where('id_vehicle', $id_vehicle)->paginate(config('Configuration')->regPerPage);
        $drivers = $this->driversModel->where('id_driver', $id_driver)->paginate(config('Configuration')->regPerPage);

        $payment_method_selected = $this->paymentMethodModel->where('id_payment_method', $id_payment_method)->paginate(config('Configuration')->regPerPage);
        foreach ($payment_method_selected as $item3) {

            $name_payment_method = $item3->name;
        }

        $services_id = $this->servicesModel->where('id_service', $id_service)->paginate(config('Configuration')->regPerPage);

        $containers = $this->containerModel->where('id_container', $id_container)->paginate(config('Configuration')->regPerPage);

        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);


        return $this->twig->render('Front/Albaranes/seeDetailAlbaran.html.twig', [
            'lugar' => 'index',
            'sum_price_supplements_select' => $sum_price_supplements_select,

            'services_id' => $services_id,
            'albaran' => $albaran,
            'supplements' => $supplements,
            'rates' => $rates,
            'containers' => $containers,

            'supplements_array' => $supplements_array,

            'service_name' => $service_name,
            'service_code' => $service_code,

            'billable' => $billable,

            'vehicles' => $vehicles,
            'worklocations' => $worklocations,
            'customers' => $customers,
            'services' => $services,
            'drivers' => $drivers,
            'name_payment_method' => $name_payment_method,
            'pager' => $this->albaranesModel->pager->links()
        ]);
    }


    //busca por id
    public function searchforCustomerAlbaranes($id_customer = null)
    {
        $workLocations = $this->workLocationModel->where('id_customer', $id_customer)->findAll();
        return json_encode($workLocations);
    }



    public function getWorkLocationsByCustomerId($customerId)
    {
        $workLocations = $this->workLocationModel->where('id_customer', $customerId)->findAll();
        return json_encode($workLocations);
    }




    //busca por
    public function searchforDriverAlbaranes($id_driver = null)
    {
        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();
        $drivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        $albaranes = $this->albaranesModel->where('id_driver', $id_driver)->paginate(config('Configuration')->regClientesPage);

        return $this->twig->render('Front/Albaranes/list.html.twig', ['albaranes' => $albaranes, 'drivers_all' => $drivers_all, 'customers_all' => $customers_all, 'pager' => $this->albaranesModel->pager->links()]);
    }


    //busca por
    public function searchforStateAlbaranesPen()
    {
        $state = "Pendiente";
        $albaranes = $this->albaranesModel->where('albaran_status', $state)->paginate(config('Configuration')->regClientesPage);

        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();
        $drivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        return $this->twig->render('Front/Albaranes/list.html.twig', ['albaranes' => $albaranes, 'drivers_all' => $drivers_all, 'customers_all' => $customers_all, 'pager' => $this->albaranesModel->pager->links()]);
    }

    //busca por
    public function searchforStateAlbaranesRea()
    {
        $state = "Realizado";

        $albaranes = $this->albaranesModel->where('albaran_status', $state)->paginate(config('Configuration')->regClientesPage);

        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();
        $drivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        return $this->twig->render('Front/Albaranes/list.html.twig', ['albaranes' => $albaranes, 'drivers_all' => $drivers_all, 'customers_all' => $customers_all, 'pager' => $this->albaranesModel->pager->links()]);
    }


    //busca por id
    public function searchforStateAlbaranesFac()
    {
        $state = "Facturado";

        $albaranes = $this->albaranesModel->where('albaran_status', $state)->paginate(config('Configuration')->regClientesPage);

        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();
        $drivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        return $this->twig->render('Front/Albaranes/list.html.twig', ['albaranes' => $albaranes, 'drivers_all' => $drivers_all, 'customers_all' => $customers_all, 'pager' => $this->albaranesModel->pager->links()]);
    }

    //busca por id  pallet 02
    public function searchforDateAlbaranes()
    {


        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();
        $drivers_all = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();

        try {

            $created_at = $this->request->getPost('created_at');


            $albaranes = $this->albaranesModel->where('created_at', $created_at)->paginate(config('Configuration')->regOrdersPage);

            return $this->twig->render('Front/Albaranes/list.html.twig', ['albaranes' => $albaranes, 'drivers_all' => $drivers_all, 'customers_all' => $customers_all, 'pager' => $this->albaranesModel->pager->links()]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listAlbaranes')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Debe seleccionar una fecha ']
            ]);
        }
    }




    public function deleteAlbaran($id_albaran = null)
    {

        try {

            $db = db_connect();

            $builder = $db->table('albaranes');
            $builder->where('id_albaran', $id_albaran);
            $builder->delete();


          // Configurar una respuesta JSON para éxito
          $response = [
            'success' => true,
            'message' => 'Albaran eliminado con éxito.'
        ];



        return $this->response->setJSON($response);

    } catch (\Throwable $th) {

        $this->log->setLine('Error', $th->getMessage());


            if ($th->getCode() == 1451) {

                // Configurar una respuesta JSON para éxito
                $response = [
                    'success' => true,
                    'message' => 'Error al eliminar el albaran, no se puede eliminar un albaran que esta ASIGNADO en alguna Factura.'
                ];

                return $this->response->setJSON($response);

                        // return redirect()->route('listAlbaranes')->with(
                        //     'msg',
                        //     [
                        //         'type' => 'alert-danger',
                        //         'body' => ['Error al eliminar el albaran, no se puede eliminar un albaran que esta ASIGNADO en alguna Factura. ']
                        //     ]
                        // );


            } else {


            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Error al eliminar el albaran. Contacte con Grupo IC Solution.'
            ];

            return $this->response->setJSON($response);

        //         return redirect()->route('listAlbaranes')->with('msg', [
        //             'type' => 'alert-danger',
        //             'body' => ['Error al eliminar el albaran. Contacte con Grupo IC Solution. ']
        //         ]);
             }
        }
    }
}
