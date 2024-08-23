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
    public function __construct(array $configData)
    {
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

    public function invoiceIdentification()
    {
        $std = new \stdClass();
        $std->cUF = 35;
        $std->cNF = '80070008';
        $std->natOp = 'Venda de Mercadorias';
        $std->indPag = 0; //NÃO EXISTE MAIS NA VERSÃO 4.00
        $std->mod = 55;
        $std->serie = 1;
        $std->nNF = 2;
        $std->dhEmi = '2015-02-19T13:48:00-02:00';
        $std->dhSaiEnt = null;
        $std->tpNF = 1;
        $std->idDest = 1;
        $std->cMunFG = 3518800;
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
    }

    public function makeInvoice()
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
        } catch (Exception $th) {
            throw new Exception($th->getMessage());
        }
    }
}
