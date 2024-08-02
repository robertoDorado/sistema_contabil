<?php

namespace Source\Controllers;

use DateTime;
use Source\Core\Controller;
use Source\Domain\Model\BalanceSheet;

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
            $dailyJournalData = array_map(function($item) {
                $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
                $item->account_value = "R$ " . number_format($item->account_value, 2, ",", ".");
                $item->account_type = empty($item->account_type) ? "Débito" : "Crédito";
                return $item;
            }, $dailyJournalData);
        }

        echo $this->view->render("admin/daily-journal-report", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/balance-sheet/daily-journal/report"],
            "dailyJournalData" => $dailyJournalData
        ]);
    }
}
