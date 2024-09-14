<?php
echo "********** Tabela de planos ************" . PHP_EOL;
$subscriptionPriceYear = (208.75 * (1 - (30/100))) * 12;
$subscriptionPriceMonth = 208.75;

$profitMoth = ($subscriptionPriceMonth*(1-(3.99/100)))-0.39;
$profitYear = ($subscriptionPriceYear*(1-(3.99/100)))-0.39;

echo "Plano mensal: R$ " . number_format($subscriptionPriceMonth, 2, ",", ".") . PHP_EOL;
echo "Plano anual: R$ " . number_format($subscriptionPriceYear, 2, ",", ".") . PHP_EOL;
echo "----------------------------" . PHP_EOL;
echo "Lucro mensal: R$ " . number_format($profitMoth, 2, ",", ".") . PHP_EOL;
echo "Lucro anual: R$ " . number_format($profitYear, 2, ",", ".") . PHP_EOL;
echo "-----------------------------" . PHP_EOL;

echo "************** Tabela de projeção de vendas *****************" . PHP_EOL;
echo "************ Mensal *************" . PHP_EOL;
for ($i=0; $i <= 100; $i++) {
    echo "Clientes: " . $i . " Lucro: R$ " . number_format(($profitMoth * $i), 2, ",", ".") . PHP_EOL;
}

echo "************ Anual *************" . PHP_EOL;
for ($i=0; $i <= 100; $i++) {
    echo "Clientes: " . $i . " Lucro: R$ " . number_format(($profitYear * $i), 2, ",", ".") . PHP_EOL;
}