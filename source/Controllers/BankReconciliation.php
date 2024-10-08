<?php

namespace Source\Controllers;

use DateTime;
use OfxParser\Parser;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow;

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

    public function importExcelFile()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        if (empty(session()->user->company_id)) {
            echo json_encode(["error" => "selecione uma empresa antes de importar o arquivo"]);
            die;
        }

        $file = $this->getRequestFiles()->getFile("excelFile");
        $verifyExtensions = ["xls", "xlsx", "csv"];

        $fileExtension = explode(".", $file["name"]);
        $fileExtension = strtolower(array_pop($fileExtension));

        if (!in_array($fileExtension, $verifyExtensions)) {
            throw new \Exception("tipo de arquivo inválido", 500);
        }

        $spreadSheetFile = IOFactory::load($file["tmp_name"]);
        $data = $spreadSheetFile->getActiveSheet()->toArray();
        $httpReferer = $this->getServer()->getServerByKey("HTTP_REFERER");
        $urlComponents = parse_url($httpReferer);

        $requiredFieldsExcelFile = [
            "Data lançamento",
            "Grupo de contas",
            "Histórico",
            "Tipo de entrada",
            "Lançamento"
        ];

        $arrayHeader = array_shift($data);
        foreach ($requiredFieldsExcelFile as $field) {
            if (!in_array($field, $arrayHeader)) {
                http_response_code(400);
                echo json_encode(["error" => "cabeçalho do arquivo inválido"]);
                die;
            }
        }

        $excelData = [];
        foreach ($data as $arrayData) {
            foreach ($arrayData as $key => $value) {
                $excelData[strtolower(substr($arrayHeader[$key], 0, 1))][] = $value;
            }
        }

        $formatDateByFileExtension = [
            "xls" => function ($item) {
                $item = preg_replace("/^(\d{1})\/(\d+)\/(\d+)$/", "0$1/$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d{1})\/(\d+)$/", "$1/0$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d+)\/(\d+)$/", "$3-$1-$2", $item);
                return $item;
            },

            "xlsx" => function ($item) {
                $item = preg_replace("/^(\d{1})\/(\d+)\/(\d+)$/", "0$1/$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d{1})\/(\d+)$/", "$1/0$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d+)\/(\d+)$/", "$3-$1-$2", $item);
                return $item;
            },

            "csv" => function ($item) {
                $item = preg_replace("/^(\d{1})\/(\d+)\/(\d+)$/", "0$1/$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d{1})\/(\d+)$/", "$1/0$2/$3", $item);
                $item = preg_replace("/^(\d+)\/(\d+)\/(\d+)$/", "$3-$2-$1", $item);
                return $item;
            }
        ];

        $excelData["d"] = array_map(function ($item) use ($formatDateByFileExtension, $fileExtension) {
            if (!empty($formatDateByFileExtension[$fileExtension])) {
                return $formatDateByFileExtension[$fileExtension]($item);
            }
        }, $excelData["d"]);

        $errorLaunchValue = [];
        foreach ($excelData["l"] as $key => $value) {
            $tempValue = trim($value);
            $tempValue = preg_replace("/^(.+)(,\d{1,2}|\.\d{1,2})$/", "$1;$2", $tempValue);
            $tempValue = preg_replace("/[^-\d;]+/", "", $tempValue);
            $tempValue = preg_replace("/;/", ".", $tempValue);

            if ($excelData["t"][$key] == "Crédito" && $tempValue < 0) {
                $errorLaunchValue["credit"][$key] = $tempValue;
            }

            if ($excelData["t"][$key] == "Débito" && $tempValue > 0) {
                $errorLaunchValue["debit"][$key] = $tempValue;
            }
        }

        if (!empty($errorLaunchValue)) {
            $errorMessage = "lançamento incorreto dos valores";
            if (!empty($errorLaunchValue["credit"])) {
                foreach ($errorLaunchValue["credit"] as $value) {
                    $errorMessage .= ", Crédito = {$value}";
                }
            }

            if (!empty($errorLaunchValue["debit"])) {
                foreach ($errorLaunchValue["debit"] as $value) {
                    $errorMessage .= ", Débito = {$value}";
                }
            }

            http_response_code(400);
            echo json_encode(["error" => $errorMessage]);
            die;
        }

        $excelData["l"] = array_map(function ($item) {
            return convertCurrencyRealToFloat($item);
        }, $excelData["l"]);

        foreach ($excelData["l"] as $key => &$launchValue) {
            $launchValue = $excelData["t"][$key] == "Crédito" ? $launchValue : $launchValue * -1;
        }

        $totalEntry = array_reduce($excelData["l"], function ($acc, $item) {
            $acc += $item;
            return $acc;
        }, 0);

        $excelData["l"] = array_map(function ($item) {
            return "R$ " . number_format($item, 2, ",", ".");
        }, $excelData["l"]);

        if ($urlComponents["path"] == "/admin/bank-reconciliation/cash-flow/manual") {
            $excelData["d"] = array_map(function ($item) {
                return (new DateTime($item))->format("d/m/Y");
            }, $excelData["d"]);

            $excelData = array_intersect_key($excelData, array_flip(["d", "h", "l"]));
            echo json_encode(
                [
                    "success" => "importação realizada com sucesso",
                    "data" => $excelData,
                    "total" => "R$ " . number_format($totalEntry, 2, ",", ".")
                ]
            );
        } else {
            $responseUserAndCompany = initializeUserAndCompanyId();
            $cashFlow = new CashFlow();
            $cashFlowData = $cashFlow->findCashFlowToCompareAutomaticImportsFile(
                $responseUserAndCompany["user"],
                $responseUserAndCompany["company_id"],
                ["entry", "created_at", "history"]
            );

            $formatExcelData = [];
            $cashFlowData = array_map(function ($item) {
                $item->history = $item->getHistory();
                $item->entry = $item->getEntry();
                return (array) $item->data();
            }, $cashFlowData);

            foreach ($excelData["l"] as $key => &$launchValue) {
                $launchValue = trim($launchValue);
                $launchValue = preg_replace("/^(.+)(,\d{1,2}|\.\d{1,2})$/", "$1;$2", $launchValue);
                $launchValue = preg_replace("/[^-\d;]+/", "", $launchValue);
                $launchValue = preg_replace("/;/", ".", $launchValue);
                $formatExcelData[$key]["created_at"] = $excelData["d"][$key];
                $formatExcelData[$key]["entry"] = $launchValue;
                $formatExcelData[$key]["history"] = $excelData["h"][$key];
            }

            $differentDateData = array_udiff($formatExcelData, $cashFlowData, function ($a, $b) {
                if ($a['created_at'] == $b['created_at']) {
                    return 0;
                }
                return ($a['created_at'] < $b['created_at']) ? -1 : 1;
            });

            $differentEntryData = array_udiff($formatExcelData, $cashFlowData, function ($a, $b) {
                if ($a['entry'] == $b['entry']) {
                    return 0;
                }
                return ($a['entry'] < $b['entry']) ? -1 : 1;
            });

            $differentHistoryData = array_udiff($formatExcelData, $cashFlowData, function ($a, $b) {
                if ($a['history'] == $b['history']) {
                    return 0;
                }
                return ($a['history'] < $b['history']) ? -1 : 1;
            });

            $total = 0;
            $differentData = arrayWithMostItems($differentDateData, $differentEntryData, $differentHistoryData);
            if (!empty($differentData)) {
                $differentData = array_map(function ($item) {
                    $dateTime = new DateTime($item["created_at"]);
                    $item["created_at"] = $dateTime->format("d/m/Y");
                    return $item;
                }, $differentData);

                $total = array_reduce($differentData, function ($acc, $item) {
                    $acc += $item["entry"];
                    return $acc;
                }, 0);

                $differentData = array_map(function ($item) {
                    $item["entry"] = "R$ " . number_format($item["entry"], 2, ",", ".");
                    return $item;
                }, $differentData);
            }

            $differentData = array_values($differentData);
            echo empty($differentData) ?
                json_encode(["success" => "todas as contas estão conciliadas"]) :
                json_encode(["data" => $differentData, "total" => "R$ " . number_format($total, 2, ",", ".")]);
        }
    }

    public function importOfxFile()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        if (empty(session()->user->company_id)) {
            echo json_encode(["error" => "selecione uma empresa antes de importar o arquivo"]);
            die;
        }

        $requestFiles = $this->getRequestFiles()->getAllFiles();
        $httpReferer = $this->getServer()->getServerByKey("HTTP_REFERER");
        $urlComponents = parse_url($httpReferer);

        if (!file_exists($requestFiles["ofxFile"]["tmp_name"]) && !is_readable($requestFiles["ofxFile"]["tmp_name"])) {
            http_response_code(400);
            echo json_encode(["error" => "o arquivo não existe ou não pode ser lido corretamente"]);
            die;
        }

        $fileContent = file_get_contents($requestFiles["ofxFile"]["tmp_name"]);
        if (empty($fileContent)) {
            http_response_code(400);
            echo json_encode(["error" => "o arquivo não pode estar vazio"]);
            die;
        }

        $parser = new Parser();
        $ofx = $parser->loadFromFile($requestFiles["ofxFile"]["tmp_name"]);

        $bankAccount = $ofx->bankAccounts[0];
        $transactions = $bankAccount->statement->transactions;

        if (empty($transactions)) {
            http_response_code(400);
            echo json_encode(["error" => "o arquivo de transações está vazio"]);
            die;
        }

        $allowKeys = ["amount", "memo", "date"];
        $responseUserAndCompany = initializeUserAndCompanyId();

        $cashFlow = new CashFlow();
        $cashFlow->id_user = $responseUserAndCompany["user_data"]->id;
        $cashFlow = new CashFlow();
            $cashFlowData = $cashFlow->findCashFlowToCompareAutomaticImportsFile(
                $responseUserAndCompany["user"],
                $responseUserAndCompany["company_id"],
                ["entry", "created_at", "history"]
            );

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
        } else {
            $cashFlowData = array_map(function ($item) {
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
            $differentDateData = array_udiff($transactions, $cashFlowData, function ($a, $b) {
                if ($a['date'] == $b['date']) {
                    return 0;
                }
                return ($a['date'] < $b['date']) ? -1 : 1;
            });

            $differentAmountData = array_udiff($transactions, $cashFlowData, function ($a, $b) {
                if ($a['amount'] == $b['amount']) {
                    return 0;
                }
                return ($a['amount'] < $b['amount']) ? -1 : 1;
            });

            $differentMemoData = array_udiff($transactions, $cashFlowData, function ($a, $b) {
                if ($a['memo'] == $b['memo']) {
                    return 0;
                }
                return ($a['memo'] < $b['memo']) ? -1 : 1;
            });

            $differentData = arrayWithMostItems($differentDateData, $differentAmountData, $differentMemoData);
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
        } else {
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
        $response = initializeUserAndCompanyId();
        $cashFlow = new CashFlow();
        $cashFlowDataByUser = $cashFlow->findCashFlowByUser([], $response["user"], $response["company_id"]);

        if (!empty($cashFlowDataByUser)) {
            $cashFlowDataByUser = array_map(function ($cashFlowData) {
                $cashFlowData->setEntry("R$ " . number_format($cashFlowData->getEntry(), 2, ",", "."));
                $cashFlowData->created_at = date("d/m/Y", strtotime($cashFlowData->created_at));
                $cashFlowData->entry_type_value = !empty($cashFlowData->entry_type) ? "Crédito" : "Débito";
                return $cashFlowData;
            }, $cashFlowDataByUser);
        }

        $cashFlow = new CashFlow();
        $balanceValue = $cashFlow->calculateBalance($response["user"], $response["company_id"]);
        $balance = !empty($balanceValue) ? 'R$ ' . number_format($balanceValue, 2, ',', '.') : 0;

        if (!empty($cashFlowDataByUser) && is_array($cashFlowDataByUser)) {
            $cashFlowDataByUser = array_reverse($cashFlowDataByUser);
        }

        echo $this->view->render("admin/manual-bank-reconciliation-cashflow", [
            "endpoints" => ["/admin/bank-reconciliation/cash-flow/manual"],
            "userFullName" => showUserFullName(),
            "cashFlowDataByUser" => $cashFlowDataByUser,
            "balance" => $balance,
            "balanceValue" => $balanceValue,
            "hasBalance" => true
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
