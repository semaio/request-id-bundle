<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\DependencyInjection;

use Ramsey\Uuid\UuidFactory;
use Semaio\RequestId\EventListener\RequestIdListener;
use Semaio\RequestId\Extension\Monolog\RequestIdProcessor;
use Semaio\RequestId\Extension\Twig\RequestIdExtension;
use Semaio\RequestId\Generator\GeneratorInterface;
use Semaio\RequestId\Generator\Md5Generator;
use Semaio\RequestId\Generator\PhpUniqidGenerator;
use Semaio\RequestId\Generator\RamseyUuid4Generator;
use Semaio\RequestId\Policy\DefaultPolicy;
use Semaio\RequestId\Policy\PolicyInterface;
use Semaio\RequestId\Policy\RejectRequestIdHeaderPolicy;
use Semaio\RequestId\Provider\ProviderInterface;
use Semaio\RequestId\Provider\SimpleRequestIdProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

/**
 * @codeCoverageIgnore
 */
final class SemaioRequestIdExtension extends ConfigurableExtension
{
    /**
     * {@inheritdoc}
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container): void
    {
        // Register generators
        $container->register('semaio_requestid_uuid_factory', UuidFactory::class)->setPublic(false);
        $container->register(RamseyUuid4Generator::class)->setPublic(false)
            ->addArgument(new Reference('semaio_requestid_uuid_factory'));

        $container->register(PhpUniqidGenerator::class)->setPublic(false)
            ->setArguments([
                $mergedConfig['generators']['phpuniqid']['prefix'],
                $mergedConfig['generators']['phpuniqid']['more_entropy'],
            ]);

        $decoratedGenerator = $mergedConfig['generators']['md5']['generator_service'] ?? RamseyUuid4Generator::class;
        $container->register(Md5Generator::class)->setPublic(false)
            ->addArgument(new Reference($decoratedGenerator));

        // Register policies
        $container->register(DefaultPolicy::class)->setPublic(false);
        $container->register(RejectRequestIdHeaderPolicy::class)->setPublic(false);

        // Register providers
        $container->register(SimpleRequestIdProvider::class)->setPublic(false);

        // Register services
        $generator = $mergedConfig['generator_service'] ?? RamseyUuid4Generator::class;
        $container->setAlias(GeneratorInterface::class, $generator)->setPublic(true);

        $policy = $mergedConfig['policy_service'] ?? DefaultPolicy::class;
        $container->setAlias(PolicyInterface::class, $policy)->setPublic(true);

        $provider = $mergedConfig['provider_service'] ?? SimpleRequestIdProvider::class;
        $container->setAlias(ProviderInterface::class, $provider)->setPublic(true);

        // Register event listener
        $container->register(RequestIdListener::class)
            ->setArguments([
                new Reference($provider),
                new Reference($generator),
                new Reference($policy),
                $mergedConfig['response_header'],
                $mergedConfig['request_header'],
            ])
            ->setPublic(false)
            ->addTag('kernel.event_subscriber');

        // Register monolog extension
        if (!empty($mergedConfig['enable_monolog'])) {
            $container->register(RequestIdProcessor::class)
                ->addArgument(new Reference($provider))
                ->setPublic(false)
                ->addTag('monolog.processor');
        }

        // Register twig extension
        if (class_exists('Twig\Extension\AbstractExtension') && !empty($mergedConfig['enable_twig'])) {
            $container->register(RequestIdExtension::class)
                ->addArgument(new Reference($provider))
                ->setPublic(false)
                ->addTag('twig.extension');
        }
    }
}
