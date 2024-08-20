<?php

namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\BalanceSheetExplanatoryNotes as ModelsBalanceSheetExplanatoryNotes;

/**
 * BalanceSheetExplanatoryNotes Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class BalanceSheetExplanatoryNotes
{
    /** @var int Id da tabela cash_flow */
    private int $id;

    /** @var string Uuid do cliente */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /** @var ModelsBalanceSheetExplanatoryNotes */
    private ModelsBalanceSheetExplanatoryNotes $balanceSheetExplanatoryNotes;

    /**
     * BalanceSheetExplanatoryNotes constructor
     */
    public function __construct()
    {
        $this->data = new \stdClass();
        $this->balanceSheetExplanatoryNotes = new ModelsBalanceSheetExplanatoryNotes();
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function findBalanceSheetExplanatoryNotesByUuid(array $columns): ?ModelsBalanceSheetExplanatoryNotes
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->balanceSheetExplanatoryNotes->find(
            "uuid=:uuid",
            ":uuid=" . $this->getUuid() . "",
            $columns
        )->fetch();
    }

    public function findBalanceSheetExplanatoryNotesJoinDataByUuid(array $columnsEx, array $columnsBalance, array $columnsChart, User $user, int $companyId): ?ModelsBalanceSheetExplanatoryNotes
    {
        $columnsEx = empty($columnsEx) ? "*" : implode(", ", $columnsEx);
        $columnsBalance = empty($columnsBalance) ? "*" : implode(", ", $columnsBalance);
        $columnsChart = empty($columnsChart) ? "*" : implode(", ", $columnsChart);

        $params = [
            CONF_DB_NAME . ".balance_sheet",
            "id",
            "id_user=:id_user AND id_company=:id_company",
            ":id_user=" . $user->getId() . "&:id_company=" . $companyId . "",
            $columnsBalance,
            "id_balance_sheet",
            CONF_DB_NAME . ".balance_sheet_explanatory_notes"
        ];

        $this->balanceSheetExplanatoryNotes = $this->balanceSheetExplanatoryNotes->find(
            "uuid=:uuid",
            ":uuid=" . $this->getUuid() . "",
            $columnsEx
        )->join(...$params);

        $params[0] = CONF_DB_NAME . ".chart_of_account";
        $params[4] = $columnsChart;
        $params[5] = "id_chart_of_account";
        $params[6] = CONF_DB_NAME . ".balance_sheet";

        return $this->balanceSheetExplanatoryNotes->join(...$params)->fetch();
    }

    /** @var ModelsBalanceSheetExplanatoryNotes[] */
    public function findAllBalanceSheetExplanatoryNotes(array $columnsEx, array $columnsBalance, array $columnsChart, User $user, int $companyId): array
    {
        $columnsEx = empty($columnsEx) ? "*" : implode(", ", $columnsEx);
        $columnsBalance = empty($columnsBalance) ? "*" : implode(", ", $columnsBalance);
        $columnsChart = empty($columnsChart) ? "*" : implode(", ", $columnsChart);

        $params = [
            CONF_DB_NAME . ".balance_sheet",
            "id",
            "id_user=:id_user AND id_company=:id_company",
            ":id_user=" . $user->getId() . "&:id_company=" . $companyId . "",
            $columnsBalance,
            "id_balance_sheet",
            CONF_DB_NAME . ".balance_sheet_explanatory_notes"
        ];

        $this->balanceSheetExplanatoryNotes = $this->balanceSheetExplanatoryNotes->find(
            "",
            "",
            $columnsEx
        )->join(...$params);

        $params[0] = CONF_DB_NAME . ".chart_of_account";
        $params[4] = $columnsChart;
        $params[5] = "id_chart_of_account";
        $params[6] = CONF_DB_NAME . ".balance_sheet";

        $response = $this->balanceSheetExplanatoryNotes->join(...$params)->fetch(true);
        if (empty($response)) {
            return [];
        }

        return $response;
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
        $tools = new Tools($this->balanceSheetExplanatoryNotes, ModelsBalanceSheetExplanatoryNotes::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
