<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @codeCoverageIgnore
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $tree = new TreeBuilder('semaio_request_id');
        $tree->getRootNode()
            ->children()
            ->scalarNode('generator_service')
                ->info('The service name for the request ID generator. Defaults to `RamseyUuid4Generator`')
            ->end()
            ->scalarNode('policy_service')
                ->info('The service name for the request ID policy. Defaults to `DefaultPolicy`.')
            ->end()
            ->scalarNode('provider_service')
                ->info('The service name for request ID provider. Defaults to `SimpleRequestIdProvider`')
            ->end()
            ->scalarNode('response_header')
                ->cannotBeEmpty()
                ->defaultValue('X-Request-Id')
                ->info('The header the bundle will set the request ID at in the response')
            ->end()
            ->scalarNode('request_header')
                ->cannotBeEmpty()
                ->defaultValue('X-Request-Id')
                ->info('The header in which the bundle will look for and set request IDs')
            ->end()
            ->booleanNode('enable_monolog')
                ->info('Whether or not to turn on the request ID processor for monolog')
                ->defaultTrue()
            ->end()
            ->booleanNode('enable_twig')
                ->info('Whether or not to enable the twig `request_id()` function. Only works if TwigBundle is present.')
                ->defaultTrue()
            ->end()
            ->arrayNode('generators')
                ->addDefaultsIfNotSet()
                ->children()
                    ->arrayNode('md5')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('generator_service')->defaultNull()->end()
                        ->end()
                    ->end()
                    ->arrayNode('phpuniqid')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('prefix')->defaultValue('')->end()
                            ->booleanNode('more_entropy')->defaultFalse()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $tree;
    }
}
