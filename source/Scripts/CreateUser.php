<?php

use Source\Domain\Model\User;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

echo "Digite o nome completo: ";
$userFullName = fgets(STDIN);

echo "Digite um nome de usuário: ";
$userNickName = fgets(STDIN);

echo "Digite um e-mail válido: ";
$userEmail = fgets(STDIN);

echo "Digite uma senha: ";
$userPassword = rtrim(`stty -echo; head -1; stty echo`);

echo "\n";

$user = new User();
$userData = [
    "user_full_name" => $userFullName,
    "user_nick_name" => $userNickName,
    "user_email" => $userEmail,
    "user_password" => password_hash($userPassword, PASSWORD_DEFAULT)
];

if (!$user->persistData($userData)) {
    throw new \Exception("erro ao criar usuário");
}

echo "usuário criado com sucesso" . "\n";