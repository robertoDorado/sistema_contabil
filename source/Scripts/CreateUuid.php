<?php

use Ramsey\Uuid\Uuid;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

echo Uuid::uuid4() . "\n";