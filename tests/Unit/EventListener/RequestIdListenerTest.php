<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Unit\EventListener;

use PHPUnit\Framework\TestCase;
use Semaio\RequestId\EventListener\RequestIdListener;
use Semaio\RequestId\Generator\GeneratorInterface;
use Semaio\RequestId\Policy\DefaultPolicy;
use Semaio\RequestId\Policy\PolicyInterface;
use Semaio\RequestId\Policy\RejectRequestIdHeaderPolicy;
use Semaio\RequestId\Provider\ProviderInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class RequestIdListenerTest extends TestCase
{
    public const REQUEST_HEADER = 'Request-Id';
    public const RESPONSE_HEADER = 'Response-Id';

    private ProviderInterface $provider;
    private GeneratorInterface $generator;
    private PolicyInterface $policy;
    private RequestIdListener $listener;
    private EventDispatcherInterface $dispatcher;
    private Request $request;
    private Response $response;
    private HttpKernelInterface $kernel;

    protected function setUp(): void
    {
        $this->provider = $this->createMock(ProviderInterface::class);
        $this->generator = $this->createMock(GeneratorInterface::class);
        $this->policy = new DefaultPolicy();

        $this->listener = new RequestIdListener($this->provider, $this->generator, $this->policy, self::RESPONSE_HEADER, self::REQUEST_HEADER);

        $this->dispatcher = new EventDispatcher();
        $this->dispatcher->addSubscriber($this->listener);

        $this->request = Request::create('/');
        $this->response = new Response('Hello World!');
        $this->kernel = $this->createMock(HttpKernelInterface::class);
    }

    /**
     * @test
     */
    public function it_will_use_request_id_if_provider_contains_request_id(): void
    {
        $this->generator->expects(static::never())->method('generate');
        $this->provider->expects(static::once())->method('getRequestId')->willReturn('abc123');
        $this->provider->expects(static::never())->method('setRequestId');

        $event = new RequestEvent(
            $this->kernel,
            $this->request,
            HttpKernelInterface::MAIN_REQUEST
        );

        $this->dispatcher->dispatch($event, KernelEvents::REQUEST);

        static::assertEquals('abc123', $this->request->headers->get(self::REQUEST_HEADER));
    }

    /**
     * @test
     */
    public function it_will_generate_request_id_if_provider_contains_no_request_id(): void
    {
        $this->generator->expects(static::once())->method('generate')->willReturn('def234');

        $this->provider->expects(static::once())->method('getRequestId')->willReturn(null);
        $this->provider->expects(static::once())->method('setRequestId')->with('def234');

        $event = new RequestEvent($this->kernel, $this->request, HttpKernelInterface::MAIN_REQUEST);
        $this->dispatcher->dispatch($event, KernelEvents::REQUEST);

        static::assertEquals('def234', $this->request->headers->get(self::REQUEST_HEADER));
    }

    /**
     * @test
     */
    public function it_will_use_incoming_request_id_if_policy_trusts_request_id_header(): void
    {
        $listener = new RequestIdListener($this->provider, $this->generator, $this->policy, self::RESPONSE_HEADER, self::REQUEST_HEADER);

        $this->dispatcher->removeSubscriber($this->listener);
        $this->dispatcher->addSubscriber($listener);

        $this->request->headers->set(self::REQUEST_HEADER, 'testId');

        $this->generator->expects(static::never())->method('generate');
        $this->provider->expects(static::never())->method('getRequestId');
        $this->provider->expects(static::once())->method('setRequestId')->with('testId');

        $event = new RequestEvent($this->kernel, $this->request, HttpKernelInterface::MAIN_REQUEST);
        $this->dispatcher->dispatch($event, KernelEvents::REQUEST);
    }

    /**
     * @test
     */
    public function it_will_reject_incoming_request_id_if_policy_does_not_trust_request_id_header(): void
    {
        $policy = new RejectRequestIdHeaderPolicy();

        $listener = new RequestIdListener($this->provider, $this->generator, $policy, self::RESPONSE_HEADER, self::REQUEST_HEADER);

        $this->dispatcher->removeSubscriber($this->listener);
        $this->dispatcher->addSubscriber($listener);

        $this->generator->expects(static::once())->method('generate')->willReturn('def234');
        $this->provider->expects(static::once())->method('getRequestId')->willReturn(null);
        $this->provider->expects(static::once())->method('setRequestId')->with('def234');

        $this->request->headers->set(self::REQUEST_HEADER, 'abc123');

        $event = new RequestEvent($this->kernel, $this->request, HttpKernelInterface::MAIN_REQUEST);
        $this->dispatcher->dispatch($event, KernelEvents::REQUEST);

        static::assertEquals('def234', $this->request->headers->get(self::REQUEST_HEADER));
    }

    /**
     * @test
     */
    public function it_will_not_generate_request_id_if_request_is_not_main_request(): void
    {
        $this->provider->expects(static::never())->method('getRequestId');

        $event = new RequestEvent($this->kernel, $this->request, HttpKernelInterface::SUB_REQUEST);
        $this->dispatcher->dispatch($event, KernelEvents::REQUEST);
    }

    /**
     * @test
     */
    public function it_will_not_generate_request_id_if_response_is_not_main_request(): void
    {
        $this->provider->expects(static::never())->method('getRequestId');

        $event = new ResponseEvent($this->kernel, $this->request, HttpKernelInterface::SUB_REQUEST, $this->response);
        $this->dispatcher->dispatch($event, KernelEvents::RESPONSE);

        static::assertFalse($this->response->headers->has(self::REQUEST_HEADER));
    }

    /**
     * @test
     */
    public function it_will_not_generate_request_id_if_policy_rejects_handling_the_request(): void
    {
        $this->provider->expects(static::never())->method('getRequestId');

        $policy = $this->createMock(PolicyInterface::class);
        $policy->expects(static::once())->method('canHandleRequest')->willReturn(false);

        $listener = new RequestIdListener($this->provider, $this->generator, $policy, self::RESPONSE_HEADER, self::REQUEST_HEADER);

        $this->dispatcher->removeSubscriber($this->listener);
        $this->dispatcher->addSubscriber($listener);

        $event = new RequestEvent($this->kernel, $this->request, HttpKernelInterface::MAIN_REQUEST);
        $this->dispatcher->dispatch($event, KernelEvents::REQUEST);
    }

    /**
     * @test
     */
    public function it_will_contain_request_id_in_repsonse_if_provider_contains_request_id(): void
    {
        $this->provider->expects(static::once())->method('getRequestId')->willReturn('ghi345');

        $event = new ResponseEvent($this->kernel, $this->request, HttpKernelInterface::MAIN_REQUEST, $this->response);
        $this->dispatcher->dispatch($event, KernelEvents::RESPONSE);

        static::assertEquals('ghi345', $this->response->headers->get(self::RESPONSE_HEADER));
    }

    /**
     * @test
     */
    public function it_will_not_contain_request_id_if_provider_contains_no_request_id(): void
    {
        $this->provider->expects(static::once())->method('getRequestId')->willReturn(null);

        $event = new ResponseEvent($this->kernel, $this->request, HttpKernelInterface::MAIN_REQUEST, $this->response);
        $this->dispatcher->dispatch($event, KernelEvents::RESPONSE);

        static::assertFalse($this->response->headers->has(self::REQUEST_HEADER));
    }
}
