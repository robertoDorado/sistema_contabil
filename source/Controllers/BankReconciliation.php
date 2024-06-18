<?php

namespace Source\Controllers;

use OfxParser\Parser;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\User;

/**
 * BankReconciliation Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class BankReconciliation extends Controller
{
    /**
     * BankReconciliation constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function importOfxFile()
    {
        if (empty(session()->user->company_id)) {
            echo json_encode(["error" => "selecione uma empresa antes de importar o arquivo"]);
            die;
        }

        $requestFiles = $this->getRequestFiles()->getAllFiles();
        $httpReferer = $this->getServer()->getServerByKey("HTTP_REFERER");
        $urlComponents = parse_url($httpReferer);

        if (!file_exists($requestFiles["ofxFile"]["tmp_name"]) && !is_readable($requestFiles["ofxFile"]["tmp_name"])) {
            http_response_code(500);
            echo json_encode(["error" => "o arquivo não existe ou não pode ser lido corretamente"]);
            die;
        }

        $fileContent = file_get_contents($requestFiles["ofxFile"]["tmp_name"]);
        if (empty($fileContent)) {
            http_response_code(500);
            echo json_encode(["error" => "o arquivo não pode estar vazio"]);
            die;
        }

        $parser = new Parser();
        $ofx = $parser->loadFromFile($requestFiles["ofxFile"]["tmp_name"]);

        $bankAccount = $ofx->bankAccounts[0];
        $transactions = $bankAccount->statement->transactions;

        if (empty($transactions)) {
            http_response_code(500);
            echo json_encode(["error" => "o arquivo de transações está vazio"]);
            die;
        }

        $datesTransaction = array_map(function ($item) {
            $item->timestamp = strtotime($item->date->format("Y-m-d"));
            return array_intersect_key((array) $item, ["timestamp" => true]);
        }, $transactions);

        $minDate = min($datesTransaction);
        $maxDate = max($datesTransaction);
        $dateRange = date("d/m/Y", $minDate["timestamp"]) . " - " . date("d/m/Y", $maxDate["timestamp"]);

        $allowKeys = ["amount", "memo", "date"];
        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail([]);
        $user->setId($userData->id);

        $cashFlow = new CashFlow();
        $cashFlow->id_user = $userData->id;
        $companyId = session()->user->company_id;
        $cashFlowData = $cashFlow->findCashFlowDataByDate($dateRange, $user, [], $companyId);

        if ($urlComponents['path'] == "/admin/bank-reconciliation/cash-flow/automatic") {
            $transactions = array_map(function ($item) use ($allowKeys) {
                $item->date = $item->date->format("Y-m-d");
                return array_intersect_key((array) $item, array_flip($allowKeys));
            }, $transactions);

            $cashFlowData = array_map(function ($item) use ($allowKeys) {
                $item->amount = $item->getEntry();
                $item->memo = $item->getHistory();
                $item->date = $item->created_at;
                return array_intersect_key((array) $item->data(), array_flip($allowKeys));
            }, $cashFlowData);
        }else {
            $cashFlowData = array_map(function($item) {
                return $item->data();
            }, $cashFlowData);

            $transactions = array_map(function ($item) {
                $item->amount_formated = "R$ " . number_format($item->amount, 2, ",", ".");
                $item->date = $item->date->format("d/m/Y");
                return $item;
            }, $transactions);
        }

        $total = 0;
        if ($urlComponents['path'] == "/admin/bank-reconciliation/cash-flow/automatic") {
            $differentData = array_udiff($transactions, $cashFlowData, function ($a, $b) {
                if ($a['amount'] == $b['amount']) {
                    return 0;
                }
                return ($a['amount'] < $b['amount']) ? -1 : 1;
            });
            
            if (!empty($differentData)) {
                $differentData = array_map(function ($data) {
                    $data["date"] = date("d/m/Y", strtotime($data["date"]));
                    $data["amount_formated"] = "R$ " . number_format($data["amount"], 2, ",", ".");
                    return $data;
                }, $differentData);
    
                $differentData = array_values($differentData);
                foreach ($differentData as $value) {
                    $total += $value["amount"];
                }
            }

            echo empty($differentData) ?
                json_encode(["success" => "todas as contas estão conciliadas"]) :
                json_encode(["data" => $differentData, "total" => "R$ " . number_format($total, 2, ",", ".")]);
        }else {
            if (!empty($transactions)) {
                foreach ($transactions as $value) {
                    $total += $value->amount;
                }
            }
            
            echo json_encode(
                [
                    "data" => $transactions, 
                    "cashFlowData" => $cashFlowData, 
                    "total" => "R$ " . number_format($total, 2, ",", "."),
                ]
            );
        }
    }

    public function manualReconciliationCashFlow()
    {
        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail();
        $user->setId($userData->id);
        
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $cashFlow = new CashFlow();
        $cashFlowDataByUser = $cashFlow->findCashFlowByUser([], $user, $companyId);

        if (!empty($cashFlowDataByUser)) {
            $cashFlowDataByUser = array_map(function($cashFlowData) {
                $cashFlowData->setEntry("R$ " . number_format($cashFlowData->getEntry(), 2, ",", "."));
                $cashFlowData->created_at = date("d/m/Y", strtotime($cashFlowData->created_at));
                $cashFlowData->entry_type_value = !empty($cashFlowData->entry_type) ? "Crédito" : "Débito";
                return $cashFlowData;
            }, $cashFlowDataByUser);
        }

        echo $this->view->render("admin/manual-bank-reconciliation-cashflow", [
            "endpoints" => ["/admin/bank-reconciliation/cash-flow/manual"],
            "userFullName" => showUserFullName(),
            "cashFlowDataByUser" => $cashFlowDataByUser
        ]);
    }

    public function automaticReconciliationCashFlow()
    {
        echo $this->view->render("admin/automatic-bank-reconciliation-cashflow", [
            "endpoints" => ["/admin/bank-reconciliation/cash-flow/automatic"],
            "userFullName" => showUserFullName()
        ]);
    }
}
