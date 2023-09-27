<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $group = [

  /** Permiso Administrador */
            [
                'id_user' => 1,
                'key' => 'registrar',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],

            [
                'id_user' => 1,
                'key' => 'create_partida',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 1,
                'key' => 'list_partida',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],

            [
                'id_user' => 1,
                'key' => 'title_partidas',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 1,
                'key' => 'show_partidas',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],

            /**Albaranes */
            [
                'id_user' => 1,
                'key' => 'show_albaranes',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            /**--------- */

              /** Trabajador  */
            [
                'id_user' =>5,
                'key' => 'list_partida',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],



            //Revisado L  Pallets Provisionales
            [
                'id_user' =>5,
                'key' => 'create_pallet',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 5,
                'key' => 'list_pallet',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 5,
                'key' => 'title_pallet',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 5,
                'key' => 'show_pallet',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
             //Revisado L  Pallets
             [
                'id_user' => 1,
                'key' => 'create_pallet',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 1,
                'key' => 'list_pallet',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 1,
                'key' => 'title_pallet',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 1,
                'key' => 'show_pallet',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            //Packages
            [
                'id_user' => 1,
                'key' => 'create_packages',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 1,
                'key' => 'list_packages',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 1,
                'key' => 'title_packages',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ],
            [
                'id_user' => 1,
                'key' => 'show_packages',
                'value' => '1',
                'created_at' => $date->format('Y-m-d H:i:s'),
                'updated_at' => $date->format('Y-m-d H:i:s')
            ]

        ];

        $builder = $this->db->table('permission');
        $builder->insertBatch($group);
    }
}
