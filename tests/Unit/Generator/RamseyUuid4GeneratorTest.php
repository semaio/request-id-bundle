<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Unit\Generator;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidFactory;
use Semaio\RequestId\Generator\GeneratorInterface;
use Semaio\RequestId\Generator\RamseyUuid4Generator;

class RamseyUuid4GeneratorTest extends TestCase
{
    private GeneratorInterface $generator;

    protected function setUp(): void
    {
        $this->generator = new RamseyUuid4Generator(new UuidFactory());
    }

    /**
     * @test
     */
    public function it_can_generate_request_id(): void
    {
        $requestId = $this->generator->generate();

        static::assertNotEmpty($requestId);
        static::assertIsString($requestId);
    }
}
