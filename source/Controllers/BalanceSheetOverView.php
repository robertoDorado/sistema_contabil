<?php

namespace Source\Controllers;

use DateTime;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\BalanceSheet;
use Source\Domain\Model\ChartOfAccount;

/**
 * BalanceSheetOverview Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class BalanceSheetOverView extends Controller
{
    /**
     * BalanceSheetOverview constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function balanceSheetReport()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $dateTime = new DateTime();
        $dateRange = !$this->getRequests()->has("daterange") ?
            $dateTime->format("01/m/Y") . " - " . $dateTime->format("t/m/Y") : $this->getRequests()->get("daterange");

        $dateRange = explode("-", $dateRange);
        $dateRange = array_map(function ($date) {
            return preg_replace("/^(\d{2})\/(\d{2})\/(\d{4})$/", "$3-$2-$1", trim($date));
        }, $dateRange);

        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(["closeAccounting", "date"])->getAllPostData();

            if (!$requestPost["closeAccounting"]) {
                http_response_code(500);
                echo json_encode(["error" => "erro ao tentar encerrar o período contábil"]);
                die;
            }

            if (empty($requestPost["date"])) {
                $dateTime = new DateTime();
                $requestPost["date"] = $dateTime->format("01/m/Y") . " - " . $dateTime->format("t/m/Y");
            }

            $requestPost["date"] = preg_replace("/^(\d{2})\/(\d{2})\/(\d{4})$/", "$3-$2-$1", trim($requestPost["date"]));
            $requestPost["date"] = explode("-", $requestPost["date"]);

            $balanceSheet = new BalanceSheet();
            $balanceSheetData = $balanceSheet->findAllBalanceSheet(
                [
                    "account_value"
                ],
                [
                    "account_name"
                ],
                [
                    "account_name AS account_name_group"
                ],
                [
                    "id_user" => $responseInitializeUserAndCompany["user_data"]->id,
                    "id_company" => $responseInitializeUserAndCompany["company_id"],
                    "deleted" => 0,
                    "date" => [
                        "date_ini" => $requestPost["date"][0],
                        "date_end" => $requestPost["date"][1]
                    ]
                ],
                true
            );
            
            echo json_encode(["success" => "apuração realizada com sucesso"]);
            die;
        }

        $params = [
            [
                "uuid",
                "account_type",
                "account_value",
                "history_account",
                "created_at"
            ],
            [
                "account_number",
                "account_name"
            ],
            [
                "account_name AS account_name_group"
            ],
            [
                "id_user" => $responseInitializeUserAndCompany["user_data"]->id,
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "deleted" => 0,
                "date" => [
                    "date_ini" => $dateRange[0],
                    "date_end" => $dateRange[1]
                ]
            ]
        ];

        $balanceSheet = new BalanceSheet();
        $currentAssets = $balanceSheet->findAllCurrentAssets(...$params);

        $balanceSheet = new BalanceSheet();
        $nonCurrentAssets = $balanceSheet->findAllNonCurrentAssets(...$params);

        $balanceSheet = new BalanceSheet();
        $currentLiabilities = $balanceSheet->findAllCurrentLiabilities(...$params);

        $balanceSheet = new BalanceSheet();
        $nonCurrentLiabilities = $balanceSheet->findAllNonCurrentLiabilities(...$params);

        $balanceSheet = new BalanceSheet();
        $shareholdersEquity = $balanceSheet->findAllShareholdersEquity(...$params);

        $accounttingCaculationAssets = $currentAssets["total"] + $nonCurrentAssets["total"];
        $accountingCalculationLiabilities = $currentLiabilities["total"] + $nonCurrentLiabilities["total"] + $shareholdersEquity["total"];

        echo $this->view->render("admin/balance-sheet-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/balnace-sheet-overview/report"],
            "currentAssetsData" => $currentAssets["data"],
            "nonCurrentAssetsData" => $nonCurrentAssets["data"],
            "currentLiabilitiesData" => $currentLiabilities["data"],
            "nonCurrentLiabilitiesData" => $nonCurrentLiabilities["data"],
            "shareholdersEquityData" => $shareholdersEquity["data"],
            "totalCurrentAssets" => "R$ " . number_format($currentAssets["total"], 2, ",", "."),
            "totalNonCurrentAssets" => "R$ " . number_format($nonCurrentAssets["total"], 2, ",", "."),
            "totalCurrentLiabilities" => "R$ " . number_format($currentLiabilities["total"], 2, ",", "."),
            "totalNonCurrentLiabilities" => "R$ " . number_format($nonCurrentLiabilities["total"], 2, ",", "."),
            "totalShareholdersEquity" => "R$ " . number_format($shareholdersEquity["total"], 2, ",", "."),
            "accounttingCaculationAssets" => $accounttingCaculationAssets,
            "accountingCalculationLiabilities" => $accountingCalculationLiabilities
        ]);
    }

    public function balanceSheetForm()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "accountType",
                    "accountHistory",
                    "accountValue",
                    "createdAt",
                    "chartOfAccountSelect",
                    "csrfToken"
                ]
            )->getAllPostData();


            $errorMessage = function (string $message) {
                http_response_code(500);
                echo json_encode(["error" => $message]);
                die;
            };

            if (empty($responseInitializeUserAndCompany["company_id"])) {
                $errorMessage("selecione uma empresa antes de fazer um lançamento");
            }

            $accountTypeMessage = "tipo de conta inválida";
            if (!preg_match("/^\d{1}$/", $requestPost["accountType"])) {
                $errorMessage($accountTypeMessage);
            }

            if ($requestPost["accountType"] > 1) {
                $errorMessage($accountTypeMessage);
            }

            if (strlen($requestPost["accountHistory"]) > 1000) {
                $errorMessage("limite de caracteres ultrapassado");
            }

            $requestPost["accountValue"] = convertCurrencyRealToFloat($requestPost["accountValue"]);
            $requestPost["createdAt"] = preg_replace("/^(\d{2})\/(\d{2})\/(\d{4})$/", "$3-$2-$1", $requestPost["createdAt"]);

            $chartOfAccount = new ChartOfAccount();
            $chartOfAccount->setUuid($requestPost["chartOfAccountSelect"]);
            $chartOfAccountData = $chartOfAccount->findChartOfAccountByUuid(["id"]);

            if (empty($chartOfAccountData)) {
                $errorMessage("plano de contas inexistente");
            }

            $balanceSheet = new BalanceSheet();
            $response = $balanceSheet->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "id_user" => $responseInitializeUserAndCompany["user"],
                "id_chart_of_account" => $chartOfAccountData->id,
                "account_type" => $requestPost["accountType"],
                "account_value" => $requestPost["accountValue"],
                "history_account" => $requestPost["accountHistory"],
                "created_at" => $requestPost["createdAt"],
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (!$response) {
                $errorMessage($balanceSheet->message->json());
            }

            echo json_encode(["success" => "registro criado com sucesso"]);
            die;
        }

        $chartOfAccount = new ChartOfAccount();
        $chartOfAccountData = $chartOfAccount->findAllChartOfAccount(
            [
                "uuid",
                "account_name",
                "account_number"
            ],
            [
                "id_user" => $responseInitializeUserAndCompany["user_data"]->id,
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "deleted" => 0
            ]
        );

        echo $this->view->render("admin/balance-sheet-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/balance-sheet-overview/form"],
            "chartOfAccountData" => $chartOfAccountData
        ]);
    }
}
