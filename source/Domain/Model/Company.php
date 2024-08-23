<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\Company as ModelsCompany;
use Source\Support\Message;

/**
 * Company Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class Company
{
    /** @var ModelsCompany Modelo tabela empresa */
    private ModelsCompany $company;

    /** @var int Id da tabela cash_flow */
    private int $id;

    /** @var string Uuid do cliente */
    private string $uuid;

    /** @var object|null */
    private object $data;

    /**
     * Company constructor
     */
    public function __construct()
    {
        $this->data = new \stdClass();
        $this->company = new ModelsCompany();
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function dropCompanyByUuid(): bool
    {
        $companyData = $this->company->find("uuid=:uuid", ":uuid=" . $this->getUuid() . "")->fetch();
        return $companyData->destroy();
    }

    /** @var ModelsCompany[] */
    public function findAllCompanyByUserDeleted(array $columns) :array
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $idUser = empty($this->data->id_user) ? 0 : $this->data->id_user;
        $companyData = $this->company->find("id_user=:id_user AND deleted=1", ":id_user=" . $idUser . "", $columns)
        ->fetch(true);

        if (empty($companyData)) {
            return [];
        }

        return $companyData;
    }

    public function findCompanyById(array $columns = []) : ?ModelsCompany
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->company->find("id=:id AND deleted=0", ":id=" . $this->getId() . "", $columns)->fetch();
    }

    public function updateCompanyByUuid(array $data): bool
    {
        $tools = new Tools($this->company, ModelsCompany::class);
        $response = $tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "registro de fluxo de caixa não encontrado"
        );
        $this->data->message = !empty($tools->message) ? $tools->message : "";
        return !empty($response) ? true : false;
    }

    public function findCompanyByUuid(array $columns = []): ?ModelsCompany
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        return $this->company->find("uuid=:uuid AND deleted=0", ":uuid=" . $this->getUuid() . "", $columns)->fetch();
    }

    /** @var ModelsCompany[] */
    public function findCompanyByUser(array $columns = []) :array
    {
        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $idUser = empty($this->data->id_user) ? 0 : $this->data->id_user;
        
        $companyData = $this->company->find("id_user=:id_user AND deleted=0", ":id_user={$idUser}", $columns)
        ->fetch(true);
        
        if (empty($companyData)) {
            return [];
        }
        
        return $companyData;
    }

    /** @var ModelsCompany[]  */
    public function findAllCompanyByUserId(array $columns = []) : array
    {
        $idUser = empty($this->data->id_user) ? 0 : $this->data->id_user;
        $columns = !empty($columns) ? implode(", ", $columns) : "*";
        $companyData = $this->company->find("id_user=:id_user AND deleted=0", ":id_user=" . $idUser . "", $columns)
        ->fetch(true);

        if (empty($companyData)) {
            return [];
        }

        return $companyData;
    }

    public function findCompanyByUserId(array $columns = []): ?ModelsCompany
    {
        $idUser = empty($this->data->id_user) ? 0 : $this->data->id_user;
        $columns = !empty($columns) ? implode(", ", $columns) : "*";
        return $this->company->find("id_user=:id_user AND deleted=0", ":id_user=" . $idUser . "", $columns)
        ->fetch();
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

    public function getId(): int
    {
        if (!isset($this->id)) {
            throw new Exception("id não atribuido");
        }
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function persistData(array $data): bool
    {
        $tools = new Tools($this->company, ModelsCompany::class);
        $response = $tools->persistData($data);
        $this->data->message = !empty($tools->message) ? $tools->message : "";

        !empty($response) ? $this->setId($tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
