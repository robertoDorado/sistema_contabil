<?php

namespace Source\Controllers;

use DateTime;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow;

/**
 * AnalyzesAndIndicators Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class AnalyzesAndIndicators extends Controller
{
    /**
     * AnalyzesAndIndicators constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function findChasFlowDataForBarChartExpensesByAccountGroup()
    {
        $user = basicsValidatesForChartsRender();
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $cashFlow = new CashFlow();
        
        $columns = ["entry", "deleted", "id_cash_flow_group"];
        $cashFlowData = $cashFlow->findCashFlowByUser($columns, $user, $companyId);
        $dateRange = $this->getRequests()->get("daterange");

        if (!empty($this->getRequests()->get("daterange"))) {
            $cashFlow = new CashFlow();
            $cashFlowData = $cashFlow->findCashFlowDataByDate($dateRange, $user, $columns, $companyId);
        }

        if (empty($cashFlowData)) {
            echo json_encode([]);
            die;
        }

        $cashFlowData = array_map(function($item) {
            $item->entry = $item->getEntry();
            $item = $item->data();
            return (array) $item;
        }, $cashFlowData);

        $grouppedCashFlowData = [];
        foreach ($cashFlowData as $value) {
            $entryValue = $value["entry"];
            if (empty($grouppedCashFlowData[$value["id_cash_flow_group"]])) {
                $grouppedCashFlowData[$value["id_cash_flow_group"]] = $value;
                $grouppedCashFlowData[$value["id_cash_flow_group"]]["total_value"] = 0;
            }

            $grouppedCashFlowData[$value["id_cash_flow_group"]]["total_value"] += $entryValue;
        }

        $grouppedCashFlowData = array_values($grouppedCashFlowData);
        $grouppedCashFlowData = array_map(function($item) {
            return array_intersect_key($item, array_flip(["group_name", "total_value"]));
        }, $grouppedCashFlowData);

        echo json_encode(["data" => $grouppedCashFlowData]);
    }

    public function findChasFlowDataForBarChartMonthlyCashFlowComparasion()
    {
        $user = basicsValidatesForChartsRender();
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $cashFlow = new CashFlow();
        
        $columns = ["created_at", "entry", "deleted"];
        $cashFlowData = $cashFlow->findCashFlowByUser($columns, $user, $companyId);
        $dateRange = $this->getRequests()->get("daterange");

        if (!empty($this->getRequests()->has("daterange"))) {
            $cashFlow = new CashFlow();
            $cashFlowData = $cashFlow->findCashFlowDataByDate($dateRange, $user, $columns, $companyId);
        }

        if (empty($cashFlowData)) {
            echo json_encode([]);
            die;
        }

        $months = [
            1 => 'Janeiro', 
            2 => 'Fevereiro', 
            3 => 'Março',
            4 => 'Abril', 
            5 => 'Maio', 
            6 => 'Junho',
            7 => 'Julho', 
            8 => 'Agosto', 
            9 => 'Setembro',
            10 => 'Outubro', 
            11 => 'Novembro', 
            12 => 'Dezembro'
        ];

        $cashFlowData = array_map(function ($item) use ($months) {
            $item->month = date("Y-m", strtotime($item->created_at));
            $dateTime = new DateTime($item->created_at);
            $item->month_name = $months[$dateTime->format("n")] . "/" . date("Y" ,strtotime($item->created_at));
            $item->entry_value = $item->getEntry();
            $item = $item->data();
            return (array) $item;
        }, $cashFlowData);

        $grouppedCashFlowData = [];
        foreach ($cashFlowData as &$value) {

            $entryValue = $value["entry_value"];
            if (empty($grouppedCashFlowData[$value["month"]])) {
                $grouppedCashFlowData[$value["month"]] = $value;
                $grouppedCashFlowData[$value["month"]]["positive_value"] = 0;
                $grouppedCashFlowData[$value["month"]]["negative_value"] = 0;
            }

            if ($entryValue > 0) {
                $grouppedCashFlowData[$value["month"]]["positive_value"] += $entryValue;
            } else {
                $grouppedCashFlowData[$value["month"]]["negative_value"] += $entryValue;
            }
        }

        ksort($grouppedCashFlowData);
        $grouppedCashFlowData = array_values($grouppedCashFlowData);
        $filterKeys = ["positive_value", "negative_value", "month_name"];

        $grouppedCashFlowData = array_map(function($item) use ($filterKeys) {
            return array_intersect_key($item, array_flip($filterKeys));
        }, $grouppedCashFlowData);

        echo json_encode(["data" => array_slice($grouppedCashFlowData, 0, 12, true)]);
    }

    public function findCashFlowDataForChartPieAccountGroupingCount()
    {
        $user = basicsValidatesForChartsRender();
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $dateRange = !empty($this->getRequests()->get("daterange")) ? $this->getRequests()->get("daterange") : "";
        $cashFlow = new CashFlow();
        $cashFlowData = $cashFlow->findGroupAccountsAgrupped($user, $companyId, $dateRange);

        if (empty($cashFlowData)) {
            echo json_encode([]);
            die;
        }

        $accountsData = [];
        $totalAccounts = [];

        foreach ($cashFlowData as $arrayData) {
            array_push($accountsData, $arrayData->group_name);
            array_push($totalAccounts, $arrayData->total_accounts);
        }

        echo json_encode(
            [
                "total_accounts" => $totalAccounts,
                "accounts_data" => $accountsData
            ]
        );
    }

    public function findCashFlowDataForChartLinePooledChasFlow()
    {
        $user = basicsValidatesForChartsRender();
        $cashFlow = new CashFlow();
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $cashFlowData = $cashFlow->findCashFlowByUser(["entry", "created_at"], $user, $companyId);

        $dateRange = $this->getRequests()->get("daterange");
        if (!empty($dateRange)) {
            $cashFlow = new CashFlow();
            $cashFlowData = $cashFlow->findCashFlowDataByDate($dateRange, $user, ["entry", "created_at"], $companyId);
        }

        if (empty($cashFlowData)) {
            echo json_encode([]);
            die;
        }

        $orderByDate = function ($a, $b) {
            $monthA = date("n", strtotime($a));
            $monthB = date("n", strtotime($b));

            $dayA = date("j", strtotime($a));
            $dayB = date("j", strtotime($b));

            if ($monthA == $monthB) {
                if ($dayA == $dayB) {
                    return 0;
                }

                return $dayA < $dayB ? -1 : 1;
            }
            return $monthA < $monthB ? -1 : 1;
        };

        $groupByDate = [];
        foreach ($cashFlowData as $value) {
            $date = $value->created_at;
            $entryValue = $value->getEntry();

            if (array_key_exists($date, $groupByDate)) {
                $groupByDate[$date] += $entryValue;
            } else {
                $groupByDate[$date] = $entryValue;
            }
        }

        uksort($groupByDate, $orderByDate);
        $formatDate = function ($date) {
            return date("d/m", strtotime($date));
        };

        $response = [];
        $response["created_at"] = array_keys($groupByDate);
        $response["created_at"] = array_map($formatDate, $response["created_at"]);

        $response["entry"] = array_values($groupByDate);
        $response["created_at"] = array_slice($response["created_at"], 0, 31);

        $response["entry"] = array_slice($response["entry"], 0, 31);
        echo json_encode($response);
    }

    public function charts()
    {
        echo $this->view->render("admin/cash-flow-charts", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/analyzes-and-indicators/cash-flow/charts-and-visualizations"]
        ]);
    }
}
