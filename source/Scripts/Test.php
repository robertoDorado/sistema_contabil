<?php
$subscriptionPriceYear = 156.75 * 12;
$subscriptionPriceMonth = 208.75;
echo "Plano mensal: R$ " . number_format($subscriptionPriceMonth, 2, ",", ".") . PHP_EOL;
echo "Plano anual: R$ " . number_format($subscriptionPriceYear, 2, ",", ".") . PHP_EOL;
echo "----------------------------" . PHP_EOL;
echo "Lucro mensal: R$ " . number_format(($subscriptionPriceMonth*(1-(3.99/100)))-0.39, 2, ",", ".") . PHP_EOL;
echo "Lucro anual: R$ " . number_format(($subscriptionPriceYear*(1-(3.99/100)))-0.39, 2, ",", ".") . PHP_EOL;