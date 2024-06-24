<?php

namespace Source\Controllers;

use DateTime;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\User;

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

    public function cashFlowProjections()
    {
        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail(["id", "deleted"]);
        $user->setId($userData->id);

        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $cashFlow = new CashFlow();
        $cashFlowData = $cashFlow->findCashFlowByUser(["entry", "deleted", "created_at"], $user, $companyId);
        $dateRange = $this->getRequests()->get("daterange");

        if ($this->getRequests()->has("daterange")) {
            $cashFlow = new CashFlow();
            $cashFlowData = $cashFlow->findCashFlowDataByDate($dateRange, $user, ["entry", "deleted", "created_at"], $companyId);
        }

        $grouppedCashFlowData = [];
        $incomeData = [];
        $expensesData = [];
        $projectedCashFlow = [];

        if (!empty($cashFlowData)) {

            $months = monthsInPortuguese();
            $cashFlowData = array_map(function ($cashFlow) use ($months) {
                $cashFlow->entry = $cashFlow->getEntry();
                $cashFlow->date = date("Y-m", strtotime($cashFlow->created_at));
                $cashFlow->month = $months[date("n", strtotime($cashFlow->created_at))] . "/" . date("Y", strtotime($cashFlow->created_at));
                return (array)$cashFlow->data();
            }, $cashFlowData);

            foreach ($cashFlowData as $value) {
                $entryValue = $value["entry"];
                if (empty($grouppedCashFlowData[$value["date"]][$value["group_name"]])) {
                    $grouppedCashFlowData[$value["date"]][$value["group_name"]] = $value;
                    $grouppedCashFlowData[$value["date"]][$value["group_name"]]["total_entry"] = 0;
                }

                $grouppedCashFlowData[$value["date"]][$value["group_name"]]["total_entry"] += $entryValue;
            }

            foreach ($grouppedCashFlowData as $dateKey => $groupsData) {
                foreach ($groupsData as $groupKey => &$group) {
                    $grouppedCashFlowData[$dateKey][$groupKey] = array_intersect_key($group, array_flip(["total_entry", "month", "date", "group_name"]));
                    $grouppedCashFlowData[$dateKey][$groupKey]["total_entry_value"] = $group["total_entry"];
                    $grouppedCashFlowData[$dateKey][$groupKey]["total_entry"] = $group["total_entry"] > 0 ? $group["total_entry"] : $group["total_entry"] * -1;
                    $grouppedCashFlowData[$dateKey][$groupKey]["total_entry"] = "R$ " . number_format($grouppedCashFlowData[$dateKey][$groupKey]["total_entry"], 2, ",", ".");
                }
            }

            foreach ($grouppedCashFlowData as $dateKey => $groupsData) {
                foreach ($groupsData as $groupKey => &$group) {
                    if (isset($group["total_entry_value"])) {
                        if ($group["total_entry_value"] >= 0) {
                            $incomeData[] = $group;
                        }else {
                            $expensesData[] = $group;
                        }
                    }
                }
            }

            foreach ($incomeData as $key => $data) {
                if (empty($projectedCashFlow[$data["date"]])) {
                    $projectedCashFlow[$data["date"]]["date"] = $data["date"];
                    $projectedCashFlow[$data["date"]]["total_income_value"] = 0;
                    $projectedCashFlow[$data["date"]]["month"] = $data["month"];
                    $projectedCashFlow[$data["date"]]["total_expenses_value"] = 0;
                    $projectedCashFlow[$data["date"]]["accumulated_balance"] = 0;
                }
                $projectedCashFlow[$data["date"]]["total_income_value"] += $data["total_entry_value"];
            }

            foreach ($expensesData as $key => $data) {
                if (empty($projectedCashFlow[$data["date"]])) {
                    $projectedCashFlow[$data["date"]]["date"] = $data["date"];
                    $projectedCashFlow[$data["date"]]["total_income_value"] = 0;
                    $projectedCashFlow[$data["date"]]["month"] = $data["month"];
                    $projectedCashFlow[$data["date"]]["total_expenses_value"] = 0;
                    $projectedCashFlow[$data["date"]]["accumulated_balance"] = 0;
                }
                $projectedCashFlow[$data["date"]]["total_expenses_value"] += $data["total_entry_value"];
                $projectedCashFlow[$data["date"]]["total_expenses_value"] = $projectedCashFlow[$data["date"]]["total_expenses_value"] * -1;
                $projectedCashFlow[$data["date"]]["month_balance"] = $projectedCashFlow[$data["date"]]["total_income_value"] - $projectedCashFlow[$data["date"]]["total_expenses_value"]; 
            }

            usort($projectedCashFlow, function($a, $b) {
                return strtotime($a["date"]) - strtotime($b["date"]);
            });

            $projectedCashFlow = array_values($projectedCashFlow);
            $accumulated = 0;
            foreach ($projectedCashFlow as &$data) {
                $accumulated += $data["month_balance"];
                $data["accumulated_balance"] += $accumulated;
            }

        }

        echo $this->view->render("admin/cash-flow-projections", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/analyzes-and-indicators/cash-flow/cash-flow-projections"],
            "incomeData" => $incomeData,
            "expensesData" => $expensesData,
            "projectedCashFlow" => $projectedCashFlow
        ]);
    }

    public function financialIndicators()
    {
        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail(["id", "deleted"]);
        $user->setId($userData->id);

        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $cashFlow = new CashFlow();
        $cashFlowData = $cashFlow->findCashFlowByUser(["entry", "deleted"], $user, $companyId);

        $financialIndicators = [
            "recebimentos de clientes" => 0,
            "pagamentos a fornecedores e empregados" => 0,
            "despesas de capital" => 0,
            "emissão de dívidas ou ações" => 0,
            "pagamento de dívidas ou dividendos" => 0,
            "compra de ativos fixos" => 0,
            "venda de investimentos" => 0,
            "pagamentos de juros" => 0,
            "pagamentos de dívidas" => 0,
            "lucro líquido" => 0,
            "receita líquida" => 0,
            "período médio de cobrança" => 0,
            "período médio de estoque" => 0,
            "período médio de pagamento" => 0,
            "fco" => 0
        ];

        if (!empty($cashFlowData)) {
            $cashFlowData = array_map(function ($item) {
                $item->entry = $item->getEntry() >= 0 ? $item->getEntry() : ($item->getEntry() * -1);
                $item->group_name = strtolower($item->group_name);
                $item = $item->data();
                return $item;
            }, $cashFlowData);

            $allowGroupNameKeys = [
                "recebimentos de clientes",
                "pagamentos a fornecedores e empregados",
                "despesas de capital",
                "emissão de dívidas ou ações",
                "pagamento de dívidas ou dividendos",
                "compra de ativos fixos",
                "venda de investimentos",
                "pagamentos de juros",
                "pagamentos de dívidas",
                "lucro líquido",
                "receita líquida",
                "período médio de cobrança",
                "período médio de estoque",
                "período médio de pagamento"
            ];

            $cashFlowData = array_filter($cashFlowData, function ($item) use ($allowGroupNameKeys) {
                return in_array($item->group_name, $allowGroupNameKeys);
            });

            foreach ($cashFlowData as $value) {
                if ($financialIndicators[$value->group_name] >= 0) {
                    $financialIndicators[$value->group_name] += $value->entry;
                }

                if (!empty($financialIndicators["recebimentos de clientes"]) && !empty($financialIndicators["pagamentos a fornecedores e empregados"])) {
                    $financialIndicators["fco"] = $financialIndicators["recebimentos de clientes"] - $financialIndicators["pagamentos a fornecedores e empregados"];
                }
            }
        }

        echo $this->view->render("admin/financial-indicators", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/analyzes-and-indicators/cash-flow/financial-indicators"],
            "financialIndicators" => $financialIndicators
        ]);
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

        $cashFlowData = array_map(function ($item) {
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
        $grouppedCashFlowData = array_map(function ($item) {
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

        $months = monthsInPortuguese();
        $cashFlowData = array_map(function ($item) use ($months) {
            $item->month = date("Y-m", strtotime($item->created_at));
            $dateTime = new DateTime($item->created_at);
            $item->month_name = $months[$dateTime->format("n")] . "/" . date("Y", strtotime($item->created_at));
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

        $grouppedCashFlowData = array_map(function ($item) use ($filterKeys) {
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
