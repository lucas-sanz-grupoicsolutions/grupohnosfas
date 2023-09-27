<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Bills extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_bills' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],
            'year' => [
                'type' => 'int',
                'constraint' => '10',
            ],


            'id_work_locations' => [
                'type' => 'INT',
                'constraint' => 12,
                'null' => false,
            ],
            'id_order' => [
                'type' => 'INT',
                'constraint' => 12,

            ],
            'id_customer' => [
                'type' => 'INT',
                'constraint' => 12,
                'null' => false,
            ],


            'id_user' => [
                'type' => 'INT',
                'constraint' => '12',
            ],

            'name_user' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],

            'iva' => [
                'type' => 'INT',
                'null' => true,
            ],

            'dni' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],

            'total_bills' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'retainer_amount' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],


            'charge' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],
            'balance' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

           'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],

            'notes' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],


            'created_at' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'state' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
            ],




            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],


            'customer_mail' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],

            'subtotal_sum_supplements' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],
            'price_total_supp' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],


            'json_supplements_aditional' => [
                'type' => 'VARCHAR',
                'constraint' => '500',
            ],

            'customer_address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],


            'customer_location' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'customer_province' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],


            'customer_zip_code' => [
                'type' => 'INT',
                'constraint' => 12,

            ],

            'customer_dni' => [
                'type' => 'VARCHAR',
                'constraint' => '15',
            ],

            'customer_phone' => [
                'type' => 'INT',
                'constraint' => 9,
            ],

            'customer_bic' => [
                'type' => 'VARCHAR',
                'constraint' => '11',
            ],

            'customer_iva' => [
                'type' => 'INT',
                'constraint' => 9,
            ],
            'customer_iban' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],
            'customer_bank' => [
                'type' => 'INT',
                'constraint' => 4,
            ],
            'customer_office_bank' => [
                'type' => 'INT',
                'constraint' => 4,
            ],
            'customer_digital_control' => [
                'type' => 'INT',
                'constraint' => 2,
            ],
            'customer_bank_count' => [
                'type' => 'INT',
                'constraint' => 12,
            ],


            'work_location_address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'work_location_location' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'work_location_province' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'work_location_zip_code' => [
                'type' => 'INT',
                'constraint' => 12,

            ],

            'rates_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'service_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'service_code' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],

            'sum_dto' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'gross_total' => [
                'type' => 'decimal',
                'constraint' => '10,2',

            ],
            'taxable_base' => [
                'type' => 'decimal',
                'constraint' => '10,2',

            ],

            'total' => [
                'type' => 'decimal',
                'constraint' => '10,2',

            ],

            'fee' => [
                'type' => 'decimal',
                'constraint' => '10,2',

            ],

            'billable' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],


            'bills_supplements' => [
                'type' => 'INT',
                'constraint' => 1,
            ],

            'num_bill' => [
                'type' => 'INT',
                'constraint' => 1,
            ],

            'words_num_bill' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],

            'id_bill_original' => [
                'type' => 'int',
                'constraint' => '10',
            ],

            'rectifyBills' => [
                'type' => 'VARCHAR',
                'constraint' => '10',
            ],


            'expiration_date' => [
                'type' => 'DATE',

            ],
            'remesas' => [
                'type' => 'VARCHAR',
                'constraint' => '4',

            ],


            'date_signing_mandate' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'recurrent_date' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'active' => [
                'type' => 'INT',
                'constraint' => 1,
            ],

        ]);

        $this->forge->addKey('id_bills', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('bills');
    }

    public function down()
    {
        $this->forge->dropTable('bills');
    }
}
