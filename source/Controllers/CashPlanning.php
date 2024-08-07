<?php

namespace Source\Controllers;

use DateTime;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\FinancingCashFlow;
use Source\Domain\Model\InvestmentCashFlow;
use Source\Domain\Model\OperatingCashFlow;
use Source\Domain\Model\User;

/**
 * CashPlanning Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class CashPlanning extends Controller
{
    /**
     * CashPlanning constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function cashVariationAnalysis()
    {
        $response = initializeUserAndCompanyId();
        $dateRange = $this->getRequests()->has("daterange") ? explode("-", $this->getRequests()->get("daterange")) : [];
        if (!empty($dateRange)) {
            $dateRange["date_ini"] = preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $dateRange[0]);
            $dateRange["date_end"] = preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $dateRange[1]);
            unset($dateRange[0], $dateRange[1]);
        }

        $dateTimeParam = empty($dateRange["date_ini"]) ? "now" : $dateRange["date_ini"];
        $dateTime = new DateTime($dateTimeParam);

        $months = monthsInPortuguese();
        $month = $months[$dateTime->format("n")] . "/" . $dateTime->format("Y");

        $dateTime = new DateTime();
        $dateRangeParam = empty($dateRange) ?
            [
                "date_ini" => $dateTime->format("Y-m-d"),
                "date_end" => $dateTime->format("Y-m-d")
            ] : $dateRange;

        $params = [
            ["id", "deleted AS variation_deleted"],
            ["group_name"],
            ["history", "entry"],
            $dateRangeParam,
            $response["user"],
            $response["company_id"]
        ];

        $operatingCashFlow = new OperatingCashFlow();
        $operatingCashFlowData = $operatingCashFlow->findOperatingCashFlowJoinCashFlowGroupAndJoinCashFlowData(...$params);

        $grouppedOperatingCashFlow = [];
        $totalGrouppedOperatingCashFlow = 0;

        if (!empty($operatingCashFlowData)) {
            $operatingCashFlowData = array_map(function ($item) {
                return (array)$item->data();
            }, $operatingCashFlowData);

            $operatingCashFlowData = array_filter($operatingCashFlowData, function ($item) {
                if (empty($item["variation_deleted"])) {
                    return $item;
                }
            });

            foreach ($operatingCashFlowData as $operating) {
                if (empty($grouppedOperatingCashFlow[$operating["history"]])) {
                    $grouppedOperatingCashFlow[$operating["history"]] = $operating;
                    $grouppedOperatingCashFlow[$operating["history"]]["total_entry"] = 0;
                }
                $grouppedOperatingCashFlow[$operating["history"]]["total_entry"] += $operating["entry"];
            }

            foreach ($grouppedOperatingCashFlow as $operating) {
                $totalGrouppedOperatingCashFlow += $operating["total_entry"];
            }

            $grouppedOperatingCashFlow = array_map(function ($item) {
                $item["total_entry"] = "R$ " . number_format($item["total_entry"], 2, ",", ".");
                return array_intersect_key($item, array_flip(["history", "total_entry"]));
            }, $grouppedOperatingCashFlow);
        }

        $investmentCashFlow = new InvestmentCashFlow();
        $investmentCashFlowData = $investmentCashFlow->findInvestmentCashFlowJoinCashFlowGroupAndJoinCashFlowData(...$params);

        $grouppedInvestmentCashFlow = [];
        $totalGrouppedInvestmentCashFlow = 0;

        if (!empty($investmentCashFlowData)) {
            $investmentCashFlowData = array_map(function ($item) {
                return (array)$item->data();
            }, $investmentCashFlowData);

            $investmentCashFlowData = array_filter($investmentCashFlowData, function ($item) {
                if (empty($item["variation_deleted"])) {
                    return $item;
                }
            });

            foreach ($investmentCashFlowData as $investment) {
                if (empty($grouppedInvestmentCashFlow[$investment["history"]])) {
                    $grouppedInvestmentCashFlow[$investment["history"]] = $investment;
                    $grouppedInvestmentCashFlow[$investment["history"]]["total_entry"] = 0;
                }
                $grouppedInvestmentCashFlow[$investment["history"]]["total_entry"] += $investment["entry"];
            }

            foreach ($grouppedInvestmentCashFlow as $investment) {
                $totalGrouppedInvestmentCashFlow += $investment["total_entry"];
            }

            $grouppedInvestmentCashFlow = array_map(function ($item) {
                $item["total_entry"] = "R$ " . number_format($item["total_entry"], 2, ",", ".");
                return array_intersect_key($item, array_flip(["history", "total_entry"]));
            }, $grouppedInvestmentCashFlow);
        }

        $financingCashFlow = new FinancingCashFlow();
        $financingCashFlowData = $financingCashFlow->findFinancingCashFlowJoinCashFlowGroupAndJoinCashFlowData(...$params);

        $grouppedFinancingCashFlow = [];
        $totalGrouppedFinancingCashFlow = 0;

        if (!empty($financingCashFlowData)) {
            $financingCashFlowData = array_map(function ($item) {
                return (array)$item->data();
            }, $financingCashFlowData);

            $financingCashFlowData = array_filter($financingCashFlowData, function ($item) {
                if (empty($item["variation_deleted"])) {
                    return $item;
                }
            });

            foreach ($financingCashFlowData as $financing) {
                if (empty($grouppedFinancingCashFlow[$financing["history"]])) {
                    $grouppedFinancingCashFlow[$financing["history"]] = $financing;
                    $grouppedFinancingCashFlow[$financing["history"]]["total_entry"] = 0;
                }
                $grouppedFinancingCashFlow[$financing["history"]]["total_entry"] += $financing["entry"];
            }

            foreach ($grouppedFinancingCashFlow as $financing) {
                $totalGrouppedFinancingCashFlow += $financing["total_entry"];
            }

            $grouppedFinancingCashFlow = array_map(function ($item) {
                $item["total_entry"] = "R$ " . number_format($item["total_entry"], 2, ",", ".");
                return array_intersect_key($item, array_flip(["history", "total_entry"]));
            }, $grouppedFinancingCashFlow);
        }

        echo $this->view->render("admin/cash-variation-analysis", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-planning/cash-flow/cash-variation-analysis"],
            "grouppedOperatingCashFlow" => $grouppedOperatingCashFlow,
            "grouppedInvestmentCashFlow" => $grouppedInvestmentCashFlow,
            "grouppedFinancingCashFlow" => $grouppedFinancingCashFlow,
            "totalGrouppedOperatingCashFlow" => $totalGrouppedOperatingCashFlow,
            "totalGrouppedInvestmentCashFlow" => $totalGrouppedInvestmentCashFlow,
            "totalGrouppedFinancingCashFlow" => $totalGrouppedFinancingCashFlow,
            "cashNetVolatility" => [
                $totalGrouppedOperatingCashFlow,
                $totalGrouppedInvestmentCashFlow,
                $totalGrouppedFinancingCashFlow
            ],
            "month" => $month
        ]);
    }

    public function cashBudget()
    {
        $response = initializeUserAndCompanyId();
        $cashFlowColumns = ["created_at", "entry"];

        $cashFlow = new CashFlow();
        $cashFlowData = $cashFlow->findCashFlowByUser($cashFlowColumns, $response["user"], $response["company_id"]);

        $dateRange = $this->getRequests()->get("daterange");
        $dateTimeFormat = $this->getRequests()->has("daterange") ? explode("-", $dateRange)[0] : "";

        $dateTimeFormat = preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $dateTimeFormat);
        $dateTimeFormat = !empty($dateTimeFormat) ? $dateTimeFormat : "now";

        $months = monthsInPortuguese();
        $dateTime = new DateTime();

        $grouppedCashFlowData = [];
        $cashBudget = [
            "opening_cash_balance" => 0,
            "month" => $months[$dateTime->format("n")] .  "/" . $dateTime->format("Y")
        ];

        $cashReceipts = [];
        $cashOutflows = [];

        $totalReceipts = 0;
        $totalOutflows = 0;

        if (!empty($cashFlowData)) {
            $currentDateTime = new DateTime($dateTimeFormat);

            $cashFlowData = array_map(function ($item) use ($months, $currentDateTime) {
                $item->entry = $item->getEntry();
                $item->month = $months[$currentDateTime->modify($item->created_at)->format("n")] .
                    "/" . $currentDateTime->modify($item->created_at)->format("Y");
                $item = (array) $item->data();
                return $item;
            }, $cashFlowData);

            foreach ($cashFlowData as $cashFlowValue) {
                if (empty($grouppedCashFlowData[$cashFlowValue["month"]][$cashFlowValue["group_name"]])) {
                    $grouppedCashFlowData[$cashFlowValue["month"]][$cashFlowValue["group_name"]] = $cashFlowValue;
                    $grouppedCashFlowData[$cashFlowValue["month"]][$cashFlowValue["group_name"]]["total_entry"] = 0;
                }
                $grouppedCashFlowData[$cashFlowValue["month"]][$cashFlowValue["group_name"]]["total_entry"] += $cashFlowValue["entry"];
            }

            $currentDateTime = new DateTime($dateTimeFormat);
            foreach ($grouppedCashFlowData as $grouppedArray) {
                foreach ($grouppedArray as $groupData) {
                    $cashFlowDateTime = new DateTime($groupData["created_at"]);
                    if ($currentDateTime->modify("-1 month")->format('m') == $cashFlowDateTime->format('m')) {
                        $cashBudget["opening_cash_balance"] += $groupData["total_entry"];
                    }

                    $currentDateTime = new DateTime($dateTimeFormat);
                    if ($currentDateTime->format("m-Y") == $cashFlowDateTime->format("m-Y")) {
                        $cashBudget["month"] = $groupData["month"];
                        if ($groupData["total_entry"] > 0) {
                            $cashReceipts[] = [
                                "group_name" => $groupData["group_name"],
                                "total_entry" => "R$ " . number_format($groupData["total_entry"], 2, ",", "."),
                                "entry" => $groupData["total_entry"]
                            ];
                        } else {
                            $cashOutflows[] = [
                                "group_name" => $groupData["group_name"],
                                "total_entry" => "R$ " . number_format($groupData["total_entry"], 2, ",", "."),
                                "entry" => $groupData["total_entry"]
                            ];
                        }
                    }
                }
            }

            foreach ($cashReceipts as $value) {
                $totalReceipts += $value["entry"];
            }

            foreach ($cashOutflows as $value) {
                $totalOutflows += $value["entry"];
            }
        }

        $finalCashBalance = array_sum([$cashBudget["opening_cash_balance"], $totalReceipts, $totalOutflows]);
        echo $this->view->render("admin/cash-budget", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-planning/cash-flow/cash-budget"],
            "cashBudget" => $cashBudget,
            "cashReceipts" => $cashReceipts,
            "cashOutflows" => $cashOutflows,
            "totalReceipts" => "R$ " . number_format($totalReceipts, 2, ",", "."),
            "totalOutflows" => "R$ " . number_format($totalOutflows, 2, ",", "."),
            "finalCashBalance" => $finalCashBalance
        ]);
    }
}
