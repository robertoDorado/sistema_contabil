<?php

namespace Source\Controllers;

use DateTime;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Controller;
use Source\Domain\Model\Support as ModelSupport;
use Source\Domain\Model\SupportTickets;

/**
 * Support Controllers
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Controllers
 */
class Support extends Controller
{
    /**
     * Support constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function ticketDetail(array $data)
    {
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields([
                'contentMessage',
                'csrfToken',
                'userSupportData',
                'ticketStatus',
                'uuid'
            ])->getAllPostData();
            $requestFiles = $this->getRequestFiles()->getFile("attachmentFile");

            $verifyTicketStatus = [
                "aberto",
                "pendente",
                "em análise",
                "resolvido"
            ];

            if (!in_array($requestPost["ticketStatus"], $verifyTicketStatus)) {
                http_response_code(500);
                echo json_encode(["error" => "status inválido"]);
                die;
            }

            $support = new ModelSupport();
            $support->setUuid($requestPost["userSupportData"]);
            $userSupportData = $support->findUserSupportByUuid(["id"]);

            if (empty($userSupportData)) {
                http_response_code(500);
                echo json_encode(["error" => "usuário suporte não encontrado"]);
                die;
            }

            if (empty($requestFiles["error"])) {
                $filePath = dirname(dirname(__DIR__)) . "/tickets";
                if (!is_dir($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                $fileDestination = $filePath . "/" . basename($requestFiles["name"]);
                $verifyImage = getimagesize($requestFiles["tmp_name"]);

                if (!$verifyImage) {
                    http_response_code(500);
                    echo json_encode(["error" => "arquivo inválido"]);
                    die;
                }

                if (!move_uploaded_file($requestFiles["tmp_name"], $fileDestination)) {
                    http_response_code(500);
                    echo json_encode(["error" => "erro no upload do arquivo"]);
                    die;
                }
            }

            $supportTickets = new SupportTickets();
            $response = $supportTickets->updateData([
                "uuid" => $requestPost["uuid"],
                "id_support" => $userSupportData->id,
                "content_message" => $requestPost["contentMessage"],
                "content_attachment" => $requestFiles["name"],
                "status" => $requestPost["ticketStatus"],
                "deleted" => 0,
                "updated_at" => date("Y-m-d")
            ]);

            if (!$response) {
                http_response_code(500);
                echo json_encode(["error" => "erro ao atualizar o ticket"]);
                die;
            }

            echo json_encode(["success" => "ticket atualizado com sucesso", "url" => url("/admin/support/my-tickets")]);
            die;
        }

        if (!Uuid::isValid($data["uuid"])) {
            redirect("/admin/support/my-tickets");
        }

        $supportTickets = new SupportTickets();
        $supportTickets->setUuid($data["uuid"]);
        $supportTicketsData = $supportTickets->findSupportTicketsJoinSupportByUuid(
            [
                "uuid",
                "id_user",
                "content_message",
                "content_attachment",
                "status"
            ],
            [
                "user_full_name"
            ]
        );

        $support = new ModelSupport();
        $userSupportData = $support->findAllUserSupport(["uuid", "user_full_name", "id"]);

        echo $this->view->render("admin/ticket-detail", [
            "userFullName" => showUserFullName(),
            "endpoints" => [],
            "userSupportData" => $userSupportData,
            "supportTicketsData" => $supportTicketsData
        ]);
    }

    public function myTickets()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $params = [
            "support_tickets" => [
                "uuid",
                "deleted",
                "content_message AS content_message_tickets",
                "content_attachment AS content_attachment_tickets",
                "status",
                "created_at AS created_at_ticket"
            ],
            "support_response" => [
                "content_message AS content_message_response",
                "content_attachment AS content_attachment_response",
                "created_at AS created_at_response"
            ],
            "support" => [
                "user_full_name"
            ],
            "id_user" => $responseInitializeUserAndCompany["user_data"]->id
        ];

        $dateRange = $this->getRequests()->get("daterange");
        if (!empty($dateRange)) {
            $date = explode("-", $dateRange);
            $date = array_map(function ($item) {
                return preg_replace("/(\d{2})\/(\d{2})\/(\d{4})/", "$3-$2-$1", $item);
            }, $date);

            $params["date"] = [
                "date_ini" => $date[0],
                "date_end" => $date[1]
            ];
        }

        $supportTickets = new SupportTickets();
        $supportTicketsData = $supportTickets->findSupportTicketsJoinSupportResponse($params);

        $verifyBadge = [
            "aberto" => "badge-danger",
            "pendente" => "badge-warning",
            "em análise" => "badge-info",
            "resolvido" => "badge-success"
        ];

        $supportTicketsData = array_filter($supportTicketsData, function ($item) {
            return empty($item->getDeleted());
        });

        $supportTicketsData = array_map(function ($item) use ($verifyBadge) {
            $item->content_message_tickets = strlen($item->content_message_tickets) > 10 ?
                substr($item->content_message_tickets, 0, 20) . "..." : $item->content_message_tickets;

            $item->created_at_ticket = (new DateTime($item->created_at_ticket))->format("d/m/Y");
            $item->created_at_response = !empty($item->created_at_response) ?
                (new DateTime($item->created_at_response))->format("d/m/Y") : "";

            $item->reply_message = !empty($item->content_message_response) ? true : false;
            $item->status = $item->getStatus();
            $item->badge = !empty($verifyBadge[$item->status]) ? $verifyBadge[$item->status] : "badge-primary";

            return $item;
        }, $supportTicketsData);

        echo $this->view->render("admin/my-tickets", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/support/my-tickets"],
            "supportTicketsData" => $supportTicketsData
        ]);
    }

    public function openTicket()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        if ($this->getServer()->getServerByKey("REQUEST_METHOD") == "POST") {
            $requestPost = $this->getRequests()->setRequiredFields(
                [
                    'contentMessage',
                    'csrfToken',
                    'userSupportData'
                ]
            )->getAllPostData();
            $requestFiles = $this->getRequestFiles()->getFile("attachmentFile");

            $support = new ModelSupport();
            $support->setUuid($requestPost["userSupportData"]);
            $userSupportData = $support->findUserSupportByUuid(["id"]);

            if (empty($userSupportData)) {
                http_response_code(500);
                echo json_encode(["error" => "usuário suporte não encontrado"]);
                die;
            }

            if (empty($requestFiles["error"])) {
                $filePath = dirname(dirname(__DIR__)) . "/tickets";
                if (!is_dir($filePath)) {
                    mkdir($filePath, 0777, true);
                }

                $fileDestination = $filePath . "/" . basename($requestFiles["name"]);
                $verifyImage = getimagesize($requestFiles["tmp_name"]);

                if (!$verifyImage) {
                    http_response_code(500);
                    echo json_encode(["error" => "arquivo inválido"]);
                    die;
                }

                if (!move_uploaded_file($requestFiles["tmp_name"], $fileDestination)) {
                    http_response_code(500);
                    echo json_encode(["error" => "erro no upload do arquivo"]);
                    die;
                }
            }

            $supportTickets = new SupportTickets();
            $response = $supportTickets->persistData([
                "uuid" => Uuid::uuid4(),
                "id_user" => $responseInitializeUserAndCompany["user"],
                "id_support" => $userSupportData->id,
                "content_message" => $requestPost["contentMessage"],
                "content_attachment" => empty($requestFiles["name"]) ? null : $requestFiles["name"],
                "status" => "aberto",
                "deleted" => 0,
                "created_at" => date("Y-m-d"),
                "updated_at" => date("Y-m-d")
            ]);

            if (!$response) {
                http_response_code(500);
                echo json_encode(["error" => "erro ao registrar o chamado"]);
                die;
            }

            echo json_encode(["success" => "chamado registrado com sucesso"]);
            die;
        }

        $support = new ModelSupport();
        $userSupportData = $support->findAllUserSupport();

        echo $this->view->render("admin/open-ticket", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/support/dashboard"],
            "userSupportData" => $userSupportData
        ]);
    }

    public function supportDashboard()
    {
        $responseInitializeUserAndCompany = initializeUserAndCompanyId();
        $supportTickets = new SupportTickets();
        $supportTicketsData = $supportTickets->findSupportTicketsBySupportUserIdJoinUser([
            "columns_ticketes" => [
                "uuid",
                "content_message",
                "content_attachment",
                "status",
                "deleted",
                "created_at"
            ],
            "columns_user" => [
                "user_full_name"
            ],
            "id_support" => $responseInitializeUserAndCompany["user_data"]->id
        ]);

        $supportTicketsData = array_filter($supportTicketsData, function ($item) {
            return empty($item->getDeleted());
        });

        $verifyBadge = [
            "aberto" => "badge-danger",
            "pendente" => "badge-warning",
            "em análise" => "badge-info",
            "resolvido" => "badge-success"
        ];

        $supportTicketsData = array_map(function ($item) use ($verifyBadge) {
            $item->created_at = (new DateTime($item->created_at))->format("d/m/Y");
            $item->content_message = strlen($item->content_message) > 10 ?
                substr($item->content_message, 0, 20) . "..." : $item->content_message;

            $item->status = $item->getStatus();
            $item->badge = !empty($verifyBadge[$item->status]) ? $verifyBadge[$item->status] : "badge-primary";
            return $item;
        }, $supportTicketsData);

        $totalTicketsOpen = array_filter($supportTicketsData, function ($item) {
            return $item->status == "aberto";
        });

        $totalTicketsPending = array_filter($supportTicketsData, function ($item) {
            return $item->status == "pendente";
        });

        $totalTicketsUnderAnalysis = array_filter($supportTicketsData, function ($item) {
            return $item->status == "em análise";
        });

        $totalTicketsResolved = array_filter($supportTicketsData, function ($item) {
            return $item->status == "resolvido";
        });

        echo $this->view->render("admin/support-dashboard", [
            "userFullName" => showUserFullName(),
            "endpoints" => ["/admin/support/dashboard"],
            "supportTicketsData" => $supportTicketsData,
            "totalTicketsOpen" => count($totalTicketsOpen),
            "totalTicketsPending" => count($totalTicketsPending),
            "totalTicketsUnderAnalysis" => count($totalTicketsUnderAnalysis),
            "totalTicketsResolved" => count($totalTicketsResolved)
        ]);
    }
}
