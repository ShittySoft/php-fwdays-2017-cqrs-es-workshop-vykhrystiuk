<?php


namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

class NotifySecurityOfCheckOutAnomaly extends Command
{
    private $buildingId;

    private $userName;

    public function __construct(Uuid $buildingId, string $userName)
    {
        $this->init();

        $this->buildingId = $buildingId;
        $this->userName = $userName;
    }

    public static function with(Uuid $buildingId, string $userName) : self
    {
        return new self($buildingId, $userName);
    }

    public function buildingId() : Uuid
    {
        return $this->buildingId;
    }

    public function userName() : string
    {
        return $this->userName;
    }

    /**
     * Return message payload as array
     *
     * The payload should only contain scalar types and sub arrays.
     * The payload is normally passed to json_encode to persist the message or
     * push it into a message queue.
     *
     * @return array
     */
    public function payload()
    {
        return [
            'buildingId' => $this->buildingId->toString(),
            'userName' => $this->userName()
        ];
    }

    /**
     * This method is called when message is instantiated named constructor fromArray
     *
     * @param array $payload
     * @return void
     */
    protected function setPayload(array $payload)
    {
        $this->buildingId = Uuid::fromString($payload['buildingId']);
        $this->userName = $payload['userName'];
    }
}