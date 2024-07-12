<?php

$response = new \stdClass();
$response->status = false;

$verifyData = [
    "label" => function () use ($response) {
        $response->status = true;
        $response->alter = "olá";
    }
];

$verifyData["label"]();
var_dump($response);