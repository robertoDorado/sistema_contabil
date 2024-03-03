<?php
namespace Source\Domain\Model;

use Exception;
use PDOException;
use Source\Core\Connect;
use Source\Models\CashFlow as ModelsCashFlow;

/**
 * CashFlow Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class CashFlow
{
    /** @var ModelsCashFlow Objeto de persistencia na tabela cash_flow */
    private ModelsCashFlow $cashFlow;

    /** @var int Id da tabela cash_flow */
    private int $id;

    /**
     * CashFlow constructor
     */
    public function __construct()
    {
        $this->cashFlow = new ModelsCashFlow();
    }

    public function updateCashFlowByUuid(array $data)
    {
        if (empty($data)) {
            return json_encode(["data_is_empty" => "data não pode ser vazio"]);
        }

        $cashFlowData = $this->cashFlow->find("uuid=:uuid", ":uuid={$data['uuid']}")->fetch();
        if (empty($cashFlowData)) {
            return json_encode(["cash_flow_data_not_found" => "registro de fluxo de caixa não encontrado"]);
        }

        $verifyKeys = [
            "id_user" => function ($value) {
                if (!$value instanceof User) {
                    throw new Exception("Instância inválida ao atualizar o dado");
                }
                return $value->getId();
            },

            "entry" => function (string $value) use ($data) {
                $value = convertCurrencyRealToFloat($value);
                $value = empty($data['entry_type']) ? ($value * -1) : $value;
                return $value;
            }
        ];
        
        foreach($data as $key => &$value) {
            if (!empty($verifyKeys[$key])) {
                $value = $verifyKeys[$key]($value);
                $cashFlowData->$key = $value;
            }else {
                $cashFlowData->$key = $value;
            }
        }
        
        validateModelProperties(ModelsCashFlow::class, $data);
        $cashFlowData->setRequiredFields(array_keys($data));

        if (!$cashFlowData->save()) {
            if (!empty($cashFlowData->fail())) {
                throw new PDOException($cashFlowData->fail()->getMessage());
            }else {
                throw new Exception($cashFlowData->message()->getText());
            }
        }
        return true;
    }

    public function dropCashFlowByUuid(string $uuid)
    {
        $cashFlowData = $this->cashFlow
            ->find("uuid=:uuid", ":uuid={$uuid}")
            ->fetch();
        
        if (!$cashFlowData->destroy()) {
            if (!empty($cashFlowData->fail())) {
                throw new PDOException($cashFlowData->fail()->getMessage());
            }else {
                throw new Exception($cashFlowData->message()->getText());
            }
        }
    }

    public function findCashFlowByUuid(string $uuid)
    {
        $cashFlowData = $this->cashFlow
            ->find("uuid=:uuid", ":uuid={$uuid}")
            ->fetch();
        
        if (empty($cashFlowData)) {
            return json_encode(["empty_cash_flow" => "o registro fluxo de caixa não existe"]);
        }

        return $cashFlowData;
    }

    public function findCashFlowByUser(array $columns = [], User $user)
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlow->find("id_user=:id_user AND deleted=:deleted", 
            ":id_user=" . $user->getId() . "&:deleted=0", $columns)->fetch(true);
        
        if (empty($data)) {
            return json_encode(["cash_flow_empty" => "nenhum registro foi encontrado"]);
        }
        
        return $data;
    }

    public function getId()
    {
        if (empty($this->id)) {
            throw new Exception("id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id)
    {
        $this->id = $id;
    }

    public function findCashFlowById(array $columns = [])
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->cashFlow->find("id=:id AND deleted=:deleted",
            ":id=" . $this->getId() . "&:deleted=0")->fetch();
    }

    public function dropCashFlowById(int $id)
    {
        $cashFlowData = $this->cashFlow->findById($id);
        if (!$cashFlowData->destroy()) {
            if (!empty($cashFlowData->fail())) {
                throw new PDOException($cashFlowData->fail()->getMessage());
            }else {
                throw new Exception($cashFlowData->message()->getText());
            }
        }
    }

    public function calculateBalance(User $user)
    {
        $data = $this->cashFlow
            ->find("id_user=:id_user AND deleted=:deleted",
            ":id_user=" . $user->getId() . "&:deleted=0")->fetch(true);
        $balance = 0;
        
        if (!empty($data)) {
            foreach ($data as $value) {
                $balance += $value->getEntry();
            }
        }

        return $balance;
    }

    public function persistData(array $data)
    {
        if (empty($data)) {
            return json_encode(["invalid_persist_data" => "dados inválidos"]);
        }

        validateModelProperties(ModelsCashFlow::class, $data);

        if (!preg_match("/^[\d\\.,]+$/", $data["entry"])) {
            throw new \Exception("valor de entrada inválido");
        }
        
        $entry = convertCurrencyRealToFloat($data["entry"]);
        $entry = $data["entry_type"] == 0 ? ($entry * -1) : $entry;
        $data["entry"] = $entry;

        foreach ($data as $key => &$value) {
            if ($key == "id_user") {
                if (!$value instanceof User) {
                    throw new Exception("Instância inválida ao persistir o dado");
                }

                $value = $value->getId();
            }

            $this->cashFlow->$key = $value;
        }

        if (!$this->cashFlow->save()) {
            if (!empty($this->cashFlow->fail())) {
                throw new PDOException($this->cashFlow->fail()->getMessage());
            }else {
                throw new Exception($this->cashFlow->message()->getText());
            }
        }

        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
