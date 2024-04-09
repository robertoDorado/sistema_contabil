<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
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
        $cashFlowGroupData = $this->cashFlowGroup
        ->find("id_user=:id_user AND deleted=1", ":id_user=" . $user->getId() . "", $columns)
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
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlowGroup->find("id_user=:id_user AND deleted=:deleted", 
            ":id_user=" . $user->getId() . "&:deleted=0", $columns)->fetch(true);
        
        $message = new Message();
        if (empty($data)) {
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
        $message = new Message();
        if (empty($data)) {
            $message->error("parametro data não pode ser vazio");
            $this->data->message = $message;
            return false;
        }

        $cashFlowGroupData = $this->cashFlowGroup->find("uuid=:uuid", ":uuid={$data['uuid']}")->fetch();
        if (empty($cashFlowGroupData)) {
            $message->error("grupo fluxo de caixa não encontrado");
            $this->data->message = $message;
            return false;
        }

        $verifyKeys = [
            "id_user" => function ($value) {
                if (!$value instanceof User) {
                    throw new Exception("Instância inválida ao atualizar o dado");
                }
                return $value->getId();
            },
        ];

        foreach ($data as $key => &$value) {
            if (!empty($verifyKeys[$key])) {
                $value = $verifyKeys[$key]($value);
            }
            $cashFlowGroupData->$key = $value;
        }
        
        $cashFlowGroupData->setRequiredFields(array_keys($data));
        return $cashFlowGroupData->save();
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
        $message = new Message();
        if (empty($data)) {
            $message->error("dados inválidos");
            $this->data->message = $message;
            return false;
        }

        validateModelProperties(ModelsCashFlowGroup::class, $data);
        
        $verifyKeys = [
            "uuid" => function($value) {
                if (!Uuid::isValid($value)) {
                    throw new Exception("uuid inválido");
                }
                return $value;
            },
            "id_user" => function ($value) {
                if (!$value instanceof User) {
                    throw new Exception("Instância inválida ao persistir o dado");
                }
                return $value->getId();
            },
        ];

        foreach ($data as $key => &$value) {
            if (!empty($verifyKeys[$key])) {
                $value = $verifyKeys[$key]($value);
            }
            $this->cashFlowGroup->$key = $value;
        }

        $this->cashFlowGroup->save();
        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
