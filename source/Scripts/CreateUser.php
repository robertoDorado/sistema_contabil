<?php

use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Model\Customer;
use Source\Domain\Model\User;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

echo "Digite o nome completo: ";
$userFullName = trim(fgets(STDIN));

echo "Digite um nome de usuário: ";
$userNickName = trim(fgets(STDIN));

echo "Digite o cpf do usuário: ";
$userDocument = trim(fgets(STDIN));

echo "Digite o aniversário do usuário: ";
$birthDate = trim(fgets(STDIN));

echo "Digite o genero do usuário (0) feminino (1) masculino: ";
$gender = trim(fgets(STDIN));

echo "Digite o cep do usuário: ";
$zipcode = trim(fgets(STDIN));

echo "Digite o endereço do usuário: ";
$address = trim(fgets(STDIN));

echo "Digite o número endereço do usuário: ";
$addressNumber = trim(fgets(STDIN));

echo "Digite o bairro do usuário: ";
$neighborhood = trim(fgets(STDIN));

echo "Digite a cidade do usuário: ";
$city = trim(fgets(STDIN));

echo "Digite o estado do usuário: ";
$state = trim(fgets(STDIN));

echo "Digite o telefone fixo do usuário: ";
$phone = trim(fgets(STDIN));

echo "Digite o celular do usuário: ";
$cellPhone = trim(fgets(STDIN));

echo "Digite um e-mail válido: ";
$userEmail = trim(fgets(STDIN));

echo "Digite uma senha: ";
$userPassword = rtrim(`stty -echo; head -1; stty echo`);

echo "\n";

$customer = new Customer();
$requestPost = [
    "uuid" => Uuid::uuid6(),
    "customer_name" => $userFullName,
    "customer_document" => $userDocument,
    "birth_date" => $birthDate,
    "customer_gender" => $gender,
    "customer_email" => $userEmail,
    "customer_zipcode" => $zipcode,
    "customer_address" => $address,
    "customer_number" => $addressNumber,
    "customer_neighborhood" => $neighborhood,
    "customer_city" => $city,
    "customer_state" => $state,
    "customer_phone" => $phone,
    "cell_phone" => $cellPhone,
    "created_at" => date("Y-m-d"),
    "updated_at" => date("Y-m-d"),
    "deleted" => 0,
];
$customer->persistData($requestPost);
$user = new User();

$userData = [
    "id_customer" => $customer,
    "uuid" => Uuid::uuid6(),
    "user_full_name" => $userFullName,
    "user_nick_name" => $userNickName,
    "user_email" => $userEmail,
    "user_password" => password_hash($userPassword, PASSWORD_DEFAULT),
    "deleted" => 0
];

if (!$user->persistData($userData)) {
    throw new \Exception("erro ao criar usuário");
}

echo "usuário criado com sucesso" . "\n";