<?php

use Ramsey\Uuid\Uuid;
use Source\Domain\Model\Customer;
use Stripe\StripeClient;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

try {
    $customerUuid = Uuid::uuid4();
    $requestPost = [
        "uuid" => $customerUuid,
        "customer_name" => "Heloisa Cecília Marlene da Costa",
        "customer_document" => "722.140.966-80",
        "birth_date" => "2006-06-21",
        "customer_gender" => 0,
        "customer_email" => "heloisa_dacosta@fibran.com.br",
        "customer_zipcode" => "58706-050",
        "customer_address" => "Rua Nelson Pereira",
        "customer_number" => "356",
        "customer_neighborhood" => "São Sebastião",
        "customer_city" => "Patos",
        "customer_state" => "PB",
        "customer_phone" => "(83) 2557-8550",
        "cell_phone" => "(83) 98684-9059",
        "created_at" => date("Y-m-d"),
        "updated_at" => date("Y-m-d"),
        "deleted" => 0,
    ];
    
    $customer = new Customer();
    $customer->persistData($requestPost);
    
    $stripe = new StripeClient(STRIPE_TEST_SECRET_KEY);
    $customer = $stripe->customers->create([
        "id" => $customerUuid,
        "name" => $requestPost["customer_name"],
        "email" => $requestPost["customer_email"],
        "source" => "tok_visa_cartesBancaires"
    ]);
    
    $product = $stripe->products->create([
        "name" => "sistema_contabil premium",
        "description" => "Assinatura premium do Sistema Contábil. 
        Projetada para atender às demandas mais exigentes de empresas 
        e profissionais contábeis, esta assinatura representa o ápice da inovação, 
        confiabilidade e eficiência no mundo da contabilidade."
    ]);
    
    $price = $stripe->prices->create([
        "currency" => "brl",
        "unit_amount" => 6990,
        "recurring" => ["interval" => "month"],
        "product" => $product->id
    ]);
    
    $subscription = $stripe->subscriptions->create([
        "customer" => $customer->id, // Tentar persistir o cliente na base e passar como id
        "items" => [["price" => $price->id]],
        "expand" => ["latest_invoice.payment_intent"]
    ]);

    print_r($subscription);
}catch (\Stripe\Exception\CardException $e) {
    throw new Exception("Erro no cartão de crédito: " . $e->getError()->message);
} catch (\Stripe\Exception\InvalidRequestException $e) {
    throw new Exception("Requisição inválida: " . $e->getError()->message);
} catch (\Stripe\Exception\AuthenticationException $e) {
    throw new Exception("Erro na autenticação: " . $e->getError()->message);
} catch (\Stripe\Exception\ApiConnectionException $e) {
   throw new Exception("Erro na conexão com a api: " . $e->getError()->message);
} catch (\Stripe\Exception\ApiErrorException $e) {
    throw new Exception("Erro na api: " . $e->getError()->message);
} catch (Exception $e) {
    throw new Exception($e->getMessage());
}