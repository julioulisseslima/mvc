<?php
namespace src\handlers;

use \src\models\User;

class LoginHandler {

    public static function checkLogin() {
        if(!empty($_SESSION['token'])) {
            $token = $_SESSION['token'];

            $data = User::select()->where('token', $token)->one();
            if(count($data)>0) {
                $loggedUser = new User();
                $loggedUser->id = $data['id'];
                $loggedUser->name = $data['name'];
                $loggedUser->avatar = $data['avatar'];
                $loggedUser->city = $data['city'];

                return $loggedUser;

            }
        }

        return false;
    }

    public static function verifyLogin($email, $password) {
        $user = User::select()
            ->where('email', $email)
        ->one();

        if($user) {
            if(password_verify($password, $user['password'])) {
                $token = md5(time().rand(0,9999).time());

                User::update()
                    ->set('token', $token)
                    ->where('email', $email)
                ->execute();

                return $token;
            }
        }
        return false;
    }

    public function idExists($id) {
        //Verifica se o e-mail já existe
        $user = User::select()
            ->where('id', $id)
            ->one();

        //Achou e-mail? SIM : NÃO
        return $user ? true : false;
    }

    public function emailExists($email) {
        //Verifica se o e-mail já existe
        $user = User::select()
            ->where('email', $email)
            ->one();

        //Achou e-mail? SIM : NÃO
        return $user ? true : false;
    }

    public  function addUser($name, $email, $password, $birthdate) {
        //Gerar hash da senha
        $hash = password_hash($password, PASSWORD_DEFAULT);
        //Gerar um token aleatório
        $token = md5(time().rand(0,9999).time());

        //inserir o novo usuário sistema
        User::insert([
            'email' => $email,
            'password' => $hash,
            'name' => $name,
            'birthday' => $birthdate,
            'avatar' => 'default.jpg',
            'token' => $token
        ])->execute();

        return $token;
    }

}
