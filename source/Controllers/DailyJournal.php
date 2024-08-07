<?php

namespace Source\Controllers;

use DateTime;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Core\Model;
use Source\Domain\Model\BalanceSheet;
use Source\Domain\Model\ChartOfAccount;

/**
 * DailyJournal Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class DailyJournal extends Controller
{
    /**
     * DailyJournal constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function dailyJournalReportBackup()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "uuid",
                    "action"
                ]
            )->getAllPostData();

            $balanceSheet = new BalanceSheet();
            $balanceSheet->setUuid($requestPost["uuid"]);
            $balanceSheetData = $balanceSheet->findBalanceSheetByUuid(["uuid", "id"]);

            if (empty($balanceSheetData)) {
                http_response_code(500);
                echo json_encode(["error" => "registro não encontrado"]);
                die;
            }

            $message = ["error" => "erro ao modificar o registro"];
            $verifyAction = [
                "restore" => function(Model $model) {
                    $model->setRequiredFields(["deleted"]);
                    $model->deleted = 0;
                    $response = $model->save();

                    if (!$response) {
                        http_response_code(500);
                        echo json_encode(["error" => "erro ao restaurar o registro"]);
                        die;
                    }

                    return ["success" => "registro restaurado com sucesso"];
                },

                "delete" => function (Model $model) {
                    $response = $model->destroy();

                    if (!$response) {
                        http_response_code(500);
                        echo json_encode(["error" => "erro ao deletar o registro"]);
                        die;
                    }

                    return ["success" => "registro deletado com sucesso"];
                }
            ];

            if (!empty($verifyAction[$requestPost["action"]])) {
                $message = $verifyAction[$requestPost["action"]]($balanceSheetData);
            }

            echo json_encode($message);
            die;
        }

        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $balanceSheet = new BalanceSheet();
        $dailyJournalData = $balanceSheet->findAllBalanceSheetJoinChartOfAccountAndJoinChartOfAccountGroup(
            [
                "uuid",
                "account_type",
                "history_account",
                "account_value",
                "created_at",
                "deleted"
            ],
            [
                "account_name"
            ],
            [
                "id"
            ],
            [
                "id_user" => $responseInitializeUserAndCompany["user_data"]->id,
                "id_company" => $responseInitializeUserAndCompany["company_id"]
            ]
        );

        if (!empty($dailyJournalData)) {
            $dailyJournalData = array_filter($dailyJournalData, function($item) {
                if (!empty($item->getDeleted())) {
                    return $item;
                }
            });

            $dailyJournalData = array_map(function($item) {
                $item->uuid = $item->getUuid();
                $item->account_value = "R$ " . number_format($item->account_value, 2, ",", ".");
                $item->account_type = empty($item->account_type) ? "Débito" : "Crédito";
                $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
                return (array) $item->data();
            }, $dailyJournalData);
        }

        echo $this->view->render("admin/daily-journal-backup", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/daily-journal/report/backup"],
            "dailyJournalData" => $dailyJournalData
        ]);
    }

    public function dailyJournalDelete()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $uuid = $this->getRequests()->setRequiredFields(["uuid"])->getPost("uuid");

        $balanceSheet = new BalanceSheet();
        $balanceSheet->setUuid($uuid);
        $balanceSheetData = $balanceSheet->findBalanceSheetByUuid(["id", "deleted"]);

        if (empty($balanceSheetData)) {
            http_response_code(500);
            echo json_encode(["error" => "registro não encontrado"]);
            die;
        }

        
        $balanceSheet = new BalanceSheet();
        $response = $balanceSheet->updateBalanceSheetDataByUuid([
            "uuid" => $uuid,
            "deleted" => 1
        ]);

        if (!$response) {
            http_response_code(500);
            echo $balanceSheet->message->json();
            die;
        }

        echo json_encode(["success" => "registro removido com sucesso"]);
    }

    public function dailyJournalUpdate(array $data)
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "createdAt",
                    "uuid",
                    "csrfToken",
                    "chartOfAccountSelect",
                    "accountType",
                    "accountValue",
                    "accountHistory"
                ]
            )->getAllPostData();

            if (preg_match("/[^\d\.,]+/", $requestPost["accountValue"])) {
                http_response_code(500);
                echo json_encode(["error" => "valor da conta inválido"]);
                die;
            }

            if (empty($responseInitializeUserAndCompany["company_id"])) {
                http_response_code(500);
                echo json_encode(["error" => "selecione uma empresa antes de atualizar algum registro"]);
                die;
            }

            if (!preg_match("/^\d{2}\/\d{2}\/\d{4}$/", $requestPost["createdAt"])) {
                http_response_code(500);
                echo json_encode(["error" => "campo data inválido"]);
                die;
            }

            if (!preg_match("/^\d+$/", $requestPost["accountType"])) {
                http_response_code(500);
                echo json_encode(["error" => "campo tipo de conta inválido"]);
                die;
            }

            if ($requestPost["accountType"] > 1) {
                http_response_code(500);
                echo json_encode(["error" => "campo tipo de conta inválido"]);
                die;
            }

            if ($requestPost["accountType"] < 0) {
                http_response_code(500);
                echo json_encode(["error" => "campo tipo de conta inválido"]);
                die;
            }

            if (strlen($requestPost["accountHistory"]) > 1000) {
                http_response_code(500);
                echo json_encode(["error" => "histórico da conta ultrapassa o limite de caracter"]);
                die;
            }

            $requestPost["accountValue"] = convertCurrencyRealToFloat($requestPost["accountValue"]);
            $requestPost["createdAt"] = preg_replace("/^(\d{2})\/(\d{2})\/(\d{4})$/", "$3-$2-$1", $requestPost["createdAt"]);

            $chartOfAccount = new ChartOfAccount();
            $chartOfAccount->setUuid($requestPost["chartOfAccountSelect"]);
            $chartOfAccountData = $chartOfAccount->findChartOfAccountByUuid(["id"]);

            if (empty($chartOfAccountData)) {
                http_response_code(500);
                echo json_encode(["error" => "conta não encontrada"]);
                die;
            }

            $balanceSheet = new BalanceSheet();
            $response = $balanceSheet->updateBalanceSheetDataByUuid(
                [
                    "uuid" => $requestPost["uuid"],
                    "id_user" => $responseInitializeUserAndCompany["user"],
                    "id_company" => $responseInitializeUserAndCompany["company_id"],
                    "id_chart_of_account" => $chartOfAccountData->id,
                    "account_type" => $requestPost["accountType"],
                    "account_value" => $requestPost["accountValue"],
                    "history_account" => $requestPost["accountHistory"],
                    "created_at" => $requestPost["createdAt"],
                    "updated_at" => (new DateTime())->format("Y-m-d")
                ]
            );

            if (!$response) {
                http_response_code(500);
                echo $balanceSheet->message->json();
                die;
            }

            echo json_encode(
                [
                    "success" => true,
                    "url" => url("/admin/balance-sheet/daily-journal/report")
                ]
            );
            die;
        }

        if (!Uuid::isValid($data["uuid"])) {
            redirect("/admin/balance-sheet/daily-journal/report");
        }

        $balanceSheet = new BalanceSheet();
        $balanceSheet->setUuid($data["uuid"]);
        $dailyJournal = $balanceSheet->findBalanceSheetByUuid([]);

        if (!empty($dailyJournal)) {
            $dailyJournal->account_value = number_format($dailyJournal->account_value, 2, ",", ".");
            $dailyJournal->created_at = (new DateTime($dailyJournal->created_at))->format("d/m/Y");
        }

        $chartOfAccount = new ChartOfAccount();
        $chartOfAccountData = $chartOfAccount->findAllChartOfAccount(
            [
                "account_number",
                "account_name",
                "id",
                "uuid"
            ],
            [
                "deleted" => 0,
                "id_user" => $responseInitializeUserAndCompany["user_data"]->id,
                "id_company" => $responseInitializeUserAndCompany["company_id"]
            ]
        );

        echo $this->view->render("admin/daily-journal-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/daily-journal/form"],
            "dailyJournal" => $dailyJournal,
            "chartOfAccountData" => $chartOfAccountData
        ]);
    }

    public function dailyJournalReport()
    {
        $balanceSheet = new BalanceSheet();
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

        $dailyJournalData = $balanceSheet->findAllBalanceSheetJoinChartOfAccountAndJoinChartOfAccountGroup(
            [
                "uuid",
                "account_type",
                "history_account",
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

        if (!empty($dailyJournalData)) {
            usort($dailyJournalData, function ($a, $b) {
                return strtotime($a->created_at) - strtotime($b->created_at);
            });

            $dailyJournalData = array_filter($dailyJournalData, function($item) {
                if (empty($item->getDeleted())) {
                    return $item;
                }
            });

            $dailyJournalData = array_map(function ($item) {
                $item->uuid = $item->getUuid();
                $item->account_value = "R$ " . number_format($item->account_value, 2, ",", ".");
                $item->account_type = empty($item->account_type) ? "Débito" : "Crédito";
                $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
                $item->account_name = $item->account_number . " " . $item->account_name;
                return (array) $item->data();
            }, $dailyJournalData);
        }

        echo $this->view->render("admin/daily-journal-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/daily-journal/report"],
            "dailyJournalData" => $dailyJournalData
        ]);
    }
}
