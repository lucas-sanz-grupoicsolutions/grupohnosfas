<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ActualState extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_actual_state' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],

            'id_work_locations' => [
                'type' => 'INT',
                'constraint' => 12,
            ],
            'id_albaran' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'id_container' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'id_customer' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'container_residue' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],

            'work_location_address' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'work_location_location' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],

            'work_location_province' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'work_location_zip_code' => [
                'type' => 'INT',
                'constraint' => 12,],

            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'cubic_meters' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'name_service' => [
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
                ]
            ]);

        $this->forge->addKey('id_actual_state', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('actual_state');
    }

    public function down()
    {
        $this->forge->dropTable('actual_state');
    }
}
