<?php

use Stripe\StripeClient;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

try {
    $stripe = new StripeClient(STRIPE_TEST_SECRET_KEY);
    $subscriptions = $stripe->subscriptions->all(["limit" => 10]);
    print_r($subscriptions);
} catch (\Stripe\Exception\ApiErrorException $e) {
    throw new Exception("Erro ao listar as assinaturas: " . $e->getError()->message);
} catch (Exception $e) {
    throw new Exception("Erro ao listar as assinaturas: " . $e->getMessage());
}