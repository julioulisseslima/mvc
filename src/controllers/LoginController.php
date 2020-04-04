<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\models\User;

class LoginController extends Controller {

    public function signin() {
        $flash = '';
        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('signin', [
            'flash' => $flash
        ]);
    }

    public function signinAction() {
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');

        if($email && $password) {
            $token = UserHandler::verifyLogin($email, $password);
            if($token) {
                $_SESSION['token'] = $token;
                $_SESSION['email'] = $email;
                $this->redirect('/');
            } else {
                $_SESSION['flash'] = 'E-mail e/ou senha não conferem';
                $this->redirect('/login');
            }

        } else {
            $_SESSION['flash'] = 'Digite os campos de e-mail e/ou senha.';
            $this->redirect('/login');
        }

    }

    public function signup() {
        $flash = '';
        if(!empty($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $_SESSION['flash'] = '';
        }
        $this->render('signup', [
            'flash' => $flash
        ]);
    }

    public function signupAction() {
        $name = filter_input(INPUT_POST, 'name');
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = filter_input(INPUT_POST, 'password');
        $birthdate = filter_input(INPUT_POST, 'birthdate');

        if($name && $email && $password && $birthdate){

            //Transformar a data padrão dd/mm/yyyy em yyyy-mm-dd
            $birthdate = explode('/',$birthdate);
            if(count($birthdate)!=3) {
                $_SESSION['flash'] = 'Data de nascimento inválida!';
                $this->redirect('/cadastro');
            }
                $birthdate = $birthdate[2].'-'.$birthdate[1].'-'.$birthdate[0];

                //Verificar se é uma data válida
                if(strtotime($birthdate === false)){
                    $_SESSION['flash'] = 'Data de nascimento inválida!';
                    $this->redirect('/cadastro');
                }

                //Verificar se existe algum usuário cadastrado com mesmo e-mail
                if(UserHandler::emailExists($email) === false){
                    //Se o e-mail não existir, insere o usuário
                    UserHandler::addUser($name, $email, $password, $birthdate);
                    $_SESSION['token'] = $token;
                    $_SESSION['name'] = $name;
                    $this->redirect('/');
                } else {
                    //Se o e-mail já existir, executa essa parte
                    $_SESSION['flash'] = 'E-mail já cadastrado!';
                    $this->redirect('/cadastro');
                }

        }else{
            //Se faltar alguma informação na primeira chamada
            $this->redirect('/cadastro');
        }
    }

}
