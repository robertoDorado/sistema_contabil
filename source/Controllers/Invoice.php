<?php

namespace Source\Controllers;

use DateTime;
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
                    "companyCity",
                    "companyState",
                    "natureOperation",
                    "invoiceNumber",
                    "invoiceSeries",
                    "certPassword",
                    "environment",
                    "invoiceType",
                    "idInvoiceOperation",
                    "municipalityInvoice"
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

            if (!preg_match("/^\d+$/", $requestPost["municipalityInvoice"])) {
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

            $requestPost["companyDocument"] = preg_replace("/[^\d]+/", "", $requestPost["companyDocument"]);
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
                if ($item["sigla"] == $requestPost["companyState"]) {
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
                "cMunFG" => $requestPost["municipalityInvoice"]
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
                "id" => $item["id"],
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
