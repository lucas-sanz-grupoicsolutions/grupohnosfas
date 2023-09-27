<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Vehicles extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_vehicle' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],

            'car_registration' => [
                'type' => 'VARCHAR',
                'constraint' => '12',
            ],

            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],

            'make' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],

            'model' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],


            'registration_date' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'date_itv' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'observations' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
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
                'constraint' => 12,
                ]
            ]);

        $this->forge->addKey('id_vehicle', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('Vehicles');
    }

    public function down()
    {
        $this->forge->dropTable('Vehicles');
    }
}
