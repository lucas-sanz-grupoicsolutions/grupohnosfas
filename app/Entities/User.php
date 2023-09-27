<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class User extends Entity
{
    protected $dates = ['created_at', 'updated_at'];

    protected function setPassword(string $password)
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    public function getRole()
    {
        $groupModel = model('GroupsModel');
        return $groupModel->where('id_group', $this->id_group)->first();
    }
}
