<?php

use Stripe\StripeClient;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

try {
    $stripe = new StripeClient(STRIPE_TEST_SECRET_KEY);
    $cancelSubscription = $stripe->subscriptions->update("sub_1P5atYC1Uv10wqUugv1m699R", [
        'cancel_at_period_end' => true
    ]);
    print_r($cancelSubscription);
} catch (\Stripe\Exception\ApiErrorException $e) {
    throw new Exception("Erro ao cancelar a assinatura: " . $e->getError()->message);
} catch (Exception $e) {
    throw new Exception("Erro ao cancelar a assinatura: " . $e->getMessage());
}