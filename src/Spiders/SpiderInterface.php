<?php

namespace Hotrush\Crawler\Spiders;

use Hotrush\Crawler\Observers\ObserverInterface;
use Hotrush\Crawler\Client\ClientInterface;
use Hotrush\Crawler\Crawler;
use Hotrush\Crawler\LinkExtractors\LinkExtractorInterface;
use Hotrush\Crawler\LinkFilters\LinkFilterInterface;
use Hotrush\Crawler\Queue\QueueInterface;
use Hotrush\Crawler\Request;
use Hotrush\Crawler\UserAgent\UserAgentInterface;

interface SpiderInterface
{
    /**
     * @return ClientInterface
     */
    public function getClient(): ClientInterface;

    /**
     * @return LinkExtractorInterface
     */
    public function getLinkExtractor(): LinkExtractorInterface;

    /**
     * @return LinkFilterInterface
     */
    public function getLinkFilter(): LinkFilterInterface;

    /**
     * @return ObserverInterface
     */
    public function getObserver(): ObserverInterface;

    /**
     * @return QueueInterface
     */
    public function getQueue(): QueueInterface;

    /**
     * @return UserAgentInterface
     */
    public function getUserAgent(): UserAgentInterface;

    /**
     * @return Request
     */
    public function getInitialRequest(): Request;

    /**
     * @return Crawler
     */
    public function getCrawler(): Crawler;
}
