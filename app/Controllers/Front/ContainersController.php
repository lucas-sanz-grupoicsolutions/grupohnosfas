<?php

namespace App\Controllers\Front;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use DateTime;
use DateTimeZone;
use Config\Services;

use App\Controllers\BaseController;
use App\Entities\Containers;

use App\Libraries\Log;
use App\Models\containersModel;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;


class ContainersController extends BaseController
{
    protected Log $log;
    protected Log $logContainers;

    protected $containersModel;

    protected $product;
    protected $product_id;
    protected $rate;


    public function __construct()
    {
        $this->log = new Log('Containers/');
        $this->containersModel = model('ContainersModel');

        helper('form');
    }


    public function index()
    {
        return $this->twig->render('Front/Containers/create.html.twig', ['lugar' => 'index']);
    }

    public function create()
    {


        if (!$this->validate(validateCreateContenedores())) {

            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }



        $containers = new Containers($this->request->getPost());
        $containers->active = 1;
        $containers->available = 1;

        try {


        $this->containersModel->save($containers);



        $previousMsg = $this->session->getFlashdata('msg');

        $currentMsg = [
            'type' => 'error',

            'title' => 'CONTENEDOR GUARDADO!',
            'text' => ' Contenedor registrado con exito!',
        ];

        if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
            $this->session->setFlashdata('msg', $currentMsg);
        }

        return redirect()->route('listContainers');
        // return redirect()->route('seeDetailOrder', [$id_last_orders_insert])->with('msg', [
        //     'type' => 'alert-success',
        //     'body' => ['Pedido registrado con exito!']
        // ]);
    } catch (\Throwable $th) {
        $this->log->setLine('Error', $th->getMessage());


        $previousMsg = $this->session->getFlashdata('msg2');

        $currentMsg = [
            'type' => 'error',

            'title' => 'ERROR!',
            'text' => ' Contenedor no se ha registrado.',
        ];

        if (!is_array($previousMsg) || $previousMsg !== $currentMsg) {
            $this->session->setFlashdata('msg2', $currentMsg);
        }

        return redirect()->route('listContainers');

       }

    }
    //Lista Contenedores
    public function result()
    {

        $containers = $this->containersModel->orderBy('residue', 'DESC')->paginate(config('Configuration')->regContainersPage);
        return $this->twig->render('Front/Containers/list.html.twig', ['containers' => $containers, 'pager' => $this->containersModel->pager->links()]);
    }




    //Muestra el Contenedor seleccionado para luego editar
    public function edit($id = null)
    {

        $container = $this->containersModel->where('id_container', $id)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Containers/edit.html.twig', ['containers' => $container, 'pager' => $this->containersModel->pager->links()]);
    }


    //Actualiza los datos BD
    public function editSave($id_container = null)
    {

        if (!$this->validate(validateCreateContenedores())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $date = new Time('now', new \DateTimeZone('Europe/Madrid'));
        $db = db_connect();
        $data = [];

        $data = [
            'residue' => $this->request->getPost('residue'),
            'price' => $this->request->getPost('price'),
            'updated_at' => $date->format('Y-m-d'),
        ];
        $builder = $db->table('containers');
        $builder->getWhere(['id_container' => $id_container]);
        $builder->set(
            'residue',
            'price',
            'updated_at'
        );


        try {
            $builder->where('id_container', $id_container);
            $builder->update($data);

            //Muestra mensaje
            return redirect()->route('listContainers')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Contenedor modificado con exito!']
            ]);
        } catch (\Throwable $th) {
            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listContainers')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al modificar el Contenedor']
            ]);
        }
    }

    public function deleteContainers($id_container = null)
    {


            $db = db_connect();

            $builder = $db->table('containers');
            $builder->where('id_container', $id_container);
            $builder->delete();



           //Mostramos el mensaje de error o correcto
             try {
                // Configurar una respuesta JSON para éxito
                $response = [
                    'success' => true,
                    'message' => 'Pedido eliminado con éxito.'
                ];

                return $this->response->setJSON($response);
                } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());

                // Configurar una respuesta JSON para error
                $response = [
                    'success' => false, // Cambiar a false para indicar un error
                    'message' => 'Error al eliminar el pedido.'
                ];

                return $this->response->setJSON($response);
                }

      }

    }
