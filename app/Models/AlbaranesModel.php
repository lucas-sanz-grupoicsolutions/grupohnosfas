<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Albaranes;

class AlbaranesModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'albaranes';
    protected $primaryKey = 'id_albaran';
    protected $useAutoIncrement = true;
    protected $insertID = 0;
    protected $returnType = Albaranes::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [

                                'preprinted',
                                'id_order',
                                'id_customer',
                                'id_work_location',
                                'id_driver',
                                'id_vehicle',
                                'id_container',
                                'id_rates',
                                'id_service',
                                'id_payment_method',
                                'id_user',

                                'name_user',

                                'customer_name',
                                'customer_mail',
                                'customer_address',
                                'customer_location',
                                'customer_province',
                                'customer_zip_code',
                                'customer_dni',
                                'customer_phone',
                                'customer_bic',
                                'customer_iva',
                                'customer_iban',
                                'customer_bank',
                                'customer_office_bank',
                                'customer_digital_control',
                                'customer_bank_count',

                                'payment_method',

                                'container_residue',
                                'container_m3',
                                'container_price',

                                'work_location_address',
                                'work_location_location',
                                'work_location_province',
                                'work_location_zip_code',

                                'driver_name',
                                'driver_phone',

                                'rates_name',

                                'service_name',
                                'service_code',

                                'vehicle_name',
                                'vehicle_make',
                                'vehicle_model',
                                'vehicle_car_registration',

                                'retainer_amount',
                                'amount',

                                'notas',
                                'albaran_status',
                                'tax_base',
                                'iva',
                                'subtotal',
                                'total',
                                'no_fee',
                                'billable',
                                'departure_time',
                                'arrival_time_date',

                                'supplements',
                                'subtotal_sum_supplements',
                                'price_total_supp',
                                'created_by',
                                'planned_date_realization',
                                'driver_assignment_date',
                                'date_performance_service',
                                'discount',
                                'price_discount',
                                'amount_tax_base_discount',
                                'active'



                            ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'date';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];


  protected $assignGroup;


}


