<?php

namespace Hotrush\Crawler;

use Hotrush\Crawler\Client\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class Request
{
    /**
     * @var string
     */
    protected $method;

    /**
     * @var UriInterface
     */
    protected $uri;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var \Throwable
     */
    protected $exception;

    /**
     * @var bool
     */
    protected $success = false;

    /**
     * Request constructor.
     *
     * @param $method
     * @param $uri
     * @param array $options
     */
    public function __construct($method, UriInterface $uri, array $options = [])
    {
        $this->method = $method;
        $this->uri = $uri;
        $this->options = $options;
    }

    /**
     * @param ClientInterface $client
     */
    public function execute(ClientInterface $client): void
    {
        try {
            echo "Request {$this->method} {$this->uri} ".json_encode($this->options).PHP_EOL;
            $this->response = $client->request($this->method, $this->uri, $this->options);
            $this->success = $this->response->getStatusCode() === 200;
        } catch (\Throwable $e) {
            $this->exception = $e;
        }
    }

    /**
     * @return bool
     */
    public function isSuccess(): bool
    {
        return $this->success === true;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @return \Throwable
     */
    public function getException(): \Throwable
    {
        return $this->exception;
    }

    public function getUri(): UriInterface
    {
        return $this->uri;
    }

    /**
     * Reset the request before re-queueing.
     */
    public function reset()
    {
        $this->success = false;
        $this->response = null;
        $this->exception = null;
    }
}
