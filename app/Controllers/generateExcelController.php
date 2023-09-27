<?php

namespace App\Controllers;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use App\Controllers\BillsController;
use App\Entities\Bills;

use App\Controllers\BaseController;


class generateExcelController extends BaseController
{

    protected $billsModel;
    protected $spreadsheet;


    public function __construct()
    {
        $this->billsModel = model('BillsModel');
        $this->spreadsheet = new Spreadsheet();
    }

    public function generateExcel()
    {

        // Obtener los datos de las facturas desde la base de datos
        $facturas = $this->billsModel->findAll();

        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados de las columnas
        $sheet->setCellValue('A1', 'Número de Factura');
        $sheet->setCellValue('B1', 'Cliente');
        // ... agregar más encabezados según tus datos

        // Llenar los datos de las facturas
        $row = 2;
        foreach ($facturas as $factura) {
            $sheet->setCellValue('A' . $row, $factura->num_bill);
            $sheet->setCellValue('B' . $row, $factura->id_order);
            // ... agregar más datos según tus datos
            $row++;
        }

        // Crear el archivo Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'facturas.xlsx';
        $writer->save($filename);

        // Descargar el archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }



    public function generateExcelForm()
    {

        $id_bills_selected_post[] = $this->request->getPost('facturas');
        dd($id_bills_selected_post);

        $bills_selected = $this->billsModel->whereIn('id_bills', $id_bills_selected_post)->findAll();
        // dd($bills_selected);


        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados de las columnas
        $sheet->setCellValue('A1', 'Número de Factura');
        $sheet->setCellValue('B1', 'Cliente');
        // ... agregar más encabezados según tus datos

        // Llenar los datos de las facturas
        $row = 2;
        foreach ($bills_selected as $bill) {
            $sheet->setCellValue('A' . $row, $bill->num_bill);
            $sheet->setCellValue('B' . $row, $bill->id_order);
            // ... agregar más datos según tus datos
            $row++;
        }

        // Crear el archivo Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'facturas.xlsx';
        $writer->save($filename);

        // Descargar el archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');


        /*
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

            $state = "Finalizada";

            $bills->id_user = $idUser;
            $bills->name_user = $name_user;
            $bills->id_customer = $object_array->id_customer;

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
        }



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



    }
*/



        // Obtener los datos de las facturas desde la base de datos
        $facturas = $this->billsModel->findAll();

        // Crear un nuevo objeto Spreadsheet
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados de las columnas
        $sheet->setCellValue('A1', 'Número de Factura');
        $sheet->setCellValue('B1', 'Cliente');
        // ... agregar más encabezados según tus datos

        // Llenar los datos de las facturas
        $row = 2;
        foreach ($facturas as $factura) {
            $sheet->setCellValue('A' . $row, $factura->num_bill);
            $sheet->setCellValue('B' . $row, $factura->id_order);
            // ... agregar más datos según tus datos
            $row++;
        }

        // Crear el archivo Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'facturas.xlsx';
        $writer->save($filename);

        // Descargar el archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
    }


}
