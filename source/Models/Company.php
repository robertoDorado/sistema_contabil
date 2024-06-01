<?php

namespace Source\Models;

use Source\Core\Model;

/**
 * Company Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Models
 */
class Company extends Model
{
    /** @var string Uuid da empresa */
    protected string $uuid = "uuid";

    /** @var string Id do usuário */
    protected string $idUser = "id_user";

    /** @var string razão social */
    protected string $companyName = "company_name";

    /** @var string CNPJ */
    protected string $companyDocument = "company_document";

    /** @var string Inscrição estadual */
    protected string $stateRegistration = "state_registration";

    /** @var string Data de abertura */
    protected string $openingDate = "opening_date";

    /** @var string Site */
    protected string $webSite = "web_site";

    /** @var string E-mail da empresa */
    protected string $companyEmail = "company_email";

    /** @var string Cep da empresa */
    protected string $companyZipcode = "company_zipcode";

    /** @var string Endereço da empresa */
    protected string $companyAddress = "company_address";

    /** @var string Número do endereço da empresa */
    protected string $companyAddressNumber = "company_address_number";

    /** @var string Bairro da empresa */
    protected string $companyNeighborhood = "company_neighborhood";

    /** @var string Cidade da empresa */
    protected string $companyCity = "company_city";

    /** @var string Estado da empresa */
    protected string $companyState = "company_state";

    /** @var string Telefone da empresa */
    protected string $companyPhone = "company_phone";

    /** @var string Celular da empresa */
    protected string $companyCellPhone = "company_cell_phone";

    /** @var string Data de criação do registro */
    protected string $createdAt = "created_at";

    /** @var string Data de atualização */
    protected string $updatedAt = "updated_at";

    /** @var string Soft delete do registro */
    protected string $deleted = "deleted";

    /**
     * Company constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".company", ["id"], [
            $this->uuid,
            $this->id_user,
            $this->companyName,
            $this->companyDocument,
            $this->stateRegistration,
            $this->openingDate,
            $this->webSite,
            $this->companyEmail,
            $this->companyZipcode,
            $this->companyAddress,
            $this->companyAddressNumber,
            $this->companyNeighborhood,
            $this->companyCity,
            $this->companyState,
            $this->companyPhone,
            $this->companyCellPhone,
            $this->createdAt,
            $this->updatedAt,
            $this->deleted
        ]);
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted(int $delete)
    {
        $this->deleted = $delete;
    }

    public function setUuid(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }
}