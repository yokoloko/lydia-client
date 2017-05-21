<?php

namespace AppBundle\Service\Lydia;

class User
{
    private $publicToken;

    public function __construct($data)
    {
        $this->publicToken = $data->user->public_token;
    }

    public function getPublicToken()
    {
        return $this->publicToken;
    }
}