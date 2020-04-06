<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use src\handlers\PostHandler;

class ProfileController extends Controller {

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHandler::checkLogin();

        if(UserHandler::checkLogin() === false) {
            $this->redirect('/login');
        }

    }

    public function index($atts=[]) {
        $page = filter_input(INPUT_GET, 'page');

        //detectando o usuário acessado
        $id = $this->loggedUser->id;
        if(!empty($atts['id'])) {
            $id = $atts['id'];
        }

        //Pegando informações do usuário
        $user = UserHandler::getUser($id, true);
        if(!$user) {
            $this->redirect('/');
        }
        
        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');

        $user->ageYears = $dateFrom->diff($dateTo)->y;

        //pegando o feed do usuário
        $feed = PostHandler::getUserFeed($id, $page, $this->loggedUser->id);

        //verificar se eu sigo o usuario
        $isFollowing = false;
        if($user->id != $this->loggedUser->id) {
            $isFollowing = UserHandler::isFollowing($this->loggedUser->id, $user->id);
        }

        $this->render('profile', [
            'loggedUser' => $this->loggedUser,
            'user' => $user,
            'feed' => $feed,
            'isFollowing' => $isFollowing
        ]);
        }



}
