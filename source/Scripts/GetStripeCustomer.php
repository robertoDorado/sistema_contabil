<?php

use Stripe\StripeClient;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

try {
    $stripe = new StripeClient(STRIPE_TEST_SECRET_KEY);
    $existingCustomers = $stripe->customers->all([
        'email' => "robertodorado7@gmail.com",
        'limit' => 1,
    ]);
    print_r($existingCustomers);
} catch (\Stripe\Exception\ApiErrorException $e) {
    throw new Exception("Erro ao encontrar cliente: " . $e->getError()->message);
} catch (Exception $e) {
    throw new Exception("Erro ao encontrar cliente: " . $e->getMessage());
}