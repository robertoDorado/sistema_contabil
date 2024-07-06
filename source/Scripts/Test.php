<?php

use Source\Support\Message;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";

$message = new Message();
$message->error("erro (A)");
$message->error("erro (B)");
echo $message->json();
