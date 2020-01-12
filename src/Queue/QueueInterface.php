<?php

namespace Hotrush\Crawler\Queue;

use Hotrush\Crawler\Request;

interface QueueInterface
{
    public function push(Request $request);

    public function shift(): Request;

    public function isEmpty(): bool;

    public function count(): int;
}
