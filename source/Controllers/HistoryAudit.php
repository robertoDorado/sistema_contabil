<?php

namespace Source\Controllers;

use DateTime;
use Source\Core\Controller;
use Source\Domain\Model\HistoryAudit as ModelHistoryAudit;

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

    public function historyAuditReport()
    {
        $response = initializeUserAndCompanyId();
        $historyAudit = new ModelHistoryAudit();
        $historyAuditData = $historyAudit->findAllHistoryAndAuditJoinReportSystem(
            ["uuid", "history_transaction", "transaction_value", "created_at"],
            ["report_name"],
            $response["user"],
            $response["company_id"],
            false
        );

        if (!empty($historyAuditData)) {
            $dateTime = new DateTime();
            $historyAuditData = array_map(function ($item) use ($dateTime) {
                $item->created_at = $dateTime->format("Y-m-d H:i:s");
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
