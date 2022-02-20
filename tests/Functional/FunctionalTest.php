<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Functional;

use Semaio\RequestId\Generator\GeneratorInterface;
use Semaio\RequestId\Policy\PolicyInterface;
use Semaio\RequestId\Provider\ProviderInterface;

class FunctionalTest extends RequestIdWebTestCase
{
    /**
     * @test
     */
    public function it_generates_request_id_and_passes_it_through_to_response(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $request = $client->getRequest();

        $response = $client->getResponse();
        static::assertSuccessfulResponse($response);

        $requestId = $client->getContainer()->get(ProviderInterface::class)->getRequestId();
        static::assertNotEmpty($requestId);
        static::assertEquals($requestId, $request->headers->get('X-Request-Id'));
        static::assertEquals($requestId, $response->headers->get('X-Request-Id'));
        static::assertStringContainsString($requestId, $response->getContent());
        static::assertLogsContainRequestId($client, $requestId);
    }

    /**
     * @test
     */
    public function it_does_not_reject_request_id_if_request_already_contains_one(): void
    {
        $requestId = 'testRequestId';

        $client = $this->createClient();
        $client->request('GET', '/', [], [], [
            'HTTP_X_REQUEST_ID' => $requestId,
        ]);

        $response = $client->getResponse();
        static::assertSuccessfulResponse($response);

        static::assertEquals($requestId, $response->headers->get('X-Request-Id'));
        static::assertEquals($requestId, $client->getContainer()->get(ProviderInterface::class)->getRequestId());
        static::assertStringContainsString($requestId, $response->getContent());
        static::assertLogsContainRequestId($client, $requestId);
    }

    /**
     * @test
     */
    public function it_uses_request_id_from_provider_if_provider_already_contains_one(): void
    {
        $testRequestId = 'testRequestId';

        $client = static::createClient();
        $client->getContainer()->get(ProviderInterface::class)->setRequestId($testRequestId);
        $client->request('GET', '/');

        $response = $client->getResponse();
        static::assertSuccessfulResponse($response);
        static::assertEquals($testRequestId, $response->headers->get('X-Request-Id'));

        $request = $client->getRequest();
        static::assertEquals($testRequestId, $request->headers->get('X-Request-Id'));
        static::assertStringContainsString($testRequestId, $response->getContent());
        static::assertLogsContainRequestId($client, $testRequestId);
    }

    /**
     * @test
     * @dataProvider serviceDataProvider
     */
    public function it_will_receive_services_for_interfaces_from_container(string $class): void
    {
        $client = static::createClient();

        $service = $client->getContainer()->get($class);
        static::assertInstanceOf($class, $service);
    }

    public static function serviceDataProvider(): \Generator
    {
        yield [GeneratorInterface::class];
        yield [PolicyInterface::class];
        yield [ProviderInterface::class];
    }
}
