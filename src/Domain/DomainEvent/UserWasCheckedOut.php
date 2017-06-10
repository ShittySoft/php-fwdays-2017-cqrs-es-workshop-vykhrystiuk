<?php


namespace Building\Domain\DomainEvent;

use Prooph\EventSourcing\AggregateChanged;

class UserWasCheckedOut extends AggregateChanged
{
    public function buildingId() : string
    {
        return $this->payload['buildingId'];
    }

    public function userName() : string
    {
        return $this->payload['userName'];
    }
}