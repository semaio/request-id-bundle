<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Functional;

use Semaio\RequestId\Test\TestKernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class RequestIdWebTestCase extends WebTestCase
{
    public static function assertSuccessfulResponse($response): void
    {
        static::assertInstanceOf(Response::class, $response);
        static::assertGreaterThanOrEqual(Response::HTTP_OK, $response->getStatusCode());
        static::assertLessThan(Response::HTTP_MULTIPLE_CHOICES, $response->getStatusCode());
    }

    public static function assertLogsContainRequestId(KernelBrowser $client, ?string $requestId): void
    {
        foreach (static::getMonologMessages($client) ?? [] as $message) {
            static::assertStringContainsString($requestId, $message); // veri
        }
    }

    public static function getMonologMessages(KernelBrowser $client): array
    {
        return $client->getContainer()->get('log.memory_handler')->getLogs();
    }

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }
}
