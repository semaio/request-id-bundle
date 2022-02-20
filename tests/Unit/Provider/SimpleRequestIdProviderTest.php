<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Semaio\RequestId\Provider\SimpleRequestIdProvider;

class SimpleRequestIdProviderTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_set_and_retrieve_request_id(): void
    {
        $provider = new SimpleRequestIdProvider();
        static::assertNull($provider->getRequestId());

        $provider->setRequestId('test');
        static::assertEquals('test', $provider->getRequestId());

        $provider->setRequestId(null);
        static::assertNull($provider->getRequestId());
    }
}
