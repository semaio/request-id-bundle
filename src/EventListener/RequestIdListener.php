<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\EventListener;

use Semaio\RequestId\Generator\GeneratorInterface;
use Semaio\RequestId\Policy\PolicyInterface;
use Semaio\RequestId\Provider\ProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class RequestIdListener implements EventSubscriberInterface
{
    public function __construct(
        /**
         * Used to provide the request ID to response or extensions.
         */
        private ProviderInterface $provider,

        /**
         * Used to generate a request ID if one isn't present.
         */
        private GeneratorInterface $generator,

        /**
         * Used to check if the request ID from request should be used.
         */
        private PolicyInterface $policy,

        /**
         * The header that will contain the request ID in the response.
         */
        private string $responseHeader,

        /**
         * The header to inspect for the incoming request ID.
         */
        private string $requestHeader
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 100],
            KernelEvents::RESPONSE => ['onResponse', -99],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        if (!$this->policy->canHandleRequest($request)) {
            return;
        }

        // Set request ID in our provider if request contains a request ID and policy allows to use this value.
        if ($this->policy->canTrustRequestIdHeader($request)
            && ($requestId = $request->headers->get($this->requestHeader))
        ) {
            $this->provider->setRequestId($requestId);

            return;
        }

        // If provider already has contains request ID set, we put that request ID into the request headers.
        if ($requestId = $this->provider->getRequestId()) {
            $request->headers->set($this->requestHeader, $requestId);

            return;
        }

        $requestId = $this->generator->generate();
        $request->headers->set($this->requestHeader, $requestId);
        $this->provider->setRequestId($requestId);
    }

    public function onResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        if ($requestId = $this->provider->getRequestId()) {
            $event->getResponse()->headers->set($this->responseHeader, $requestId);
        }
    }
}
