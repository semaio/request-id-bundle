<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Unit\Extension\Monolog;

use Monolog\DateTimeImmutable;
use Monolog\Level;
use Monolog\Logger;
use Monolog\LogRecord;
use PHPUnit\Framework\TestCase;
use Semaio\RequestId\Extension\Monolog\RequestIdProcessor;
use Semaio\RequestId\Provider\ProviderInterface;

class RequestIdProcessorTest extends TestCase
{
    private $provider;
    private $processor;

    protected function setUp(): void
    {
        $this->provider = $this->createMock(ProviderInterface::class);
        $this->processor = new RequestIdProcessor($this->provider);
    }

    /**
     * @test
     */
    public function it_will_add_request_id_if_provider_contains_request_id(): void
    {
        $this->provider->expects(static::once())->method('getRequestId')->willReturn('testRequestId');

        $record = call_user_func($this->processor, new LogRecord(
            datetime: new DateTimeImmutable(true),
            channel: 'test',
            level: Logger::toMonologLevel(Level::Debug),
            message: 'test',
            extra: []
        ));

        static::assertArrayHasKey('request_id', $record['extra']);
        static::assertEquals('testRequestId', $record['extra']['request_id']);
    }

    /**
     * @test
     */
    public function it_will_not_add_request_id_if_provider_does_not_contain_request_id(): void
    {
        $this->provider->expects(static::once())->method('getRequestId')->willReturn(null);

        $record = call_user_func($this->processor, new LogRecord(
            datetime: new DateTimeImmutable(true),
            channel: 'test',
            level: Logger::toMonologLevel(Level::Debug),
            message: 'test',
            extra: []
        ));

        static::assertArrayNotHasKey('request_id', $record['extra']);
    }
}
