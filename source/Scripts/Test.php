<?php
$std = new stdClass();
$std->detPag = new \stdClass();
$std->detPag->indPag = '0';
$std->detPag->tPag = '01';
$std->detPag->vPag = '200.75';
$std->detPag->vTroco = null; //incluso no layout 4.00, obrigatório informar para NFCe (65)
print_r($std);