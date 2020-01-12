<?php

namespace Hotrush\Crawler\LinkFilters;

use Psr\Http\Message\UriInterface;

interface LinkFilterInterface
{
    public function shouldCrawl(UriInterface $uri): bool;

    public function alreadyCrawled(UriInterface $uri): bool;

    public function addProcessed(UriInterface $uri): void;
}
