<?php

namespace App\Providers;

use Crumbls\Pipeline\RateLimit\RateLimitedPipe;

class TestPipeline extends RateLimitedPipe
{
    protected int $requestsPerMinute = 30;

    protected function process($passable): mixed
    {
        dd($passable);
        // Your API processing logic here
        return $processedData;
    }
}
