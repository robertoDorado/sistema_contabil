<?php

namespace Source\Controllers;

use DateTime;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow;
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

    public function cashBudget()
    {
        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail(["id", "deleted"]);
        $user->setId($userData->id);

        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $cashFlowColumns = ["created_at", "entry"];

        $cashFlow = new CashFlow();
        $cashFlowData = $cashFlow->findCashFlowByUser($cashFlowColumns, $user, $companyId);
        
        $dateRange = $this->getRequests()->get("daterange");
        $dateTimeFormat = $this->getRequests()->has("daterange") ? explode("-", $dateRange)[0] : "";
        
        $dateTimeFormat = preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $dateTimeFormat);
        $dateTimeFormat = !empty($dateTimeFormat) ? $dateTimeFormat : "now";

        $grouppedCashFlowData = [];
        $cashBudget = [
            "opening_cash_balance" => 0
        ];

        $cashReceipts = [];
        $cashOutflows = [];

        $totalReceipts = 0;
        $totalOutflows = 0;

        if (!empty($cashFlowData)) {
            $months = monthsInPortuguese();
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
