<?php

namespace Source\Controllers;

use DateTime;
use Exception;
use NFePHP\DA\NFe\Danfe;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Core\Model;
use Source\Domain\Model\Company;
use Source\Domain\Model\Invoice as ModelInvoice;
use Source\Support\Invoice as SupportInvoice;
use Source\Support\Message;

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

    public function invoiceBackup()
    {
        $responseInitializaUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields([
                "uuid",
                "action"
            ])->getAllPostData();

            $invoice = new ModelInvoice();
            $invoice->setUuid($requestPost["uuid"]);
            $invoiceData = $invoice->findInvoiceByUuid(["id", "deleted"]);

            if (empty($invoiceData)) {
                http_response_code(400);
                echo json_encode(["error" => "registro não encontrado"]);
                die;
            }

            $invoiceData->setRequiredFields(["deleted"]);
            $response = new \stdClass();
            $response->message = new Message();
            $response->success = true;

            $validateAction = [
                "restore" => function(Model $model) use (&$response) {
                    $model->deleted = 0;
                    $response->message->success("registro restaurado com sucesso");
                    if (!$model->save()) {
                        $response->message->error("erro ao tentar restaurar o registro");
                        $response->success = false;
                    }
                },

                "delete" => function(Model $model) use (&$response) {
                    $response->message->success("registro excluido com sucesso");
                    if (!$model->destroy()) {
                        $response->message->error("erro ao tentar excluir o registro");
                        $response->success = false;
                    }
                }
            ];

            if (!empty($validateAction[$requestPost["action"]])) {
                $validateAction[$requestPost["action"]]($invoiceData);
            }

            if (!$response->success) {
                http_response_code(400);
                echo $response->message->json();
                die;
            }

            echo $response->message->json();
            die;
        }

        $modelInvoice = new ModelInvoice();
        $invoiceReportData = $modelInvoice->findAllInvoiceJoinCompany(
            [
                "id_user" => $responseInitializaUserAndCompany["user_data"]->id,
                "id_company" => $responseInitializaUserAndCompany["company_id"]
            ],
            [
                "xml",
                "protocol_number",
                "access_key",
                "created_at",
                "uuid",
                "deleted"
            ],
            [
                "company_name"
            ]
        );

        $invoiceReportData = array_filter($invoiceReportData, function ($item) {
            return !empty($item->getDeleted());
        });

        $invoiceReportData = array_map(function ($item) {
            $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
            return $item;
        }, $invoiceReportData);

        echo $this->view->render("admin/invoice-backup", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/invoice/backup"],
            "invoiceReportData" => $invoiceReportData
        ]);
    }

    public function invoiceRemove()
    {
        $uuid = $this->getRequests()->getPost("uuid");
        $invoice = new ModelInvoice();
        $invoice->setUuid($uuid);
        $invoiceData = $invoice->findInvoiceByUuid(["deleted", "id"]);

        if (empty($invoiceData)) {
            http_response_code(400);
            echo json_encode(["error" => "registro não encontrado"]);
            die;
        }

        $invoiceData->setRequiredFields(["deleted"]);
        $invoiceData->deleted = 1;
        if (!$invoiceData->save()) {
            http_response_code(400);
            echo json_encode(["error" => "erro ao tentar excluir o registro"]);
            die;
        }

        echo json_encode(["success" => "nota excluida com sucesso"]);
    }

    public function invoiceCancelNfe(array $data)
    {
        $responseInitializaUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields([
                'reasonOfCancellation',
                'csrfToken',
                'certPassword',
                'environment',
                'uuid'
            ])->getAllPostData();
            $requestFile = $this->getRequestFiles()->getAllFiles();

            if (empty($responseInitializaUserAndCompany["company_id"])) {
                http_response_code(400);
                echo json_encode(["error" => "selecione uma empresa antes de cancelar uma nota fiscal"]);
                die;
            }

            $validateEnvironment = ["1", "2"];
            if (!in_array($requestPost["environment"], $validateEnvironment)) {
                http_response_code(400);
                echo json_encode(["error" => "ambiente inválido"]);
                die;
            }

            $company = new Company();
            $company->setId($responseInitializaUserAndCompany["company_id"]);
            $companyData = $company->findCompanyById(["company_name", "company_document", "company_state"]);

            if (empty($companyData)) {
                http_response_code(400);
                echo json_encode(["error" => "empresa não encontrada"]);
                die;
            }

            $companyData->company_document = preg_replace("/[^\d]+/", "", $companyData->company_document);
            $certificate = $requestFile["pfxFile"]["tmp_name"];

            $invoiceModel = new ModelInvoice();
            $invoiceModel->setUuid($requestPost["uuid"]);
            $invoiceData = $invoiceModel->findInvoiceByUuid(["protocol_number", "access_key"]);

            if (empty($invoiceData)) {
                http_response_code(400);
                echo json_encode(["error" => "nfe não encontrada"]);
                die;
            }

            try {
                $invoice = new SupportInvoice([
                    "tpAmb" => $requestPost["environment"],
                    "companyName" => $companyData->company_name,
                    "companyDocument" => $companyData->company_document,
                    "companyState" => $companyData->company_state,
                    "certPfx" => $certificate,
                    "certPassword" => $requestPost["certPassword"],
                ]);

                $invoice->isValidCertPfx();
                $response = $invoice->cancelInvoice($invoiceData->access_key, $requestPost["reasonOfCancellation"], $invoiceData->protocol_number);
                echo json_encode($response);
            } catch (\Exception $th) {
                http_response_code(400);
                echo json_encode(["error" => $th->getMessage()]);
            }
            die;
        }

        if (!Uuid::isValid($data["uuid"])) {
            redirect("/admin/invoice/report");
        }

        echo $this->view->render("admin/invoice-cancel", [
            "userFullName" => showUserFullName(),
            "endpoints" => []
        ]);
    }

    public function invoiceEmissionDanfe()
    {
        $uuidData = $this->getRequests()->getPost("uuid");
        $invoice = new ModelInvoice();

        $invoice->setUuid($uuidData);
        $invoiceData = $invoice->findInvoiceByUuid(["xml"]);

        if (empty($invoiceData)) {
            throw new Exception("a emissão da danfe falhou - este registro não existe");
        }

        $danfe = new Danfe($invoiceData->getXml());
        $danfe->debugMode(false);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="danfe-' . $uuidData . '.pdf"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        echo $danfe->render();
    }

    public function invoiceReport()
    {
        $responseInitializaUserAndCompany = initializeUserAndCompanyId();
        $params = [
            [
                "id_user" => $responseInitializaUserAndCompany["user_data"]->id,
                "id_company" => $responseInitializaUserAndCompany["company_id"]
            ],
            [
                "xml",
                "protocol_number",
                "access_key",
                "created_at",
                "uuid",
                "deleted"
            ],
            [
                "company_name"
            ]
        ];

        $dateRange = $this->getRequests()->get("daterange");
        if (!empty($dateRange)) {
            $dates = explode("-", $dateRange);
            $dates = array_map(function ($value) {
                return preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $value);
            }, $dates);

            $params[0]["date"] = [
                "date_ini" => $dates[0],
                "date_end" => $dates[1]
            ];
        }

        $modelInvoice = new ModelInvoice();
        $invoiceReportData = $modelInvoice->findAllInvoiceJoinCompany(...$params);

        $invoiceReportData = array_filter($invoiceReportData, function ($item) {
            return empty($item->getDeleted());
        });

        $invoiceReportData = array_map(function ($item) {
            $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
            return $item;
        }, $invoiceReportData);

        echo $this->view->render("admin/invoice-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/invoice/report"],
            "invoiceReportData" => $invoiceReportData
        ]);
    }

    public function invoiceForm()
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
                    'productItem',
                    'productCode',
                    'productDescription',
                    'productComercialUnit',
                    'qttyProduct',
                    'productValueUnit',
                    'productTotalValue',
                    'productTaxUnit',
                    'qttyProuctTax',
                    'taxUnitValue',
                    'productNcmCode',
                    'productCodeCfop',
                    'shippingMethod',
                    'productOrigin',
                    'productIcmsSituation',
                    'typePaymentMethod',
                    'paymentValue'
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

            if (!empty($requestPost["shippingMethod"])) {
                $validateShippingMethod = ['0', '1', '2', '3', '4', '9'];
                if (!in_array($requestPost["shippingMethod"], $validateShippingMethod)) {
                    $message(["error" => "modalidade de frete inválido"], 500);
                    die;
                }
            }

            if (!empty($requestPost["codeMethodPayment"])) {
                $validateCodeMethodPayment = ["01", "02", "03", "04", "05", "10", "11", "12", "13", "15", "90", "99"];
                if (!in_array($requestPost["codeMethodPayment"], $validateCodeMethodPayment)) {
                    $message(["error" => "código da forma de pagamento inválido"], 500);
                    die;
                }
            }

            $validateProductOrigin = ['0', '1', '2'];
            if (!in_array($requestPost["productOrigin"], $validateProductOrigin)) {
                $message(["error" => "origem do produto inválido"], 500);
                die;
            }

            if (!empty($requestPost["determiningIcmsCalc"])) {
                $validateDeterminingIcmsCalc = ["0", "1", "2", "3"];
                if (!in_array($requestPost["determiningIcmsCalc"], $validateDeterminingIcmsCalc)) {
                    $message(["error" => "determinação do cálculo do icms inválido"], 500);
                    die;
                }
            }

            $validateProductIcmsSituation = [
                "00",
                "10",
                "20",
                "30",
                "40",
                "41",
                "50",
                "51",
                "60",
                "70",
                "90",
                "101",
                "102",
                "103",
                "201",
                "202",
                "203",
                "300",
                "400",
                "500",
                "900"
            ];

            if (!in_array($requestPost["productIcmsSituation"], $validateProductIcmsSituation)) {
                $message(["error" => "código da situação tributária icms inválido"], 500);
                die;
            }

            if (!empty($requestPost["paymentMethodIndicator"])) {
                $validatePaymentMethodIndicator = ["0", "1"];
                if (!in_array($requestPost["paymentMethodIndicator"], $validatePaymentMethodIndicator)) {
                    $message(["error" => "indicador do método de pagamento inválido"], 500);
                    die;
                }
            }

            $validateTypePaymentMethod = ["01", "02", "03", "04"];
            if (!in_array($requestPost["typePaymentMethod"], $validateTypePaymentMethod)) {
                $message(["error" => "tipo do método de pagamento inválido"], 500);
                die;
            }

            if (!empty($requestPost["cardOperatorFlag"])) {
                $validateCardOperatorFlag = ["01", "02", "03", "04"];
                if (!in_array($requestPost["cardOperatorFlag"], $validateCardOperatorFlag)) {
                    $message(["error" => "bandeira da operadora inválida"], 500);
                    die;
                }
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
                "documentOfPaymentInstitution",
                "companyZipcode",
                "recipientZipcode"
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
                "changeMoney",
                "calculationBaseValue",
                "icmsRate",
                "icmsValue",
                "paymentValue"
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

            try {
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
                    http_response_code(400);
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
                    http_response_code(400);
                    echo json_encode(["error" => "código do estado inexistente"]);
                    die;
                }

                $invoice->isValidCertPfx();
                $dateTime = new DateTime();

                $xmlSigned = $invoice->makeInvoice()->invoiceIdentification([
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
                        "cProd" => $requestPost["productCode"],
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
                    ])->icmsInformation([
                        "item" => $requestPost["productItem"],
                        "orig" => $requestPost["productOrigin"],
                        "CST" => $requestPost["productIcmsSituation"],
                        "modBC" => $requestPost["determiningIcmsCalc"] ?? null,
                        "vBC" => $requestPost["calculationBaseValue"] ?? null,
                        "pICMS" => $requestPost["icmsRate"] ?? null,
                        "vICMS" => $requestPost["icmsValue"] ?? null,
                        "pFCP" => null,
                        "vFCP" => null,
                        "vBCFCP" => null,
                        "modBCST" => null,
                        "pMVAST" => null,
                        "pRedBCST" => null,
                        "vBCST" => null,
                        "pICMSST" => null,
                        "vICMSST" => null,
                        "vBCFCPST" => null,
                        "pFCPST" => null,
                        "vFCPST" => null,
                        "vICMSDeson" => null,
                        "motDesICMS" => null,
                        "pRedBC" => null,
                        "vICMSOp" => null,
                        "pDif" => null,
                        "vICMSDif" => null,
                        "vBCSTRet" => null,
                        "pST" => null,
                        "vICMSSTRet" => null,
                        "vBCFCPSTRet" => null,
                        "pFCPSTRet" => null,
                        "vFCPSTRet" => null,
                        "pRedBCEfet" => null,
                        "vBCEfet" => null,
                        "pICMSEfet" => null,
                        "vICMSEfet" => null,
                        "vICMSSubstituto" => null,
                        "vICMSSTDeson" => null,
                        "motDesICMSST" => null,
                        "pFCPDif" => null,
                        "vFCPDif" => null,
                        "vFCPEfet" => null,
                        "pRedAdRem" => null,
                        "qBCMono" => null,
                        "adRemiICMS" => null,
                        "vICMSMono" => null,
                        "adRemICMSRet" => null,
                        "vICMSMonoRet" => null,
                        "vICMSMonoDif" => null,
                        "cBenefRBC" => null,
                        "indDeduzDeson" => null
                    ])->paymentDetails([
                        "indPag" => $requestPost["paymentMethodIndicator"] ?? null,
                        "tPag" => $requestPost["typePaymentMethod"],
                        "vPag" => $requestPost["paymentValue"],
                        "CNPJ" => $requestPost["documentOfPaymentInstitution"] ?? null,
                        "tBand" => $requestPost["cardOperatorFlag"] ?? null,
                        "cAut" => null,
                        "tpIntegra" => null,
                        "CNPJPag" => null,
                        "UFPag" => null,
                        "CNPJReceb" => null,
                        "idTermPag" => null
                    ])->sendNfeToSefaz();

                $modelInvoice = new ModelInvoice();
                $response = $modelInvoice->persistData([
                    "uuid" => Uuid::uuid4(),
                    "id_user" => $responseInitializaUserAndCompany["user"],
                    "id_company" => $responseInitializaUserAndCompany["company_id"],
                    "xml" => $xmlSigned["xml"],
                    "protocol_number" => $xmlSigned["protocol_number"],
                    "access_key" => $xmlSigned["access_key"],
                    "created_at" => date("Y-m-d"),
                    "updated_at" => date("Y-m-d"),
                    "deleted" => 0
                ]);

                if (empty($response)) {
                    http_response_code(400);
                    echo $modelInvoice->message->json();
                    die;
                }

                echo json_encode(["success" => "nota fiscal emitida com sucesso"]);
            } catch (\Exception $th) {
                http_response_code(400);
                $jsonErrors = !empty($invoice->getMake()) ? $invoice->getMake()->getErrors() : [];
                $jsonErrors = json_encode($jsonErrors);
                echo json_encode(["error" => $th->getMessage() . " " . $jsonErrors]);
            }
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
