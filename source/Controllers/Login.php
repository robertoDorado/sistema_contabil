<?php

namespace Source\Controllers;

use Source\Core\Controller;
use Source\Domain\Model\Customer;
use Source\Domain\Model\Subscription;
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
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()
                ->setRequiredFields(["csrfToken", "userData", "userPassword"])->getAllPostData();

            $user = new User();
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

            if (empty($customerData)) {
                echo $customer->message->json();
                die;
            }

            session()->set("user", [
                "subscription" => $status,
                "id_customer" => $customerData->id,
                "user_full_name" => $userData->user_full_name,
                "user_nick_name" => $userData->user_nick_name,
                "user_email" => $userData->user_email,
                "period_end" => $periodEnd
            ]);

            echo json_encode(["login_success" => true, "url" => url("/admin")]);
            die;
        }

        echo $this->view->render("admin/login", []);
    }
}
