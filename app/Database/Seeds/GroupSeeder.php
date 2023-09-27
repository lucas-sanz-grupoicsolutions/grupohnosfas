<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class GroupSeeder extends Seeder
{
    public function run()
    {
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $group = [
            [
                'name_group' => 'Admin',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'name_group' => 'User',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ]
        ];

        $builder = $this->db->table('groups');
        $builder->insertBatch($group);
    }
}
