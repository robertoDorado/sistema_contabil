<?php
$data = [
    "values" => [
        15,
        10,
        20
    ]
];

$data["values"]  = $data["values"] ?? [];
$value = array_filter($data["values"], function ($number) {
    return $number == 15;
});
print_r($value);