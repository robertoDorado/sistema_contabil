<?php

namespace Source\Controllers;

use DateTime;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\BalanceSheet;
use Source\Domain\Model\BalanceSheetExplanatoryNotes as ModelBalanceSheetExplanatoryNotes;
use Source\Models\BalanceSheetExplanatoryNotes as ModelsBalanceSheetExplanatoryNotes;

/**
 * BalanceSheetExplanatoryNotes Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class BalanceSheetExplanatoryNotes extends Controller
{
    /**
     * BalanceSheetExplanatoryNotes constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function balanceSheetExplanatoryNotesBackup()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "uuid",
                    "action"
                ]
            )->getAllPostData();

            $explanatoryNotesBalanceSheet = new ModelBalanceSheetExplanatoryNotes();
            $explanatoryNotesBalanceSheet->setUuid($requestPost["uuid"]);
            $explanatoryNotesBalanceSheetData = $explanatoryNotesBalanceSheet->findBalanceSheetExplanatoryNotesByUuid([]);

            if (empty($explanatoryNotesBalanceSheetData)) {
                http_response_code(400);
                echo json_encode(["error" => "este registro não existe"]);
                die;
            }

            $explanatoryNotesBalanceSheetData->setRequiredFields(["deleted"]);
            $verifyAction = [
                "restore" => function(ModelsBalanceSheetExplanatoryNotes $model): bool {
                    $model->deleted = 0;
                    return $model->save();
                },
                "delete" => function(ModelsBalanceSheetExplanatoryNotes $model): bool {
                    return $model->destroy();
                },
            ];

            $response = false;
            if (!empty($verifyAction[$requestPost["action"]])) {
                $response = $verifyAction[$requestPost["action"]]($explanatoryNotesBalanceSheetData);
            }

            if (!$response) {
                http_response_code(400);
                echo json_encode(["error" => "erro ao modificar o registro"]);
                die;
            }

            echo json_encode(["success" => "registro modificado com sucesso"]);
            die;
        }

        $explanatoryNotesBalanceSheet = new ModelBalanceSheetExplanatoryNotes();
        $explanatoryNotesBalanceSheetData = $explanatoryNotesBalanceSheet->findAllBalanceSheetExplanatoryNotes(
            [
                "uuid",
                "note",
                "deleted"
            ],
            [
                "account_value",
                "account_type"
            ],
            [
                "account_name",
                "account_number"
            ],
            $responseInitializeUserAndCompany["user"],
            $responseInitializeUserAndCompany["company_id"]
        );

        if (!empty($explanatoryNotesBalanceSheetData)) {
            $explanatoryNotesBalanceSheetData = array_filter($explanatoryNotesBalanceSheetData, function ($item) {
                if (!empty($item->getDeleted())) {
                    return $item;
                }
            });

            $explanatoryNotesBalanceSheetData = array_map(function ($item) {
                $item->account_value = "R$ " . number_format($item->account_value, 2, ",", ".");
                $item->account_name = $item->account_number . " " . $item->account_name;
                $item->account_type = empty($item->account_type) ? "Débito" : "Crédito";
                return $item;
            }, $explanatoryNotesBalanceSheetData);
        }

        echo $this->view->render("admin/balance-sheet-explanatory-notes-backup", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet-explanatory-notes/form/backup"],
            "explanatoryNotesBalanceSheetData" => $explanatoryNotesBalanceSheetData
        ]);
    }

    public function balanceSheetExplanatoryNotesRemove()
    {
        verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
        $requestPostUuid = $this->getRequests()->getPost("uuid");

        $explanatoryNotesBalanceSheet = new ModelBalanceSheetExplanatoryNotes();
        $explanatoryNotesBalanceSheet->setUuid($requestPostUuid);
        $explanatoryNotesBalanceSheetData = $explanatoryNotesBalanceSheet->findBalanceSheetExplanatoryNotesByUuid([]);
        $message = function (string $key, string $message, int $code = 200) {
            http_response_code($code);
            echo json_encode([$key => $message]);
        };

        if (empty($explanatoryNotesBalanceSheetData)) {
            $message("error", "este registro não existe", 500);
            die;
        }

        $explanatoryNotesBalanceSheetData->setRequiredFields(["deleted"]);
        $explanatoryNotesBalanceSheetData->deleted = 1;
        if (!$explanatoryNotesBalanceSheetData->save()) {
            $message("error", "erro ao deletar o registro", 500);
            die;
        }

        $message("success", "registro removido com sucesso");
    }

    public function balanceSheetExplanatoryNotesUpdate(array $data)
    {
        $redirectEndpoint = "/admin/balance-sheet-explanatory-notes/report";
        if (!Uuid::isValid($data["uuid"])) {
            redirect($redirectEndpoint);
        }

        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $searchBalanceSheetExplanatoryNotesData = function (string $uuid, $viewData = true) use ($responseInitializeUserAndCompany) {
            $explanatoryNotesBalanceSheet = new ModelBalanceSheetExplanatoryNotes();
            $params = [
                [
                    "id",
                    "note"
                ],
                [
                    "created_at",
                    "account_type",
                    "account_value",
                    "history_account"
                ],
                [
                    "account_number",
                    "account_name"
                ],
                $responseInitializeUserAndCompany["user"],
                $responseInitializeUserAndCompany["company_id"]
            ];

            $explanatoryNotesBalanceSheet->setUuid($uuid);
            return $viewData ? $explanatoryNotesBalanceSheet->findBalanceSheetExplanatoryNotesJoinDataByUuid(...$params) : 
            $explanatoryNotesBalanceSheet->findBalanceSheetExplanatoryNotesByUuid([]);
        };

        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "csrfToken",
                    "explanatoryNoteText",
                    "uuid"
                ]
            )->getAllPostData();

            $explanatoryNotesBalanceSheetData = $searchBalanceSheetExplanatoryNotesData($requestPost["uuid"], false);
            if (empty($explanatoryNotesBalanceSheetData)) {
                http_response_code(400);
                echo json_encode(["error" => "nota não encontrada"]);
                die;
            }

            $explanatoryNotesBalanceSheetData->setRequiredFields(["note"]);
            $explanatoryNotesBalanceSheetData->note = $requestPost["explanatoryNoteText"];
            if (!$explanatoryNotesBalanceSheetData->save()) {
                http_response_code(400);
                echo json_encode(["error" => "erro ao tentar atualizar a nota"]);
                die;
            }

            echo json_encode(["success" => true, "url" => $redirectEndpoint]);
            die;
        }

        $explanatoryNotesBalanceSheetData = $searchBalanceSheetExplanatoryNotesData($data["uuid"]);
        if (empty($explanatoryNotesBalanceSheetData)) {
            redirect($redirectEndpoint);
        }

        $explanatoryNotesBalanceSheetData->created_at = (new DateTime($explanatoryNotesBalanceSheetData->created_at))->format("d/m/Y");
        $explanatoryNotesBalanceSheetData->account_type = empty($explanatoryNotesBalanceSheetData->account_type) ? "Débito" : "Crédito";
        $explanatoryNotesBalanceSheetData->account_value = "R$ " . number_format($explanatoryNotesBalanceSheetData->account_value, 2, ",", ".");

        echo $this->view->render("admin/balance-sheet-explanatory-notes-update", [
            "userFullName" => showUserFullName(),
            "endpoints" => [],
            "explanatoryNotesBalanceSheetData" => $explanatoryNotesBalanceSheetData
        ]);
    }

    public function balanceSheetExplanatoryNotesForm()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $params = [
            [
                "id AS balance_sheet_id",
                "uuid",
                "history_account",
                "deleted",
                "created_at"
            ],
            [
                "account_name",
                "account_number"
            ],
            [
                "id"
            ],
            [
                "id_user" => $responseInitializeUserAndCompany["user_data"]->id,
                "id_company" => $responseInitializeUserAndCompany["company_id"]
            ]
        ];

        $formatAccountData = function ($item) {
            $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
            $item->account_data = $item->created_at . " " . $item->account_number . " " . $item->account_name . " " . $item->history_account;
            return $item;
        };

        $refreshBalanceSheetExplanatoryNotesData = function () use ($responseInitializeUserAndCompany) {
            $balanceSheetExplanatoryNotes = new ModelBalanceSheetExplanatoryNotes();
            $data = $balanceSheetExplanatoryNotes->findAllBalanceSheetExplanatoryNotes(
                [
                    "id AS id_explanatory_notes"
                ],
                [
                    "id AS id_balance_sheet"
                ],
                [
                    "id AS id_chart_of_account"
                ],
                $responseInitializeUserAndCompany["user"],
                $responseInitializeUserAndCompany["company_id"]
            );

            return array_reduce($data, function ($acc, $item) {
                $acc[] = $item->id_balance_sheet;
                return $acc;
            }, []);
        };

        $explanatoryNotesBalanceSheetData = $refreshBalanceSheetExplanatoryNotesData();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "csrfToken",
                    "explanatoryNoteText",
                    "balanceSheetSelectMultiple"
                ]
            )->getAllPostData();

            $requestPost["balanceSheetSelectMultiple"] = array_map(function ($uuid) {
                $balanceSheet = new BalanceSheet();
                $balanceSheet->setUuid($uuid);
                $balanceSheetData = $balanceSheet->findBalanceSheetByUuid([]);
                return $balanceSheetData->id;
            }, $requestPost["balanceSheetSelectMultiple"]);

            foreach ($requestPost["balanceSheetSelectMultiple"] as $balanceSheetId) {
                $balanceSheetExplanatoryNotes = new ModelBalanceSheetExplanatoryNotes();
                $response = $balanceSheetExplanatoryNotes->persistData([
                    "uuid" => Uuid::uuid4(),
                    "id_balance_sheet" => $balanceSheetId,
                    "note" => $requestPost["explanatoryNoteText"],
                    "deleted" => 0
                ]);

                if (empty($response)) {
                    http_response_code(400);
                    echo $balanceSheetExplanatoryNotes->message->json();
                    die;
                }
            }

            $balanceSheet = new BalanceSheet();
            $balanceSheetData = $balanceSheet->findAllBalanceSheetJoinChartOfAccountAndJoinChartOfAccountGroup(...$params);

            $balanceSheetData = array_filter($balanceSheetData, function ($item) {
                if (empty($item->getDeleted())) {
                    return $item;
                }
            });

            $explanatoryNotesBalanceSheetData = $refreshBalanceSheetExplanatoryNotesData();
            $optionsUpdated = array_filter($balanceSheetData, function ($item) use ($explanatoryNotesBalanceSheetData) {
                if (!in_array($item->balance_sheet_id, $explanatoryNotesBalanceSheetData)) {
                    return $item;
                }
            });

            $optionsUpdated = array_map($formatAccountData, $optionsUpdated);
            $optionsUpdated = array_reduce($optionsUpdated, function ($acc, $item) {
                $acc[] = [
                    "account_data" => $item->account_data,
                    "uuid" => $item->getUuid()
                ];
                return $acc;
            }, []);

            echo json_encode(["success" => "nota criada com sucesso", "options_updated" => $optionsUpdated]);
            die;
        }

        $balanceSheet = new BalanceSheet();
        $balanceSheetData = $balanceSheet->findAllBalanceSheetJoinChartOfAccountAndJoinChartOfAccountGroup(...$params);

        $balanceSheetData = array_filter($balanceSheetData, function ($item) use ($explanatoryNotesBalanceSheetData) {
            if (empty($item->getDeleted()) && !in_array($item->balance_sheet_id, $explanatoryNotesBalanceSheetData)) {
                return $item;
            }
        });

        $balanceSheetData = array_map($formatAccountData, $balanceSheetData);
        echo $this->view->render("admin/balance-sheet-explanatory-notes-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet-explanatory-notes/form"],
            "balanceSheetData" => $balanceSheetData
        ]);
    }

    public function balanceSheetExplanatoryNotesReport()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $explanatoryNotesBalanceSheet = new ModelBalanceSheetExplanatoryNotes();
        $explanatoryNotesBalanceSheetData = $explanatoryNotesBalanceSheet->findAllBalanceSheetExplanatoryNotes(
            [
                "uuid",
                "note",
                "deleted"
            ],
            [
                "account_value",
                "account_type"
            ],
            [
                "account_name",
                "account_number"
            ],
            $responseInitializeUserAndCompany["user"],
            $responseInitializeUserAndCompany["company_id"]
        );

        if (!empty($explanatoryNotesBalanceSheetData)) {
            $explanatoryNotesBalanceSheetData = array_filter($explanatoryNotesBalanceSheetData, function ($item) {
                if (empty($item->getDeleted())) {
                    return $item;
                }
            });

            $explanatoryNotesBalanceSheetData = array_map(function ($item) {
                $item->account_value = "R$ " . number_format($item->account_value, 2, ",", ".");
                $item->account_name = $item->account_number . " " . $item->account_name;
                $item->account_type = empty($item->account_type) ? "Débito" : "Crédito";
                return $item;
            }, $explanatoryNotesBalanceSheetData);
        }


        echo $this->view->render("admin/balance-sheet-explanatory-notes-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet-explanatory-notes/report"],
            "explanatoryNotesBalanceSheetData" => $explanatoryNotesBalanceSheetData
        ]);
    }
}
