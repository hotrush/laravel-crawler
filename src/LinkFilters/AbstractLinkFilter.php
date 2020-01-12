<?php

namespace Hotrush\Crawler\LinkFilters;

use Psr\Http\Message\UriInterface;

abstract class AbstractLinkFilter implements LinkFilterInterface
{
    protected $processed = [];

    public function addProcessed(UriInterface $uri): void
    {
        $uri = (string) $uri;

        if (!in_array($uri, $this->processed)) {
            $this->processed[] = $uri;
        }
    }

    public function alreadyCrawled(UriInterface $uri): bool
    {
        return in_array((string) $uri, $this->processed);
    }
}
