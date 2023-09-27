<?php

namespace App\Models;
use CodeIgniter\Model;
use App\Entities\LastBills;


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


class LastBillsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'last_bills';
    protected $primaryKey = 'id_last_bills';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = LastBills::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [

                                'num_bill',
                                'num_year',

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

     public function getId($id_last_bills)
     {
         $query = $this->select('id_last_bills')
                       ->where('id_last_bills', $id_last_bills)
                       ->get();

         if ($query->getResult()) {
             return $query->getRow()->id_last_bills;
         } else {
             return null;
         }
     }




}




