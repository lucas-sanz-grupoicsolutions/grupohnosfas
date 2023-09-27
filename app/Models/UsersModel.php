<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\User;

class UsersModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = User::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['name', 'mail', 'password', 'id_group', 'active'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
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
    protected $beforeInsert = ['addGroup'];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];


    protected $assignGroup;

    protected function addGroup($data): array
    {

        $data['data']['id_group'] = $this->assignGroup;
        return $data;
    }

    public function withGroup(string $group)
    {
        $row = $this->db->table('groups')
            ->where('name_group', $group)
            ->get()->getFirstRow();
        //select * from grpups where name_group = $group
        if ($row != null) {
            $this->assignGroup = $row->id_group;
        }
        return $this->assignGroup;
    }

    public function getUserBy(string $columna, string $value)
    {
        return $this->where($columna, $value)->first();
    }

    public function getUserByUserName(string $mail)
    {
        if (!$user = $this->getUserBy('mail', $mail)) {
            throw new \Exception("User does not exist.");
        }
        return $user;
    }
}
