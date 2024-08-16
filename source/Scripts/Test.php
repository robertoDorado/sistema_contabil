<?php
$data = [
    "a" => [
        "itemA",
        "itemB",
        "itemC",
    ],
    "b" => [
        "itemA",
        "itemB",
        "itemCD",
    ],
];


$data['c'] = [];
foreach ($data as $key => &$items) {
    if ($key === 'c') continue; // Ignora a chave 'c' já que estamos adicionando a ela

    foreach ($items as $item) {
        if (in_array($item, ['itemC', 'itemCD'])) {
            $data['c'][] = $item;
        }
    }

    $items = array_filter($items, function($item) {
        if (!in_array($item, ['itemC', 'itemCD'])) {
            return $item;
        }
    });
}

print_r($data);