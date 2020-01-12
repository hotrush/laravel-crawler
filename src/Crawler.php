<?php

namespace Hotrush\Crawler;

use Hotrush\Crawler\Client\ClientInterface;
use Hotrush\Crawler\LinkExtractors\LinkExtractorInterface;
use Hotrush\Crawler\LinkFilters\LinkFilterInterface;
use Hotrush\Crawler\Observers\ObserverInterface;
use Hotrush\Crawler\Queue\QueueInterface;
use Hotrush\Crawler\UserAgent\UserAgentInterface;

class Crawler
{
    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * @var UserAgentInterface
     */
    private $userAgent;

    /**
     * @var LinkFilterInterface
     */
    private $linkFilter;

    /**
     * @var LinkExtractorInterface
     */
    private $linkExtractor;

    /**
     * @var ObserverInterface
     */
    private $observer;

    /**
     * Crawler constructor.
     *
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param QueueInterface $queue
     * @return $this
     */
    public function withQueue(QueueInterface $queue): self
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @param UserAgentInterface $userAgent
     * @return Crawler
     */
    public function withUserAgent(UserAgentInterface $userAgent): self
    {
        $this->userAgent = $userAgent;

        return $this;
    }

    /**
     * @param LinkFilterInterface $linkFilter
     * @return Crawler
     */
    public function withLinksFilter(LinkFilterInterface $linkFilter): self
    {
        $this->linkFilter = $linkFilter;

        return $this;
    }

    /**
     * @param LinkExtractorInterface $linkExtractor
     * @return Crawler
     */
    public function withLinksExtractor(LinkExtractorInterface $linkExtractor): self
    {
        $this->linkExtractor = $linkExtractor;

        return $this;
    }

    /**
     * @param ObserverInterface $observer
     * @return Crawler
     */
    public function withObserver(ObserverInterface $observer): self
    {
        $this->observer = $observer;

        return $this;
    }

    /**
     * @param Request $request
     * @return Crawler
     */
    public function withInitialRequest(Request $request): self
    {
        $this->queue->push($request);

        return $this;
    }

    public function start()
    {
        while (!$this->queue->isEmpty()) {
            $this->processRequest($this->queue->shift());
        }

        $this->observer->finished();
    }

    /**
     * @param Request $request
     */
    protected function processRequest(Request $request)
    {
        $this->observer->wilCrawl($request->getUri());

        $request->execute($this->client);

        if ($request->isSuccess()) {
            $this->observer->crawled($request->getUri(), $request->getResponse());

            $this->linkFilter->addProcessed($request->getUri());

            $this->appendToQueue(
                $this->linkExtractor->extractLinks(
                    $request->getUri(),
                    $request->getResponse(),
                    $this->linkFilter
                )
            );
        } else {
            if ($this->observer->crawlFailed($request->getUri(), $request->getException()) === true) {
                $request->reset();
                $this->appendToQueue([$request]);
            }
        }
    }

    /**
     * @param Request[] $requests
     */
    protected function appendToQueue(array $requests): void
    {
        foreach ($requests as $request) {
            $this->queue->push($request);
        }
    }
}
