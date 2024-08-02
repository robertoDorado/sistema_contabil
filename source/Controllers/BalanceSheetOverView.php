<?php

namespace Source\Controllers;

use DateTime;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
use Source\Core\Controller;
use Source\Domain\Model\BalanceSheet;
use Source\Domain\Model\ChartOfAccount;
use Source\Domain\Model\ChartOfAccountGroup;

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
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
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

            if (empty($responseInitializeUserAndCompany["company_id"])) {
                http_response_code(500);
                echo json_encode(["error" => "selecione uma empresa antes de encerrar um período contábil"]);
                die;
            }

            $requestPost["date"] = explode("-", $requestPost["date"]);
            $requestPost["date"] = array_map(function ($date) {
                return preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", trim($date));
            }, $requestPost["date"]);

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

            $groupAccounting = [];
            foreach ($balanceSheetData as $balanceSheet) {
                if (preg_match("/receita/", strtolower($balanceSheet["account_name_group"]))) {
                    if (empty($groupAccounting[$balanceSheet["account_name_group"]])) {
                        $groupAccounting[$balanceSheet["account_name_group"]] = $balanceSheet;
                        $groupAccounting[$balanceSheet["account_name_group"]]["account_value"] = 0;
                    }
                    $groupAccounting[$balanceSheet["account_name_group"]]["account_value"] += $balanceSheet["account_value"];
                }

                if (preg_match("/custo/", strtolower($balanceSheet["account_name_group"]))) {
                    if (empty($groupAccounting[$balanceSheet["account_name_group"]])) {
                        $groupAccounting[$balanceSheet["account_name_group"]] = $balanceSheet;
                        $groupAccounting[$balanceSheet["account_name_group"]]["account_value"] = 0;
                    }
                    $groupAccounting[$balanceSheet["account_name_group"]]["account_value"] += $balanceSheet["account_value"];
                }

                if (preg_match("/(despesa|imposto|depreciacao|amortizacao)/", strtolower(removeAccets($balanceSheet["account_name_group"])))) {
                    if (empty($groupAccounting[$balanceSheet["account_name_group"]])) {
                        $groupAccounting[$balanceSheet["account_name_group"]] = $balanceSheet;
                        $groupAccounting[$balanceSheet["account_name_group"]]["account_value"] = 0;
                    }
                    $groupAccounting[$balanceSheet["account_name_group"]]["account_value"] += $balanceSheet["account_value"];
                }
            }

            $expensesAccounting = array_filter($groupAccounting, function ($item) {
                if (preg_match("/(despesa|imposto|depreciacao|amortizacao)/", strtolower(removeAccets($item["account_name_group"])))) {
                    return $item;
                }
            });

            $expensesAccountingValue = array_reduce($expensesAccounting, function($acc, $item) {
                $acc += $item["account_value"];
                return $acc;
            }, 0);

            $costAccounting = array_filter($groupAccounting, function ($item) {
                if (preg_match("/custo/", strtolower($item["account_name_group"]))) {
                    return $item;
                }
            });

            $costAccountingValue = array_reduce($costAccounting, function($acc, $item) {
                $acc += $item["account_value"];
                return $acc;
            }, 0);

            $revenueAccounting = array_filter($groupAccounting, function ($item) {
                if (preg_match("/receita/", strtolower($item["account_name_group"]))) {
                    return $item;
                }
            });

            $revenueAccountingValue = array_reduce($revenueAccounting, function($acc, $item) {
                $acc += $item["account_value"];
                return $acc;
            }, 0);
            
            $chartOfAccountParams = [
                [
                    "id",
                    "uuid",
                    "account_name",
                    "account_number"
                ],
                [
                    "account_name AS account_name_group"
                ],
                [
                    "id_user" => $responseInitializeUserAndCompany["user_data"]->id,
                    "id_company" => $responseInitializeUserAndCompany["company_id"],
                    "account_name" => "lucro",
                    "deleted" => 0
                ]
            ];

            $chartOfAccount = new ChartOfAccount();
            $profitAccounting = $chartOfAccount->findChartOfAccountLikeAccountName(...$chartOfAccountParams);

            if (empty($profitAccounting)) {
                http_response_code(500);
                echo json_encode(["error" => "conta {$chartOfAccountParams[2]['account_name']} não existe"]);
                die;
            }

            $profitAccounting = array_filter($profitAccounting, function ($item) {
                if (preg_match("/lucro acumulado/", strtolower($item->account_name))) {
                    return $item;
                }
            });

            if (empty($profitAccounting)) {
                http_response_code(500);
                echo json_encode(["error" => "não foi encontrada a conta lucro acumulado"]);
                die;
            }

            $profitAccounting = array_filter($profitAccounting, function ($item) {
                if (preg_match("/patrimonio liquido/", strtolower(removeAccets($item->account_name_group)))) {
                    return $item;
                }
            });

            if (empty($profitAccounting)) {
                http_response_code(500);
                echo json_encode(["error" => "a conta lucro acumulado não pertence ao patrimônio líquido"]);
                die;
            }

            $profitAccounting = array_shift($profitAccounting);
            $chartOfAccount = new ChartOfAccount();
            $chartOfAccountParams[2]["account_name"] = "receita";
            $salesRevenueAccounting = $chartOfAccount->findChartOfAccountLikeAccountName(...$chartOfAccountParams);

            if (empty($salesRevenueAccounting)) {
                http_response_code(500);
                echo json_encode(["error" => "conta {$chartOfAccountParams[2]['account_name']} não existe"]);
                die;
            }

            $salesRevenueAccounting = array_filter($salesRevenueAccounting, function ($item) {
                if (preg_match("/receita bruta de venda de produtos e servicos/", strtolower(removeAccets($item->account_name)))) {
                    return $item;
                }
            });

            if (empty($salesRevenueAccounting)) {
                http_response_code(500);
                echo json_encode(["error" => "não foi encontrada a conta receita bruta de venda de produtos e serviços"]);
                die;
            }

            $salesRevenueAccounting = array_filter($salesRevenueAccounting, function ($item) {
                if (preg_match("/receitas de vendas de produtos e servicos/", strtolower(removeAccets($item->account_name_group)))) {
                    return $item;
                }
            });

            if (empty($salesRevenueAccounting)) {
                http_response_code(500);
                echo json_encode(["error" => "a conta receita bruta de venda de produtos e serviços não pertence ao grupo receitas de vendas de produtos e serviços"]);
                die;
            }

            $salesRevenueAccounting = array_shift($salesRevenueAccounting);
            $chartOfAccount = new ChartOfAccount();
            $chartOfAccountParams[2]["account_name"] = "custo";
            $costOfProductsSold = $chartOfAccount->findChartOfAccountLikeAccountName(...$chartOfAccountParams);

            if (empty($costOfProductsSold)) {
                http_response_code(500);
                echo json_encode(["error" => "conta {$chartOfAccountParams[2]['account_name']} não existe"]);
                die;
            }

            $costOfProductsSold = array_filter($costOfProductsSold, function ($item) {
                if (preg_match("/custo dos produtos vendidos/", strtolower($item->account_name))) {
                    return $item;
                }
            });

            if (empty($costOfProductsSold)) {
                http_response_code(500);
                echo json_encode(["error" => "não foi encontrada custo dos produtos vendidos"]);
                die;
            }

            $costOfProductsSold = array_filter($costOfProductsSold, function ($item) {
                if (preg_match("/custo das vendas/", strtolower($item->account_name_group))) {
                    return $item;
                }
            });

            if (empty($costOfProductsSold)) {
                http_response_code(500);
                echo json_encode(["error" => "a conta custo dos produtos vendidos não pertence a conta custo das vendas"]);
                die;
            }

            $costOfProductsSold = array_shift($costOfProductsSold);
            $chartOfAccount = new ChartOfAccount();
            $chartOfAccountParams[2]["account_name"] = "despesa";
            $expensesAdminitrativeAccounting = $chartOfAccount->findChartOfAccountLikeAccountName(...$chartOfAccountParams);

            if (empty($expensesAdminitrativeAccounting)) {
                http_response_code(500);
                echo json_encode(["error" => "conta {$chartOfAccountParams[2]['account_name']} não existe"]);
                die;
            }

            $expensesAdminitrativeAccounting = array_filter($expensesAdminitrativeAccounting, function ($item) {
                if (preg_match("/despesas administrativas/", strtolower($item->account_name))) {
                    return $item;
                }
            });

            if (empty($expensesAdminitrativeAccounting)) {
                http_response_code(500);
                echo json_encode(["error" => "não foi encontrada a conta despesas administrativas"]);
                die;
            }

            $expensesAdminitrativeAccounting = array_filter($expensesAdminitrativeAccounting, function ($item) {
                if (preg_match("/despesas operacionais/", strtolower($item->account_name_group))) {
                    return $item;
                }
            });

            if (empty($expensesAdminitrativeAccounting)) {
                http_response_code(500);
                echo json_encode(["error" => "a conta despesas administrativas não pertence ao grupo de contas despesas operacionais"]);
                die;
            }
            
            $expensesAdminitrativeAccounting = array_shift($expensesAdminitrativeAccounting);
            $balanceSheet = new BalanceSheet();
            $dateTime = new DateTime($requestPost["date"][1]);
            $balanceSheetParams = [
                "uuid" => Uuid::uuid4(),
                "id_user" => $responseInitializeUserAndCompany["user"],
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "account_type" => 0,
                "id_chart_of_account" => $salesRevenueAccounting->id,
                "account_value" => $revenueAccountingValue,
                "history_account" => "encerramento contábil no mês de " . monthsInPortuguese()[$dateTime->format("n")] . "/" . $dateTime->format("Y") . "",
                "created_at" => $requestPost["date"][1],
                "updated_at" => $requestPost["date"][1],
                "deleted" => 0
            ];

            Connect::getInstance()->beginTransaction();
            $response = $balanceSheet->persistData($balanceSheetParams);
            if (!$response) {
                http_response_code(500);
                echo json_encode([
                    "error" => "erro ao tentar realizar o encerramento contábil para a conta D - receita de vendas"
                ]);
                Connect::getInstance()->rollBack();
                die;
            }

            $balanceSheetParams["uuid"] = Uuid::uuid4();
            $balanceSheetParams["account_type"] = 1;
            $balanceSheetParams["id_chart_of_account"] = $profitAccounting->id;
            $response = $balanceSheet->persistData($balanceSheetParams);
            if (!$response) {
                http_response_code(500);
                echo json_encode([
                    "error" => "erro ao tentar realizar o encerramento contábil para a conta C - lucro acumulado"
                ]);
                Connect::getInstance()->rollBack();
                die;
            }

            if (!empty($costAccountingValue)) {
                $balanceSheetParams["uuid"] = Uuid::uuid4();
                $balanceSheetParams["account_type"] = 0;
                $balanceSheetParams["account_value"] = $costAccountingValue;
                $response = $balanceSheet->persistData($balanceSheetParams);
                if (!$response) {
                    http_response_code(500);
                    echo json_encode([
                        "error" => "erro ao tentar realizar o encerramento contábil para a conta D - lucro acumulado"
                    ]);
                    Connect::getInstance()->rollBack();
                    die;
                }

                $balanceSheetParams["uuid"] = Uuid::uuid4();
                $balanceSheetParams["account_type"] = 1;
                $balanceSheetParams["id_chart_of_account"] = $costOfProductsSold->id;
                $response = $balanceSheet->persistData($balanceSheetParams);
                if (!$response) {
                    http_response_code(500);
                    echo json_encode([
                        "error" => "erro ao tentar realizar o encerramento contábil para a conta C - custo dos produtos vendidos"
                    ]);
                    Connect::getInstance()->rollBack();
                    die;
                }
            }

            if (!empty($expensesAccountingValue)) {
                $balanceSheetParams["uuid"] = Uuid::uuid4();
                $balanceSheetParams["account_type"] = 0;
                $balanceSheetParams["account_value"] = $expensesAccountingValue;
                $balanceSheetParams["id_chart_of_account"] = $profitAccounting->id;
                $response = $balanceSheet->persistData($balanceSheetParams);
                if (!$response) {
                    http_response_code(500);
                    echo json_encode([
                        "error" => "erro ao tentar realizar o encerramento contábil para a conta D - lucro acumulado"
                    ]);
                    Connect::getInstance()->rollBack();
                    die;
                }
    
                $balanceSheetParams["uuid"] = Uuid::uuid4();
                $balanceSheetParams["account_type"] = 1;
                $balanceSheetParams["id_chart_of_account"] = $expensesAdminitrativeAccounting->id;
                $response = $balanceSheet->persistData($balanceSheetParams);
                if (!$response) {
                    http_response_code(500);
                    echo json_encode([
                        "error" => "erro ao tentar realizar o encerramento contábil para a conta C - despesas administrativas"
                    ]);
                    Connect::getInstance()->rollBack();
                    die;
                }
            }
            Connect::getInstance()->commit();
            $calculationAccounting = $revenueAccountingValue - ($costAccountingValue + $expensesAccountingValue);
            $profitAccounting->created_at = preg_replace("/^(\d{4})-(\d{2})-(\d{2})$/", "$3/$2/$1", $requestPost["date"][1]);
            $profitAccounting->uuid = $profitAccounting->getUuid();
            $profitAccounting->account_value_formated = "R$ " . number_format($calculationAccounting, 2, ",", ".");
            $profitAccounting->account_value = $calculationAccounting;

            echo json_encode(
                [
                    "success" => "apuração realizada com sucesso",
                    "profit_accounting" => (array) $profitAccounting->data()
                ]
            );
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
