<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Containers;
use CodeIgniter\Entity\Cast\ObjectCast;
use stdClass;

use DateTime;
use DateTimeZone;
use CodeIgniter\I18n\Time;

class ContainersModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'containers';
    protected $primaryKey = 'id_container';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = Containers::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['id_customer', 'residue','cubic_meters', 'price','state', 'date', 'available', 'active'];

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
    protected $array;





    /**
     * Obtiene todos los Containers que no existen en esa direccion para mostrar el listado pra seleccionar */
    public function getContainerAvailable(): array
    {

        $array = [];
        $query = $this->db->query('SELECT * FROM containers WHERE available = 1');

        foreach ($query->getResult() as $row2) {
            $array[] = $row2;
        }

        return $array;
    }

    /**
     * Obtiene el id_wok_location por medio del id_container
     * */
    /*
    public function getIdActualStateByIdContainerInfo(array $id_containers)
       {

        $query = 0;
        $array = [];

        $id = 7;

        $builder = $this->db->table('containers');
        $builder->select('*');
        $builder->join('actual_state', 'actual_state.id_container =' . $id);
        $query = $builder->get();
        $query->getResult();
        $array[] = $query->getResult();


        /*
        if($id_container === null || $id_container === false ){
            $query = 0;
        }else{
            $query = $this->db->query('SELECT * FROM containers JOIN actual_sate, actual_state.id_container = 7');
        }
        //


        return $array;
    }
    */

     /**
     * Actualizar el id_container
     * */
    public function upDateContainer($id_container )
      {
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $builder = $this->db->table('containers');
        $data05 = [
            'available' => 1,
            'updated_at' => $date->format('Y-m-d'),
        ];
        $builder->where('id_container', $id_container);
        $builder->update($data05);
    }

       /**
     * Obtiene el id_actual_state por medio del ids_work_locations
     * */
    public function getIdContainerByActualState(array $idActualState )
        {
        $query=null;
        $containers = [];

        if($idActualState === null || $idActualState === false ){
            $query = 0;
        }else{
            foreach ($idActualState as $row2) {
                $query = $this->db->query('SELECT *  FROM containers  JOIN actual_state  ON actual_state.id_container = containers.id_container;');

            }

        }

        $containers [] = $query;
        return $query;
    }
}
