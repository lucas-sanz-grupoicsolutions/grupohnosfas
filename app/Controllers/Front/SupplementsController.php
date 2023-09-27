<?php

namespace App\Controllers\Front;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use DateTime;
use DateTimeZone;
use Config\Services;

use App\Controllers\BaseController;
use App\Entities\Supplements;

use App\Libraries\Log;
use App\Models\SupplementsModel;
use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;


class SupplementsController extends BaseController
{
    protected Log $log;
    protected Log $logsupplements;

    protected $supplementsModel;
    protected $ordersModel;
    protected $albaranesModel;

    public function __construct()
    {
        $this->log = new Log('supplements/');
        $this->supplementsModel = model('SupplementsModel');
        $this->ordersModel = model('OrdersModel');
        $this->albaranesModel = model('AlbaranesModel');

        helper('form');
    }


    public function index()
    {
        return $this->twig->render('Front/Supplements/create.html.twig', ['lugar' => 'index']);
    }


    public function create()
    {


        if (!$this->validate(validateSupplementsCreate())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $name = $this->request->getPost('name');
        $pvp = $this->request->getPost('pvp');

        $supplements = new Supplements();
        $supplements->name = $name;
        $supplements->pvp = $pvp;

        try {

            $this->supplementsModel->save($supplements);

            $previousMsg = $this->session->getFlashdata('msg');
            $currentMsg = [
                'type' => 'error',

                'title' => 'Suplemento guardado !',
                'text' => 'El suplemento se ha registrado con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('listSuplements');
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION !',
                'text' => 'Error al registrar el suplemento!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('listSuplements');
        }
    }


    //Lista
    public function result()
    {

        $orderSupplements = $this->albaranesModel->select('supplements')->findAll();

       // Inicializa un array para almacenar los id_supplement
        $orderSupplementsIds = [];

        // Recorre cada registro de albaranes
        foreach ($orderSupplements as $s) {

            // Verifica si el valor de "supplement" no es nulo
            if ($s->supplements !== null) {
                // Decodifica el JSON en un array asociativo
                $orderSupplementsIds[] = json_decode($s->supplements, true);

            }
        }


        $supplements = $this->supplementsModel->orderBy('id_supplements', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Supplements/list.html.twig', ['$orderSupplementsIds'=>$orderSupplementsIds,'supplements' => $supplements, 'pager' => $this->supplementsModel->pager->links()]);
    }





    //Muestra el Contenedor seleccionado para luego editar
    public function editSuplements($id = null)
    {


        $supplements = $this->supplementsModel->where('id_supplements', $id)->paginate(config('Configuration')->regPerPage);

        return $this->twig->render('Front/Supplements/edit.html.twig', ['supplements' => $supplements, 'pager' => $this->supplementsModel->pager->links()]);
    }

    public function editSuplementsSave($id_supplements = null)
    {

        if (!$this->validate(validateSupplementsEdit())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }


        $db = db_connect();

        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));

        $name = $this->request->getPost('name');
        $pvp = $this->request->getPost('pvp');

        $data = [
            'name'  =>  $name,
            'pvp'  =>  $pvp,

            'updated_at' => $date->format('Y-m-d'),
        ];

        $builder = $db->table('supplements');
        $builder->getWhere(['id_supplements' => $id_supplements]);
        $builder->set(
            'name',
            'pvp',

            'updated_at'
        );


        try {
            $builder->where('id_supplements', $id_supplements);
            $builder->update($data);

            $previousMsg = $this->session->getFlashdata('msg');
            $currentMsg = [
                'type' => 'error',

                'title' => 'Suplemento modificado !',
                'text' => 'El suplemento se ha modificado con exito!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg', $currentMsg);
            }

            return redirect()->route('listSuplements');
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            $previousMsg = $this->session->getFlashdata('msg2');
            $currentMsg = [
                'type' => 'error',

                'title' => 'ATENCION !',
                'text' => 'Error al modificar el suplemento!',
            ];

            if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
                $this->session->setFlashdata('msg2', $currentMsg);
            }

            return redirect()->route('listSuplements');
        }
    }

    public function deleteSupplements($id_supplements = null)
    {

        try {

            $db = db_connect();

            $builder = $db->table('supplements');
            $builder->where('id_supplements', $id_supplements);
            $builder->delete();

            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Suplemento eliminado con exito!'
            ];


            return $this->response->setJSON($response);

        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());



            // Configurar una respuesta JSON para éxito
            $response = [
                'success' => true,
                'message' => 'Error al eliminar el suplemento.'
            ];


            return $this->response->setJSON($response);
        }
    }
}
