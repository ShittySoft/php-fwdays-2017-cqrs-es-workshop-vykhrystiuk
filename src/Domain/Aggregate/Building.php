<?php

declare(strict_types=1);

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\NewBuildingWasRegistered;
use Building\Domain\DomainEvent\UserWasCheckedIn;
use Building\Domain\DomainEvent\UserWasCheckedOut;
use Prooph\EventSourcing\AggregateRoot;
use Rhumsaa\Uuid\Uuid;

final class Building extends AggregateRoot
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $name;

    private $checkInUsers;

    public static function new(string $name) : self
    {
        $self = new self();

        $self->recordThat(NewBuildingWasRegistered::occur(
            (string) Uuid::uuid4(),
            [
                'name' => $name
            ]
        ));

        return $self;
    }

    public function checkInUser(string $username)
    {
        if (array_key_exists($username, $this->checkInUsers)) {
            throw new \Exception('Already checkIn');
        }

        $this->recordThat(UserWasCheckedIn::occur(
            $this->aggregateId(),
            [
                'userName' => $username
            ]
        ));
    }

    public function checkOutUser(string $username)
    {
        if (!array_key_exists($username, $this->checkInUsers)) {
            throw new \Exception('Already checkout');
        }

        $this->recordThat(UserWasCheckedOut::occur(
            $this->aggregateId(),
            [
                'userName' => $username
            ]
        ));
    }

    public function whenUserWasCheckedIn(UserWasCheckedIn $event)
    {
        $this->checkInUsers[$event->userName()] = null;
    }

    public function whenUserWasCheckedOut(UserWasCheckedOut $event)
    {
        unset($this->checkInUsers[$event->userName()]);
    }

    public function whenNewBuildingWasRegistered(NewBuildingWasRegistered $event)
    {
        $this->uuid = $event->uuid();
        $this->name = $event->name();
    }

    /**
     * {@inheritDoc}
     */
    protected function aggregateId() : string
    {
        return (string) $this->uuid;
    }

    /**
     * {@inheritDoc}
     */
    public function id() : string
    {
        return $this->aggregateId();
    }
}
