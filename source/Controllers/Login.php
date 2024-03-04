<?php
namespace Source\Controllers;

use Source\Core\Controller;
use Source\Domain\Model\User;

/**
 * Login C:\php-projects\sistema-contabil\source\Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Controllers
 */
class Login extends Controller
{
    /**
     * Login constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function logout(array $data)
    {
        if (empty($data['request'])) {
            die;
        }

        if (is_string($data['request']) && json_decode($data['request']) != null) {
            $data = json_decode($data['request'], true);

            if (!empty($data['logout'])) {
                session()->unset('user');
                echo json_encode(["logout_success" => true]);
            }
        }

    }

    public function login()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()
            ->setRequiredFields(["csrfToken", "userData", "userPassword"])->getAllPostData();
            
            $user = new User();
            $userDataOrErrorMessage = $user
                ->login($requestPost["userData"], $requestPost["userData"], $requestPost["userPassword"]);

            if (is_string($userDataOrErrorMessage) && json_decode($userDataOrErrorMessage) !== null) {
                echo $userDataOrErrorMessage;
                die;
            }

            if (isset($requestPost["remember"]) && $requestPost["remember"] == "on") {
                setcookie("user_email", $requestPost["userData"], time() + 3600);
                setcookie("user_password", $requestPost["userPassword"], time() + 3600);
            }

            session()->set("user", [
                "user_full_name" => $userDataOrErrorMessage->user_full_name,
                "user_nick_name" => $userDataOrErrorMessage->user_nick_name,
                "user_email" => $userDataOrErrorMessage->user_email
            ]);
            
            echo json_encode(["login_success" => true, "url" => url("/admin")]);
            die;
        }

        echo $this->view->render("admin/login", []);
    }
}
