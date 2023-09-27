<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Entities\Customers;

class CustomersModel extends Model
{
    protected $DBGroup = 'default';
    protected $table = 'customers';
    protected $primaryKey = 'id_customer';
    protected $useAutoIncrement = true;
  //  protected $insertID = 0;
    protected $returnType = Customers::class;
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = ['names',
    'mail',
    'phone',
     'dni',
     'location',
     'province',
     'payment_method',
     'address',
     'zip_code',

     'iva',

     'bic',
     'iban',
     'bank',
     'office_bank',
     'digital_control',
     'bank_count',

     'date_signing_mandate',
     'recurrent_date',

     'observations',
     'contact_person',
     'active'];


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




     /**
     * Obtiene el pallet del Albaran
     */
    public function getNameCustomers(string $id_customer)
    {
        $query = $this->db->query('SELECT names FROM customers WHERE id_customer = ' . $id_customer);

        $names = null;

        foreach ($query->getResult() as $row) {

           $names = $row->names;

        }

        return $names;
    }



}
