<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Retainer extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_retainer' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],

            'date' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'amount' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'bill' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
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
                ]
            ]);

        $this->forge->addKey('id_retainer', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('retainer');
    }

    public function down()
    {
        $this->forge->dropTable('retainer');
    }
}
