<?php
namespace Source\Domain\Model;

use DateTime;
use Exception;
use PDOException;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Core\Connect;
use Source\Models\CashFlow as ModelsCashFlow;
use Source\Support\Message;

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

    /** @var string Uuid do cliente */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /**
     * CashFlow constructor
     */
    public function __construct()
    {
        $this->cashFlow = new ModelsCashFlow();
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

    public function findCashFlowDeletedTrue(array $columns, User $user): ?ModelsCashFlow
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $cashFlowData = $this->cashFlow
        ->find("id_user=:id_user AND deleted=1", ":id_user=" . $user->getId() . "", $columns)
        ->join("cash_flow_group", "id", "id_user=:id_user AND deleted=0",
        ":id_user=" . $user->getId() . "", "group_name", "id_cash_flow_group", "cash_flow")
        ->fetch(true);

        $message = new Message();
        if (empty($cashFlowData)) {
            $message->error("não há registros deletados");
            $this->data->message = $message;
            return null;
        }

        return $cashFlowData;
    }

    public function findGroupAccountsAgrupped(User $user)
    {
        return $this->cashFlow->findGroupAccountsAgrupped($user);
    }

    public function findCashFlowDataByDate(string $dates, User $user, array $columns = []): ?ModelsCashFlow
    {
        $dates = empty($dates) ? "" : explode("-", $dates);
        $columns = empty($columns) ? "*" : implode(", ", $columns);

        if (is_array($dates) && !empty($dates)) {
            if (count($dates) != 2) {
                throw new Exception("parametro dates inválido");
            }

            foreach ($dates as &$date) {
                $date = date("Y-m-d", strtotime(str_replace("/", "-", $date)));
            }
            
            return $this->cashFlow
                ->find("id_user=:id_user AND deleted=0", 
                ":id_user=" . $user->getId() . "", $columns)
                ->join("cash_flow_group", "id", "deleted=0 AND id_user=:id_user",
                ":id_user=" . $user->getId() . "", "group_name", "id_cash_flow_group", "cash_flow")
                ->between("created_at", "sistema_contabil.cash_flow", 
                [
                    "date_init" => $dates[0], 
                    "date_end" => $dates[1]
                ])->fetch(true);
        }else {
            return null;
        }
    }

    public function updateCashFlowByUuid(array $data): bool
    {
        $message = new Message();
        if (empty($data)) {
            $message->error("data não pode ser vazio");
            $this->data->message = $message;
            return false;
        }

        $cashFlowData = $this->cashFlow->find("uuid=:uuid", ":uuid={$data['uuid']}")->fetch();
        if (empty($cashFlowData)) {
            $message->error("registro de fluxo de caixa não encontrado");
            $this->data->message = $message;
            return false;
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
            }
            $cashFlowData->$key = $value;
        }
        
        $cashFlowData->setRequiredFields(array_keys($data));
        return $cashFlowData->save();
    }

    public function dropCashFlowByUuid(): bool
    {
        $cashFlowData = $this->cashFlow
        ->find("uuid=:uuid", ":uuid={$this->getUuid()}")
        ->fetch();
        
        return $cashFlowData->destroy();
    }

    public function findCashFlowByUuid(): ?ModelsCashFlow
    {
        if (empty($uuid)) {
            return json_encode(["error" => "uuid não pode estar vazio"]);
        }

        $cashFlowData = $this->cashFlow
        ->find("uuid=:uuid", ":uuid={$this->getUuid()}")
        ->join("cash_flow_group", "id", 
        "deleted=:deleted", ":deleted=0", "group_name", "id_cash_flow_group", "cash_flow")
        ->fetch();
        
        $message = new Message();
        if (empty($cashFlowData)) {
            $message->error("o registro fluxo de caixa não existe");
            $this->data->message = $message;
            return null;
        }

        return $cashFlowData;
    }

    public function getUuid(): string
    {
        if (empty($this->uuid)) {
            throw new Exception("uuid não foi atribuido");
        }
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        if (!Uuid::isValid($uuid)) {
            throw new Exception("uuid inválido");
        }
        $this->uuid = $uuid;
    }

    public function findCashFlowByUser(array $columns = [], User $user): ?ModelsCashFlow
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlow->find("id_user=:id_user AND deleted=:deleted", 
        ":id_user=" . $user->getId() . "&:deleted=0", $columns)
        ->join("cash_flow_group", "id", "deleted=:deleted AND id_user=:id_user",
        ":deleted=0&:id_user=" . $user->getId() . "", "group_name", "id_cash_flow_group", "cash_flow")
        ->fetch(true);

        $message = new Message();
        if (empty($data)) {
            $message->error("nenhum registro foi encontrado");
            $this->data->message = $message;
            return null;
        }
        
        return $data;
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

    public function calculateBalance(User $user): float
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

    public function persistData(array $data): bool
    {
        $message = new Message();
        if (empty($data)) {
            $message->error("dados inválidos");
            $this->data->message = $message;
            return false;
        }

        validateModelProperties(ModelsCashFlow::class, $data);

        if (!preg_match("/^[\d\\.,]+$/", $data["entry"])) {
            $message->error("valor de entrada inválido");
            $this->data->message = $message;
            return false;
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
            }
            $this->cashFlow->$key = $value;
        }

        $this->cashFlow->save();
        $this->setId(Connect::getInstance()->lastInsertId());
        return true;
    }
}
