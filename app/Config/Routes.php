<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('App\Controllers\Front\Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->group('/', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    $routes->get('', 'Home::index', ['as' => 'home']);
    $routes->get('not-permission', 'Home::notPermission',  ['as' => 'notPermission']);
});

/*
$routes->group('/auth', ['namespace' => 'App\Controllers\Auth'], function ($routes) {
	$routes->get('registro', 'Register::index', ['as' => 'register']);
	$routes->post('add', 'Register::addUser' ,['as' => 'addUser']);
	$routes->get('login', 'Login::index', ['as' => 'login']);
	$routes->post('check', 'Login::signin', ['as' => 'signin']);
	$routes->get('logout', 'Login::signout', ['as' => 'logout']);
});
*/
$routes->group('/', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    $routes->get('', 'Home::index', ['as' => 'home']);
    $routes->get('not-permission', 'Home::notPermission',  ['as' => 'notPermission']);
});

//FPdf
$routes->group('/Fpdf', ['namespace' => 'App\Controllers\Front'], function ($routes) {

    $routes->get('/', 'FpdfController::index', ['as' => 'showFormBarcorde']);
    $routes->post('crear', 'FpdfController::create', ['as' => 'addBarcode']);
    $routes->get('ver/(:any)', 'FpdfController::seeDetailBarcode/$1', ['as' => 'seeDetailBarcode']);

     //Imprime etiquetas codigo barras individsual
    $routes->post('imprimePdf/(:any)', 'FpdfController::imprimePdf/$1', ['as' => 'imprimePdf']);

    //Imprime etiquetas codigo barras varios
    $routes->post('imprimePdfVarios', 'FpdfController::imprimePdfVarios', ['as' => 'imprimePdfVarios']);

    //Imprime albaran
     $routes->get('imprimePdfAlbaran/(:any)', 'FpdfController::imprimePdfAlbaran/$1', ['as' => 'imprimePdfAlbaran']);
      //Imprime albaran
     $routes->get('PdfFactura/(:any)', 'FpdfController::PdfBills/$1', ['as' => 'printPdfBills']);

     $routes->get('PdfFacturaSuplementos/(:any)', 'FpdfController::PdfBillsSupp/$1', ['as' => 'printPdfBillsSupplements']);

     //Proforma
     $routes->get('PdfProForma/(:any)', 'FpdfController::PdfBillsProForma/$1', ['as' => 'printPdfBillsProforma']);

});



$routes->group('/auth', ['namespace' => 'App\Controllers\Auth'], function ($routes) {
    $routes->get('registro', 'RegisterController::index', ['as' => 'register']);
    $routes->post('add', 'RegisterController::addUser', ['as' => 'addUser']);
    $routes->get('login', 'LoginController::index', ['as' => 'login']);
    $routes->post('check', 'LoginController::signin', ['as' => 'signin']);
    $routes->get('logout', 'LoginController::signout', ['as' => 'logout']);

    $routes->get('PasswordReset', 'LoginController::PasswordReset', ['as' => 'PasswordReset']);
    $routes->post('ResetearPassword', 'LoginController::sendMail', ['as' => 'sendMail']);

    $routes->post('changePassword', 'LoginController::changePassword', ['as' => 'changePassword']);
});


$routes->group('/auth', ['namespace' => 'App\Controllers\Auth'], function ($routes) {
    $routes->get('registro', 'RegisterController::index', ['as' => 'register']);
    $routes->post('add', 'RegisterController::addUser', ['as' => 'addUser']);
    $routes->get('login', 'LoginController::index', ['as' => 'login']);
    $routes->post('check', 'LoginController::signin', ['as' => 'signin']);
    $routes->get('logout', 'LoginController::signout', ['as' => 'logout']);

    $routes->get('PasswordReset', 'LoginController::PasswordReset', ['as' => 'PasswordReset']);
    $routes->post('ResetearPassword', 'LoginController::sendMail', ['as' => 'sendMail']);
    $routes->post('changePassword', 'LoginController::changePassword', ['as' => 'changePassword']);
});

//Contenedores
$routes->group('/Contenedores', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
 //$routes->group('/Contenedores', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    $routes->get('/', 'ContainersController::index', ['as' => 'showFormContainers']);

    $routes->get('listar', 'ContainersController::result', ['as' => 'listContainers']);
    $routes->post('crear', 'ContainersController::create', ['as' => 'addContainers']);
    $routes->get('verContenedor/(:any)', 'ContainersController::seeDetailContainersF/$1', ['as' => 'seeDetailContainers']);
    $routes->get('editar/(:any)', 'ContainersController::edit/$1', ['as' => 'updateContainers']); //Con parametro de envio
    $routes->post('editarGuardar/(:any)', 'ContainersController::editSave/$1', ['as' => 'updateContainersSave']); //Listado luego de modificar
    $routes->post('eliminar/(:any)', 'ContainersController::deleteContainers/$1', ['as' => 'deleteContainer']); //Listado luego de eliminar
});



//Clientes
  $routes->group('/Clientes', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
 //$routes->group('/Clientes', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    $routes->get('/', 'CustomersController::index', ['as' => 'showFormCustomers']);

    //Formulario crea paquete
    $routes->post('crear', 'CustomersController::create', ['as' => 'addCustomers']);
    $routes->get('listar', 'CustomersController::result', ['as' => 'listCustomers']);
    $routes->get('ver/(:num)', 'CustomersController::seeDetailCustomers/$1', ['as' => 'seeDetailCustomers']);

    $routes->get('editar/(:any)', 'CustomersController::edit/$1', ['as' => 'updateCustomers']); //Con parametro de envio
    $routes->post('editarGuardar/(:any)', 'CustomersController::editSave/$1', ['as' => 'updateCustomersSave']); //Listado luego de modificar

    $routes->get('editarPersonaCCliente/(:any)', 'CustomersController::borrarPersonaContactoEditarCliente/$1', ['as' => 'borrarPersonaContactoEditarCliente']); //Con parametro de envio

    $routes->get('Preeliminar/(:any)', 'CustomersController::preDelete/$1', ['as' => 'PredeleteCustomers']); //Listado luego de eliminar
    $routes->post('eliminar/(:any)', 'CustomersController::deleteCustomers/$1', ['as' => 'deleteCustomers']); //Listado luego de eliminar

    $routes->post('guardarFechaMandato/(:any)', 'CustomersController::editDateMandateRemesa/$1', ['as' => 'editDateMandateRemesa']);

      //filtrar por cliente
      $routes->get('FiltrarClientes/(:any)', 'CustomersController::searchforNameCustomer/$1', ['as' => 'searchforNameCustomer']);
});


//Persona de Contacto
$routes->group('/PersonaContacto', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/PersonaContacto', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    $routes->get('/', 'PersonContactController::index', ['as' => 'showFormPersonContact']);

    //Formulario crea paquete
    $routes->post('crear', 'PersonContactController::create', ['as' => 'addPersonContact']);
    $routes->get('listar', 'PersonContactController::result', ['as' => 'listPersonContact']);
    $routes->get('ver/(:num)', 'PersonContactController::seeDetailPersonContac/$1', ['as' => 'seeDetailPersonContac']);

    $routes->get('editar/(:any)', 'PersonContactController::edit/$1', ['as' => 'editPersonContact']); //Con parametro de envio

    $routes->post('editarPc/(:any)', 'PersonContactController::editSave/$1', ['as' => 'editPersonContactSave']); //Listado luego de modificar

    $routes->get('Preeliminar/(:any)', 'PersonContactController::preDelete/$1', ['as' => 'PredeletePersonaContacto']); //Listado luego de eliminar
    $routes->get('eliminar/(:any)', 'PersonContactController::deletePersonaContacto/$1', ['as' => 'deletePersonaContacto']); //Listado luego de eliminar
});


//Pedidos
   $routes->group('/Orders', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
  //  $routes->group('/Orders', ['namespace' => 'App\Controllers\Front'], function ($routes) {

        $routes->get('/', 'OrdersController::index', ['as' => 'showFormOrders']);
        $routes->post('crear', 'OrdersController::create', ['as' => 'addOrders']);
        $routes->get('listar', 'OrdersController::result', ['as' => 'listOrders']);
        $routes->get('ver/(:any)', 'OrdersController::seeDetailOrder/$1', ['as' => 'seeDetailOrder']);

        $routes->get('editar/(:any)', 'OrdersController::edit/$1', ['as' => 'editOrders']);
        $routes->post('editarGuardarOrden', 'OrdersController::editSaveOrders', ['as' => 'editSaveOrders']);

        $routes->get('asignar/(:any)', 'OrdersController::asignDriverVehicle/$1', ['as' => 'asignDriverVehicle']);
        $routes->post('asignarguardar', 'OrdersController::asignDriverVehicleSave', ['as' => 'asignDriverVehicleSave']);

        $routes->get('BuscarPorClienteDireccion/(:any)', 'OrdersController::getIdWorkLocationOrders/$1', ['as' => 'getIdWorkLocationOrders']);

        $routes->get('BuscarPorClienteDireccionEditar/(:any)', 'OrdersController::getIdWorkLocationOrdersEdit/$1', ['as' => 'getIdWorkLocationOrdersEdit']);


        $routes->get('BuscarPorClientePedidos/(:any)', 'OrdersController::getIdWorkLocationCustomersOrders/$1', ['as' => 'getIdWorkLocationCustomersOrders']);
        //Obteneos el id cliente en la pagina crear albaran
        $routes->get('getIdCustomersAlbaran/(:any)', 'OrdersController::getIdCustomersOrders/$1', ['as' => 'getIdCustomersOrders']);


        $routes->post('eliminarOrden/(:any)', 'OrdersController::deleteOrders/$1', ['as' => 'deleteOrders']); //Listado luego de eliminar

         //filtrar por cliente
       $routes->get('FiltrarClientesPedidos/(:any)', 'OrdersController::searchforCustomerOrders/$1', ['as' => 'searchforCustomerOrders']);

       $routes->get('FiltrarEstadosPedidosPen', 'OrdersController::searchforStateOrdersPen', ['as' => 'searchforStateOrdersPen']);
       $routes->get('FiltrarEstadosPedidosAsi', 'OrdersController::searchforStateOrdersAsi', ['as' => 'searchforStateOrdersAsi']);
       $routes->get('FiltrarEstadosPedidosFac', 'OrdersController::searchforStateOrdersFac', ['as' => 'searchforStateOrdersFac']);


       $routes->post('FiltrarFechasPedidos', 'OrdersController::searchforDateOrders', ['as' => 'searchforDateOrders']);

       $routes->post('FiltrarFechasPedidosPlanificacion', 'OrdersController::searchforDateOrdersPlanned', ['as' => 'searchforDateOrdersPlanned']);

    });



//Albaranes
$routes->group('/Albaranes', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/Albaranes', ['namespace' => 'App\Controllers\Front'], function ($routes) {

    $routes->get('/', 'AlbaranesController::index/$1', ['as' => 'showFormAlbaranes']);

    $routes->get('alta(:num)', 'AlbaranesController::addAlb/$1', ['as' => 'addAlb']);

    $routes->post('crear/(:any)', 'AlbaranesController::create/$1', ['as' => 'addAlbaranes']);

    $routes->get('listar', 'AlbaranesController::result', ['as' => 'listAlbaranes']);
    $routes->get('ver/(:num)', 'AlbaranesController::seeDetailAlbaran/$1', ['as' => 'seeDetailAlbaran']);

    $routes->get('editar/(:any)', 'AlbaranesController::editAlbaran/$1', ['as' => 'editAlbaran']);
    $routes->post('editarGuardarAlbaran', 'AlbaranesController::editSaveAlbaran', ['as' => 'editSaveAlbaran']);

    //Obteneos el id cliente en la pagina crear albaran
    $routes->get('getIdCustomersAlbaran/(:any)', 'AlbaranesController::getIdCustomersAlbaran/$1', ['as' => 'getIdCustomersAlbaran']);

    $routes->get('Preeliminar/(:any)', 'AlbaranesController::preDelete/$1', ['as' => 'PredeleteAlbaran']); //Listado luego de eliminar
    $routes->post('eliminaralbaran/(:any)', 'AlbaranesController::deleteAlbaran/$1', ['as' => 'deleteAlbaran']); //Listado luego de eliminar


    //filtrar por
    $routes->get('FiltrarClientesAlbaranes/(:any)', 'AlbaranesController::searchforCustomerAlbaranes/$1', ['as' => 'searchforCustomerAlbaranes']);
    $routes->get('FiltrarAlbaranesConductores/(:any)', 'AlbaranesController::searchforDriverAlbaranes/$1', ['as' => 'searchforDriverAlbaranes']);

    $routes->get('FiltrarAlbaranesEstadosPedidosPen', 'AlbaranesController::searchforStateAlbaranesPen', ['as' => 'searchforStateAlbaranesPen']);
    $routes->get('FiltrarAlbaranesEstadosPedidosAsi', 'AlbaranesController::searchforStateAlbaranesRea', ['as' => 'searchforStateAlbaranesRea']);
    $routes->get('FiltrarAlbaranesEstadosPedidosFac', 'AlbaranesController::searchforStateAlbaranesFac', ['as' => 'searchforStateAlbaranesFac']);


    $routes->post('FiltrarFechasAlbaranesAlta', 'AlbaranesController::searchforDateAlbaranes', ['as' => 'searchforDateAlbaranes']);

});


//WorkLocations
$routes->group('/DireccionesDeObras', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/DireccionesDeObras', ['namespace' => 'App\Controllers\Front'], function ($routes) {

    $routes->get('/', 'WorkLocationsController::index', ['as' => 'showFormWorkLocations']);

    //Formulario crea paquete
    $routes->post('crear', 'WorkLocationsController::create', ['as' => 'addWorkLocations']);

    $routes->get('listar', 'WorkLocationsController::result', ['as' => 'listWorkLocations']);

    $routes->get('ver/(:num)', 'WorkLocationsController::seeDetailWorkLocations/$1', ['as' => 'seeDetailWorkLocations']);

    $routes->get('editar/(:any)', 'WorkLocationsController::edit/$1', ['as' => 'editWorkLocations']); //Con parametro de envio

    $routes->post('editarGuardar/(:any)', 'WorkLocationsController::editSave/$1', ['as' => 'editWorkLocationsSave']); //Con parametro de envio


    $routes->get('asignarContenedor/(:any)', 'WorkLocationsController::asignContainer/$1', ['as' => 'asignContainer']); //Con parametro de envio

    $routes->post('asignarContenedorGuardar/(:any)', 'WorkLocationsController::asignContainerUpDate/$1', ['as' => 'asignContainerUpDate']);

    $routes->get('asignarContenedorActualizado/(:any)', 'WorkLocationsController::asignContainerReturn/$1', ['as' => 'asignContainerReturn']);

    $routes->post('LiberarContenedor/(:any)', 'WorkLocationsController::setFreeContainer/$1', ['as' => 'setFreeContainer']); //Con parametro de envio


    $routes->get('editarGuardar/(:any)', 'WorkLocationsController::editSave/$1', ['as' => 'editSaveWorkLocations']); //Listado luego de modificar

    $routes->get('Preeliminar/(:any)', 'WorkLocationsController::preDelete/$1', ['as' => 'PredeleteWorkLocations']); //Listado luego de eliminar

    $routes->post('eliminarDireccionDeObra/(:any)', 'WorkLocationsController::deleteWorkLocation/$1', ['as' => 'deleteWorkLocation']); //Listado luego de eliminar

    $routes->get('FiltrarDireccion/(:any)', 'WorkLocationsController::searchforWorkLocation/$1', ['as' => 'searchforWorkLocation']);
    $routes->get('FiltrarPorClienteDireccionObra/(:any)', 'WorkLocationsController::searchforCustomersWorkLocation/$1', ['as' => 'searchforCustomersWorkLocation']);
});


//Servicios
$routes->group('/Servicios', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/Servicios', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    $routes->get('/', 'Services_Controller::index', ['as' => 'showFormService']);

    //Formulario crea paquete
    $routes->post('crear', 'Services_Controller::create', ['as' => 'addService']);
    $routes->get('listar', 'Services_Controller::result', ['as' => 'listService']);
    $routes->get('ver/(:num)', 'Services_Controller::seeDetailService/$1', ['as' => 'seeDetailService']);


    $routes->get('editar/(:any)', 'Services_Controller::edit/$1', ['as' => 'editService']); //Con parametro de envio

    $routes->post('editarAlbaran/(:any)', 'Services_Controller::editSave/$1', ['as' => 'editSaveService']); //Listado luego de modificar

    $routes->get('Preeliminar/(:any)', 'Services_Controller::preDelete/$1', ['as' => 'PredeleteService']); //Listado luego de eliminar
    $routes->post('eliminar/(:any)', 'Services_Controller::deleteService/$1', ['as' => 'deleteService']); //Listado luego de eliminar
});


//Estados Actuales
$routes->group('/EstadosActuales', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//    $routes->group('/EstadosActuales', ['namespace' => 'App\Controllers\Front'], function ($routes) {

        $routes->get('listar', 'ActualStateController::result', ['as' => 'listAS']);

        $routes->get('FiltrarEstadosActualesPorCliente/(:any)', 'ActualStateController::searchforCustomerActualState/$1', ['as' => 'searchforCustomerActualState']);
        $routes->get('FiltrarEstadosActualesPorContenedor/(:any)', 'ActualStateController::searchforContainerActualState/$1', ['as' => 'searchforContainerActualState']);

  });



//Facturas
  $routes->group('/Facturas', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/Facturas', ['namespace' => 'App\Controllers\Front'], function ($routes) {

    $routes->get('/', 'BillsController::index', ['as' => 'showFormBills']);

    $routes->get('crearSuplementos', 'BillsController::createSupplements', ['as' => 'createSupplements']);

    $routes->post('PreFactura', 'BillsController::pre_view_create', ['as' => 'pre_view_create']);
    $routes->post('PreFacturaDesdeListaAlbaranes', 'BillsController::pre_view_bills_albaran', ['as' => 'pre_view_bills_albaran_list']);

    //Pre visualizar factura de SOLO SUPLEMENTOS
    $routes->post('PreFacturaSuplementos', 'BillsController::pre_view_create_supplements', ['as' => 'pre_view_create_supplements']);
    $routes->get('Proforma', 'BillsController::proForma', ['as' => 'proForma']);
    //Formulario crea paquete

    $routes->post('FacturaCreada', 'BillsController::billsCreated', ['as' => 'billsCreated']);
    //Formulario crea paquete
    $routes->post('crear', 'BillsController::create', ['as' => 'addBills']);
    $routes->post('crearGuardar', 'BillsController::createSaveBills', ['as' => 'createSaveBills']);

    $routes->post('crearFacturadesdeListado', 'BillsController::createBillsAutomatic', ['as' => 'createBillsAuto']);

    $routes->post('crearGuardarSup', 'BillsController::createSaveBillsSupplements', ['as' => 'createSaveBillsSupplements']);
   //Obteneos el id cliente en la pagina crear albaran
    $routes->get('Crea_Facturas/(:any)', 'BillsController::getIdWorkLocationBills/$1', ['as' => 'getIdWorkLocationBills']);

    //Obteneos el id cliente en la pagina crear SUPLEMENTOS
    $routes->get('Crea_Facturas_Suplementos/(:any)', 'BillsController::getIdWorkLocationBillsSupp/$1', ['as' => 'getIdWorkLocationBillsSupp']);

    //filtrar por cliente en create bills
    $routes->get('BuscarPorCliente/(:any)', 'BillsController::getIdWorkLocationCustomersBills/$1', ['as' => 'getIdWorkLocationCustomersBills']);

      //filtrar por cliente en create bills SUPLEMENTOS
    $routes->get('BuscarPorClientesSuplementos/(:any)', 'BillsController::getIdWorkLocationCustomersBillsSuppl/$1', ['as' => 'getIdWorkLocationCustomersBillsSuppl']);

    $routes->get('listar', 'BillsController::result', ['as' => 'listBills']);


    //Remesas
    $routes->get('remesas', 'BillsController::remesas', ['as' => 'remesas']);
    $routes->post('crear_remesas', 'BillsController::createRemesas', ['as' => 'createRemesas']);
    $routes->get('mensajes_remesas', 'BillsController::redirectToMessagesRemesas', ['as' => 'redirectToMessagesRemesas']);
    $routes->get('listadoRemesas', 'BillsController::listRemesas', ['as' => 'listRemesas']);
    $routes->post('descargarRemesa', 'BillsController::downloadFileRemesas', ['as' => 'downloadFileRemesas']);



    $routes->get('listarPost', 'BillsController::redirectToResult', ['as' => 'redirectToResult']);



    $routes->get('ver/(:any)', 'BillsController::seeDetailBills/$1', ['as' => 'seeDetailBills']);

    //Ver la factuara desde listado
    $routes->get('verGuardadaDetalle/(:any)', 'BillsController::seeDetailBillsS/$1', ['as' => 'seeDetailBillsSave']);

    $routes->get('verGuardadaDetalleListaSuplemento/(:any)', 'BillsController::seeDetailBillsSaveSup/$1', ['as' => 'seeDetailBillsSaveSupplements']);


    $routes->get('editarFacturas/(:any)', 'BillsController::editBills/$1', ['as' => 'editBills']); //Con parametro de envio
    $routes->get('editarFacturasSuplementos/(:any)', 'BillsController::editBillsSupl/$1', ['as' => 'editBillsSupplements']); //Con parametro de envio

    $routes->get('editarFacturasAlbaran/(:any)', 'BillsController::editBillsAlbaran/$1', ['as' => 'editBillsAlbaran']); //Con parametro de envio

    $routes->post('editarGuardarFactura', 'BillsController::editSaveBills', ['as' => 'editSaveBills']); //Listado luego de modificar
    $routes->post('editarGuardarFacturaSuplemento', 'BillsController::editSaveBillsSupplements', ['as' => 'editSaveBillsSupl']); //Listado luego de modificar


    $routes->get('eliminar/(:any)', 'BillsController::deleteBills/$1', ['as' => 'deleteBills']); //Listado luego de eliminar
    $routes->get('AbonarRectificar/(:any)', 'BillsController::rectifyBills/$1', ['as' => 'rectifyBills']); //Listado luego de eliminar
    $routes->get('leerAccess', 'BillsController::leerAccess', ['as' => 'leerAccess']); //Listado luego de eliminar



    $routes->post('generarXml', 'BillsController::exportToXml', ['as' => 'exportarToXml']);

});

//EXCEL y XML
$routes->group('/Excel', ['namespace' => 'App\Controllers', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
    //$routes->group('/Conductores', ['namespace' => 'App\Controllers\Front'], function ($routes) {
        $routes->get('exportToExcel', 'generateExcelController::generateExcel', ['as' => 'generateExcel']);
        $routes->post('exportToExcel', 'generateExcelController::generateExcelForm', ['as' => 'generateExcelForm']);



    });



//Conductores
$routes->group('/Conductores', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/Conductores', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    $routes->get('/', 'DDriversController::index', ['as' => 'showFormDrivers']);

    $routes->post('crear', 'DDriversController::create', ['as' => 'addDrivers']);

    $routes->get('listar', 'DDriversController::result', ['as' => 'listDrivers']);
    $routes->get('ver/(:num)', 'DDriversController::seeDetailDrivers/$1', ['as' => 'seeDetailDrivers']);

    $routes->get('editarConductor/(:any)', 'DDriversController::editDrivers/$1', ['as' => 'editDrivers']); //Con parametro de envio

    $routes->post('editarGuardar/(:any)', 'DDriversController::editSaveDrivers/$1', ['as' => 'editSaveDrivers']); //Listado luego de modificar

    $routes->post('eliminarConductor/(:any)', 'DDriversController::deleteDriver/$1', ['as' => 'deleteDriver']); //Listado luego de eliminar


    $routes->get('FiltrarConductores/(:any)', 'DDriversController::searchforDriver/$1', ['as' => 'searchforDriver']);
});

//Vehiculos
$routes->group('/Vehiculos', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/Vehiculos', ['namespace' => 'App\Controllers\Front'], function ($routes) {

    $routes->get('/', 'VehiclesController::index', ['as' => 'showFormVehicle']);
    $routes->post('crear', 'VehiclesController::create', ['as' => 'addVehicle']);
    $routes->get('listar', 'VehiclesController::result', ['as' => 'listVehicle']);
    $routes->get('verVehiculo/(:num)', 'VehiclesController::seeDetailVehicle/$1', ['as' => 'seeDetailVehicle']);
    $routes->get('editarVehiculo/(:any)', 'VehiclesController::editVehicle/$1', ['as' => 'editVehicle']); //Con parametro de envio
    $routes->post('editarGuardarVehiculos/(:any)', 'VehiclesController::editSaveVehicle/$1', ['as' => 'editSaveVehicle']); //Listado luego de modificar

    $routes->post('eliminar/(:any)', 'VehiclesController::deleteV/$1', ['as' => 'deleteVehicle']); //Listado luego de eliminar

    $routes->get('FiltrarVehiculo/(:any)', 'VehiclesController::searchforVehicle/$1', ['as' => 'searchforVehicle']);

});

//Estados
$routes->group('/Estados', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/Estados', ['namespace' => 'App\Controllers\Front'], function ($routes) {

     $routes->get('listar', 'StateController::listar', ['as' => 'listState']);
     $routes->get('ver/(:num)', 'StateController::seeDetailState/$1', ['as' => 'seeDetailState']);
});


//Formas de Pagos
$routes->group('/FormasPago', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/FormasPago', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    $routes->get('/', 'FormasPagoController::index', ['as' => 'showFormFormasPago']);

    $routes->post('crear', 'FormasPagoController::crear', ['as' => 'crearFormasPago']);
    $routes->get('listar', 'FormasPagoController::listar', ['as' => 'listarFormasPago']);
    $routes->get('ver/(:num)', 'FormasPagoController::verDetalleFormasPago/$1', ['as' => 'verDetalleFormasPago']);

    $routes->get('editar/(:any)', 'FormasPagoController::editar/$1', ['as' => 'editarFormasPago']); //Con parametro de envio

    $routes->get('editarEstados/(:any)', 'FormasPagoController::editarGuardar/$1', ['as' => 'EditarGuardarFormasPago']); //Listado luego de modificar

    $routes->get('Preeliminar/(:any)', 'FormasPagoController::preDelete/$1', ['as' => 'PredeleteFormasPago']); //Listado luego de eliminar
    $routes->get('eliminar/(:any)', 'FormasPagoController::deleteFormasPago/$1', ['as' => 'deleteFormasPago']); //Listado luego de eliminar
});

//Suplementos
$routes->group('/Sumplementos', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
   // $routes->group('/Sumplementos', ['namespace' => 'App\Controllers\Front'], function ($routes) {
        $routes->get('/', 'SupplementsController::index', ['as' => 'showFormSuplements']);

        $routes->post('crearSuplemento', 'SupplementsController::create', ['as' => 'addSuplements']);
        $routes->get('listarSuplemento', 'SupplementsController::result', ['as' => 'listSuplements']);

        $routes->get('editarSuplemento/(:any)', 'SupplementsController::editSuplements/$1', ['as' => 'editSuplements']); //Con parametro de envio

        $routes->post('editarGuardarSuplemento/(:any)', 'SupplementsController::editSuplementsSave/$1', ['as' => 'editSuplementsSave']); //Listado luego de modificar

        $routes->post('eliminar/(:any)', 'SupplementsController::deleteSupplements/$1', ['as' => 'deleteSupplements']); //Listado luego de eliminar



    });

//Tarifas
$routes->group('/Tarifas', ['namespace' => 'App\Controllers\Front', 'filter' => 'auth:Admin', 'filter' => 'auth:User'], function ($routes) {
//$routes->group('/Tarifas', ['namespace' => 'App\Controllers\Front'], function ($routes) {
    $routes->get('/', 'TarifasController::index', ['as' => 'showFormTarifas']);

    $routes->post('crear', 'TarifasController::crear', ['as' => 'crearTarifas']);
    $routes->get('listar', 'TarifasController::listar', ['as' => 'listarTarifas']);
    $routes->get('ver/(:num)', 'TarifasController::verDetalleTarifas/$1', ['as' => 'verDetalleTarifas']);

    $routes->get('editar/(:any)', 'TarifasController::editar/$1', ['as' => 'editarTarifas']); //Con parametro de envio

    $routes->get('editarTarifas/(:any)', 'TarifasController::editarGuardar/$1', ['as' => 'EditarGuardarTarifas']); //Listado luego de modificar

    $routes->get('Preeliminar/(:any)', 'TarifasController::preDelete/$1', ['as' => 'PredeleteTarifas']); //Listado luego de eliminar
    $routes->get('eliminar/(:any)', 'TarifasController::deleteTarifas/$1', ['as' => 'deleteTarifas']); //Listado luego de eliminar
});
/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
