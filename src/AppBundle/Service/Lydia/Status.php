<?php

namespace AppBundle\Service\Lydia;

class Status
{
    const STATUSES = [
        1  => 'Request accepted (transaction completed if the amount is greater than 0€)',
        0  => 'Waiting to be accepted',
        5  => 'Refused by the payer',
        6  => 'Cancelled by the owner',
        -1 => 'Unknown (should be considered as an invalid request)'
    ];

    private $state;

    public function __construct($data)
    {
        $this->state = $data->state;
    }

    public function getStateMessage()
    {
        return self::STATUSES[$this->state];
    }
}