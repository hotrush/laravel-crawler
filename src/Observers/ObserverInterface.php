<?php

namespace Hotrush\Crawler\Observers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface ObserverInterface
{
    public function wilCrawl(UriInterface $uri);

    public function crawled(UriInterface $uri, ResponseInterface $response);

    public function crawlFailed(UriInterface $uri, \Throwable $e);

    public function finished();
}
