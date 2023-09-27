<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Supplements;

class SupplementsModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'supplements';
    protected $primaryKey = 'id_supplements';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = Supplements::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name','pvp','dto','pvp_edit','price_dto','price_total','price_total_all_supp'];

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
    public function editPvp($data)
    {

            foreach ($data as $supplements_id_item) {

                $data1 = [
                    'id_supplements' => $supplements_id_item->id_supplements,
                    'pvp_edit'  => $supplements_id_item->pvp_edit,
                    'dto'  => $supplements_id_item->dto,
                    'price_dto' => $supplements_id_item->price_dto,
                    'price_total'  => $supplements_id_item->price_total,
                    'price_total_all_supp' => $supplements_id_item->price_total_all_supp,

                ];

                $builder = $this->db->table('supplements');
              //  $builder->where(['id_supplements',$supplements_id_item->id_supplements]);
                $builder->set(
                    'id_supplements',
                    'pvp_edit',
                    'dto',
                    'price_dto',
                    'price_total',
                    'price_total_all_supp',

                );

                $builder->where('id_supplements', $supplements_id_item->id_supplements);
                $builder->update($data1);


        }

    }

 /**
     * Obtiene todos los Containers que no existen en esa direccion para mostrar el listado pra seleccionar */
    public function CleanSupplementDto($data)
    {
            foreach ($data as $supplements_id_item) {

                $data1 = [

                    'dto'  => 0,
                    'price_dto' => 0,


                ];

                $builder = $this->db->table('supplements');
              //  $builder->where(['id_supplements',$supplements_id_item->id_supplements]);
                $builder->set(

                    'dto',
                    'price_dto',

                );

                $builder->where('id_supplements', $supplements_id_item->id_supplements);
                $builder->update($data1);

        }
    }




}
