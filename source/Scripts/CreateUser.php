<?php

use Ramsey\Uuid\Uuid;
use Source\Domain\Model\User;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

echo "Digite o nome completo: ";
$userFullName = trim(fgets(STDIN));

echo "Digite um nome de usuário: ";
$userNickName = trim(fgets(STDIN));

echo "Digite um e-mail válido: ";
$userEmail = trim(fgets(STDIN));

echo "Digite uma senha: ";
$userPassword = rtrim(`stty -echo; head -1; stty echo`);

echo "\n";

$user = new User();
$userData = [
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