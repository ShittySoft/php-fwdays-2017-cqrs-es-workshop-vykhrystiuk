<?php


namespace Building\Domain\Command;

use Prooph\Common\Messaging\Command;
use Rhumsaa\Uuid\Uuid;

class CheckInUser extends Command
{
    /**
     * @var Uuid
     */
    private $buildingId;
    /**
     * @var string
     */
    private $userName;

    private function __construct(Uuid $buildingId, string $name)
    {
        $this->init();

        $this->buildingId = $buildingId;
        $this->userName = $name;
    }

    public static function fromBuildingAndName(Uuid $buildingId, string $name) : self
    {
        return new self($buildingId, $name);
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
            'userName' => $this->userName
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