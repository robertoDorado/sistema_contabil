<?php
namespace Source\Domain\Model;

use Exception;
use Ramsey\Uuid\Nonstandard\Uuid;
use Source\Domain\Support\Tools;
use Source\Models\SubscriptionCancellation as ModelsSubscriptionCancellation;

/**
 * SubscriptionCancellation Domain\Model
 * @link 
 * @author Roberto Dorado <robertodorado7@gmail.com>
 * @package Source\Domain\Model
 */
class SubscriptionCancellation
{
    /** @var int Id */
    private int $id;

    /** @var object */
    private object $data;

    /** @var string Uuid do usuário */
    private string $uuid;

    /** @var Tools */
    private Tools $tools;

    /** @var ModelsSubscriptionCancellation */
    private ModelsSubscriptionCancellation $subscriptionCancellation;

    /**
     * SubscriptionCancellation constructor
     */
    public function __construct()
    {
        $this->subscriptionCancellation = new ModelsSubscriptionCancellation();
        $this->data = new \stdClass();
        $this->tools = new Tools($this->subscriptionCancellation, ModelsSubscriptionCancellation::class);
    }

    public function __get($name)
    {
        return $this->data->$name ?? null;
    }

    public function __set($name, $value)
    {
        $this->data->$name = $value;
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

    public function updateData(array $data): bool
    {
        $response = $this->tools->updateData(
            "uuid=:uuid",
            ":uuid={$data['uuid']}",
            $data,
            "cancelamento da assinatura não encontrado"
        );
        $this->data->message = !empty($this->tools->message) ? $this->tools->message : "";
        return !empty($response) ? true : false;
    }

    public function persistData(array $data): bool
    {
        $response = $this->tools->persistData($data);
        $this->data->message = !empty($this->tools->message) ? $this->tools->message : "";

        !empty($response) ? $this->setId($this->tools->lastId) : null;
        return !empty($response) ? true : false;
    }
}
