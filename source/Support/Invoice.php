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

    public function paymentMethodInformation(array $config)
    {
        $this->data->std = new \stdClass();
        $this->data->std->indPag = $config["indPag"];
        $this->data->std->tPag = $config["tPag"];
        $this->data->std->vPag = $config["vPag"];
        $this->data->std->vTroco = $config["vTroco"];
        $this->make->tagpag($this->data->std);
        return $this;
    }

    public function declareTaxData(array $config)
    {
        $this->data->std = new \stdClass();
        $this->data->std->item = $config["item"];
        $this->data->std->vTotTrib = $config["vTotTrib"];
        $this->make->tagimposto($this->data->std);
        return $this;
    }

    public function shippingMethod(array $config)
    {
        $this->data->std = new \stdClass();
        $this->data->std->modFrete = $config["modFrete"];
        $this->make->tagtransp($this->data->std);
        return $this;
    }

    public function productOrServiceData(array $config)
    {
        $this->data->std = new \stdClass();
        $this->data->std->item = $config["item"];
        $this->data->std->cProd = $config["cProd"];
        $this->data->std->cEAN = $config["cEAN"];
        $this->data->std->cBarra = $config["cBarra"];
        $this->data->std->xProd = $config["xProd"];
        $this->data->std->NCM = $config["NCM"];
        $this->data->std->cBenef = $config["cBenef"];
        $this->data->std->EXTIPI = $config["EXTIPI"];
        $this->data->std->CFOP = $config["CFOP"];
        $this->data->std->uCom = $config["uCom"];
        $this->data->std->qCom = $config["qCom"];
        $this->data->std->vUnCom = $config["vUnCom"];
        $this->data->std->vProd = $config["VProd"];
        $this->data->std->cEANTrib = $config["cEANTrib"];
        $this->data->std->cBarraTrib = $config["cBarraTrib"];
        $this->data->std->uTrib = $config["uTrib"];
        $this->data->std->qTrib = $config["qTrib"];
        $this->data->std->vUnTrib = $config["vUnTrib"];
        $this->data->std->vFrete = $config["vFrete"];
        $this->data->std->vSeg = $config["vSeg"];
        $this->data->std->vDesc = $config["vDesc"];
        $this->data->std->vOutro = $config["vOutro"];
        $this->data->std->indTot = $config["indTot"];
        $this->data->std->xPed = $config["xPed"];
        $this->data->std->nItemPed = $config["nItemPed"];
        $this->data->std->nFCI = $config["nFCI"];
        $this->make->tagprod($this->data->std);
        return $this;
    }

    public function recipientAddressData(array $config)
    {
        $this->data->std = new \stdClass();
        $this->data->std->xLgr = $config["xLgr"];
        $this->data->std->nro = $config["nro"];
        $this->data->std->xCpl = $config["xCpl"];
        $this->data->std->xBairro = $config["xBairro"];
        $this->data->std->cMun = $config["cMun"];
        $this->data->std->xMun = $config["xMun"];
        $this->data->std->UF = $config["UF"];
        $this->data->std->CEP = $config["CEP"];
        $this->data->std->cPais = $config["cPais"];
        $this->data->std->xPais = $config["xPais"];
        $this->data->std->fone = $config["fone"];
        $this->make->tagenderDest($this->data->std);
        return $this;
    }

    public function recipientData(array $config)
    {
        $this->data->std = new \stdClass();
        $this->data->std->xNome = $config["xNome"];
        $this->data->std->indIEDest = $config["indIEDest"];
        $this->data->std->IE = $config["IE"];
        $this->data->std->ISUF = $config["ISUF"];
        $this->data->std->IM = $config["IM"];
        $this->data->std->email = $config["email"];
        $this->data->std->CNPJ = $config["CNPJ"];
        $this->data->std->CPF = $config["CPF"];
        $this->data->std->idEstrangeiro = $config["idEstrangeiro"];
        $this->make->tagdest($this->data->std);
        return $this;
    }

    public function issuerAddressData(array $config)
    {
        $this->data->std = new \stdClass();
        $this->data->std->xLgr = $config["xLgr"];
        $this->data->std->nro = $config["nro"];
        $this->data->std->xCpl = $config["xCpl"];
        $this->data->std->xBairro = $config["xBairro"];
        $this->data->std->cMun = $config["cMun"];
        $this->data->std->xMun = $config["xMun"];
        $this->data->std->UF = $config["UF"];
        $this->data->std->CEP = $config["CEP"];
        $this->data->std->cPais = $config["cPais"];
        $this->data->std->xPais = $config["xPais"];
        $this->data->std->fone = $config["fone"];
        $this->make->tagenderEmit($this->data->std);
        return $this;
    }

    public function issuerData(array $config)
    {
        $this->data->std = new \stdClass();
        $this->data->std->xNome = $config["xNome"];
        $this->data->std->xFant = $config["xFant"];
        $this->data->std->IE = $config["IE"];
        $this->data->std->CNAE = $config["CNAE"];
        $this->data->std->CRT = $config["CRT"];
        $this->data->std->CNPJ = $config["CNPJ"];
        $this->make->tagemit($this->data->std);
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
        $this->data->std = new \stdClass();
        $this->data->std->cUF = $config["cUF"];
        $this->data->std->cNF = $config["cNF"];
        $this->data->std->natOp = $config["natOp"];
        $this->data->std->mod = $config["mod"];
        $this->data->std->serie = $config["serie"];
        $this->data->std->nNF = $config["nNF"];
        $this->data->std->dhEmi = $config["dhEmi"];
        $this->data->std->dhSaiEnt = $config["dhSaiEnt"];
        $this->data->std->tpNF = $config["tpNF"];
        $this->data->std->idDest = $config["idDest"];
        $this->data->std->cMunFG = $config["cMunFG"];
        $this->data->std->tpImp = $config["tpImp"];
        $this->data->std->tpEmis = $config["tpEmis"];
        $this->data->std->tpAmb = $this->configData["tpAmb"];
        $this->data->std->finNFe = $config["finNFe"];
        $this->data->std->indFinal = $config["indFinal"];
        $this->data->std->indPres = $config["indPres"];
        $this->data->std->indIntermed = null;
        $this->data->std->procEmi = 0;
        $this->data->std->verProc = '3.10.31';
        $this->data->std->dhCont = null;
        $this->data->std->xJust = null;
        $this->make->tagide($this->data->std);
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
