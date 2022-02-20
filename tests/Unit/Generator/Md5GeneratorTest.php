<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Unit\Generator;

use PHPUnit\Framework\TestCase;
use Semaio\RequestId\Generator\GeneratorInterface;
use Semaio\RequestId\Generator\Md5Generator;

class Md5GeneratorTest extends TestCase
{
    private GeneratorInterface $generator;

    protected function setUp(): void
    {
        $decoratedGenerator = $this->createMock(GeneratorInterface::class);
        $decoratedGenerator->method('generate')->willReturn('test');

        $this->generator = new Md5Generator($decoratedGenerator);
    }

    /**
     * @test
     */
    public function it_can_generate_request_id(): void
    {
        $requestId = $this->generator->generate();

        static::assertNotEmpty($requestId);
        static::assertEquals('098f6bcd4621d373cade4e832627b4f6', $requestId);
    }
}
