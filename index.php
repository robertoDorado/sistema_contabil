<?php

ob_start();

require __DIR__ . '/vendor/autoload.php';

use Source\Core\MyRouter;

setlocale(LC_ALL, 'en_US.UTF-8');
date_default_timezone_set("America/Sao_Paulo");

$path = "./Logs/error.log";

if (!file_exists($path)) {
    mkdir("Logs");
    file_put_contents($path, '');
}

error_reporting(E_ALL & (~E_NOTICE | ~E_USER_NOTICE));
ini_set('error_log', $path);
ini_set('log_errors', true);

$verifyGlobalEndpoints = [
    "/admin/logout",
    "/customer/subscribe",
    "/customer/subscription/thanks-purchase"
];

if (empty($_POST["request"]) && !empty($_SERVER["REDIRECT_URL"]) && !in_array($_SERVER["REDIRECT_URL"], $verifyGlobalEndpoints)) {
    if (empty(session()->user)) {
        redirect("/admin/login");
    } else if ($_SERVER["REDIRECT_URL"] != "/admin/company/register") {

        if (!userHasCompany()) {
            redirect("/admin/warning/empty-company");
        }

        $customer = new \Source\Domain\Model\Customer();
        $customer->email = session()->user->user_email;
        $customerData = $customer->findCustomerByEmail();

        if (empty($customerData)) {
            redirect("/admin/login");
        }

        if (!empty($customerData->getDeleted())) {
            redirect("/admin/login");
        }

        $allowEndpointsCashVariation = ["/admin/cash-variation-setting/backup", "/admin/cash-variation-setting/report"];
        if (!in_array($_SERVER["REDIRECT_URL"], $allowEndpointsCashVariation)) {
            if (!empty(session()->account_group_variation) && !empty(session()->account_group_variation_id)) {
                session()->unset("account_group_variation");
                session()->unset("account_group_variation_id");
            }
        }
    }
}

$route = new MyRouter(url(), "::");

/**
 * Site Route
 */
$module = null;
$route->namespace("Source\Controllers");
$route->group($module);
$route->get("/", "Site::index");

/**
 * Admin Route
 */
$module = "admin";
$route->namespace("Source\Controllers");
$route->group($module);
$route->get("/", "Site::admin");

/**
 * Admin Login
 */
$route->get("/login", "Login::login");
$route->post("/login", "Login::login");
$route->post("/logout", "Login::logout");

/**
 * Admin fluxo de caixa
 */
$route->get("/cash-flow/report", "CashFlow::cashFlowReport");
$route->get("/cash-flow/form", "CashFlow::cashFlowForm");
$route->post("/cash-flow/form", "CashFlow::cashFlowForm");
$route->get("/cash-flow/update/form/{uuid}", "CashFlow::cashFlowUpdateForm");
$route->post("/cash-flow/update/form/{uuid}", "CashFlow::cashFlowUpdateForm");
$route->post("/cash-flow/remove/{uuid}", "CashFlow::cashFlowRemoveRegister");
$route->post("/cash-flow/import-excel", "CashFlow::importExcelFile");
$route->get("/cash-flow/backup/report", "CashFlow::cashFlowBackupReport");
$route->post("/cash-flow/modify/{uuid}", "CashFlow::cashFlowModifyData");

/**
 * Admin grupo fluxo de caixa
 */
$route->get("/cash-flow-group/form", "CashFlowGroup::cashFlowGroupForm");
$route->post("/cash-flow-group/form", "CashFlowGroup::cashFlowGroupForm");
$route->get("/cash-flow-group/report", "CashFlowGroup::cashFlowGroupReport");
$route->get("/cash-flow-group/update/form/{uuid}", "CashFlowGroup::cashFlowGroupFormUpdate");
$route->post("/cash-flow-group/update/form/{uuid}", "CashFlowGroup::cashFlowGroupFormUpdate");
$route->post("/cash-flow-group/remove/{uuid}", "CashFlowGroup::cashFlowGroupRemoveRegister");
$route->get("/cash-flow-group/backup/report", "CashFlowGroup::cashFlowGroupBackupReport");
$route->post("/cash-flow-group/modify/{uuid}", "CashFlowGroup::cashFlowGroupModiFyData");

/**
 * Admin Cliente
 */
$route->get("/customer/update-data/form", "Customer::updateDataCustomerForm");
$route->post("/customer/update-data/form", "Customer::updateDataCustomerForm");
$route->get("/customer/cancel-subscription", "Customer::cancelSubscription");

/**
 * Admin empresa
 */
$route->get("/company/register", "Company::companyRegister");
$route->post("/company/register", "Company::companyRegister");
$route->post("/company/sesssion", "Company::companySession");
$route->get("/company/report", "Company::companyReport");
$route->get("/company/update/form/{uuid}", "Company::companyFormUpdate");
$route->post("/company/update/form", "Company::companyFormUpdate");
$route->post("/company/delete", "Company::companyDeleteRegister");
$route->get("/company/backup/report", "Company::companyBackupReport");
$route->post("/company/modify", "Company::companyModifyData");
$route->get("/warning/empty-company", "Company::warningEmptyCompany");

/**
 * Conciliação bancária
 */
$route->get("/bank-reconciliation/cash-flow/automatic", "BankReconciliation::automaticReconciliationCashFlow");
$route->get("/bank-reconciliation/cash-flow/manual", "BankReconciliation::manualReconciliationCashFlow");
$route->post("/bank-reconciliation/cash-flow/import-ofx-file", "BankReconciliation::importOfxFile");
$route->post("/bank-reconciliation/cash-flow/import-excel-file", "BankReconciliation::importExcelFile");

/**
 * Análises e indicadores
 */
$route->get("/analyzes-and-indicators/cash-flow/charts-and-visualizations", "AnalyzesAndIndicators::charts");
$route->get("/analyzes-and-indicators/cash-flow/chart-line-data/pooled-cash-flow", "AnalyzesAndIndicators::findCashFlowDataForChartLinePooledChasFlow");
$route->get("/analyzes-and-indicators/cash-flow/chart-pie-data/account-grouping-count", "AnalyzesAndIndicators::findCashFlowDataForChartPieAccountGroupingCount");
$route->get("/analyzes-and-indicators/cash-flow/chart-bar-data/monthly-cash-flow-comparison", "AnalyzesAndIndicators::findChasFlowDataForBarChartMonthlyCashFlowComparasion");
$route->get("/analyzes-and-indicators/cash-flow/chart-bar-data/expenses-by-account-group", "AnalyzesAndIndicators::findChasFlowDataForBarChartExpensesByAccountGroup");
$route->get("/analyzes-and-indicators/cash-flow/financial-indicators", "AnalyzesAndIndicators::financialIndicators");
$route->get("/analyzes-and-indicators/cash-flow/cash-flow-projections", "AnalyzesAndIndicators::cashFlowProjections");

/**
 * Config. variação de fluxo de caixa
 */
$route->get("/cash-variation-setting/form", "CashVariationSetting::cashVariationForm");
$route->post("/cash-variation-setting/form", "CashVariationSetting::cashVariationForm");
$route->get("/cash-variation-setting/report", "CashVariationSetting::cashVariationReport");
$route->post("/cash-variation-setting/report", "CashVariationSetting::cashVariationReport");
$route->get("/cash-variation-setting/form-update/{uuid}", "CashVariationSetting::cashVariationFormUpdate");
$route->post("/cash-variation-setting/form-update", "CashVariationSetting::cashVariationFormUpdate");
$route->post("/cash-variation-setting/remove", "CashVariationSetting::cashVariationRemoveData");
$route->post("/cash-variation-setting/backup", "CashVariationSetting::cashVariationBackupReport");
$route->get("/cash-variation-setting/backup", "CashVariationSetting::cashVariationBackupReport");

/**
 * Planejamento de caixa
 */
$route->get("/cash-planning/cash-flow/cash-budget", "CashPlanning::cashBudget");
$route->get("/cash-planning/cash-flow/cash-variation-analysis", "CashPlanning::cashVariationAnalysis");

/**
 * Notas explicativas do fluxo de caixa
 */
$route->get("/cash-flow-explanatory-notes/form", "CashFlowExplanatoryNotes::cashFlowExplanatoryNotesForm");
$route->post("/cash-flow-explanatory-notes/form", "CashFlowExplanatoryNotes::cashFlowExplanatoryNotesForm");
$route->get("/cash-flow-explanatory-notes/report", "CashFlowExplanatoryNotes::cashFlowExplanatoryNotesReport");
$route->get("/cash-flow-explanatory-notes/form/update/{uuid}", "CashFlowExplanatoryNotes::cashFlowExplanatoryNotesUpdate");
$route->post("/cash-flow-explanatory-notes/form/update", "CashFlowExplanatoryNotes::cashFlowExplanatoryNotesUpdate");
$route->post("/cash-flow-explanatory-notes/remove", "CashFlowExplanatoryNotes::cashFlowExplanatoryNotesRemove");
$route->get("/cash-flow-explanatory-notes/backup", "CashFlowExplanatoryNotes::cashFlowExplanatoryNotesBackup");
$route->post("/cash-flow-explanatory-notes/backup", "CashFlowExplanatoryNotes::cashFlowExplanatoryNotesBackup");

/**
 * Notas Explicativas do balanço patrimonial
 */
$route->get("/balance-sheet-explanatory-notes/report", "BalanceSheetExplanatoryNotes::balanceSheetExplanatoryNotesReport");
$route->get("/balance-sheet-explanatory-notes/form", "BalanceSheetExplanatoryNotes::balanceSheetExplanatoryNotesForm");
$route->post("/balance-sheet-explanatory-notes/form", "BalanceSheetExplanatoryNotes::balanceSheetExplanatoryNotesForm");
$route->get("/balance-sheet-explanatory-notes/form/update/{uuid}", "BalanceSheetExplanatoryNotes::balanceSheetExplanatoryNotesUpdate");
$route->post("/balance-sheet-explanatory-notes/form/update", "BalanceSheetExplanatoryNotes::balanceSheetExplanatoryNotesUpdate");
$route->post("/balance-sheet-explanatory-notes/form/remove", "BalanceSheetExplanatoryNotes::balanceSheetExplanatoryNotesRemove");
$route->get("/balance-sheet-explanatory-notes/form/backup", "BalanceSheetExplanatoryNotes::balanceSheetExplanatoryNotesBackup");
$route->post("/balance-sheet-explanatory-notes/form/backup", "BalanceSheetExplanatoryNotes::balanceSheetExplanatoryNotesBackup");

/**
 * Histórico e Auditoria
 */
$route->get("/history-audit/report", "HistoryAudit::historyAuditReport");
$route->get("/history-audit/backup", "HistoryAudit::historyAuditBackup");
$route->post("/history-audit/backup", "HistoryAudit::historyAuditBackup");
$route->post("/history-audit/remove", "HistoryAudit::historyAuditRemove");

/**
 * Balanço Patrimonial - Plano de contas
 */
$route->get("/balance-sheet/chart-of-account", "BalanceSheet::chartOfAccount");
$route->post("/balance-sheet/chart-of-account", "BalanceSheet::chartOfAccount");
$route->post("/balance-sheet/export-model-chart-of-account", "BalanceSheet::exportChartOfAccountModelExcelFile");
$route->get("/balance-sheet/chart-of-account/update/{uuid}", "BalanceSheet::chartOfAccountFormUpdate");
$route->post("/balance-sheet/chart-of-account/update", "BalanceSheet::chartOfAccountFormUpdate");
$route->post("/balance-sheet/chart-of-account/delete", "BalanceSheet::chartOfAccountFormDelete");
$route->post("/balance-sheet/chart-of-account/import-file", "BalanceSheet::chartOfAccountImportFile");
$route->get("/balance-sheet/chart-of-account/backup", "BalanceSheet::chartOfAccountBackup");
$route->post("/balance-sheet/chart-of-account/backup", "BalanceSheet::chartOfAccountBackup");
$route->get("/balance-sheet/chart-of-account-group", "BalanceSheet::chartOfAccountGroup");
$route->get("/balance-sheet/chart-of-account-group/update/{uuid}", "BalanceSheet::chartOfAccountGroupUpdate");
$route->post("/balance-sheet/chart-of-account-group/update", "BalanceSheet::chartOfAccountGroupUpdate");
$route->get("/balance-sheet/chart-of-account-group/form", "BalanceSheet::chartOfAccountGroupForm");
$route->post("/balance-sheet/chart-of-account-group/form", "BalanceSheet::chartOfAccountGroupForm");
$route->post("/balance-sheet/chart-of-account-group/delete", "BalanceSheet::chartOfAccountGroupDelete");
$route->get("/balance-sheet/chart-of-account-group/backup", "BalanceSheet::chartOfAccountGroupBackup");
$route->post("/balance-sheet/chart-of-account-group/backup", "BalanceSheet::chartOfAccountGroupBackup");

/**
 * Balanço Patrimonial - Visão geral
 */
$route->get("/balance-sheet/balance-sheet-overview/form", "BalanceSheetOverView::balanceSheetForm");
$route->post("/balance-sheet/balance-sheet-overview/form", "BalanceSheetOverView::balanceSheetForm");
$route->get("/balance-sheet/balance-sheet-overview/report", "BalanceSheetOverView::balanceSheetReport");
$route->post("/balance-sheet/balance-sheet-overview/report", "BalanceSheetOverView::balanceSheetReport");

// Relatórios Contábeis

/**
 * Livro Diário
 */
$route->get("/balance-sheet/daily-journal/report", "DailyJournal::dailyJournalReport");
$route->get("/balance-sheet/daily-journal/form/{uuid}", "DailyJournal::dailyJournalUpdate");
$route->get("/balance-sheet/daily-journal/report/backup", "DailyJournal::dailyJournalReportBackup");
$route->post("/balance-sheet/daily-journal/report/backup", "DailyJournal::dailyJournalReportBackup");
$route->post("/balance-sheet/daily-journal/form", "DailyJournal::dailyJournalUpdate");
$route->post("/balance-sheet/daily-journal/delete", "DailyJournal::dailyJournalDelete");

/**
 * Balancete de verificação, Livro razão, DRE, DVA
 */
$route->get("/balance-sheet/trial-balance/report", "FinancialStatements::trialBalanceReport");
$route->get("/balance-sheet/general-ledge/report", "FinancialStatements::generalLedgeReport");
$route->get("/balance-sheet/income-statement/report", "FinancialStatements::incomeStatementReport");
$route->get("/balance-sheet/statement-of-value-added/report", "FinancialStatements::statementOfValueAdded");

/** 
 * Assinatura do cliente
 */
$module = "customer";
$route->namespace("Source\Controllers");
$route->group($module);
$route->get("/subscribe", "Customer::customerSubscribeForm");
$route->post("/subscription/process-payment", "Subscription::processSubscription");
$route->get("/subscription/thanks-purchase", "Customer::thanksPurchase");
$route->post("/subscription/cancel-subscription", "Subscription::cancelSubscription");

/**
 * Webhook da stripe
 */
$module = "stripe";
$route->namespace("Source\Controllers");
$route->group($module);
$route->post("/webhook/update/subscription", "Server::webhookUpdateSubscription");


/**
 * Error Route
 */
$module = "ops";
$route->namespace("Source\Controllers");
$route->group($module);
$route->get("/error/{error_code}", "Site::error");


/**
 * Route
 */
$route->dispatch();

$route->error();

ob_end_flush();
