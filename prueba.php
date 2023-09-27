  public function pre_view_create()
    {
        /*
        if (!$this->validate(validateUpdatePartida())) {
            return redirect()->back()
                   ->with('errors', $this->validator->getErrors())
                   ->withInput();
           }
        */
        // $id_customer = $this->request->getPost('id_customer');

        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $date_today = $date->format('d-m-Y');

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


        $services = [];
        $objeto = null;
        $services_select = [];
        $id_alabaran = null;
        $supplementsPricesObjectArray = [];

        $pricesObjectArray = [];
        $supplementsObjectArray = [];



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

        $supplementsObjectAditional = null;

        $supplements_obj_array_aditional = [];
        $campo1 = 0;
        $campo1 = 0;

        $campos=[];


        // Trae los albaranes seleccionados
        $id_albaranes_selected = $this->request->getPost('albaranes');
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

                if( $supplement_dto[$key] !== 0 || $supplement_dto[$key] !== null){
                    $pvp_edit_total = $pvp_edit[$key] * $supplement_dto[$key] / 100;
                    $price_final =  $pvp_edit[$key] - $pvp_edit_total;

                }else{
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

                $supplementsObjectArrayAditional=[];
                $supplementsObjectArrayAditionalStdClass=[];

                $supplementsObjectAditional = new Supplements();
                $supplementsObjectAditional->name = $obj_supp->name;
                $supplementsObjectAditional->pvp_edit = $pvp_edit[$key];
                $supplementsObjectAditional->dto = $dto_s;
                $supplementsObjectAditional->price_dto = $price_dto;
                $supplementsObjectAditional->price_total = $price_final;

                $supplementsObjectArrayAditional[$key] = $supplementsObjectAditional;
                $supplements_obj_array[$key]->supplementsObjectAditional = $supplementsObjectArrayAditional;

            }
        } else{
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
            $subtotal_sum_supplements += $obj->subtotal_sum_supplements;

            //Suma total con descuentos de los albaranes
            $price_total_base_albaranes += $amount_tax_base_discount;
            $sum_tax_base += $tax_base;
            $sum_dto += $price_discount;

            /**
             * Decofificamos y guarsamos los servicios guardados de albaranes en una variable
             */
            $services = json_decode($obj->services_select);

            /**
             * Recorremos los servicios
             */
            if ($services !== null) {
                foreach ($services as $x => $s) {
                    /**
                     * Los guardamos en un objeto temporal en un campo llamado servicesObject los valores name y code
                     */
                    $servicesObject = new stdClass();
                    $servicesObject->name = $s->name;
                    $servicesObject->code = $s->code;
                    /**
                     * Metemos en un array
                     *
                     * */
                    $servicesObjectArray[$x] = $servicesObject;
                }
            }



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

                $id_albaranes_selected[$key]->supplementsObject = $supplementsObjectArray;
            }


            $prices = new stdClass();

            $prices->tax_base = $tax_base;
            $prices->discount = $discount;
            $prices->price_discount = $price_discount;
            $prices->amount_tax_base_discount = $amount_tax_base_discount;

            $pricesObjectArray[$key] = $prices;

            $id_albaranes_selected[$key]->servicesObject = $servicesObjectArray;
            $id_albaranes_selected[$key]->prices = $pricesObjectArray;
        }


        // dd($id_albaranes_selected);

        if ($iva === "21") {
            $cuota =  $price_total_base_albaranes * 0.21;
        }
        if ($iva === "10") {
            $cuota =  $price_total_base_albaranes * 0.10;
        }
        if ($iva === "4") {
            $cuota =  $price_total_base_albaranes * 0.04;
        }


        $price_total_base_albaranes = $price_total_base_albaranes + $price_total_all + $total_price_supp_final;
        $total_con_iva = $cuota +  $price_total_base_albaranes;

        $cargos = $total_con_iva;
        $saldos = $total_con_iva;

        if ($retainer_amount > 0) {
            $cargos = $total_con_iva - $retainer_amount;
            $saldos = $total_con_iva - $retainer_amount;
        }

        // if ($retainer_amount > $price_total_base_albaranes) {
        //     return redirect()->back()
        //            ->with('errors', $this->validator->getErrors())
        //            ->withInput();
        // }

        $worklocations = $this->workLocationModel->where('id_work_locations', $id_work_location)->paginate(config('Configuration')->regPerPage);

     if($price_total_all === 0){
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
        'json_services' => $json_services,
        'retainer_amount' => $retainer_amount,
        'price_total_width_iva' => $price_total_width_iva,
        'total_final' => $total_final,
        'payment_method' => $payment_method,

        'cargos' => $cargos,
        'saldos' => $saldos,
        'sum_dto' => $sum_dto,

        'price_final'=>$price_final,

        'bank_count' => $bank_count,
        'campos'=>$campos,

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

        'price_total_all'=>$price_total_all,

        'suppl_editional_existe' => $suppl_editional_existe,

        'pager' => $this->albaranesModel->pager->links()
    ]);


    }
