<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Controllers\CustomersController;
use App\Controllers\Front\PDF_LibreryClass;
use App\Controllers\Front\PDF_Albaranes;
use App\Controllers\Front\PDF_Bills;

use DateTime;
use DateTimeZone;
use Config\Services;

use App\Entities\Customers;
use App\Entities\Config;
use App\Entities\Albaranes;
use App\Libraries\Log;
use App\Libraries\FPDF\FPDF;


use App\Models\CustomersModel;
use App\Models\ConfigModel;
use App\Models\AlbaranesModel;

use CodeIgniter\I18n\Time;
use PDO;

use App\Entities\PersonContact;
use App\Models\PersonContactModel;

use App\Entities\Bills;
use App\Models\BillsModel;


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

use App\Entities\TableBillsAlbaranes;
use App\Models\TableBillsAlbaranesModel;

use stdClass;


class FpdfController extends BaseController
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
    protected $warehousesModel;
    protected $paymentMethodModel;
    protected $ratesModel;
    protected $supplementsModel;
    protected $actualstateModel;
    protected $driver;
    protected $retainerModel;
    protected $billsModel;

    protected $tableBillsAlbaranesModel;

    protected $filepath;
    protected $text;
    protected $size;
    protected $orientation;
    protected $code_type;
    protected $print;
    protected $sizefactor;

    public function __construct()
    {
        $this->log = new Log('Albaranes/');
        $this->albaranesModel = model('AlbaranesModel');
        $this->personContactModel = model('PersonContactModel');
        $this->customersModel = model('CustomersModel');
        $this->driversModel = model('Drivers_Model');
        $this->servicesModel = model('Services_Model');
        $this->workLocationModel = model('WorkLocationModel');
        $this->vehiclesModel = model('VehiclesModel');
        $this->containerModel = model('ContainersModel');
        $this->warehousesModel = model('WarehousesModel');
        $this->paymentMethodModel = model('PaymentMethodModel');
        $this->ratesModel = model('RatesModel');
        $this->supplementsModel = model('SupplementsModel');
        $this->actualstateModel = model('ActualStateModel');
        $this->retainerModel = model('RetainerModel');

        $this->tableBillsAlbaranesModel = model('TableBillsAlbaranesModel');

        $this->billsModel = model('BillsModel');

        helper('filesystem');
    }

    // Funcion muestra la pagina pagina LECTOR CODIGO  ------
    public function  imprimePdfAlbaran($id_albaran)
    {


        $id_customer = 0;
        $id_container = null;
        $id_vehicle = null;
        $id_driver = null;
        $id_service = null;
        $form_of_payment = null;
        $id_rates = null;
        $id_work_location = null;
        $id_warehouse = null;
        $id_retainer = null;
        $albaran_status = null;


        $customers_names = null;
        $customers_email = null;
        $customers_phone = null;
        $customers_address = null;
        $customers_location = null;
        $customers_zip_code = null;
        $worklocations_address = null;
        $worklocations_location = null;
        $worklocations_province = null;
        $worklocations_zip_code = null;
        $drivers_name = null;
        $drivers_phone = null;
        $rate_name = null;
        $drivers_car_registration = null;
        $id_sp = null;
        $id = null;
        $supplements_concept = null;
        $supplements_pvp = null;
        $rates_id = null;

        $array = [];
        $supplements_descriptions = [" - ", " - ", " - ", " - ", " - "];
        $services_descriptions = [" - ", " - ", " - ", " - ", " - "];

        $supplements = [];
        $supplements2 = [];
        $array_supplements = [];
        $array_services = [];
        $array_supplements_concept = [];
        $array_supplements_pvp = [];
        $array_name = [];
        $array_name_services = [];
        $array_pvp_sum = 0;
        $suma_price_service = 0;
        $sum_price_supplements_select = 0;
        $sum_price_service_select = 0;
        $id_payment_method = 0;
        $id_payment = 0;

        $subtotal = 0;

        $suppl_1 = "-";
        $suppl_2 = "-";
        $suppl_3 = "-";
        $suppl_4 = "-";
        $suppl_5 = "-";

        $name_service = null;

        $payment_method = $this->paymentMethodModel->orderBy('id_payment_method', 'ASC')->find();
        $vehicles = $this->vehiclesModel->orderBy('id_vehicle', 'DESC')->find();


        $actualstate = $this->actualstateModel->where('id_customer', $id_customer)->find();
        $container = $this->containerModel->where('id_customer', $id_customer)->find();

        $albaran = $this->albaranesModel->where('id_albaran', $id_albaran)->find();

        foreach ($albaran as $item) {
            $id_customer = $item->id_customer;
            $id_container = $item->id_container;
            $id_vehicle = $item->id_vehicle;
            $id_driver = $item->id_driver;
            $id_service = $item->id_service;
            $form_of_payment = $item->form_of_payment;
            $id_rates = $item->id_rates;
            $id_work_location = $item->id_work_location;
            $id_retainer = $item->id_retainer;
            $date = $item->created_at;
            $albaran_status = $item->albaran_status;
            $tax_base = $item->tax_base;
            $iva = $item->iva;
            $total = $item->total;
            $subtotal = $item->subtotal;
            $retainer_amount = $item->retainer_amount;
            $id_payment_method = $item->form_of_payment;
        }

        $services_ = $this->servicesModel->where('id_service', $id_service)->find();
        foreach ($services_ as $s) {
            $name_service = $s->name;

        }

        $customers = $this->customersModel->where('id_customer', $id_customer)->find();
        foreach ($customers as $c) {
            $id_customer = $c->id_customer;
            $customers_names = $c->names;
            $customers_email = $c->mail;
            $customers_phone = $c->phone;
            $customers_address = $c->address;
            $customers_location = $c->location;
            $customers_zip_code = $c->zip_code;
        }

        $payment = $this->paymentMethodModel->where('id_payment_method', $id_payment_method)->find();
        foreach ($payment as $fp) {
            $id_payment = $fp->name;
        }

        // Para pasar a minúsculas
        $customers_names = strtolower($customers_names);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customers_names = ucfirst($customers_names);
        // Para pasar a minúsculas
        $customers_address = strtolower($customers_address);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customers_address = ucfirst($customers_address);
        // Para pasar a minúsculas
        $customers_location = strtolower($customers_location);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customers_location = ucfirst($customers_location);


        $worklocations = $this->workLocationModel->where('id_customer', $id_customer)->find();
        foreach ($worklocations as $wl) {
            $worklocations_address = $wl->address;
            $worklocations_location = $wl->location;
            $worklocations_province = $wl->province;
            $worklocations_zip_code = $wl->zip_code;
        }

        // Para pasar a minúsculas
        $worklocations_address = strtolower($worklocations_address);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_address = ucfirst($worklocations_address);
        // Para pasar a minúsculas
        $worklocations_location = strtolower($worklocations_location);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_location = ucfirst($worklocations_location);
        // Para pasar a minúsculas
        $worklocations_province = strtolower($worklocations_province);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_province = ucfirst($worklocations_province);

        $drivers = $this->driversModel->where('id_driver', $id_driver)->find();
        foreach ($drivers as $dr) {
            $drivers_name = $dr->name;
            $drivers_phone = $dr->phone;
        }
        // Para pasar a minúsculas
        $drivers_name = strtolower($drivers_name);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $drivers_name = ucfirst($drivers_name);

        $rates = $this->ratesModel->where('id_rates', $id_rates)->find();
        foreach ($rates as $rate) {
            $rate_name = $rate->name;

        }


        $vehicles = $this->vehiclesModel->where('id_vehicle', $id_vehicle)->find();
        foreach ($vehicles as $vehicle) {
            $drivers_car_registration = $vehicle->car_registration;
        }


        // Para pasar a minúsculas
        $rate_name = strtolower($rate_name);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $rate_name = ucfirst($rate_name);

        $containers = $this->containerModel->where('id_container', $id_container)->find();
        foreach ($containers as $container) {

            $container_residue = $container->residue;
        }

        $albaran = $this->albaranesModel->where('id_albaran', $id_albaran)->first();

        /**
         * Suplementos -----------------------------------------------------------
         *
         */
        $supplements_obj = $this->supplementsModel->orderBy('id_supplements', 'ASC')->find();

        $json_supplements = json_decode($albaran->supplements);
        foreach ($json_supplements as $item) {

            $id_sp = $item->id_supplements;
            $pvp = $item->pvp;

            $supplements_pvp[] = $pvp;
            $supplements[] = $this->supplementsModel->find($id_sp);
        }

        //Si coincide el id selrvicio con los selecionados suma los valores de los campos pvp
        foreach ($supplements_pvp as $key => $supplements_id_) { //id seleccionados

            foreach ($supplements_obj as $key2 => $supplements_pvp_2) {

                if ($key === $key2) {
                    $sum_price_supplements_select += $supplements_id_;
                }
            }
        }

        $json_supplements = json_decode($albaran->supplements);
        foreach ($json_supplements as $key => $value) {
            # code...
            $id_supplements = $value->id_supplements;
            $array_supplements[] = $this->supplementsModel->where('id_supplements', $id_supplements)->first();
        }
        $count = count($array_supplements);

        $icon_euro = '€';
        $str = iconv('UTF-8', 'windows-1252', $icon_euro);



        //Obtenemos los names y pvp de supplements
        foreach ($array_supplements as $item) {

            $name = $item->name;
            $pvp = $item->pvp;
            $array_name[] = $name . '  ' . $str . '  ' . $pvp;
        }

        for ($i = 0; $i < $count; $i++) {
            $supplements_descriptions[$i] =  $array_name[$i];
        }

        $supplements_descriptions_2 = [];

        foreach ($supplements_descriptions as $supplements_i) {
            $supplements_descriptions_2[] = $supplements_i;
        }




        $pdf = new PDF_Albaranes();
        $pdf->AddPage();

        $pdf->SetDrawColor(0, 111, 46);
        $pdf->SetTextColor(0, 111, 46);
        $pdf->SetFont('Arial', 'B', 15.5, 'C');


        /**Lineas */

        $pdf->Line(200, 8, 200, 60); //Vertical 2
        $pdf->Line(10, 8, 10, 80); //Vertical 1

        $pdf->Line(144.2, 8, 144.2, 55); //Vertical
        $pdf->Line(10, 8, 200, 8);  //Horizontal  1

        $pdf->Line(200, 16,144.5,16);  //Horizontal  1



        /*---*/


        $pdf->Cell(47, 7, '', 0, 0);
        $pdf->Cell(88, 8, 'TOT PER CONSTRUIR 2012, S.L.', 0, 0);
        $pdf->SetFillColor(221, 246, 233);

        $pdf->SetFont('Arial', 'B', 14);

        $pdf->Cell(40, 5, 'ALBARAN: ', 0, 0);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(22, 5, ' ' . $id_albaran, 0, 1); //end of line

        $pdf->Cell(150, 3, '', 0, 1); //end of line
        $pdf->SetFont('Arial', '', 9);

        $pdf->Cell(60, 7, '', 0, 0);
        $pdf->Cell(75, 5, 'C.I.F. B-98474497 NIMA: 4600018805', 0, 0);


        $pdf->Cell(135, 7, '', 0, 1);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(135, 7, '', 0, 0);
        $pdf->Cell(20, 5, 'Id Cliente:', 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(22, 5, $id_customer, 0, 1); //end of line

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(135, 5, 'Direccion: Calle Rio Guadalaviar, 6. Quart de Poblet (Valencia).', 0, 0);
        $pdf->Cell(20, 5, 'Fecha: ', 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(22, 5, ' ' . $date, 0, 1); //end of line

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(135, 5, 'CP: 46930. Telefono: 961391755 656414748', 0, 0);
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(20, 5, 'Matricula: ', 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(22, 5, $drivers_car_registration, 0, 1); //end of line

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(135, 5, 'Correo electronico: gestion@tpc2012.es', 0, 0);
        $pdf->Cell(35, 5, 'Forma de pago:', 0, 0);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(35, 5, $id_payment, 0, 1); //end of line

        $pdf->SetFont('Arial', '', 12);

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(135, 5, 'Pagina web: www.tpc2012.es', 0, 0);

        $pdf->SetFont('Arial', 'B', 12);
        $total_title = 'COBRO';
        $w07 = $pdf->GetStringWidth($total_title) + 17.5;
        $pdf->Cell($w07, 8, $total_title, 0, 0, 'L', 1);
        $pdf->SetFont('Arial', 'B', 12);

        $w08 = $pdf->GetStringWidth($str) + 19;
        $pdf->Cell($w08, 8, $str . ' ' . $total, 0, 1, 'L', 1);

        $pdf->SetFont('Arial', 'B', 12);

        /* Direccion de Obra*/
        $title = 'Direccion de Obra';
        $pdf->SetFont('Arial', '', 12);
        $w = $pdf->GetStringWidth($title) + 155.80;
        $pdf->SetFillColor(221, 246, 233);

        $pdf->Cell(5, 2, '', 0, 1);
        $pdf->Cell($w, 8, $title, 1, 1, 'C', 1);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(90, 0, '', 0, 1);
        $pdf->Cell(0, 8, 'Direccion: ' . $worklocations_address . ',' .  $worklocations_location . ',' . $worklocations_province . ',' . $worklocations_zip_code, 1, 0);
        $pdf->Cell(189.50, 2, '', 0, 1);

        /* Transportista*/
        $title = 'Transportista productor y negociante de residuos no peligrosos ';
        $pdf->SetFont('Arial', '', 12);
        $w = $pdf->GetStringWidth($title) + 70.50;
        $pdf->SetFillColor(221, 246, 233);

        $pdf->Cell(5, 8, '', 0, 1);
        $pdf->Cell($w, 8, $title, 1, 1, 'C', 1);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(90, 0, '', 0, 1);
        $pdf->Cell(0, 8, $drivers_name . ', Telefono ' . $drivers_phone, 1, 0);
        $pdf->Cell(189.50, 2, '', 0, 1);


         /* Servicios*/
         $title_ser = 'Servicios';
         $pdf->SetFont('Arial', '', 12);
         $w2 = $pdf->GetStringWidth($title_ser) + 173.00;
         $pdf->SetFillColor(221, 246, 233);

         $pdf->Cell(5, 8, '', 0, 1);
         $pdf->Cell($w2, 8, $title_ser, 1, 1, 'C', 1);
         $pdf->SetFont('Arial', '', 9);
         $pdf->Cell(90, 0, '', 0, 1);
         $pdf->Cell(0, 8, $name_service, 1, 0);
         $pdf->Cell(189.50, 10, '', 0, 1);

         $pdf->SetFont('Arial', '', 12);
        /* Suplementos*/
        $title_supp = 'Suplementos';
        $sup = $pdf->GetStringWidth($title_supp) + 165.30;
        $pdf->SetFillColor(221, 246, 233);


        //Title
        $pdf->Cell($sup, 8, $title_supp, 1, 1, 'C', 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(38, 8, $supplements_descriptions_2[0], 1, 0, 'C');
        $pdf->Cell(38, 8, $supplements_descriptions_2[1], 1, 0, 'C');
        $pdf->Cell(38, 8, $supplements_descriptions_2[2], 1, 0, 'C');
        $pdf->Cell(38, 8, $supplements_descriptions_2[3], 1, 0, 'C');
        $pdf->Cell(38, 8, $supplements_descriptions_2[4], 1, 1, 'C');

        $pdf->Cell(189, 2, '', 0, 1);

        /* Contenedor*/
        $pdf->SetFont('Arial', '', 12, 'C');
        $title_cont = 'Contenedor ';
        $context = $pdf->GetStringWidth($title_cont) + 52.70;
        $pdf->SetFillColor(221, 246, 233);
        $pdf->Cell($context, 8, $title_cont, 1, 0, 'C', 1);

        $title_hl = 'Hora LLegada';
        $w4 = $pdf->GetStringWidth($title_hl) + 10.90;
        $pdf->SetFillColor(221, 246, 233);
        //Title
        $pdf->Cell($w4, 8, $title_hl, 1, 0, 'C', 1);

        $title_hs = 'Hora Salida';
        $w5 = $pdf->GetStringWidth($title_hs) + 15.90;
        $pdf->SetFillColor(221, 246, 233);
        //Title
        $pdf->Cell($w5, 8, $title_hs, 1, 0, 'C', 1);

        $title_tar = 'Tarifa';
        $w6 = $pdf->GetStringWidth($title_tar) + 27.3;
        $pdf->SetFillColor(221, 246, 233);
        //Title
        $pdf->Cell($w6, 8, $title_tar, 1, 1, 'C', 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->Cell(76, 8, $container_residue, 1, 0, 'C');

        $pdf->Cell(37.9, 8, ' ', 1, 0, 'C');
        $pdf->Cell(38, 8, ' ', 1, 0, 'C');

        // Para pasar a minúsculas
        $id_rates = strtolower($id_rates);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $id_rates = ucfirst($id_rates);

        $pdf->Cell(38.1, 8, $id_rates, 1, 1, 'C');

        $pdf->Cell(189, 2, '', 0, 1);

        $pdf->SetFont('Arial', '', 11);

        $pdf->Cell(50, 15, 'Observaciones', 1, 0, 'C');
        $pdf->Cell(64, 15, 'Nombre y Firma del conductor', 1, 0, 'C');


        $pdf->Multicell(76.5, 5, utf8_decode("Conforme, soy conocedor y acepto  las \n condiciones generales del presente,\n El cliente"), 1, 'l', false);

        $pdf->Cell(50, 30, '', 1, 0);
        $pdf->Cell(64, 30, '', 1, 0);
        $pdf->Multicell(76.5, 30, utf8_decode(""), 1, 'l', false);

        /* Direccion de Obras*/
        $pdf->Cell(5, 0, '', 0, 1);
        $pdf->SetFont('Arial', '', 9);
        $pdf->Multicell(190.50, 5, iconv('UTF-8', 'windows-1252', 'El cliente conoce su obligación de solicitud de ocupación de vía publica siendo responsable de los perjuicios que pudieran derivarse por la no solicitud y/o concesión de la misma eximiendo a Tot Per Construir 2012 S.L. de cualquier responsabilidad. Los residuos depositados en el contenedor son inertes y no peligrosos, según la legislación estatal y autonómica en vigor. El usuario del contenedor, se compromete a realizar las operaciones de vertido, en las condiciones exigidas por la Ordenanza Municipal de Limpieza Urbana procediendo a la inmediata limpieza de los restos caídos en la acera o en la calzada y sin sobrecargar la capacidad nominal del contenedor. Así como el tapado nocturno del contenedor para evitar derrames. También será obligatoria la señalización del contenedor por el usuario en los casos que pudiere representar peligro para el tráfico rodado, no debiendo quitarse dicha señalización hasta la retirada del contenedor. '), 1, 0);

        $pdf->Ln();
        //fin cabecera

        $pdf->Output('Mi pdf', 'I');

        return $this->twig->render('Front/Fpdf/imprimirPdf.html.twig');
    }


    /**
     * Pdf de la factura
     */
    public function  PdfBills($id_bills =null)
    {


        $customer_name_title = "";
        $id_customer = 0;
        $id_container = null;
        $id_vehicle = null;
        $id_driver = null;
        $id_service = null;
        $form_of_payment = null;
        $id_rates = null;
        $id_work_location = null;

        $id_retainer = null;
        $albaran_status = null;

        $tax_base = null;
        $price_discount = null;
        $total = null;
        $discount = null;

        $customers_names = "";
        $customers_email = null;
        $customers_phone = null;
        $customers_address = "";
        $customers_location = "";
        $customers_zip_code = null;
        $worklocations_address = "";
        $worklocations_location = "";
        $worklocations_province = "";
        $worklocations_zip_code = "";
        $drivers_name = "-";
        $drivers_phone = "0";
        $rate_name = "";
        $drivers_car_registration = null;
        $id_sp = null;
        $id = null;
        $supplements_concept = null;
        $supplements_pvp = null;
        $rates_id = null;

        $array = [];
        $supplements_descriptions = [" - ", " - ", " - ", " - ", " - "];
        $services_descriptions = [" - ", " - ", " - ", " - ", " - "];

        $supplements = [];
        $customer_iban = null;
        $customer_bank = null;
        $customer_office_bank = null;
        $customer_digital_control = null;
        $customer_bank_count = null;

        $sum_dto = null;
        $fee = null;

        $customer_name = null;


        $subtotal = 0;

        $suppl_1 = "-";
        $suppl_2 = "-";
        $suppl_3 = "-";
        $suppl_4 = "-";
        $suppl_5 = "-";

        $expiration_date = null;

        $amount_tax_base_discount = null;

        $created_at_bill = null;

        $work_location_location = null;
        $work_location_address = null;

        $container_residue = null;

        $bills = $this->billsModel->where('id_bills', $id_bills)->findAll();


        foreach ($bills as $item) {




            $id_customer = $item->id_customer;

            $payment_method = $item->payment_method;
            $customer_name = $item->customer_name;

            $customer_mail = $item->customer_mail;
            $customer_address = $item->customer_address;
            $customer_location = $item->customer_location;
            $customer_province = $item->customer_province;
            $customer_zip_code = $item->customer_zip_code;
            $customer_dni = $item->customer_dni;
            $customer_phone = $item->customer_phone;
            $customer_iva = $item->customer_iva;
            $customer_iban = $item->customer_iban;
            $customer_bank = $item->customer_bank;
            $customer_office_bank = $item->customer_office_bank;
            $customer_digital_control = $item->customer_digital_control;
            $customer_bank_count = $item->customer_bank_count;

            $worklocations_address = $item->work_location_address;
            $worklocations_location = $item->work_location_location;
            $worklocations_province = $item->work_location_province;
            $worklocations_zip_code = $item->work_location_zip_code;

            $expiration_date = $item->expiration_date;

            $total_bills = $item->total_bills;
            $charge = $item->charge;
            $balance = $item->balance;

            $fee = $item->fee;


            $sum_dto = $item->sum_dto;
            $taxable_base = $item->taxable_base;
            $gross_total = $item->gross_total;
            $price_total_supp = $item->price_total_supp;

            $retainer_amount = $item->retainer_amount;

            $drivers_name = $item->driver_name;
            $drivers_phone = $item->driver_phone;

            $rate_name = $item->rates_name;
            $created_at_bill = $item->created_at;
            $created_at_bill = date("Y-m-d", strtotime($created_at_bill));

        }

        $id_customer_expiration_date = $this->customersModel->where('id_customer', $id_customer)->find();
        foreach ($id_customer_expiration_date as $key => $rows) {
            $payment_method_customer =  $rows->payment_method;

        }
        $name_customer_expiration_date = $this->paymentMethodModel->where('id_payment_method', $payment_method_customer)->first();




        // Para pasar a minúsculas
        $customer_name_title = strtoupper($customer_name);

        // Para pasar a minúsculas
        $customer_name = strtolower($customer_name);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customer_name = ucfirst($customer_name);
        // Para pasar a minúsculas
        $customer_address = strtolower($customer_address);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customer_address = ucfirst($customer_address);
        // Para pasar a minúsculas
        $customer_location = strtolower($customer_location);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customer_location = ucfirst($customer_location);

        // Para pasar a minúsculas
        $worklocations_address = strtolower($worklocations_address);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_address = ucfirst($worklocations_address);
        // Para pasar a minúsculas
        $worklocations_location = strtolower($worklocations_location);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_location = ucfirst($worklocations_location);
        // Para pasar a minúsculas
        $worklocations_province = strtolower($worklocations_province);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_province = ucfirst($worklocations_province);


        $tableBillsAlbaranes = $this->tableBillsAlbaranesModel->where('id_bills', $id_bills)->paginate(config('Configuration')->regBillsPage);

        $pdf = new PDF_Bills();
        $pdf->AddPage();

        $pdf->SetAutoPageBreak(true, 15);
        $pdf->Header();


        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(189, 5, '', 0, 1);


        $pdf->SetTextColor(0, 111, 46);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(125, 5, 'Direccion fiscal: ', 0, 0);

        $pdf->SetTextColor(0, 111, 46);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(35, 5, 'Direccion Correspondencia: ', 0, 1);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(125, 7, $customer_name_title, 0, 0);


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(125, 7, $customer_name_title, 0, 1);
        $pdf->SetFont('Arial', '', 10);

        $worklocations_address = utf8_decode($worklocations_address);
        $worklocations_location = utf8_decode($worklocations_location);

        $pdf->Cell(125, 5, $worklocations_address, 0, 0);
        $pdf->Cell(125, 5, $worklocations_address, 0, 1);

        $pdf->Cell(125, 5,  $worklocations_location . "(" . $worklocations_province . ")", 0, 0);
        $pdf->Cell(125, 5,  $worklocations_location . "(" . $worklocations_province . ")", 0, 1);

        $pdf->Cell(115, 5, 'CP:' .$customer_zip_code.   ' Telefono: ' . $customer_phone , 0, 0);

        $pdf->Cell(135, 10, '', 0, 1);
        /**
         * Datos cliente factura
         */
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(115, 5, 'F a c t u r a', 0, 0);
        $pdf->Cell(115, 6, '', 0, 1);

        $pdf->SetFont('Arial', '', 9);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(25, 4, 'Fecha', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $created_at_bill, 0,);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 4, 'Cliente', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $id_customer, 0,);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(25, 5, 'Telefono/Fax', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $customer_phone, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(25, 5, 'Numero', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $id_bills, 0,);


        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 4, 'CIF/NIF', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4,  $customer_dni, 0, 1);


        $pdf->Cell(189, 1, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 8, 'C');
        $text_code = 'Codigo';
        $w2 = $pdf->GetStringWidth($text_code) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w2, 5, $text_code, 0, 0, 'C', 1);

        $text_cantidad = 'Cantidad';
        $w3 = $pdf->GetStringWidth($text_cantidad) + 10;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        //Title
        $pdf->Cell($w3, 5, $text_cantidad, 0, 0, 'C', 1);

        $text_descr = 'Descripcion';
        $w4 = $pdf->GetStringWidth($text_descr) + 60;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w4, 5, $text_descr, 0, 0, 'C', 1);

        $text_precio = 'Precio';
        $w5 = $pdf->GetStringWidth($text_precio) + 16;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w5, 5, $text_precio, 0, 0, 'C', 1);

        $text_dto = '% Dto.';
        $w6 = $pdf->GetStringWidth($text_dto) + 7;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w6, 5, $text_dto, 0, 0, 'C', 1);

        $text_imp = 'Imp.Dto.';
        $w7 = $pdf->GetStringWidth($text_imp) + 7;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w7, 5, $text_imp, 0, 0, 'C', 1);

        $text_impo = 'Importe';
        $w8 = $pdf->GetStringWidth($text_impo) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);


        $pdf->Cell($w8, 5, $text_impo, 0, 1, 'C', 1);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(40, 40, 40);

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

            $id_albaran = $obj->id_albaran;
            $container_residue = $obj->container_residue;
            $container_m3 = $obj->container_m3;
            $container_price = $obj->container_price;

            $discount = $obj->discount;
            $price_discount = $obj->price_discount;
            $subtotal = $obj->subtotal;

            $amount_tax_base_discount = $obj->amount_tax_base_discount;


            $work_location_location = $obj->work_location_location;
            $work_location_address = $obj->work_location_address;


            $created_at = $obj->created_at;
            $created_at = date("Y-m-d", strtotime($created_at));


            $supplements_exits = $obj->supplements_exits;



        if ($supplements !== null) {

            $json_supplements = json_decode($supplements);



                // $pdf->SetFont('Arial', '', 9);
                // $Sevicio = "Sevicio Nº (Alabaran) " .  $id_albaran . " de Fecha : " . $created_at . "      "  .$tax_base . "         "  . $discount . "            "  .$price_discount . "               "  .$amount_tax_base_discount;
                // $Sevicio = utf8_decode($Sevicio);
                // $pdf->SetFillColor(40, 255, 255);
                // $pdf->SetTextColor(40, 40, 40);
                // $w_11 = $pdf->GetStringWidth($Sevicio)  -40;
                // $pdf->SetX(($pdf->GetPageWidth() - $w_11) / 2); // Centrar el contenido horizontalmente
                // $pdf->Cell($w_11, 5, $Sevicio, 0, 1,  1);

                $pdf->Cell(22, 5, "", 0, 0,  1);
                $pdf->Cell(22, 5,"", 0, 0,  0);

                $pdf->SetFont('Arial', '', 9);
                $Sevicio = "Sevicio Nº (Alabaran) " .  $id_albaran . " de Fecha : " . $created_at;
                $Sevicio = utf8_decode($Sevicio);
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
                $pdf->Cell(75, 5, $Sevicio, 0, 0, 'C', 0);

                if( $discount === 0 || $discount === "0.00" || $discount === 0.00 || $discount === null){
                    $discount = "";
                }

                if( $price_discount === 0 || $price_discount === "0.00" || $price_discount === 0.00 || $price_discount === null){
                    $price_discount = "";
                }



                $pdf->SetFont('Arial', '', 9);
                $tax_base_text = $container_price;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);

              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $tax_base_text,  0, 0, 'C', 0);



                $pdf->SetFont('Arial', '', 9);
                $discount_text = $discount;
                $discount_text = (int)$discount;

                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $discount_text, 0, 0, 'C', 0);


                $pdf->SetFont('Arial', '', 9);
                $price_discount_text = $price_discount;
                $discount_text = (int)$discount;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $price_discount_text, 0, 0,  'C', 0);

                $pdf->SetFont('Arial', '', 9);
                $amount_tax_base_discount_text = $amount_tax_base_discount;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $amount_tax_base_discount_text, 0, 1, 'C',  0);


                $pdf->Cell(22, 5, "", 0, 0,  1);
                $pdf->Cell(22, 5,"", 0, 0,  0);

                $work_location_location = "Obra: " .  $work_location_location . " " . $work_location_address ;
                $work_location_location = utf8_decode($work_location_location);

                $pdf->Cell(75, 5, $work_location_location, 0, 1, 'C', 0);

                $pdf->Cell(22, 5, "", 0, 0, 'C');
                $pdf->Cell(22, 5,"", 0, 0, 'C');

                $container_residue = $container_residue .  " M3 " . $container_m3 ;
                $pdf->Cell(75, 5, $container_residue, 0, 1, 'C');


            foreach ($json_supplements as $key1 => $item) {

                if($item->dto === 0){
                    $item->dto ="     ";
                }

                if($item->price_dto === 0){
                    $item->price_dto ="     ";
                }

                $pdf->Cell(22, 5, "", 0, 0,  1);
                $pdf->Cell(22, 5,"", 0, 0,  0);
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);


               // $container_residue_supplements =  $item->name  .  "                                     "  . $item->pvp_edit . "         "  . $item->dto . "            "  . $item->price_dto . "                      "  .$item->price_total;

                $container_residue_supplements =  $item->name;
                $pdf->Cell(75, 5, $container_residue_supplements, 0, 0,'C', 0);


                $pdf->SetFont('Arial', '', 9);
                $pvp_edit_text = $item->pvp_edit;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
                $pdf->Cell(19, 5, $pvp_edit_text, 0, 0, 'C', 0);


            if( $item->dto === 0 || $item->dto === "0.00" || $item->dto === 0.00 || $item->dto === null){
                $item->dto = "";
            }

            if( $item->price_dto === 0 || $item->price_dto === "0.00" || $item->price_dto === 0.00 || $item->price_dto === null){
                $item->price_dto = "";
            }

                $pdf->SetFont('Arial', '', 9);
                $discount_text_suppl = $item->dto;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
                $pdf->Cell(19, 5, $discount_text_suppl, 0, 0,'C',0);


                $pdf->SetFont('Arial', '', 9);
                $dto_text_suppl = $item->price_dto;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
                $pdf->Cell(19, 5, $dto_text_suppl, 0, 0, 'C', 0);

                $pdf->SetFont('Arial', '', 9);
                $price_total_text_supp = "  " . $item->price_total;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $price_total_text_supp, 0, 1,'C', 0);


            }

            // $id_albaranes_selected = $this->albaranesModel->join('tablebillsalbaranes', 'tablebillsalbaranes.id_albaran = albaranes.id_albaran')->where('tablebillsalbaranes.num_bill', $num_bill)->findAll();
        }else{


            $pdf->Cell(22, 5, "", 0, 0,  1);
            $pdf->Cell(22, 5,"", 0, 0,  0);

            $pdf->SetFont('Arial', '', 9);
            $Sevicio = "Sevicio Nº (Alabaran) " .  $id_albaran . " de Fecha : " . $created_at;
            $Sevicio = utf8_decode($Sevicio);
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);
            $pdf->Cell(75, 5, $Sevicio, 0, 0, 'C', 0);

            if( $discount === 0 || $discount === "0.00" || $discount === 0.00 || $discount === null){
                $discount = "";
            }

            if( $price_discount === 0 || $price_discount === "0.00" || $price_discount === 0.00 || $price_discount === null){
                $price_discount = "";
            }



            $pdf->SetFont('Arial', '', 9);
            $tax_base_text = $container_price;
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);

          // Centrar el contenido horizontalmente
            $pdf->Cell(19, 5, $tax_base_text, 0, 0,  'C', 0);



            $pdf->SetFont('Arial', '', 9);
            $discount_text = $discount;
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);
          // Centrar el contenido horizontalmente
            $pdf->Cell(19, 5, $discount_text, 0, 0,  'C', 0);


            $pdf->SetFont('Arial', '', 9);
            $price_discount_text = $price_discount;
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);
          // Centrar el contenido horizontalmente
            $pdf->Cell(19, 5, $price_discount_text, 0, 0, 'C',  0);

            $pdf->SetFont('Arial', '', 9);
            $amount_tax_base_discount_text = $amount_tax_base_discount;
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);
          // Centrar el contenido horizontalmente
            $pdf->Cell(19, 5, $amount_tax_base_discount_text, 0, 1, 'C', 0);


            $pdf->Cell(22, 5, "", 0, 0,  1);
            $pdf->Cell(22, 5,"", 0, 0,  0);

            $work_location_location = "Obra: " .  $work_location_location . " " . $work_location_address ;
            $work_location_location = utf8_decode($work_location_location);

            $pdf->Cell(75, 5, $work_location_location, 0, 1, 'C', 0);

            $container_residue = $container_residue .  " M3 " . $container_m3 ;
            $w55 = $pdf->GetStringWidth($container_residue) +25;
            $pdf->SetX(($pdf->GetPageWidth() - $w55) / 2); // Centrar el contenido horizontalmente
            $pdf->Cell($w55, 5, $container_residue, 0, 1, 1);


        }

    }


        /**
         * Tabla 2 Total Bruto
         */
        $currentY = $pdf->GetY();
        // Altura de la celda deseada
        $desiredY = 50; // Posición vertical de la celda deseada en unidades

        // Verificar si es necesario realizar un salto de página
        if ($currentY + $desiredY > $pdf->GetPageHeight()) {
            $pdf->AddPage(); // Agregar una nueva página
        }



        $pdf->SetFont('Arial', 'B', 8, 'L');
        // $pdf->SetFont('Arial','I',8,'L');
        $text_total_bruto = 'Total Bruto';
        $w_2 = $pdf->GetStringWidth($text_total_bruto) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_2, 5, $text_total_bruto, 0, 0, 'C', 1);

        $text_suma_dto = 'Suma Dtos.';
        $w_3 = $pdf->GetStringWidth($text_suma_dto) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        //Title
        $pdf->Cell($w_3, 5, $text_suma_dto, 0, 0, 'C', 1);

        $text_dto_comp = '% Dto.Com.';
        $w_4 = $pdf->GetStringWidth($text_dto_comp) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_4, 5, $text_dto_comp, 0, 0, 'C', 1);

        $text_dto_pp = '% Dto.P.P.';
        $w_5 = $pdf->GetStringWidth($text_dto_pp) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_5, 5, $text_dto_pp, 0, 0, 'C', 1);

        $text_base_impo = 'BASE IMPONIBLE';
        $w_6 = $pdf->GetStringWidth($text_base_impo) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_6, 5, $text_base_impo, 0, 0, 'C', 1);

        $text_irpf = '% I.R.P.F.';
        $w_7 = $pdf->GetStringWidth($text_irpf) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_7, 5, $text_irpf, 0, 0, 'C', 1);

        $text_iva = '% I.V.A.';
        $w_8 = $pdf->GetStringWidth($text_iva) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_8, 5, $text_iva, 0, 0, 'C', 1);


        $text_cuota = 'Cuota';
        $w_9 = $pdf->GetStringWidth($text_cuota) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_9, 5, $text_cuota, 0, 0, 'C', 1);


        $text_TOTAL = 'TOTAL';
        $w_10 = $pdf->GetStringWidth($text_TOTAL) + 10;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_10, 5, $text_TOTAL, 0, 1, 'C', 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(23.05, 5, $gross_total, 0, 0, 'C');
        $pdf->Cell(23.52, 5, $sum_dto, 0, 0, 'C');
        $pdf->Cell(23.83, 5, '-', 0, 0, 'C');
        $pdf->Cell(20.11, 5, '-', 0, 0, 'C');
        $pdf->Cell(30.30, 5,  $taxable_base, 0, 0, 'C');
        $pdf->Cell(18.86, 5, '-', 0, 0, 'C');
        $pdf->Cell(16.35, 5, $fee, 0, 0, 'C');
        $pdf->Cell(15.99, 5, $customer_iva, 0, 0, 'C');
        $pdf->Cell(19.40, 5, $total_bills, 0, 1, 'C');

        $pdf->Cell(189, 5, '', 0, 1);


        if( $retainer_amount === null){
            $retainer_amount = "-";
        }

        /**
         * Anticipos
         */
        $pdf->SetFont('Arial', '', 9);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(15, 4, 'Anticipos:', 0, 0);


        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, $retainer_amount, 0,);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(12, 4, 'Cargos:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, $charge, 0,);



        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(10, 4, 'Saldo:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, $balance, 0,);


        $pdf->SetFont('Arial', 'I', 10, 'L');
        $text_TOTAL = 'TOTAL FACTURA';
        $w_10 = $pdf->GetStringWidth($text_TOTAL) + 41;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_10, 6, $text_TOTAL, 0, 0, 'L', 1);



        $text_TOTAL = $total_bills;
        $w_10 = $pdf->GetStringWidth($text_TOTAL) + 5;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_10, 6, $text_TOTAL, 0, 1, 'L', 1);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(189, 10, '', 0, 1);




        $pdf->SetFont('Arial', 'I', 10, 'L');

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Forma de Pago:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);


         $txt_trnasferencia = $name_customer_expiration_date->name ;
        $pdf->Cell(20, 6, $txt_trnasferencia, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Domiciliacion:', 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 6, $customer_iban . " " . $customer_bank . " " . $customer_office_bank . " " . $customer_digital_control . " " . $customer_bank_count, 0, 1);



        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Vencimientos:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 6, $expiration_date, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Importes:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 6, $total_bills, 0, 1);


        $pdf->Cell(5, 10, '', 0, 1);
        $pdf->SetFont('Arial', '', 8, 'L');
        $pdf->SetTextColor(0, 0, 0);



        $pdf->Ln();
        //fin cabecera
        $pdf->Output('factura.pdf', 'I');
       // $pdf->Output('D', 'archivo.pdf');

       return $pdf->Output("Front/Fpdf/imprimirPdf.html.twig", "I");
       // return redirect()->back();
    }



     /**
     * Pdf de la factura
     */
    public function  PdfBillsSupp($id_bills =null)
    {



        $customer_name_title = "";
        $id_customer = 0;
        $id_container = null;
        $id_vehicle = null;
        $id_driver = null;
        $id_service = null;
        $form_of_payment = null;
        $id_rates = null;
        $id_work_location = null;

        $id_retainer = null;
        $albaran_status = null;

        $tax_base = null;
        $price_discount = null;
        $total = null;
        $discount = null;

        $customers_names = "";
        $customers_email = null;
        $customers_phone = null;
        $customers_address = "";
        $customers_location = "";
        $customers_zip_code = null;
        $worklocations_address = "";
        $worklocations_location = "";
        $worklocations_province = "";
        $worklocations_zip_code = "";
        $drivers_name = "-";
        $drivers_phone = "0";
        $rate_name = "";
        $drivers_car_registration = null;
        $id_sp = null;
        $id = null;
        $supplements_concept = null;
        $supplements_pvp = null;
        $rates_id = null;

        $array = [];

        $sum_dto = null;
        $fee = null;

        $json_supplements_aditional = null;
        $customer_name = null;
        $subtotal = 0;

        $customer_iban = null;
        $customer_bank = null;
        $customer_office_bank = null;
        $customer_digital_control = null;
        $customer_bank_count = null;

        $created_at_bill = null;

        $work_location_location = null;
        $work_location_address = null;

        $container_residue = null;
        $expiration_date = null;

        $customer_iban = null;
        $customer_bank = null;
        $customer_office_bank = null;
        $customer_digital_control = null;
        $customer_bank_count = null;


        $bills = $this->billsModel->where('id_bills', $id_bills)->findAll();


        foreach ($bills as $item) {


            $id_customer = $item->id_customer;

            $payment_method = $item->payment_method;
            $customer_name = $item->customer_name;

            $customer_mail = $item->customer_mail;
            $customer_address = $item->customer_address;
            $customer_location = $item->customer_location;
            $customer_province = $item->customer_province;
            $customer_zip_code = $item->customer_zip_code;
            $customer_dni = $item->customer_dni;
            $customer_phone = $item->customer_phone;
            $customer_iva = $item->customer_iva;
            $customer_iban = $item->customer_iban;
            $customer_bank = $item->customer_bank;
            $customer_office_bank = $item->customer_office_bank;
            $customer_digital_control = $item->customer_digital_control;
            $customer_bank_count = $item->customer_bank_count;

            $worklocations_address = $item->work_location_address;
            $worklocations_location = $item->work_location_location;
            $worklocations_province = $item->work_location_province;
            $worklocations_zip_code = $item->work_location_zip_code;

            $total_bills = $item->total_bills;
            $charge = $item->charge;
            $balance = $item->balance;

            $expiration_date = $item->expiration_date;

            $fee = $item->fee;

            $sum_dto = $item->sum_dto;
            $taxable_base = $item->taxable_base;
            $gross_total = $item->gross_total;
            $price_total_supp = $item->price_total_supp;

            $retainer_amount = $item->retainer_amount;

            $drivers_name = $item->driver_name;
            $drivers_phone = $item->driver_phone;

            $rate_name = $item->rates_name;
            $created_at_bill = $item->created_at;
            $created_at_bill = date("Y-m-d", strtotime($created_at_bill));



        }

        $id_customer_expiration_date = $this->customersModel->where('id_customer', $id_customer)->find();
        foreach ($id_customer_expiration_date as $key => $rows) {
            $payment_method_customer =  $rows->payment_method;

        }
        $name_customer_expiration_date = $this->paymentMethodModel->where('id_payment_method', $payment_method_customer)->first();


        // Para pasar a minúsculas
        $customer_name_title = strtoupper($customer_name);


        // Para pasar a minúsculas
        $customer_name = strtolower($customer_name);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customer_name = ucfirst($customer_name);
        // Para pasar a minúsculas
        $customer_address = strtolower($customer_address);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customer_address = ucfirst($customer_address);
        // Para pasar a minúsculas
        $customer_location = strtolower($customer_location);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customer_location = ucfirst($customer_location);



        // Para pasar a minúsculas
        $worklocations_address = strtolower($worklocations_address);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_address = ucfirst($worklocations_address);
        // Para pasar a minúsculas
        $worklocations_location = strtolower($worklocations_location);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_location = ucfirst($worklocations_location);
        // Para pasar a minúsculas
        $worklocations_province = strtolower($worklocations_province);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_province = ucfirst($worklocations_province);




        $pdf = new PDF_Bills();
        $pdf->AddPage();

        $pdf->SetAutoPageBreak(true, 15);
        $pdf->Header();


        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(189, 5, '', 0, 1);


        $pdf->SetTextColor(0, 111, 46);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(115, 5, 'Direccion fiscal: ', 0, 0);

        $pdf->SetTextColor(0, 111, 46);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(35, 5, 'Direccion Correspondencia: ', 0, 1);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(115, 7, $customer_name_title, 0, 0);


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(115, 7, $customer_name_title, 0, 1);
        $pdf->SetFont('Arial', '', 10);

        $worklocations_address = utf8_decode($worklocations_address);
        $worklocations_location = utf8_decode($worklocations_location);

        $pdf->Cell(115, 5, $worklocations_address, 0, 0);
        $pdf->Cell(115, 5, $worklocations_address, 0, 1);

        $pdf->Cell(115, 5,  $worklocations_location . "(" . $worklocations_province . ")", 0, 0);
        $pdf->Cell(115, 5,  $worklocations_location . "(" . $worklocations_province . ")", 0, 1);

        $pdf->Cell(115, 5, 'CP:' .$customer_zip_code.   ' Telefono: ' . $customer_phone , 0, 0);

        $pdf->Cell(135, 10, '', 0, 1);
        /**
         * Datos cliente factura
         */
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(115, 5, 'F a c t u r a', 0, 0);
        $pdf->Cell(115, 6, '', 0, 1);

        $pdf->SetFont('Arial', '', 9);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(25, 4, 'Fecha', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $created_at_bill, 0,);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 4, 'Cliente', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $id_customer, 0,);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(25, 5, 'Telefono/Fax', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $customer_phone, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(25, 5, 'Numero', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $id_bills, 0,);


        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 4, 'CIF/NIF', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4,  $customer_dni, 0, 1);


        $pdf->Cell(189, 6, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 8, 'C');
        $text_code = 'Codigo';
        $w2 = $pdf->GetStringWidth($text_code) + 10;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w2, 6, $text_code, 0, 0, 'C', 1);

        $text_cantidad = 'Cantidad';
        $w3 = $pdf->GetStringWidth($text_cantidad) + 10;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        //Title
        $pdf->Cell($w3, 6, $text_cantidad, 0, 0, 'C', 1);

        $text_descr = 'Descripcion';
        $w4 = $pdf->GetStringWidth($text_descr) + 60;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w4, 6, $text_descr, 0, 0, 'C', 1);

        $text_precio = 'Precio';
        $w5 = $pdf->GetStringWidth($text_precio) + 9;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w5, 6, $text_precio, 0, 0, 'C', 1);

        $text_dto = '% Dto.';
        $w6 = $pdf->GetStringWidth($text_dto) + 9;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w6, 6, $text_dto, 0, 0, 'C', 1);

        $text_imp = 'Imp.Dto.';
        $w7 = $pdf->GetStringWidth($text_imp) + 9;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w7, 6, $text_imp, 0, 0, 'C', 1);

        $text_impo = 'Importe';
        $w8 = $pdf->GetStringWidth($text_impo) + 9;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);


        $pdf->Cell($w8, 6, $text_impo, 0, 1, 'C', 1);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(40, 40, 40);



        foreach ($bills as $item) {


            $json_supplements_aditional = $item->json_supplements_aditional;

            $created_at = $item->created_at;
            $created_at = date("Y-m-d", strtotime($created_at));


            $json_supplements = json_decode($json_supplements_aditional);


            foreach ($json_supplements as $key => $item) {

                $price_total = $item->price_total;
                $price_dto = $item->price_dto;
                $dto = $item->dto;
                $pvp_edit = $item->pvp_edit;

                $pdf->SetFont('Arial', '', 9);

                $pdf->Cell(20, 7, '', 0, 0, 'C',1);
                $pdf->Cell(20, 7, '', 0, 0, 'C',1);

                $pdf->Cell(75, 7, $item->name, 0, 0, 'C',1);
                $pdf->Cell(20, 7, $pvp_edit, 0, 0, 'C',1);
                $pdf->Cell(20, 7, $dto, 0, 0, 'C',1);
                $pdf->Cell(20, 7, $price_dto, 0, 0,'C',1);
                $pdf->Cell(20, 7, $price_total, 0, 1,'C',1);



            }


    }



        /**
         * Tabla 2 Total Bruto
         */
        $currentY = $pdf->GetY();
        // Altura de la celda deseada
        $desiredY = 50; // Posición vertical de la celda deseada en unidades

        // Verificar si es necesario realizar un salto de página
        if ($currentY + $desiredY > $pdf->GetPageHeight()) {
            $pdf->AddPage(); // Agregar una nueva página
        }



        $pdf->SetFont('Arial', 'B', 8, 'L');
        // $pdf->SetFont('Arial','I',8,'L');
        $text_total_bruto = 'Total Bruto';
        $w_2 = $pdf->GetStringWidth($text_total_bruto) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_2, 6, $text_total_bruto, 0, 0, 'C', 1);

        $text_suma_dto = 'Suma Dtos.';
        $w_3 = $pdf->GetStringWidth($text_suma_dto) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        //Title
        $pdf->Cell($w_3, 6, $text_suma_dto, 0, 0, 'C', 1);




        $text_dto_comp = '% Dto.Com.';
        $w_4 = $pdf->GetStringWidth($text_dto_comp) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_4, 6, $text_dto_comp, 0, 0, 'C', 1);

        $text_dto_pp = '% Dto.P.P.';
        $w_5 = $pdf->GetStringWidth($text_dto_pp) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_5, 6, $text_dto_pp, 0, 0, 'C', 1);



        $text_base_impo = 'BASE IMPONIBLE';
        $w_6 = $pdf->GetStringWidth($text_base_impo) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_6, 6, $text_base_impo, 0, 0, 'C', 1);



        $text_irpf = '% I.R.P.F.';
        $w_7 = $pdf->GetStringWidth($text_irpf) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_7, 6, $text_irpf, 0, 0, 'C', 1);

        $text_iva = '% I.V.A.';
        $w_8 = $pdf->GetStringWidth($text_iva) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_8, 6, $text_iva, 0, 0, 'C', 1);




        $text_cuota = 'Cuota';
        $w_9 = $pdf->GetStringWidth($text_cuota) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_9, 6, $text_cuota, 0, 0, 'C', 1);


        $text_TOTAL = 'TOTAL';
        $w_10 = $pdf->GetStringWidth($text_TOTAL) + 10;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_10, 6, $text_TOTAL, 0, 1, 'C', 1);



        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(23.05, 8, $gross_total, 0, 0, 'C');


        if($sum_dto === null){
            $sum_dto = "-";
        };

        $pdf->Cell(23.52, 8, $sum_dto, 0, 0, 'C');


        $pdf->Cell(23.83, 8, '-', 0, 0, 'C');
        $pdf->Cell(20.11, 8, '-', 0, 0, 'C');
        $pdf->Cell(30.30, 8,  $taxable_base, 0, 0, 'C');
        $pdf->Cell(18.86, 8, '-', 0, 0, 'C');
        $pdf->Cell(16.35, 8, $fee, 0, 0, 'C');
        $pdf->Cell(15.99, 8, $customer_iva, 0, 0, 'C');
        $pdf->Cell(19.40, 8, $total_bills, 0, 1, 'C');


        $pdf->Cell(189, 5, '', 0, 1);


        if( $retainer_amount === null){
            $retainer_amount = "-";
        }

        /**
         * Anticipos
         */
        $pdf->SetFont('Arial', '', 9);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(15, 4, 'Anticipos:', 0, 0);


        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, $retainer_amount, 0,);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(12, 4, 'Cargos:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, $charge, 0,);



        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(10, 4, 'Saldo:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, $balance, 0,);


        $pdf->SetFont('Arial', 'I', 10, 'L');
        $text_TOTAL = 'TOTAL FACTURA';
        $w_10 = $pdf->GetStringWidth($text_TOTAL) + 50;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_10, 6, $text_TOTAL, 0, 0, 'L', 1);



        $text_TOTAL = $total_bills;
        $w_10 = $pdf->GetStringWidth($text_TOTAL) + 5;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_10, 6, $text_TOTAL, 0, 1, 'L', 1);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(189, 10, '', 0, 1);


        $pdf->SetFont('Arial', 'I', 10, 'L');

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Forma de Pago:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);


        $txt_trnasferencia = $name_customer_expiration_date->name ;
        $pdf->Cell(20, 6, $txt_trnasferencia, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Domiciliacion:', 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 6, $customer_iban . " " . $customer_bank . " " . $customer_office_bank . " " . $customer_digital_control . " " . $customer_bank_count, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Vencimientos:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 6, $expiration_date, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Importes:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 6, $total_bills, 0, 1);



        $pdf->Cell(5, 10, '', 0, 1);
        $pdf->SetFont('Arial', '', 8, 'L');
        $pdf->SetTextColor(0, 0, 0);



            $pdf->Ln();
            //fin cabecera
            $pdf->Output('factura.pdf', 'I');
            // $pdf->Output('D', 'archivo.pdf');

            return $pdf->Output("Front/Fpdf/imprimirPdf.html.twig", "I");
            // return redirect()->back();

        // return $this->twig->render('Front/Fpdf/imprimirPdf.html.twig');
    }






    /**
     * Pdf de la factura
     */
    public function  PdfBillsProForma($id_bills =null)
    {


        $customer_name_title = "";
        $id_customer = 0;
        $id_container = null;
        $id_vehicle = null;
        $id_driver = null;
        $id_service = null;
        $form_of_payment = null;
        $id_rates = null;
        $id_work_location = null;

        $id_retainer = null;
        $albaran_status = null;

        $tax_base = null;
        $price_discount = null;
        $total = null;
        $discount = null;

        $customers_names = "";
        $customers_email = null;
        $customers_phone = null;
        $customers_address = "";
        $customers_location = "";
        $customers_zip_code = null;
        $worklocations_address = "";
        $worklocations_location = "";
        $worklocations_province = "";
        $worklocations_zip_code = "";
        $drivers_name = "-";
        $drivers_phone = "0";
        $rate_name = "";
        $drivers_car_registration = null;
        $id_sp = null;
        $id = null;
        $supplements_concept = null;
        $supplements_pvp = null;
        $rates_id = null;

        $array = [];
        $supplements_descriptions = [" - ", " - ", " - ", " - ", " - "];
        $services_descriptions = [" - ", " - ", " - ", " - ", " - "];

        $supplements = [];
        $supplements2 = [];
        $array_supplements = [];
        $array_services = [];
        $array_supplements_concept = [];
        $array_supplements_pvp = [];
        $array_name = [];
        $array_name_services = [];
        $array_pvp_sum = 0;
        $suma_price_service = 0;
        $sum_price_supplements_select = 0;
        $sum_price_service_select = 0;
        $id_payment_method = 0;
        $id_payment = 0;
        $sum_dto = null;
        $fee = null;

        $customer_name = null;


        $subtotal = 0;

        $customer_iban = null;
        $customer_bank = null;
        $customer_office_bank = null;
        $customer_digital_control = null;
        $customer_bank_count = null;
        $expiration_date = null;

        $amount_tax_base_discount = null;

        $created_at_bill = null;

        $work_location_location = null;
        $work_location_address = null;

        $container_residue = null;

        $bills = $this->billsModel->where('id_bills', $id_bills)->findAll();


        foreach ($bills as $item) {


            $id_customer = $item->id_customer;

            $payment_method = $item->payment_method;
            $customer_name = $item->customer_name;

            $customer_mail = $item->customer_mail;
            $customer_address = $item->customer_address;
            $customer_location = $item->customer_location;
            $customer_province = $item->customer_province;
            $customer_zip_code = $item->customer_zip_code;
            $customer_dni = $item->customer_dni;
            $customer_phone = $item->customer_phone;
            $customer_iva = $item->customer_iva;
            $customer_iban = $item->customer_iban;
            $customer_bank = $item->customer_bank;
            $customer_office_bank = $item->customer_office_bank;
            $customer_digital_control = $item->customer_digital_control;
            $customer_bank_count = $item->customer_bank_count;

            $worklocations_address = $item->work_location_address;
            $worklocations_location = $item->work_location_location;
            $worklocations_province = $item->work_location_province;
            $worklocations_zip_code = $item->work_location_zip_code;

            $total_bills = $item->total_bills;
            $charge = $item->charge;
            $balance = $item->balance;

            $expiration_date = $item->expiration_date;

            $container_price = $item->container_price;
            $tax_base = $container_price;

            $fee = $item->fee;


            $sum_dto = $item->sum_dto;
            $taxable_base = $item->taxable_base;
            $gross_total = $item->gross_total;
            $price_total_supp = $item->price_total_supp;

            $retainer_amount = $item->retainer_amount;

            $drivers_name = $item->driver_name;
            $drivers_phone = $item->driver_phone;

            $rate_name = $item->rates_name;
            $created_at_bill = $item->created_at;
            $created_at_bill = date("Y-m-d", strtotime($created_at_bill));

        }

        $id_customer_expiration_date = $this->customersModel->where('id_customer', $id_customer)->find();
        foreach ($id_customer_expiration_date as $key => $rows) {
            $payment_method_customer =  $rows->payment_method;

        }
        $name_customer_expiration_date = $this->paymentMethodModel->where('id_payment_method', $payment_method_customer)->first();



        // Para pasar a minúsculas
        $customer_name_title = strtoupper($customer_name);

        // Para pasar a minúsculas
        $customer_name = strtolower($customer_name);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customer_name = ucfirst($customer_name);
        // Para pasar a minúsculas
        $customer_address = strtolower($customer_address);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customer_address = ucfirst($customer_address);
        // Para pasar a minúsculas
        $customer_location = strtolower($customer_location);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $customer_location = ucfirst($customer_location);

        // Para pasar a minúsculas
        $worklocations_address = strtolower($worklocations_address);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_address = ucfirst($worklocations_address);
        // Para pasar a minúsculas
        $worklocations_location = strtolower($worklocations_location);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_location = ucfirst($worklocations_location);
        // Para pasar a minúsculas
        $worklocations_province = strtolower($worklocations_province);
        // Para pasar a mayúsculas solo la primera letra de toda la cadena
        $worklocations_province = ucfirst($worklocations_province);


        $tableBillsAlbaranes = $this->tableBillsAlbaranesModel->where('id_bills', $id_bills)->paginate(config('Configuration')->regBillsPage);

        $pdf = new PDF_Bills();
        $pdf->AddPage();

        $pdf->SetAutoPageBreak(true, 15);
        $pdf->Header();


        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(189, 5, '', 0, 1);


        $pdf->SetTextColor(0, 111, 46);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(125, 5, 'Direccion fiscal: ', 0, 0);

        $pdf->SetTextColor(0, 111, 46);
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(35, 5, 'Direccion Correspondencia: ', 0, 1);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(125, 7, $customer_name_title, 0, 0);


        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(125, 7, $customer_name_title, 0, 1);
        $pdf->SetFont('Arial', '', 10);

        $worklocations_address = utf8_decode($worklocations_address);
        $worklocations_location = utf8_decode($worklocations_location);

        $pdf->Cell(125, 5, $worklocations_address, 0, 0);
        $pdf->Cell(125, 5, $worklocations_address, 0, 1);

        $pdf->Cell(125, 5,  $worklocations_location . "(" . $worklocations_province . ")", 0, 0);
        $pdf->Cell(125, 5,  $worklocations_location . "(" . $worklocations_province . ")", 0, 1);

        $pdf->Cell(115, 5, 'CP:' .$customer_zip_code.   ' Telefono: ' . $customer_phone , 0, 0);

        $pdf->Cell(135, 10, '', 0, 1);
        /**
         * Datos cliente factura
         */
        $pdf->SetFont('Arial', 'B', 18);
        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(115, 5, 'P r o f o r m a', 0, 0);
        $pdf->Cell(115, 6, '', 0, 1);

        $pdf->SetFont('Arial', '', 9);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(25, 4, 'Fecha', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $created_at_bill, 0,);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 4, 'Cliente', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $id_customer, 0,);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(25, 5, 'Telefono/Fax', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $customer_phone, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(25, 5, 'Numero', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4, $id_bills, 0,);


        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 4, 'CIF/NIF', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(30, 4,  $customer_dni, 0, 1);


        $pdf->Cell(189, 1, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 8, 'C');
        $text_code = 'Codigo';
        $w2 = $pdf->GetStringWidth($text_code) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w2, 5, $text_code, 0, 0, 'C', 1);

        $text_cantidad = 'Cantidad';
        $w3 = $pdf->GetStringWidth($text_cantidad) + 10;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        //Title
        $pdf->Cell($w3, 5, $text_cantidad, 0, 0, 'C', 1);

        $text_descr = 'Descripcion';
        $w4 = $pdf->GetStringWidth($text_descr) + 60;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w4, 5, $text_descr, 0, 0, 'C', 1);

        $text_precio = 'Precio';
        $w5 = $pdf->GetStringWidth($text_precio) + 16;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w5, 5, $text_precio, 0, 0, 'C', 1);

        $text_dto = '% Dto.';
        $w6 = $pdf->GetStringWidth($text_dto) + 7;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w6, 5, $text_dto, 0, 0, 'C', 1);

        $text_imp = 'Imp.Dto.';
        $w7 = $pdf->GetStringWidth($text_imp) + 7;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w7, 5, $text_imp, 0, 0, 'C', 1);

        $text_impo = 'Importe';
        $w8 = $pdf->GetStringWidth($text_impo) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);


        $pdf->Cell($w8, 5, $text_impo, 0, 1, 'C', 1);
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(40, 40, 40);

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

            $id_albaran = $obj->id_albaran;
            $container_residue = $obj->container_residue;
            $container_m3 = $obj->container_m3;

            $container_price = $obj->container_price;

            $discount = $obj->discount;
            $price_discount = $obj->price_discount;
            $subtotal = $obj->subtotal;

            $amount_tax_base_discount = $obj->amount_tax_base_discount;

            $work_location_location = $obj->work_location_location;
            $work_location_address = $obj->work_location_address;


            $created_at = $obj->created_at;
            $created_at = date("Y-m-d", strtotime($created_at));


            $supplements_exits = $obj->supplements_exits;


        if ($supplements !== null) {

            $json_supplements = json_decode($supplements);


                $pdf->Cell(22, 5, "", 0, 0,  1);
                $pdf->Cell(22, 5,"", 0, 0,  0);

                $pdf->SetFont('Arial', '', 9);
                $Sevicio = "Sevicio Nº (Alabaran) " .  $id_albaran . " de Fecha : " . $created_at;
                $Sevicio = utf8_decode($Sevicio);
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
                $pdf->Cell(75, 5, $Sevicio, 0, 0, 'C', 0);

                if( $discount === 0 || $discount === "0.00" || $discount === 0.00 || $discount === null){
                    $discount = "";
                }

                if( $price_discount === 0 || $price_discount === "0.00" || $price_discount === 0.00 || $price_discount === null){
                    $price_discount = "";
                }



                $pdf->SetFont('Arial', '', 9);
                $tax_base_text = $container_price;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);

              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $tax_base_text,  0, 0, 'C', 0);



                $pdf->SetFont('Arial', '', 9);
                $discount_text = $discount;
                $discount_text = (int)$discount;

                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $discount_text, 0, 0, 'C', 0);


                $pdf->SetFont('Arial', '', 9);
                $price_discount_text = $price_discount;
                $discount_text = (int)$discount;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $price_discount_text, 0, 0,  'C', 0);

                $pdf->SetFont('Arial', '', 9);
                $amount_tax_base_discount_text = $amount_tax_base_discount;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $amount_tax_base_discount_text, 0, 1, 'C',  0);


                $pdf->Cell(22, 5, "", 0, 0,  1);
                $pdf->Cell(22, 5,"", 0, 0,  0);

                $work_location_location = "Obra: " .  $work_location_location . " " . $work_location_address ;
                $work_location_location = utf8_decode($work_location_location);

                $pdf->Cell(75, 5, $work_location_location, 0, 1, 'C', 0);

                $pdf->Cell(22, 5, "", 0, 0, 'C');
                $pdf->Cell(22, 5,"", 0, 0, 'C');

                $container_residue = $container_residue .  " M3 " . $container_m3 ;
                $pdf->Cell(75, 5, $container_residue, 0, 1, 'C');


            foreach ($json_supplements as $key1 => $item) {



                if($item->dto === 0){
                    $item->dto ="     ";
                }

                if($item->price_dto === 0){
                    $item->price_dto ="     ";
                }

                $pdf->Cell(22, 5, "", 0, 0,  1);
                $pdf->Cell(22, 5,"", 0, 0,  0);
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);


               // $container_residue_supplements =  $item->name  .  "                                     "  . $item->pvp_edit . "         "  . $item->dto . "            "  . $item->price_dto . "                      "  .$item->price_total;

                $container_residue_supplements =  $item->name;
                $pdf->Cell(75, 5, $container_residue_supplements, 0, 0,'C', 0);


                $pdf->SetFont('Arial', '', 9);
                $pvp_edit_text = $item->pvp_edit;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
                $pdf->Cell(19, 5, $pvp_edit_text, 0, 0, 'C', 0);


            if( $item->dto === 0 || $item->dto === "0.00" || $item->dto === 0.00 || $item->dto === null){
                $item->dto = "";
            }

            if( $item->price_dto === 0 || $item->price_dto === "0.00" || $item->price_dto === 0.00 || $item->price_dto === null){
                $item->price_dto = "";
            }

                $pdf->SetFont('Arial', '', 9);
                $discount_text_suppl = $item->dto;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
                $pdf->Cell(19, 5, $discount_text_suppl, 0, 0,'C',0);


                $pdf->SetFont('Arial', '', 9);
                $dto_text_suppl = $item->price_dto;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
                $pdf->Cell(19, 5, $dto_text_suppl, 0, 0, 'C', 0);

                $pdf->SetFont('Arial', '', 9);
                $price_total_text_supp = "  " . $item->price_total;
                $pdf->SetFillColor(40, 255, 255);
                $pdf->SetTextColor(40, 40, 40);
              // Centrar el contenido horizontalmente
                $pdf->Cell(19, 5, $price_total_text_supp, 0, 1,'C', 0);


            }

            // $id_albaranes_selected = $this->albaranesModel->join('tablebillsalbaranes', 'tablebillsalbaranes.id_albaran = albaranes.id_albaran')->where('tablebillsalbaranes.num_bill', $num_bill)->findAll();
        }else{


            $pdf->Cell(22, 5, "", 0, 0,  1);
            $pdf->Cell(22, 5,"", 0, 0,  0);

            $pdf->SetFont('Arial', '', 9);
            $Sevicio = "Sevicio Nº (Alabaran) " .  $id_albaran . " de Fecha : " . $created_at;
            $Sevicio = utf8_decode($Sevicio);
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);
            $pdf->Cell(75, 5, $Sevicio, 0, 0, 'C', 0);

            if( $discount === 0 || $discount === "0.00" || $discount === 0.00 || $discount === null){
                $discount = "";
            }

            if( $price_discount === 0 || $price_discount === "0.00" || $price_discount === 0.00 || $price_discount === null){
                $price_discount = "";
            }



            $pdf->SetFont('Arial', '', 9);
            $tax_base_text = $container_price;
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);


          // Centrar el contenido horizontalmente
            $pdf->Cell(19, 5, $tax_base_text, 0, 0,  'C', 0);



            $pdf->SetFont('Arial', '', 9);
            $discount_text = $discount;
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);
          // Centrar el contenido horizontalmente
            $pdf->Cell(19, 5, $discount_text, 0, 0,  'C', 0);


            $pdf->SetFont('Arial', '', 9);
            $price_discount_text = $price_discount;
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);
          // Centrar el contenido horizontalmente
            $pdf->Cell(19, 5, $price_discount_text, 0, 0, 'C',  0);

            $pdf->SetFont('Arial', '', 9);
            $amount_tax_base_discount_text = $amount_tax_base_discount;
            $pdf->SetFillColor(40, 255, 255);
            $pdf->SetTextColor(40, 40, 40);
          // Centrar el contenido horizontalmente
            $pdf->Cell(19, 5, $amount_tax_base_discount_text, 0, 1, 'C', 0);


            $pdf->Cell(22, 5, "", 0, 0,  1);
            $pdf->Cell(22, 5,"", 0, 0,  0);

            $work_location_location = "Obra: " .  $work_location_location . " " . $work_location_address ;
            $work_location_location = utf8_decode($work_location_location);

            $pdf->Cell(75, 5, $work_location_location, 0, 1, 'C', 0);

            $container_residue = $container_residue .  " M3 " . $container_m3 ;
            $w55 = $pdf->GetStringWidth($container_residue) +25;
            $pdf->SetX(($pdf->GetPageWidth() - $w55) / 2); // Centrar el contenido horizontalmente
            $pdf->Cell($w55, 5, $container_residue, 0, 1, 1);


        }

    }


        /**
         * Tabla 2 Total Bruto
         */
        $currentY = $pdf->GetY();
        // Altura de la celda deseada
        $desiredY = 50; // Posición vertical de la celda deseada en unidades

        // Verificar si es necesario realizar un salto de página
        if ($currentY + $desiredY > $pdf->GetPageHeight()) {
            $pdf->AddPage(); // Agregar una nueva página
        }



        $pdf->SetFont('Arial', 'B', 8, 'L');
        // $pdf->SetFont('Arial','I',8,'L');
        $text_total_bruto = 'Total Bruto';
        $w_2 = $pdf->GetStringWidth($text_total_bruto) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_2, 5, $text_total_bruto, 0, 0, 'C', 1);

        $text_suma_dto = 'Suma Dtos.';
        $w_3 = $pdf->GetStringWidth($text_suma_dto) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        //Title
        $pdf->Cell($w_3, 5, $text_suma_dto, 0, 0, 'C', 1);

        $text_dto_comp = '% Dto.Com.';
        $w_4 = $pdf->GetStringWidth($text_dto_comp) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_4, 5, $text_dto_comp, 0, 0, 'C', 1);

        $text_dto_pp = '% Dto.P.P.';
        $w_5 = $pdf->GetStringWidth($text_dto_pp) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_5, 5, $text_dto_pp, 0, 0, 'C', 1);

        $text_base_impo = 'BASE IMPONIBLE';
        $w_6 = $pdf->GetStringWidth($text_base_impo) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_6, 5, $text_base_impo, 0, 0, 'C', 1);

        $text_irpf = '% I.R.P.F.';
        $w_7 = $pdf->GetStringWidth($text_irpf) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_7, 5, $text_irpf, 0, 0, 'C', 1);

        $text_iva = '% I.V.A.';
        $w_8 = $pdf->GetStringWidth($text_iva) + 6;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_8, 5, $text_iva, 0, 0, 'C', 1);


        $text_cuota = 'Cuota';
        $w_9 = $pdf->GetStringWidth($text_cuota) + 8;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_9, 5, $text_cuota, 0, 0, 'C', 1);


        $text_TOTAL = 'TOTAL';
        $w_10 = $pdf->GetStringWidth($text_TOTAL) + 10;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_10, 5, $text_TOTAL, 0, 1, 'C', 1);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(23.05, 5, $gross_total, 0, 0, 'C');
        $pdf->Cell(23.52, 5, $sum_dto, 0, 0, 'C');
        $pdf->Cell(23.83, 5, '-', 0, 0, 'C');
        $pdf->Cell(20.11, 5, '-', 0, 0, 'C');
        $pdf->Cell(30.30, 5,  $taxable_base, 0, 0, 'C');
        $pdf->Cell(18.86, 5, '-', 0, 0, 'C');
        $pdf->Cell(16.35, 5, $fee, 0, 0, 'C');
        $pdf->Cell(15.99, 5, $customer_iva, 0, 0, 'C');
        $pdf->Cell(19.40, 5, $total_bills, 0, 1, 'C');

        $pdf->Cell(189, 5, '', 0, 1);


        if( $retainer_amount === null){
            $retainer_amount = "-";
        }

        /**
         * Anticipos
         */
        $pdf->SetFont('Arial', '', 9);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(15, 4, 'Anticipos:', 0, 0);


        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, $retainer_amount, 0,);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(12, 4, 'Cargos:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, $charge, 0,);



        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(10, 4, 'Saldo:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 4, $balance, 0,);


        $pdf->SetFont('Arial', 'I', 10, 'L');
        $text_TOTAL = 'TOTAL FACTURA';
        $w_10 = $pdf->GetStringWidth($text_TOTAL) + 41;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_10, 6, $text_TOTAL, 0, 0, 'L', 1);



        $text_TOTAL = $total_bills;
        $w_10 = $pdf->GetStringWidth($text_TOTAL) + 5;
        $pdf->SetFillColor(77, 143, 36);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell($w_10, 6, $text_TOTAL, 0, 1, 'L', 1);

        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(189, 10, '', 0, 1);

     // $pdf->Line(10, 100, 200, 100); //Horizontal  1 linea de arriba de codigo

     // $pdf->Line(10, 175, 200, 175);  //Horizontal  2 linea de arriba de codigo
     // $pdf->Line(10, 210, 200, 210);  //Horizontal  3 linea de arriba de codigo

        $pdf->SetFont('Arial', 'I', 10, 'L');

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Forma de Pago:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $txt_trnasferencia = $name_customer_expiration_date->name ;
        $pdf->Cell(20, 6, $txt_trnasferencia, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Domiciliacion:', 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 6, $customer_iban . " " . $customer_bank . " " . $customer_office_bank . " " . $customer_digital_control . " " . $customer_bank_count, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Vencimientos:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 6, $expiration_date, 0, 1);

        $pdf->SetTextColor(77, 143, 36);
        $pdf->Cell(30, 6, 'Importes:', 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell(20, 6, $total_bills, 0, 1);



        $pdf->Cell(5, 10, '', 0, 1);
        $pdf->SetFont('Arial', '', 8, 'L');
        $pdf->SetTextColor(0, 0, 0);




        $pdf->Ln();
        //fin cabecera
        $pdf->Output('factura.pdf', 'I');
       // $pdf->Output('D', 'archivo.pdf');

       return $pdf->Output("Front/Fpdf/imprimirPdf.html.twig", "I");
       // return redirect()->back();
    }






}
