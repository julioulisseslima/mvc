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
        $userList = UserRelation::select()
        ->where('user_from', $idUser)
        ->execute();
        $users = [];
        foreach($userList as $userItem) {
            $users[] = $userItem['user_to'];
        }
        $users[] = $idUser;

        
        //2. pegar os posts dessa galera ordenando pela data.
        $postsList = Post::select()
        ->where('id_user', 'in', $users)
        ->orderBy('created_at', 'desc')
        ->get();

        
        //3. trasnformar o resultado em objetos do models
        $posts = [];
        foreach($postsList as $postItem) {
            $newPost = new Post();
            $newPost->id = $postItem['id'];
            $newPost->type = $postItem['type'];
            $newPost->created_at = $postItem['created_at'];
            $newPost->body = $postItem['body'];
            $newPost->mine = false;

            if($postItem['id_user'] == $idUser) {
                $newPost->mine = true;
            }
            
            //4. preenhcer as informações adicionais no post
            $newUser = User::select()
                    ->where('id', $postItem['id_user'])
                    ->one();
            $newPost->user = new User();
            $newPost->user->id = $newUser['id'];
            $newPost->user->name = $newUser['name'];
            $newPost->user->avatar = $newUser['avatar'];
            
            //TODO: 4.1 preenhcer informaç~´oes de LIKE
            $newPost->likeCount = 0;
            $newPost->liked = false;


            //TOOD: 4.2 preencher informações de COMMENTS
            $newPost->comments = [];
            $posts[] = $newPost;
        }
        

        
        
        //5. retornar o resultado
        return $posts;
    }

}
