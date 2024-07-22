<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
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

    public function findCashFlowToCompareAutomaticImportsFile(User $user, int $companyId, array $columns)
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $response = $this->cashFlow->find(
            "id_company=:id_company AND id_user=:id_user",
            ":id_company=" . $companyId
                . "&:id_user=" . $user->getId() . "",
            $columns
        )->fetch(true);

        if (empty($response)) {
            return [];
        }

        return $response;
    }

    /** @return ModelsCashFlow[] */
    public function findCashFlowDeletedTrue(array $columns, User $user, int $companyId): array
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $cashFlowData = $this->cashFlow
            ->find(
                "id_user=:id_user AND deleted=1 AND id_company=:id_company",
                ":id_user=" . $user->getId() . "&:id_company=" . $companyId . "",
                $columns
            )
            ->join(
                "cash_flow_group",
                "id",
                "id_user=:id_user AND deleted=0 AND id_company=:id_company",
                ":id_user=" . $user->getId() . "&:id_company=" . $companyId . "",
                "group_name",
                "id_cash_flow_group",
                "cash_flow"
            )
            ->fetch(true);

        $message = new Message();
        if (empty($cashFlowData)) {
            $message->error("não há registros deletados");
            $this->data->message = $message;
            return [];
        }

        return $cashFlowData;
    }

    public function findGroupAccountsAgrupped(User $user, int $companyId, string $dateRange): array
    {
        return $this->cashFlow->findGroupAccountsAgrupped($user, $companyId, $dateRange);
    }

    /** @return ModelsCashFlow[] */
    public function findCashFlowDataByDate(string $dates, User $user, array $columns = [], int $companyId): array
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

            $cashFlowData = $this->cashFlow
                ->find(
                    "id_user=:id_user AND deleted=0 AND id_company=:id_company",
                    ":id_user=" . $user->getId() . "&:id_company=" . $companyId,
                    $columns
                )
                ->join(
                    "cash_flow_group",
                    "id",
                    "deleted=0 AND id_user=:id_user AND id_company=:id_company",
                    ":id_user=" . $user->getId() . ":id_company=" . $companyId,
                    "group_name",
                    "id_cash_flow_group",
                    "cash_flow"
                )
                ->between(
                    "created_at",
                    "" . CONF_DB_NAME . ".cash_flow",
                    [
                        "date_init" => $dates[0],
                        "date_end" => $dates[1]
                    ]
                )->fetch(true);

            if (empty($cashFlowData)) {
                $message = new Message();
                $message->error("registro não encontrado");
                $this->data->message = $message;
                return [];
            }

            return $cashFlowData;
        } else {
            return [];
        }
    }

    public function updateCashFlowByUuid(array $data): bool
    {
        $tools = new Tools($this->cashFlow, ModelsCashFlow::class);
        $response = $tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "registro de fluxo de caixa não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
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
        $cashFlowData = $this->cashFlow
            ->find("uuid=:uuid", ":uuid={$this->getUuid()}")
            ->join(
                "cash_flow_group",
                "id",
                "deleted=:deleted",
                ":deleted=0",
                "group_name",
                "id_cash_flow_group",
                "cash_flow"
            )
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
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
    {
        if (!Uuid::isValid($uuid)) {
            throw new Exception("uuid inválido");
        }
        $this->uuid = $uuid;
    }

    /** @return ModelsCashFlow[] */
    public function findCashFlowByUser(array $columns = [], User $user, int $companyId): array
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $data = $this->cashFlow->find(
            "id_user=:id_user AND deleted=:deleted AND id_company=:id_company",
            ":id_user=" . $user->getId() . "&:deleted=0&:id_company=" . $companyId,
            $columns
        )
            ->join(
                "cash_flow_group",
                "id",
                "deleted=:deleted AND id_user=:id_user AND id_company=:id_company",
                ":deleted=0&:id_user=" . $user->getId() . "&:id_company=" . $companyId,
                "group_name",
                "id_cash_flow_group",
                "cash_flow"
            )
            ->fetch(true);

        $message = new Message();
        if (empty($data)) {
            $message->error("nenhum registro foi encontrado");
            $this->data->message = $message;
            return [];
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

    public function calculateBalance(User $user, int $companyId): float
    {
        $data = $this->cashFlow
            ->find(
                "id_user=:id_user AND deleted=:deleted AND id_company=:id_company",
                ":id_user=" . $user->getId() . "&:deleted=0&:id_company=" . $companyId . ""
            )->fetch(true);
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
        $tools = new Tools($this->cashFlow, ModelsCashFlow::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
