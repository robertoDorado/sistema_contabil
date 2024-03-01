<?php

ob_start();

require __DIR__ . '/vendor/autoload.php';

use Source\Core\MyRouter;

date_default_timezone_set("America/Sao_Paulo");

$path = "./Logs/error.log";

if (!file_exists($path)) {
    mkdir("Logs");
    file_put_contents($path, '');
}

error_reporting(E_ALL & (~E_NOTICE | ~E_USER_NOTICE));
ini_set('error_log', $path);
ini_set('log_errors', true);

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


/**
 * Error Route
 */
$module = "ops";
$route->namespace("Source\Controllers");
$route->group($module);
$route->get("/error/{error_code}", "Admin::error");


/**
 * Route
 */
$route->dispatch();

$route->error();

ob_end_flush();