<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;
use App\Models\ContainersModel;

class ActualState extends Entity
{
    public $container;
    public $customer;
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function setContainer()
    {
        $this->container = model("ContainersModel")->where('id_container', $this->id_container)->first();
    }
    public function setCustomer()
    {
        $this->customer = model("CustomersModel")->where('id_customer', $this->id_customer)->first();
    }

}
