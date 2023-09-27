<?php

namespace App\Controllers\Front;



use DateTime;



use App\Entities\Remesas;
use App\Controllers\BaseController;
use App\Entities\Bills;

use App\Libraries\Log;
use CodeIgniter\I18n\Time;

use App\Entities\Albaranes;
use App\Entities\LastBills;
use App\Entities\Supplements;
use App\Entities\TableBillsAlbaranes;

use App\Libraries\Sepa\Sepa;
use App\Libraries\Sepa\SepaHeader;
use App\Libraries\Sepa\SepaPaymentInformation;

use DateInterval;
use Exception;
use stdClass;

class BillsController extends BaseController
{
    protected Log $log;

    protected Log $logFacturas;
    protected $billsModel;

    protected $lastBillsModel;

    protected $remesasModel;

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

    protected $tableBillsAlbaranesModel;

    protected $arrayNombre = [];
    protected $db;


    public function __construct()
    {
        $this->log = new Log('Bills/');
        $this->billsModel = model('BillsModel');
        $this->lastBillsModel = model('LastBillsModel');
        $this->albaranesModel = model('AlbaranesModel');
        $this->customersModel = model('CustomersModel');
        $this->driversModel = model('Drivers_Model');
        $this->servicesModel = model('Services_Model');
        $this->workLocationModel = model('WorkLocationModel');
        $this->vehiclesModel = model('VehiclesModel');
        $this->containerModel = model('ContainersModel');

        $this->remesasModel = model('RemesasModel');

        $this->paymentMethodModel = model('PaymentMethodModel');
        $this->ratesModel = model('RatesModel');
        $this->supplementsModel = model('SupplementsModel');
        $this->actualstateModel = model('ActualStateModel');
        $this->retainerModel = model('RetainerModel');

        $this->tableBillsAlbaranesModel = model('TableBillsAlbaranesModel');


        helper('form');
    }

    /**
     * Crar Facturas de  albaranes y supplementos
     */
    public function index()
    {

        $withoutCustomer = false;
        $form_add = 2;
        $customers_selected = $this->customersModel->orderBy('id_customer', 'ASC')->findAll();
        $services_ = $this->servicesModel->orderBy('id_service', 'ASC')->findAll();
        $supplements = $this->supplementsModel->orderBy('id_supplements', 'ASC')->findAll();
        $rates = $this->ratesModel->orderBy('id_rates', 'DESC')->findAll();
        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'DESC')->findAll();
        $vehicles = $this->vehiclesModel->orderBy('id_vehicle', 'DESC')->findAll();

     //   $worklocations = $this->workLocationModel->orderBy('id_work_locations', 'DESC')->findAll();
        $containers = $this->containerModel->orderBy('id_container', 'DESC')->findAll();
        $services = $this->servicesModel->orderBy('id_service', 'DESC')->findAll();
        $drivers = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();
        $customers = $this->customersModel->orderBy('id_customer', 'ASC')->findAll();

        $albaranes = $this->albaranesModel->orderBy('id_albaran', 'ASC')->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Bills/create.html.twig', ['lugar' => 'index', 'services_' => $services_, 'customers_selected' => $customers_selected, 'withoutCustomer' => $withoutCustomer, 'form_add' => $form_add, 'albaranes' => $albaranes, 'supplements' => $supplements, 'rates' => $rates, 'containers' => $containers,  'vehicles' => $vehicles, 'customers' => $customers, 'services' => $services, 'drivers' => $drivers, 'payment_method' => $payment_method, 'pager' => $this->albaranesModel->pager->links()]);
    }



    /**
     * Funcion que muestra la pagina de crear solo supllementos
     */
    public function createSupplements()
    {

        $withoutCustomer = false;
        $form_add = 2;
        $customers_selected = $this->customersModel->orderBy('created_at', 'DESC')->findAll();
        $services_ = $this->servicesModel->orderBy('id_service', 'ASC')->findAll();
        $supplements = $this->supplementsModel->orderBy('id_supplements', 'ASC')->findAll();
        $rates = $this->ratesModel->orderBy('id_rates', 'DESC')->findAll();
        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'DESC')->paginate(config('Configuration')->regPerPage);
        $vehicles = $this->vehiclesModel->orderBy('created_at', 'DESC')->findAll();

     //   $worklocations = $this->workLocationModel->orderBy('id_work_locations', 'DESC')->findAll();
        $containers = $this->containerModel->orderBy('id_container', 'DESC')->findAll();
        $services = $this->servicesModel->orderBy('id_service', 'DESC')->findAll();
        $drivers = $this->driversModel->orderBy('id_driver', 'DESC')->findAll();
        $customers = $this->customersModel->orderBy('id_customer', 'ASC')->findAll();

        $albaranes = $this->albaranesModel->orderBy('id_albaran', 'ASC')->paginate(config('Configuration')->regPerPage);



        return $this->twig->render('Front/Bills/createSupplements.html.twig', [
            'services_' => $services_,
            'customers_selected' => $customers_selected,
            'withoutCustomer' => $withoutCustomer,
            'form_add' => $form_add,
            'albaranes' => $albaranes,
            'supplements' => $supplements,
            'rates' => $rates,
            'containers' => $containers,

            'vehicles' => $vehicles,
          //  'worklocations' => $worklocations,
            'customers' => $customers,
            'services' => $services,
            'drivers' => $drivers,
            'payment_method' => $payment_method,
            'pager' => $this->albaranesModel->pager->links()
        ]);
    }

    /**
     * funcion boton seleccionar de lista de Direcciones de Obras
     */
    public function getIdWorkLocationBills($id_work_location = null)
    {
        $w_selected = true;
        $id_albaran_work_location = null;
        $work_location = null;

        $worklocations = NULL;
        $arraysIdsAlbaranes = [];
        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->paginate(config('Configuration')->regPerPage);

        $customers_selected = $this->customersModel->orderBy('id_customer', 'ASC')->orderBy('id_customer', 'DESC')->findAll();
        $supplements = $this->supplementsModel->orderBy('id_supplements', 'ASC')->findAll();
        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regPerPage);

        $worklocations_false = false;


        /**
         * Si no ahi direciones de obras con esos albaranes
         */
        if (empty($worklocations) || $worklocations === NULL || $worklocations === 0) {
            $worklocations_false = true;
        }


        //Obtenemos el cliente
        foreach ($worklocations as $id) {
            $id_customer = $id->id_customer;
            $customersId = $id->id_customer;
        }

        //Obtenemos la direccion de Obra de Albaranes
        foreach ($worklocations as $id_alb) {
            $id_work_locations = $id_alb->id_work_locations;
        }
        //Obtenemos solo los albaranes de esa direccion de obra
        $worklocations_albaranes = $this->albaranesModel->where('id_work_location', $id_work_locations)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regBillsAlbaranesPage);


        $customers = $this->customersModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regCustomersPage);

        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regCustomersPage);



        return $this->twig->render('Front/Bills/create.html.twig', [
            'lugar' => 'index',
            'customersId' => $customersId,
            'arraysIdsAlbaranes' => $arraysIdsAlbaranes,
            'supplements' => $supplements,
            'worklocations_albaranes' => $worklocations_albaranes,
            'id_albaran_work_location' => $id_albaran_work_location,
            'worklocations' => $worklocations,
            'customers' => $customers,
            'payment_method' => $payment_method,

            'w_selected' => $w_selected,
            'customers_selected' => $customers_selected,
            'worklocations_false' => $worklocations_false,
            'pager' => $this->albaranesModel->pager->links()
        ]);
    }

    /**
     * funcion boton seleccionar de lista de Direcciones de Obras  PAGINA DE SUPLEMENTOS
     */
    public function getIdWorkLocationBillsSupp($id_work_location = null)
    {



        $id_albaran_work_location = null;
        $work_location = null;
        $ID_payment_method = null;
        $worklocations = NULL;
        $arraysIdsAlbaranes = [];
        $name_payment_method = null;

        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'DESC')->paginate(config('Configuration')->regCustomersPage);

        $customers_selected = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();

        $supplements = $this->supplementsModel->orderBy('id_supplements', 'DESC')->findAll();


        $worklocations_false = false;
        $worklocations_selection = true;

        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regCustomersPage);

        //Obtenemos el cliente
        foreach ($worklocations as $id) {
            $id_customer = $id->id_customer;
            $customersId = $id->id_customer;
        }

        //Obtenemos la direccion de Obra de Albaranes
        foreach ($worklocations as $id_alb) {
            $id_work_locations = $id_alb->id_work_locations;
        }
        //Obtenemos solo los albaranes de esa direccion de obra
        $worklocations_albaranes = $this->albaranesModel->where('id_work_location', $id_work_locations)->paginate(config('Configuration')->regCustomersPage);
        $customers = $this->customersModel->where('id_customer', $id_customer)->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regCustomersPage);





        foreach ($customers as $rows) {
            $name_payment_method = $rows->payment_method;
        }
        $name_payment_method = (int)$name_payment_method;


        return $this->twig->render('Front/Bills/createSupplements.html.twig', [
            'lugar' => 'index',
            'customersId' => $customersId,
            'arraysIdsAlbaranes' => $arraysIdsAlbaranes,
            'supplements' => $supplements,
            'worklocations_albaranes' => $worklocations_albaranes,
            'id_albaran_work_location' => $id_albaran_work_location,
            'worklocations' => $worklocations,
            'customers' => $customers,
            'name_payment_method' => $name_payment_method,
            'worklocations_selection' => $worklocations_selection,

            'payment_method' => $payment_method,


            'customers_selected' => $customers_selected,
            'worklocations_false' => $worklocations_false,
            'pager' => $this->albaranesModel->pager->links()
        ]);
    }

    /**
     * Filtro para obtener el cliente seleccionado del apartado de filtros de Facturas en Seleccionar Direccion de Obra
     */
    public function getIdWorkLocationCustomersBills($id_customer = null)
    {


        $countWorkLocacion = false;
        $actual_state_container_customer = null;
        $id_last_albaran_insert = 1;
        $payment_method_customer = null;
        $worklocations_albaranes = false;


        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'DESC')->paginate(config('Configuration')->regPerPage);

        $supplements = $this->supplementsModel->orderBy('id_supplements', 'DESC')->findAll();
        $customers_selected = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();

        //    $albaranes = $this->albaranesModel->orderBy('id_albaran', 'DESC')->paginate(config('Configuration')->regPerPage);

        $albaranes = $this->albaranesModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regCustomersPage);

        $customersId = $this->customersModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regCustomersPage);

        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regCustomersPage);


        /**
         * Si no ahi direciones de obras con esos albaranes
         */
        if ($worklocations === 0 || $worklocations === null || $worklocations === false) {
            $worklocations = false;
        } else {
            $worklocations_selected = $this->workLocationModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regCustomersPage);
        }

        /**
         * Si no ahi direciones de obras con esos albaranes
         */
        if ($albaranes === 0 || $albaranes === null || $albaranes === false) {
            $worklocations_albaranes = false;
        }

        //Obtenemos el cliente
        foreach ($worklocations as $id) {
            $customersId = $id->id_customer;
        }

        $customers = $this->customersModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regCustomersPage);

        return $this->twig->render('Front/Bills/create.html.twig', [
            'lugar' => 'index',
            'actual_state_container_customer' => $actual_state_container_customer,
            'id_last_albaran_insert' => $id_last_albaran_insert,
            'customersId' => $customersId,
            'customers_selected' => $customers_selected,
            'supplements' => $supplements,
            'countWorkLocacion' => $countWorkLocacion,


            'albaranes' => $albaranes,
            'worklocations' => $worklocations,
            'customers' => $customers,
            'payment_method' => $payment_method,

            'pager' => $this->albaranesModel->pager->links()
        ]);
    }


    /**
     * Filtro para obtener el cliente seleccionado del apartado de filtros de Facturas en Seleccionar Direccion de Obra
     * SUPLEMENTOS
     */
    public function getIdWorkLocationCustomersBillsSuppl($id_customer = null)
    {


        $countWorkLocacion = false;
        $actual_state_container_customer = null;
        $id_last_albaran_insert = 1;
        $payment_method_customer = null;
        $worklocations_false = false;

        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->paginate(config('Configuration')->regPerPage);
        $supplements = $this->supplementsModel->orderBy('id_supplements', 'ASC')->findAll();
        $customers_selected = $this->customersModel->orderBy('created_at', 'DESC')->findAll();

        $albaranes = $this->albaranesModel->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regPerPage);
        $albaranes = $this->albaranesModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regPerPage);
        $customersId = $this->customersModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regPerPage);



        /**
         * Si no ahi direciones de obras con esos albaranes
         */
        if (empty($worklocations) || $worklocations === NULL || $worklocations === 0) {
            $worklocations_false = true;
        } else {
            $worklocations_selected = $this->workLocationModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        }

        //Obtenemos el cliente
        foreach ($worklocations as $id) {

            $customersId = $id->id_customer;
        }


        $customers = $this->customersModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regCustomersPage);
        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regCustomersPage);


        return $this->twig->render('Front/Bills/createSupplements.html.twig', [
            'lugar' => 'index',
            'actual_state_container_customer' => $actual_state_container_customer,
            'id_last_albaran_insert' => $id_last_albaran_insert,
            'customersId' => $customersId,
            'customers_selected' => $customers_selected,
            'supplements' => $supplements,
            'countWorkLocacion' => $countWorkLocacion,
            'albaranes' => $albaranes,
            'worklocations' => $worklocations,
            'customers' => $customers,
            'payment_method' => $payment_method,
            'worklocations_false' => $worklocations_false,
            'pager' => $this->albaranesModel->pager->links()
        ]);
    }

    public function pre_view_create()
    {


        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $date_today = $date->format('d-m-Y');



        $id_customer = null;
        $id_customer = null;
        $id_work_location = null;
        $containers = null;
        $sum_price_supplements_select = 0;
        $sum_price_service_select = 0;
        $id_customer = null;
        $id_work_location = null;

        $info_alb_con_ser = [];
        $array_all_containers = [];
        $array_all_cubic_meters = [];
        $id_customer = null;
        $id_work_location = null;
        $iva = 0;
        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        //Suplementos
        $selected_supplements = [];
        $array_supplements = [];

        $price_dto = 0;
        $price_total = 0;
        $price_total_dtos = 0;

        $anticipos = 0;
        $cargos = 0;
        $saldos = 0;
        $bank_count = 0;

        $json_supplements = [];

        $containers = [];
        $array_all_cubic_meters = [];
        $arraysIdsAlbaranes = [];
        $supplements = [];
        $json_services = [];
        $albaran = [];

        $total_dto_all_supplements = 0;
        $services = [];
        $objeto = null;
        $services_select = [];
        $id_alabaran = null;
        $supplementsPricesObjectArray = [];

        $pricesObjectArray = [];
        $supplementsObjectArray = [];

        $price_final = 0;

        $total_price_supp_alb = 0;

        $supplementsObjectAditional = [];
        $supplementsObjectArrayAditional = [];

        $price_total_base_albaranes = null;
        $servicesObjectArray = [];
        //Suplementos
        $selected_supplements = [];

        $price_total_base_albaranes = 0;

        $iva = 0;
        $sum_tax_base = 0;
        $total_con_iva = 0;
        $retainer_amount = 0;
        $price_total_all = 0;
        $sum_dto = 0;
        $dto_s = 0;

        $subtotal_sum_supplements = 0;
        $price_total_all_supp = 0;

        $suppl_editional_existe = false;

        $supplements_obj_existe_alb = false;

        $total_price_supp = 0;
        $total_price_supp_final = 0;
        $total_price_supp_aditional = 0;

        $supplementsObjectAditional = null;

        $supplements_obj_array_aditional = [];

        $total_dtos_container_supplement = 0;

        $total_dto_all_supplements_alb = 0;
        $total_dto_all_supplements_adtitional = 0;

        $price_dto_supp = 0;
        $total_dto_all_suppl_alb = 0;

        // Trae los albaranes seleccionados
        $id_albaranes_selected = $this->request->getPost('albaranes');
        $supplements_id = $this->request->getPost('supplements_id');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $supplement_dto = $this->request->getPost('supplement_dto');
        $retainer_amount = $this->request->getPost('retainer_amount');
        $payment_method = $this->request->getPost('payment_method');

        if ($pvp_edit !== null) {

            for ($i = 0; $i < count($pvp_edit); $i++) {
                if ($pvp_edit[$i] === null || $pvp_edit[$i] === "0.00" || $pvp_edit[$i] === "" || $pvp_edit[$i] === "0") {
                    $pvp_edit = 0;
                    $pvp_edit[$i] = $pvp_edit;
                }
            }
        }
        if ($supplement_dto !== null) {
            for ($i = 0; $i < count($supplement_dto); $i++) {
                if ($supplement_dto[$i] === null || $supplement_dto[$i] === "0.00" || $supplement_dto[$i] === "" || $supplement_dto[$i] === "0") {
                    $dto = 0;
                    $supplement_dto[$i] = $dto;
                }
            }
        }
        /**
         * recorremos los albaranes seleccionados del array y obtenemos un id de alabaran
         */
        foreach ($id_albaranes_selected as $rows1) {
            $id_alabaran = $rows1;
        }
        /**
         * de ese id de albaran obtenomos un objeto modelo
         */
        $albaran_3 = $this->albaranesModel->where('id_albaran', $id_alabaran)->paginate(config('Configuration')->regPerPage);

        /**
         * Lo recorremos y obtenemos el id_customer y el id de la direccion de obra
         */
        foreach ($albaran_3 as $rows) {
            $id_customer = $rows->id_customer;
            $id_work_location = $rows->id_work_location;
        }
        /**
         * Obtenemos dfe la tabla customer
         */
        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);




        //fecha de vencimiento para obtner la fecha de   fecha de vencimiento depende el metodo de pago  ------
        $id_customer_expiration_date = $this->customersModel->where('id_customer', $id_customer)->find();
        foreach ($id_customer_expiration_date as $key => $rows) {
            $payment_method_customer =  $rows->payment_method;
        }
        $name_customer_expiration_date = $this->paymentMethodModel->where('id_payment_method', $payment_method_customer)->first();


            $name_payment_method = $name_customer_expiration_date->name;

        $tipoPago = $payment_method_customer;

        $fechaActual = new DateTime();


        if ($tipoPago === '10') {
            $intervalo = new DateInterval('P10D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '11') {
            $intervalo = new DateInterval('P15D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '12') {
            $intervalo = new DateInterval('P30D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '13') {
            $intervalo = new DateInterval('P45D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '14') {
            $intervalo = new DateInterval('P60D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '15') {
            $intervalo = new DateInterval('P90D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '16') {
            $intervalo = new DateInterval('P60D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '30') {
            $intervalo = new DateInterval('P1D');
            $fechaActual->add($intervalo);
        } else {

            $fechaActual = $fechaActual;
        }

        // Formatear y mostrar la fecha resultante
        $fechaFormateada = $fechaActual->format('Y-m-d');



        /**
         * Recorremos y obtenemos el iva y la cuetna bancaria
         */
        foreach ($customers as $i) {
            $iva = $i->iva;
            $bank_count = $i->bank_count;
        }

        /**
         * Codificamos para seguridad
         */
        $bank_count = substr_replace($bank_count, "******", 0, 6);

        /**
         * Obtenemos el precio total con iva
         */
        $price_total_width_iva =  $price_total *  $iva / 100;

        /**
         * Obtenemos el precio final que es el precio total + el precio del iva
         */
        $total_final = $price_total + $price_total_width_iva;

        /**
         * Si ahi anticipos lo restamos al precio total
         */
        if ($anticipos > 0) {
            $total_final =  $total_final - $retainer_amount;
        }

        $anticipos = $retainer_amount;
        $cargos = $total_final;
        $saldos = $total_final;

        /**
         * Obtenemos los albranes selecionado por objetos
         */
        $id_albaranes_selected = $this->albaranesModel->join('containers', 'containers.id_container = albaranes.id_container')->whereIn('id_albaran', $id_albaranes_selected)->paginate(config('Configuration')->regPerPage);


        if ($supplements_id) {

            $suppl_editional_existe = true;
            /**
             * Recorremos los objetos de los supplementos adiconales  seleccionados
             */

            $supplements_obj_array = $this->supplementsModel->whereIn('id_supplements', $supplements_id)->paginate(config('Configuration')->regPerPage);

            foreach ($supplements_obj_array as $key => $obj_supp) {

                $pvp_edit_total = 0;

                if ($supplement_dto[$key] !== 0 || $supplement_dto[$key] !== null) {
                    $pvp_edit_total = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $price_final =  $pvp_edit[$key] - $pvp_edit_total;
                } else {
                    $pvp_edit_total = $pvp_edit[$key];
                    $price_final =  $pvp_edit[$key];
                }

                $pvp_edit_total += $pvp_edit_total;

                if ($supplement_dto[$key] === 0 || $supplement_dto[$key] === null) {

                    $dto_s = 0;
                    $price_dto = 0;
                } else {
                    $price_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $dto_s = $supplement_dto[$key];
                }

                //90  + 90 +90 = 270
                $total_price_supp_aditional += $price_final;

                $total_dto_all_supplements_adtitional += $price_dto;

                //suma de todos los dtos y precios base y se descuentan
                $total_price_and_dto = $price_total_all - $total_dto_all_supplements;




                $id_supplements = $obj_supp->id_supplements;

                $supplementsObjectArrayAditional = [];
                $supplementsObjectArrayAditionalStdClass = [];

                $supplementsObjectAditional = new Supplements();
                $supplementsObjectAditional->name = $obj_supp->name;
                $supplementsObjectAditional->pvp_edit = $pvp_edit[$key];
                $supplementsObjectAditional->dto = $dto_s;
                $supplementsObjectAditional->price_dto = $price_dto;
                $supplementsObjectAditional->price_total = $price_final;

                $supplementsObjectArrayAditional[$key] = $supplementsObjectAditional;
                $supplements_obj_array[$key]->supplementsObjectAditional = $supplementsObjectArrayAditional;
            }
        } else {
            $suppl_editional_existe = false;
        }


        //fin if

        /**
         * Recorremos los objetos de los albaranes seleccionados
         */
        foreach ($id_albaranes_selected as $key => $obj) {

            $tax_base = $obj->tax_base;
            $discount = $obj->discount;
            $price_discount = $obj->price_discount;
            $amount_tax_base_discount = $obj->amount_tax_base_discount;

            //total de los supllementos
            $total_dto_all_supplements_alb += $obj->subtotal_sum_supplements;

            //Suma total con descuentos de los albaranes
            $price_total_base_albaranes += $amount_tax_base_discount;

            $sum_tax_base += $tax_base;

            //dto de contenedor
            $sum_dto += $price_discount;



            /**
             * Realizamos lo mismo con los suplementos
             */

            $supplements_alb = $obj->supplements;

            if ($supplements_alb) {
                $supplements = json_decode($obj->supplements);
                $supplements_obj_existe_alb = true;

                foreach ($supplements as $x2 => $s) {

                    $id_supplements = $s->id_supplements;
                    $pvp_edit = $s->pvp_edit;
                    $price_dto_supp = $s->price_dto;


                    if ($price_dto_supp !== null) {
                        $total_dto_all_suppl_alb += $price_dto_supp;
                    }
                    //suma todos los dtos


                    $total_price_supp = $s->price_total;

                    //suma todos los suplementos
                    $total_price_supp_alb += $total_price_supp;

                    $supplementsObject = new stdClass();
                    $supplementsObject->name = $s->name;
                    $supplementsObject->pvp_edit = $s->pvp_edit;
                    $supplementsObject->price_dto = $s->price_dto;
                    $supplementsObject->dto = $s->dto;
                    $supplementsObject->total_price_supp = $total_price_supp;

                    $supplementsObjectArray[$x2] = $supplementsObject;
                }

                $id_albaranes_selected[$key]->supplementsObject = $supplementsObjectArray;
            }



            $prices = new stdClass();

            $prices->tax_base = $tax_base;
            $prices->discount = $discount;
            $prices->price_discount = $price_discount;
            $prices->amount_tax_base_discount = $amount_tax_base_discount;

            $pricesObjectArray[$key] = $prices;

            $id_albaranes_selected[$key]->prices = $pricesObjectArray;
        }



        //-------------


        //suma dtos de suplementos
        $all_dto_sup =  $total_dto_all_supplements_adtitional + $total_dto_all_suppl_alb;


        //suma total todos los descuento contenderesw y suplementos
        $total_dtos = $sum_dto + $all_dto_sup;

        //suma los dtos de los supplemntos en precios y de los contenedores
        //675

        $total_dtos_and_price_container_supplement = $tax_base -  $total_dtos_container_supplement;

        //suma los suplementos de adiconales y dentro del albaran  148
        $sum_all_supplements = $total_price_supp_aditional + $total_price_supp_alb;
        //675  suma contenedor taxe base - dto  =
        $total_dtos_and_price_container = $sum_tax_base - $sum_dto;


        $gross_total = $total_dtos_and_price_container + $sum_all_supplements;


        $taxable_base = $gross_total - $total_dtos;


        //---------------




        if ($iva === "21") {
            $cuota =  $taxable_base * 0.21;
        }
        if ($iva === "10") {
            $cuota =  $taxable_base * 0.10;
        }
        if ($iva === "4") {
            $cuota =  $taxable_base * 0.04;
        }



        $total_con_iva = $cuota +  $taxable_base;

        $cargos = $total_con_iva;
        $saldos = $total_con_iva;

        if ($retainer_amount > 0) {
            $cargos = $total_con_iva - $retainer_amount;
            $saldos = $total_con_iva - $retainer_amount;
        }



        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);

        if ($price_total_all === 0) {
            $price_total_all = "";
        }



        return $this->twig->render('Front/Bills/preBills.html.twig', [

            'array_all_containers' => $array_all_containers,
            'array_all_cubic_meters' => $array_all_cubic_meters,
            'customers' => $customers,
            'info_alb_con_ser' => $info_alb_con_ser,
            'albaran' => $albaran,
            'containers' => $containers,
            'worklocations' => $worklocations,
            'id_work_location' => $id_work_location,
            'date_today' => $date_today,
            'id_albaranes_selected' => $id_albaranes_selected,

            'total_dtos_and_price_container_supplement' => $total_dtos_and_price_container_supplement,

            'total_dtos_container_supplement' => $total_dtos_container_supplement,
            'taxable_base' => $taxable_base,


            'total_dtos' => $total_dtos,

            'gross_total' => $gross_total,





            'retainer_amount' => $retainer_amount,
            'price_total_width_iva' => $price_total_width_iva,
            'total_final' => $total_final,
            'payment_method' => $payment_method,

            'cargos' => $cargos,
            'saldos' => $saldos,
            'sum_dto' => $sum_dto,

            'price_final' => $price_final,

            'bank_count' => $bank_count,

            'sum_price_supplements_select' => $sum_price_supplements_select,
            'selected_supplements' => $selected_supplements,
            'json_supplements' => $json_supplements,
            'supplements' => $supplements,
            'supplements_obj_array' => $supplements_obj_array,

            'price_total' => $price_total,
            'price_total_dtos' => $price_total_dtos,


               'fechaFormateada' => $fechaFormateada,
            'name_payment_method' => $name_payment_method,


            'price_total_base_albaranes' => $price_total_base_albaranes,
            'iva' => $iva,
            'sum_tax_base' => $sum_tax_base,
            'total_con_iva' => $total_con_iva,
            'cuota' => $cuota,

            'price_total_all' => $price_total_all,
            'suppl_editional_existe' => $suppl_editional_existe,

            'pager' => $this->albaranesModel->pager->links()
        ]);
    }

    public function pre_view_bills_albaran()
    {



        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $date_today = $date->format('d-m-Y');



        $id_customer = null;
        $id_customer = null;
        $id_work_location = null;
        $containers = null;
        $sum_price_supplements_select = 0;
        $sum_price_service_select = 0;
        $id_customer = null;
        $id_work_location = null;

        $info_alb_con_ser = [];
        $array_all_containers = [];
        $array_all_cubic_meters = [];
        $id_customer = null;
        $id_work_location = null;
        $iva = 0;
        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        //Suplementos
        $selected_supplements = [];
        $array_supplements = [];

        $price_dto = 0;
        $price_total = 0;
        $price_total_dtos = 0;

        $anticipos = 0;
        $cargos = 0;
        $saldos = 0;
        $bank_count = 0;

        $json_supplements = [];

        $containers = [];
        $array_all_cubic_meters = [];
        $arraysIdsAlbaranes = [];
        $supplements = [];
        $json_services = [];
        $albaran = [];

        $total_dto_all_supplements = 0;
        $services = [];
        $objeto = null;
        $services_select = [];
        $id_alabaran = null;
        $supplementsPricesObjectArray = [];

        $pricesObjectArray = [];
        $supplementsObjectArray = [];

        $price_final = 0;

        $total_price_supp_alb = 0;

        $supplementsObjectAditional = [];
        $supplementsObjectArrayAditional = [];

        $price_total_base_albaranes = null;
        $servicesObjectArray = [];
        //Suplementos
        $selected_supplements = [];

        $price_total_base_albaranes = 0;

        $iva = 0;
        $sum_tax_base = 0;
        $total_con_iva = 0;
        $retainer_amount = 0;
        $price_total_all = 0;
        $sum_dto = 0;
        $dto_s = 0;

        $subtotal_sum_supplements = 0;
        $price_total_all_supp = 0;

        $suppl_editional_existe = false;

        $supplements_obj_existe_alb = false;

        $total_price_supp = 0;
        $total_price_supp_final = 0;
        $total_price_supp_aditional = 0;

        $supplementsObjectAditional = null;

        $supplements_obj_array_aditional = [];

        $total_dtos_container_supplement = 0;

        $total_dto_all_supplements_alb = 0;
        $total_dto_all_supplements_adtitional = 0;

        $price_dto_supp = 0;
        $total_dto_all_suppl_alb = 0;

        // Trae los albaranes seleccionados
        $id_albaranes_selected = $this->request->getPost('albaranes');
        $supplements_id = $this->request->getPost('supplements_id');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $supplement_dto = $this->request->getPost('supplement_dto');
        $retainer_amount = $this->request->getPost('retainer_amount');
        $payment_method = $this->request->getPost('payment_method');



        if ($pvp_edit !== null) {

            for ($i = 0; $i < count($pvp_edit); $i++) {
                if ($pvp_edit[$i] === null || $pvp_edit[$i] === "0.00" || $pvp_edit[$i] === "" || $pvp_edit[$i] === "0") {
                    $pvp_edit = 0;
                    $pvp_edit[$i] = $pvp_edit;
                }
            }
        }
        if ($supplement_dto !== null) {
            for ($i = 0; $i < count($supplement_dto); $i++) {
                if ($supplement_dto[$i] === null || $supplement_dto[$i] === "0.00" || $supplement_dto[$i] === "" || $supplement_dto[$i] === "0") {
                    $dto = 0;
                    $supplement_dto[$i] = $dto;
                }
            }
        }
        /**
         * recorremos los albaranes seleccionados del array y obtenemos un id de alabaran
                */
            if ($id_albaranes_selected !== null) {

                foreach ($id_albaranes_selected as $rows1) {
                        $id_alabaran = $rows1;
                }

            }else{

                $previousMsg = $this->session->getFlashdata('msg2');
                $currentMsg = [
                    'type' => 'error',
                    'title' => 'ATENCION!',
                    'text' => 'Revise los albaranes.',
                    ];

                if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
                }
                return redirect()->route('pre_view_create_supplements');

            }
        /**
         * de ese id de albaran obtenomos un objeto modelo
         */
        $albaran_3 = $this->albaranesModel->where('id_albaran', $id_alabaran)->paginate(config('Configuration')->regPerPage);

        /**
         * Lo recorremos y obtenemos el id_customer y el id de la direccion de obra
         */
        foreach ($albaran_3 as $rows) {
            $id_customer = $rows->id_customer;
            $id_work_location = $rows->id_work_location;
        }
        /**
         * Obtenemos dfe la tabla customer
         */
        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

        /**
         * Recorremos y obtenemos el iva y la cuetna bancaria
         */
        foreach ($customers as $i) {
            $iva = $i->iva;
            $bank_count = $i->bank_count;
        }

        /**
         * Codificamos para seguridad
         */
        $bank_count = substr_replace($bank_count, "******", 0, 6);

        /**
         * Obtenemos el precio total con iva
         */
        $price_total_width_iva =  $price_total *  $iva / 100;

        /**
         * Obtenemos el precio final que es el precio total + el precio del iva
         */
        $total_final = $price_total + $price_total_width_iva;

        /**
         * Si ahi anticipos lo restamos al precio total
         */
        if ($anticipos > 0) {
            $total_final =  $total_final - $retainer_amount;
        }

        $anticipos = $retainer_amount;
        $cargos = $total_final;
        $saldos = $total_final;

        /**
         * Obtenemos los albranes selecionado por objetos
         */
        $id_albaranes_selected = $this->albaranesModel->join('containers', 'containers.id_container = albaranes.id_container')->whereIn('id_albaran', $id_albaranes_selected)->paginate(config('Configuration')->regPerPage);


        if ($supplements_id) {

            $suppl_editional_existe = true;
            /**
             * Recorremos los objetos de los supplementos adiconales  seleccionados
             */

            $supplements_obj_array = $this->supplementsModel->whereIn('id_supplements', $supplements_id)->paginate(config('Configuration')->regPerPage);

            foreach ($supplements_obj_array as $key => $obj_supp) {

                $pvp_edit_total = 0;

                if ($supplement_dto[$key] !== 0 || $supplement_dto[$key] !== null) {
                    $pvp_edit_total = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $price_final =  $pvp_edit[$key] - $pvp_edit_total;
                } else {
                    $pvp_edit_total = $pvp_edit[$key];
                    $price_final =  $pvp_edit[$key];
                }

                $pvp_edit_total += $pvp_edit_total;

                if ($supplement_dto[$key] === 0 || $supplement_dto[$key] === null) {

                    $dto_s = 0;
                    $price_dto = 0;
                } else {
                    $price_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $dto_s = $supplement_dto[$key];
                }

                //90  + 90 +90 = 270
                $total_price_supp_aditional += $price_final;

                $total_dto_all_supplements_adtitional += $price_dto;

                //suma de todos los dtos y precios base y se descuentan
                $total_price_and_dto = $price_total_all - $total_dto_all_supplements;




                $id_supplements = $obj_supp->id_supplements;

                $supplementsObjectArrayAditional = [];
                $supplementsObjectArrayAditionalStdClass = [];

                $supplementsObjectAditional = new Supplements();
                $supplementsObjectAditional->name = $obj_supp->name;
                $supplementsObjectAditional->pvp_edit = $pvp_edit[$key];
                $supplementsObjectAditional->dto = $dto_s;
                $supplementsObjectAditional->price_dto = $price_dto;
                $supplementsObjectAditional->price_total = $price_final;

                $supplementsObjectArrayAditional[$key] = $supplementsObjectAditional;
                $supplements_obj_array[$key]->supplementsObjectAditional = $supplementsObjectArrayAditional;
            }
        } else {
            $suppl_editional_existe = false;
        }


        //fin if

        /**
         * Recorremos los objetos de los albaranes seleccionados
         */
        foreach ($id_albaranes_selected as $key => $obj) {

            $tax_base = $obj->tax_base;
            $discount = $obj->discount;
            $price_discount = $obj->price_discount;
            $amount_tax_base_discount = $obj->amount_tax_base_discount;

            //total de los supllementos
            $total_dto_all_supplements_alb += $obj->subtotal_sum_supplements;

            //Suma total con descuentos de los albaranes
            $price_total_base_albaranes += $amount_tax_base_discount;

            $sum_tax_base += $tax_base;

            //dto de contenedor
            $sum_dto += $price_discount;



            /**
             * Realizamos lo mismo con los suplementos
             */

            $supplements_alb = $obj->supplements;

            if ($supplements_alb) {
                $supplements = json_decode($obj->supplements);
                $supplements_obj_existe_alb = true;

                foreach ($supplements as $x2 => $s) {

                    $id_supplements = $s->id_supplements;
                    $pvp_edit = $s->pvp_edit;
                    $price_dto_supp = $s->price_dto;


                    if ($price_dto_supp !== null) {
                        $total_dto_all_suppl_alb += $price_dto_supp;
                    }
                    //suma todos los dtos


                    $total_price_supp = $s->price_total;

                    //suma todos los suplementos
                    $total_price_supp_alb += $total_price_supp;

                    $supplementsObject = new stdClass();
                    $supplementsObject->name = $s->name;
                    $supplementsObject->pvp_edit = $s->pvp_edit;
                    $supplementsObject->price_dto = $s->price_dto;
                    $supplementsObject->dto = $s->dto;
                    $supplementsObject->total_price_supp = $total_price_supp;

                    $supplementsObjectArray[$x2] = $supplementsObject;
                }

                $id_albaranes_selected[$key]->supplementsObject = $supplementsObjectArray;
            }



            $prices = new stdClass();

            $prices->tax_base = $tax_base;
            $prices->discount = $discount;
            $prices->price_discount = $price_discount;
            $prices->amount_tax_base_discount = $amount_tax_base_discount;

            $pricesObjectArray[$key] = $prices;

            $id_albaranes_selected[$key]->prices = $pricesObjectArray;
        }



        //-------------


        //suma dtos de suplementos
        $all_dto_sup =  $total_dto_all_supplements_adtitional + $total_dto_all_suppl_alb;


        //suma total todos los descuento contenderesw y suplementos
        $total_dtos = $sum_dto + $all_dto_sup;

        //suma los dtos de los supplemntos en precios y de los contenedores
        //675

        $total_dtos_and_price_container_supplement = $tax_base -  $total_dtos_container_supplement;

        //suma los suplementos de adiconales y dentro del albaran  148
        $sum_all_supplements = $total_price_supp_aditional + $total_price_supp_alb;
        //675  suma contenedor taxe base - dto  =
        $total_dtos_and_price_container = $sum_tax_base - $sum_dto;


        $gross_total = $total_dtos_and_price_container + $sum_all_supplements;


        $taxable_base = $gross_total - $total_dtos;


        //---------------




        if ($iva === "21") {
            $cuota =  $taxable_base * 0.21;
        }
        if ($iva === "10") {
            $cuota =  $taxable_base * 0.10;
        }
        if ($iva === "4") {
            $cuota =  $taxable_base * 0.04;
        }



        $total_con_iva = $cuota +  $taxable_base;

        $cargos = $total_con_iva;
        $saldos = $total_con_iva;

        if ($retainer_amount > 0) {
            $cargos = $total_con_iva - $retainer_amount;
            $saldos = $total_con_iva - $retainer_amount;
        }



        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);

        if ($price_total_all === 0) {
            $price_total_all = "";
        }




        return $this->twig->render('Front/Bills/preBills.html.twig', [

            'array_all_containers' => $array_all_containers,
            'array_all_cubic_meters' => $array_all_cubic_meters,
            'customers' => $customers,
            'info_alb_con_ser' => $info_alb_con_ser,
            'albaran' => $albaran,
            'containers' => $containers,
            'worklocations' => $worklocations,
            'id_work_location' => $id_work_location,
            'date_today' => $date_today,
            'id_albaranes_selected' => $id_albaranes_selected,

            'total_dtos_and_price_container_supplement' => $total_dtos_and_price_container_supplement,

            'total_dtos_container_supplement' => $total_dtos_container_supplement,
            'taxable_base' => $taxable_base,


            'total_dtos' => $total_dtos,

            'gross_total' => $gross_total,


            'retainer_amount' => $retainer_amount,
            'price_total_width_iva' => $price_total_width_iva,
            'total_final' => $total_final,
            'payment_method' => $payment_method,

            'cargos' => $cargos,
            'saldos' => $saldos,
            'sum_dto' => $sum_dto,

            'price_final' => $price_final,

            'bank_count' => $bank_count,

            'sum_price_supplements_select' => $sum_price_supplements_select,
            'selected_supplements' => $selected_supplements,
            'json_supplements' => $json_supplements,
            'supplements' => $supplements,
            'supplements_obj_array' => $supplements_obj_array,

            'price_total' => $price_total,
            'price_total_dtos' => $price_total_dtos,

            'price_total_base_albaranes' => $price_total_base_albaranes,
            'iva' => $iva,
            'sum_tax_base' => $sum_tax_base,
            'total_con_iva' => $total_con_iva,
            'cuota' => $cuota,

            'price_total_all' => $price_total_all,
            'suppl_editional_existe' => $suppl_editional_existe,

            'pager' => $this->albaranesModel->pager->links()
        ]);
    }

    public function pre_view_create_supplements()
    {

        $cuota = 0;

        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $date_today = $date->format('d-m-Y');


        $id_customer = null;

        $id_work_location = null;
        $containers = null;
        $sum_price_supplements_select = 0;

        $id_customer = null;
        $id_work_location = null;
        $iva = 0;
        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        //Suplementos
        $selected_supplements = [];
        $array_supplements = [];

        $price_dto = 0;
        $price_total = 0;
        $price_total_dtos = 0;

        $anticipos = 0;
        $cargos = 0;
        $saldos = 0;

        $bank_count = 0;
        $price_total_all = 0;
        $dto_s = 0;
        $price_dto = 0;
        $payment_method_customer = null;

        $id_customer = $this->request->getPost('id_customer');

        $supplements_id = $this->request->getPost('supplements_id');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $supplement_dto = $this->request->getPost('supplement_dto');
        $retainer_amount = $this->request->getPost('retainer_amount');
        $payment_method = $this->request->getPost('payment_method');




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

        /**
         * Recorremos los objetos de los supplementos adiconales  seleccionados
         */
        $sum_price_total = 0;
        $sum_price_dto = 0;

        $supplementsObjectAditional = [];
        $supplementsObjectArrayAditional = [];
        $supplements_obj_array = $this->supplementsModel->whereIn('id_supplements', $supplements_id)->paginate(config('Configuration')->regPerPage);

            if ($supplements_obj_array !== null) {

                foreach ($supplements_obj_array as $key => $obj_supp) {

                    //15
                    $pvp_edit_total = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $price_final =  $pvp_edit[$key] - $pvp_edit_total;

                    if ($supplement_dto[$key] === 0) {
                        $price_dto = 0;
                    } else {


                        $price_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                        $sum_price_dto += $price_dto;
                    }


                    //90  + 90 +90 = 270
                    $price_total_all += $price_final;

                    $supplementsObjectAditional = new Supplements();
                    $supplementsObjectAditional->name = $obj_supp->name;
                    $supplementsObjectAditional->pvp_edit = $pvp_edit[$key];
                    $supplementsObjectAditional->dto = $supplement_dto[$key];
                    $supplementsObjectAditional->price_dto = $price_dto;
                    $supplementsObjectAditional->price_total = $price_final;

                    $supplements_obj_array[$key] = $supplementsObjectAditional;
            }
        } else {
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',
                'title' => 'ATENCION!',
                'text' => 'Revise los suplementos seleccionados.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }
            return redirect()->route('listBills');
        }

        $id_work_locations = $this->request->getPost('id_work_locations');

        $worklocations = $this->albaranesModel->where('id_work_location', $id_work_locations)->findAll();



        foreach ($worklocations as $c) {
            $id_customer = $c->id_customer;
        }
        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);


        //fecha de vencimiento para obtner la fecha de   fecha de vencimiento depende el metodo de pago  ------
        $id_customer_expiration_date = $this->customersModel->where('id_customer', $id_customer)->first();


        $name_customer_expiration_date = $this->paymentMethodModel->where('id_payment_method', $id_customer_expiration_date->payment_method)->first();
        $name_payment_method = $name_customer_expiration_date->name;



        $tipoPago = $id_customer_expiration_date->payment_method;
        $fechaActual = new DateTime();


        if ($tipoPago === '10') {
            $intervalo = new DateInterval('P10D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '11') {
            $intervalo = new DateInterval('P15D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '12') {
            $intervalo = new DateInterval('P30D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '13') {
            $intervalo = new DateInterval('P45D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '14') {
            $intervalo = new DateInterval('P60D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '15') {
            $intervalo = new DateInterval('P90D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '16') {
            $intervalo = new DateInterval('P60D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '30') {
            $intervalo = new DateInterval('P1D');
            $fechaActual->add($intervalo);
        } else {

            $fechaActual = $fechaActual;
        }

        // Formatear y mostrar la fecha resultante
        $fechaFormateada = $fechaActual->format('Y-m-d');

        foreach ($customers as $i) {
            $iva = $i->iva;
            $bank_count = $i->bank_count;
        }

        $bank_count = substr_replace($bank_count, "******", 0, 6);

        $price_total_width_iva =  $price_total *  $iva / 100;
        $total_final = $price_total + $price_total_width_iva;

        if ($anticipos > 0) {
            $total_final =  $total_final - $retainer_amount;
        }


        if ($iva === "21") {
            $cuota =  $price_total_all * 0.21;
        }
        if ($iva === "10") {
            $cuota =  $price_total_all * 0.10;
        }
        if ($iva === "4") {
            $cuota =  $price_total_all * 0.04;
        }


        $total_con_iva = $cuota + $price_total_all;

        $anticipos = $retainer_amount;
        $cargos = $total_con_iva;
        $saldos = $total_con_iva;

        $cargos = $total_con_iva;
        $saldos = $total_con_iva;

        if ($retainer_amount > 0) {
            $cargos = $total_con_iva - $retainer_amount;
            $saldos = $total_con_iva - $retainer_amount;
        }

        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);


        return $this->twig->render('Front/Bills/preBillsSupplements.html.twig', [
            'sum_price_supplements_select' => $sum_price_supplements_select,
            'customers' => $customers,
            'supplements' => $supplements,
            // 'supplemen' => $supplemen,
            'containers' => $containers,
            'worklocations' => $worklocations,
            'id_work_location' => $id_work_location,
            'date_today' => $date_today,
            'selected_supplements' => $selected_supplements,
            'price_total_width_iva' => $price_total_width_iva,
            'total_final' => $total_final,
            'payment_method' => $payment_method,

            'anticipos' => $anticipos,
            'cargos' => $cargos,
            'saldos' => $saldos,
            'cuota' => $cuota,
            'total_con_iva' => $total_con_iva,

            'sum_price_dto' => $sum_price_dto,
            'price_total_all' => $price_total_all,

            'fechaFormateada' => $fechaFormateada,
            'name_payment_method' => $name_payment_method,


            'bank_count' => $bank_count,

            'supplements_obj_array' => $supplements_obj_array,

            'price_total' => $price_total,
            'price_total_dtos' => $price_total_dtos,

            'pager' => $this->workLocationModel->pager->links()
        ]);
    }


    public function createSaveBills()
    {


        if (!$this->validate(validateCreateBills())) {
            return redirect()->back()
                ->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['Error al crear la Factura, revise los campos debajo.']
                ])
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $customer_name = NULL;
        $customer_mail = NULL;
        $customer_address = NULL;
        $customer_location = NULL;
        $customer_province = NULL;
        $customer_zip_code = NULL;
        $customer_dni = NULL;
        $customer_phone = NULL;
        $customer_iva = NULL;
        $customer_iban = NULL;
        $customer_bank = NULL;
        $customer_office_bank = NULL;
        $customer_digital_control = NULL;
        $customer_bank_count = NULL;

        $payment_method = NULL;

        $container_residue = NULL;
        $container_m3 = NULL;
        $container_price = NULL;

        $work_location_address = NULL;
        $work_location_location = NULL;
        $work_location_province = NULL;
        $work_location_zip_code = NULL;

        $driver_name = NULL;
        $driver_phone = NULL;

        $rates_name = NULL;

        $service_name = NULL;
        $service_code = NULL;

        $vehicle_name = NULL;
        $vehicle_make = NULL;
        $vehicle_model = NULL;
        $vehicle_car_registration = NULL;


        $tableBillsAlbaranes = new TableBillsAlbaranes();

        $db = db_connect();
        $date_actual = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $id_customer = null;
        $id_work_location = null;

        $id_customer = null;
        $id_work_location = null;

        $id_customer = null;
        $id_work_location = null;
        $iva = 0;
        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        $price_final = 0;

        //Suplementos

        $array_supplements = [];
        $subtotal_sum_supplements = 0;
        $price_total_base_albaranes = 0;

        $price_dto = 0;
        $price_total = 0;
        $id_alabaran = 0;
        $sum_tax_base = 0;
        $sum_dto = 0;

        //Suplementos
        $array_supplements = [];

        $total_sum_supplementos = 0;

        $price_dto = 0;
        $price_total = 0;
        $sum_total_dtos = 0;

        $price_total_all_dtos_euros = 0;
        $price_total_all_sin_dtos_base = 0;
        $amount_tax_base_discount = 0;
        $price_discount = 0;

        $total_sum_pvp_edit = 0;
        $total_sum_dto = 0;

        $price_total_supp = 0;
        $price_total_all_suppl = 0;

        $supplements = [];
        $array_albaranes = [];
        $total_price_supp_final = 0;
        $array_supplements = [];
        $id_last_bills_insert = null;
        $retainer_amount_alb = 0;
        $total_taxe_base_alb = 0;
        $total_sum_pvp_edit = 0;
        $total_sum_dto = 0;
        $price_total_all = 0;
        $total_albaranes = 0;
        $id_order = null;
        $tax_base = null;
        $subtotal = null;
        $total = null;
        $preprinted = null;
        $price_total_supp = null;

        $sum_price_supplements_select = null;

        $payment_method_alb = null;
        $supplements_exits = 0;
        $supplementsObject = null;

        $supplementsObjectArray = [];
        $sum_dto = null;
        $payment_method_customer = null;

        $retainer_amount = 0;
        $sum_all_dto_supp_alb = 0;

        $sum_pvp_edit = 0;
        $sum_container_price = 0;

        $sum_container_suplements_base = 0;
        $id_bills  = null;

        $id_num_bill = null;
        $customer_id_date_expiration = 0;

        $customer_bic = null;

        $_SESSION['idUser'];
        $idUser = session()->get('idUser');
        $name_user = session()->get('name');

        // Trae los albaranes seleccionados

        $id_albaranes_selected_post = $this->request->getPost('albaranes');




        $supplements_id = $this->request->getPost('supplements_id');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $pvp_edit_suppl_additional = $pvp_edit;
        $supplement_dto = $this->request->getPost('supplement_dto');
        $supplement_dto_aditional = $supplement_dto;
        $payment_method_selected = $this->request->getPost('payment_method');
        $payment_method = $this->request->getPost('payment_method');
        $retainer_amount_bills = $this->request->getPost('retainer_amount');
        $notes = $this->request->getPost('notas');
        $id_customer = $this->request->getPost('id_customer');
        $id_customer_selected = $this->request->getPost('id_customer');
        $c_iva = $this->request->getPost('c_iva');


        $json_supplements_aditionals = null;

        // $objBills = new Bills($this->request->getPost());
        $bills = new Bills($this->request->getPost());
        $bills->id_customer = $id_customer;

        $customer = $this->customersModel->where('id_customer', $id_customer)->first();

        $bills->date_signing_mandate = $customer->date_signing_mandate;
        $bills->recurrent_date = $customer->recurrent_date;


        $bills->iva = $c_iva;

        if (!$supplements_id) {
            $supplements_id = 0;
        }
        $myJSON = null;

        $id_albaranes_selected = $this->albaranesModel->join('containers', 'containers.id_container = albaranes.id_container')->whereIn('id_albaran', $id_albaranes_selected_post)->findAll();

        foreach ($id_albaranes_selected as $key => $obj) {


            $id_work_location = $obj->id_work_location;
            $customer_name = $obj->customer_name;
            $customer_mail = $obj->customer_mail;
            $customer_address = $obj->customer_address;
            $customer_location = $obj->customer_location;
            $customer_province = $obj->customer_province;
            $customer_zip_code = $obj->customer_zip_code;

            $customer_bic = $obj->customer_bic;

            $customer_dni = $obj->customer_dni;
            $customer_phone = $obj->customer_phone;
            $customer_iva = $obj->customer_iva;
            $customer_iban = $obj->customer_iban;
            $customer_bank = $obj->customer_bank;
            $customer_office_bank = $obj->customer_office_bank;
            $customer_digital_control = $obj->customer_digital_control;
            $customer_bank_count = $obj->customer_bank_count;

            $payment_method_alb = $obj->payment_method;

            $retainer_amount_alb = $obj->retainer_amount;


            $container_residue = $obj->container_residue;
            $container_m3 = $obj->container_m3;
            $container_price = $obj->container_price;

            $sum_container_price += $container_price;

            $work_location_address = $obj->work_location_address;
            $work_location_location = $obj->work_location_location;
            $work_location_province = $obj->work_location_province;
            $work_location_zip_code = $obj->work_location_zip_code;

            $driver_name = $obj->driver_name;
            $driver_phone = $obj->driver_phone;

            $rates_name = $obj->rates_name;

            $service_name = $obj->service_name;
            $service_code = $obj->service_code;

            $vehicle_name = $obj->vehicle_name;
            $vehicle_make = $obj->vehicle_make;
            $vehicle_model = $obj->vehicle_model;
            $vehicle_car_registration = $obj->vehicle_car_registration;

            $preprinted = $obj->preprinted;

            $price_total_supp = $obj->price_total_supp;


            //para actualziar pedido
            $id_order = $obj->id_order;

            $subtotal = $obj->subtotal;

            $total_taxe_base_alb += $obj->tax_base;
            $tax_base = $obj->tax_base;
            $discount = $obj->discount;
            $price_discount = $obj->price_discount;
            $amount_tax_base_discount = $obj->amount_tax_base_discount;

            //total de los supllementos
            $subtotal_sum_supplements += $obj->subtotal_sum_supplements;

            //Suma total con descuentos de los albaranes
            $price_total_base_albaranes += $amount_tax_base_discount;
            $sum_tax_base += $tax_base;
            $sum_dto += $price_discount;

            $albaranes = new Albaranes();
            $albaranes->id_albaran = $obj->id_albaran;

            $total_albaranes += $obj->total;

            //  $albaranes->supplements =  $item->supplements;

            $array_albaranes[] = $albaranes;
            $json_albaranes = json_encode($array_albaranes);
            /**
             * Decofificamos y guarsamos los servicios guardados de albaranes en una variable
             */


            $supplements_alb = $obj->supplements;


            if ($supplements_alb) {
                $supplements = json_decode($obj->supplements);

                foreach ($supplements as $x2 => $s) {

                    $id_supplements = $s->id_supplements;
                    $pvp_edit = $s->pvp_edit;

                    $sum_pvp_edit += $s->pvp_edit;

                    $price_dto = $s->price_dto;
                    $total_price_supp = $s->price_total;

                    $sum_all_dto_supp_alb += $price_dto;

                    $total_price_supp_final += $total_price_supp;

                    $supplementsObject = new stdClass();
                    $supplementsObject->name = $s->name;
                    $supplementsObject->pvp_edit = $s->pvp_edit;
                    $supplementsObject->price_dto = $s->price_dto;
                    $supplementsObject->dto = $s->dto;
                    $supplementsObject->total_price_supp = $total_price_supp;

                    $supplementsObjectArray[$x2] = $supplementsObject;
                }

                $supplements_exits = 1;

                $id_albaranes_selected[$key]->supplementsObject = $supplementsObjectArray;
            }


            $prices = new stdClass();

            $prices->tax_base = $tax_base;
            $prices->discount = $discount;
            $prices->price_discount = $price_discount;
            $prices->amount_tax_base_discount = $amount_tax_base_discount;

            $pricesObjectArray[$key] = $prices;
            $id_albaranes_selected[$key]->prices = $pricesObjectArray;
        }



        if ($supplements_id) {

            $supplements_exits = 1;

            for ($i = 0; $i < count($pvp_edit_suppl_additional); $i++) {
                if ($pvp_edit_suppl_additional[$i] === null || $pvp_edit_suppl_additional[$i] === "0.00" || $pvp_edit_suppl_additional[$i] === "" || $pvp_edit_suppl_additional[$i] === "0") {
                    $pvp_edit_suppl_additional = 0;
                    $pvp_edit_suppl_additional[$i] = $pvp_edit_suppl_additional;
                }
            }

            for ($i = 0; $i < count($supplement_dto_aditional); $i++) {
                if ($supplement_dto_aditional[$i] === null || $supplement_dto_aditional[$i] === "0.00" || $supplement_dto_aditional[$i] === "" || $supplement_dto_aditional[$i] === "0") {
                    $dto = 0;
                    $supplement_dto_aditional[$i] = $dto;
                }
            }

            for ($i = 0; $i < count($supplements_id); $i++) {
                $supplemen_id_obj = new Supplements();
                $supplemen_id_obj->id_supplements = $supplements_id[$i];
                $supplemen_id_obj->pvp_edit = $pvp_edit_suppl_additional[$i];
                $supplemen_id_obj->dto = $supplement_dto_aditional[$i];

                //ejemplo 10 10 10 = 30
                $price_total_all_dtos_euros += $supplement_dto_aditional[$i];

                //ejemplo 100 100 100 = 300
                $price_total_all_sin_dtos_base += $pvp_edit_suppl_additional[$i];

                //porcentaje de descuento de cada suplemento en euros eje 10 €
                //100 * 10% = 10E
                $price_dto = $pvp_edit_suppl_additional[$i] * $supplement_dto_aditional[$i] / 100;

                //Sumamos el total de esos valores de dtos 10 + 10 +15 = 45
                $sum_total_dtos += $price_dto;

                //Precio total base menos dtos 270
                $price_total_width_dto = $pvp_edit_suppl_additional[$i] - $price_dto;

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

                $total_sum_pvp_edit += $pvp_edit_suppl_additional[$key];
                $total_sum_dto += $pvp_edit_suppl_additional[$key] * $supplement_dto_aditional[$key] / 100;

                /**
                 * Sacamos el price_total (PVP - Dto)
                 */
                $spl_pvp_edit = 0;
                $spl_dto = 0;

                $spl_pvp_edit = $pvp_edit_suppl_additional[$key];
                $spl_dto = $pvp_edit_suppl_additional[$key] * $supplement_dto_aditional[$key] / 100;

                $supplemen->pvp = $pvp_edit_suppl_additional[$key];
                $supplemen->pvp_edit = $pvp_edit_suppl_additional[$key];
                $supplemen->dto = $supplement_dto_aditional[$key];
                $supplemen->price_dto = $pvp_edit_suppl_additional[$key] * $supplement_dto_aditional[$key] / 100;

                $price_total_supp = $spl_pvp_edit - $spl_dto; //90
                $supplemen->price_total =  $price_total_supp;

                //Se utiliza estas variable para guardar en la tabla de albaranes
                $price_total_all_suppl += $price_total_supp; //270




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



        foreach ($id_albaranes_selected as $rows1) {
            $id_alabaran = $rows1->id_albaran;
        }

        $albaran_3 = $this->albaranesModel->where('id_albaran', $id_alabaran)->paginate(config('Configuration')->regPerPage);
        foreach ($albaran_3 as $rows) {
            $id_customer = $rows->id_customer;
            $id_work_location = $rows->id_work_location;
        }


        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        foreach ($customers as $i) {
            $iva = $i->iva;
            $c_iva = $i->iva;
        }


        /**
         * Suplementos adicionales
         */

        if ($supplements_id) {

            $supplements_exits = 1;

            $supplements_obj_array = $this->supplementsModel->whereIn('id_supplements', $supplements_id)->paginate(config('Configuration')->regPerPage);

            foreach ($supplements_obj_array as $key => $obj_supp) {

                $pvp_edit_total = 0;

                if ($supplement_dto[$key] !== 0 || $supplement_dto[$key] !== null) {
                    $pvp_edit_total = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $price_final =  $pvp_edit[$key] - $pvp_edit_total;
                } else {
                    $pvp_edit_total = $pvp_edit[$key];
                    $price_final =  $pvp_edit[$key];
                }

                if ($supplement_dto[$key] === 0 || $supplement_dto[$key] === null) {

                    $dto_s = 0;
                    $price_dto = 0;
                } else {
                    $price_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $dto_s = $supplement_dto[$key];
                }


                //90  + 90 +90 = 270
                $price_total_all += $price_final;

                $id_supplements = $obj_supp->id_supplements;

                $supplementsObjectArrayAditional = [];


                $supplementsObjectAditional = new Supplements();
                $supplementsObjectAditional->name = $obj_supp->name;
                $supplementsObjectAditional->pvp_edit = $pvp_edit[$key];
                $supplementsObjectAditional->dto = $dto_s;
                $supplementsObjectAditional->price_dto = $price_dto;
                $supplementsObjectAditional->price_total = $price_final;
                $supplementsObjectAditional->price_total_all_supp = $price_total_all;

                $supplementsObjectArrayAditional[$key] = $supplementsObjectAditional;
                $json_supplements_aditionals = json_encode($supplementsObjectArrayAditional);

                $supplements_obj_array[$key]->supplementsObjectAditional = $supplementsObjectArrayAditional;
            }
        } else {
        }

        if (!$retainer_amount_bills) {
            if (!$retainer_amount_alb) {
                $retainer_amount = 0;
            } else {
                $retainer_amount = $retainer_amount_alb;
            }
        } else {
            $retainer_amount = $retainer_amount_bills;
        }




        $sum_container_suplements_base = $sum_container_price + $sum_pvp_edit;
        //suma los dtos contenedores y suplemtnos

        //330


        $total_dtos_container_supplement = $sum_all_dto_supp_alb + $sum_dto;

        $total_dtos_and_price_container_supplement = $sum_container_suplements_base - $total_dtos_container_supplement;


        if ($iva === "21") {
            $cuota =  $total_dtos_and_price_container_supplement * 0.21;
        }
        if ($iva === "10") {
            $cuota =  $total_dtos_and_price_container_supplement * 0.10;
        }
        if ($iva === "4") {
            $cuota =  $total_dtos_and_price_container_supplement * 0.04;
        }


        $total_con_iva = $cuota + $total_dtos_and_price_container_supplement;


        if ($retainer_amount > 0) {
            $balance = $total_con_iva - $retainer_amount;
        } else {
            $balance = $total_con_iva;
        }

        $total = $total_con_iva;
        $charge = $total_con_iva;


        //Total Bruto
        $gross_total = $sum_container_price + $sum_pvp_edit;

        $taxable_base = $gross_total - $total_dtos_container_supplement;


        /*--*/

        $balance = $total_con_iva;
        $retainer_amount = floatval($retainer_amount);

        $charge = $total_con_iva - $retainer_amount;
        $balance = $total_con_iva - $retainer_amount;



        if ($supplements_id) {

            $bills->json_supplements_aditional = $json_supplements_aditionals;
        } else {
            $bills->json_supplements_aditional = null;
        }


        $asteriscos = str_repeat('*', 2); // crear una cadena de 2 asteriscos
        $customer_bank_con_asteriscos = substr_replace($customer_bank, $asteriscos, 0, 2); // reemplazar los primeros 2 números con asteriscos

        $state = "Finalizada";



        //para obtner la fecha de   fecha de vencimiento depende el metodo de pago  ------

        $id_customer_expiration_date = $this->customersModel
            ->select('payment_method.id_payment_method')
            ->join('payment_method', 'payment_method.id_payment_method = customers.payment_method', 'inner')
            ->where('customers.id_customer', $id_customer)
            ->find();


        foreach ($id_customer_expiration_date as $key => $rows) {
            $payment_method_customer =  $rows->id_payment_method;
        }

        // -----  fecha de vencimiento -----   ///
        $tipoPago = $payment_method_customer;

        $fechaActual = new DateTime();

        if ($tipoPago === '10') {

            $intervalo = new DateInterval('P10D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '11') {
            $intervalo = new DateInterval('P15D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '12') {
            $intervalo = new DateInterval('P30D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '13') {
            $intervalo = new DateInterval('P45D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '14') {
            $intervalo = new DateInterval('P60D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '15') {
            $intervalo = new DateInterval('P90D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '16') {
            $intervalo = new DateInterval('P60D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '30') {
            $intervalo = new DateInterval('P1D');
            $fechaActual->add($intervalo);
        } else {

            $fechaActual = $fechaActual;
        }


        // Formatear y mostrar la fecha resultante
        $fechaFormateada = $fechaActual->format('Y-m-d');
        $bills->expiration_date = $fechaFormateada;


        $bills->id_user = $idUser;
        $bills->name_user = $name_user;

        $bills->id_order = $id_order;
        $bills->json_albaranes = $json_albaranes;
        $bills->id_work_locations = $id_work_location;
        $bills->id_customer = $id_customer_selected;
        $bills->customer_name = $customer_name;
        $bills->customer_mail = $customer_mail;
        $bills->customer_address = $customer_address;
        $bills->customer_location = $customer_location;
        $bills->customer_zip_code = $customer_zip_code;
        $bills->customer_dni = $customer_dni;
        $bills->customer_phone = $customer_phone;
        $bills->customer_iva = $customer_iva;

        $bills->customer_bic = $customer_bic;


        $bills->customer_iban = $customer_iban;

        $bills->customer_bank = $customer_bank;
        $bills->customer_office_bank = $customer_office_bank;
        $bills->customer_digital_control = $customer_digital_control;
        $bills->customer_bank_count = $customer_bank_count;


        if ($payment_method === "null") {
            $bills->payment_method = $payment_method_alb;
        } else {
            $bills->payment_method = $payment_method_selected;
        }


        $bills->work_location_address = $work_location_address;
        $bills->work_location_location = $work_location_location;
        $bills->work_location_province = $work_location_province;
        $bills->work_location_zip_code = $work_location_zip_code;

        $bills->rates_name = $rates_name;
        $bills->service_name = $service_name;
        $bills->service_code = $service_code;

        $bills->fee = $cuota;

        $bills->iva = $c_iva;
        $bills->subtotal = $subtotal;
        $bills->sum_dto = $total_dtos_container_supplement;

        $bills->gross_total = $gross_total;
        $bills->taxable_base = $taxable_base;

        $bills->total = $total;
        $bills->subtotal_sum_supplements = $subtotal_sum_supplements;
        $bills->price_total_supp = $price_total_supp;

        $bills->total_bills = $total;
        $bills->advance = $retainer_amount;
        $bills->retainer_amount = $retainer_amount;
        $bills->charge = $charge;
        $bills->balance = $balance;

        $bills->notes = $notes;
        $bills->state = $state;
        $bills->active = 1;
        $bills->bills_supplements = 0;

        //Consultar ulitmo id
        // $id_num_bill = $this->lastBillsModel->getIdLastBills();


        $year = date('Y'); // Obtener el año actual
        $year = (int)$year;

        //Obtenemos el registro de la tabla last bill con el año vigente
        $lastBill = $this->lastBillsModel->where('num_year', $year)->first();


        if (!$lastBill) {

            $lastBill = new LastBills();
            $lastBill->num_bill = 0;
            $lastBill->num_year = $year;

            // $this->lastBillsModel->save($lastBill);

        }

        $lastBill->num_bill = $lastBill->num_bill + 1;
        try {

        $bills->year = $lastBill->num_year;
        $bills->num_bill = $lastBill->num_bill;
        $bills->words_num_bill = "M";

        $this->billsModel->save($bills);
        $id_bills = $db->insertID($bills);
        $id_last_bills_insert = $id_bills;


        $this->lastBillsModel->save($lastBill);



        /**
         * Guradamos en la tabla de facturas y Albaranes
         */
        foreach ($id_albaranes_selected as $key => $obj_albaranes) {

            $id_customer = $obj_albaranes->id_customer;
            $id_alb = $obj_albaranes->id_albaran;
            $id_container = $obj_albaranes->id_container;
            $id_order = $obj_albaranes->id_order;
            $id_rates = $obj_albaranes->id_rates;
            $id_driver = $obj_albaranes->id_driver;
            $id_vehicle = $obj_albaranes->id_vehicle;
            $value_iva = $obj_albaranes->value_iva;
            $id_rates = $obj_albaranes->id_rates;
            $id_driver = $obj_albaranes->id_driver;
            $id_vehicle = $obj_albaranes->id_vehicle;
            $tax_base_original = $obj_albaranes->tax_base_original;

            $id_service = $obj_albaranes->id_service;

            $customer_name = $obj_albaranes->customer_name;
            $customer_mail = $obj_albaranes->customer_mail;
            $customer_address = $obj_albaranes->customer_address;
            $customer_location = $obj_albaranes->customer_location;
            $customer_province = $obj_albaranes->customer_province;
            $customer_zip_code = $obj_albaranes->customer_zip_code;
            $customer_dni = $obj_albaranes->customer_dni;
            $customer_phone = $obj_albaranes->customer_phone;

            $customer_bic = $obj_albaranes->customer_bic;

            $customer_iva = $obj_albaranes->customer_iva;
            $customer_bank = $obj_albaranes->customer_bank;
            $customer_office_bank = $obj_albaranes->customer_office_bank;
            $customer_digital_control = $obj_albaranes->customer_digital_control;
            $customer_bank_count = $obj_albaranes->customer_bank_count;
            $payment_method = $obj_albaranes->payment_method;
            $customer_bank = $obj_albaranes->customer_bank;
            $customer_office_bank = $obj_albaranes->customer_office_bank;
            $container_residue = $obj_albaranes->container_residue;
            $container_m3 = $obj_albaranes->container_m3;
            $container_price = $obj_albaranes->container_price;
            $work_location_address = $obj_albaranes->work_location_address;
            $work_location_location = $obj_albaranes->work_location_location;
            $work_location_province = $obj_albaranes->work_location_province;
            $work_location_zip_code = $obj_albaranes->work_location_zip_code;
            $driver_name = $obj_albaranes->driver_name;
            $driver_phone = $obj_albaranes->driver_phone;
            $rates_name = $obj_albaranes->rates_name;
            $service_name = $obj_albaranes->service_name;
            $service_code = $obj_albaranes->service_code;
            $vehicle_name = $obj_albaranes->vehicle_name;
            $vehicle_make = $obj_albaranes->vehicle_make;
            $vehicle_model = $obj_albaranes->vehicle_model;
            $vehicle_car_registration = $obj_albaranes->vehicle_car_registration;
            $id_order = $obj_albaranes->id_order;

            $id_work_location = $obj_albaranes->id_work_location;
            $id_rates = $obj_albaranes->id_rates;
            $id_driver = $obj_albaranes->id_driver;
            $id_vehicle = $obj_albaranes->id_vehicle;
            $discount = $obj_albaranes->discount;
            $price_discount = $obj_albaranes->price_discount;
            $amount_tax_base_discount = $obj_albaranes->amount_tax_base_discount;
            // $retainer_amount = $obj_albaranes->retainer_amount;
            $iva = $obj_albaranes->iva;
            $subtotal = $obj_albaranes->subtotal;
            $total = $obj_albaranes->total;
            $tax_base = $obj_albaranes->tax_base;

            $supplements = $obj_albaranes->supplements;


            if ($supplements) {
                $supplements_exits = 1;
                $tableBillsAlbaranes->supplements_exits = $supplements_exits;
            } else {
                $supplements_exits = 0;
                $tableBillsAlbaranes->supplements_exits = $supplements_exits;
            }


            $tableBillsAlbaranes->preprinted = $preprinted;
            $tableBillsAlbaranes->id_service = $id_service;

            $tableBillsAlbaranes->id_bills = $id_bills;
            $tableBillsAlbaranes->id_albaran = $obj_albaranes->id_albaran;
            $tableBillsAlbaranes->customer_name = $customer_name;
            $tableBillsAlbaranes->customer_mail = $customer_mail;
            $tableBillsAlbaranes->customer_address = $customer_address;
            $tableBillsAlbaranes->customer_location = $customer_location;
            $tableBillsAlbaranes->customer_province = $customer_province;
            $tableBillsAlbaranes->customer_zip_code = $customer_zip_code;
            $tableBillsAlbaranes->customer_dni = $customer_dni;
            $tableBillsAlbaranes->customer_phone = $customer_phone;
            $tableBillsAlbaranes->customer_iva = $c_iva;
            $tableBillsAlbaranes->customer_iban = $customer_iban;
            $tableBillsAlbaranes->customer_bank = $customer_bank;
            $tableBillsAlbaranes->customer_office_bank = $customer_office_bank;
            $tableBillsAlbaranes->customer_digital_control = $customer_digital_control;
            $tableBillsAlbaranes->customer_bank_count = $customer_bank_count;
            $tableBillsAlbaranes->payment_method = $payment_method;
            $tableBillsAlbaranes->container_residue = $container_residue;
            $tableBillsAlbaranes->container_m3 = $container_m3;
            $tableBillsAlbaranes->container_price = $container_price;
            $tableBillsAlbaranes->work_location_address = $work_location_address;
            $tableBillsAlbaranes->work_location_location = $work_location_location;
            $tableBillsAlbaranes->work_location_province = $work_location_province;
            $tableBillsAlbaranes->work_location_zip_code = $work_location_zip_code;
            $tableBillsAlbaranes->driver_name = $driver_name;
            $tableBillsAlbaranes->driver_phone = $driver_phone;
            $tableBillsAlbaranes->rates_name = $rates_name;
            $tableBillsAlbaranes->service_name = $service_name;
            $tableBillsAlbaranes->service_code = $service_code;
            $tableBillsAlbaranes->vehicle_name = $vehicle_name;
            $tableBillsAlbaranes->vehicle_make = $vehicle_make;
            $tableBillsAlbaranes->vehicle_model = $vehicle_model;
            $tableBillsAlbaranes->vehicle_car_registration = $vehicle_car_registration;
            $tableBillsAlbaranes->rates_name = $rates_name;
            $tableBillsAlbaranes->id_order = $id_order;

            $tableBillsAlbaranes->id_customer = $id_customer_selected;
            $tableBillsAlbaranes->id_work_location = $id_work_location;
            $tableBillsAlbaranes->id_container = $id_container;
            $tableBillsAlbaranes->id_rates = $id_rates;
            $tableBillsAlbaranes->id_driver = $id_driver;
            $tableBillsAlbaranes->id_vehicle = $id_vehicle;
            $tableBillsAlbaranes->discount = $discount;
            $tableBillsAlbaranes->price_discount = $price_discount;
            $tableBillsAlbaranes->amount_tax_base_discount = $amount_tax_base_discount;
            $tableBillsAlbaranes->retainer_amount = $retainer_amount;

            $tableBillsAlbaranes->notes = $notes;

            $tableBillsAlbaranes->iva = $iva;
            $tableBillsAlbaranes->subtotal = $subtotal;
            $tableBillsAlbaranes->total = $total_con_iva;
            $tableBillsAlbaranes->tax_base = $tax_base;


            // $tableBillsAlbaranes->supplementsObject = $supplementsObjectArray;

            //Suplementos
            if ($supplements_id) {
                $tableBillsAlbaranes->supplements = $myJSON;
            } else {
                $tableBillsAlbaranes->supplements = $supplements;
            }


            //suma de los desceuntos en precios euros ejemplo 10 + 10 +10 € ( 10%) = 30€
            $tableBillsAlbaranes->price_total_supp = $price_total_supp;
            //Suma de total de los suplementos 45
            $tableBillsAlbaranes->subtotal_sum_supplements = $sum_price_supplements_select;


            //Guardamos en la tabla Tabla Bills Albaranes
            $tableBillsAlbaranes->active = 1;
            $this->tableBillsAlbaranesModel->save($tableBillsAlbaranes);


            $albaran_status = "Facturado";

            //Obtenemos los datos del formulario por cada campo
            $data = [
                // 'active'  => 2,
                'updated_at' => $date_actual->format('Y-m-d'),
                'albaran_status' => $albaran_status,

            ];
            $builder = $db->table('albaranes');
            $builder->getWhere(['id_albaran' => $id_alb]);
            $builder->set(
                'albaran_status',
                'updated_at'
            );
            $builder->where('id_albaran', $id_alb);
            $builder->update($data);


            /**
             * Actualizamos el estado del pedido
             */
            $state = "Facturado";
            $data2 = [
                'state' => $state,
                'updated_at' => $date_actual->format('Y-m-d'),
            ];
            $builder = $db->table('orders');
            $builder->getWhere(['id_order' => $id_order]);
            $builder->set(
                'state',
                'updated_at'

            );
            $builder->where('id_order', $id_order);
            $builder->update($data2);
        }


        $previousMsg = $this->session->getFlashdata('msg');
        $currentMsg = [
            'type' => 'error',
            'title' => 'FACTURA CREADA!',
            'text' => 'Factura creada con exito.',
        ];
        if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
            $this->session->setFlashdata('msg', $currentMsg);
        }
        return redirect()->route('listBills');

        } catch (\Throwable $th) {

            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',
                'title' => 'ATENCION!',
                'text' => 'Error al crear la Factura.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }
            return redirect()->route('listBills');

        }



    }

    public function createBillsAutomatic()
    {

        $_SESSION['idUser'];
        $idUser = session()->get('idUser');
        $name_user = session()->get('name');

        $tableBillsAlbaranes = new TableBillsAlbaranes();
        $db = db_connect();

        $id_customer  = 0;
        $iva = 0;
        $supplements = [];
        $amount_tax_base_discount = 0;
        $supplements = [];
        $id_order = 0;
        $total = 0;
        $retainer_amount = 0;
        $supplements_exits = 0;
        $c_iva = null;
        $albaran_status = null;
        $payment_method_customer  = "";
        $id_customer_date_mandate = null;

        $id_albaranes_selected_post = $this->request->getPost('albaranes');
        $id_albaranes_selected = $this->albaranesModel->whereIn('id_albaran', $id_albaranes_selected_post)->findAll();

        try {

            $albaranesGroupedAddress = [];
            foreach ($id_albaranes_selected as $key => $obj) {

                if (!array_key_exists($obj->id_work_location, $albaranesGroupedAddress)) {
                    $albaranesGroupedAddress[$obj->id_work_location] = [];
                }
                $albaranesGroupedAddress[$obj->id_work_location][] = $obj;
            }

            foreach ($albaranesGroupedAddress as $key => $arrayDeAlbaranesDeDireccionActual) {

                $bills = new Bills($this->request->getPost());
                $bills->id_work_locations = $key;
                $cuota = 0;
                $amount_tax_base_discount = 0;
                $amount_tax_base_discount = 0;
                $sum_dtos = 0;
                $supplements = null;

                $total = 0;
                $total_con_iva = 0;
                $charge = 0;
                $total_con_iva = 0;
                $gross_total = 0;
                $taxable_base = 0;
                $gross_total = 0;
                $taxable_base = 0;
                $total = 0;
                $dtos_supplements = 0;
                $charge = 0;
                $charge = 0;
                $balance = 0;

                $dtos_containers = 0;
                $json_supplements = null;
                $albaran = null;
                $pvp_suplemments = 0;

                $sum_container_price = 0;
                $sum_retainer_amount = 0;

                $albaran_status = "Facturado";

                foreach ($arrayDeAlbaranesDeDireccionActual as $key2 => $object_array) {



                    $id_customer = $object_array->id_customer;
                    $id_customer_expiration_date = $this->customersModel->where('id_customer', $id_customer)->find();

                    foreach ($id_customer_expiration_date as $key => $rows) {
                        $payment_method_customer =  $rows->payment_method;
                    }
                    $name_customer_expiration_date = $this->paymentMethodModel->where('id_payment_method', $payment_method_customer)->first();
                    //-------------



                    $state = "Finalizada";

                    $bills->id_user = $idUser;
                    $bills->name_user = $name_user;
                    $bills->id_customer = $object_array->id_customer;

                    $id_customer_date_mandate = $bills->id_customer;

                    $bills->customer_name = $object_array->customer_name;
                    $bills->customer_mail = $object_array->customer_mail;
                    $bills->customer_address = $object_array->customer_address;
                    $bills->customer_location = $object_array->customer_location;
                    $bills->customer_province = $object_array->customer_province;
                    $bills->customer_zip_code = $object_array->customer_zip_code;
                    $bills->customer_dni = $object_array->customer_dni;
                    $bills->customer_phone = $object_array->customer_phone;
                    $bills->customer_iva = $object_array->customer_iva;
                    $bills->customer_iban = $object_array->customer_iban;
                    $bills->customer_bank = $object_array->customer_bank;
                    $bills->customer_office_bank = $object_array->customer_office_bank;
                    $bills->customer_digital_control = $object_array->customer_digital_control;
                    $bills->customer_bank_count = $object_array->customer_bank_count;


                    $bills->payment_method = $object_array->payment_method;

                    $bills->customer_bic = $object_array->customer_bic;


                    $bills->billable = $object_array->billable;

                    $bills->work_location_address = $object_array->work_location_address;
                    $bills->work_location_location = $object_array->work_location_location;
                    $bills->work_location_province = $object_array->work_location_province;
                    $bills->work_location_zip_code = $object_array->work_location_zip_code;

                    $bills->rates_name = $object_array->rates_name;
                    $bills->service_name = $object_array->service_name;
                    $bills->service_code = $object_array->service_code;

                    $bills->id_order = $object_array->id_order;

                    $retainer_amount = $object_array->retainer_amount;
                    $retainer_amount = (float)$retainer_amount;

                    $sum_retainer_amount += $retainer_amount;

                    $container_price = $object_array->container_price;
                    $container_price = (float)$container_price;

                    $sum_container_price += $container_price;

                    //Container con dto si lo hubiera
                    $amount_tax_base_discount = $object_array->amount_tax_base_discount;
                    $amount_tax_base_discount = (float)$amount_tax_base_discount;


                    //Container dtos   150
                    $dtos_containers += $object_array->price_discount;

                    //Supplements precios base
                    if ($object_array->supplements !== null) {

                        $albaran = $this->albaranesModel->where('id_albaran', $object_array->id_albaran)->first();
                        $json_supplements = json_decode($albaran->supplements);

                        foreach ($json_supplements as $x) {
                            $pvp_suplemments += $x->pvp_edit;
                            $dtos_supplements += $x->price_dto;
                        }
                    }

                    //Supplements  90 dtos
                    $sum_dtos = $dtos_containers + $dtos_supplements;

                    //Suma total con  de los albaranes
                    $gross_total = $sum_container_price + $pvp_suplemments;
                    $taxable_base = $gross_total - $sum_dtos;

                    $customers = $this->customersModel->where('id_customer',  $object_array->id_customer)->paginate(config('Configuration')->regPerPage);
                    foreach ($customers as $i) {
                        $iva = $i->iva;
                        $c_iva = $i->iva;
                    }

                    $cuota =  $taxable_base * ((int)$iva / 100);
                    $total_con_iva = $cuota + $taxable_base;

                    if ($sum_retainer_amount > 0) {
                        $balance = $total_con_iva - $sum_retainer_amount;
                    } else {
                        $balance = $total_con_iva;
                    }

                    $total = $total_con_iva;
                    $charge = $total_con_iva;

                    $bills->sum_dto = $sum_dtos;
                    $bills->iva = $c_iva;
                    $bills->gross_total = $gross_total;
                    $bills->taxable_base = $taxable_base;
                    $bills->total = $total;

                    $bills->total_bills = $charge;
                    $bills->charge = $charge;
                    $bills->balance = $balance;

                    $bills->state = $state;
                    $bills->active = 1;
                    $bills->bills_supplements = 0;
                    $bills->fee = $cuota;

                    $bills->retainer_amount = $sum_retainer_amount;


                    // -----  fecha de vencimiento -----   ///
                    $tipoPago = $name_customer_expiration_date->id_payment_method;



                    $fechaActual = new DateTime();


                    if ($tipoPago === '10') {
                        $intervalo = new DateInterval('P10D');
                        $fechaActual->add($intervalo);
                    } elseif ($tipoPago === '11') {
                        $intervalo = new DateInterval('P15D');
                        $fechaActual->add($intervalo);
                    } elseif ($tipoPago === '12') {
                        $intervalo = new DateInterval('P30D');
                        $fechaActual->add($intervalo);
                    } elseif ($tipoPago === '13') {
                        $intervalo = new DateInterval('P45D');
                        $fechaActual->add($intervalo);
                    } elseif ($tipoPago === '14') {
                        $intervalo = new DateInterval('P60D');
                        $fechaActual->add($intervalo);
                    } elseif ($tipoPago === '15') {
                        $intervalo = new DateInterval('P90D');
                        $fechaActual->add($intervalo);
                    } elseif ($tipoPago === '16') {
                        $intervalo = new DateInterval('P60D');
                        $fechaActual->add($intervalo);
                    } elseif ($tipoPago === '30') {
                        $intervalo = new DateInterval('P1D');
                        $fechaActual->add($intervalo);
                    } else {

                        $fechaActual = $fechaActual;
                    }

                    // Formatear y mostrar la fecha resultante
                    $fechaFormateada = $fechaActual->format('Y-m-d');
                }

                //----------//

                $year = date('Y'); // Obtener el año actual
                $year = (int)$year;

                //Obtenemos el registro de la tabla last bill con el año vigente
                $lastBill = $this->lastBillsModel->where('num_year', $year)->first();


                if (!$lastBill) {

                    $lastBill = new LastBills();
                    $lastBill->num_bill = 0;
                    $lastBill->num_year = $year;

                    // $this->lastBillsModel->save($lastBill);

                }

                $lastBill->num_bill = $lastBill->num_bill + 1;


                $bills->year = $lastBill->num_year;
                $bills->num_bill = $lastBill->num_bill;
                $bills->words_num_bill = "A";

                //fecha de vencimiento
                $bills->expiration_date = $fechaFormateada;

                $customer = $this->customersModel->where('id_customer', $id_customer_date_mandate)->first();

                $bills->date_signing_mandate = $customer->date_signing_mandate;

                $bills->recurrent_date = $customer->recurrent_date;

                $this->billsModel->save($bills);
                $id_bills = $db->insertID($bills);


                $this->lastBillsModel->save($lastBill);

                $tableBillsAlbaranes = [];
                $tableBillsAlbaran = new TableBillsAlbaranes();

                $tableBillsAlbaranes[] = $tableBillsAlbaran;

                foreach ($arrayDeAlbaranesDeDireccionActual as $albaran) {

                    $tableBillsAlbaran->id_albaran = $albaran->id_albaran;
                    $tableBillsAlbaran->id_bills = $id_bills;
                    $tableBillsAlbaran->id_customer = $albaran->id_customer;
                    $tableBillsAlbaran->id_container = $albaran->id_container;
                    $tableBillsAlbaran->id_order = $albaran->id_order;
                    $tableBillsAlbaran->id_rates = $albaran->id_rates;
                    $tableBillsAlbaran->id_driver = $albaran->id_driver;
                    $tableBillsAlbaran->id_vehicle = $albaran->id_vehicle;
                    $tableBillsAlbaran->id_service = $albaran->id_service;
                    $tableBillsAlbaran->id_work_location = $albaran->id_work_location;

                    $tableBillsAlbaran->customer_name = $albaran->customer_name;
                    $tableBillsAlbaran->customer_mail = $albaran->customer_mail;
                    $tableBillsAlbaran->customer_address = $albaran->customer_address;
                    $tableBillsAlbaran->customer_location = $albaran->customer_location;
                    $tableBillsAlbaran->customer_province = $albaran->customer_province;
                    $tableBillsAlbaran->customer_zip_code = $albaran->customer_zip_code;
                    $tableBillsAlbaran->customer_dni = $albaran->customer_dni;
                    $tableBillsAlbaran->customer_phone = $albaran->customer_phone;
                    $tableBillsAlbaran->customer_iva = $c_iva;
                    $tableBillsAlbaran->customer_iban = $albaran->customer_iban;
                    $tableBillsAlbaran->customer_bank = $albaran->customer_bank;
                    $tableBillsAlbaran->customer_office_bank = $albaran->customer_office_bank;
                    $tableBillsAlbaran->customer_digital_control = $albaran->customer_digital_control;
                    $tableBillsAlbaran->customer_bank_count = $albaran->customer_bank_count;

                    $tableBillsAlbaran->container_m3 = $albaran->container_m3;
                    $tableBillsAlbaran->container_residue = $albaran->container_residue;
                    $tableBillsAlbaran->container_price = $albaran->container_price;

                    $tableBillsAlbaran->payment_method = $albaran->payment_method;

                    $tableBillsAlbaran->work_location_address = $albaran->work_location_address;
                    $tableBillsAlbaran->work_location_location = $albaran->work_location_location;
                    $tableBillsAlbaran->work_location_province = $albaran->work_location_province;
                    $tableBillsAlbaran->work_location_zip_code = $albaran->work_location_zip_code;

                    $tableBillsAlbaran->driver_name = $albaran->driver_name;
                    $tableBillsAlbaran->driver_phone = $albaran->driver_phone;
                    $tableBillsAlbaran->rates_name = $albaran->rates_name;
                    $tableBillsAlbaran->service_name = $albaran->service_name;
                    $tableBillsAlbaran->service_code = $albaran->service_code;

                    $tableBillsAlbaran->vehicle_name = $albaran->vehicle_name;
                    $tableBillsAlbaran->vehicle_make = $albaran->vehicle_make;
                    $tableBillsAlbaran->vehicle_model = $albaran->vehicle_model;
                    $tableBillsAlbaran->vehicle_car_registration = $albaran->vehicle_car_registration;

                    $tableBillsAlbaran->discount = $albaran->discount;
                    $tableBillsAlbaran->price_discount = $albaran->price_discount;
                    $tableBillsAlbaran->amount_tax_base_discount = $albaran->amount_tax_base_discount;
                    $tableBillsAlbaran->retainer_amount = $albaran->retainer_amount;
                    $tableBillsAlbaran->iva = $albaran->iva;

                    $tableBillsAlbaran->subtotal = $albaran->subtotal;
                    $tableBillsAlbaran->total = $albaran->total;
                    $tableBillsAlbaran->tax_base = $albaran->tax_base;
                    $tableBillsAlbaran->preprinted = $albaran->preprinted;
                    $tableBillsAlbaran->total_con_iva = $albaran->total_con_iva;

                    $tableBillsAlbaran->supplements = $albaran->supplements;
                    $supplements = $albaran->supplements;

                    if ($supplements) {
                        $supplements_exits = 1;
                        $tableBillsAlbaran->supplements_exits = $supplements_exits;
                    } else {
                        $supplements_exits = 0;
                        $tableBillsAlbaran->supplements_exits = $supplements_exits;
                    }


                    $tableBillsAlbaran->price_total_supp = $albaran->price_total_supp;
                    $tableBillsAlbaran->subtotal_sum_supplements = $albaran->sum_price_supplements_select;
                    $tableBillsAlbaran->active = 1;
                    $this->tableBillsAlbaranesModel->save($tableBillsAlbaran);

                    $dates_actual = new Time('now', new \DateTimeZone('Europe/Madrid'));

                    $data1 = [
                        // 'active'  => 2,
                        'updated_at' => $dates_actual->format('Y-m-d'),
                        'albaran_status' => $albaran_status,
                    ];

                    $builder = $db->table('albaranes');
                    $builder->getWhere(['id_albaran' => $albaran->id_albaran]);
                    $builder->set(
                        'updated_at',
                        'albaran_status',
                    );
                    $builder->where('id_albaran', $albaran->id_albaran);
                    $builder->update($data1);
                }

                /**
                 * Actualizamos el estado del pedido
                 */
                $state = "Facturado";
                $data2 = [
                    'state' => $state,
                    'updated_at' => $dates_actual->format('Y-m-d'),
                ];
                $builder = $db->table('orders');
                $builder->getWhere(['id_order' => $id_order]);
                $builder->set(
                    'state',
                    'updated_at'
                );
                $builder->where('id_order', $id_order);
                $builder->update($data2);
            }



            return redirect()->route('redirectToResult');


        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listAlbaranes')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al crear la Factura']
            ]);
        }
    }


    public function redirectToResult()
    {
        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();
        $bills = $this->billsModel->orderBy('id_bills', 'DESC')->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Bills/list.html.twig', [
            'bills' => $bills,
            'customers_all' => $customers_all,
            'pager' => $this->billsModel->pager->links()
        ]);
    }


    public function editBillsAlbaran($id_b_a = null)
    {



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

        $tableBillsAlbaranes = $this->tableBillsAlbaranesModel->where('id_b_a', $id_b_a)->findAll();

        foreach ($tableBillsAlbaranes as $item2) {

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

            $supplements_exits = $item2->supplements_exits;
        }


        //suplemetnos con valores en los campos

        if ($supplements_exits === 1) {

            $tableBillsAlbaranesSelected = $this->tableBillsAlbaranesModel->where('id_b_a', $id_b_a)->first();
            $json_supplements = json_decode($tableBillsAlbaranesSelected->supplements);

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


        return $this->twig->render('Front/Bills/editBillsAlbaran.html.twig', [

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

            // 'id_albaran' => $id_albaran,
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



    public function createSaveBillsSupplements()
    {

        $_SESSION['idUser'];
        $idUser = session()->get('idUser');
        $name_user = session()->get('name');

        $customer_name = null;
        $customer_mail = null;
        $customer_address = null;
        $customer_location = null;
        $customer_zip_code = null;
        $customer_dni = null;
        $customer_phone = null;
        $customer_iva = null;
        $customer_iban = null;
        $customer_bank = null;
        $customer_office_bank = null;
        $customer_digital_control = null;
        $customer_bank_count = null;

        $work_location_address = null;
        $work_location_location = null;
        $work_location_province = null;
        $work_location_zip_code = null;

        $db = db_connect();
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $date_today = $date->format('d-m-Y');

        $id_customer = null;
        $id_work_location = null;

        $sum_price_service_select = 0;
        $id_work_location = null;

        $id_customer = null;
        $id_work_location = null;
        $iva = 0;
        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        $price_final = 0;

        $array_supplements = [];
        $subtotal_sum_supplements = 0;
        $price_total_base_albaranes = 0;

        $price_dto = 0;


        $price_total_all = 0;
        $cuota = 0;
        $iva = 0;

        $suppl_editional_existe = false;
        $json_supplements_aditionals = null;

        $namePaymentMethod = null;

        // Trae los albaranes seleccionados
        $supplements_id = $this->request->getPost('supplements_id');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $supplement_dto = $this->request->getPost('supplement_dto');
        $retainer_amount = $this->request->getPost('retainer_amount');
        $payment_method = $this->request->getPost('payment_method');
        $notas = $this->request->getPost('notas');

        $id_work_locations = $this->request->getPost('id_work_locations');


        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_locations)->paginate(config('Configuration')->regPerPage);
        foreach ($worklocations as $item) {

            $id_customer = $item->id_customer;

            $work_location_address = $item->address;
            $work_location_location = $item->location;
            $work_location_province = $item->province;
            $work_location_zip_code = $item->zip_code;
        }

        $customer = $this->customersModel->where('id_customer', $id_customer)->first();



        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        foreach ($customers as $i) {

            $iva = $i->iva;
            $id_customer = $i->id_customer;

            $customer_name = $i->names;
            $customer_mail = $i->mail;
            $customer_address = $i->address;
            $customer_location = $i->location;

            $customer_zip_code = $i->zip_code;
            $customer_dni = $i->dni;
            $customer_phone = $i->phone;
            $customer_iva = $i->iva;
            $customer_iban = $i->iban;
            $customer_bank = $i->bank;
            $customer_office_bank = $i->office_bank;
            $customer_digital_control = $i->digital_control;
            $customer_bank_count = $i->bank_count;
        }


        // $objBills = new Bills($this->request->getPost());
        $bills = new Bills($this->request->getPost());
        $bills->id_customer = $id_customer;

        $suppl_editional_existe = true;
        $supplements_obj_array = $this->supplementsModel->whereIn('id_supplements', $supplements_id)->paginate(config('Configuration')->regPerPage);

        foreach ($supplements_obj_array as $key => $obj_supp) {

            $pvp_edit_total = 0;

            if ($supplement_dto[$key] !== 0 || $supplement_dto[$key] !== null) {
                $pvp_edit_total = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                $price_final =  $pvp_edit[$key] - $pvp_edit_total;
            } else {
                $pvp_edit_total = $pvp_edit[$key];
                $price_final =  $pvp_edit[$key];
            }

            if ($supplement_dto[$key] === 0 || $supplement_dto[$key] === null) {

                $dto_s = 0;
                $price_dto = 0;
            } else {
                $price_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                $dto_s = $supplement_dto[$key];
            }


            //90  + 90 +90 = 270
            $price_total_all += $price_final;

            $supplementsObjectAditional = new Supplements();
            $supplementsObjectAditional->id_supplements = $obj_supp->id_supplements;
            $supplementsObjectAditional->name = $obj_supp->name;
            $supplementsObjectAditional->pvp_edit = $pvp_edit[$key];
            $supplementsObjectAditional->dto = $dto_s;
            $supplementsObjectAditional->price_dto = $price_dto;
            $supplementsObjectAditional->price_total = $price_final;
            $supplementsObjectAditional->price_total_all_supp = $price_total_all;

            $supplements_obj_array[$key] = $supplementsObjectAditional;
            $json_supplements_aditionals = json_encode($supplements_obj_array);
        }


        //  dd( $supplements_obj_array);

        if ($iva === "21") {
            $cuota =  $price_total_all * 0.21;
        }
        if ($iva === "10") {
            $cuota =  $price_total_all * 0.10;
        }
        if ($iva === "4") {
            $cuota =  $price_total_all * 0.04;
        }

        $total_con_iva = $cuota + $price_total_all;

        $total = $total_con_iva;
        $charge = $total_con_iva;
        $balance = $total_con_iva;

        //Total Bruto
        $gross_total = $price_total_all;
        $taxable_base = $price_total_all;


        if ($retainer_amount > 0) {
            $charge = $total_con_iva - $retainer_amount;
            $balance = $total_con_iva - $retainer_amount;
        }


        //fecha de vencimiento para obtner la fecha de   fecha de vencimiento depende el metodo de pago  ------
        $id_customer_expiration_date = $this->customersModel->where('id_customer', $id_customer)->find();
        foreach ($id_customer_expiration_date as $key => $rows) {
            $payment_method_customer =  $rows->payment_method;
        }
        $name_customer_expiration_date = $this->paymentMethodModel->where('id_payment_method', $payment_method_customer)->first();

        $tipoPago = $payment_method_customer;

        $fechaActual = new DateTime();


        if ($tipoPago === '10') {
            $intervalo = new DateInterval('P10D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '11') {
            $intervalo = new DateInterval('P15D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '12') {
            $intervalo = new DateInterval('P30D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '13') {
            $intervalo = new DateInterval('P45D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '14') {
            $intervalo = new DateInterval('P60D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '15') {
            $intervalo = new DateInterval('P90D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '16') {
            $intervalo = new DateInterval('P60D');
            $fechaActual->add($intervalo);
        } elseif ($tipoPago === '30') {
            $intervalo = new DateInterval('P1D');
            $fechaActual->add($intervalo);
        } else {

            $fechaActual = $fechaActual;
        }

        // Formatear y mostrar la fecha resultante
        $fechaFormateada = $fechaActual->format('Y-m-d');



        $bills->expiration_date = $fechaFormateada;


        $state = "Finalizada";

        $bills->id_work_locations = $id_work_locations;
        $bills->id_customer = $id_customer;

        $bills->name_user = $name_user;

        $bills->customer_name = $customer_name;
        $bills->customer_mail = $customer_mail;
        $bills->customer_address = $customer_address;
        $bills->customer_location = $customer_location;
        $bills->customer_zip_code = $customer_zip_code;
        $bills->customer_dni = $customer_dni;
        $bills->customer_phone = $customer_phone;
        $bills->customer_iva = $customer_iva;
        $bills->customer_iban = $customer_iban;
        $bills->customer_bank = $customer_bank;
        $bills->customer_office_bank = $customer_office_bank;
        $bills->customer_digital_control = $customer_digital_control;
        $bills->customer_bank_count = $customer_bank_count;

        $bills->date_signing_mandate = $customer->date_signing_mandate;
        $bills->recurrent_date = $customer->recurrent_date;

        $bills->work_location_address = $work_location_address;
        $bills->work_location_location = $work_location_location;
        $bills->work_location_province = $work_location_province;
        $bills->work_location_zip_code = $work_location_zip_code;

        $bills->json_supplements_aditional = $json_supplements_aditionals;


        $bills->fee = $cuota;

        $bills->iva = $iva;
        $bills->subtotal = $price_total_all;

        $bills->gross_total = $gross_total;
        $bills->taxable_base = $taxable_base;

        $bills->total = $total;
        $bills->subtotal_sum_supplements = $price_total_all;
        $bills->price_total_supp = $price_final;

        $bills->total_bills = $total;
        $bills->charge = $charge;
        $bills->balance = $balance;


        $bills->state = $state;
        $bills->active = 1;

        $bills->advance = $retainer_amount;
        $bills->payment_method = $name_customer_expiration_date->name;
        $bills->notas = $notas;

        $bills->bills_supplements = 1;



        $year = date('Y'); // Obtener el año actual
        $year = (int)$year;

        //Obtenemos el registro de la tabla last bill con el año vigente
        $lastBill = $this->lastBillsModel->where('num_year', $year)->first();


        if (!$lastBill) {

            $lastBill = new LastBills();
            $lastBill->num_bill = 0;
            $lastBill->num_year = $year;

            // $this->lastBillsModel->save($lastBill);

        }

        $lastBill->num_bill = $lastBill->num_bill + 1;

        try {

        $bills->year = $lastBill->num_year;
        $bills->num_bill = $lastBill->num_bill;
        $bills->words_num_bill = "M";
        $this->billsModel->save($bills);
        $this->lastBillsModel->save($lastBill);


        $previousMsg = $this->session->getFlashdata('msg');
        $currentMsg = [
            'type' => 'error',
            'title' => 'FACTURA CREADA!',
            'text' => 'Factura creada con exito.',
        ];
        if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
            $this->session->setFlashdata('msg', $currentMsg);
        }
        return redirect()->route('listBills');



        } catch (\Throwable $th) {

            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',
                'title' => 'ATENCION!',
                'text' => 'Error al crear la Factura.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }
            return redirect()->route('listBills');

        }
    }





    //Revisar no se usa
    public function seeDetailBills($id_bills = null)
    {


        $tableBillsAlbaranes = new TableBillsAlbaranes();
        $tableBillsAlbaranes2 = new TableBillsAlbaranes();

        $db = db_connect();
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $date_today = $date->format('d-m-Y');


        $id_work_location = null;
        $containers = null;

        $sum_price_service_select = 0;
        $id_work_location = null;

        $info_alb_con_ser = [];
        $array_all_containers = [];
        $array_all_cubic_meters = [];
        $id_customer = null;
        $id_work_location = null;
        $iva = 0;
        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        $price_final = 0;

        //Suplementos

        $array_supplements = [];
        $subtotal_sum_supplements = 0;
        $price_total_base_albaranes = 0;

        $price_dto = 0;
        $price_total = 0;
        $price_total_dtos = 0;

        $anticipos = 0;
        $cargos = 0;
        $saldos = 0;
        $bank_count = 0;
        $id_alabaran = 0;
        $sum_tax_base = 0;
        $sum_dto = 0;


        $json_services = [];

        $array_albaranes = [];
        $total_price_supp_final = 0;

        $services = [];
        $objeto = null;
        $services_select = [];

        $array_supplements = [];
        $array_supplements_albaranes = [];

        $numBills = null;
        $idAlb = null;
        $id_last_bills_insert = null;

        $total_taxe_base_alb = 0;
        $total_sum_pvp_edit = 0;
        $total_sum_dto = 0;

        $price_total_all = 0;
        $total_albaranes = 0;

        $n_bill = null;

        $cuota = 0;

        $suppl_editional_existe = false;
        $json_supplements_aditionals = null;

        // Trae los albaranes seleccionados
        $supplements_id = $this->request->getPost('supplements_id');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $supplement_dto = $this->request->getPost('supplement_dto');
        $retainer_amount = $this->request->getPost('retainer_amount');
        $payment_method = $this->request->getPost('payment_method');
        $notas = $this->request->getPost('notas');

        $id_work_locations = $this->request->getPost('id_work_locations');





        $bills = $this->billsModel->where('id_bills', $id_bills)->paginate(config('Configuration')->regPerPage);
        foreach ($bills as $i) {
            $id_customer = $i->id_customer;
        }



        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_locations)->paginate(config('Configuration')->regPerPage);
        foreach ($worklocations as $id) {
            $id_customer = $id->id_customer;
        }




        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        foreach ($customers as $i) {
            $iva = $i->iva;
        }



        // $objBills = new Bills($this->request->getPost());
        $bills = new Bills($this->request->getPost());
        $bills->id_customer = $id_customer;
        $bills->iva = $iva;


        if ($supplements_id) {

            $suppl_editional_existe = true;

            $supplements_obj_array = $this->supplementsModel->whereIn('id_supplements', $supplements_id)->paginate(config('Configuration')->regPerPage);

            foreach ($supplements_obj_array as $key => $obj_supp) {

                $pvp_edit_total = 0;

                if ($supplement_dto[$key] !== 0 || $supplement_dto[$key] !== null) {
                    $pvp_edit_total = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $price_final =  $pvp_edit[$key] - $pvp_edit_total;
                } else {
                    $pvp_edit_total = $pvp_edit[$key];
                    $price_final =  $pvp_edit[$key];
                }

                if ($supplement_dto[$key] === 0 || $supplement_dto[$key] === null) {

                    $dto_s = 0;
                    $price_dto = 0;
                } else {
                    $price_dto = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $dto_s = $supplement_dto[$key];
                }


                //90  + 90 +90 = 270
                $price_total_all += $price_final;

                $id_supplements = $obj_supp->id_supplements;

                $supplementsObjectArrayAditional = [];
                $supplementsObjectArrayAditionalStdClass = [];


                $supplementsObjectAditional = new Supplements();
                $supplementsObjectAditional->name = $obj_supp->name;
                $supplementsObjectAditional->pvp_edit = $pvp_edit[$key];
                $supplementsObjectAditional->dto = $dto_s;
                $supplementsObjectAditional->price_dto = $price_dto;
                $supplementsObjectAditional->price_total = $price_final;
                $supplementsObjectAditional->price_total_all_supp = $price_total_all;

                $supplementsObjectArrayAditional[$key] = $supplementsObjectAditional;
                $json_supplements_aditionals = json_encode($supplementsObjectArrayAditional);

                $supplements_obj_array[$key]->supplementsObjectAditional = $supplementsObjectArrayAditional;
            }
        } else {
            $suppl_editional_existe = false;
        }



        if ($iva === "21") {
            $cuota =  $price_final * 0.21;
        }
        if ($iva === "10") {
            $cuota =  $price_final * 0.10;
        }
        if ($iva === "4") {
            $cuota =  $price_final * 0.04;
        }


        $total_con_iva = $cuota + $price_total_all;

        $charge = $total_con_iva;
        $balance = $total_con_iva;


        if ($retainer_amount > 0) {
            $charge = $total_con_iva - $retainer_amount;
            $balance = $total_con_iva - $retainer_amount;
        }


        if ($supplements_id) {

            $bills->json_supplements_aditional = $json_supplements_aditionals;
        } else {
            $bills->json_supplements_aditional = null;
        }



        $bills->id_work_location = $id_work_location;
        $bills->id_customer = $id_customer;
        $bills->total_bills = $charge;
        $bills->advance = $retainer_amount;
        $bills->charge = $charge;
        $bills->balance = $balance;
        $bills->payment_method = $payment_method;
        $bills->notas = $notas;
        $bills->state = 1;
        $bills->active = 1;

        // try {


        $this->billsModel->save($bills);
        $id_last_bills_insert = $db->insertID($bills); //Ultimo id pallet insertado



        if ($supplements_id) {

            foreach ($supplements_obj_array as $key => $supplements) {
                //Guardamos en la tabla Tabla Bills Albaranes

                $tableBillsAlbaranes2->id_supplements = $supplements->id_supplements;

                $tableBillsAlbaranes2->json_supplements = $json_supplements_aditionals;

                $tableBillsAlbaranes2->id_bills = $id_last_bills_insert;

                $this->tableBillsAlbaranesModel->save($tableBillsAlbaranes2);
            }
        }

        $bills = $this->billsModel->where('id_bills', $id_bills)->paginate(config('Configuration')->regPerPage);


        return $this->twig->render('Front/Bills/BillsCreatedSupplements.html.twig', [

            // 'array_all_containers' => $array_all_containers,
            // 'array_all_cubic_meters' => $array_all_cubic_meters,
            'customers' => $customers,
            'worklocations' => $worklocations,
            'id_work_location' => $id_work_location,
            'date_today' => $date_today,

            'json_services' => $json_services,
            'retainer_amount' => $retainer_amount,
            'payment_method' => $payment_method,

            'charge' => $charge,
            'balance' => $balance,

            'id_bills' => $id_bills,

            'id_last_bills_insert' => $id_last_bills_insert,

            'sum_dto' => $sum_dto,
            'price_final' => $price_final,
            'bank_count' => $bank_count,
            'supplements_obj_array' => $supplements_obj_array,
            'price_total_base_albaranes' => $price_total_base_albaranes,
            'iva' => $iva,
            'sum_tax_base' => $sum_tax_base,
            'total_con_iva' => $total_con_iva,
            'cuota' => $cuota,

            'price_total_all' => $price_total_all,

            'suppl_editional_existe' => $suppl_editional_existe,

            'pager' => $this->workLocationModel->pager->links()
        ]);
    }

    public function seeDetailBillsS($id_bills = null)
    {


        $supplements = [];

        $subtotal_sum_supplements = 0;
        $price_total_base_albaranes = 0;

        $sum_tax_base = 0;
        $sum_dto = 0;

        $amount_tax_base_discount = 0;
        $price_discount = 0;

        $supplements = [];

        $array_albaranes = [];
        $total_price_supp_final = 0;

        $total_taxe_base_alb = 0;
        $total_albaranes = 0;
        $tax_base = null;

        $supplements_exits = 0;
        $words_num_bill = "";
        $num_bill_original = 0;

        $bills = $this->billsModel->where('id_bills', $id_bills)->paginate(config('Configuration')->regPerPage);
        $bills_original = $this->billsModel->where('id_bill_original', $id_bills)->paginate(config('Configuration')->regPerPage);


        //Si existe abono
        if ($bills_original) {

            //recorremos
            foreach ($bills_original as $row) {

                $num_bill_original = $row->num_bill;
                $words_num_bill = "R";
            }

            $bill = $bills_original;
        }

        $id_albaranes_selected = $this->tableBillsAlbaranesModel->where('id_bills', $id_bills)->findAll();

        //Obtenemos los albaranes
        $id_albaranes = [];
        foreach ($id_albaranes_selected as $key => $obj1) {
            $id_albaranes[] = $obj1->id_albaran;
        }


        if ($id_albaranes) {
            $id_albaranes_selected = $this->albaranesModel->whereIn('id_albaran', $id_albaranes)->findAll();

            foreach ($id_albaranes_selected as $key => $obj) {


                $total_taxe_base_alb += $obj->tax_base;
                $tax_base = $obj->tax_base;
                $discount = $obj->discount;
                $price_discount = $obj->price_discount;
                $amount_tax_base_discount = $obj->amount_tax_base_discount;

                //total de los supllementos
                $subtotal_sum_supplements += $obj->subtotal_sum_supplements;

                //Suma total con descuentos de los albaranes
                $price_total_base_albaranes += $amount_tax_base_discount;
                $sum_tax_base += $tax_base;
                $sum_dto += $price_discount;

                $albaranes = new Albaranes();
                $albaranes->id_albaran = $obj->id_albaran;

                $total_albaranes += $obj->total;

                //  $albaranes->supplements =  $item->supplements;

                $array_albaranes[] = $albaranes;
                $json_albaranes = json_encode($array_albaranes);
                /**
                 * Decofificamos y guarsamos los servicios guardados de albaranes en una variable
                 */


                $supplements_alb = $obj->supplements;


                if ($supplements_alb) {
                    $supplements = json_decode($obj->supplements);
                    $supplements_obj_existe_alb = true;

                    foreach ($supplements as $x2 => $s) {

                        $id_supplements = $s->id_supplements;
                        $pvp_edit = $s->pvp_edit;
                        $price_dto = $s->price_dto;
                        $total_price_supp = $s->price_total;

                        $total_price_supp_final += $total_price_supp;

                        $supplementsObject = new stdClass();
                        $supplementsObject->name = $s->name;
                        $supplementsObject->pvp_edit = $s->pvp_edit;
                        $supplementsObject->price_dto = $s->price_dto;
                        $supplementsObject->dto = $s->dto;
                        $supplementsObject->total_price_supp = $total_price_supp;

                        $supplementsObjectArray[$x2] = $supplementsObject;
                    }

                    $supplements_exits = 1;

                    $id_albaranes_selected[$key]->supplementsObject = $supplementsObjectArray;
                }

                $prices = new stdClass();

                $prices->tax_base = $tax_base;
                $prices->discount = $discount;
                $prices->price_discount = $price_discount;
                $prices->amount_tax_base_discount = $amount_tax_base_discount;

                $pricesObjectArray[$key] = $prices;
                $id_albaranes_selected[$key]->prices = $pricesObjectArray;
            }
        }



        foreach ($bills as $key => $obj) {

            $supplements = $obj->json_supplements_aditional;

            if ($supplements) {

                $supplements = json_decode($obj->json_supplements_aditional);

                foreach ($supplements as $x2 => $s) {

                    $id_supplements = $s->id_supplements;
                    $pvp_edit = $s->pvp_edit;
                    $price_dto = $s->price_dto;
                    $total_price_supp = $s->price_total;

                    $total_price_supp_final += $total_price_supp;

                    $supplementsObject = new stdClass();
                    $supplementsObject->name = $s->name;
                    $supplementsObject->pvp_edit = $s->pvp_edit;
                    $supplementsObject->price_dto = $s->price_dto;
                    $supplementsObject->dto = $s->dto;
                    $supplementsObject->total_price_supp = $total_price_supp;

                    $supplementsObjectArray[$x2] = $supplementsObject;
                }

                $supplements_exits = 1;
                $bills[$key]->supplementsObject = $supplementsObjectArray;
            }
        }


        return $this->twig->render('Front/Bills/seeDetailBillsSav.html.twig', [

            'bills' => $bills,
            'words_num_bill' => $words_num_bill,
            'id_albaranes_selected' => $id_albaranes_selected,
            '$supplements_exits' => $supplements_exits,

            'num_bill_original' => $num_bill_original,

            'pager' => $this->billsModel->pager->links()
        ]);
    }

    public function seeDetailBillsSaveSup($id_bills = null)
    {



        $supplements = [];
        $supplements_obj_array = [];
        $subtotal_sum_supplements = 0;
        $price_total_base_albaranes = 0;

        $sum_tax_base = 0;
        $sum_dto = 0;

        $amount_tax_base_discount = 0;
        $price_discount = 0;

        $supplements = [];
        $total_price_supp_final = 0;

        $total_taxe_base_alb = 0;
        $tax_base = null;



        $words_num_bill = "";

        $num_bill_original = 0;

        $bills = $this->billsModel->where('id_bills', $id_bills)->paginate(config('Configuration')->regPerPage);
        $bills_original = $this->billsModel->where('id_bill_original', $id_bills)->paginate(config('Configuration')->regPerPage);

        //Si existe abono
        if ($bills_original) {

            //recorremos
            foreach ($bills_original as $row) {

                $num_bill_original = $row->num_bill;
                $words_num_bill = "R";
            }
        }


        foreach ($bills as $key => $obj) {


            $total_taxe_base_alb += $obj->tax_base;
            $tax_base = $obj->tax_base;
            $discount = $obj->discount;
            $price_discount = $obj->price_discount;
            $amount_tax_base_discount = $obj->amount_tax_base_discount;

            //total de los supllementos
            $subtotal_sum_supplements += $obj->subtotal_sum_supplements;

            //Suma total con descuentos de los albaranes
            $price_total_base_albaranes += $amount_tax_base_discount;
            $sum_tax_base += $tax_base;
            $sum_dto += $price_discount;


            /**
             * Decofificamos y guarsamos los servicios guardados de albaranes en una variable
             */

            $supplements = $obj->json_supplements_aditional;



            if ($supplements) {

                $supplements = json_decode($obj->json_supplements_aditional);


                foreach ($supplements as $x2 => $s) {

                    $id_supplements = $s->id_supplements;
                    $pvp_edit = $s->pvp_edit;
                    $price_dto = $s->price_dto;
                    $total_price_supp = $s->price_total;

                    $total_price_supp_final += $total_price_supp;

                    $supplementsObject = new stdClass();
                    $supplementsObject->name = $s->name;
                    $supplementsObject->pvp_edit = $s->pvp_edit;
                    $supplementsObject->price_dto = $s->price_dto;
                    $supplementsObject->dto = $s->dto;
                    $supplementsObject->total_price_supp = $total_price_supp;

                    $supplementsObjectArray[$x2] = $supplementsObject;
                }

                $supplements_exits = 1;

                $bills[$key]->supplementsObject = $supplementsObjectArray;
            }


            $prices = new stdClass();

            $prices->tax_base = $tax_base;
            $prices->discount = $discount;
            $prices->price_discount = $price_discount;
            $prices->amount_tax_base_discount = $amount_tax_base_discount;

            $pricesObjectArray[$key] = $prices;
            $bills[$key]->prices = $pricesObjectArray;
        }

        $data['bills'] = $bills;
        $data['supplementsObjectArray'] = $supplementsObjectArray;



        return $this->twig->render('Front/Bills/seeDetailBillsSav.html.twig', [

            'bills' => $bills,
            'data' => $data,
            'words_num_bill' => $words_num_bill,
            'num_bill_original' => $num_bill_original,
            'supplements_obj_array' => $supplements_obj_array,

            'pager' => $this->billsModel->pager->links()
        ]);
    }



    public function result()
    {
        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();

        $id_customer = $this->request->getGet('customer_id');
        $id_work_locations = $this->request->getGet('id_work_locations');
        $created_at = $this->request->getGet('created_at');
        $id_bills = $this->request->getGet('id_bills');


        $payment_method = $this->request->getGet('payment_method');



        if (count($_GET) > 0) {

            $bills = $this->billsModel->select('*');


            if (!empty($id_bills)) {
                $bills->where('id_bills', $id_bills);
            }

            if (!empty($id_customer)) {
                $bills->where('id_customer', $id_customer);
            }

            if (!empty($id_work_locations)) {
                $bills->where('id_work_locations', $id_work_locations);
            }

            if (!empty($id_work_locations)) {
                $bills->where('id_work_locations', $id_work_locations);
            }


            if (!empty($payment_method)) {
                $bills->where('payment_method', $payment_method);
            }

            $bills = $this->billsModel->paginate(config('Configuration')->regListBillsPage);
            // dd($albaranes );

        } else {
            $bills = $this->billsModel->orderBy('id_bills', 'DESC')->paginate(config('Configuration')->regListBillsPage);
        }



        return $this->twig->render('Front/Bills/list.html.twig', [
            'bills' => $bills,
            'customers_all' => $customers_all,
            'pager' => $this->billsModel->pager->links()
        ]);
    }





    public function proForma()

    {
        /*
        if (!$this->validate(validateUpdatePartida())) {
            return redirect()->back()
                   ->with('errors', $this->validator->getErrors())
                   ->withInput();
           }
        */
        // $id_customer = $this->request->getPost('id_customer');

        $id_customer = null;
        $id_customer = null;
        $id_work_location = null;
        $containers = null;

        $sum_price_service_select = 0;
        $id_customer = null;
        $id_work_location = null;

        $info_alb_con_ser = [];
        $array_all_containers = [];
        $array_all_cubic_meters = [];

        $containers = [];
        $array_all_cubic_meters = [];
        $arraysIdsAlbaranes = [];
        $supplements = [];
        $json_services = [];
        $albaran = [];

        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $date_today = $date->format('d-m-Y');

        // Trae los albaranes seleccionados
        $id_albaranes_selected = $this->request->getPost('albaranes');

        $id_alabaran = null;
        foreach ($id_albaranes_selected as $rows1) {
            $id_alabaran = $rows1;
        }
        $albaran_3 = $this->albaranesModel->where('id_albaran', $id_alabaran)->paginate(config('Configuration')->regPerPage);
        foreach ($albaran_3 as $rows) {
            $id_customer = $rows->id_customer;
            $id_work_location = $rows->id_work_location;
        }


        $services = [];
        $objeto = null;
        $services_select = [];
        $id_albaranes_selected = $this->albaranesModel->join('containers', 'containers.id_container = albaranes.id_container')->whereIn('id_albaran', $id_albaranes_selected)->paginate(config('Configuration')->regPerPage);

        //Suplementos
        $selected_supplements = [];
        $array_supplements = [];
        foreach ($id_albaranes_selected as $objeto) {


            $json_supplements = json_decode($objeto->supplements);


            foreach ($json_supplements as $item) {

                $id_sp = $item->id_supplements;

                $selected_supplements[] = $this->supplementsModel->find($id_sp);
            }
        }


        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Bills/ProForma.html.twig', [

            'sum_price_service_select' => $sum_price_service_select,
            'array_all_containers' => $array_all_containers,
            'array_all_cubic_meters' => $array_all_cubic_meters,
            'customers' => $customers,
            'info_alb_con_ser' => $info_alb_con_ser,
            'albaran' => $albaran,
            'supplements' => $supplements,
            'containers' => $containers,
            'worklocations' => $worklocations,
            'id_work_location' => $id_work_location,
            'date_today' => $date_today,

            'selected_supplements' => $selected_supplements,

            'id_albaranes_selected' => $id_albaranes_selected,

            'json_services' => $json_services,
            'pager' => $this->albaranesModel->pager->links()


        ]);
    }


    /**
     * Albaranes y suplementos
     */
    public function create()
    {



        $array = [];
        $name_customer = null;
        $myJSON = null;
        $myJSON2 = null;
        $id_albaran = null;
        $bills = null;
        $bill = null;
        $array_albaranes = [];

        $id_albaranes_select = $this->request->getPost('id_albaranes');

        $albaranes_obj = $this->albaranesModel->orderBy('id_albaran', 'ASC')->findAll();

        $bills = new Bills($this->request->getPost());

        //lee los selecionados
        foreach ($id_albaranes_select as $key => $id_albaran_select) {

            //con todos los albaranes
            foreach ($albaranes_obj as $id) {
                $ids_albaranes_obj = $id->id_albaran;

                if ($ids_albaranes_obj  === $id_albaran_select) {
                    $albaranes = new Albaranes();
                    $albaranes->id_albaran = $id_albaran_select;
                    $array_albaranes[] = $albaranes;
                    $myJSON = json_encode($array_albaranes);
                }
            }
        }
        try {

        $bills->id_albaran = $myJSON;
        $this->billsModel->save($bills);

            return redirect()->route('showFormBills')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Factura registrada con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('showFormBills')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al crear la Factura']
            ]);
        }
    }




    public function editBills($id_bills = null)
    {

        $withoutCustomer = false;
        $customer_name = NULL;
        $customer_mail = NULL;
        $customer_address = NULL;
        $customer_location = NULL;
        $customer_province = NULL;
        $customer_zip_code = NULL;
        $customer_dni = NULL;
        $customer_phone = NULL;
        $customer_iva = NULL;
        $customer_iban = NULL;
        $customer_bank = NULL;
        $customer_office_bank = NULL;
        $customer_digital_control = NULL;
        $customer_bank_count = NULL;

        $payment_method = NULL;

        $container_residue = NULL;
        $container_m3 = NULL;
        $container_price = NULL;

        $work_location_address = NULL;
        $work_location_location = NULL;
        $work_location_province = NULL;
        $work_location_zip_code = NULL;


        $db = db_connect();
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $date_today = $date->format('d-m-Y');

        $id_customer = null;
        $id_work_location = null;
        $containers = null;

        $sum_price_service_select = 0;
        $id_customer = null;
        $id_work_location = null;

        $info_alb_con_ser = [];
        $array_all_containers = [];
        $array_all_cubic_meters = [];
        $id_customer = null;
        $id_work_location = null;
        $iva = 0;
        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        $price_final = 0;

        //Suplementos

        $price_total_base_albaranes = 0;

        $bank_count = 0;
        $id_alabaran = 0;
        $sum_tax_base = 0;
        $sum_dto = 0;

        $bank_count = 0;
        $supplements = [];
        $id_last_bills_insert = null;

        $price_total_all = 0;

        $retainer_amount = null;
        $charge = null;
        $balance = null;
        $cuota = null;
        $total_con_iva = null;
        $supplements = null;
        $supplements_edit = [];

        $id_service = null;

        $suppl_editional_existe = false;

        $supplements_all = $this->supplementsModel->orderBy('id_supplements', 'ASC')->paginate(config('Configuration')->regPerPage);

        // Trae los albaranes seleccionados
        $tableBillsAlbaranes = $this->tableBillsAlbaranesModel->where('id_bills', $id_bills)->paginate(config('Configuration')->regBillsPage);
        foreach ($tableBillsAlbaranes as $key => $obj) {

            $supplements = $obj->supplements;
            $id_albaran = $obj->id_albaran;
            $id_service = $obj->id_service;

            $id_customers = $obj->id_customers;
            $customer_name = $obj->customer_name;
            $customer_mail = $obj->customer_mail;
            $customer_address = $obj->customer_address;
            $customer_location = $obj->customer_location;
            $customer_province = $obj->customer_province;
            $customer_zip_code = $obj->customer_zip_code;
            $customer_dni = $obj->customer_dni;
            $customer_phone = $obj->customer_phone;
            $customer_iva = $obj->customer_iva;

            $work_location_address = $obj->work_location_address;
            $work_location_location = $obj->work_location_location;
            $work_location_province = $obj->work_location_province;
            $work_location_zip_code = $obj->work_location_zip_code;


            if ($supplements !== null) {

                //  dd($supplements);



                //$albaran = $this->tableBillsAlbaranesModel->where('id_bills', $id_bills)->where('id_albaran', $id_albaran)->first();

                $json_supplements = json_decode($supplements);

                $formatedSupplements = [];

                foreach ($supplements_all as $x) {
                    $stdClass = new stdClass();
                    $stdClass->id_supplements = $x->id_supplements;
                    $stdClass->name = $x->name;
                    $stdClass->dto = $x->dto;
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
                    $supplementsObject->dto = $item->dto;
                    $supplementsObject->pvp_edit = $item->pvp_edit;
                    $supplementsObject->price_dto =  $item->price_dto;
                    $supplementsObject->price_total = $item->price_total;
                    $supplementsObject->active = true;

                    $supplementsObjectArray[$item->name] = $supplementsObject;
                }


                $tableBillsAlbaranes[$key]->supplementsObject = $supplementsObjectArray;

                $no_supplements = false;
                $supplements_edit = array_merge($formatedSupplements, $supplementsObjectArray);
            } else {
                $supplements_edit = 0;
            }
            // $id_albaranes_selected = $this->albaranesModel->join('tablebillsalbaranes', 'tablebillsalbaranes.id_albaran = albaranes.id_albaran')->where('tablebillsalbaranes.id_bills', $id_bills)->findAll();
        }


        $supplements_all = $this->supplementsModel->orderBy('id_supplements', 'ASC')->paginate(config('Configuration')->regPerPage);

        $bills = $this->billsModel->where('id_bills', $id_bills)->paginate(config('Configuration')->regBillsPage);
        foreach ($bills as $i) {
            $id_customers = $i->id_customer;
        }

        // regContainersPage
        $services_ = $this->servicesModel->orderBy('id_service', 'ASC')->findAll();
        $supplements_all = $this->supplementsModel->orderBy('id_supplements', 'ASC')->findAll();
        $containers = $this->containerModel->orderBy('id_container', 'ASC')->findAll();
        $customers = $this->customersModel->where('id_customer', $id_customers)->paginate(config('Configuration')->regPerPage);



        return $this->twig->render('Front/Bills/edit.html.twig', [

            //  'supplementsObject' => $supplementsObject,

            'containers' => $containers,
            'supplements_all' => $supplements_all,
            'supplements_edit' => $supplements_edit,
            'services_' => $services_,
            'id_service' => $id_service,

            'id_customers' => $id_customers,
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

            'work_location_address' => $work_location_address,
            'work_location_location' => $work_location_location,
            'work_location_province' => $work_location_province,
            'work_location_zip_code' => $work_location_zip_code,

            'id_work_location' => $id_work_location,
            'date_today' => $date_today,
            'tableBillsAlbaranes' => $tableBillsAlbaranes,

            'retainer_amount' => $retainer_amount,
            'payment_method' => $payment_method,

            'charge' => $charge,
            'balance' => $balance,
            'id_bills' => $id_bills,
            'bills' => $bills,

            'id_last_bills_insert' => $id_last_bills_insert,

            'sum_dto' => $sum_dto,
            'price_final' => $price_final,
            'bank_count' => $bank_count,
            'supplements_obj_array' => $supplements_obj_array,
            'price_total_base_albaranes' => $price_total_base_albaranes,
            'iva' => $iva,
            'sum_tax_base' => $sum_tax_base,
            'total_con_iva' => $total_con_iva,
            'cuota' => $cuota,

            'price_total_all' => $price_total_all,

            'suppl_editional_existe' => $suppl_editional_existe,

            'pager' => $this->billsModel->pager->links()
        ]);
    }





    public function editSaveBills()
    {

        //Suplementos
        $selected_supplements = [];
        $array_supplements = [];

        $total_sum_supplementos = 0;

        $price = null;
        $id_supplements_obj = null;
        $array = [];
        $array_service = [];
        $services_price_final = null;
        $id_work_locations_selected = null;

        $sum_price_supplements_select = 0;
        $myJSON = null;

        $supplements = [];
        $supplements_obj_array = [];

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
        $price_total_width_dto = 0;

        $price_total_supp = 0;
        $price_total_all_supp = 0;
        $tax_base_original = 0;

        $sum_container_new_supplement = 0;

        $price_container_old = 0;
        $array_id_supplements = [];

        $final_total = null;
        $final_container = null;

        $db = db_connect();
        $data = [];

        $id_containers_array = [];
        $id_container_new = [];
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $_SESSION['idUser'];
        $idUser = session()->get('idUser');
        $name_user = session()->get('name');

        $supplements_id[][] = null;
        $supplements_name[][] = null;
        $pvp_edit[][] = null;
        $supplement_dto[][] = null;
        $carry = null;

        $array_precios = [];

        $sum_total_container = 0;
        $sum_container_old = 0;
        $sum_container_new = 0;

        $supplemento_price_total = 0;
        $cuota = 0;
        $total_con_iva = 0;

        $sum_container_price = 0;
        $sum_price_discount = 0;
        $sum_retainer_amount = 0;
        $sum_price_discount_supplements = 0;
        $pvp_edit_total_supplement = 0;

        $supplements_id = $this->request->getPost('supplements_id');
        $supplements_name = $this->request->getPost('supplements_name');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $supplement_dto = $this->request->getPost('supplement_dto');

        $id_service = $this->request->getPost('id_service');

        $id_table = $this->request->getPost('id_b_a');
        $id_bills = $this->request->getPost('id_bills');

        $pvp_edit_total_supplement_old = 0;
        $pvp_edit_total_supplement_new = 0;
        $pvp_edit_total_supplement_total = 0;

        $pvp_edit_old = 0;

        $iva = $this->request->getPost('c_iva');
        $id_container_new = $this->request->getPost('id_container_new');

        $supplements_obj_array = $this->supplementsModel->orderBy('id_supplements', 'ASC')->findAll();
        $indexed_array = array_reduce($supplements_obj_array, function ($carry, $item) {
            $carry[$item->id_supplements] =  $item;

            return $carry;
        }, []);


        $table_albaran_bills  = $this->tableBillsAlbaranesModel->where('id_bills', $id_bills)->findAll();

        $bills  = $this->billsModel->where('id_bills', $id_bills)->findAll();

        $bills_obj = new Bills();
        $bills_obj->id_bills = $id_bills;

        try {

            foreach ($table_albaran_bills as  $albaranes) {

                $price_discount = $albaranes->price_discount;
                $price_container_old = $albaranes->container_price;
                $id_container_old = $albaranes->id_container;

                //porcentaje dto.
                $discount_container_old = $albaranes->discount;

                //old
                $supplements_alb = $albaranes->supplements;

                if ($supplements_alb) {
                    $supplements = json_decode($albaranes->supplements);
                    foreach ($supplements as $x2 => $s) {

                        $pvp_edit_total_supplement_old += $s->pvp_edit;
                        $pvp_edit_old += $s->pvp_edit;
                        $price_dto = $s->price_dto;
                        $sum_price_discount_supplements += $price_dto;
                    }
                }


                //new
                if ($supplements_id) {

                    foreach ($supplements_id as $idAlbaran => $suplementosId) {

                        $array_supplements = [];

                        foreach ($suplementosId as $i) {

                            if ($indexed_array[$i]->pvp_edit === "") {
                                $pvp_edit[$idAlbaran][$i] = 0;
                            }
                            if ($supplement_dto[$idAlbaran][$i] === "") {
                                $supplement_dto[$idAlbaran][$i] = 0;
                            }

                            $indexed_array[$i]->pvp_edit = $pvp_edit[$idAlbaran][$i];
                            $indexed_array[$i]->dto = $supplement_dto[$idAlbaran][$i];

                            $price_dto += $indexed_array[$i]->priceToDiscount();
                            $price_total_width_dto = $indexed_array[$i]->priceWithDiscount();

                            $indexed_array[$i]->price_dto = $price_dto;
                            $indexed_array[$i]->price_total = $price_total_width_dto;

                            //ejemplo 10 10 10 = 30          SUMA EL TOTAL DE TODOS LOS DESCEUNTOS EN PREICO EUROS
                            $price_total_all_dtos_euros += $supplement_dto[$idAlbaran][$i];

                            //ejemplo 100 100 100 = 300
                            $price_total_all_sin_dtos_base += $pvp_edit[$idAlbaran][$i];

                            //suma los edit_pvp
                            $pvp_edit_total_supplement_new += $pvp_edit[$idAlbaran][$i];

                            //porcentaje de descuento de cada suplemento en euros eje 10 €
                            //100 * 10% = 10E
                            $price_dto = $pvp_edit[$idAlbaran][$i] * $supplement_dto[$idAlbaran][$i] / 100;

                            //Sumamos el total de esos valores de dtos 10 + 10 +15 = 45
                            $sum_total_dtos += $price_dto;

                            $sum_price_discount_supplements += $sum_total_dtos;

                            //Precio total base menos dtos 270
                            $price_total_width_dto = $pvp_edit[$idAlbaran][$i] - $price_dto;


                            $price_total += $price_total_width_dto;
                            $supplemento_price_total = $price_total;


                            // precio total de cada LINEA  de suplementos $price_total_width_dto
                            // precio total  de suplementos   $supplemento_price_total

                            //-----------------------------*/

                            $albaranes->price_total_supp = $price_total;


                            array_push($array_supplements,  $indexed_array[$i]);
                            $myJSON = json_encode($array_supplements);
                        }


                        //Actualizar dentro del cada albaran el campo supplements

                        $data01 = [
                            'supplements' => $myJSON,
                        ];
                        $builder01 = $db->table('tablebillsalbaranes');
                        $builder01->getWhere(['id_b_a' => $albaranes->id_b_a]);

                        $builder01->set(
                            'supplements',
                        );
                        $builder01->where('id_b_a', $albaranes->id_b_a);
                        $builder01->update($data01);
                    }
                } else {
                    $myJSON = null;
                }




                //Si añade de la lista de container uno nuevo
                if ($id_container_new) {

                    if (array_key_exists($albaranes->id_albaran, $id_container_new)) {

                        $idContenedor = $id_container_new[$albaranes->id_albaran];
                        $container = $this->containerModel->where('id_container', $idContenedor)->first();

                        // Crear un nuevo objeto $albaranNuevo y asignarle los valores del $albaranes actual
                        $TableBillsAlb = new TableBillsAlbaranes();

                        $TableBillsAlb->id_albaran = $albaranes->id_albaran;
                        $TableBillsAlb->id_b_a = $albaranes->id_b_a;
                        $TableBillsAlb->tax_base = $container->price;

                        $TableBillsAlb->discount = $albaranes->discount;


                        $sum_retainer_amount += $albaranes->retainer_amount;
                        $sum_container_price += $container->price;

                        if ($discount_container_old) {

                            if ($discount_container_old !== 0 || $discount_container_old > 0) {
                                $price_discount = $container->price * $discount_container_old / 100;
                                $sum_price_discount += $price_discount;
                                $TableBillsAlb->price_discount = $price_discount;
                            }
                        }



                        $TableBillsAlb->id_container = $container->id_container;
                        $TableBillsAlb->container_residue = $container->residue;
                        $TableBillsAlb->container_price = $container->price;
                        $TableBillsAlb->container_m3 = $container->cubic_meters;

                        $TableBillsAlb->amount_tax_base_discount = $container->price - $price_discount;


                        $TableBillsAlb->subtotal = $container->price + $albaranes->price_total_supp;
                        $subtotal = $container->price + $albaranes->price_total_supp;


                        if ($iva === "21") {
                            $cuota =  $subtotal * 0.21;
                        }
                        if ($iva === "10") {
                            $cuota =   $subtotal * 0.10;
                        }
                        if ($iva === "4") {
                            $cuota =   $subtotal * 0.04;
                        }


                        $total = $subtotal + $cuota;

                        $TableBillsAlb->iva = $cuota;
                        $TableBillsAlb->charge = $cuota;
                        $TableBillsAlb->total = $total;


                        $this->tableBillsAlbaranesModel->save($TableBillsAlb);
                    } else {

                        //$albaranes->container_price = $price_container_old;

                        $container = $this->containerModel->where('id_container', $id_container_old)->first();

                        // Crear un nuevo objeto $albaranNuevo y asignarle los valores del $albaranes actual
                        $TableBillsAlb = new TableBillsAlbaranes();

                        $TableBillsAlb->id_albaran = $albaranes->id_albaran;
                        $TableBillsAlb->id_b_a = $albaranes->id_b_a;
                        $TableBillsAlb->tax_base = $container->price;

                        $sum_retainer_amount += $albaranes->retainer_amount;
                        $sum_container_price += $container->price;
                        $sum_price_discount += $albaranes->price_discount;

                        $TableBillsAlb->id_container = $container->id_container;
                        $TableBillsAlb->container_residue = $container->residue;
                        $TableBillsAlb->container_price = $container->price;
                        $TableBillsAlb->container_m3 = $container->cubic_meters;

                        $TableBillsAlb->amount_tax_base_discount = $container->price - $albaranes->price_discount;

                        $TableBillsAlb->subtotal = $container->price + $albaranes->price_total_supp;
                        $subtotal = $container->price + $albaranes->price_total_supp;


                        if ($iva === "21") {
                            $cuota =  $subtotal * 0.21;
                        }
                        if ($iva === "10") {
                            $cuota =   $subtotal * 0.10;
                        }
                        if ($iva === "4") {
                            $cuota =   $subtotal * 0.04;
                        }

                        $total = $subtotal + $cuota;

                        $TableBillsAlb->iva = $cuota;
                        $TableBillsAlb->charge = $cuota;
                        $TableBillsAlb->total = $total;

                        $this->tableBillsAlbaranesModel->save($TableBillsAlb);
                    }
                } else {
                    //$albaranes->container_price = $price_container_old;

                    $container = $this->containerModel->where('id_container', $id_container_old)->first();

                    // Crear un nuevo objeto $albaranNuevo y asignarle los valores del $albaranes actual
                    $TableBillsAlb = new TableBillsAlbaranes();

                    $TableBillsAlb->id_albaran = $albaranes->id_albaran;
                    $TableBillsAlb->id_b_a = $albaranes->id_b_a;
                    $TableBillsAlb->tax_base = $container->price;

                    $sum_retainer_amount += $albaranes->retainer_amount;

                    $sum_container_price += $container->price;

                    $sum_price_discount += $albaranes->price_discount;

                    $TableBillsAlb->id_container = $container->id_container;
                    $TableBillsAlb->container_residue = $container->residue;

                    $TableBillsAlb->container_price = $container->price;
                    $TableBillsAlb->container_m3 = $container->cubic_meters;

                    $TableBillsAlb->amount_tax_base_discount = $container->price - $albaranes->price_discount;

                    $TableBillsAlb->subtotal = $container->price + $albaranes->price_total_supp;
                    $subtotal = $container->price + $albaranes->price_total_supp;


                    if ($iva === "21") {
                        $cuota =  $subtotal * 0.21;
                    }
                    if ($iva === "10") {
                        $cuota =   $subtotal * 0.10;
                    }
                    if ($iva === "4") {
                        $cuota =   $subtotal * 0.04;
                    }

                    $total = $subtotal + $cuota;
                    $TableBillsAlb->iva = $cuota;
                    $TableBillsAlb->total = $total;




                    $albaran_status = "Facturado";

                    //Obtenemos los datos del formulario por cada campo
                    $data = [
                        // 'active'  => 2,
                        'updated_at' => $date->format('Y-m-d'),
                        'albaran_status' => $albaran_status,

                    ];
                    $builder = $db->table('albaranes');
                    $builder->getWhere(['id_albaran' => $albaranes->id_albaran]);
                    $builder->set(
                        'albaran_status',
                        'updated_at'
                    );
                    $builder->where('id_albaran', $albaranes->id_albaran);
                    $builder->update($data);


                    /**
                     * Actualizamos el estado del pedido
                     */
                    $state = "Facturado";
                    $data = [
                        'state' => $state,
                        'updated_at' => $date->format('Y-m-d'),
                    ];
                    $builder = $db->table('orders');
                    $builder->getWhere(['id_order' => $albaranes->id_order]);
                    $builder->set(
                        'state',
                        'updated_at'

                    );


                    $this->tableBillsAlbaranesModel->save($TableBillsAlb);
                }


                $pvp_edit_total_supplement_total = $pvp_edit_total_supplement_old + $pvp_edit_total_supplement_new;
            }


            foreach ($bills as $row) {



                $bills_obj->id_order = $row->id_order;
                $bills_obj->id_work_locations = $row->id_work_locations;
                $bills_obj->id_customer = $row->id_customer;
                $bills_obj->id_user = $row->id_order;
                $bills_obj->name_user = $row->name_user;

                $bills_obj->iva = $row->iva;

                $bills_obj->payment_method = $row->payment_method;
                $bills_obj->notes = $row->notes;
                $bills_obj->state = $row->state;

                $bills_obj->customer_name = $row->customer_name;
                $bills_obj->customer_mail = $row->customer_mail;

                $bills_obj->customer_address = $row->customer_address;
                $bills_obj->customer_location = $row->customer_location;
                $bills_obj->customer_province = $row->customer_province;
                $bills_obj->customer_zip_code = $row->customer_zip_code;
                $bills_obj->customer_dni = $row->customer_dni;
                $bills_obj->customer_phone = $row->customer_phone;

                $bills_obj->customer_iva = $row->customer_iva;
                $bills_obj->customer_iban = $row->customer_iban;
                $bills_obj->customer_bank = $row->customer_bank;
                $bills_obj->customer_office_bank = $row->customer_office_bank;
                $bills_obj->customer_digital_control = $row->customer_digital_control;
                $bills_obj->customer_bank_count = $row->customer_bank_count;

                $bills_obj->work_location_address = $row->work_location_address;
                $bills_obj->work_location_location = $row->work_location_location;
                $bills_obj->work_location_province = $row->work_location_province;
                $bills_obj->work_location_zip_code = $row->work_location_zip_code;

                $bills_obj->rates_name = $row->rates_name;

                $bills_obj->service_name = $row->service_name;
                $bills_obj->service_name = $row->service_name;

                $bills_obj->retainer_amount = $row->retainer_amount;
                $bills_obj->notes = $row->notes;
                $bills_obj->billable = $row->billable;
                $bills_obj->bills_supplements = $row->bills_supplements;
                $bills_obj->active = $row->active;

                $gross_total = $sum_container_price + $pvp_edit_total_supplement_total;
                $sum_all_discount =  $sum_price_discount + $sum_price_discount_supplements;
                $sum_taxable_base = $gross_total - $sum_all_discount;


                $bills_obj->sum_dto = $sum_price_discount + $sum_price_discount_supplements;
                $bills_obj->gross_total = $gross_total;
                $bills_obj->taxable_base = $sum_taxable_base;


                $iva = $row->iva;

                $cuota =  $sum_taxable_base * ((int)$iva / 100);

                $total_con_iva = $cuota + $sum_taxable_base;


                $charge = $total_con_iva;
                $balance = $total_con_iva;

                $bills_obj->fee = $cuota;
                $bills_obj->charge = $charge;
                $bills_obj->balance = $balance;
                $bills_obj->total_bills = $total_con_iva;
                $bills_obj->total = $total_con_iva;

                $this->billsModel->save($bills_obj);
            }




            //Servicios para guardas en tabla albaranes facturas
            $services_array = $this->servicesModel->orderBy('id_service', 'ASC')->findAll();

            $indexed_array_service = array_reduce($services_array, function ($carry1, $item1) {
                $carry1[$item1->id_service] =  $item1;

                return $carry1;
            }, []);



            foreach ($id_service as $id_b_a => $servicesId) {

                $ids =  $indexed_array_service[$servicesId]->id_service;

                //Para guardas en tabla albaranes facturas
                $data04 = [
                    'service_name' => $indexed_array_service[$servicesId]->name,
                    'service_code' => $indexed_array_service[$servicesId]->code,
                ];
                $builder04 = $db->table('tablebillsalbaranes');
                $builder04->getWhere(['id_b_a' => $id_b_a]);
                $builder04->set(
                    'service_name',
                    'service_code',
                );
                $builder04->where('id_b_a', $id_b_a);
                $builder04->update($data04);


                //-------------------- Factura  ------------- //
                $data05 = [
                    'service_name' => $indexed_array_service[$servicesId]->name,
                    'service_code' => $indexed_array_service[$servicesId]->code,
                ];
                $builder05 = $db->table('bills');
                $builder05->getWhere(['id_bills' => $id_bills]);
                $builder05->set(
                    'service_name',
                    'service_code',
                );
                $builder05->where('id_bills', $id_bills);
                $builder05->update($data05);
            }



            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',
                'title' => 'FACTURA MODIFICADA!',
                'text' => 'Factura modificada con exito.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('editBills', [$id_bills]);


        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');

            $currentMsg = [
                'type' => 'error',
                'title' => 'ATENCION!',
                'text' => 'Error al modificar la factura.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('editBills', [$id_bills]);
        }

    }



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


    public function editBillsSupl($id_bills = null)
    {


        $withoutCustomer = false;

        $customer_name = NULL;
        $customer_mail = NULL;
        $customer_address = NULL;
        $customer_location = NULL;
        $customer_province = NULL;
        $customer_zip_code = NULL;
        $customer_dni = NULL;
        $customer_phone = NULL;
        $customer_iva = NULL;
        $customer_iban = NULL;
        $customer_bank = NULL;
        $customer_office_bank = NULL;
        $customer_digital_control = NULL;
        $customer_bank_count = NULL;

        $payment_method = NULL;

        $container_residue = NULL;
        $container_m3 = NULL;
        $container_price = NULL;

        $work_location_address = NULL;
        $work_location_location = NULL;
        $work_location_province = NULL;
        $work_location_zip_code = NULL;

        $driver_name = NULL;
        $driver_phone = NULL;

        $rates_name = NULL;

        $service_name = NULL;
        $service_code = NULL;

        $vehicle_name = NULL;
        $vehicle_make = NULL;
        $vehicle_model = NULL;
        $vehicle_car_registration = NULL;



        $db = db_connect();
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $date_today = $date->format('d-m-Y');

        $id_customer = null;
        $id_work_location = null;
        $containers = null;

        $sum_price_service_select = 0;
        $id_customer = null;
        $id_work_location = null;

        $info_alb_con_ser = [];
        $array_all_containers = [];
        $array_all_cubic_meters = [];
        $id_customer = null;
        $id_work_location = null;
        $iva = 0;
        $supplements = [];
        $pvp_edit = [];
        $supplements_obj_array = [];

        $price_final = 0;

        //Suplementos

        $array_supplements = [];
        $subtotal_sum_supplements = 0;
        $price_total_base_albaranes = 0;

        $price_dto = 0;
        $price_total = 0;
        $price_total_dtos = 0;

        $anticipos = 0;
        $cargos = 0;
        $saldos = 0;
        $bank_count = 0;
        $id_alabaran = 0;
        $sum_tax_base = 0;
        $sum_dto = 0;

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
        $id_albaran = null;

        $array_all_cubic_meters = [];
        $arraysIdsAlbaranes = [];
        $supplements = [];
        $json_services = [];
        $supplements_name = null;
        $array_albaranes = [];
        $total_price_supp_final = 0;
        $services = [];
        $objeto = null;
        $services_select = [];
        $array_supplements = [];
        $array_supplements_albaranes = [];
        $idAlb = null;
        $id_last_bills_insert = null;
        $total_taxe_base_alb = 0;
        $total_sum_pvp_edit = 0;
        $total_sum_dto = 0;
        $price_total_all = 0;
        $total_albaranes = 0;
        $id_order = null;
        $tax_base = null;
        $subtotal = null;
        $total = null;
        $preprinted = null;
        $price_total_supp = null;
        $id_customer = null;
        $retainer_amount = null;
        $charge = null;
        $balance = null;
        $cuota = null;
        $total_con_iva = null;
        $supplements = null;
        $supplements_edit = [];
        $id_service = null;
        $json_supplements = [];
        $sum_price_supplements_select = null;
        $suppl_editional_existe = false;
        $payment_method_alb = null;
        $id_albaranes_selecteda_array = [];

        $supplementsObject = null;

        $tableBillsAlbaranes = [];

        $supplements_all = $this->supplementsModel->orderBy('id_supplements', 'ASC')->paginate(config('Configuration')->regPerPage);
        // Trae los albaranes seleccionados
        $bills = $this->billsModel->where('id_bills', $id_bills)->paginate(config('Configuration')->regBillsPage);


        foreach ($bills as $key1 => $obj) {

            $supplements = $obj->json_supplements_aditional;


            $id_customers = $obj->id_customers;
            $customer_name = $obj->customer_name;
            $customer_mail = $obj->customer_mail;
            $customer_address = $obj->customer_address;
            $customer_location = $obj->customer_location;
            $customer_province = $obj->customer_province;
            $customer_zip_code = $obj->customer_zip_code;
            $customer_dni = $obj->customer_dni;
            $customer_phone = $obj->customer_phone;
            $customer_iva = $obj->customer_iva;

            $work_location_address = $obj->work_location_address;
            $work_location_location = $obj->work_location_location;
            $work_location_province = $obj->work_location_province;
            $work_location_zip_code = $obj->work_location_zip_code;

            $exit_supplements = true;

            $bill_supl = $this->billsModel->where('id_bills', $id_bills)->first();



            if ($supplements !== null) {

                $exit_supplements = true;
                $json_supplements = json_decode($supplements);

                $formatedSupplements = [];

                foreach ($supplements_all as $x) {
                    $stdClass = new stdClass();
                    $stdClass->id_supplements = $x->id_supplements;
                    $stdClass->name = $x->name;
                    $stdClass->dto = $x->dto;
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
                    $supplementsObject->dto = $item->dto;
                    $supplementsObject->pvp_edit = $item->pvp_edit;
                    $supplementsObject->price_dto =  $item->price_dto;
                    $supplementsObject->price_total = $item->price_total;
                    $supplementsObject->active = true;

                    $supplementsObjectArray[$item->name] = $supplementsObject;
                }


                $no_supplements = false;
                $supplements_edit = array_merge($formatedSupplements, $supplementsObjectArray);
            } else {
                $supplements_edit = 0;
            }


            // $id_albaranes_selected = $this->albaranesModel->join('tablebillsalbaranes', 'tablebillsalbaranes.id_albaran = albaranes.id_albaran')->where('tablebillsalbaranes.id_bills', $id_bills)->findAll();
        }




        return $this->twig->render('Front/Bills/editSupplements.html.twig', [



            'supplements_edit' => $supplements_edit,

            'id_customers' => $id_customers,
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

            'work_location_address' => $work_location_address,
            'work_location_location' => $work_location_location,
            'work_location_province' => $work_location_province,
            'work_location_zip_code' => $work_location_zip_code,
            'id_work_location' => $id_work_location,


            'retainer_amount' => $retainer_amount,
            'payment_method' => $payment_method,

            'charge' => $charge,
            'balance' => $balance,
            'bills' => $bills,

            'id_last_bills_insert' => $id_last_bills_insert,

            'sum_dto' => $sum_dto,
            'price_final' => $price_final,
            'bank_count' => $bank_count,
            'supplements_obj_array' => $supplements_obj_array,

            'bills' => $bills,

            'iva' => $iva,
            'sum_tax_base' => $sum_tax_base,
            'total_con_iva' => $total_con_iva,
            'cuota' => $cuota,

            'price_total_all' => $price_total_all,

            'pager' => $this->billsModel->pager->links()



        ]);
    }




    public function editSaveBillsSupplements()
    {

        //Suplementos
        $selected_supplements = [];
        $array_supplements = [];

        $total_sum_supplementos = 0;

        $price = null;
        $id_supplements_obj = null;
        $array = [];
        $array_service = [];
        $services_price_final = null;
        $id_work_locations_selected = null;

        $sum_price_supplements_select = 0;
        $myJSON = null;

        $supplements = [];
        $supplements_obj_array = [];

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

        $price_total_supp = 0;
        $price_total_all_supp = 0;
        $tax_base_original = 0;


        $id_customer = null;
        $cuota = null;
        $price_total_all = null;

        $array_id_supplements = [];

        $db = db_connect();
        $data = [];

        $id_containers_array = [];
        $id_container_new = [];

        $_SESSION['idUser'];
        $idUser = session()->get('idUser');
        $name_user = session()->get('name');


        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $supplements_id = $this->request->getPost('supplements_id');
        $supplements_name = $this->request->getPost('supplements_name');
        $pvp_edit = $this->request->getPost('pvp_edit');
        $supplement_dto = $this->request->getPost('supplement_dto');

        $id_bills = $this->request->getPost('id_bills');
        $c_iva = $this->request->getPost('c_iva');


        $id_table = $this->request->getPost('id_b_a');

        try {

            if ($supplements_id) {

                foreach ($supplements_id as $key => $id) {

                    if ($pvp_edit !== null) {

                        for ($i = 0; $i < count($pvp_edit); $i++) {
                            if ($pvp_edit[$i] === null || $pvp_edit[$i] === "0.00" || $pvp_edit[$i] === "" || $pvp_edit[$i] === "0") {
                                $pvp_edit = 0;
                                $pvp_edit[$i] = $pvp_edit;
                            }
                        }
                    }

                    if ($pvp_edit !== null) {
                        for ($i = 0; $i < count($supplement_dto); $i++) {
                            if ($supplement_dto[$i] === null || $supplement_dto[$i] === "0.00" || $supplement_dto[$i] === "" || $supplement_dto[$i] === "0") {
                                $dto = 0;
                                $supplement_dto[$i] = $dto;
                            }
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
                }




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
                    $price_total_all_supp += $price_total_supp; //270


                    $supplemen->name = $supplements_name[$id_supplements->id_supplements];

                    if ($total_sum_pvp_edit === null || $total_sum_dto  === null) {
                        $total_sum_dto = 0;
                    }

                    $total_sum_supplementos = $total_sum_pvp_edit - $total_sum_dto;

                    $supplemen->price_total_all_supp =  $total_sum_supplementos;

                    $array[] = $supplemen;
                    $supplements = json_encode($array);
                }
                //---------------------------------------------------------------------
                if ($c_iva === "21") {
                    $cuota =  $total_sum_supplementos * 0.21;
                }
                if ($c_iva === "10") {
                    $cuota =  $total_sum_supplementos * 0.10;
                }
                if ($c_iva === "4") {
                    $cuota =  $total_sum_supplementos * 0.04;
                }

                $total_con_iva = $cuota + $total_sum_supplementos;

                $total = $total_con_iva;
                $charge = $total_con_iva;
                $balance = $total_con_iva;


                //Tabla Faccturas
                $data = [
                    'fee' => $cuota,
                    'name_user' => $name_user,

                    'iva' => $c_iva,
                    'subtotal' => $price_total_all_supp,
                    'gross_total' => $price_total_all_supp,
                    'taxable_base' => $price_total_all_supp,
                    'total' => $total,
                    'subtotal_sum_supplements' => $total_sum_supplementos,
                    'price_total_supp' => $price_total_all,
                    'total_bills' => $total_con_iva,
                    'charge' => $total_con_iva,
                    'balance' => $total_con_iva,

                    'json_supplements_aditional' => $supplements,
                    'updated_at' => $date->format('Y-m-d'),
                ];
                $builder = $db->table('bills');
                $builder->getWhere(['id_bills' => $id_bills]);
                $builder->set(
                    'fee',
                    'name_user',

                    'iva',
                    'subtotal',
                    'gross_total',
                    'taxable_base',
                    'total',
                    'subtotal_sum_supplements',
                    'price_total_supp',
                    'total_bills',
                    'charge',
                    'balance',

                    'json_supplements_aditional',
                    'updated_at'
                );
                $builder->where('id_bills', $id_bills);
                $builder->update($data);
            }


            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',
                'title' => 'FACTURA MODIFICADA!',
                'text' => 'Factura modificada con exito.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('editBillsSupplements', [$id_bills]);



        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());


            $previousMsg = $this->session->getFlashdata('msg2');

            $currentMsg = [
                'type' => 'error',
                'title' => 'ATENCION!',
                'text' => 'Error al modificar la factura.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('editBillsSupplements', [$id_bills]);


        }
    }

    public function rectifyBills($id_bills)
    {

        $db = db_connect();
        $bills = new Bills();
        $id = 0;
        $n_b = 0;

        $bills_  = $this->billsModel->where('id_bills', $id_bills)->findAll();

        foreach ($bills_ as $key => $id) {
            $n_b = $id->num_bill;
        }


        //Obtén el registro original que deseas duplicar
        $registroOriginal = $this->billsModel->find($id_bills);

        //Copia los datos del registro original en un nuevo objeto
        $nuevoRegistro = clone $registroOriginal;


        /*-----------------------*/
        $year = date('Y'); // Obtener el año actual
        $year = (int)$year;

        //Obtenemos el registro de la tabla last bill con el año vigente
        $lastBill = $this->lastBillsModel->where('num_year', $year)->first();
        //A ese valor le agregamos uno
        $lastBill->num_bill = $lastBill->num_bill + 1;

        //Tabla lastBills
        $this->lastBillsModel->save($lastBill);

        //Factura Original  BILLS
        $nuevoRegistro->id_bill_original = $n_b;
        $nuevoRegistro->num_bill = $lastBill->num_bill;
        $nuevoRegistro->rectifyBills = "R";
        $nuevoRegistro->words_num_bill = "R";

        // Establece el ID del nuevo registro en nulo para que se cree uno nuevo en la base de datos
        $nuevoRegistro->id_bills = null;

        // Guarda el nuevo registro en la base de datos
        $this->billsModel->insert($nuevoRegistro);



        //Actualizamos la factura original el campo rectifyBills
        $data = [
            'rectifyBills' => "R",
        ];
        $builder = $db->table('bills');
        $builder->getWhere(['id_bills' => $id_bills]);
        $builder->set(
            'rectifyBills',
        );
        $builder->where('id_bills', $id_bills);
        $builder->update($data);

        try {

            return redirect()->route('listBills')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Factura de Rectifiacion o Abono creada con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listBills')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al crear la Factura de Rectifiacion o Abono']
            ]);
        }
    }





    public function remesas()
    {

        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->findAll();
        $payment_method_all = $this->paymentMethodModel->orderBy('id_payment_method', 'DESC')->findAll();

        $created_at = $this->request->getGet('created_at');


        $payment_method = $this->request->getGet('pm');



        if (count($_GET) > 0) {

            $bills = $this->billsModel->select('*');

            if (!empty($created_at)) {
                $bills->where('created_at', $created_at);
            }

            if (!empty($payment_method)) {
                $bills->where('payment_method', $payment_method);
            }

            $bills = $this->billsModel->paginate(config('Configuration')->regListBillsPage);
            // dd($albaranes );

        } else {
            $bills = $this->billsModel->orderBy('id_bills', 'DESC')->paginate(config('Configuration')->regListBillsPage);
        }


        return $this->twig->render('Front/Bills/Remesas.html.twig', [
            'bills' => $bills,
            'payment_method_all' => $payment_method_all,
            'customers_all' => $customers_all,
            'pager' => $this->billsModel->pager->links()
        ]);
    }







    function createRemesas()
    {



        //Transaccion conecion
       $db = \Config\Database::connect();
      //  $db->transStart();

        $num_trans = 0;
        $ids_bills = [];
        $idsBills_and_dateBills_effect= [];
        $total = 0;
        $bills_total = 0;
        $recurrent_date = null;
        $fechaActual = null;
        $customer = [];

        $bills_seleted = $this->request->getPost('bills');
        $current_date = $this->request->getPost('current_date');
        $bank = $this->request->getPost('bank');


        //Descomentar

      //  $db->transBegin();

        $bills_seleted_all = $this->billsModel->whereIn("id_bills", $bills_seleted)->findAll();

        try {
        //Transaccion conecion
        $remesas =new Remesas();

            foreach ($bills_seleted_all as $key => $obj) {

                $bills = new Bills();
                $currentDateTime = Time::now('Europe/Madrid', 'en_US');
                $bills->created_at = $currentDateTime;
                $bills->id_bills = $obj->id_bills;
                $total += $obj->total;

                $bills_total = $obj->total;
                $customer[] = $obj->id_customer;

               $bills->remesas = "SI";
               $this->billsModel->save($bills);

                $num_trans++;

                $created_at = $obj->created_at;
                $created_at = explode(' ', $created_at);
                // Toma solo la primera parte que representa la fecha
                $dateOnly = $created_at[0];
                $dateOnly = str_replace(['-', ' ', ':'], '', $dateOnly);


                $bills = new Bills();
                $bills->num_bill = $obj->num_bill;
                $array_numBill[] = $bills;


            }



            $json_numBills = json_encode($array_numBill);

            // Asignar el JSON al campo 'remesas' en el objeto $remesas
            $remesas->id_bills = $json_numBills;
            $remesas->created_a =$created_at;
            // Guardar el objeto $remesas en la base de datos
            $this->remesasModel->save($remesas);
            $id_last_remesa_insert = $db->insertID($remesas); //Ultimo id pallet insertado


            $sepaHeader = new SepaHeader($id_last_remesa_insert,$num_trans,$total);

            $SepaPaymentInformation = new SepaPaymentInformation($bills_seleted_all,$bank,$total,$num_trans,$bills_total,$id_last_remesa_insert);

            $sepa = new Sepa($sepaHeader, $SepaPaymentInformation);


            $sepa->create();
            $sepa->saveFile($bank,$id_last_remesa_insert);


            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',
                'title' => 'FICHERO CREADO CON EXITO!',
                'text' =>  'El fichero se ha generado con exito.',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

         //   $this->downloadFileRemesas($sepa,$bank);

         return redirect()->route('listRemesas');

        } catch (\Exception $e) {

            //Transaccion conecion
            //$db->transRollback();

            $previousMsg = $this->session->getFlashdata('msg');

            $currentMsg = [
                'type' => 'error',
                'title' => 'ERROR!',
                'text' => "Ocurrió un error al generar el fichero.",
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }


            //Transaccion conecion
            //$db->transComplete();


            return redirect()->route('listRemesas');
        }


    }




    public function listRemesas()
    {
        // Ruta de la carpeta de archivos descargados
        $ruta = FCPATH . 'remesas/';

        // Obtener una lista de archivos en la carpeta con información adicional
        $archivosConContenido = [];

        if ($handle = opendir($ruta)) {
            while (false !== ($archivo = readdir($handle))) {
                // Ignorar "." y ".."
                if ($archivo != "." && $archivo != "..") {
                    $rutaArchivo = $ruta . $archivo;
                    // Obtener la fecha de creación del archivo
                    $fechaCreacion = filectime($rutaArchivo);
                    // Obtener el contenido del archivo
                    $contenido = file_get_contents($rutaArchivo);
                    // Almacenar el archivo junto con su información en el array
                    $archivosConContenido[] = [
                        'nombre' => $archivo,
                        'fecha_creacion' => $fechaCreacion,
                        'contenido' => $contenido
                    ];
                }
            }
            closedir($handle);
        }

        // Ordenar el array de archivos por fecha de creación en orden descendente
        usort($archivosConContenido, function ($a, $b) {
            return $b['fecha_creacion'] - $a['fecha_creacion'];
        });

        return $this->twig->render('Front/Bills/ListRemesas.html.twig', [
            'archivosConContenido' => $archivosConContenido
        ]);
    }



    public function downloadFileRemesas()
    {
        $params = [];
        $name = $this->request->getPost('name');
        $content = $this->request->getPost('content');

        $file_path = 'remesas/' . $name;

        try {
            $headers = [
                "Content-Type" => "application/xml",
                "Content-Disposition" => "attachment; filename=\"" . $name . "\"",
                "Content-Transfer-Encoding" => "binary",
                "Content-Length" => mb_strlen($content),
                "Connection" => "close"
            ];

            foreach ($params as $k => $v) {
                $headers[$k] = $v;
            }

            foreach ($headers as $k => $v) {
                header(sprintf("%s: %s", $k, $v));
            }


                // Descarga el archivo
            if (readfile($file_path)) {
                readfile($file_path);
               // return redirect()->route('remesas');
                exit();

            }else{
               exit();
            }


        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
        }


    }



}
