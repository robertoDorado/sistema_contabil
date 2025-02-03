<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\TaxRegimeModel as ModelsTaxRegimeModel;

/**
 * TaxRegimeModel Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class TaxRegimeModel
{
    /** @var object */
    private object $data;

    /** @var int Id do usuário */
    private int $id;

    /** @var string Uuid do usuário */
    private string $uuid;

    /** @var ModelsTaxRegimeModel */
    private ModelsTaxRegimeModel $taxRegimeModel;

    /**
     * TaxRegimeModel constructor
     */
    public function __construct()
    {
        $this->taxRegimeModel = new ModelsTaxRegimeModel();
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
        $tools = new Tools($this->taxRegimeModel, ModelsTaxRegimeModel::class);
        $response = $tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "modelo de regime tributário não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->taxRegimeModel, ModelsTaxRegimeModel::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
