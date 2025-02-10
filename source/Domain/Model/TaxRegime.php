<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\TaxRegime as ModelsTaxRegime;

/**
 * TaxRegime Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class TaxRegime
{
    /** @var object */
    private object $data;

    /** @var int Id do usuário */
    private int $id;

    /** @var string Uuid do usuário */
    private string $uuid;

    /** @var ModelsTaxRegime */
    private ModelsTaxRegime $taxRegime;

    /** @var Tools */
    private Tools $tools;

    /**
     * TaxRegime constructor
     */
    public function __construct()
    {
        $this->taxRegime = new ModelsTaxRegime();
        $this->data = new \stdClass();
        $this->tools = new Tools($this->taxRegime, ModelsTaxRegime::class);
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function findTaxRegimeByUuid(array $columns, string $uuid): ?ModelsTaxRegime
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->taxRegime->find("uuid=:uuid", ":uuid={$uuid}", $columns)->fetch();
    }

    public function findTaxRegimeByTaxRegimeModelId(array $columnsA, array $columnsB, array $params): ?ModelsTaxRegime
    {
        $columnsA = empty($columnsA) ? "*" : implode(", ", $columnsA);
        $columnsB = empty($columnsB) ? "*" : implode(", ", $columnsB);
        return $this->taxRegime->find(
            "deleted=0 AND id_user=:id_user AND id_company=:id_company",
            ":id_user={$params['id_user']}&:id_company={$params['id_company']}",
            $columnsA
        )->join(
            CONF_DB_NAME . ".tax_regime_model",
            "id",
            "deleted=0 AND id_user=:id_user AND id_company=:id_company",
            ":id_user={$params['id_user']}&:id_company={$params['id_company']}",
            $columnsB,
            "tax_regime_id",
            CONF_DB_NAME . ".tax_regime"
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

    public function updateData(array $data): bool
    {
        $response = $this->tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "regime tributário não encontrado"
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
