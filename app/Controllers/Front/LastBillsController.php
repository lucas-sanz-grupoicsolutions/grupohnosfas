<?php

namespace App\Controllers\Front;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use DateTime;
use DateTimeZone;
use Config\Services;


use App\Controllers\BaseController;
use App\Entities\Bills;

use App\Libraries\Log;
use App\Models\BillsModel;


use App\Entities\LastBills;
use App\Models\LastBillsModel;


use stdClass;

class LastBillsController extends BaseController
{
    protected Log $log;

    protected Log $logFacturas;
    protected $billsModel;

    protected $lastBillsModel;

    protected $paymentMethodModel;
    protected $ratesModel;
    protected $supplementsModel;
    protected $actualstateModel;
    protected $driver;
    protected $retainerModel;

    protected $tableBillsAlbaranesModel;

    protected $arrayNombre = [];
    protected $db;


    public function __construct()
    {
        $this->log = new Log('Bills/');
        $this->billsModel = model('BillsModel');
        $this->lastBillsModel = model('LastBillsModel');
        $this->paymentMethodModel = model('PaymentMethodModel');
        $this->ratesModel = model('RatesModel');
        $this->supplementsModel = model('SupplementsModel');
        $this->actualstateModel = model('ActualStateModel');
        $this->retainerModel = model('RetainerModel');

        $this->tableBillsAlbaranesModel = model('TableBillsAlbaranesModel');


        helper('form');
    }

    /**
     * Crar Facturas de  albaranes y supplementos
     */
    public function index()
    {




    }




}
