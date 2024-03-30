<?php
namespace Source\Models;

use Source\Core\Model;

/**
 * Customer C:\php-projects\sistema-contabil\source\Models
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Models
 */
class Customer extends Model
{
    /** @var string Uuid de identificação  */
    protected string $uuid;

    /** @var string Nome do cliente */
    protected string $customerName = "customer_name";

    /** @var string Documento, cpf, rg, cnpj */
    protected string $customerDocument = "customer_document";

    /** Data de aniversário */
    protected string $birthDate = "birth_date";

    /** @var string Gênero, masculino ou feminino */
    protected string $customerGender = "customer_gender";

    /** @var string E-mail do cliente */
    protected string $customerEmail = "customer_email";

    /** @var string Cep do cliente */
    protected string $customerZipcode = "customer_zipcode";

    /** @var string Endereço do cliente */
    protected string $customerAddress = "customer_address";

    /** @var string Número da casa */
    protected string $customerNumber = "customer_number";

    /** @var string Bairro */
    protected string $customerNeighborhood = "customer_neighborhood";

    /** @var string Cidade */
    protected string $customerCity = "customer_city";

    /** @var string Estado  */
    protected string $customerState = "customer_state";

    /** @var string Telefone fixo */
    protected string $customerPhone = "customer_phone";

    /** @var string Celular */
    protected string $cellPhone = "cell_phone";

    /**
     * Customer constructor
     */
    public function __construct()
    {
        parent::__construct(CONF_DB_NAME . ".customer", ["id"], [
            $this->uuid,
            $this->customerName,
            $this->customerDocument,
            $this->birthDate,
            $this->customerGender,
            $this->customerEmail,
            $this->customerZipcode,
            $this->customerAddress,
            $this->customerNumber,
            $this->customerNeighborhood,
            $this->customerCity,
            $this->customerState
        ]);
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
