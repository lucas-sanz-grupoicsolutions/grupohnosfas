<?php

namespace App\Controllers\Front;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use DateTime;
use DateTimeZone;
use Config\Services;

use App\Controllers\BaseController;
use App\Entities\Retainer;
use App\Entities\SesionPallets;

use App\Libraries\Log;
use App\Models\RetainerModel;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;


class RetainerController extends BaseController
{
    protected Log $log;
    protected Log $logRetainer;

    protected $retainerModel;

    public function __construct()
    {
        $this->log = new Log('Retainer/');
        $this->retainerModel = model('RetainerModel');

        helper('form');
    }


    public function index()
    {
        return $this->twig->render('Front/Retainer/create.html.twig', ['lugar' => 'index']);
    }


    public function create()
    {

        /*
     if (!$this->validate(validateCreateContenedores())) {

        return redirect()->back()
               ->with('errors', $this->validator->getErrors())
               ->withInput();
       }

       */

        /*   $metros_cubicos = $this->request->getPost('tapa');
      dd($metros_cubicos);
    */

        $retainer = new Retainer($this->request->getPost());
        $this->retainerModel->save($retainer);

        try {

            return redirect()->route('listRetainer')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Anticipo registrado con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listRetainer')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al crear el Anticipo']
            ]);
        }


        return $this->twig->render('Front/Retainer/create.html.twig');
    }



    //Lista Contenedores
    public function result()
    {
        $retainers = $this->retainerModel->orderBy('id_retainer', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Retainer/list.html.twig', ['retainers' => $retainers, 'pager' => $this->retainerModel->pager->links()]);
    }

    //Muestra el Contenedor seleccionado para luego editar
    public function edit($id = null)
    {
        $contenedor = $this->contenedoresModel->where('id_contenedor', $id)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Contenedores/editarContenedor.html.twig', ['contenedor' => $contenedor, 'pager' => $this->contenedoresModel->pager->links()]);
    }


    //Actualiza los datos BD
    public function editSave($id = null)
    {
        /*
        if (!$this->validate(validateUpdatePackage())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }
    */
        $numero = $this->request->getPost('numero');


        $contenedores = new Contenedores($this->request->getPost());
        $contenedores->activo = 1;
        $contenedores->disponible = 1;

        $this->contenedoresModel->save($contenedores);

        try {
            //Muestra mensaje
            return redirect()->route('listPackages')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Paquete modificado con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listPackages')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al modificar el paquete']
            ]);
        }

        //Mostramos los datos actualizados
        $packages = $this->packagesModel->orderBy('id_package', 'ASC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Packages/list.html.twig', ['id_package' => $packages, 'pager' => $this->packagesModel->pager->links()]);
    }



    //Muestra el Paquete  seleccionado para luego editar
    public function editPallet($id = null)
    {
        $packages = $this->packagesModel->where('id_package', $id)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Packages/updatePallet.html.twig', [
            'packages' => $packages, 'pager' => $this->packagesModel->pager->links()
        ]);
    }

    //Actualiza los datos BD el cambio de Pallet
    public function editSavePallet($id = null)
    {


        try {

            $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
            $db = db_connect();
            $data = [
                'id_pallet '  => $this->request->getPost('id_pallet'),
                'updated_at' => $date->format('Y-m-d'),
            ];

            $builder = $db->table('packages');
            $builder->getWhere(['id_package' => $id]);
            $builder->set(
                'id_pallet ',
                'updated_at'
            );
            $builder->where('id_package', $id);
            $builder->update($data);

            //Muestra mensaje
            return redirect()->route('listPackages')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Paquete registrado con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listPackages')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al modificar el paquete']
            ]);
        }

        //Mostramos los datos actualizados
        $packages = $this->packagesModel->orderBy('id_package', 'ASC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Packages/list.html.twig', ['id_package' => $packages, 'pager' => $this->packagesModel->pager->links()]);
    }


    public function preDelete($id = null)
    {
        $packages = $this->packagesModel->where('id_package', $id)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Packages/Predelete.html.twig', ['packages' => $packages, 'pager' => $this->packagesModel->pager->links()]);
    }

    public function deletePackages($id = null)
    {
        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $packages = $this->packagesModel->where('id_package', $id)->paginate(config('Configuration')->regPerPage);

        foreach ($packages as $item) {
            $id_pallet = $item->id_pallet;
        }



        //Mostramos el mensaje de error o correcto
        try {
            $db = db_connect();
            $data = [
                'active '  => 0,
            ];

            $builder = $db->table('packages');
            $builder->getWhere(['id_package' => $id]);
            $builder->set(
                'active ',
            );
            $builder->where('id_package', $id);
            $builder->update($data);


            /* --------- Actuluzamos pallet luego de borrar paquetes - /*/
            //Sumamos el total de metros m2 de todos lo paquetes y guardamos en campo meters_totals de Pallet
            $restaMetrosTotalPackage  =  $this->palletsModel->restaMetrosTotalPackage($id_pallet);

            //Sumamos el total de metros m2 con descentos de todos lo paquetes y guardamos en campo
            //meters_total_dto de Pallet
            $restaMetrosTotalPackageDto  =  $this->palletsModel->restaMetrosTotalPackageDto($id_pallet);

            $num_paquetes_en_pallet = $this->packagesModel->getByNum_package($id_pallet);

            $pall = $this->palletsModel->where('id_pallet', $id_pallet)->paginate(config('Configuration')->regPerPage);

            foreach ($pall as $item) {
                $id_pallet = $item->id_pallet;
            }

            $multiMetrosTotal = $this->palletsModel->MultiMetrosTotalxPricePallet($id_pallet, $restaMetrosTotalPackage);
            $multiMetrosTotalDto = $this->palletsModel->MultiMetrosTotalxPricePalletDto($id_pallet, $restaMetrosTotalPackageDto);

            //Obtenemos los datos del formulario por cada campo
            $data2 = [

                //guardamos el total de m2 con dtos
                'meters_total_dto' => $restaMetrosTotalPackageDto,
                'meters_totals' => $restaMetrosTotalPackage,
                'num_packages' => $num_paquetes_en_pallet,
                'updated_at' => $date->format('Y-m-d'),
                'date_last_modified' => $date->format('Y-m-d'),

                'price_total_dto' => $multiMetrosTotalDto,
                'price_total' => $multiMetrosTotal,


            ];
            $builder = $db->table('pallets');
            $builder->getWhere(['id_pallet' => $id_pallet]);
            $builder->set(
                'meters_total_dto',
                'meters_totals',
                'num_packages',
                'date_last_modified',
                'updated_at',

                'price_total_dto',
                'price_total',

            );

            $builder->where('id_pallet', $id_pallet);
            $builder->update($data2);


            //Muestra mensaje
            return redirect()->route('listPackages')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Paquete eliminado con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listPackages')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al eliminar el Paquete provisional']
            ]);
        }

        //Mostramos los datos actualizados
        $packages = $this->packagesModel->orderBy('id_package', 'ASC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Packages/list.html.twig', ['packages' => $packages, 'pager' => $this->packagesModel->pager->links()]);
    }
}
