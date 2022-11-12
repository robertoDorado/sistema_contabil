<?php

ob_start();

require __DIR__ . '/vendor/autoload.php';

use Source\Core\MyRouter;

$route = new MyRouter(url(), ":");

/**
 * Home Route
 */
$module = null;
$route->namespace("Source\Controllers");
$route->group($module);
$route->get("/", "Home:index");

/**
 * Error Route
 */
$module = "ops";
$route->namespace("Source\Controllers");
$route->group($module);
$route->get("/error/{error_code}", "Home:error");


/**
 * Route
 */
$route->dispatch();

$route->error();

ob_end_flush();