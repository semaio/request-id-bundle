<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Unit\Extension\Twig;

use PHPUnit\Framework\TestCase;
use Semaio\RequestId\Extension\Twig\RequestIdExtension;
use Semaio\RequestId\Provider\ProviderInterface;
use Semaio\RequestId\Provider\SimpleRequestIdProvider;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class RequestIdExtensionTest extends TestCase
{
    public const TEMPLATE = '{{ request_id() }}';

    private ProviderInterface $provider;
    private Environment $twig;

    protected function setUp(): void
    {
        $this->provider = new SimpleRequestIdProvider();

        $loader = new ArrayLoader([
            'test' => self::TEMPLATE,
        ]);

        $this->twig = new Environment($loader);
        $this->twig->addExtension(new RequestIdExtension($this->provider));
    }

    /**
     * @test
     */
    public function it_will_contain_request_id_provider_contains_request_id(): void
    {
        $this->provider->setRequestId('testRequestId');

        $result = $this->twig->render('test');

        static::assertSame($result, 'testRequestId');
    }
}
