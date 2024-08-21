<?php

namespace Source\Controllers;

use DateTime;
use Source\Core\Controller;
use Source\Core\Model;
use Source\Domain\Model\HistoryAudit as ModelHistoryAudit;
use Source\Support\Message;

/**
 * HistoryAudit Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class HistoryAudit extends Controller
{
    /**
     * HistoryAudit constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function historyAuditBackup()
    {
        $responseUserAndCompany = initializeUserAndCompanyId();
        $historyAudit = new ModelHistoryAudit();

        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(["uuid", "action"])->getAllPostData();

            $historyAudit->setUuid($requestPost["uuid"]);
            $historyAuditData = $historyAudit->findHistoryAndAuditByUuid(["id", "deleted"]);

            $response = new \stdClass();
            $message = new Message();
            $message->error("Erro interno ao modificar o registro");
            $response->message = $message;
            $response->status = false;

            $verifyAction = [
                "restore" => function (Model $model) use ($response) {
                    $model->setRequiredFields(["deleted"]);
                    $model->deleted = 0;
                    $response->label = "restaurado";
                    $response->status = $model->save();
                },

                "delete" => function (Model $model) use ($response) {
                    $model->setRequiredFields(["deleted"]);
                    $response->label = "excluído";
                    $response->status = $model->destroy();
                }
            ];

            if (!empty($verifyAction[$requestPost["action"]])) {
                $verifyAction[$requestPost["action"]]($historyAuditData);
            }

            if (!$response->status) {
                http_response_code(500);
                echo $response->message->json();
                die;
            }

            echo json_encode(["success" => "registro {$response->label} com sucesso"]);
            die;
        }

        $historyAuditData = $historyAudit->findAllHistoryAndAuditJoinReportSystem(
            ["uuid", "history_transaction", "transaction_value", "created_at"],
            ["report_name"],
            [
                "user" => $responseUserAndCompany["user"],
                "company_id" => $responseUserAndCompany["company_id"],
                "deleted" => false
            ]
        );

        if (!empty($historyAuditData)) {
            $historyAuditData = array_map(function ($item) {
                $dateTime = new DateTime($item->created_at);
                $item->date_created_at = $dateTime->format("d/m/Y");
                $item->time_created_at = $dateTime->format("H:i:s");
                $item->transaction_value = "R$ " . number_format($item->transaction_value, 2, ",", ".");
                return $item;
            }, $historyAuditData);
        }

        echo $this->view->render("admin/history-audit-backup", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/history-audit/backup"],
            "historyAuditData" => $historyAuditData
        ]);
    }

    public function historyAuditRemove()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $requestPost = $this->getRequests()->setRequiredFields(["uuid"])->getAllPostData();
        $historyAudit = new ModelHistoryAudit();

        $historyAudit->setUuid($requestPost["uuid"]);
        $historyAuditData = $historyAudit->findHistoryAndAuditByUuid(["deleted", "id"]);

        if (empty($historyAuditData)) {
            http_response_code(500);
            echo json_encode(["error" => "este registro não existe"]);
            die;
        }

        $historyAuditData->setRequiredFields(["deleted"]);
        $historyAuditData->deleted = 1;

        if (!$historyAuditData->save()) {
            http_response_code(500);
            echo json_encode(["error" => "erro ao deletar o registro"]);
            die;
        }

        echo json_encode(["success" => "registro deletado com sucesso"]);
    }

    public function historyAuditReport()
    {
        $responseUserAndCompany = initializeUserAndCompanyId();
        $dateRange = $this->getRequests()->get("daterange");
        $params = [
            "user" => $responseUserAndCompany["user"],
            "company_id" => $responseUserAndCompany["company_id"],
            "deleted" => false
        ];

        if (!empty($dateRange)) {
            $dates = explode("-", $dateRange);
            $dates = array_map(function($date) {
                return preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $date);
            }, $dates);

            $params["date"] = [
                "date_ini" => $dates[0],
                "date_end" => $dates[1]
            ];
        }

        $historyAudit = new ModelHistoryAudit();
        $historyAuditData = $historyAudit->findAllHistoryAndAuditJoinReportSystem(
            ["uuid", "history_transaction", "transaction_value", "created_at"],
            ["report_name"],
            $params
        );

        if (!empty($historyAuditData)) {
            $historyAuditData = array_map(function ($item) {
                $dateTime = new DateTime($item->created_at);
                $item->date_created_at = $dateTime->format("d/m/Y");
                $item->time_created_at = $dateTime->format("H:i:s");
                $item->transaction_value = "R$ " . number_format($item->transaction_value, 2, ",", ".");
                return $item;
            }, $historyAuditData);
        }

        echo $this->view->render("admin/history-audit-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/history-audit/report"],
            "historyAuditData" => $historyAuditData
        ]);
    }
}
