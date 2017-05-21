<?php

namespace AppBundle\Service;

use AppBundle\Entity\Request;
use AppBundle\Entity\User;
use AppBundle\Service\Lydia\Result;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Symfony\Component\Routing\Router;

class Lydia
{
    /**
     * @var string
     */
    private $token;

    private $url;
    /**
     * @var Router $router
     */
    private $router;

    /**
     * @var ClientInterface
     */
    private $client;

    public function __construct($token, $url, $router, ClientInterface $client = null)
    {
        $this->token    = $token;
        $this->url      = $url;
        $this->router   = $router;

        if ($client === null) {
            $client = new Client();
        }

        $this->client   = $client;
    }

    /**
     * @param User $user
     * @return Lydia\User
     * @throws \Exception
     */
    public function register(User $user)
    {
        $params = [
            'vendor_token' => $this->token,
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail(),
            'phone' => $user->getPhone(),
            'password' => $user->getPassword()
        ];

        $result = $this->call($params, '/api/auth/register.json');

        if (!$result->isSuccessful()) {
            throw new \Exception($result->getErrorMessage());
        }

        return new Lydia\User($result->getContent());
    }

    /**
     * @param Request $request
     * @return Lydia\Status
     */
    public function status(Request $request)
    {
        $params = [
            'request_id' => $request->getRequestId(),
            'request_uuid' => $request->getRequestUuid(),
            'vendor_token' => $this->token
        ];

        $result = $this->call($params, '/api/request/state.json');

        return new Lydia\Status($result->getContent());
    }

    /**
     * @param Request $request
     * @return Lydia\Request
     * @throws \Exception
     */
    public function request(Request $request)
    {
        $mode = Router::ABSOLUTE_URL;
        $params = [
            'vendor_token' => $this->token,
            'user_token' => $request->getUser()->getPublicToken(),
            'amount' => $request->getAmount(),
            'currency' => $request->getCurrency(),
            'recipient' => $request->getUser()->getPhone(),
            'type' => 'phone',
            'threeDSecure' => 'no',
            'mobile_success_url' => $this->router->generate('status', ['id' => $request->getId()], $mode),
            'browser_success_url' => $this->router->generate('status', ['id' => $request->getId()], $mode)
        ];

        $result = $this->call($params, '/api/request/do.json');

        if (!$result->isSuccessful()) {
            throw new \Exception('Cannot do request ' . $result->getErrorMessage());
        }

        return new Lydia\Request($result->getContent());
    }

    /**
     * @param $params
     * @param $endpoint
     * @return Result
     */
    private function call($params, $endpoint)
    {
        $params['signature'] = $this->computeSignature($params, $this->token);

        $client = $this->client;
        $res = $client->request(
            'POST',
            $this->url . $endpoint,
            [
                'form_params' => $params,
                'Content-type' => 'application/octet-stream'
            ]
        );

        return new Result($res);
    }

    /**
     * @param ClientInterface $client
     */
    public function setClient(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param $params
     * @param $token
     * @return string
     */
    public function computeSignature($params, $token)
    {
        ksort($params); // alphabetical sorting

        $sig = array();

        foreach ($params as $key => $val) {
            $sig[] .= $key.'='.$val;
        }

        // Concat the private token (provider one or vendor one) and has the result
        return md5(implode("&", $sig)."&" . $token);
    }
}