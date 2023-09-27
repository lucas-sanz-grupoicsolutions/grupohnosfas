<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Configuration extends BaseConfig
{

    public $defaultGroupUsers = 'user';
    public $regPerPage = 30;

    public $defaultClientes = 'clientes';
    public $regClientesPage = 1000;

    public $defaultCustomers = 'clientes';
    public $regCustomersPage = 20;

    public $defaultworklocation = 'worklocation';
    public $regworklocationPage = 10;

    public $defaultOrders = 'Pedidos';
    public $regOrdersPage = 10;

    public $defaultContainers = 'Containers';
    public $regContainersPage = 100;

    public $defaulBills = 'Bills';
    public $regBillsPage = 100;

    public $defaultBillsAlbaranes = 'BillsAlbaranes';
    public $regBillsAlbaranesPage = 20;





    public $defaultBills = 'BillsList';
    public $regListBillsPage = 100;

    public $defaultAlbaranes = 'ListAlbaranes';
    public $regListAlbaranesPage = 30;



}
