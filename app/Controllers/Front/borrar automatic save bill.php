<?php

public function createBillsAutomatic()
    {

        if (!$this->validate(validateCreateBillsAutomatic())) {
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
        $tableBillsAlbaranes2 = new TableBillsAlbaranes();

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

        $numBills = null;
        $idAlb = null;
        $id_last_bills_insert = null;

        $total_taxe_base_alb = 0;
        $total_sum_pvp_edit = 0;
        $total_sum_dto = 0;

        $price_total_all = 0;
        $total_albaranes = 0;
        $id_order = 0;
        $tax_base = 0;
        $subtotal = 0;
        $total = 0;
        $preprinted = 0;
        $price_total_supp = 0;

        $final_taxe_price_dto_container = 0;

        $sum_price_supplements_select = 0;

        $suppl_editional_existe = false;
        $payment_method_alb = null;

        $supplements_exits = 0;
        $supplementsObject = null;
        $c_iva = null;

        $price_dto_total_supplements = 0;

        $supplementsObjectArray = [];
        // Trae los albaranes seleccionados


        $id_albaranes_selected_post = $this->request->getPost('albaranes');
        $id_customer_selected = $this->request->getPost('id_customer');

        $num_bill = $this->request->getPost('num_bill');


        // $objBills = new Bills($this->request->getPost());
        $bills = new Bills($this->request->getPost());

        //  $id_albaranes_selected = $this->albaranesModel->join('containers', 'containers.id_container = albaranes.id_container')->whereIn('id_albaran', $id_albaranes_selected_post)->paginate(config('Configuration')->regPerPage);

        $id_albaranes_selected = $this->albaranesModel->whereIn('id_albaran', $id_albaranes_selected_post)->findAll();

        foreach ($id_albaranes_selected as $key => $obj) {

            $id_customer = $obj->id_customer;

            $id_work_location = $obj->id_work_location;
            $customer_name = $obj->customer_name;
            $customer_mail = $obj->customer_mail;
            $customer_address = $obj->customer_address;
            $customer_location = $obj->customer_location;
            $customer_province = $obj->customer_province;
            $customer_zip_code = $obj->customer_zip_code;
            $customer_dni = $obj->customer_dni;
            $customer_phone = $obj->customer_phone;
            $customer_iva = $obj->customer_iva;
            $customer_iban = $obj->customer_iban;
            $customer_bank = $obj->customer_bank;
            $customer_office_bank = $obj->customer_office_bank;
            $customer_digital_control = $obj->customer_digital_control;
            $customer_bank_count = $obj->customer_bank_count;

            $payment_method_alb = $obj->payment_method;

            $container_residue = $obj->container_residue;
            $container_m3 = $obj->container_m3;
            $container_price = $obj->container_price;

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

            $retainer_amount = $obj->retainer_amount;

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
                $supplements_obj_existe_alb = true;

                foreach ($supplements as $x2 => $s) {

                    $id_supplements = $s->id_supplements;
                    $pvp_edit = $s->pvp_edit;
                    $price_dto = $s->price_dto;
                    $total_price_supp = $s->price_total;

                    $price_dto_total_supplements += $price_dto;

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



        foreach ($id_albaranes_selected as $rows1) {
            $id_alabaran = $rows1->id_albaran;
        }

        $albaran_3 = $this->albaranesModel->where('id_albaran', $id_alabaran)->paginate(config('Configuration')->regPerPage);
        foreach ($albaran_3 as $rows) {
            $id_customer = $rows->id_customer;
            $id_work_location = $rows->id_work_location;
        }

        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);

        $customers = $this->customersModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);
        foreach ($customers as $i) {
            $iva = $i->iva;
            $c_iva = $i->iva;
        }

        //suma los dtos de los supplemntos en precios
        $total_dtos_container_supplement = $price_dto_total_supplements + $sum_dto;


        // amount_tax_base_discount

        $price_total_base_albaranes = $price_total_base_albaranes + $price_total_all + $total_price_supp_final;

        $total_dtos_and_price_container_supplement = $price_total_base_albaranes - $total_dtos_container_supplement;

        //Suma la linea de dto y precio base para linea de container
        $final_taxe_price_dto_container = $price_total_base_albaranes - $price_discount;



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

        $total = number_format($total, 2);
        $cuota = number_format($cuota, 2);
        //Total Bruto
        $gross_total = $price_total_base_albaranes;


        $taxable_base = $price_total_base_albaranes - $total_dtos_container_supplement;


        $asteriscos = str_repeat('*', 2); // crear una cadena de 2 asteriscos
        $customer_bank_con_asteriscos = substr_replace($customer_bank, $asteriscos, 0, 2); // reemplazar los primeros 2 números con asteriscos

        $state = "Finalizada";

        $bills->sum_dto = $sum_dto;
        $bills->id_order = $id_order;
        $bills->json_albaranes = $json_albaranes;
        $bills->num_bill = $num_bill;
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
        $bills->customer_iban = $customer_iban;
        $bills->customer_bank = $customer_bank;
        $bills->customer_office_bank = $customer_office_bank;
        $bills->customer_digital_control = $customer_digital_control;
        $bills->customer_bank_count = $customer_bank_count;
        $bills->payment_method = $payment_method_alb;

        $bills->container_residue = $container_residue;
        $bills->container_m3 = $container_m3;
        $bills->container_price = $container_price;

        $bills->work_location_address = $work_location_address;
        $bills->work_location_location = $work_location_location;
        $bills->work_location_province = $work_location_province;
        $bills->work_location_zip_code = $work_location_zip_code;

        $bills->driver_name = $driver_name;
        $bills->driver_phone = $driver_phone;

        $bills->rates_name = $rates_name;

        $bills->service_name = $service_name;
        $bills->service_code = $service_code;

        $bills->vehicle_name = $vehicle_name;
        $bills->vehicle_make = $vehicle_make;
        $bills->vehicle_model = $vehicle_model;
        $bills->vehicle_car_registration = $vehicle_car_registration;

        $bills->fee = $cuota;
        $bills->tax_base = $tax_base;
        $bills->iva = $c_iva;
        $bills->subtotal = $subtotal;

        $bills->gross_total = $gross_total;
        $bills->taxable_base = $taxable_base;


        $bills->total = $total;
        $bills->preprinted = $preprinted;
        $bills->subtotal_sum_supplements = $subtotal_sum_supplements;
        $bills->price_total_supp = $price_total_supp;

        $bills->total_bills = $charge;
        $bills->charge = $charge;
        $bills->balance = $balance;

        $bills->retainer_amount = $retainer_amount;


        $bills->state = $state;
        $bills->active = 1;

        $bills->bills_supplements = 0;

        // try {

        $this->billsModel->save($bills);
        $id_bills = $db->insertID($bills); //Ultimo id pallet insertado

        $bill_num = $this->billsModel->where('id_bills', $id_bills)->find();
        foreach ($bill_num as $key => $item) {
            $id_last_bills_insert = $item->num_bill;
        }

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
            $customer_iva = $obj_albaranes->customer_iva;
            $customer_bank = $obj_albaranes->customer_bank;
            $customer_office_bank = $obj_albaranes->customer_office_bank;
            $customer_digital_control = $obj_albaranes->customer_digital_control;
            $customer_bank_count = $obj_albaranes->customer_bank_count;

            $payment_method = $obj_albaranes->payment_method;

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
            $retainer_amount = $obj_albaranes->retainer_amount;
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


            $tableBillsAlbaranes->num_bill = $num_bill;
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

            $tableBillsAlbaranes->charge = $charge;
            $tableBillsAlbaranes->balance = $balance;

            $tableBillsAlbaranes->iva = $iva;
            $tableBillsAlbaranes->subtotal = $subtotal;
            $tableBillsAlbaranes->total = $total_con_iva;
            $tableBillsAlbaranes->tax_base = $tax_base;

            $tableBillsAlbaranes->supplements = $supplements;


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
                'updated_at' => $date->format('Y-m-d'),
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
            $data = [
                'state' => $state,
                'updated_at' => $date->format('Y-m-d'),
            ];
            $builder = $db->table('orders');
            $builder->getWhere(['id_order' => $id_order]);
            $builder->set(
                'state',
                'updated_at'

            );
        }

        $bills = $this->billsModel->where('num_bill', $num_bill)->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Bills/BillsCreated.html.twig', [

            // 'array_all_containers' => $array_all_containers,
            // 'array_all_cubic_meters' => $array_all_cubic_meters,
            'customers' => $customers,
            'worklocations' => $worklocations,
            'id_work_location' => $id_work_location,
            'date_today' => $date_today,
            'id_albaranes_selected' => $id_albaranes_selected,

            'retainer_amount' => $retainer_amount,
            'payment_method' => $payment_method,

            'charge' => $charge,
            'balance' => $balance,
            'num_bill' => $num_bill,
            'bills' => $bills,
            'gross_total' => $gross_total,


            'customer_bank_con_asteriscos' => $customer_bank_con_asteriscos,

            'id_last_bills_insert' => $id_last_bills_insert,

            'total_dtos_container_supplement' => $total_dtos_container_supplement,
            'price_final' => $price_final,
            'bank_count' => $bank_count,
            'supplements_obj_array' => $supplements_obj_array,
            'price_total_base_albaranes' => $price_total_base_albaranes,
            'iva' => $c_iva,
            'sum_tax_base' => $sum_tax_base,
            'total_con_iva' => $total_con_iva,
            'cuota' => $cuota,
            'taxable_base' => $taxable_base,

            'price_total_all' => $price_total_all,

            'suppl_editional_existe' => $suppl_editional_existe,

            'pager' => $this->billsModel->pager->links()
        ]);
    }
