<?php

use Stripe\StripeClient;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

try {
    $stripe = new StripeClient(STRIPE_TEST_SECRET_KEY);
    $refund = $stripe->refunds->create([
        "charge" => "ch_3P1YRRC1Uv10wqUu08LoVWia"
    ]);
    print_r($refund);
} catch (\Stripe\Exception\ApiErrorException $e) {
    throw new Exception("Erro ao realizar o estorno: " . $e->getError()->message);

} catch (Exception $e) {
    throw new Exception("Erro ao realizar o estorno: " . $e->getMessage());
}