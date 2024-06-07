<?php

ob_start();

require __DIR__ . '/vendor/autoload.php';

use Source\Core\MyRouter;

setlocale(LC_ALL, 'pt_BR');
date_default_timezone_set("America/Sao_Paulo");

$path = "./Logs/error.log";

if (!file_exists($path)) {
    mkdir("Logs");
    file_put_contents($path, '');
}

error_reporting(E_ALL & (~E_NOTICE | ~E_USER_NOTICE));
ini_set('error_log', $path);
ini_set('log_errors', true);

if (empty($_POST["request"]) && !empty($_SERVER["REDIRECT_URL"]) && $_SERVER["REDIRECT_URL"] != "/admin/logout") {
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
$route->get("/login", "Login::login");
$route->post("/login", "Login::login");
$route->post("/logout", "Login::logout");
$route->get("/cash-flow/report", "CashFlow::cashFlowReport");
$route->get("/cash-flow/form", "CashFlow::cashFlowForm");
$route->post("/cash-flow/form", "CashFlow::cashFlowForm");
$route->get("/cash-flow/update/form/{uuid}", "CashFlow::cashFlowUpdateForm");
$route->post("/cash-flow/update/form/{uuid}", "CashFlow::cashFlowUpdateForm");
$route->post("/cash-flow/remove/{uuid}", "CashFlow::cashFlowRemoveRegister");
$route->post("/cash-flow/import-excel", "CashFlow::importExcelFile");
$route->get("/cash-flow-group/form", "CashFlowGroup::cashFlowGroupForm");
$route->post("/cash-flow-group/form", "CashFlowGroup::cashFlowGroupForm");
$route->get("/cash-flow-group/report", "CashFlowGroup::cashFlowGroupReport");
$route->get("/cash-flow-group/update/form/{uuid}", "CashFlowGroup::cashFlowGroupFormUpdate");
$route->post("/cash-flow-group/update/form/{uuid}", "CashFlowGroup::cashFlowGroupFormUpdate");
$route->post("/cash-flow-group/remove/{uuid}", "CashFlowGroup::cashFlowGroupRemoveRegister");
$route->get("/cash-flow/chart-line-data", "CashFlow::findCashFlowDataForChartLine");
$route->get("/cash-flow/chart-pie-data", "CashFlow::findCashFlowDataForChartPie");
$route->get("/cash-flow-group/backup/report", "CashFlowGroup::cashFlowGroupBackupReport");
$route->get("/cash-flow/backup/report", "CashFlow::cashFlowBackupReport");
$route->post("/cash-flow-group/modify/{uuid}", "CashFlowGroup::cashFlowGroupModiFyData");
$route->post("/cash-flow/modify/{uuid}", "CashFlow::cashFlowModifyData");
$route->get("/customer/update-data/form", "Customer::updateDataCustomerForm");
$route->post("/customer/update-data/form", "Customer::updateDataCustomerForm");
$route->get("/customer/cancel-subscription", "Customer::cancelSubscription");
$route->get("/warning/empty-company", "Company::warningEmptyCompany");
$route->get("/company/register", "Company::companyRegister");
$route->post("/company/register", "Company::companyRegister");

/**
 * API para arquivos CNAB
 */
$module = "api";
$route->namespace("Source\Controllers");
$route->group($module);
$route->post("/cnab/remessa", "CnabBill::generateCnabFile");

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
