<?php

declare(strict_types=1);

namespace Building\Domain\Aggregate;

use Building\Domain\DomainEvent\CheckedInAnomalyDetected;
use Building\Domain\DomainEvent\CheckedOutAnomalyDetected;
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
        $anomalyDetected = array_key_exists($username, $this->checkInUsers);

        $this->recordThat(UserWasCheckedIn::occur(
            $this->aggregateId(),
            [
                'userName' => $username
            ]
        ));

        if ($anomalyDetected) {
            $this->recordThat(CheckedInAnomalyDetected::occur(
                $this->aggregateId(),
                [
                    'userName' => $username
                ]
            ));
        }
    }

    public function checkOutUser(string $username)
    {
        $anomalyDetected = !array_key_exists($username, $this->checkInUsers);

        $this->recordThat(UserWasCheckedOut::occur(
            $this->aggregateId(),
            [
                'userName' => $username
            ]
        ));

        if ($anomalyDetected) {
            $this->recordThat(CheckedOutAnomalyDetected::occur(
                $this->aggregateId(),
                [
                    'userName' => $username
                ]
            ));
        }
    }

    public function whenUserWasCheckedIn(UserWasCheckedIn $event)
    {
        $this->checkInUsers[$event->userName()] = null;
    }

    public function whenCheckedOutAnomalyDetected(CheckedOutAnomalyDetected $event)
    {
        //$this->checkInUsers[$event->userName()] = null;
    }

    public function whenCheckedInAnomalyDetected(CheckedInAnomalyDetected $event)
    {
        //$this->checkInUsers[$event->userName()] = null;
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
