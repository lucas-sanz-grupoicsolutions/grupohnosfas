<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Entities\User;
use App\Libraries\Log;

use App\Entities\Users;
use App\Models\UsersModel;

class LoginController extends BaseController
{

    protected $userModel;

    public function __construct()
    {

        $this->userModel = model('UsersModel');

    }

    public function index()
    {
        if (!session()->isLogged) {
            return $this->twig->render('Front/index.html.twig');
        }
    }

    /**
     * It validates the form, if it's not valid, it returns the form with the errors, if it is valid, it gets the user from
     * the database, sets the session variables and redirects to the home page
     *
     * @return \CodeIgniter\HTTP\Response A response object
     */
    public function signin(): \CodeIgniter\HTTP\Response
    {

        if (!$this->validate(validateLogin())) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }

        $email = trim($this->request->getVar('mail'));
        $usersModel = model('UsersModel');

        $user = $usersModel->getUserBy('mail', $email);

        session()->set([
            'idUser' => $user->id,
            'name' => $user->name,
            'isLogged' => true,
            'perms' => loadPermissions($user->id)
        ]);
        $this->session = session();

        return redirect()->route('home')->with('msg', [
            'type' => 'alert-success',
            'body' => ['Bienvenido nuevamente ' . $user->name]
        ]);

    }

    public function signout(): \CodeIgniter\HTTP\RedirectResponse
    {
        session()->destroy();
        return redirect()->route('home');
    }



    // public function ForgotPassword()
    // {
    //     $email = $this->input->post('email');
    //     $findemail = $this->usermodel->ForgotPassword($email);

    //     if ($findemail) {
    //         $this->usermodel->sendpassword($findemail);
    //     } else {
    //         $this->session->set_flashdata('msg', ' Email not found!');

    //         return $this->twig->render('login/login.html.twig');
    //     }
    // }


    public function changePassword()
    {



        $password = trim($this->request->getVar('password'));
        $c_password = trim($this->request->getVar('c-password'));



        if($password != $c_password){

                if (!$this->validate(validateLogin())) {
                    return redirect()->back()
                        ->with('errors', $this->validator->getErrors())
                        ->withInput();
                }
        }

        $_SESSION['idUser'];
        $id = session()->get('idUser');

        $user = new User($this->request->getPost());
        $userModel = model('UsersModel');

        //el tipo de usuario que se registra
        $configs = config('Configuration');
        $user->id_group = $userModel->withGroup($configs->defaultGroupUsers);
        $user->active = 1;


        $log = new Log('Registro/');

        try {
            $userModel->save($user);

            return redirect()->route('PasswordReset')->with('msg', [
                'type' => 'alert-success',
                'body' => ['Contraseña cambiada con exito!']
            ]);
        } catch (\Throwable $th) {
            $log->setLine('Error', $th->getMessage());
             return redirect()->route('PasswordReset')->with('msg', [
                'type' => 'alert-danger',
                'body' => ['Error al cambiar la contraseña']
            ]);
        }

    }


    /**
     * Muestra la pagina de cambiar contraseña
     */
    public function PasswordReset()
    {

        //obtenemos el user con session y el mail del user
        $_SESSION['idUser'];
        $id = session()->get('idUser');

        $id = (int)$id;

        $users = $this->userModel->where('id', $id)->find();



        // $query = $this->$db->query('SELECT mail FROM users WHERE id = ' . $id );

        foreach ($users as $row) {

            $mail = $row->mail;
        }


            return $this->twig->render('login/passwordReset.html.twig',['email'=> $mail,'id'=> $id]);

    }


}
