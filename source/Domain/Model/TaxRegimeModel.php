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

    /** @var Tools */
    private Tools $tools;

    /**
     * TaxRegimeModel constructor
     */
    public function __construct()
    {
        $this->taxRegimeModel = new ModelsTaxRegimeModel();
        $this->data = new \stdClass();
        $this->tools = new Tools($this->taxRegimeModel, ModelsTaxRegimeModel::class);
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function findTaxRegimeByName(string $name, array $params): ?ModelsTaxRegimeModel
    {
        return $this->taxRegimeModel->find(
            "tax_regime_value=:tax_regime_value AND deleted=0 AND id_company=:id_company AND id_user=:id_user", 
            ":tax_regime_value={$name}&:id_company={$params['id_company']}&:id_user={$params['id_user']}",
            "id"
        )->fetch();
    }

    public function findAllTaxRegimeModel(array $columns, array $params): array
    {
        return $this->tools->findAllData(
            $columns, 
            false,
            "deleted=:deleted AND id_company=:id_company AND id_user=:id_user", 
            ":deleted=0&:id_company={$params['id_company']}&:id_user={$params['id_user']}"
        );
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
        $response = $this->tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "modelo de regime tributário não encontrado"
        );
        $this->data->message = !empty($this->tools->message) ? $this->tools->message : "";
        return !empty($response) ? true : false;
    }

    public function persistData(array $data): bool
    {
        $response = $this->tools->persistData($data);
        $this->data->message = !empty($this->tools->message) ? $this->tools->message : "";

        !empty($response) ? $this->setId($this->tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
