<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class Supplements extends Entity
{
    protected $dates = ['created_at', 'updated_at'];

    public function priceToDiscount()
    {
        return $this->pvp_edit * $this->dto / 100;
    }

    public function priceWithDiscount()
    {
        return $this->pvp_edit - $this->priceToDiscount();
    }

}
