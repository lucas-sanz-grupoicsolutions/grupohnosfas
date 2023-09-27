<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Containers extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_container' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],

            'id_customer' => [
                'type' => 'INT',
                'null' => true,
            ],

            'residue' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'cubic_meters' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],

            'price' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'state' => [
                'type' => 'INT',
                'constraint' => '2',
            ],

            'date' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'available' => [
                'type' => 'INT',
                'constraint' => '2',
            ],

            'active' => [
                'type' => 'INT',
                'constraint' => '2',
                ]
            ]);

        $this->forge->addKey('id_container', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('containers');
    }

    public function down()
    {
        $this->forge->dropTable('containers');
    }
}
