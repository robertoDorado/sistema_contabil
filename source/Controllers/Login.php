<?php

namespace Source\Controllers;

use Source\Core\Controller;
use Source\Domain\Model\Customer;
use Source\Domain\Model\Subscription;
use Source\Domain\Model\Support;
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
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
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
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()
                ->setRequiredFields(["csrfToken", "userData", "userPassword", "userType"])->getAllPostData();

            $validateUserType = ["0", "1"];
            if (!in_array($requestPost["userType"], $validateUserType)) {
                http_response_code(500);
                echo json_encode(["error" => "tipo de usuário inválido"]);
                die;
            }

            $verifyUserType = [
                "0" => new User(),
                "1" => new Support()
            ];

            if (!empty($verifyUserType[$requestPost["userType"]])) {
                $user = $verifyUserType[$requestPost["userType"]];
            }

            if (filter_var($requestPost["userData"], FILTER_VALIDATE_EMAIL)) {
                $user->setEmail($requestPost["userData"]);
            } else {
                $user->setNickName($requestPost["userData"]);
            }

            $userData = $user->login($requestPost["userPassword"]);
            if (empty($userData)) {
                http_response_code(500);
                echo $user->message->json();
                die;
            }

            if (isset($requestPost["remember"]) && $requestPost["remember"] == "on") {
                setcookie("user_email", $requestPost["userData"], time() + 3600);
                setcookie("user_password", $requestPost["userPassword"], time() + 3600);
            }

            $subscription = new Subscription();
            $subscription->customer_id = $userData->id_customer;
            $subscriptionData = $subscription->findSubsCriptionByCustomerId(["status", "period_end"]);

            if (empty($subscriptionData)) {
                $subscriptionStatus = new \stdClass();
                $subscriptionStatus->status = "free";
            }

            $status = empty($subscriptionData)
                ? $subscriptionStatus->status : $subscriptionData->getStatus();
            $periodEnd = empty($subscriptionData->period_end) ? null : $subscriptionData->period_end;

            $customer = new Customer();
            $customer->customer_id = $userData->id_customer;
            $customerData = $customer->findCustomerById();

            session()->set("user", [
                "subscription" => $status,
                "id_customer" => $customerData->id ?? null,
                "user_full_name" => $userData->user_full_name,
                "user_nick_name" => $userData->user_nick_name,
                "user_email" => $userData->user_email,
                "period_end" => $periodEnd,
                "user_type" => $requestPost["userType"]
            ]);

            $verifyRedirectUrl = [
                "0" =>  url("/admin"),
                "1" =>  url("/admin/support/dashboard")
            ];

            $url = $verifyRedirectUrl[$requestPost["userType"]];
            echo json_encode(["login_success" => true, "url" => $url]);
            die;
        }

        echo $this->view->render("admin/login", []);
    }
}
