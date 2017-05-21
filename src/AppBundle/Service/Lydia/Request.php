<?php

namespace AppBundle\Service\Lydia;

class Request
{
    private $requestUuid;
    private $requestId;
    private $url;

    public function __construct($data)
    {
        $this->requestUuid = $data->request_uuid;
        $this->requestId = $data->request_id;
        $this->url       = $data->mobile_url;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    public function getRequestUuid()
    {
        return $this->requestUuid;
    }

    public function getUrl()
    {
        return $this->url;
    }
}