<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use Config\Services;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;
    protected $session;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var array
     */
    protected $helpers = ['permission', 'validation','errors'];
    protected $twig;

    /**
     * Constructor.
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        $this->session = Services::session();

        $appPaths = new \Config\Paths();
        $appViewPaths = $appPaths->viewDirectory;

        $loader = new \Twig\Loader\FilesystemLoader($appViewPaths);

        $this->twig = new \Twig\Environment($loader, [
            'cache' => WRITEPATH . '/cache/twig',
            'auto_reload' => true,
            'debug' => true
        ]);
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());


        $function = new \Twig\TwigFunction('baseUrl', function (string $url) {
            return base_url(route_to($url));
        });
        $this->twig->addFunction($function);


        //Funcion para redirigir enlaces formularios
        //Pasa parametros de string y array -> ...$params --> PHP 8
        $function = new \Twig\TwigFunction('routeTo', function (string $url, ...$params) {
            if ($params) {
                return route_to($url, ...$params);
            }
            return route_to($url);
        });
        $this->twig->addFunction($function);


       //Funcion para redirigir enlaces formularios
        //Pasa parametros de string y array -> ...$params --> PHP 8
        $function = new \Twig\TwigFunction('routeToParams', function (string $url, ...$params) {
              return route_to($url, ...$params);

        });
        $this->twig->addFunction($function);



        $function = new \Twig\TwigFunction('getPath', function () {
            return service('request')->uri->getPath();
        });
        $this->twig->addFunction($function);


        //Mensajes
        $function = new \Twig\TwigFunction('msg', function (string $text = null) {
            return session($text);
        });
        $this->twig->addFunction($function);


        $function = new \Twig\TwigFunction('session', function (string $text = null) {
            return $this->session->get($text);
        });
        $this->twig->addFunction($function);

        $function = new \Twig\TwigFunction('checkPerms', function (array $perms) {
            return checkPermission($perms, (array)session()->perms);
        });
        $this->twig->addFunction($function);



        $function = new \Twig\TwigFunction('old', function (string $text) {
            return old($text);
        });
        $this->twig->addFunction($function);




    }
}
