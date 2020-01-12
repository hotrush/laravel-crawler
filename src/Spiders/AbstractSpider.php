<?php

namespace Hotrush\Crawler\Spiders;

use Hotrush\Crawler\Client\ClientInterface;
use Hotrush\Crawler\Client\GuzzleClient;
use Hotrush\Crawler\Crawler;
use Hotrush\Crawler\LinkExtractors\DefaultLinkExtractor;
use Hotrush\Crawler\LinkExtractors\LinkExtractorInterface;
use Hotrush\Crawler\Queue\CollectionQueue;
use Hotrush\Crawler\Queue\QueueInterface;
use Hotrush\Crawler\UserAgent\StaticUserAgent;
use Hotrush\Crawler\UserAgent\UserAgentInterface;

abstract class AbstractSpider implements SpiderInterface
{
    /**
     * @inheritdoc
     */
    public function getClient(): ClientInterface
    {
        return new GuzzleClient();
    }

    /**
     * @inheritdoc
     */
    public function getLinkExtractor(): LinkExtractorInterface
    {
        return new DefaultLinkExtractor();
    }

    /**
     * @inheritdoc
     */
    public function getQueue(): QueueInterface
    {
        return new CollectionQueue();
    }

    /**
     * @inheritdoc
     */
    public function getUserAgent(): UserAgentInterface
    {
        return new StaticUserAgent();
    }

    /**
     * @inheritdoc
     */
    public function getCrawler(): Crawler
    {
        return (new Crawler($this->getClient()))
            ->withLinksExtractor($this->getLinkExtractor())
            ->withLinksFilter($this->getLinkFilter())
            ->withObserver($this->getObserver())
            ->withQueue($this->getQueue())
            ->withUserAgent($this->getUserAgent())
            ->withInitialRequest($this->getInitialRequest());
    }
}
