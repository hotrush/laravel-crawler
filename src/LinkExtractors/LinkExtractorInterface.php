<?php

namespace Hotrush\Crawler\LinkExtractors;

use Hotrush\Crawler\LinkFilters\LinkFilterInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

interface LinkExtractorInterface
{
    public function extractLinks(UriInterface $uri, ResponseInterface $response, LinkFilterInterface $linkFilter): array;
}
