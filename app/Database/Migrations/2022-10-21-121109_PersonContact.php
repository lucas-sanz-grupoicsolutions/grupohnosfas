<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PersonContact extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_personC' => [
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
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',

            ],

            'position' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'phone' => [
                'type' => 'INT',
                'constraint' => '12',
            ],
            'mail' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
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
                'constraint' => 1,
                ]
            ]);




        $this->forge->addKey('id_personC', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('personcontact',true);


    }


public function down()
{
    $this->forge->dropTable('personcontact');
}
}
