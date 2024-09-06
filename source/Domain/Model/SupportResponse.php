<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\SupportResponse as ModelsSupportResponse;

/**
 * SupportResponse Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class SupportResponse
{
    /** @var object */
    private object $data;

    /** @var int Id do usuário */
    private int $id;

    /** @var string Uuid do usuário */
    private string $uuid;

    /** @var ModelsSupportResponse Model */
    private ModelsSupportResponse $supportResponse;

    /**
     * SupportResponse constructor
     */
    public function __construct()
    {
        $this->data = new \stdClass();
        $this->supportResponse = new ModelsSupportResponse();
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function findSupportResponseBySupportTicketId(array $columns, int $idSupportTicket): ?ModelsSupportResponse
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->supportResponse->find(
            "id_support_tickets=:id_support_tickets",
            ":id_support_tickets=" . $idSupportTicket . "",
            $columns
        )->fetch();
    }

    public function findSupportResponseJoinSupportByUuid(array $data): ?ModelsSupportResponse
    {
        $data["support_response_columns"] = empty($data["support_response_columns"]) ? "*" : implode(", ", $data["support_response_columns"]);
        $data["support_columns"] = empty($data["support_columns"]) ? "*" : implode(", ", $data["support_columns"]);

        return $this->supportResponse->find(
            "uuid=:uuid",
            ":uuid=" . $data["uuid"] . "",
            $data["support_response_columns"]
        )->join(
            CONF_DB_NAME . ".support",
            "id",
            "",
            "",
            $data["support_columns"],
            "id_support",
            CONF_DB_NAME . ".support_response",
        )->fetch();
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

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->supportResponse, ModelsSupportResponse::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
