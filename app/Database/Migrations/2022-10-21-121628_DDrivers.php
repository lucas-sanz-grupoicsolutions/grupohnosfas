<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DDrivers extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_driver' => [
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

            'province' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],

            'phone' => [
                'type' => 'INT',
                'constraint' => '12',
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
                'constraint' => '1',
                ]
            ]);

        $this->forge->addKey('id_driver', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('ddrivers');
    }

    public function down()
    {
        $this->forge->dropTable('ddrivers');
    }
}
