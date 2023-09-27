<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\ActualState;

class ActualStateModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'actual_state';
    protected $primaryKey = 'id_actual_state';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = ActualState::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
                                'id_actual_state',
                                'id_albaran',
                                'id_work_locations',
                                'id_container',
                                'id_customer',
                                'container_residue',
                                'cubic_meters',
                                'work_location_address',
                                'work_location_location',
                                'work_location_province',
                                'work_location_zip_code',
                                'customer_name',
                                'name_service'

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

     /**
     * Obtiene todos los Containers que no existen en esa direccion para mostrar el listado pra seleccionar */
    public function getContainer(string $id_container)
    {

        $query = $this->db->query('SELECT id_container FROM containers WHERE id_container = ' . $id_container);

        if($id_container === null || $id_container === false ){

            $query = false;

        }else{

            foreach ($query->getResult() as $row2) {

                $id_container02 = $row2->id_container;

                    //Si coincide con el container pasado
                    if($id_container === $id_container02 ){

                        $query = $this->db->query('SELECT id_container FROM containers WHERE id_container = ' . $id_container);

                    }else{

                        $query = false;

                    }
             }

        }

        return $query;
    }


      /**
     * Obtiene el id_actual_state por medio del id_wod_location
     */
    public function getIdActualState($id_work_location): array
    {

         $id_actual_state = 0;

         $query = $this->db->query('SELECT id_actual_state FROM actual_state WHERE id_work_location = ' . $id_work_location);

        if($id_work_location === null || $id_work_location === false ){

            $id_actual_state = 0;

        } else{
                 //Si no esta vacia
                 foreach ($query->getResult() as $row2) {
                    $id_actual_state = $row2->id_actual_state;

                    return $id_actual_state;

                 }
        }

        return $id_actual_state;
    }


    /**
     * Obtiene Container Compara si existe el contenedor en esa direccion y devuelve todos los id_container de esa dirrecion en un array
     */
    public function getContainerWorkLocation($id_work_location): array
    {

        $idsContainers = [];

        $query = $this->db->query('SELECT id_container FROM actual_state WHERE id_work_locations = ' . $id_work_location);

         if($id_work_location === null || $id_work_location === false ){

            $idsContainers[] = null;

        }

        if(!$query !== null || !$query ){

              foreach ($query->getResult() as $row2) {

                $idsContainers[] =  $row2->id_container;

              }
        }


       return $idsContainers;
    }

     /**
     * Obtiene el id_wok_location por medio del id_container
     * */
    public function getIdActualStateByIdContainer(string $id_container )    {


        $query = $this->db->query('SELECT id_actual_state FROM actual_state WHERE id_container = ' . $id_container);

        $id_actual_state = 0;

        if($id_container === null || $id_container === false ){
            $id_actual_state = 0;

        }else{
            foreach ($query->getResult() as $row2) {

                $id_actual_state = $row2->id_actual_state;

             }
        }
        return $id_actual_state;
    }

     /**
     * Borra el id_actual_state
     * */
    public function deleteIdActualState($idActualState )
      {
        $builder01 = $this->db->table('actual_state');
        $builder01->where('id_actual_state',$idActualState);
        $builder01->delete();
    }



}


