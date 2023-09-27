<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Libraries\Log;


class ActualStateController extends BaseController
{
    protected Log $log;

    protected Log $logActualState;
    protected $actualstateModel;
    protected $containersModel;
    protected $customersModel;
    protected $ordersModel;


    public function __construct()
    {
        $this->log = new Log('ActualState/');
        $this->actualstateModel = model('ActualStateModel');
        $this->containersModel = model('ContainersModel');
        $this->customersModel = model('CustomersModel');
        $this->ordersModel = model('OrdersModel');

        helper('form');
    }

    public function index()
    {
        return $this->twig->render('Front/ActualState/create.html.twig', ['lugar' => 'index']);
    }


    //Lista Estados Actuales
    public function result()
    {
        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);
        $containers_all = $this->containersModel->orderBy('id_container', 'DESC')->paginate(config('Configuration')->regClientesPage);



        $cubicMetersTypes = $this->containersModel->select('cubic_meters')->where('active', 1)->distinct()->orderBy('cubic_meters', 'DESC')->findAll();

        // Eliminar duplicados utilizando una función personalizada
        $uniqueCubicMeters = $this->uniqueCubicMeters($cubicMetersTypes);



        $actualStates = $this->actualstateModel->orderBy('id_actual_state', 'DESC')->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/ActualState/list.html.twig', ['customers_all' => $customers_all,
        'containers_all' => $containers_all,
         'actualStates' => $actualStates,
         'uniqueCubicMeters' => $uniqueCubicMeters, // Pasar los tipos de metros cúbicos a Twig
         'pager' => $this->actualstateModel->pager->links()]);
    }

        private function uniqueCubicMeters($containers)
        {
            $uniqueCubicMeters = [];

            foreach ($containers as $container) {
                $cubicMeters = $container->cubic_meters;

                // Verificar si el valor ya está en el array
                if (!in_array($cubicMeters, $uniqueCubicMeters)) {
                    $uniqueCubicMeters[] = $cubicMeters;
                }
            }

            return $uniqueCubicMeters;
        }





       //busca por id
       public function searchforCustomerActualState($id_customer = null)
       {
           $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);
           $containers_all = $this->containersModel->orderBy('id_container', 'DESC')->paginate(config('Configuration')->regClientesPage);

           $actualStates = $this->actualstateModel->where('id_customer', $id_customer)->paginate(config('Configuration')->regPerPage);

           return $this->twig->render('Front/ActualState/list.html.twig', ['customers_all' => $customers_all,'containers_all' => $containers_all, 'actualStates' => $actualStates,  'pager' => $this->actualstateModel->pager->links()]);
       }


    //busca por id
    public function searchforContainerActualState($cubic_meters = null)
    {
        $customers_all = $this->customersModel->orderBy('id_customer', 'DESC')->paginate(config('Configuration')->regClientesPage);


        $cubicMetersTypes = $this->containersModel->select('cubic_meters')->where('active', 1)->distinct()->orderBy('cubic_meters', 'DESC')->findAll();

        // Eliminar duplicados utilizando una función personalizada
        $uniqueCubicMeters = $this->uniqueCubicMeters($cubicMetersTypes);

        $actualStates = $this->actualstateModel->where('cubic_meters', $cubic_meters)->paginate(config('Configuration')->regClientesPage);

        return $this->twig->render('Front/ActualState/list.html.twig', [
            'customers_all' => $customers_all,
            'uniqueCubicMeters' => $uniqueCubicMeters, // Pasar los tipos de metros cúbicos a Twig
            'actualStates' => $actualStates,
            'pager' => $this->actualstateModel->pager->links()
        ]);
    }


}
