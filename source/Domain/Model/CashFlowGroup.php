<?php
namespace Source\Domain\Model;

use Exception;
use PDOException;
use Source\Core\Connect;
use Source\Models\CashFlowGroup as ModelsCashFlowGroup;

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

    /** @var ModelsCashFlowGroup Modelo de persistência para agrupamento do fluxo de caixa */
    private ModelsCashFlowGroup $cashFlowGroup;

    /**
     * CashFlowGroup constructor
     */
    public function __construct()
    {
        $this->cashFlowGroup = new ModelsCashFlowGroup();
    }

    public function findCashFlowGroupDeletedTrue(array $columns, User $user)
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $cashFlowGroupData = $this->cashFlowGroup
        ->find("id_user=:id_user AND deleted=1", ":id_user=" . $user->getId() . "", $columns)
        ->fetch(true);

        if (empty($cashFlowGroupData)) {
            return json_encode(["error" => "não há registros deletados"]);
        }

        return $cashFlowGroupData;
    }

    public function findCashFlowGroupByName(string $groupName, User $user, array $columns = [])
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlowGroup->find("id_user=:id_user AND deleted=:deleted
        AND group_name=:group_name", ":id_user=" . $user->getId() .
        "&:deleted=0&:group_name=" . $groupName . "", $columns)->fetch();
        
        if (empty($data)) {
            return json_encode(["error" => "nenhum registro foi encontrado"]);
        }
        
        return $data;
    }

    public function findCashFlowGroupByUser(array $columns = [], User $user)
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlowGroup->find("id_user=:id_user AND deleted=:deleted", 
            ":id_user=" . $user->getId() . "&:deleted=0", $columns)->fetch(true);
        
        if (empty($data)) {
            return json_encode(["error" => "nenhum registro foi encontrado"]);
        }
        
        return $data;
    }

    public function findCashFlowGroupByUuid(string $uuid)
    {
        if (empty($uuid)) {
            return json_encode(["error" => "uuid não pode estar vazio"]);
        }

        $cashFlowGroupData = $this->cashFlowGroup->find("uuid=:uuid", ":uuid={$uuid}")->fetch();
        if (empty($cashFlowGroupData)) {
            return json_encode(["error" => "registro não encontrado"]);
        }

        return $cashFlowGroupData;
    }

    public function dropCashFlowGroupById(int $id)
    {
        if (empty($id)) {
            return json_encode(["error" => "id não pode estar vazio"]);
        }

        $cashFlowGroupData = $this->cashFlowGroup->findById($id);
        return $cashFlowGroupData->destroy();
    }

    public function dropCashFlowGroupByUuid(string $uuid)
    {
        if (empty($uuid)) {
            return json_encode(["error" => "uuid não pode estar vazio"]);
        }

        $cashFlowGroupData = $this->cashFlowGroup->find("uuid=:uuid", ":uuid={$uuid}")->fetch();
        if (empty($cashFlowGroupData)) {
            return json_encode(["error" => "o registro não existe"]);
        }
        
        return $cashFlowGroupData->destroy();
    }

    public function updateCashFlowGroupByUuid(array $data)
    {
        if (empty($data)) {
            return json_encode(["error" => "data não pode ser vazio"]);
        }

        $cashFlowGroupData = $this->cashFlowGroup->find("uuid=:uuid", ":uuid={$data['uuid']}")->fetch();
        if (empty($cashFlowGroupData)) {
            return json_encode(["error" => "grupo fluxo de caixa não encontrado"]);
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
                $cashFlowGroupData->$key = $value;
            }else {
                $cashFlowGroupData->$key = $value;
            }
        }
        
        $cashFlowGroupData->setRequiredFields(array_keys($data));
        return $cashFlowGroupData->save();
    }

    public function getId()
    {
        if (empty($this->id)) {
            throw new Exception("Id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function persistData(array $data)
    {
        if (empty($data)) {
            return json_encode(["error" => "dados inválidos"]);
        }

        validateModelProperties(ModelsCashFlowGroup::class, $data);
        
        $verifyKeys = [
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
                $this->cashFlowGroup->$key = $value;
            }else {
                $this->cashFlowGroup->$key = $value;
            }
        }

        $this->cashFlowGroup->save();
        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
