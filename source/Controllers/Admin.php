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
            $requestPost = $this->getRequests()->getAllPostData();
            
            $user = new User();
            $userDataOrErrorMessage = $user
                ->login($requestPost["userEmail"], $requestPost["userPassword"]);

            if (is_string($userDataOrErrorMessage) && json_decode($userDataOrErrorMessage) !== null) {
                echo $userDataOrErrorMessage;
                die;
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

    public function index()
    {
        if (empty(session()->user)) {
            redirect("/admin/login");
        }

        $user = new User();
        $userData = $user->findUserByEmail(session()->user->user_email);

        if (is_string($userData) && json_decode($userData) != null) {
            throw new \Exception($userData);
        }

        $userFullNameData = explode(" ", $userData->user_full_name);
        $userFullName = [];
        $keys = [0, 1];

        foreach ($userFullNameData as $key => $value) {
            if (in_array($key, $keys)) {
                array_push($userFullName, $value);
            }
        }

        $userFullName = implode(" ", $userFullName);
        $userFullName = ucwords($userFullName);
        
        echo $this->view->render("admin/home", [
            "userFullName" => $userFullName
        ]);
    }

}
