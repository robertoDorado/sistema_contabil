<?php

namespace Source\Support;

use Exception;
use NFePHP\Common\Certificate;
use NFePHP\NFe\Common\Standardize;
use NFePHP\NFe\Tools;
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

    /** @var Tools */
    private Tools $tools;

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

    public function getMake(): Make
    {
        return $this->make;
    }

    public function sendNfeToSefaz(): array
    {
        $xml = $this->make->getXML();
        if (empty($xml)) {
            http_response_code(500);
            echo json_encode(["error" => "o xml não foi gerado"]);
            die;
        }

        $xml = $this->tools->signNFe($xml);
        $idLote = str_pad(rand(1, 999999999999999), 15, '0', STR_PAD_LEFT);

        // Enviar NF-e para a sefaz
        $response = $this->tools->sefazEnviaLote([$xml], $idLote);
        $stdResponse = (new Standardize())->toStd($response);
        if ($stdResponse->cStat != 103) {
            http_response_code(500);
            echo json_encode(["error" => "Erro ao enviar lote: " . $stdResponse->xMotivo]);
            die;
        }

        $dom = new \DOMDocument('1.0', 'utf-8');
        $dom->loadXML($xml);
        $chNFe = preg_replace("/[^\d]+/", '', $dom->getElementsByTagName('infNFe')->item(0)->getAttribute("Id"));

        $protocolNumber = !empty($dom->getElementsByTagName('nProt')->item(0)->nodeValue) ?
            $dom->getElementsByTagName('nProt')->item(0)->nodeValue :
            date("y") . str_pad(mt_rand(0, 9999999999999), 13, '0', STR_PAD_LEFT);

        $xml = $dom->saveXML();
        return [
            "access_key" => $chNFe,
            "protocol_number" => $protocolNumber,
            "xml" => $xml
        ];
    }

    public function paymentDetails(array $config): Invoice
    {
        $this->data->std = new \stdClass();
        $this->data->std->indPag = $config["indPag"];
        $this->data->std->tPag = $config["tPag"];
        $this->data->std->vPag = $config["vPag"];
        $this->data->std->CNPJ = $config["CNPJ"];
        $this->data->std->tBand = $config["tBand"];
        $this->data->std->cAut = $config["cAut"];
        $this->data->std->tpIntegra = $config["tpIntegra"];
        $this->data->std->CNPJPag = $config["CNPJPag"];
        $this->data->std->UFPag = $config["UFPag"];
        $this->data->std->CNPJReceb = $config["CNPJReceb"];
        $this->data->std->idTermPag = $config["idTermPag"];
        $this->make->tagdetPag($this->data->std);
        return $this;
    }

    public function icmsInformation(array $config): Invoice
    {
        $this->data->std = new \stdClass();
        $this->data->std->item = $config["item"];
        $this->data->std->orig = $config["orig"];
        $this->data->std->CST = $config["CST"];
        $this->data->std->modBC = $config["modBC"];
        $this->data->std->vBC = $config["vBC"];
        $this->data->std->pICMS = $config["pICMS"];
        $this->data->std->vICMS = $config["vICMS"];
        $this->data->std->pFCP = $config["pFCP"];
        $this->data->std->vFCP = $config["vFCP"];
        $this->data->std->vBCFCP = $config["vBCFCP"];
        $this->data->std->modBCST = $config["modBCST"];
        $this->data->std->pMVAST = $config["pMVAST"];
        $this->data->std->pRedBCST = $config["pRedBCST"];
        $this->data->std->vBCST = $config["vBCST"];
        $this->data->std->pICMSST = $config["pICMSST"];
        $this->data->std->vICMSST = $config["vICMSST"];
        $this->data->std->vBCFCPST = $config["vBCFCPST"];
        $this->data->std->pFCPST = $config["pFCPST"];
        $this->data->std->vFCPST = $config["vFCPST"];
        $this->data->std->vICMSDeson = $config["vICMSDeson"];
        $this->data->std->motDesICMS = $config["motDesICMS"];
        $this->data->std->pRedBC = $config["pRedBC"];
        $this->data->std->vICMSOp = $config["vICMSOp"];
        $this->data->std->pDif = $config["pDif"];
        $this->data->std->vICMSDif = $config["vICMSDif"];
        $this->data->std->vBCSTRet = $config["vBCSTRet"];
        $this->data->std->pST = $config["pST"];
        $this->data->std->vICMSSTRet = $config["vICMSSTRet"];
        $this->data->std->vBCFCPSTRet = $config["vBCFCPSTRet"];
        $this->data->std->pFCPSTRet = $config["pFCPSTRet"];
        $this->data->std->vFCPSTRet = $config["vFCPSTRet"];
        $this->data->std->pRedBCEfet = $config["pRedBCEfet"];
        $this->data->std->vBCEfet = $config["vBCEfet"];
        $this->data->std->pICMSEfet = $config["pICMSEfet"];
        $this->data->std->vICMSEfet = $config["vICMSEfet"];
        $this->data->std->vICMSSubstituto = $config["vICMSSubstituto"];
        $this->data->std->vICMSSTDeson = $config["vICMSSTDeson"];
        $this->data->std->motDesICMSST = $config["motDesICMSST"];
        $this->data->std->pFCPDif = $config["pFCPDif"];
        $this->data->std->vFCPDif = $config["vFCPDif"];
        $this->data->std->vFCPEfet = $config["vFCPEfet"];
        $this->data->std->pRedAdRem = $config["pRedAdRem"];
        $this->data->std->qBCMono = $config["qBCMono"];
        $this->data->std->adRemiICMS = $config["adRemiICMS"];
        $this->data->std->vICMSMono = $config["vICMSMono"];
        $this->data->std->adRemICMSRet = $config["adRemICMSRet"];
        $this->data->std->vICMSMonoRet = $config["vICMSMonoRet"];
        $this->data->std->vICMSMonoDif = $config["vICMSMonoDif"];
        $this->data->std->cBenefRBC = $config["cBenefRBC"];
        $this->data->std->indDeduzDeson = $config["indDeduzDeson"];
        $this->make->tagICMS($this->data->std);
        return $this;
    }

    public function paymentMethodInformation(array $config): Invoice
    {
        $this->data->std = new \stdClass();
        $this->data->std->indPag = $config["indPag"];
        $this->data->std->tPag = $config["tPag"];
        $this->data->std->vPag = $config["vPag"];
        $this->data->std->vTroco = $config["vTroco"];
        $this->make->tagpag($this->data->std);
        return $this;
    }

    public function declareTaxData(array $config): Invoice
    {
        $this->data->std = new \stdClass();
        $this->data->std->item = $config["item"];
        $this->data->std->vTotTrib = $config["vTotTrib"];
        $this->make->tagimposto($this->data->std);
        return $this;
    }

    public function shippingMethod(array $config): Invoice
    {
        $this->data->std = new \stdClass();
        $this->data->std->modFrete = $config["modFrete"];
        $this->make->tagtransp($this->data->std);
        return $this;
    }

    public function productOrServiceData(array $config): Invoice
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
        $this->data->std->vProd = $config["vProd"];
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

    public function recipientAddressData(array $config): Invoice
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

    public function recipientData(array $config): Invoice
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

    public function issuerAddressData(array $config): Invoice
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

    public function issuerData(array $config): Invoice
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
            $this->tools = new Tools(json_encode($this->configData), $certificate);
            return true;
        } catch (Exception $_) {
            http_response_code(500);
            echo json_encode(["error" => "certficado inválido"]);
            die;
        }
    }
}
