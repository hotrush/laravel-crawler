<?php

namespace Hotrush\Crawler\Client;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ClientInterface
{
    /**
     * @param $method
     * @param UriInterface $uri
     * @param array $options
     * @return ResponseInterface
     */
    public function request($method, UriInterface $uri, array $options = []): ResponseInterface;
}
