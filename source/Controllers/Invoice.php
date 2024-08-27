<?php

namespace Source\Controllers;

use DateTime;
use Exception;
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
                    "companyName",
                    "companyDocument",
                    "stateRegistration",
                    "companyZipcode",
                    "companyAddress",
                    "companyAddressNumber",
                    "companyNeighborhood",
                    "companyState",
                    "natureOperation",
                    "invoiceNumber",
                    "invoiceSeries",
                    "certPassword",
                    "environment",
                    "invoiceType",
                    "idInvoiceOperation",
                    "municipalityInvoice",
                    "purposeOfIssuance",
                    "finalConsumer",
                    "buyersPresence",
                    "companyTaxRegime",
                    "fantasyName",
                    "companyComplement",
                    "recipientName",
                    "recipientStateRegistrationIndicator",
                    "recipientEmail",
                    "recipientDocumentType",
                    "recipientDocument"
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

            if (!empty($requestPost["cnaeInformation"])) {
                if (!preg_match("/^\d+$/", $requestPost["cnaeInformation"])) {
                    $message(["error" => "campo CNAE inválido"], 500);
                    die;
                }
            }

            $requestPost["municipalityInvoice"] = explode("-", $requestPost["municipalityInvoice"]);
            $municipalityCode = $requestPost["municipalityInvoice"][0];
            $municipalityName = $requestPost["municipalityInvoice"][1];

            $requestPost["companyDocument"] = preg_replace("/[^\d]+/", "", $requestPost["companyDocument"]);
            $requestPost["recipientDocument"] = preg_replace("/[^\d]+/", "", $requestPost["recipientDocument"]);
            $requestPost["recipientStateRegistration"] = preg_replace("/[^\d]+/", "", $requestPost["recipientStateRegistration"]);
            $requestPost["stateRegistration"] = preg_replace("/[^\d]+/", "", $requestPost["stateRegistration"]);

            $recipientAddress = [
                "xNome" => $requestPost["recipientName"],
                "indIEDest" => $requestPost["recipientStateRegistrationIndicator"],
                "IE" => $requestPost["recipientStateRegistration"],
                "ISUF" => null,
                "IM" => null,
                "email" => $requestPost["recipientEmail"],
                "idEstrangeiro" => null
            ];

            $validateRecipientDocument = [
                "1" => function(string $value) use (&$recipientAddress) {
                    $recipientAddress["CPF"] = $value;
                    $recipientAddress["CNPJ"] = null;
                },
                "2" => function(string $value) use (&$recipientAddress) {
                    $recipientAddress["CNPJ"] = $value;
                    $recipientAddress["CPF"] = null;
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
            ])->recipientAddress($recipientAddress);

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
