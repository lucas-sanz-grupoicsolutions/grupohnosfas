<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitSeeder extends Seeder
{
    public function run()
    {
        $this->call('GroupSeeder');
        $this->call('UserSeeder');
        $this->call('PermissionSeeder');
    }
}
