<?php

$value = "1.545,23";
if (empty($value)) {
    throw new \Exception("Valor a ser convertido não pode estar vazio.");
}

$value = preg_replace("/[^\d\.,]+/", "", $value);
$value = str_replace(".", "", $value);
$value = str_replace(",", ".", $value);
$value = floatval($value);

if (gettype($value) !== "double") {
    throw new \Exception("Erro na conversão do valor para float");
}

echo $value . PHP_EOL;