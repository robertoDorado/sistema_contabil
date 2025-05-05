<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\SupportTickets as ModelsSupportTickets;

/**
 * SupportTickets Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class SupportTickets
{
    /** @var object */
    private object $data;

    /** @var int Id do usuário */
    private int $id;

    /** @var string Uuid do usuário */
    private string $uuid;

    /** @var ModelsSupportTickets */
    private ModelsSupportTickets $supportTickets;

    /**
     * SupportTickets constructor
     */
    public function __construct()
    {
        $this->supportTickets = new ModelsSupportTickets();
        $this->data = new \stdClass();
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    /** @var ModelsSupportTickets[] */
    public function findAllSupportTickets(array  $columns): array
    {
        $tools = new Tools($this->supportTickets, ModelsSupportTickets::class);
        return $tools->findAllData($columns);
    }

    public function findSupportTicketsByUuid(array $columns): ?ModelsSupportTickets
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->supportTickets->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "", $columns)->fetch();
    }

    /** @var ModelsSupportTickets[] */
    public function findSupportTicketsBySupportUserIdJoinUser(array $data): array
    {
        $data["columns_tickets"] = empty($data["columns_tickets"]) ? "*" : implode(", ", $data["columns_tickets"]);
        $data["columns_user"] = empty($data["columns_user"]) ? "*" : implode(", ", $data["columns_user"]);
        
        $response = $this->supportTickets->find(
            "id_support=:id_support",
            ":id_support=" . $data["id_support"] . "",
            $data["columns_tickets"]
        )->join(
            CONF_DB_NAME . ".user",
            "id",
            "",
            "",
            $data["columns_user"],
            "id_user",
            CONF_DB_NAME . ".support_tickets"
        )->order(CONF_DB_NAME . ".support_tickets.id", true)->fetch(true);

        if (empty($response)) {
            return [];
        }

        return $response;
    }

    public function findSupportTicketsJoinSupportByUuid(array $columnsSupportTicket, array $columnsSupport): ?ModelsSupportTickets
    {
        $columnsSupportTicket = empty($columnsSupportTicket) ? "*" : implode(", ", $columnsSupportTicket);
        $columnsSupport = empty($columnsSupport) ? "*" : implode(", ", $columnsSupport);

        return $this->supportTickets
            ->find(
                "uuid=:uuid",
                ":uuid=" . $this->getUuid() . "",
                $columnsSupportTicket
            )->join(
                CONF_DB_NAME . ".support",
                "id",
                "",
                "",
                $columnsSupport,
                "id_support",
                CONF_DB_NAME . ".support_tickets"
            )->fetch();
    }

    /** @var ModelsSupportTickets[] */
    public function findSupportTicketsJoinSupportResponse(array $data): array
    {
        $data["support_tickets"] = empty($data["support_tickets"]) ? "*" : implode(", ", $data["support_tickets"]);
        $data["support_response"] = empty($data["support_response"]) ? "*" : implode(", ", $data["support_response"]);
        $data["support"] = empty($data["support"]) ? "*" : implode(", ", $data["support"]);

        $response = $this->supportTickets
            ->find(
                "id_user=:id_user",
                ":id_user=" . $data["id_user"] . "",
                $data["support_tickets"]
            )->leftJoin(
                CONF_DB_NAME . ".support_response",
                "id_support_tickets",
                "",
                "",
                $data["support_response"],
                "id",
                CONF_DB_NAME . ".support_tickets"
            )->join(
                CONF_DB_NAME . ".support",
                "id",
                "",
                "",
                $data["support"],
                "id_support",
                CONF_DB_NAME . ".support_tickets"
            );

        if (!empty($data["date"])) {
            $response = $response->between(
                "created_at",
                CONF_DB_NAME . ".support_tickets",
                [
                    "date_ini" => $data["date"]["date_ini"],
                    "date_end" => $data["date"]["date_end"]
                ]
            );
        }

        if (empty($response->order(CONF_DB_NAME . ".support_tickets.id", true)->fetch(true))) {
            return [];
        }

        return $response->order(CONF_DB_NAME . ".support_tickets.id", true)->fetch(true);
    }

    public function getId(): int
    {
        if (empty($this->id)) {
            throw new Exception("id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        if (!Uuid::isValid($uuid)) {
            throw new Exception("uuid inválido");
        }
        $this->uuid = $uuid;
    }

    public function updateData(array $data): bool
    {
        $tools = new Tools($this->supportTickets, ModelsSupportTickets::class);
        $response = $tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "ticket não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->supportTickets, ModelsSupportTickets::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
