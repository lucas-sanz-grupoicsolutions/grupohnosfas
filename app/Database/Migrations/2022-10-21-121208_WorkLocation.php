<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class WorkLocation extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_work_locations' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'id_customer' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'name_customer' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'location' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'province' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'zip_code' => [
                'type' => 'VARCHAR',
                'constraint' => '12',
            ],
            'observations' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'active' => [
                'type' => 'INT',
                'constraint' => 2,
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



        $this->forge->addKey('id_work_locations', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('WorkLocation');
    }

    public function down()
    {
        $this->forge->dropTable('WorkLocation');
    }
}
