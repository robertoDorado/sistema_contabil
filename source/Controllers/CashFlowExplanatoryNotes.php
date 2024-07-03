<?php

namespace Source\Controllers;

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

    public function cashFlowExplanatoryNotesForm()
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "csrfToken",
                    "explanatoryNoteText", 
                    "cashFlowSelectMultiple"
                ]
            )->getAllPostData();
            echo json_encode($requestPost);
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
