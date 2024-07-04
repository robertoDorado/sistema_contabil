<?php

namespace Source\Controllers;

use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\CashFlow;
use Source\Domain\Model\CashFlowExplanatoryNotes as ModelCashFlowExplanatoryNotes;
use Source\Domain\Model\User;

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

    public function cashFlowExplanatoryNotesReport()
    {
        $response = initializeUserAndCompanyId();
        $cashFlowExplanatoryNotes = new ModelCashFlowExplanatoryNotes();
        $cashFlowExplanatoryNotesData = $cashFlowExplanatoryNotes->findCashFlowExplanatoryNotesJoinCashFlow(
            ["note", "uuid"],
            ["entry", "history"],
            $response["user"],
            $response["company_id"],
            false
        );

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
                    "note" => $requestPost["explanatoryNoteText"]
                ]);

                if (empty($response)) {
                    http_response_code(500);
                    echo $cashFlowExplanatoryNotes->message->json();
                    die;
                }
            }


            echo json_encode(["success" => "nota criada com sucesso"]);
            die;
        }

        $user = new User();
        $user->setEmail(session()->user->user_email);
        $userData = $user->findUserByEmail(["id", "deleted"]);

        $user->setId($userData->id);
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;

        $cashFlow = new CashFlow();
        $cashFlowData = $cashFlow->findCashFlowByUser(["history", "uuid"], $user, $companyId);

        echo $this->view->render("admin/cash-flow-explanatory-notes-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/cash-flow-explanatory-notes/form"],
            "cashFlowData" => $cashFlowData
        ]);
    }
}
