<?php

namespace Source\Controllers;

use DateTime;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
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

    public function dailyJournalUpdate(array $data)
    {
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

        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $chartOfAccount = new ChartOfAccount();
        $chartOfAccountData = $chartOfAccount->findAllChartOfAccount(
            [
                "account_number",
                "account_name",
                "id"
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

        $dateTime = new DateTime();
        $dateRange = empty($this->getRequests()->get("daterange")) ?
            $dateTime->format("01/01/Y") . "-" . $dateTime->format("t/12/Y") : $this->getRequests()->get("daterange");

        $date = explode("-", $dateRange);
        $date = array_map(function ($item) {
            return preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $item);
        }, $date);

        $dailyJournalData = $balanceSheet->findAllBalanceSheet(
            [
                "uuid",
                "account_type",
                "history_account",
                "account_value",
                "created_at"
            ],
            [
                "account_name"
            ],
            [
                "account_name AS account_name_group"
            ],
            [
                "id_user" => $responseInitializeUserAndCompany["user_data"]->id,
                "id_company" => $responseInitializeUserAndCompany["company_id"],
                "deleted" => 0,
                "date" => $date
            ]
        );

        if (!empty($dailyJournalData)) {
            usort($dailyJournalData, function ($a, $b) {
                return strtotime($a->created_at) - strtotime($b->created_at);
            });

            $dailyJournalData = array_map(function ($item) {
                $item->uuid = $item->getUuid();
                $item->account_value = "R$ " . number_format($item->account_value, 2, ",", ".");
                $item->account_type = empty($item->account_type) ? "Débito" : "Crédito";
                $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
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
