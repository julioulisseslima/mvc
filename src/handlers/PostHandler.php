<?php
namespace src\handlers;

use \src\models\Post;
use \src\models\User;
use \src\models\UserRelation;

class PostHandler {

    public static function addPost($idUser, $type, $body){
        $body = trim($body);

        if(!empty($idUser) && !empty($body)){

            Post::insert([
                'id_user' => $idUser,
                'type' => $type,
                'created_at' => date('Y-m-d H:i:s'),
                'body' => $body
            ])->execute();
        }
    }

    public static function getHomeFeed($idUser)
    {
        //1. pegar lista de usuários que eu sigo.
        $userList = UserRelation::select()->where('user_from', $idUser)->execute();
        $users = [];
        foreach($userList as $userItem) {
            $user[] = $userItem['user_to'];
        }
        $users[] = $idUser;

        print_r($users);
        //2. pegar os posts dessa galera ordenando pela data.
        //3. trasnformar o resultado em objetos do models
        //4. preenhcer as informações adicionais no post
        //5. retornar o resultado
    }

}
