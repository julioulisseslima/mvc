<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class HomeController extends Controller {

    private $loggedUser;

    public function __construct()
    {
        $this->loggedUser = UserHandler::checkLogin();

        if(UserHandler::checkLogin() === false) {
            $this->redirect('/login');
        }

    }

    public function index() {
        $page = filter_input(INPUT_GET, 'page');

        echo "PAGE".$page;

        $feed = PostHandler::getHomeFeed(
            $this->loggedUser->id,
            $page
        );

        $this->render('home', [
            'loggedUser' => $this->loggedUser,
            'feed' => $feed
        ]);
    }

}
