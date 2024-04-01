<?php

use Ramsey\Uuid\Uuid;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;
use Stripe\StripeClient;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

// echo "Digite o nome completo: ";
// $userFullName = trim(fgets(STDIN));

// echo "Digite um nome de usuário: ";
// $userNickName = trim(fgets(STDIN));

// echo "Digite um e-mail válido: ";
// $userEmail = trim(fgets(STDIN));

// echo "Digite uma senha: ";
// $userPassword = rtrim(`stty -echo; head -1; stty echo`);

// echo "\n";
$customerUuid = Uuid::uuid6();
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
    "unit_amount_decimal" => "69.90",
    "recurring" => ["interval" => "month"],
    "product" => $product->id
]);

$subscription = $stripe->subscriptions->create([
    "customer" => $customer->id, // Tentar persistir o cliente na base e passar como id
    "items" => [["price" => $price->id]],
    "payment_settings" => [
        "payment_method_types" => "card"
    ]
]);
print_r($subscription);

// $user = new User();
// $userData = [
//     "uuid" => Uuid::uuid6(),
//     "user_full_name" => $userFullName,
//     "user_nick_name" => $userNickName,
//     "user_email" => $userEmail,
//     "user_password" => password_hash($userPassword, PASSWORD_DEFAULT),
//     "deleted" => 0
// ];

// if (!$user->persistData($userData)) {
//     throw new \Exception("erro ao criar usuário");
// }

// echo "usuário criado com sucesso" . "\n";