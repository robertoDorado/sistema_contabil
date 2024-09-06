<?php

use Source\Core\Model;
use Source\Domain\Model\SupportTickets;

require dirname(dirname(__DIR__)) . "/vendor/autoload.php";


$supportTickets = new SupportTickets();
$supportTicketsData = $supportTickets->findAllSupportTickets(["id", "status", "created_at"]);

$verifyStatus = ["em análise", "resolvido"];
$dateTimeNow = new  DateTime();

$supportTicketsData = array_filter($supportTicketsData, function($item) use ($dateTimeNow, $verifyStatus) {
    $dateTimeTicket = new DateTime($item->created_at);
    return !in_array($item->getStatus(), $verifyStatus) && $dateTimeNow->diff($dateTimeTicket)->days >= 7;
});

$updatePendingTicket = function(Model $model) {
    $model->setRequiredFields(["status"]);
    $model->status = "pendente";
    if (!$model->save()) {
        throw new Exception("erro ao atualizar o ticket");
    }
};

array_walk($supportTicketsData, $updatePendingTicket);
