<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PaymentMethod extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_payment_method' => [
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

            'created_at' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATE',
                'null' => true,
                ]
            ]);



        $this->forge->addKey('id_payment_method', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('payment_method');
    }

    public function down()
    {
        $this->forge->dropTable('payment_method');
    }
}

