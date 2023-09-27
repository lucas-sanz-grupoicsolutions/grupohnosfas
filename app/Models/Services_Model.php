<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Services_;

class Services_Model extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'services';
    protected $primaryKey = 'id_service';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = Services_::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name','code','active'];

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
