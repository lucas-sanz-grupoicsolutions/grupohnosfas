<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Services_ extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_service' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],

            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],

            'code' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],



            'created_at' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'active' => [
                    'type' => 'INT',

                ],
            ]);

        $this->forge->addKey('id_service', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('services');
    }

    public function down()
    {
        $this->forge->dropTable('services');
    }
}

