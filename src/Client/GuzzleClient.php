<?php

namespace Hotrush\Crawler\Client;

use GuzzleHttp\Client;
use Hotrush\Crawler\UserAgent\UserAgentInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class GuzzleClient implements ClientInterface
{
    /**
     * @var UserAgentInterface
     */
    private $userAgent;

    /**
     * @var Client
     */
    private $client;

    /**
     * GuzzleClient constructor.
     *
     * @param UserAgentInterface $userAgent
     * @param array $options
     */
    public function __construct(UserAgentInterface $userAgent = null, array $options = [])
    {
        $this->userAgent = $userAgent;
        $this->client = new Client($options);
    }

    /**
     * @inheritdoc
     */
    public function request($method, UriInterface $uri, array $options = []): ResponseInterface
    {
        $options['headers']['User-Agent'] = $this->userAgent ? $this->userAgent->getUserAgent() : ClientInterface::DEFAULT_USER_AGENT;

        return $this->client->request($method, (string) $uri, $options);
    }
}
