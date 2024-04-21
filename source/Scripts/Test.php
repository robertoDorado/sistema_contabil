<?php

$requestPost = [
    "state" => "SP",
    "zipcode" => "02723-050",
    "document" => "47.503.014/0001-91"
];

if (!preg_match("/^[A-Z]{2}$/", $requestPost["state"])) {
    throw new Exception("estado inválido");
}

$verifyZipcode = preg_replace("/[^\d]+/", "", $requestPost["zipcode"]);
if (strlen($verifyZipcode) > 8) {
    throw new Exception("cep inválido");
}

$verifyDocument = preg_replace("/[^\d]+/", "", $requestPost["document"]);
if (strlen($verifyDocument) > 14) {
    throw new Exception("documento inválido");
}

echo "ok" . PHP_EOL;