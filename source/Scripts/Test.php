<?php
$requestPost["municipalityInvoice"] = "São Paulo";
if (!preg_match("/\d+-\w/i", $requestPost["municipalityInvoice"])) {
    echo "erro" . PHP_EOL;
    die;
}
echo "ok" . PHP_EOL;
