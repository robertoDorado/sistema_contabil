<?php
$array = [["opcao a", "1.1.1.02", "opcao b"], ["opcao a", "2.3.4.05", "opcao b"], ["opcao a", "6.7.8.09", "opcao b"]];

$novoArray = array_map(function($item) {
    $item[1] = preg_replace("/[\.]+/", ",", $item[1]);
    return $item;
}, $array);

print_r($novoArray);