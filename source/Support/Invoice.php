<?php

namespace Source\Support;

use Exception;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapFake;
use NFePHP\NFe\Common\Tools;
use NFePHP\NFe\Make;

/**
 * Invoice Support
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Support
 */
class Invoice
{
    /** @var array Dados de inicialização para emissão da nota fiscal */
    private array $configData;

    /** @var Make */
    private Make $make;

    /**
     * Invoice constructor
     */
    public function __construct(array $configData = [])
    {
        if (!empty($configData)) {
            $this->configData = [
                "atualizacao" => "2016-11-03 18:01:21",
                "tpAmb" => (int) $configData["tpAmb"],
                "razaosocial" => $configData["companyName"],
                "cnpj" => $configData["companyDocument"],
                "siglaUF" => $configData["companyState"],
                "certPfx" => $configData["certPfx"],
                "certPassword" => $configData["certPassword"],
                "schemes" => "PL_009_V4",
                "versao" => '4.00',
                "tokenIBPT" => "AAAAAAA",
                "CSC" => "GPB0JBWLUR6HWFTVEAS6RJ69GPCROFPBBB8G",
                "CSCid" => "000001",
                "proxyConf" => [
                    "proxyIp" => "",
                    "proxyPort" => "",
                    "proxyUser" => "",
                    "proxyPass" => ""
                ]
            ];
        }
    }

    public function requestMunicipality(): array
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://servicodados.ibge.gov.br/api/v1/localidades/municipios',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        if (!$response) {
            return [];
        }

        curl_close($curl);
        return json_decode($response, true);
    }

    public function requestStateCode(): array
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://servicodados.ibge.gov.br/api/v1/localidades/estados',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        if (!$response) {
            return [];
        }

        curl_close($curl);
        return json_decode($response, true);
    }

    public function invoiceIdentification(array $config): Invoice
    {
        $std = new \stdClass();
        $std->cUF = $config["cUF"];
        $std->cNF = $config["cNF"];
        $std->natOp = $config["natOp"];
        $std->mod = $config["mod"];
        $std->serie = $config["serie"];
        $std->nNF = $config["nNF"];
        $std->dhEmi = $config["dhEmi"];
        $std->dhSaiEnt = $config["dhSaiEnt"];
        $std->tpNF = $config["tpNF"];
        $std->idDest = $config["idDest"];
        $std->cMunFG = $config["cMunFG"];
        $std->tpImp = 1;
        $std->tpEmis = 1;
        $std->cDV = 2;
        $std->tpAmb = 2;
        $std->finNFe = 1;
        $std->indFinal = 0;
        $std->indPres = 0;
        $std->indIntermed = null;
        $std->procEmi = 0;
        $std->verProc = '3.10.31';
        $std->dhCont = null;
        $std->xJust = null;
        $this->make->tagide($std);
        return $this;
    }

    public function makeInvoice(): Invoice
    {
        $this->make = new Make();
        $std = new \stdClass();
        $std->versao = '4.00';
        $std->pk_nItem = null;

        $this->make->taginfNFe($std);
        return $this;
    }

    public function isValidCertPfx(): bool
    {
        try {
            $content = file_get_contents($this->configData['certPfx'], true);
            $certificate = Certificate::readPfx($content, $this->configData['certPassword']);
            $tools = new Tools(json_encode($this->configData), $certificate);

            if ($this->configData["tpAmb"] == 2) {
                $soap = new SoapFake();
                $soap->disableCertValidation(true);
                $tools->model('55');
                $tools->setVerAplic('5.1.34');
                $tools->loadSoapClass($soap);
            }

            return true;
        } catch (Exception $_) {
            http_response_code(500);
            echo json_encode(["error" => "certficado inválido"]);
            die;
        }
    }
}
