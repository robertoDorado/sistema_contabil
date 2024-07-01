<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\InvestmentCashFlow as ModelsInvestmentCashFlow;

/**
 * InvestmentCashFlow Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class InvestmentCashFlow
{
    /** @var ModelsInvestmentCashFlow */
    private ModelsInvestmentCashFlow $investmentCashFlow;

    /** @var object */
    private object $data;

    /** @var string */
    private string $uuid;

    /** @var int */
    private int $id;

    /**
     * InvestmentCashFlow constructor
     */
    public function __construct()
    {
        $this->investmentCashFlow = new ModelsInvestmentCashFlow();
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

    /** @var ModelsInvestmentCashFlow[] */
    public function findInvestmentCashFlowJoinCashFlowGroupAndJoinCashFlowData(array $columnsInv, array $columnsCg, array $columnsCd, array $betweenData, User $user, int $companyId): array
    {
        $columnsInv = empty($columnsInv) ? "*" : implode(", ", $columnsInv);
        $columnsCg = empty($columnsCg) ? "*" : implode(", ", $columnsCg);
        $columnsCd = empty($columnsCd) ? "*" : implode(", ", $columnsCd);

        $terms = "id_user=:id_user AND id_company=:id_company AND deleted=0";
        $params = ":id_user=" . $user->getId() . "&:id_company=" . $companyId . "";

        $response = $this->investmentCashFlow->find("", "", $columnsInv)
            ->join(
                CONF_DB_NAME . ".cash_flow_group",
                "id",
                $terms,
                $params,
                $columnsCg,
                "cash_flow_group_id",
                CONF_DB_NAME . ".investment_cash_flow",
            )->join(
                CONF_DB_NAME . ".cash_flow",
                "id_cash_flow_group",
                $terms,
                $params,
                $columnsCd,
                "id",
                CONF_DB_NAME . ".cash_flow_group"
            )->between("created_at", CONF_DB_NAME . ".cash_flow", $betweenData)->fetch(true);
        
        if (empty($response)) {
            return [];
        }
        return $response;
    }

    public function findInvestmentCashFlowByCashFlowGroupId(array $columns, int $cashFlowGroupId)
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->investmentCashFlow
            ->find(
                "cash_flow_group_id=:cash_flow_group_id",
                ":cash_flow_group_id=" . $cashFlowGroupId . ""
            )->fetch();
    }

    /** @var ModelsInvestmentCashFlow[] */
    public function findInvestmentCashFlowJoinCashFlowGroup(array $columnsInv, array $columnsCg, User $user, int $companyId): array
    {
        $columnsInv = empty($columnsInv) ? "*" : implode(", ", $columnsInv);
        $columnsCg = empty($columnsCg) ? "*" : implode(", ", $columnsCg);

        $response = $this->investmentCashFlow->find("", "", $columnsInv)
            ->join(
                CONF_DB_NAME . ".cash_flow_group",
                "id",
                "id_user=:id_user AND id_company=:id_company",
                ":id_user=" . $user->getId() . "&:id_company=" . $companyId . "",
                $columnsCg,
                "cash_flow_group_id",
                CONF_DB_NAME . ".investment_cash_flow"
            )->fetch(true);

        if (empty($response)) {
            return [];
        }

        return $response;
    }

    function findInvestmentCashFlowByUuid(array $columnsInv, array $columnsCg): ?ModelsInvestmentCashFlow
    {
        $columnsInv = empty($columnsInv) ? "*" : implode(", ", $columnsInv);
        $columnsCg = empty($columnsCg) ? "*" : implode(", ", $columnsCg);

        return $this->investmentCashFlow->find("", "", $columnsInv)
            ->join(
                CONF_DB_NAME . ".cash_flow_group",
                "id",
                "uuid=:uuid",
                ":uuid=" . $this->getUuid() . "",
                $columnsCg,
                "cash_flow_group_id",
                CONF_DB_NAME . ".investment_cash_flow"
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

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->investmentCashFlow, ModelsInvestmentCashFlow::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
