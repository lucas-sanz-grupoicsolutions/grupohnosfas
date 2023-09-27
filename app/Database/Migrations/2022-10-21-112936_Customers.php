<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Customers extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_customer' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'names' => [
                'type' => 'VARCHAR',
                'constraint' => '255',

            ],
            'mail' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],

            'phone' => [
                'type' => 'INT',
                'constraint' => '12',
                'null' => false,
            ],
            'dni' => [
                'type' => 'VARCHAR',
                'constraint' => '12',
            ],

            'location' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'province' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],

            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],

            'address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'zip_code' => [
                'type' => 'INT',
                'constraint' => 12,
                'null' => false,
            ],
            'iva' => [
                'type' => 'INT',
                'constraint' => 12,
                'null' => false,
            ],

            'bic' => [
                'type' => 'VARCHAR',
                'constraint' => '11',
            ],

            'iban' => [
                'type' => 'VARCHAR',
                'constraint' => '4',
            ],
            'bank' => [
                'type' => 'VARCHAR',
                'constraint' => 4,

            ],
            'office_bank' => [
                'type' => 'VARCHAR',
                'constraint' => 4,

            ],
            'digital_control' => [
                'type' => 'VARCHAR',
                'constraint' => 2,

            ],
            'bank_count' => [
                'type' => 'VARCHAR',
                'constraint' => 10,

            ],

            'observations' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],


            'contact_person' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],



            'date_signing_mandate' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'recurrent_date' => [
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
            'active' => [
                'type' => 'INT',
                'constraint' => 2,
                'null' => false,
                ]
            ]);

        $this->forge->addKey('id_customers', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('customers');


    }

    public function down()
    {
        $this->forge->dropTable('customers');
    }
}
