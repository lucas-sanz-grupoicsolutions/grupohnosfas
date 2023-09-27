<?php

namespace App\Models;
use CodeIgniter\Model;
use App\Entities\Bills;


use App\Libraries\Log;
use DateTime;
use DateTimeZone;
use Config\Services;
use CodeIgniter\I18n\Time;

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


class BillsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'bills';
    protected $primaryKey = 'id_bills';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = Bills::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [

                                'id_order',
                                'year',
                                'id_work_locations',
                                'id_customer',
                                'id_user',
                                'name_user',
                                'iva',
                                'total_bills',
                                'charge',
                                'balance',
                                'payment_method',
                                'expiration',
                                'notes',
                                'state',
                                'price_total_supp',

                                'subtotal_sum_supplements',
                                'json_supplements_aditional',

                                'customer_name',
                                'customer_mail',
                                'customer_address',
                                'customer_location',
                                'customer_province',
                                'customer_zip_code',
                                'customer_dni',
                                'customer_phone',
                                'customer_bic',
                                'customer_iva',
                                'customer_iban',
                                'customer_bank',
                                'customer_office_bank',
                                'customer_digital_control',
                                'customer_bank_count',

                                'work_location_address',
                                'work_location_location',
                                'work_location_province',
                                'work_location_zip_code',

                                'rates_name',
                                'service_name',
                                'service_code',
                                'retainer_amount',

                                'notes',
                                'fee',
                                'sum_dto',

                                'tax_base',
                                'iva',

                                'gross_total',
                                'taxable_base',
                                'total',

                                'billable',
                                'words_num_bill',
                                'num_bill',

                                'id_bill_original',
                                'rectifyBills',

                                'bills_supplements',
                                'expiration_date',
                                'remesas',

                                'date_signing_mandate',
                                'recurrent_date',
                                'active'


                            ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'date';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

     // Validation
     protected $validationRules = [];
     protected $validationMessages = [];
     protected $skipValidation = false;
     protected $cleanValidationRules = true;

     // Callbacks
     protected $allowCallbacks = true;
     protected $beforeInsert = [];
     protected $afterInsert = [];
     protected $beforeUpdate = [];
     protected $afterUpdate = [];
     protected $beforeFind = [];
     protected $afterFind = [];
     protected $beforeDelete = [];
     protected $afterDelete = [];


     protected $assignGroup;

     public function getIdBills($id_bills)
     {
         $query = $this->select('id_bills')
                       ->where('id_bills', $id_bills)
                       ->get();

         if ($query->getResult()) {
             return $query->getRow()->id;
         } else {
             return null;
         }
     }


     //Selec Max para obtener el maximo id registropar obtener y generar el num_bill
     public function getIdLastBills()
     {

        $time = new \CodeIgniter\I18n\Time('now', 'UTC');
        $anioActual = $time->getYear();

        $fechaActual = date("Y-m-d");


        $query = $this->query('SELECT MAX(num_bill) AS max_id FROM bills  WHERE created_at LIKE ' );
        $result = $query->getRow();
        $maxId = $result->max_id;
        $id_num_bill = $maxId + 1;

        return $id_num_bill;
 }











}




