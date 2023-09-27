<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Entities\User;

use App\Libraries\Log;

class RegisterController extends BaseController
{

    public function index()
    {
        return $this->twig->render('login/createUser.html.twig');
    }

    /**
     * It creates a new user.
     *
     * @return ```php
     * return redirect()->route('login')->with('msg', [
     *                 'type' => 'alert-success',
     *                 'body' => ['Usuario registrado con éxito!']
     *             ]);
     * ```
     */
    public function addUser()
    {
        if (!$this->validate(valiateCreateUser())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }


        $user = new User($this->request->getPost());
        $userModel = model('UsersModel');
        //el tipo de usuario que se registra
        $configs = config('Configuration');
        $user->id_group = $userModel->withGroup($configs->defaultGroupUsers);
        $user->active = 1;


        $log = new Log('Registro/');
        try {
            $userModel->save($user);
            return redirect()->route('login')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Usuario registrado con éxito!, ya puede Acceder.']
            ]);
        } catch (\Throwable $th) {
            $log->setLine('Error', $th->getMessage());
             return redirect()->route('register')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al crear el usuario']
            ]);
        }

    }


}
