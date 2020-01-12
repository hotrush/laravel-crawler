<?php

namespace Hotrush\Crawler\LinkExtractors;

use Hotrush\Crawler\LinkFilters\LinkFilterInterface;
use Hotrush\Crawler\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Link;

class DefaultLinkExtractor implements LinkExtractorInterface
{
    /**
     * @param UriInterface $uri
     * @param ResponseInterface $response
     * @param LinkFilterInterface $linkFilter
     * @return Request[]
     */
    public function extractLinks(UriInterface $uri, ResponseInterface $response, LinkFilterInterface $linkFilter): array
    {
        $links = $this->extractLinksFromHtml($response->getBody()->getContents(), $uri);

        $filteredLinks = collect($links)
            ->map(function (UriInterface $uri) {
                return $this->normalizeUrl($uri);
            })
            ->filter(function (UriInterface $uri) {
                return $this->hasCrawlableScheme($uri);
            })
            ->filter(function (UriInterface $uri) {
                return strpos($uri->getPath(), '/tel:') === false;
            })
            ->filter(function (UriInterface $uri) use ($linkFilter) {
                return !$linkFilter->alreadyCrawled($uri) && $linkFilter->shouldCrawl($uri);
            })
            ->map(function (UriInterface $uri) {
                return new Request('GET', $uri, []);
            })
            ->toArray();

        return $filteredLinks;
    }

    /**
     * @param $html
     * @param UriInterface $uri
     * @return mixed
     */
    protected function extractLinksFromHtml($html, UriInterface $uri)
    {
        $domCrawler = new Crawler($html, $uri, $this->getBaseUri());

        return collect($domCrawler->filterXpath('//a | //link[@rel="next" or @rel="prev"]')->links())
            ->reject(function (Link $link) {
                return $link->getNode()->getAttribute('rel') === 'nofollow';
            })
            ->map(function (Link $link) {
                try {
                    return new Uri($link->getUri());
                } catch (\InvalidArgumentException $exception) {
                    return;
                }
            })
            ->filter();
    }

    /**
     * @param UriInterface $uri
     * @return bool
     */
    protected function hasCrawlableScheme(UriInterface $uri): bool
    {
        return in_array($uri->getScheme(), ['http', 'https']);
    }

    /**
     * @param UriInterface $uri
     * @return UriInterface
     */
    protected function normalizeUrl(UriInterface $uri): UriInterface
    {
        return $uri->withFragment('');
    }

    protected function getBaseUri()
    {
        return null;
    }
}
