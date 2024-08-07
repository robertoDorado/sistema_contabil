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
