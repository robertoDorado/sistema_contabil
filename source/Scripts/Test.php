<?php
$path = dirname(dirname(__DIR__)) . "/Logs/subscription-canceled.log";
$response = file_put_contents($path, json_encode("testando...")  . PHP_EOL, FILE_APPEND);
if (!$response) {
    throw new Exception("Erro");
}