<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Controllers\CustomersController;
use App\Controllers\Front\PDF_Code128Class;

use DateTime;
use DateTimeZone;
use Config\Services;

use App\Entities\Customers;
use App\Entities\Config;
use App\Entities\Albaranes;
use App\Libraries\Log;
use App\Libraries\FPDF\FPDF;

use App\Models\CustomersModel;
use App\Models\ConfigModel;
use App\Models\AlbaranesModel;

use CodeIgniter\I18n\Time;


class mdb extends BaseController
{
    $db_conn = new COM("ADODB.Connection");
    $connstr = "DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=". realpath("bd1.mdb").";";
    $db_conn->open($connstr);

} // eoc mdb

