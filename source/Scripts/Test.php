<?php
require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

print_r(new PDO(
    "mysql:host=" . CONF_DB_HOST . ";dbname=" . CONF_DB_NAME,
    CONF_DB_USER,
    CONF_DB_PASS
));