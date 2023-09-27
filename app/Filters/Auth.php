<?php

namespace App\Filters;

use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class Auth implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (!session()->isLogged){
            return redirect()->route('login')->with('msg',[
                'type' => 'alert-danger',
                'body' => ['Para acceder a este lugar primero debe logerase.']
            ]);
        }

        $usersModel = model('UsersModel');
        if(!$user = $usersModel->getUserBy('id',session()->idUser)){
            session()->destroy();
            return redirect()->route('login')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['El usuario actualmente no esta disponible']
            ]);
        }
        if (!in_array($user->getRole()->name_group, $arguments) ) {
            return redirect()->route('notPermission')->with('msg',[
                'type' => 'alert-danger',
                'body' => ['Acceso restringido']
            ]);
           // throw PageNotFoundException::forPageNotFound();
        }

    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
