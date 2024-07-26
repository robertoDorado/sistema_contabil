<?php

namespace Source\Controllers;

use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\BalanceSheet;
use Source\Domain\Model\ChartOfAccount;

/**
 * BalanceSheetOverview Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class BalanceSheetOverView extends Controller
{
    /**
     * BalanceSheetOverview constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function balanceSheetForm()
    {
        $responseInitalizeUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            verifyRequestHttpOrigin($this->getServer()->getServerByKey("HTTP_ORIGIN"));
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    "accountType",
                    "accountHistory",
                    "accountValue",
                    "createdAt",
                    "chartOfAccountSelect",
                    "csrfToken"
                ]
            )->getAllPostData();


            $errorMessage = function (string $message) {
                http_response_code(500);
                echo json_encode(["error" => $message]);
                die;
            };

            if (empty($responseInitalizeUserAndCompany["company_id"])) {
                $errorMessage("selecione uma empresa antes de fazer um lançamento");
            }

            $accountTypeMessage = "tipo de conta inválida";
            if (!preg_match("/^\d{1}$/", $requestPost["accountType"])) {
                $errorMessage($accountTypeMessage);
            }

            if ($requestPost["accountType"] > 1) {
                $errorMessage($accountTypeMessage);
            }

            if (strlen($requestPost["accountHistory"]) > 1000) {
                $errorMessage("limite de caracteres ultrapassado");
            }

            $requestPost["accountValue"] = convertCurrencyRealToFloat($requestPost["accountValue"]);
            $requestPost["createdAt"] = preg_replace("/^(\d{2})\/(\d{2})\/(\d{4})$/", "$3-$2-$1", $requestPost["createdAt"]);

            $chartOfAccount = new ChartOfAccount();
            $chartOfAccount->setUuid($requestPost["chartOfAccountSelect"]);
            $chartOfAccountData = $chartOfAccount->findChartOfAccountByUuid(["id"]);

            if (empty($chartOfAccountData)) {
                $errorMessage("plano de contas inexistente");
            }

            $balanceSheet = new BalanceSheet();
            $response = $balanceSheet->persistData([
                "uuid" => Uuid::uuid4(),
                "id_company" => $responseInitalizeUserAndCompany["company_id"],
                "id_user" => $responseInitalizeUserAndCompany["user"],
                "id_chart_of_account" => $chartOfAccountData->id,
                "account_type" => $requestPost["accountType"],
                "account_value" => $requestPost["accountValue"],
                "history_account" => $requestPost["accountHistory"],
                "created_at" => $requestPost["createdAt"],
                "updated_at" => date("Y-m-d"),
                "deleted" => 0
            ]);

            if (!$response) {
                $errorMessage($balanceSheet->message->json());
            }

            echo json_encode(["success" => "registro criado com sucesso"]);
            die;
        }

        $chartOfAccount = new ChartOfAccount();
        $chartOfAccountData = $chartOfAccount->findAllChartOfAccount(
            [
                "uuid",
                "account_name",
                "account_number"
            ],
            [
                "id_user" => $responseInitalizeUserAndCompany["user_data"]->id,
                "id_company" => $responseInitalizeUserAndCompany["company_id"],
                "deleted" => 0
            ]
        );

        if (!empty($chartOfAccountData)) {
            $chartOfAccountData = array_filter($chartOfAccountData, function ($item) {
                if (!preg_match("/^\d+\.\d+\.\d+$/", $item->account_number)) {
                    return $item;
                }
            });
        }

        echo $this->view->render("admin/balance-sheet-form", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/balance-sheet-overview/form"],
            "chartOfAccountData" => $chartOfAccountData
        ]);
    }
}
