<?php

namespace App\Models;

use App\Entities\Permission;
use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'permission';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $insertID         = 0;
    protected $returnType       = Permission::class;
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id_user', 'key','value'];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    public function getPermissionAll(string $columna, string $value)//: object|array|null
    {
        return $this->where($columna, $value)->findAll();
    }


    public function getPermissionBy(int $idUser, string $permission)
    {
        $row = $this->db->table('permission')
            ->where('id_user', $idUser)
            ->where('key', $permission)
            ->get()->getFirstRow();
        return $row;
    }


}
