<?php
namespace Source\Controllers;

use Source\Core\Controller;
use Source\Domain\Model\User;

/**
 * Admin Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Admin extends Controller
{
    /**
     * Admin constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function login()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->getAllPostData();
            
            $user = new User();
            $userDataOrErrorMessage = $user
                ->login($requestPost["userEmail"], $requestPost["userPassword"]);

            if (is_string($userDataOrErrorMessage) && json_decode($userDataOrErrorMessage) !== null) {
                echo $userDataOrErrorMessage;
                die;
            }
            
            echo json_encode($requestPost);
            die;
        }

        echo $this->view->render("admin/login", []);
    }

    public function index()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        echo $this->view->render("admin/home", []);
    }

}
