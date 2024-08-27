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

    /** @var object Data */
    private object $data;

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

            $this->data = new \stdClass();
        }
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function recipientAddress(array $config)
    {
        $this->std = new \stdClass();
        $this->std->xNome = $config["xNome"];
        $this->std->indIEDest = $config["indIEDest"];
        $this->std->IE = $config["IE"];
        $this->std->ISUF = $config["ISUF"];
        $this->std->IM = $config["IM"];
        $this->std->email = $config["email"];
        $this->std->CNPJ = $config["CNPJ"];
        $this->std->CPF = $config["CPF"];
        $this->std->idEstrangeiro = $config["idEstrangeiro"];
        $this->make->tagdest($this->std);
        return $this;
    }

    public function issuerAddressData(array $config)
    {
        $this->std = new \stdClass();
        $this->std->xLgr = $config["xLgr"];
        $this->std->nro = $config["nro"];
        $this->std->xCpl = $config["xCpl"];
        $this->std->xBairro = $config["xBairro"];
        $this->std->cMun = $config["cMun"];
        $this->std->xMun = $config["xMun"];
        $this->std->UF = $config["UF"];
        $this->std->CEP = $config["CEP"];
        $this->std->cPais = $config["cPais"];
        $this->std->xPais = $config["xPais"];
        $this->std->fone = $config["fone"];
        $this->make->tagenderEmit($this->std);
        return $this;
    }

    public function issuerData(array $config)
    {
        $this->std = new \stdClass();
        $this->std->xNome = $config["xNome"];
        $this->std->xFant = $config["xFant"];
        $this->std->IE = $config["IE"];
        $this->std->CNAE = $config["CNAE"];
        $this->std->CRT = $config["CRT"];
        $this->std->CNPJ = $config["CNPJ"];
        $this->make->tagemit($this->std);
        return $this;
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
        $this->std = new \stdClass();
        $this->std->cUF = $config["cUF"];
        $this->std->cNF = $config["cNF"];
        $this->std->natOp = $config["natOp"];
        $this->std->mod = $config["mod"];
        $this->std->serie = $config["serie"];
        $this->std->nNF = $config["nNF"];
        $this->std->dhEmi = $config["dhEmi"];
        $this->std->dhSaiEnt = $config["dhSaiEnt"];
        $this->std->tpNF = $config["tpNF"];
        $this->std->idDest = $config["idDest"];
        $this->std->cMunFG = $config["cMunFG"];
        $this->std->tpImp = $config["tpImp"];
        $this->std->tpEmis = $config["tpEmis"];
        $this->std->tpAmb = $this->configData["tpAmb"];
        $this->std->finNFe = $config["finNFe"];
        $this->std->indFinal = $config["indFinal"];
        $this->std->indPres = $config["indPres"];
        $this->std->indIntermed = null;
        $this->std->procEmi = 0;
        $this->std->verProc = '3.10.31';
        $this->std->dhCont = null;
        $this->std->xJust = null;
        $this->make->tagide($this->std);
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
