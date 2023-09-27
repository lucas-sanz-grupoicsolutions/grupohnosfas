<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Vehicles;

class VehiclesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'vehicles';
    protected $primaryKey = 'id_vehicle';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = Vehicles::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['car_registration', 'name', 'make', 'model', 'registration_date','date_itv','observations','active'];

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





