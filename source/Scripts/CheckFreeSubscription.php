<?php

use Source\Core\Model;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

$user = new User();
$userData = $user->findAllUserJoinCustomerJoinSubscription([
    "user_columns" => ["id", "deleted", "id_customer", "user_email"],
    "customer_columns" => ["created_at"],
    "subscription_columns" => ["status"]
]);

$filterEmail = ["robertodorado7@gmail.com"];
$dateTimeNow = new DateTime();
$userData = array_filter($userData, function ($item) use ($dateTimeNow, $filterEmail) {
    $dateTimeUser = new DateTime($item->created_at);
    return $dateTimeNow->diff($dateTimeUser)->days >= 7 && $item->status != "active" && empty($item->deleted) && !in_array($item->user_email, $filterEmail);
});

$closeFreeAccount = function (Model $userData) {
    $customer = new Customer();
    $customer->setId($userData->id_customer);

    $user = new User();
    $response = $user->updateUserByCustomerId([
        "id_customer" => $customer,
        "deleted" => 1
    ]);

    if (!$response) {
        throw new Exception($user->message->json());
    }

    $customer = new Customer();
    $response = $customer->updateCustomerById([
        "id" => $userData->id_customer,
        "deleted" => 1
    ]);

    if (!$response) {
        throw new Exception($customer->message->json());
    }
};

$loop = function (Model $userData) use ($closeFreeAccount) {
    $closeFreeAccount($userData);
};

array_walk($userData, $loop);
