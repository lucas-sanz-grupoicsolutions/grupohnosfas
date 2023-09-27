<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\WorkLocation;


class WorkLocationModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'work_locations';
    protected $primaryKey = 'id_work_locations';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = WorkLocation::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id_customer','name_customer', 'location', 'province', 'address', 'zipe_code','observations','active'];

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

         /**
     * Obtiene el id_wok_location por medio del id_container
     * */
    /*
    public function getIdWorkLocationsByCustomers(string $id_customers )    {


        $array = [];
        $query = $this->db->query('SELECT id_work_locations FROM work_locations WHERE id_customer = ' . $id_customers);

        $id_work_locations = 0;

        if($id_customers === null || $id_customers === false ){
            $array[] = 0;

        }else{
            foreach ($query->getResult() as $row2) {

                $id_work_locations = $row2->id_work_locations;
                $array[] =  $id_work_locations;

             }
        }
        return $array;
    }
*/

}



