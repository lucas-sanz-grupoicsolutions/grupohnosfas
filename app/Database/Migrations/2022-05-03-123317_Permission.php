<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Permission extends Migration
{
    public function up()
    {
      /*  $this->db->disableForeignKeyChecks();*/
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => '12',
                'unsigned' => true,
                'null' => false
            ],
            'key' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'value' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_user', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('permission');
      /*  $this->db->enableForeignKeyChecks();*/
    }

    public function down()
    {
        $this->forge->dropTable('permission');
    }
}
