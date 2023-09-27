<?php

namespace App\Controllers\Front;

use CodeIgniter\Cookie\Cookie;
use CodeIgniter\Cookie\CookieStore;
use DateTime;
use DateTimeZone;
use Config\Services;

use App\Controllers\BaseController;
use App\Entities\Rates;

use App\Libraries\Log;
use App\Models\RatesModel;
use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;


class RatesController extends BaseController
{
    protected Log $log;
    protected Log $logRates;

    protected $ratesModel;

    public function __construct()
    {
        $this->log = new Log('Rates/');
        $this->ratesModel = model('RatesModel');

        helper('form');
    }


    public function index()
    {
        return $this->twig->render('Front/Rates/create.html.twig', ['lugar' => 'index']);
    }




    public function create() {

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

       $contenedores = new Contenedores($this->request->getPost());
       $contenedores->activo = 1;
       $contenedores->disponible = 1;

       $this->contenedoresModel->save($contenedores);

       try {

           return redirect()->route('listContainers')->with('msg', [
               'type' => 'alert-success',
               'body' => ['Contenedor registrado con exito!']
           ]);
       } catch (\Throwable $th) {
           $this->log->setLine('Error', $th->getMessage());
           return redirect()->route('listContainers')->with('msg', [
               'type' => 'alert-danger',
               'body' => ['Error al crear el Contenedor']
           ]);
       }


        return $this->twig->render('Front/Contenedores/crear.html.twig' );
    }



    //Lista Contenedores
    public function result()
    {

        $contenedores = $this->contenedoresModel->orderBy('id_contenedor', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Contenedores/listar.html.twig', ['contenedores' => $contenedores, 'pager' => $this->contenedoresModel->pager->links()]);
    }



    //Ver detalle del contenedor
    public function seeDetailContainersF($id = null)
    {
        $contenedor = $this->contenedoresModel->where('id_contenedor', $id)->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Contenedores/seeDetailContainers.html.twig', ['contenedor' => $contenedor, 'pager' => $this->contenedoresModel->pager->links()]);
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

        try{
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

        foreach ($pall as $item){
            $id_pallet = $item->id_pallet;

        }

        $multiMetrosTotal = $this->palletsModel->MultiMetrosTotalxPricePallet($id_pallet,$restaMetrosTotalPackage);
        $multiMetrosTotalDto = $this->palletsModel->MultiMetrosTotalxPricePalletDto($id_pallet,$restaMetrosTotalPackageDto);

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
            'price_total' ,

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

    //Elimina desde la lista de paquetes escaneados
    public function deletePackEscan($id = null)
    {

        //Mostramos el mensaje de error o correcto
        //Conectamos a la BD
        $db = db_connect();
        $packageId = $this->packagesModel->where('id_package', $id)->paginate(config('Configuration')->regBarcodePage);

        //Conectamos a la BD, realizamos la consulta
        $builder = $db->table('packages');
        $builder->where('id_package', $id);
        $builder->delete();

        //Obtenemos el id pallet
        foreach ($packageId as $item) {
            $id_pallet = $item->id_pallet;
        }

        //Obtenemos el num_pallet
        $num = $this->packagesModel->where('id_pallet', $id_pallet)->paginate(config('Configuration')->regBarcodePage);
        //  dd($num);
        $cont_pack = null;
        //Contamos la cantidad de paqquetes
        foreach ($num as $item) {
            $cont_pack++;
        }

        //Lo guardamos en el pallet el valor actualizado de la cantidad de paquetes
        //Luego de barrar uno
        $builder = $db->table('pallets');
        $data2 = [
            'num_packages' => $cont_pack,
        ];


        $builder->where('id_pallet', $id_pallet);
        $builder->update($data2);

        $pallet = $this->palletsModel->where('id_pallet', $id_pallet)->paginate(config('Configuration')->regBarcodePage);

        $pallets = $this->palletsModel->where('id_pallet', $id_pallet)->paginate(config('Configuration')->regBarcodePage);
        $primeraFilaCompleta = $this->packagesModel->where('id_pallet', $id_pallet)->first();
        $packages = $this->packagesModel->where('id_pallet', $id_pallet)->paginate(config('Configuration')->regBarcodePage);
        return $this->twig->render('Front/Packages/listPaquetesEscaneados.html.twig', ['packages' => $packages, 'package' => $primeraFilaCompleta, 'pallets' => $pallets, 'pallet' => $pallet, 'pager' => $this->packagesModel->pager->links()]);
    }



    //Multiplica los valores de Largo,Ancho,Hoja y los envia a Metros cuadrados
    function multiplicar($large, $wide, $sheets)
    {

        $resultFinal =null;

        $int_large = (int)$large;
        $int_wide = (int)$wide;
        $int_sheets = (int)$sheets;

        $resultMulti = $int_large * $int_wide * $int_sheets;



        //convertimos en string el entero para coloarle el . como decimal 95060 a 9.50
        $String_resultMulti = strval($resultMulti);

        if(strlen($String_resultMulti) == 6){
            $decimal =  substr($String_resultMulti, 0, 2);
            $point  =   ".";
            $decimal2 =  substr($String_resultMulti, 2, 2);
            $resultFinal = $decimal . $point . $decimal2;

        }

        if(strlen($String_resultMulti) == 5){
            $decimal =  substr($String_resultMulti, 0, 1);

            $point  =   ".";
            $decimal2 =  substr($String_resultMulti, 1, 2);
           $resultFinal = $decimal . $point . $decimal2;

        }

     //  $valor_float_de_var = floatval($resultFinal);
        return $resultFinal;
    }


    /**
     * Obtiene y filtra el valor del formulariod de tronco,num paquete y pallet
     * Agrega 0 al la diferencia entre el valor intro yu el maximo del tronco,num paquete y pallet
     * Ejemplo si Agregamos
     */
    function codeFilterTrunk(int $Longitud_trunk, string $trunks_code, string $id_trunk)
    {
        //Depende la longitud del trono asignado la diferneica hasta 9 digitos es 0
        //Ejemplo tronco 1155, hasta 9 se reemplaza los de adelante po 0 => 000001155
        switch ($Longitud_trunk) {

            case 1:
                $trunks_code = "00000000" . $id_trunk;
                break;
            case 2:
                $trunks_code = "0000000" . $id_trunk;
                break;
            case 3:
                $trunks_code = "000000" . $id_trunk;
                break;
            case 4:
                $trunks_code = "00000" . $id_trunk;
                break;
            case 5:
                $trunks_code = "0000" . $id_trunk;
                break;
            case 6:
                $trunks_code = "000" . $id_trunk;
                break;
            case 7:
                $trunks_code = "00" . $id_trunk;
                break;
            case 8:
                $trunks_code = "0" . $id_trunk;
                break;
            case 9:
                $trunks_code = $id_trunk;
                break;
        }

        return $trunks_code;
    }

    /**
     * Obtiene y filtra el valor del formulariod de tronco,num paquete y pallet
     * Agrega 0 al la diferencia entre el valor intro yu el maximo del tronco,num paquete y pallet
     * Ejemplo si Agregamos
     */
    function codeFilterNumPaq(int $Longitud_package, string $bndl_code, string $id_package_sipwood)
    {

        //Depende la longitud del paquete asignado la diferneica hasta 9 digitos es 0
        //Ejemplo paquete  105, hasta 9 se reemplaza los de adelante po 0 => 000000105
        switch ($Longitud_package) {

            case 1:
                $bndl_code = "000000" . $id_package_sipwood;
                break;
            case 2:
                $bndl_code = "00000" . $id_package_sipwood;
                break;
            case 3:
                $bndl_code = "0000" . $id_package_sipwood;
                break;
            case 4:
                $bndl_code = "000" . $id_package_sipwood;
                break;
            case 5:
                $bndl_code = "00" . $id_package_sipwood;
                break;
            case 6:
                $bndl_code = "0" . $id_package_sipwood;
                break;
            case 7:
                $bndl_code =  $id_package_sipwood;
                break;
        }

        return $bndl_code;
    }


    /**
     * Obtiene y filtra el valor del formulariod de tronco,num paquete y pallet
     * Agrega 0 al la diferencia entre el valor intro yu el maximo del tronco,num paquete y pallet
     * Ejemplo si Agregamos
     */
    function codeFilterPallet(int $Longitud_pallet, string $pallet_origen_code, string $id_pallet)
    {
        //Depende la longitud del trono asignado la diferneica hasta 9 digitos es 0
        //Ejemplo tronco 1155, hasta 9 se reemplaza los de adelante po 0 => 000001155
        switch ($Longitud_pallet) {

            case 1:
                $pallet_origen_code = "00000" . $id_pallet;
                break;
            case 2:
                $pallet_origen_code = "0000" . $id_pallet;
                break;
            case 3:
                $pallet_origen_code = "000" . $id_pallet;
                break;
            case 4:
                $pallet_origen_code = "00" . $id_pallet;
                break;
            case 5:
                $pallet_origen_code = "0" . $id_pallet;
                break;
            case 6:
                $pallet_origen_code =  $id_pallet;
                break;
        }
        return $pallet_origen_code;
    }

    //ordena por fecha los contenedores
    public function searchforDateDownContenedor()
    {
        $contenedores = $this->contenedoresModel->orderBy('created_at', 'DESC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Contenedores/list.html.twig', ['contenedores' => $contenedores, 'pager' => $this->contenedoresModel->pager->links()]);
    }

//ordena por fecha los contenedores
    public function searchforDateUpContenedor()
    {
        $contenedores = $this->contenedoresModel->orderBy('created_at', 'ASC')->paginate(config('Configuration')->regPerPage);
        return $this->twig->render('Front/Contenedores/list.html.twig', ['contenedores' => $contenedores, 'pager' => $this->contenedoresModel->pager->links()]);
    }


    //busca por id paquete
    public function searchforIdPaq()
    {


        $inputBarcode = $this->request->getPost('id_package_code');

        if (!$this->validate(validateSearchByIdPackage())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }


        $searchString = " ";
        $replaceString = "";
        $inputBarcode = str_replace($searchString, $replaceString, $inputBarcode);

        $comillas1  =  substr($inputBarcode, 6, 1);
        $comillas2  =  substr($inputBarcode, 9, 1);

        $etiquetaPrignitz2 = false;

        $inputBarcode_longitud = strlen($inputBarcode);


        if( $comillas1  == "'" && $comillas2 == "'" && $inputBarcode_longitud == 22){
            $etiquetaPrignitz2 = true;

        }



       if ($inputBarcode_longitud == 22 && $etiquetaPrignitz2 == true) {

                    $searchString = "'";
                    $replaceString = "";
                    $inputBarcode = str_replace($searchString, $replaceString, $inputBarcode);


                    $id_package_code = $this->packagesModel->where('barcode', $inputBarcode)->paginate(config('Configuration')->regPerPage);

                    foreach ($id_package_code as $item) {
                        $id_package_code = $item->id_package_code;

                    }


                    try {

                        //Muestra mensaje
                        return redirect()->route('searchforIdPackage', [$id_package_code]);

                    } catch (\Throwable $th) {
                        $this->log->setLine('Error', $th->getMessage());
                        return redirect()->route('listPackages')->with('msg', [
                            'type' => 'alert-danger',
                            'body' => ['Error al leer el id, vuelva a intentarlo porfavor.']
                        ]);
                    }
       }

/*
        if (!$this->validate(validateSearchByIdPackageOak())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }
*/
        $inputBarcode = $this->request->getPost('id_package_code');
        $sipwood =  substr($inputBarcode, 0, 2);

        if (strlen($inputBarcode) == 29) {

            if (!$this->validate(validateSearchByIdPackage())) {
                return redirect()->back()
                    ->with('errors', $this->validator->getErrors())
                    ->withInput();
            }

            $id_package_code = $this->packagesModel->where('barcode', $inputBarcode)->paginate(config('Configuration')->regPerPage);

            foreach ($id_package_code as $item) {
                $id_package_code = $item->barcode;
            }

            try {     //Muestra mensaje
                return redirect()->route('searchforIdPackage', [$id_package_code]);
            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());
                return redirect()->route('listPackages')->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['Error al leer el id, vuelva a intentarlo porfavor.']
                ]);
            }
        } else if (strlen($inputBarcode) == 22 && $etiquetaPrignitz2 == false) {

            if (!$this->validate(validateSearchByIdPackage())) {
                return redirect()->back()
                    ->with('errors', $this->validator->getErrors())
                    ->withInput();
            }

            $id_package_code = $this->packagesModel->where('id_package_code', $inputBarcode)->paginate(config('Configuration')->regPerPage);

            foreach ($id_package_code as $item) {
                $id_package_code = $item->id_package_code;
            }

            try {     //Muestra mensaje
                return redirect()->route('searchforIdPackage', [$id_package_code]);
            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());
                return redirect()->route('listPackages')->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['Error al leer el id, vuelva a intentarlo porfavor.']
                ]);
            }
        } else if (strlen($inputBarcode) == 25) {

            if (!$this->validate(validateSearchByIdPackage())) {
                return redirect()->back()
                    ->with('errors', $this->validator->getErrors())
                    ->withInput();
            }

            $id_package_code = $this->packagesModel->where('barcode', $inputBarcode)->paginate(config('Configuration')->regPerPage);

            foreach ($id_package_code as $item) {
                $id_package_code = $item->barcode;
            }


            try {     //Muestra mensaje
                return redirect()->route('searchforIdPackage', [$id_package_code]);
            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());
                return redirect()->route('listPackages')->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['Error al leer el id, vuelva a intentarlo porfavor.']
                ]);
            }
        } else if (strlen($inputBarcode) == 21) {

            if (!$this->validate(validateSearchByIdPackage())) {
                return redirect()->back()
                    ->with('errors', $this->validator->getErrors())
                    ->withInput();
            }

            $id_package_code = $this->packagesModel->where('barcode', $inputBarcode)->paginate(config('Configuration')->regPerPage);

            foreach ($id_package_code as $item) {
                $id_package_code = $item->barcode;
            }


            try {     //Muestra mensaje
                return redirect()->route('searchforIdPackage', [$id_package_code]);
            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());
                return redirect()->route('listPackages')->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['Error al leer el id, vuelva a intentarlo porfavor.']
                ]);
            }
        } else if (strlen($inputBarcode) == 20) {


            $searchString = "'";
            $replaceString = "";
            $inputBarcode = str_replace($searchString, $replaceString, $inputBarcode);


            //No lleva validation esta al comienzo
            $id_package_code = $this->packagesModel->where('barcode', $inputBarcode)->paginate(config('Configuration')->regPerPage);

            foreach ($id_package_code as $item) {
                $id_package_code = $item->barcode;
            }

            try {     //Muestra mensaje
                return redirect()->route('searchforIdPackage', [$id_package_code]);
            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());
                return redirect()->route('listPackages')->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['Error al leer el id, vuelva a intentarlo porfavor.']
                ]);
            }
        } else if (strlen($inputBarcode) == 18 && $sipwood == 36 || $sipwood == 37) {

            if (!$this->validate(validateSearchByIdPackage())) {
                return redirect()->back()
                    ->with('errors', $this->validator->getErrors())
                    ->withInput();
            }

            $id_package_code = $this->packagesModel->where('barcode', $inputBarcode)->paginate(config('Configuration')->regPerPage);

            foreach ($id_package_code as $item) {
                $id_package_code = $item->barcode;
            }



            try {     //Muestra mensaje
                return redirect()->route('searchforIdPackage', [$id_package_code]);
            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());
                return redirect()->route('listPackages')->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['Error al leer el id, vuelva a intentarlo porfavor.']
                ]);
            }


        } else {


            try {     //Muestra mensaje
                return redirect()->route('listPackages')->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['No exite el paquete!, revise si el codigo de barras si esta dañado o insertelo manualmente.']
                ]);
            } catch (\Throwable $th) {
                $this->log->setLine('Error', $th->getMessage());
                return redirect()->route('listPackages')->with('msg', [
                    'type' => 'alert-danger',
                    'body' => ['Error al leer el id, vuelva a intentarlo porfavor.']
                ]);
            }
        }

        //Redirigimos y envaimos el pack
        return redirect()->route('searchforIdPackage', [$id_package_code]);
    }


    public function searchforIdPackage($id_package_code)
    {

        if (strlen($id_package_code) == 22) {
            $id_package_code = $this->packagesModel->where('id_package_code', $id_package_code)->paginate(config('Configuration')->regPerPage);
        } else {
            $id_package_code = $this->packagesModel->where('barcode', $id_package_code)->paginate(config('Configuration')->regPerPage);
        }



        return $this->twig->render('Front/Packages/list.html.twig', ['packages' => $id_package_code, 'pager' => $this->packagesModel->pager->links()]);
    }

   /**
     * Busqueda por fecha
     */
    public function searchPackageforDateC()
    {

        $fechaIni = $this->request->getPost('fechaIni');

        if ($fechaIni == "") {
            if (!$this->validate(validateSearchByDatePackage())) {
                return redirect()->back()
                    ->with('errors', $this->validator->getErrors())
                    ->withInput();
            }
        }


        try{
            return redirect()->route('searchPackageforDateC2',[$fechaIni]);


        } catch (\Throwable $th) {


            $this->log->setLine('Error', $th->getMessage());
            return redirect()->route('listPackages')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al buscar el Paquete ']
            ]);
        }


    }

        /**
     * Busqueda de Pallets cerrados por fecha
     */
    public function searchPackageforDateC2($fechaIni)
    {

        $packages = $this->packagesModel->orderBy('id_package', 'DESC')->paginate(config('Configuration')->regPerPage);

        foreach ($packages as $item) {
            $packages = $this->packagesModel->where('created_at', $fechaIni)->paginate(config('Configuration')->regPerPage);
        }

        return $this->twig->render('Front/Packages/list.html.twig', ['packages' => $packages, 'pager' => $this->packagesModel->pager->links()]);


    }



}
