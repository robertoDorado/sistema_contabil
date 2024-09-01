<?php
$event = json_decode(file_get_contents("config/subscription_renewal.json", true));

date_default_timezone_set("America/Sao_Paulo");
$dateTime = new DateTime("2025-11-01");
echo 'start: ' . $dateTime->getTimestamp() . PHP_EOL . date("Y-m-d", $dateTime->getTimestamp()) . PHP_EOL;

$dateTime = new DateTime("2025-12-01");
echo 'end: ' . $dateTime->getTimestamp() . PHP_EOL . date("Y-m-d", $dateTime->getTimestamp()) . PHP_EOL;

$dateTime = new DateTime("2025-10-22");
echo 'canceled: ' . $dateTime->getTimestamp() . PHP_EOL . date("Y-m-d", $dateTime->getTimestamp()) . PHP_EOL;
