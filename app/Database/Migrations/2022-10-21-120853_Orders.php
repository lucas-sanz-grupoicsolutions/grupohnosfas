<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Orders extends Migration
{

    public function up()
    {

        $this->forge->addField([

            'id_order' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],

            'id_customer' => [
                'type' => 'INT',
                'constraint' => 100,
            ],

            'id_work_location' => [
                'type' => 'INT',
                'constraint' => 100,
            ],

            'id_driver' => [
                'type' => 'INT',
                'constraint' => '12',
            ],

            'id_vehicle' => [
                'type' => 'INT',
                'constraint' => '12',
            ],


            'id_container' => [
                'type' => 'INT',
                'constraint' => '12',
            ],
            'id_service' => [
                'type' => 'INT',
                'constraint' => '12',
            ],

            'id_user' => [
                'type' => 'INT',
                'constraint' => '12',
            ],

            'name_user' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],


            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],

            'name_customer' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],


            'name_work_location' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],


            'notas' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],


            'planned_date' => [
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

            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],
            'state' => [
                'type' => 'INT',
                'constraint' => '12',
            ],
            'active' => [
                'type' => 'INT',
                'constraint' => '12',
            ],

            ]);

        $this->forge->addKey('id_order', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('orders');
    }

    public function down()
    {
        $this->forge->dropTable('orders');
        $this->db->query('ALTER TABLE orders DROP FOREIGN KEY fk_orders_drivers');

    }
}
