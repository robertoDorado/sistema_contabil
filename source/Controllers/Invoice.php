<?php

namespace Source\Controllers;

use DateTime;
use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\Company;
use Source\Support\Invoice as SupportInvoice;

/**
 * Invoice Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Invoice extends Controller
{
    /**
     * Invoice constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function invoiceReport()
    {
        $responseInitializaUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    'natureOperation',
                    'invoiceNumber',
                    'invoiceSeries',
                    'invoiceType',
                    'idInvoiceOperation',
                    'purposeOfIssuance',
                    'finalConsumer',
                    'buyersPresence',
                    'pfxFile',
                    'certPassword',
                    'environment',
                    'companyName',
                    'csrfToken',
                    'fantasyName',
                    'companyDocument',
                    'stateRegistration',
                    'companyTaxRegime',
                    'companyZipcode',
                    'companyAddress',
                    'companyAddressNumber',
                    'companyNeighborhood',
                    'companyState',
                    'municipalityInvoice',
                    'recipientName',
                    'recipientStateRegistrationIndicator',
                    'recipientEmail',
                    'recipientDocumentType',
                    'recipientDocument',
                    'recipientZipcode',
                    'recipientAddress',
                    'recipientAddressNumber',
                    'recipientNeighborhood',
                    'recipientMunicipality',
                    'recipientState',
                    'recipientPhone',
                    'productItem',
                    'productDescription',
                    'productComercialUnit',
                    'qttyProduct',
                    'productValueUnit',
                    'productTotalValue',
                    'productTaxUnit',
                    'qttyProuctTax',
                    'taxUnitValue',
                    'productNcmCode',
                    'productCodeCfop'
                ]
            )->getAllPostData();
            $requestFile = $this->getRequestFiles()->getAllFiles();

            $message = function (array $data, int $code) {
                http_response_code($code);
                echo json_encode($data);
            };

            if (!preg_match("/^\d{9}$/", $requestPost["invoiceNumber"])) {
                $message(["error" => "número da nota inválido"], 500);
                die;
            }

            if (empty($responseInitializaUserAndCompany["company_id"])) {
                $message(["error" => "selecione uma empresa antes de emitir uma nota fiscal"], 500);
                die;
            }

            if (!preg_match("/^\d+$/", $requestPost["invoiceSeries"])) {
                $message(["error" => "o número de série deve ser apenas numérico"], 500);
                die;
            }

            if (!preg_match("/\d+-\w/i", $requestPost["municipalityInvoice"])) {
                $message(["error" => "código do município inválido"], 500);
                die;
            }

            if ($requestPost["invoiceSeries"] > 999) {
                $message(["error" => "o número de série da nota não pode ser acima de 999"], 500);
                die;
            }

            if ($requestPost["invoiceSeries"] < 0) {
                $message(["error" => "o número de série da nota não pode ser um valor negativo"], 500);
                die;
            }

            $validateEnvironment = ['1', '2'];
            if (!in_array($requestPost["environment"], $validateEnvironment)) {
                $message(["error" => "variável de ambiente inválida"], 500);
                die;
            }

            $validateInvoiceType = ['0', '1'];
            if (!in_array($requestPost["invoiceType"], $validateInvoiceType)) {
                $message(["error" => "Tipo de nota fiscal inválida"], 500);
                die;
            }

            $validateIdInvoiceOperation = ['1', '2', '3'];
            if (!in_array($requestPost["idInvoiceOperation"], $validateIdInvoiceOperation)) {
                $message(["error" => "Identificador do destino da operação inválida"], 500);
                die;
            }

            $validatePurposeOfIssuance = ['1', '2', '3', '4'];
            if (!in_array($requestPost["purposeOfIssuance"], $validatePurposeOfIssuance)) {
                $message(["error" => "finalidade da emissão inválida"], 500);
                die;
            }

            $validateFinalConsumer = ['0', '1'];
            if (!in_array($requestPost["finalConsumer"], $validateFinalConsumer)) {
                $message(["error" => "consumidor final inválido"], 500);
                die;
            }

            $validateBuyersPresence = ['0', '1', '2', '3', '4', '9'];
            if (!in_array($requestPost["buyersPresence"], $validateBuyersPresence)) {
                $message(["error" => "presença do consumidor inválido"], 500);
                die;
            }

            $validateCompanyTaxRegime = ['1', '2', '3'];
            if (!in_array($requestPost["companyTaxRegime"], $validateCompanyTaxRegime)) {
                $message(["error" => "regime tributário da empresa inválido"], 500);
                die;
            }

            $validateRecipientDocumentType = ['1', '2'];
            if (!in_array($requestPost["recipientDocumentType"], $validateRecipientDocumentType)) {
                $message(["error" => "tipo de documento do destinatário é inválido"], 500);
                die;
            }

            $recipientStateRegistrationIndicator = ['1', '2', '9'];
            if (!in_array($requestPost["recipientStateRegistrationIndicator"], $recipientStateRegistrationIndicator)) {
                $message(["error" => "indicador de inscrição estadual do destinatário é inválido"], 500);
                die;
            }

            $validateShippingMethod = ['0', '1', '2', '3', '4', '9'];
            if (!in_array($requestPost["shippingMethod"], $validateShippingMethod)) {
                $message(["error" => "modalidade de frete inválido"], 500);
                die;
            }

            $validateCodeMethodPayment = ["01", "02", "03", "04", "05", "10", "11", "12", "13", "15", "90", "99"];
            if (!in_array($requestPost["codeMethodPayment"], $validateCodeMethodPayment)) {
                $message(["error" => "código da forma de pagamento inválido"], 500);
                die;
            }

            if (!empty($requestPost["cnaeInformation"])) {
                if (!preg_match("/^\d+$/", $requestPost["cnaeInformation"])) {
                    $message(["error" => "campo CNAE inválido"], 500);
                    die;
                }
            }

            $requestPost["municipalityInvoice"] = explode("-", $requestPost["municipalityInvoice"]);
            $municipalityCode = $requestPost["municipalityInvoice"][0];
            $municipalityName = $requestPost["municipalityInvoice"][1];

            $requestPost["recipientMunicipality"] = explode("-", $requestPost["recipientMunicipality"]);
            $recipientMunicipalityCode = $requestPost["recipientMunicipality"][0];
            $recipientMunicipalityName = $requestPost["recipientMunicipality"][1];

            $validateCleanValue = [
                "companyDocument",
                "recipientDocument",
                "recipientStateRegistration",
                "stateRegistration",
                "companyPhone",
                "recipientPhone",
            ];

            foreach ($validateCleanValue as $key) {
                $requestPost[$key] = preg_replace("/[^\d]+/", "", $requestPost[$key]);
            }

            $validateConvertCurrencyRealToFloat = [
                "productValueUnit",
                "productTotalValue",
                "taxUnitValue",
                "productShippingValue",
                "productInsuranceValue",
                "productDiscountAmount",
                "productValueOtherExpenses",
                "totalTaxValue",
                "paymentTotalValue",
                "changeMoney"
            ];

            foreach ($validateConvertCurrencyRealToFloat as $key) {
                $requestPost[$key] = convertCurrencyRealToFloat($requestPost[$key]);
            }

            $recipientData = [
                "xNome" => $requestPost["recipientName"],
                "indIEDest" => $requestPost["recipientStateRegistrationIndicator"],
                "IE" => $requestPost["recipientStateRegistration"],
                "ISUF" => null,
                "IM" => null,
                "email" => $requestPost["recipientEmail"],
                "idEstrangeiro" => null
            ];

            $validateRecipientDocument = [
                "1" => function (string $value) use (&$recipientData) {
                    $recipientData["CPF"] = $value;
                    $recipientData["CNPJ"] = null;
                },
                "2" => function (string $value) use (&$recipientData) {
                    $recipientData["CNPJ"] = $value;
                    $recipientData["CPF"] = null;
                }
            ];

            if (!empty($validateRecipientDocument[$requestPost["recipientDocumentType"]])) {
                $validateRecipientDocument[$requestPost["recipientDocumentType"]]($requestPost["recipientDocument"]);
            }

            $certificate = $requestFile["pfxFile"]["tmp_name"];
            $invoice = new SupportInvoice([
                "tpAmb" => $requestPost["environment"],
                "companyName" => $requestPost["companyName"],
                "companyDocument" => $requestPost["companyDocument"],
                "companyState" => $requestPost["companyState"],
                "certPfx" => $certificate,
                "certPassword" => $requestPost["certPassword"],
            ]);

            $requestStateCode = $invoice->requestStateCode();
            if (empty($requestStateCode)) {
                http_response_code(500);
                echo http_response_code(["error" => "erro ao consultar o código do estado"]);
                die;
            }

            $requestPost["companyState"] = array_reduce($requestStateCode, function ($acc, $item) use ($requestPost) {
                if (strtolower($item["sigla"]) == strtolower($requestPost["companyState"])) {
                    $acc = $item;
                }
                return $acc;
            }, []);

            if (empty($requestPost["companyState"])) {
                http_response_code(500);
                echo json_encode(["error" => "código do estado inexistente"]);
                die;
            }

            $invoice->isValidCertPfx();
            $dateTime = new DateTime();

            $invoice->makeInvoice()->invoiceIdentification([
                "cUF" => $requestPost["companyState"]["id"],
                "cNF" => str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                "natOp" => $requestPost["natureOperation"],
                "mod" => 55,
                "serie" => $requestPost["invoiceSeries"],
                "nNF" => $requestPost["invoiceNumber"],
                "dhEmi" => $dateTime->format("Y-m-d") . "T" . $dateTime->format("H:i:sP"),
                "dhSaiEnt" => null,
                "tpNF" => $requestPost["invoiceType"],
                "idDest" => $requestPost["idInvoiceOperation"],
                "cMunFG" => $municipalityCode,
                "tpImp" => 1,
                "tpEmis" => 1,
                "finNFe" => $requestPost["purposeOfIssuance"],
                "indFinal" => $requestPost["finalConsumer"],
                "indPres" => $requestPost["buyersPresence"]
            ])->issuerData([
                "xNome" => $requestPost["companyName"],
                "xFant" => $requestPost["fantasyName"],
                "IE" => $requestPost["stateRegistration"],
                "CNAE" => $requestPost["cnaeInformation"],
                "CRT" => $requestPost["companyTaxRegime"],
                "CNPJ" => $requestPost["companyDocument"]
            ])->issuerAddressData([
                "xLgr" => $requestPost["companyAddress"],
                "nro" => $requestPost["companyAddressNumber"],
                "xCpl" => $requestPost["companyComplement"],
                "xBairro" => $requestPost["companyNeighborhood"],
                "cMun" => $municipalityCode,
                "xMun" => $municipalityName,
                "UF" => $requestPost["companyState"]["sigla"],
                "CEP" => $requestPost["companyZipcode"],
                "cPais" => 1058,
                "xPais" => "Brasil",
                "fone" => $requestPost["companyPhone"] ?? null,
            ])->recipientData($recipientData)
                ->recipientAddressData([
                    "xLgr" => $requestPost["recipientAddress"],
                    "nro" => $requestPost["recipientAddressNumber"],
                    "xCpl" => $requestPost["recipientComplement"],
                    "xBairro" => $requestPost["recipientNeighborhood"],
                    "cMun" => $recipientMunicipalityCode,
                    "xMun" => $recipientMunicipalityName,
                    "UF" => $requestPost["recipientState"],
                    "CEP" => $requestPost["recipientZipcode"],
                    "cPais" => 1058,
                    "xPais" => "Brasil",
                    "fone" => $requestPost["recipientPhone"] ?? null
                ])->productOrServiceData([
                    "item" => $requestPost["productItem"],
                    "cProd" => $requestPost["productCode"] ?? Uuid::uuid4()->toString(),
                    "cEAN" => $requestPost["barCodeProduct"] ?? null,
                    "cBarra" => $requestPost["additionalBarCodeProduct"] ?? null,
                    "xProd" => $requestPost["productDescription"],
                    "NCM" => $requestPost["productNcmCode"],
                    "cBenef" => $requestPost["productCodeBenef"] ?? null,
                    "EXTIPI" => $requestPost["productCodeTipi"] ?? null,
                    "CFOP" => $requestPost["productCodeCfop"],
                    "uCom" => $requestPost["productComercialUnit"],
                    "qCom" => $requestPost["qttyProduct"],
                    "vUnCom" => $requestPost["productValueUnit"],
                    "vProd" => $requestPost["productTotalValue"],
                    "cEANTrib" => $requestPost["barCodeProductTrib"] ?? null,
                    "cBarraTrib" => $requestPost["additionalBarCodeProductTrib"] ?? null,
                    "uTrib" => $requestPost["productTaxUnit"],
                    "qTrib" => $requestPost["qttyProuctTax"],
                    "vUnTrib" => $requestPost["taxUnitValue"],
                    "vFrete" => $requestPost["productShippingValue"] ?? null,
                    "vSeg" => $requestPost["productInsuranceValue"] ?? null,
                    "vDesc" => $requestPost["productDiscountAmount"] ?? null,
                    "vOutro" => $requestPost["productValueOtherExpenses"] ?? null,
                    "indTot" => 1,
                    "xPed" => $requestPost["productOrderNumber"] ?? null,
                    "nItemPed" => $requestPost["productItemNumberBuyOrder"] ?? null,
                    "nFCI" => $requestPost["fciNumber"] ?? null
                ])->shippingMethod([
                    "modFrete" => $requestPost["shippingMethod"]
                ])->declareTaxData([
                    "item" => $requestPost["productItem"],
                    "vTotTrib" => $requestPost["totalTaxValue"] ?? null
                ])->paymentMethodInformation([
                    "indPag" => $requestPost["indicatorPaymentMethod"] ?? null,
                    "tPag" => $requestPost["codeMethodPayment"] ?? null,
                    "vPag" => $requestPost["paymentTotalValue"] ?? null,
                    "vTroco" => $requestPost["changeMoney"] ?? null
                ]);

            echo json_encode(["success" => "nota fiscal válida"]);
            die;
        }

        $invoice = new SupportInvoice();
        $responseMunicipality = $invoice->requestMunicipality();
        $responseMunicipality = array_reduce($responseMunicipality, function ($acc, $item) {
            $acc[] = [
                "name" => $item["nome"] . "/" .
                    $item["microrregiao"]["nome"] . "/" .
                    $item["microrregiao"]["mesorregiao"]["nome"] . "/" .
                    $item["microrregiao"]["mesorregiao"]["UF"]["nome"] . "/" .
                    $item["microrregiao"]["mesorregiao"]["UF"]["sigla"],
                "id" => $item["id"] . "-" . $item["nome"],
            ];
            return $acc;
        }, []);

        $company = new Company();
        $company->setId($responseInitializaUserAndCompany["company_id"]);
        $companyData = $company->findCompanyById();

        echo $this->view->render("admin/invoice-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/invoice/form"],
            "companyData" => $companyData,
            "responseMunicipality" => $responseMunicipality
        ]);
    }
}
