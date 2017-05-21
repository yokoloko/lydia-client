<?php

namespace AppBundle\Service\Lydia;

use Psr\Http\Message\ResponseInterface;

class Result
{
    /**
     * @var ResponseInterface
     */
    private $resonse;
    private $body;

    public function __construct(ResponseInterface $response)
    {
        $this->resonse = $response;
        $this->body    = json_decode($this->resonse->getBody()->getContents());
    }

    public function isSuccessful()
    {
        if ($this->resonse->getStatusCode() !== 200) {
            return false;
        }

        $content = $this->body;

        if (isset($content->error) && $content->error !== '0') {
            return false;
        }

        return true;
    }

    public function getContent()
    {
        return $this->body;
    }

    public function getErrorMessage()
    {
        return $this->body->message;
    }
}