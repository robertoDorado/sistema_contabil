<?php
namespace Source\Domain\Model;

use DateTime;
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

    public function findCashFlowDataByDate(string $dates, User $user)
    {
        $dates = empty($dates) ? "" : explode("-", $dates);

        if (is_array($dates) && !empty($dates)) {
            if (count($dates) != 2) {
                throw new \Exception("parametro dates inválido");
            }

            foreach ($dates as &$date) {
                $date = date("Y-m-d", strtotime(str_replace("/", "-", $date)));
            }
            
            return $this->cashFlow
                ->find("id_user=:id_user AND deleted=0", 
                ":id_user=" . $user->getId() . "")
                ->join("cash_flow_group", "id", "deleted=0 AND id_user=:id_user",
                ":id_user=" . $user->getId() . "", "group_name", "id_cash_flow_group", "cash_flow")
                ->between("created_at", "sistema_contabil.cash_flow", 
                [
                    "date_init" => $dates[0], 
                    "date_end" => $dates[1]
                ])->fetch(true);
        }else {
            return;
        }
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
            "id_cash_flow_group" => function ($value) {
                if (!$value instanceof CashFlowGroup) {
                    throw new Exception("Instância inválida ao atualizar o dado");
                }
                return $value->getId();
            },

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
        
        $cashFlowData->setRequiredFields(array_keys($data));
        return $cashFlowData->save();
    }

    public function dropCashFlowByUuid(string $uuid)
    {
        if (empty($uuid)) {
            return json_encode(["error" => "uuid não pode estar vazio"]);
        }

        $cashFlowData = $this->cashFlow
            ->find("uuid=:uuid", ":uuid={$uuid}")
            ->fetch();
        
        $cashFlowData->destroy();
    }

    public function findCashFlowByUuid(string $uuid)
    {
        if (empty($uuid)) {
            return json_encode(["error" => "uuid não pode estar vazio"]);
        }

        $cashFlowData = $this->cashFlow
        ->find("uuid=:uuid", ":uuid={$uuid}")
        ->join("cash_flow_group", "id", 
        "deleted=:deleted", ":deleted=0", "group_name", "id_cash_flow_group", "cash_flow")
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
        ":id_user=" . $user->getId() . "&:deleted=0", $columns)
        ->join("cash_flow_group", "id", "deleted=:deleted AND id_user=:id_user",
        ":deleted=0&:id_user=" . $user->getId() . "", "group_name", "id_cash_flow_group", "cash_flow")
        ->fetch(true);
        
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
        if (empty($id)) {
            return json_encode(["error" => "id não pode estar vazio"]);
        }
        
        $cashFlowData = $this->cashFlow->findById($id);
        $cashFlowData->destroy();
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
        
        $verifyKeys = [
            "id_cash_flow_group" => function ($value) {
                if (!$value instanceof CashFlowGroup) {
                    throw new Exception("Instância inválida ao persistir o dado");
                }
                return $value->getId();
            },

            "id_user" => function ($value) {
                if (!$value instanceof User) {
                    throw new Exception("Instância inválida ao persistir o dado");
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
                $this->cashFlow->$key = $value;
            }else {
                $this->cashFlow->$key = $value;
            }
        }

        $this->cashFlow->save();
        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
