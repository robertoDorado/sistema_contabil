<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\Invoice as ModelsInvoice;

/**
 * Invoice Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class Invoice
{
    /** @var int Id da tabela balanço patrimonial */
    private int $id;

    /** @var string Uuid do registro */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /** @var ModelsInvoice */
    private ModelsInvoice $invoice;

    /**
     * Invoice constructor
     */
    public function __construct()
    {
        $this->invoice = new ModelsInvoice();
        $this->data = new \stdClass();
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function findInvoiceByUuid(array $columns): ?ModelsInvoice
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->invoice->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "", $columns)->fetch();
    }

    /** @var ModelsInvoice[] */
    public function findAllInvoiceJoinCompany(array $data, array $columnsInvoice, array $columnsCompany): array
    {
        $columnsInvoice = empty($columnsInvoice) ? "*" : implode(", ", $columnsInvoice);
        $columnsCompany = empty($columnsCompany) ? "*" : implode(", ", $columnsCompany);

        $response = $this->invoice->find(
            "id_user=:id_user AND id_company=:id_company", 
            ":id_company=" . $data["id_company"] . "&:id_user=" . $data["id_user"] . "",
            $columnsInvoice
        )->join(
            CONF_DB_NAME . ".company",
            "id",
            null,
            null,
            $columnsCompany,
            "id_company",
            CONF_DB_NAME . ".invoice"
        );

        if (!empty($data["date"])) {
            $response = $response->between(
                "created_at",
                CONF_DB_NAME . ".invoice",
                [
                    "date_ini" => $data["date"]["date_ini"],
                    "date_end" => $data["date"]["date_end"]
                ]
            );
        }

        if (empty($response->fetch(true))) {
            return [];
        }

        return $response->fetch(true);
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

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->invoice, ModelsInvoice::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
