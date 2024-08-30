<?php

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Tools;

try {
    $config = json_decode(file_get_contents('config/config.json', true), true);
    $content = file_get_contents("config/{$config['certPfx']}", true);
    
    $certificate = Certificate::readPfx($content, $config['certPassword']);
    $tools = new Tools(json_encode($config), $certificate);
    
    // Chave de acesso da NFe que deseja consultar
    $chaveNFe = '35150295352076000192550014444444441800700083'; 
    // Consulta a situação da NFe
    $response = $tools->sefazConsultaChave($chaveNFe);
    
    // Processa a resposta da SEFAZ
    $standardize = new Standardize($response);
    $std = $standardize->toStd();

    if ($std->cStat == '100') { // Código 100 significa autorizado
        $protocolo = $std->protNFe->infProt->nProt;
        echo "Número do protocolo: $protocolo";
    } else {
        echo "Erro ao consultar NFe: {$std->xMotivo}";
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
