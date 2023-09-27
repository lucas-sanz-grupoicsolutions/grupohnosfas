<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LastBills extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_last_bills' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'num_bill' => [
                'type' => 'int',
                'constraint' => '10',
            ],
            'num_year' => [
                'type' => 'int',
                'unsigned' => true,
                'constraint' => '10',
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



        $this->forge->addKey('id_last_bills', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('last_bills');
    }

    public function down()
    {
        $this->forge->dropTable('last_bills');
    }
}
