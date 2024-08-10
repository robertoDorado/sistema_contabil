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

            if ($requestPost["date"] == "null") {
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
            $balanceSheetData = $balanceSheet->findAllBalanceSheetJoinChartOfAccountAndJoinChartOfAccountGroup(
                [
                    "account_value",
                    "deleted"
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
                    "date" => [
                        "date_ini" => $requestPost["date"][0],
                        "date_end" => $requestPost["date"][1]
                    ]
                ]
            );

            if (!empty($balanceSheetData)) {
                $balanceSheetData = array_filter($balanceSheetData, function ($item) {
                    if (empty($item->getDeleted())) {
                        return $item;
                    }
                });

                $balanceSheetData = array_map(function ($item) {
                    return (array) $item->data();
                }, $balanceSheetData);
            }

            $grouppingBalanceSheetDataByAccountNameGroup = function (array $validateData) use ($balanceSheetData) {
                return array_reduce($balanceSheetData, function ($acc, $item) use ($validateData) {
                    foreach ($validateData as $account) {
                        if (preg_match("/({$account})/", strtolower(removeAccets($item["account_name_group"])))) {
                            if (empty($acc[$item["account_name_group"]])) {
                                $acc[$item["account_name_group"]] = $item;
                                $acc[$item["account_name_group"]]["account_value"] = 0;
                            }
                            $acc[$item["account_name_group"]]["account_value"] += $item["account_value"];
                        }
                    }
                    return $acc;
                }, []);
            };

            $groupAccounting = $grouppingBalanceSheetDataByAccountNameGroup(
                [
                    "receita", 
                    "custo",
                    "despesa|depreciacao|amortizacao|imposto"
                ]
            );

            $calculateGrupValue = function (array $validateData) use ($groupAccounting): int {
                $data = array_filter($groupAccounting, function ($item) use ($validateData) {
                    foreach ($validateData as $account) {
                        if (preg_match("/{$account}/", strtolower(removeAccets($item["account_name_group"])))) {
                            return $item;
                        }
                    }
                });

                return array_reduce($data, function ($acc, $item) {
                    $acc += $item["account_value"];
                    return $acc;
                }, 0);
            };

            $costAccountingValue = $calculateGrupValue(["custo"]);
            $revenueAccountingValue = $calculateGrupValue(["receita"]);
            $expensesAccountingValue = $calculateGrupValue([
                "despesa",
                "depreciacao",
                "amortizacao",
                "imposto"
            ]);


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
                    "deleted" => 0
                ]
            ];

            $findChartOfAccountData = function (string $accountName, string $accountNameGroup) use ($chartOfAccountParams) {
                $chartOfAccount = new ChartOfAccount();
                $chartOfAccountParams[2]["account_name"] = $accountName;
                $data = $chartOfAccount->findChartOfAccountLikeAccountName(...$chartOfAccountParams);

                if (empty($data)) {
                    http_response_code(500);
                    echo json_encode(["error" => "conta {$chartOfAccountParams[2]['account_name']} não existe"]);
                    die;
                }

                $data = array_filter($data, function ($item) use ($accountName) {
                    if (preg_match("/{$accountName}/", strtolower(removeaccets($item->account_name)))) {
                        return $item;
                    }
                });

                if (empty($data)) {
                    http_response_code(500);
                    echo json_encode(["error" => "não foi encontrada a conta {$accountName}"]);
                    die;
                }

                $data = array_filter($data, function ($item) use ($accountNameGroup) {
                    if (preg_match("/{$accountNameGroup}/", strtolower(removeAccets($item->account_name_group)))) {
                        return $item;
                    }
                });
    
                if (empty($data)) {
                    http_response_code(500);
                    echo json_encode(["error" => "a conta {$accountName} não pertence ao {$accountNameGroup}"]);
                    die;
                }
    
                return array_shift($data);
            };

            $profitAccounting = $findChartOfAccountData("lucro acumulado", "patrimonio liquido");
            $salesRevenueAccounting = $findChartOfAccountData("receita", "receitas de vendas de produtos e servicos");
            $costOfProductsSold = $findChartOfAccountData("custo", "custo das vendas");
            $expensesAdminitrativeAccounting = $findChartOfAccountData("despesa", "despesas operacionais");

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

            $persistCloseAccounting = function (array $balanceSheetParams) {
                $balanceSheet = new BalanceSheet();
                $response = $balanceSheet->persistData($balanceSheetParams);
                $accountType = empty($balanceSheetParams["account_type"]) ? "Débito" : "Crédito";
                
                $chartOfAccount = new ChartOfAccount();
                $chartOfAccountData = $chartOfAccount->findChartOfAccountById(["account_name"], $balanceSheetParams["id_chart_of_account"]);

                if (!$response) {
                    http_response_code(500);
                    echo json_encode([
                        "error" => "erro ao tentar realizar o encerramento contábil para a conta {$accountType} - {$chartOfAccountData->account_name}"
                    ]);
                    Connect::getInstance()->rollBack();
                    die;
                }
            };
            
            Connect::getInstance()->beginTransaction();
            if (!empty($revenueAccountingValue)) {
                // D - Receita
                $persistCloseAccounting($balanceSheetParams);
    
                $balanceSheetParams["uuid"] = Uuid::uuid4();
                $balanceSheetParams["account_type"] = 1;
                $balanceSheetParams["id_chart_of_account"] = $profitAccounting->id;
    
                // C - Lucro acumulado
                $persistCloseAccounting($balanceSheetParams);
            }
            
            if (!empty($costAccountingValue)) {
                $balanceSheetParams["uuid"] = Uuid::uuid4();
                $balanceSheetParams["account_type"] = 0;
                $balanceSheetParams["account_value"] = $costAccountingValue;
                $balanceSheetParams["id_chart_of_account"] = $profitAccounting->id;

                // D - Lucro cumulado
                $persistCloseAccounting($balanceSheetParams);
                
                $balanceSheetParams["uuid"] = Uuid::uuid4();
                $balanceSheetParams["account_type"] = 1;
                $balanceSheetParams["id_chart_of_account"] = $costOfProductsSold->id;

                // C - CMV
                $persistCloseAccounting($balanceSheetParams);
            }

            if (!empty($expensesAccountingValue)) {
                $balanceSheetParams["uuid"] = Uuid::uuid4();
                $balanceSheetParams["account_type"] = 0;
                $balanceSheetParams["account_value"] = $expensesAccountingValue;
                $balanceSheetParams["id_chart_of_account"] = $profitAccounting->id;

                // D - Lucro acumulado
                $persistCloseAccounting($balanceSheetParams);
                
                $balanceSheetParams["uuid"] = Uuid::uuid4();
                $balanceSheetParams["account_type"] = 1;
                $balanceSheetParams["id_chart_of_account"] = $expensesAdminitrativeAccounting->id;

                // C - Despesas administrativas
                $persistCloseAccounting($balanceSheetParams);
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
