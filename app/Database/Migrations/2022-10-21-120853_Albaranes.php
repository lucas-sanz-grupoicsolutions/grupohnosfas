<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Albaranes extends Migration
{
    public function up()
    {
        $this->forge->addField([

            'id_albaran' => [
                'type' => 'INT',
                'constraint' => 12,
                'unsigned' => true,
                'auto_increment' => true,
                'null' => false,
            ],

            'id_order' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'id_customer' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'id_work_location' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'id_driver' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'id_vehicle' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'id_payment_method' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'id_container' => [
                'type' => 'INT',
                'constraint' => 12,
            ],

            'id_rates' => [
                'type' => 'INT',
                'constraint' => 12,
            ],
            'id_service' => [
                'type' => 'INT',
                'constraint' => 12,
            ],
            'id_payment_method' => [
                'type' => 'INT',
                'constraint' => 12,
            ],


            'id_user' => [
                'type' => 'INT',
                'constraint' => '12',
            ],

            'name_user' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],

            'customer_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],


            'customer_mail' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
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
                'constraint' => '4',
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
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],




            'container_residue' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'container_m3' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'container_price' => [
                'type' => 'decimal',
                'constraint' => '10,2',

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


            'driver_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'driver_phone' => [
                'type' => 'INT',
                'constraint' => 9,
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


            'vehicle_name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'vehicle_make' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'vehicle_model' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
            ],
            'vehicle_car_registration' => [
                'type' => 'VARCHAR',
                'constraint' => '12',
            ],




            'retainer_amount' => [
                'type' => 'decimal',
                'constraint' => '10,2',

            ],

            'amount' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],


            'notas' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'albaran_status' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
            ],

            'tax_base' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'iva' => [
                'type' => 'decimal',
                'constraint' => '10,2',

            ],
            'subtotal' => [
                'type' => 'decimal',
                'constraint' => '10,2',

            ],

            'total' => [
                'type' => 'decimal',
                'constraint' => '10,2',

            ],

            'no_fee' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],

            'billable' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],


            'preprinted' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],

            'supplements' => [
                'type' => 'VARCHAR',
                'constraint' => '1000',
            ],


            'subtotal_sum_supplements' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'price_total_supp' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'created_by' => [
                'type' => 'VARCHAR',
                'constraint' => '50',
            ],

            'planned_date_realization' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'driver_assignment_date' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'date_performance_service' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'discount' => [
                'type' => 'INT',
                'constraint' => '12',
            ],

            'price_discount' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],
            'amount_tax_base_discount' => [
                'type' => 'decimal',
                'constraint' => '10,2',
            ],

            'active' => [
                'type' => 'INT',
                'constraint' => '12',
            ],
            'arrival_time_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'departure_time' => [
                'type' => 'DATETIME',
                'null' => true,
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

        $this->forge->addKey('id_albaran', true);
		//$this->forge->addForeignKey('id_group', 'groups', 'id_group', 'CASCADE', 'CASCADE');
		$this->forge->createTable('albaranes');
    }

    public function down()
    {
        $this->forge->dropTable('albaranes');
    }
}
