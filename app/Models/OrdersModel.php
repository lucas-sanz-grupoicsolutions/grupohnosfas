<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Orders;

class OrdersModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'orders';
    protected $primaryKey = 'id_order';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = Orders::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [

                                'id_customer',
                                'id_work_location',
                                'id_driver',
                                'id_vehicle',
                                'id_container',
                                'id_service',
                                'id_user',
                                'name_user',
                                'payment_method',
                                'name_customer',
                                'name_work_location',
                                'notas',
                                'planned_date',
                                'created_by',
                                'state',
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


}


