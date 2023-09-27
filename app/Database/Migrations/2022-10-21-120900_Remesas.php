<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Remesas extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_remesas' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'id_bills' => [
                'type' => 'VARCHAR',
                'constraint' => '1200',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'effect' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
            ],


        ]);

        $this->forge->addKey('id_remesas', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('remesas');
    }

    public function down()
    {
        $this->forge->dropTable('remesas');
    }
}
