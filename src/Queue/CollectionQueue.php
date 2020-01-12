<?php

namespace Hotrush\Crawler\Queue;

use Hotrush\Crawler\Request;
use Illuminate\Support\Collection;

class CollectionQueue implements QueueInterface
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $queue;

    public function __construct()
    {
        $this->queue = new Collection();
    }

    public function push(Request $request)
    {
        if (!$this->queue->contains($request)) {
            $this->queue->push($request);
        }
    }

    public function shift(): Request
    {
        if ($this->isEmpty()) {
            throw new \InvalidArgumentException('Queue is empty. Nothing to shift.');
        }

        return $this->queue->shift();
    }

    public function isEmpty(): bool
    {
        return $this->queue->isEmpty();
    }

    public function count(): int
    {
        return $this->queue->count();
    }
}
