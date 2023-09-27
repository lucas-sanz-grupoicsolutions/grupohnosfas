<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Suplements extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_supplements' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],

            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'pvp' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],
            'pvp_edit' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'dto' => [
                'type' => 'INT',
            ],

            'price_dto' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],
            'price_total' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'price_total_all_supp' => [
                'type' => 'decimal',
                'constraint' => '10,2',
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

        $this->forge->addKey('id_supplements', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('supplements');
    }

    public function down()
    {
        $this->forge->dropTable('supplements');
    }
}
