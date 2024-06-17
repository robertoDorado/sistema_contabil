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

    public function automaticReconciliationCashFlow()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            if (empty(session()->user->company_id)) {
                echo json_encode(["error" => "selecione uma empresa antes de importar o arquivo"]);
                die;
            }

            $requestFiles = $this->getRequestFiles()->getAllFiles();
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

            $differentData = array_udiff($transactions, $cashFlowData, function ($a, $b) {
                if ($a['amount'] == $b['amount']) {
                    return 0;
                }
                return ($a['amount'] < $b['amount']) ? -1 : 1;
            });

            $total = 0;
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
            die;
        }

        echo $this->view->render("admin/automatic-bank-reconciliation", [
            "endpoints" => ["/admin/bank-reconciliation/cash-flow/automatic"],
            "userFullName" => showUserFullName()
        ]);
    }
}
