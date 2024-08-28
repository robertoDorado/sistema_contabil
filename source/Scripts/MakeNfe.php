<?php

use DeepCopy\TypeFilter\Date\DateIntervalFilter;
use NFePHP\NFe\Make;
use NFePHP\NFe\Tools;
use NFePHP\Common\Certificate;
use NFePHP\Common\Soap\SoapFake;
use NFePHP\DA\NFe\Danfe;
use NFePHP\NFe\Common\Standardize;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

try {
    // Carregar as configurações
    $config = json_decode(file_get_contents('config/config.json', true), true);
    $soap = new SoapFake();
    $soap->disableCertValidation(true);

    $content = file_get_contents("config/{$config['certPfx']}", true);

    // Carregar o certificado digital
    $certificate = Certificate::readPfx($content, $config['certPassword']);

    // Instanciar a classe de ferramentas da NF-e
    $tools = new Tools(json_encode($config), $certificate);
    $tools->model('55');
    $tools->setVerAplic('5.1.34');
    $tools->loadSoapClass($soap);

    // Instanciar a classe de criação do XML
    $make = new Make();

    // Criar a NF-e (mesmo processo da produção)
    $std = new stdClass();
    $std->versao = '4.00'; //versão do layout (string)
    $std->pk_nItem = null; //deixe essa variavel sempre como NULL

    // Node das informações suplementares da NFCe.
    $make->taginfNFe($std);

    $std = new stdClass();
    $std->cUF = 35;
    $std->cNF = '80070008';
    $std->natOp = 'Venda de Mercadorias';
    $std->indPag = 0; //NÃO EXISTE MAIS NA VERSÃO 4.00
    $std->mod = 55;
    $std->serie = 1;
    $std->nNF = '444444444';
    $std->dhEmi = '2015-02-19T13:48:00-00:00';
    $std->dhSaiEnt = null;
    $std->tpNF = 0;
    $std->idDest = 1;
    $std->cMunFG = 3518800;
    $std->tpImp = 1;
    $std->tpEmis = 1;
    // $std->cDV = 2;
    $std->tpAmb = 2;
    $std->finNFe = 1;
    $std->indFinal = 0;
    $std->indPres = 0;
    $std->indIntermed = null;
    $std->procEmi = 0;
    $std->verProc = '3.10.31';
    $std->dhCont = null;
    $std->xJust = null;

    // Node de identificação da NFe
    $make->tagide($std);

    $std = new stdClass();
    $std->xNome = 'Minha Empresa LTDA';
    $std->xFant = 'Minha Empresa';
    $std->IE = '12345678000195';
    $std->IEST = null;
    $std->IM = null;
    $std->CNAE = '';
    $std->CRT = 1;
    $std->CNPJ = '95352076000192'; //indicar apenas um CNPJ ou CPF
    $std->CPF = null; //indicar apenas um CNPJ ou CPF

    // Node com os dados do emitente
    $make->tagemit($std);
    
    //code...
    $std = new stdClass();
    $std->xLgr = "Rua Cerata Donzeli Bongiovani teste";
    $std->nro = 869;
    $std->xCpl = null;
    $std->xBairro = "Jardim Novo Bongiovani";
    $std->cMun = 1234567;
    $std->xMun = "São Paulo";
    $std->UF = "SP";
    $std->CEP = "19026675";
    $std->cPais = 1058;
    $std->xPais = "Brasil";
    $std->fone = null;
    // Node com o endereço do emitente
    $make->tagenderEmit($std);

    $std = new stdClass();
    $std->xNome = 'César e Carolina Transportes Ltda';
    $std->indIEDest = 1;
    $std->IE = '329369593095';
    $std->ISUF = null;
    $std->IM = null;
    $std->email = 'cobranca@cesarecarolinatransportesltda.com.br';
    $std->CNPJ = '87480709000110';
    $std->idEstrangeiro = null;
    $std->CPF = null; //indicar apenas um CNPJ ou CPF

    // Node com os dados do destinatário
    $make->tagdest($std);

    $std = new stdClass();
    $std->xLgr = "Rua Nove de Junho";
    $std->nro = 968;
    $std->xCpl = null;
    $std->xBairro = "Vila Anchieta";
    $std->cMun = 1234567;
    $std->xMun = "São Paulo";
    $std->UF = "SP";
    $std->CEP = "15050210";
    $std->cPais = 1058;
    $std->xPais = "Brasil";
    $std->fone = null;

    // Node de endereço do destinatário
    $make->tagenderDest($std);

    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->cProd = 1;
    $std->cEAN = null;
    $std->cBarra = null;
    $std->xProd = 'Produto Teste';
    $std->NCM = 33;
    $std->cBenef = null;
    $std->EXTIPI = null;
    $std->CFOP = 5102;
    $std->uCom = "77T";
    $std->qCom = 1;
    $std->vUnCom = 200.75;
    $std->vProd = 200.75;
    $std->cEANTrib = null;
    $std->cBarraTrib = null;
    $std->uTrib = 1;
    $std->qTrib = 1;
    $std->vUnTrib = 4.75;
    $std->vFrete = null;
    $std->vSeg = 2.75;
    $std->vDesc = null;
    $std->vOutro = null;
    $std->indTot = 1;
    $std->xPed = 'Xh78';
    $std->nItemPed = 45;
    $std->nFCI = null;

    // Node de dados do produto/serviço
    $make->tagprod($std);

    $std = new stdClass();
    $std->vBC = null;
    $std->vICMS = null;
    $std->vICMSDeson = null;
    $std->vBCST = null;
    $std->vST = null;
    $std->vProd = null;
    $std->vFrete = null;
    $std->vSeg = null;
    $std->vDesc = null;
    $std->vII = null;
    $std->vIPI = null;
    $std->vPIS = null;
    $std->vCOFINS = null;
    $std->vOutro = null;
    $std->vNF = null;
    $std->vIPIDevol = null;
    $std->vTotTrib = null;
    $std->vFCP = null;
    $std->vFCPST = null;
    $std->vFCPSTRet = null;
    $std->vFCPUFDest = null;
    $std->vICMSUFDest = null;
    $std->vICMSUFRemet = null;
    $std->qBCMono = null;
    $std->vICMSMono = null;
    $std->qBCMonoReten = null;
    $std->vICMSMonoReten = null;
    $std->qBCMonoRet = null;
    $std->vICMSMonoRet = null;

    // NOTA: Esta tag não necessita que sejam passados valores, 
    // pois a classe irá calcular esses totais e irá usar essa totalização para complementar 
    // e gerar esse node, caso nenhum valor seja passado como parâmetro.
    $make->tagICMSTot($std);

    $std = new stdClass();
    $std->modFrete = 1;

    // Node indicativo da forma de frete
    $make->tagtransp($std);

    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->vTotTrib = null;

    // Node inicial dos Tributos incidentes no Produto ou Serviço do item da NFe
    $make->tagimposto($std);

    $std = new stdClass();
    $std->indPag = null;
    $std->tPag = null;
    $std->vPag = null;
    $std->vTroco = null; //incluso no layout 4.00, obrigatório informar para NFCe (65)

    // Node referente as formas de pagamento OBRIGATÓRIO para NFCe a partir do layout 3.10
    //  e também obrigatório para NFe (modelo 55) a partir do layout 4.00
    $make->tagpag($std);

    $std = new stdClass();
    $std->item = 1; //item da NFe
    $std->orig = 0;
    $std->CST = '00';
    $std->modBC = 3;
    $std->vBC = 100;
    $std->pICMS = 18;
    $std->vICMS = 18;
    $std->pFCP;
    $std->vFCP;
    $std->vBCFCP;
    $std->modBCST;
    $std->pMVAST;
    $std->pRedBCST;
    $std->vBCST;
    $std->pICMSST;
    $std->vICMSST;
    $std->vBCFCPST;
    $std->pFCPST;
    $std->vFCPST;
    $std->vICMSDeson;
    $std->motDesICMS;
    $std->pRedBC;
    $std->vICMSOp;
    $std->pDif;
    $std->vICMSDif;
    $std->vBCSTRet;
    $std->pST;
    $std->vICMSSTRet;
    $std->vBCFCPSTRet;
    $std->pFCPSTRet;
    $std->vFCPSTRet;
    $std->pRedBCEfet;
    $std->vBCEfet;
    $std->pICMSEfet;
    $std->vICMSEfet;
    $std->vICMSSubstituto; //NT 2020.005 v1.20
    $std->vICMSSTDeson; //NT 2020.005 v1.20
    $std->motDesICMSST; //NT 2020.005 v1.20
    $std->pFCPDif; //NT 2020.005 v1.20
    $std->vFCPDif; //NT 2020.005 v1.20
    $std->vFCPEfet; //NT 2020.005 v1.20
    $std->pRedAdRem; //NT 2023.001-v1.10
    $std->qBCMono; //NT 2023.001-v1.10
    $std->adRemiICMS; //NT 2023.001-v1.10
    $std->vICMSMono; //NT 2023.001-v1.10
    $std->adRemICMSRet; //NT 2023.001-v1.10
    $std->vICMSMonoRet; //NT 2023.001-v1.10
    $std->vICMSMonoDif; //NT 2023.001-v1.10
    $std->cBenefRBC; //NT 2019.001 v1.61
    $std->indDeduzDeson; //NT 2023.004 v1.00
    $make->tagICMS($std);

    $std = new stdClass();
    $std->indPag = '0'; //0= Pagamento à Vista 1= Pagamento à Prazo
    $std->tPag = '03';
    $std->vPag = 200.75; //Obs: deve ser informado o valor pago pelo cliente
    $std->CNPJ = '12345678901234';
    $std->tBand = '01';
    $std->cAut = '3333333';
    $std->tpIntegra = 1; //incluso na NT 2015/002
    $std->CNPJPag; //NT 2023.004 v1.00
    $std->UFPag; //NT 2023.004 v1.00
    $std->CNPJReceb; //NT 2023.004 v1.00
    $std->idTermPag; //NT 2023.004 v1.00

    $make->tagdetPag($std);

    // Geração do XML
    $xml = $make->getXML();

    // Assinar XML
    $xml = $tools->signNFe($xml);
    $idLote = str_pad(rand(1, 999999999999999), 15, '0', STR_PAD_LEFT);

    // Enviar NF-e para a sefaz
    // $response = $tools->sefazEnviaLote([$xml], $idLote);
    // $stdResponse = (new Standardize())->toStd($response);
    // if ($stdResponse->cStat != 103) {
    //     throw new Exception("Erro ao enviar lote: " . $stdResponse->xMotivo);
    // }

    $path = "source/Scripts/config";
    $response = file_put_contents("{$path}/nota.xml", $xml);
    if (!$response) {
        throw new Exception("erro ao criar o arquivo xml");
    }

    $parseXml = simplexml_load_string($xml);
    if ($parseXml === false) {
        throw new Exception('xml está malformado ou inválido.');
    }

    $danfe = new Danfe($xml);
    $danfe->debugMode(true);
    $pdf = $danfe->render();

    $response = file_put_contents("{$path}/nota.pdf", $pdf);
    if (!$response) {
        throw new Exception("erro ao criar o arquivo xml");
    }
} catch (Exception $th) {
    echo $th->getMessage() . PHP_EOL;
    if (!empty($make)) {
        print_r($make->getErrors());
    }
}
