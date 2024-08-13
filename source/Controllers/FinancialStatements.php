<?php

namespace Source\Controllers;

use DateTime;
use Source\Core\Controller;
use Source\Domain\Model\BalanceSheet;
use Source\Domain\Model\ChartOfAccount;

/**
 * FinancialStatements Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class FinancialStatements extends Controller
{
    /**
     * FinancialStatements constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function statementOfValueAdded()
    {
        $responseCompanyAndUser = initializeUserAndCompanyId();
        $balanceSheet = new BalanceSheet();
        $statementOfValueAdded = $balanceSheet->findAllBalanceSheetJoinChartOfAccountAndJoinChartOfAccountGroup(
            [
                "account_value"
            ],
            [
                "account_name"
            ],
            [
                "id"
            ],
            [
                "id_user" => $responseCompanyAndUser["user_data"]->id,
                "id_company" => $responseCompanyAndUser["company_id"]
            ]
        );

        if (!empty($statementOfValueAdded)) {
            $statementOfValueAdded = array_map(function($item) {
                $item->account_value = "R$ " . number_format($item->account_value, 2, ",", ".");
                $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
                return $item;
            }, $statementOfValueAdded);
        }

        echo $this->view->render("admin/statement-of-value-added", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/balance-sheet/statement-of-value-added/report"],
            "statementOfValueAdded" => $statementOfValueAdded
        ]);
    }

    public function incomeStatementReport()
    {
        $responseInitializaUserAndCompany = initializeUserAndCompanyId();
        $params = [
            "id_user" => $responseInitializaUserAndCompany["user_data"]->id,
            "id_company" => $responseInitializaUserAndCompany["company_id"]
        ];

        $dateRange = $this->getRequests()->get("daterange");
        if (!empty($dateRange)) {
            $date = explode("-", $dateRange);
            $date = array_map(function ($item) {
                return preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $item);
            }, $date);

            $params["date"] = [
                "date_ini" => $date[0],
                "date_end" => $date[1],
            ];
        }

        $balanceSheet = new BalanceSheet();
        $incomeStatement = $balanceSheet->findAllBalanceSheetJoinChartOfAccountAndJoinChartOfAccountGroup(
            [
                "uuid",
                "account_type",
                "account_value",
                "history_account",
                "created_at",
                "deleted"
            ],
            [
                "account_name",
                "account_number"
            ],
            [
                "account_name AS account_name_group",
                "account_number AS account_number_group"
            ],
            $params
        );

        $totalRevenueSale = 0;
        $salesDeductions = 0;
        $costOfSold = 0;
        $totalOperationalExpenses = 0;
        $financingRevenue = 0;
        $financialExpenses = 0;
        $taxProfit = 0;

        $operationalExpenses = [];
        $totalRevenueSalesData = [];
        $salesDeductionsData = [];
        $costOfSoldData = [];
        $financingRevenueData = [];
        $financialExpensesData = [];
        $taxProfitData = [];

        $sortAccounts = function ($item, $array, $referenceKey = "account_name") {
            foreach ($array as $account) {
                if (preg_match("/{$account}/", strtolower(removeAccets($item->$referenceKey)))) {
                    return $item;
                }
            }
        };

        $calculateTotalAccount = function ($acc, $item) {
            $acc += $item->account_value;
            return $acc;
        };

        if (!empty($incomeStatement)) {
            $incomeStatement = array_filter($incomeStatement, function ($item) {
                if (empty($item->getDeleted())) {
                    return $item;
                }
            });

            $revenueResponse = array_filter($incomeStatement, function ($item) {
                if (preg_match("/receita/", strtolower($item->account_name))) {
                    return $item;
                }
            });

            $financingRevenueData = array_filter($revenueResponse, function ($item) use ($sortAccounts) {
                return $sortAccounts($item, [
                    "juros recebidos", 
                    "descontos obtidos", 
                    "rendimentos de aplicacoes financeiras", 
                    "dividendos",
                    "variacao cambial ativa",
                    "ganhos de capital sobre investimentos"
                ]);
            });
            
            $financingRevenue = array_reduce($financingRevenueData, $calculateTotalAccount, 0);
            $totalRevenueSalesData = array_filter($revenueResponse, function ($item) use ($sortAccounts) {
                return $sortAccounts($item, [
                    "venda",
                    "produto",
                    "servico"
                ]);
            });

            $totalRevenueSalesData = array_filter($totalRevenueSalesData, function ($item) {
                if (!preg_match("/RECEITA BRUTA DE VENDA DE PRODUTOS E SERVICOS/i", strtolower(removeAccets($item->account_name)))) {
                    return $item;
                }
            });

            $totalRevenueSale = array_reduce($totalRevenueSalesData, $calculateTotalAccount, 0);
            $salesDeductionsData = array_filter($incomeStatement, function ($item) use ($sortAccounts) {
                return $sortAccounts($item, [
                    "devolucoes",
                    "devolucao",
                    "abatimento",
                    "desconto",
                    "icms",
                    "iss",
                    "pis",
                    "cofins",
                    "frete",
                    "ipi",
                    "inss sobre receita bruta"
                ]);
            });

            $salesDeductions = array_reduce($salesDeductionsData, $calculateTotalAccount, 0);
            $costOfSoldData = array_filter($incomeStatement, function ($item) use ($sortAccounts) {
                return $sortAccounts($item, ["custo"]);
            });

            $costOfSoldData = array_filter($costOfSoldData, function ($item) {
                if (preg_match("/CUSTOS DAS VENDAS DOS PRODUTOS, MERCADORIAS E SERVICOS/i", strtolower(removeAccets($item->account_name)))) {
                    return $item;
                }
            });

            $costOfSold = array_reduce($costOfSoldData, $calculateTotalAccount, 0);
            $operationalExpensesData = array_filter($incomeStatement, function ($item) use ($sortAccounts) {
                return $sortAccounts($item, [
                    "despesa",
                    "inss sobre folha de pagamento"
                ], "account_name_group");
            });

            $operationalExpensesData = array_filter($operationalExpensesData, function ($item) {
                if (preg_match("/DESPESAS ADMINISTRATIVAS/i", strtolower(removeAccets($item->account_name)))) {
                    return $item;
                }
            });

            $totalOperationalExpenses = array_reduce($operationalExpensesData, $calculateTotalAccount, 0);
            $financialExpensesData = array_filter($incomeStatement, function ($item) use ($sortAccounts) {
                return $sortAccounts($item, [
                    "juros pagos",
                    "encargos financeiros",
                    "variacao cambial passiva",
                    "perdas com investimentos",
                    "provisao para perdas em investimentos"
                ]);
            });

            $financialExpenses = array_reduce($financialExpensesData, $calculateTotalAccount, 0);
            $taxProfitData = array_filter($incomeStatement, function ($item) use ($sortAccounts) {
                return $sortAccounts($item, [
                    "imposto de renda a pagar",
                    "provisao para imposto de renda",
                    "contribuicao social a pagar",
                    "provisao para contribuicao social",
                    "imposto de renda diferido",
                    "contribuicao social diferida",
                    "imposto sobre lucros retidos no exterior",
                    "tributos sobre o lucro a pagar"
                ]);
            });

            $taxProfit = array_reduce($taxProfitData, $calculateTotalAccount, 0);
        }

        $groupData = function ($acc, $item) {
            if (!isset($acc[$item->account_name])) {
                $acc[$item->account_name] = $item;
                $acc[$item->account_name]->total = 0;
            }
            $acc[$item->account_name]->total += $item->account_value;
            return $acc;
        };

        $formatCurrency = function ($item) {
            $item->total_formated = "R$ " . number_format($item->total, 2, ",", ".");
            return $item;
        };

        if (!empty($taxProfitData)) {
            $taxProfitData = array_reduce($taxProfitData, $groupData, []);
            $taxProfitData = array_map($formatCurrency, $taxProfitData);
        }

        if (!empty($financialExpensesData)) {
            $financialExpensesData = array_reduce($financialExpensesData, $groupData, []);
            $financialExpensesData = array_map($formatCurrency, $financialExpensesData);
        }

        if (!empty($financingRevenueData)) {
            $financingRevenueData = array_reduce($financingRevenueData, $groupData, []);
            $financingRevenueData = array_map($formatCurrency, $financingRevenueData);
        }

        if (!empty($costOfSoldData)) {
            $costOfSoldData = array_reduce($costOfSoldData, $groupData, []);
            $costOfSoldData = array_map($formatCurrency, $costOfSoldData);
        }

        if (!empty($salesDeductionsData)) {
            $salesDeductionsData = array_reduce($salesDeductionsData, $groupData, []);
            $salesDeductionsData = array_map($formatCurrency, $salesDeductionsData);
        }

        if (!empty($totalRevenueSalesData)) {
            $totalRevenueSalesData = array_reduce($totalRevenueSalesData, $groupData, []);
            $totalRevenueSalesData = array_map($formatCurrency, $totalRevenueSalesData);
        }

        if (!empty($operationalExpensesData)) {
            $operationalExpensesData = array_reduce($operationalExpensesData, $groupData, []);
            $operationalExpenses = array_map($formatCurrency, $operationalExpensesData);
        }
        
        $resultRevenueSalesValue = ($totalRevenueSale - $salesDeductions);
        $grossProfit = ($resultRevenueSalesValue - $costOfSold);
        $totalOperationalExpenses = ($grossProfit - $totalOperationalExpenses);
        $taxesOnProfitValue = $totalOperationalExpenses + ($financingRevenue - $financialExpenses);
        $resultOfExercise = ($taxesOnProfitValue - $taxProfit);

        // Receita bruta de vendas
        $totalRevenueSale = "R$ " . number_format($totalRevenueSale, 2, ",", ".");

        // Deduções de vendas
        $salesDeductions = "R$ " . number_format($salesDeductions, 2, ",", ".");

        // Receita líquida de vendas
        $resultRevenueSales = "R$ " . number_format($resultRevenueSalesValue, 2, ",", ".");

        // Custo das mercadorias vendidas
        $costOfSold = "R$ " . number_format($costOfSold, 2, ",", ".");

        // Lucro bruto
        $grossProfit = "R$ " . number_format($grossProfit, 2, ",", ".");
        
        // Resultado operacional
        $operatingResult = "R$ " . number_format($totalOperationalExpenses, 2, ",", ".");
        
        // Receitas financeiras
        $financingRevenue = "R$ " . number_format($financingRevenue, 2, ",", ".");
        
        // Despesas financeiras
        $financialExpenses = "R$ " . number_format($financialExpenses, 2, ",", ".");

        // Resultado antes dos tributos
        $taxesOnProfit = "R$ " . number_format($taxesOnProfitValue, 2, ",", ".");

        // Impostos sobre o lucro
        $taxProfit = "R$ " . number_format($taxProfit, 2, ",", ".");

        // Resultado líquido do exercício
        $resultOfExercise = "R$ " . number_format($resultOfExercise, 2, ",", ".");

        echo $this->view->render("admin/income-statement-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/income-statement/report"],
            "incomeStatementData" => $incomeStatement,
            "totalRevenue" => $totalRevenueSale,
            "salesDeductions" => $salesDeductions,
            "resultRevenueSales" => $resultRevenueSales,
            "costOfSold" => $costOfSold,
            "grossProfit" => $grossProfit,
            "operationalExpenses" => $operationalExpenses,
            "operatingResult" => $operatingResult,
            "financingRevenue" => $financingRevenue,
            "financialExpenses" => $financialExpenses,
            "taxesOnProfit" => $taxesOnProfit,
            "taxProfit" => $taxProfit,
            "resultOfExercise" => $resultOfExercise,
            "totalRevenueSalesData" => $totalRevenueSalesData,
            "salesDeductionsData" => $salesDeductionsData,
            "costOfSoldData" => $costOfSoldData,
            "financingRevenueData" => $financingRevenueData,
            "financialExpensesData" => $financialExpensesData,
            "taxProfitData" => $taxProfitData
        ]);
    }

    public function generalLedgeReport()
    {
        $responseInitializaUserAndCompany = initializeUserAndCompanyId();
        $params = [
            "id_user" => $responseInitializaUserAndCompany["user_data"]->id,
            "id_company" => $responseInitializaUserAndCompany["company_id"],
            "deleted" => 0
        ];

        $dateRange = $this->getRequests()->get("daterange");
        if (!empty($dateRange)) {
            $date = explode("-", $dateRange);
            $date = array_map(function($item) {
                return preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $item);
            }, $date);

            $params["date"] = [
                "date_ini" => $date[0],
                "date_end" => $date[1]
            ];
        }

        $balanceSheet = new BalanceSheet();
        $generalLedgeData = $balanceSheet->findAllBalanceSheetJoinChartOfAccountAndJoinChartOfAccountGroup(
            [
                "uuid AS uuid_balance_sheet",
                "account_type",
                "account_value",
                "history_account",
                "created_at",
                "deleted"
            ],
            [
                "uuid AS uuid_account",
                "account_name",
                "account_number"
            ],
            [
                "account_name AS account_name_group",
                "id"
            ],
            $params
        );

        $chartOfAccount = new ChartOfAccount();
        $chartOfAccountData = $chartOfAccount->findAllChartOfAccount(
            [
                "uuid",
                "account_name",
                "account_number"
            ],
            $params
        );

        if (!empty($chartOfAccountData)) {
            $chartOfAccountData = array_map(function ($item) {
                $item->account_name = $item->account_number . " " . $item->account_name;
                return $item;
            }, $chartOfAccountData);
        }

        $chartOfAccountSelected = !empty($this->getRequests()->get("selectChartOfAccountMultiple")) ?
            $this->getRequests()->get("selectChartOfAccountMultiple") : [];
        
        if (!empty($generalLedgeData)) {
            usort($generalLedgeData, function ($a, $b) {
                return strtotime($a->created_at) - strtotime($b->created_at);
            });

            $generalLedgeData = array_filter($generalLedgeData, function ($item) use ($chartOfAccountSelected) {
                if (!empty($chartOfAccountSelected)) {
                    if (empty($item->getDeleted()) && in_array($item->uuid_account, $chartOfAccountSelected)) {
                        return $item;
                    }
                }else {
                    if (empty($item->getDeleted())) {
                        return $item;
                    }
                }
            });

            $balance = 0;
            $generalLedgeData = array_map(function ($item) use (&$balance) {
                $item->account_name = $item->account_number . " " . $item->account_name;
                $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
                $item->outstanding_balance = empty($item->account_type) ? "R$ " . number_format($item->account_value, 2, ",", ".") : "-";
                $item->credit_balance = !empty($item->account_type) ? "R$ " . number_format($item->account_value, 2, ",", ".") : "-";

                if (preg_match("/ativo/", strtolower($item->account_name_group))) {
                    if (!empty($item->account_type)) {
                        $item->account_value = $item->account_value * -1;
                    }else {
                        $item->account_value = $item->account_value;
                    }
                }else {
                    if (empty($item->account_type)) {
                        $item->account_value = $item->account_value * -1;
                    }else {
                        $item->account_value = $item->account_value;
                    }
                }

                $balance += $item->account_value;
                $item->balance = "R$ " . number_format($balance, 2, ",", ".");
                return $item;
            }, $generalLedgeData);
        }

        echo $this->view->render("admin/general-ledge-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/general-ledge/report"],
            "chartOfAccountData" => $chartOfAccountData,
            "chartOfAccountSelected" => $chartOfAccountSelected,
            "generalLedgeData" => $generalLedgeData
        ]);
    }

    public function trialBalanceReport()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $params = [
            "id_user" => $responseInitializeUserAndCompany["user_data"]->id,
            "id_company" => $responseInitializeUserAndCompany["company_id"]
        ];

        $dateRange = $this->getRequests()->get("daterange");
        if (!empty($dateRange)) {
            $date = explode("-", $dateRange);

            $date = array_map(function ($item) {
                return preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $item);
            }, $date);

            $params["date"] = [
                "date_ini" => $date[0],
                "date_end" => $date[1]
            ];
        }

        $balanceSheet = new BalanceSheet();
        $trialBalanceData = $balanceSheet->findAllBalanceSheetJoinChartOfAccountAndJoinChartOfAccountGroup(
            [
                "uuid",
                "account_type",
                "account_value",
                "created_at",
                "deleted"
            ],
            [
                "account_name",
                "account_number"
            ],
            [
                "id"
            ],
            $params
        );

        $totalTrialBalance = new \stdClass();
        $totalTrialBalance->outstanding_balance = 0;
        $totalTrialBalance->credit_balance = 0;

        if (!empty($trialBalanceData)) {
            $trialBalanceData = array_filter($trialBalanceData, function ($item) {
                if (empty($item->getDeleted())) {
                    return $item;
                }
            });

            usort($trialBalanceData, function ($a, $b) {
                return strtotime($a->created_at) - strtotime($b->created_at);
            });

            $trialBalanceData = array_map(function ($item) {
                $item->account_value_formated = "R$ " . number_format($item->account_value, 2, ",", ".");
                $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
                $item->outstanding_balance = empty($item->account_type) ? $item->account_value_formated : "-";
                $item->credit_balance = !empty($item->account_type) ? $item->account_value_formated : "-";
                $item->account_name = $item->account_number . " " . $item->account_name;
                return $item;
            }, $trialBalanceData);

            $totalTrialBalance->outstanding_balance = array_reduce($trialBalanceData, function ($acc, $item) {
                if (empty($item->account_type)) {
                    $acc += $item->account_value;
                }
                return $acc;
            }, 0);

            $totalTrialBalance->credit_balance = array_reduce($trialBalanceData, function ($acc, $item) {
                if (!empty($item->account_type)) {
                    $acc += $item->account_value;
                }
                return $acc;
            }, 0);
        }

        $totalTrialBalance->outstanding_balance = "R$ " . number_format($totalTrialBalance->outstanding_balance, 2, ",", ".");
        $totalTrialBalance->credit_balance = "R$ " . number_format($totalTrialBalance->credit_balance, 2, ",", ".");

        echo $this->view->render("admin/trial-balance-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/trial-balance/report"],
            "trialBalanceData" => $trialBalanceData,
            "totalTrialBalance" => $totalTrialBalance
        ]);
    }
}
