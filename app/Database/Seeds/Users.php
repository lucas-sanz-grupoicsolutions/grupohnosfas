<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserSeeder extends Seeder
{
    public function run()
    {
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $user = [
            [
                'name' => 'lucas',
                'mail' => 'lucas.sanz@grupoicsolutions.com',
                'password' => '$2y$10$ksf72BSRC8yrJdFwjRwyrumXmRXZjkRHM1jUrmnRMhtoOXz.yNhiK',
                'id_group' => 1,
                'active' => 1,
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                //Revisado L
                'name' => 'lucas',
                'mail' => 'lucas.sanz@grupoicsolutions.com',
                'password' => '$2y$10$YfPTK1c3TqMyF4QUmb5ZNumKcpXE3oh0YhQsMI3p8PnCMufE1wZYS',
                'id_group' => 1,
                'active' => 1,
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ]
        ];

        $builder = $this->db->table('users');
        $builder->insertBatch($user);
    }
}
