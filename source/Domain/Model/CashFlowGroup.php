<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\CashFlowGroup as ModelsCashFlowGroup;
use Source\Support\Message;

/**
 * CashFlowGroup Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Domain\Model
 */
class CashFlowGroup
{
    /** @var int Id da tabela */
    private int $id;

    /** @var string Uuid do grupo de contas */
    private string $uuid;

    /** @var ModelsCashFlowGroup Modelo de persistência para agrupamento do fluxo de caixa */
    private ModelsCashFlowGroup $cashFlowGroup;

    /** @var object|null */
    private object $data;

    /**
     * CashFlowGroup constructor
     */
    public function __construct()
    {
        $this->cashFlowGroup = new ModelsCashFlowGroup();
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

    /** @return ModelsCashFlowGroup[] */
    public function findCashFlowGroupDeletedTrue(array $columns, User $user): array
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $cashFlowGroupData = $this->cashFlowGroup
            ->find("id_user=:id_user AND deleted=1 AND id_company=:id_company",
             ":id_user=" . $user->getId() . "&:id_company=" . $companyId . "", $columns)
            ->fetch(true);

        $message = new Message();
        if (empty($cashFlowGroupData)) {
            $message->error("não há registros deletados");
            $this->data->message = $message;
            return [];
        }

        return $cashFlowGroupData;
    }

    public function findCashFlowGroupByName(string $groupName, User $user, array $columns = []): ?ModelsCashFlowGroup
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlowGroup->find("id_user=:id_user AND deleted=:deleted
        AND group_name=:group_name", ":id_user=" . $user->getId() .
            "&:deleted=0&:group_name=" . $groupName . "", $columns)->fetch();

        $message = new Message();
        if (empty($data)) {
            $message->error("nenhum registro foi encontrado");
            $this->data->message = $message;
            return null;
        }

        return $data;
    }

    /** @return ModelsCashFlowGroup[] */
    public function findCashFlowGroupByUser(array $columns = [], User $user): array
    {
        $companyId = empty(session()->user->company_id) ? 0 : session()->user->company_id;
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlowGroup->find(
            "id_user=:id_user AND deleted=:deleted AND id_company=:id_company",
            ":id_user=" . $user->getId() . "&:deleted=0&:id_company=" . $companyId . "",
            $columns
        )->fetch(true);

        if (empty($data)) {
            $message = new Message();
            $message->error("nenhum registro foi encontrado");
            $this->data->message = $message;
            return [];
        }

        return $data;
    }

    public function findCashFlowGroupByUuid(): ?ModelsCashFlowGroup
    {
        $cashFlowGroupData = $this->cashFlowGroup->find("uuid=:uuid", ":uuid={$this->getUuid()}")->fetch();
        $message = new Message();

        if (empty($cashFlowGroupData)) {
            $message->error("registro não encontrado");
            $this->data->message = $message;
            return null;
        }

        return $cashFlowGroupData;
    }

    public function dropCashFlowGroupByUuid(): bool
    {
        $cashFlowGroupData = $this->cashFlowGroup->find("uuid=:uuid", ":uuid={$this->getUuid()}")->fetch();
        $message = new Message();

        if (empty($cashFlowGroupData)) {
            $message->error("o registro não existe");
            $this->data->message = $message;
            return false;
        }

        return $cashFlowGroupData->destroy();
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

    public function updateCashFlowGroupByUuid(array $data): bool
    {
        $tools = new Tools($this->cashFlowGroup, ModelsCashFlowGroup::class);
        $response = $tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "grupo fluxo de caixa não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function getId(): int
    {
        if (empty($this->id)) {
            throw new Exception("Id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->cashFlowGroup, ModelsCashFlowGroup::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
