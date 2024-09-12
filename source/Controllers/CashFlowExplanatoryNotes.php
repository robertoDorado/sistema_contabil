<?php

namespace Source\Controllers;

use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\CashFlowExplanatoryNotes as ModelCashFlowExplanatoryNotes;
use Source\Models\CashFlowExplanatoryNotes as ModelsCashFlowExplanatoryNotes;
use Source\Support\Message;

/**
 * CashFlowExplanatoryNotes Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class CashFlowExplanatoryNotes extends Controller
{
    /**
     * CashFlowExplanatoryNotes constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function cashFlowExplanatoryNotesBackup()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields([
                "csrfToken",
                "action",
                "uuid"
            ])->getAllPostData();
            
            $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
            $cashFlowExplanatoryNotes->setUuid($requestPost["uuid"]);
            $noteParams = ["id", "deleted"];
            $cashFlowExplanatoryNotesData = $cashFlowExplanatoryNotes->findCashFlowExplanatoryNotesByUuid($noteParams, true);

            if (empty($cashFlowExplanatoryNotesData)) {
                http_response_code(400);
                echo json_encode(["error" => "este registro não existe"]);
                die;
            }

            $response = new \stdClass();
            $message = new Message();
            $response->message = $message;
            $response->message->error("nenhuma modificação foi feita");
            $response->modify = false;

            $verifyAction = [
                "delete" => function(ModelsCashFlowExplanatoryNotes $model) use ($message) {
                    $response = new \stdClass();
                    $response->modify = true;

                    if (!$model->destroy()) {
                        $message->error("erro ao tentar restaurar o registro");
                        $response->message = $message;
                        $response->modify = false;
                    }
                    return $response;
                },
                "restore" => function(ModelsCashFlowExplanatoryNotes $model) use ($message, $noteParams): object {
                    $model->setRequiredFields($noteParams);
                    $model->deleted = 0;
                    $response = new \stdClass();
                    $response->modify = true;
                    
                    if (!$model->save()) {
                        $message->error("erro ao tentar restaurar o registro");
                        $response->message = $message;
                        $response->modify = false;
                    }
                    return $response;
                }
            ];

            if (!empty($verifyAction[$requestPost["action"]])) {
                $response = $verifyAction[$requestPost["action"]]($cashFlowExplanatoryNotesData);
            }

            if (!$response->modify) {
                http_response_code(400);
                echo $response->message->json();
                die;
            }

            echo json_encode(["success" => "registro modificado com sucesso"]);
            die;
        }

        $response = initializeUserAndCompanyId();
        $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
        $cashFlowExplanatoryNotesData = $cashFlowExplanatoryNotes->findCashFlowExplanatoryNotesJoinCashFlow(
            ["note", "uuid"],
            ["entry", "history", "entry_type"],
            $response["user"],
            $response["company_id"],
            true
        );

        if (!empty($cashFlowExplanatoryNotesData)) {
            $cashFlowExplanatoryNotesData = array_map(function($item) {
                $item->entry_type = empty($item->entry_type) ? "Débito" : "Crédito";
                return $item;
            }, $cashFlowExplanatoryNotesData);
        }

        echo $this->view->render("admin/cash-flow-explanatory-notes-backup", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-explanatory-notes/backup"],
            "cashFlowExplanatoryNotesData" => $cashFlowExplanatoryNotesData
        ]);
    }

    public function cashFlowExplanatoryNotesRemove()
    {
        $requestPost = $this->getRequests()->setRequiredFields(["uuid"])
            ->getAllPostData();

        if (!Uuid::uuid4($requestPost["uuid"])) {
            http_response_code(400);
            echo json_encode(["error" => "uuid inválido"]);
            die;
        }

        $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
        $cashFlowExplanatoryNotes->setUuid($requestPost["uuid"]);
        $cashFlowExplanatoryNotesData = $cashFlowExplanatoryNotes->findCashFlowExplanatoryNotesByUuid([], false);

        if (empty($cashFlowExplanatoryNotesData)) {
            http_response_code(400);
            echo json_encode(["error" => "este registro não existe"]);
            die;
        }

        $cashFlowExplanatoryNotesData->setRequiredFields(["deleted"]);
        $cashFlowExplanatoryNotesData->deleted = 1;
        $cashFlowExplanatoryNotesData->save();
        echo json_encode(["success" => "registro deletado com sucesso"]);
    }

    public function cashFlowExplanatoryNotesUpdate(array $data)
    {
        $response = initializeUserAndCompanyId();
        $endpointReport = "/admin/cash-flow-explanatory-notes/report";

        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields([
                "explanatoryNoteText",
                "csrfToken",
                "uuid"
            ])->getAllPostData();

            if (!Uuid::isValid($requestPost["uuid"])) {
                http_response_code(400);
                echo json_encode(["error" => "uuid inválido"]);
                die;
            }

            $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
            $cashFlowExplanatoryNotes->setUuid($data["uuid"]);
            $cashFlowExplanatoryNotesData = $cashFlowExplanatoryNotes->findCashFlowExplanatoryNotesByUuid([], false);

            if (empty($cashFlowExplanatoryNotesData)) {
                http_response_code(400);
                echo json_encode(["error" => "este registro não existe"]);
                die;
            }

            $cashFlowExplanatoryNotesData->setRequiredFields(["note"]);
            $cashFlowExplanatoryNotesData->note = $requestPost["explanatoryNoteText"];
            $cashFlowExplanatoryNotesData->save();
            echo json_encode(["success" => url($endpointReport)]);
            die;
        }

        if (!Uuid::isValid($data["uuid"])) {
            redirect($endpointReport);
        }

        $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
        $cashFlowExplanatoryNotes->setUuid($data["uuid"]);
        $cashFlowExplanatoryNotesData = $cashFlowExplanatoryNotes->findCashFlowExplanatoryNotesJoinCashFlowByUuid(
            ["note", "uuid"],
            ["history", "entry", "entry_type"],
            $response["user"],
            $response["company_id"],
            false
        );

        if (empty($cashFlowExplanatoryNotesData)) {
            redirect($endpointReport);
        }

        $entryType = empty($cashFlowExplanatoryNotesData->entry_type) ? "Débito" : "Crédito";
        echo $this->view->render("admin/cash-flow-explanatory-notes-form-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => [],
            "dataNote" => $cashFlowExplanatoryNotesData->getNote() ?? null,
            "history" => $cashFlowExplanatoryNotesData->history ?? null,
            "entry" => "R$ " . number_format($cashFlowExplanatoryNotesData->entry, 2, ",", ".") ?? null,
            "entryType" => $entryType ?? null
        ]);
    }

    public function cashFlowExplanatoryNotesReport()
    {
        $response = initializeUserAndCompanyId();
        $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
        $cashFlowExplanatoryNotesData = $cashFlowExplanatoryNotes->findCashFlowExplanatoryNotesJoinCashFlow(
            ["note", "uuid"],
            ["entry", "history", "entry_type"],
            $response["user"],
            $response["company_id"],
            false
        );

        if (!empty($cashFlowExplanatoryNotesData)) {
            $cashFlowExplanatoryNotesData = array_map(function ($item) {
                $item->entry_type = !empty($item->entry_type) ? "Crédito" : "Débito";
                return $item;
            }, $cashFlowExplanatoryNotesData);
        }

        echo $this->view->render("admin/cash-flow-explanatory-notes-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-explanatory-notes/report"],
            "cashFlowExplanatoryNotesData" => $cashFlowExplanatoryNotesData
        ]);
    }

    public function cashFlowExplanatoryNotesForm()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "csrfToken",
                    "explanatoryNoteText",
                    "cashFlowSelectMultiple"
                ]
            )->getAllPostData();

            $requestPost["cashFlowSelectMultiple"] = array_map(function ($uuid) {
                $cashFlow = new CashFlow();
                $cashFlow->setUuid($uuid);
                $cashFlowData = $cashFlow->findCashFlowByUuid();
                return $cashFlowData->id;
            }, $requestPost["cashFlowSelectMultiple"]);

            foreach ($requestPost["cashFlowSelectMultiple"] as $cashFlowId) {
                $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
                $response = $cashFlowExplanatoryNotes->persistData([
                    "uuid" => Uuid::uuid4(),
                    "id_cash_flow" => $cashFlowId,
                    "note" => $requestPost["explanatoryNoteText"],
                    "deleted" => 0
                ]);

                if (empty($response)) {
                    http_response_code(400);
                    echo $cashFlowExplanatoryNotes->message->json();
                    die;
                }
            }

            $response = initializeUserAndCompanyId();
            $cashFlow = new CashFlow();
            $cashFlowData = $cashFlow->findCashFlowByUser(
                ["history", "uuid", "id"],
                $response["user"],
                $response["company_id"]
            );

            $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
            $cashFlowExplanatoryNotesData = $cashFlowExplanatoryNotes->findAllCashFlowExplanatoryNotes(
                ["id_cash_flow"],
                ["id"],
                $response["user"],
                $response["company_id"]
            );

            $cashFlowExplanatoryNotesData = array_map(function ($item) {
                return $item->data();
            }, $cashFlowExplanatoryNotesData);

            $cashFlowExplanatoryNotesData = array_reduce($cashFlowExplanatoryNotesData, function ($carry, $item) {
                $carry[] = $item->id_cash_flow;
                return $carry;
            }, []);

            $cashFlowData = array_filter($cashFlowData, function ($item) use ($cashFlowExplanatoryNotesData) {
                if (!in_array($item->id, $cashFlowExplanatoryNotesData)) {
                    return $item;
                }
            });

            $cashFlowData = array_map(function($item) {
                $item->history = $item->getHistory();
                $item->uuid = $item->getUuid();
                return (array)$item->data();
            }, $cashFlowData);

            $cashFlowData = array_values($cashFlowData);
            echo json_encode(["success" => "nota criada com sucesso", "options_updated" => $cashFlowData]);
            die;
        }

        $response = initializeUserAndCompanyId();
        $cashFlow = new CashFlow();
        $cashFlowData = $cashFlow->findCashFlowByUser(
            ["history", "uuid", "id"],
            $response["user"],
            $response["company_id"]
        );

        $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
        $cashFlowExplanatoryNotesData = $cashFlowExplanatoryNotes->findAllCashFlowExplanatoryNotes(
            ["id_cash_flow"],
            ["id"],
            $response["user"],
            $response["company_id"]
        );

        if (!empty($cashFlowExplanatoryNotesData)) {
            $cashFlowExplanatoryNotesData = array_map(function ($item) {
                return $item->data();
            }, $cashFlowExplanatoryNotesData);

            $cashFlowExplanatoryNotesData = array_reduce($cashFlowExplanatoryNotesData, function ($carry, $item) {
                $carry[] = $item->id_cash_flow;
                return $carry;
            }, []);

            $cashFlowData = array_filter($cashFlowData, function ($item) use ($cashFlowExplanatoryNotesData) {
                if (!in_array($item->id, $cashFlowExplanatoryNotesData)) {
                    return $item;
                }
            });
        }

        echo $this->view->render("admin/cash-flow-explanatory-notes-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-explanatory-notes/form"],
            "cashFlowData" => $cashFlowData
        ]);
    }
}
