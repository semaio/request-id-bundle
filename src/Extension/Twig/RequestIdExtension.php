<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Extension\Twig;

use Semaio\RequestId\Provider\ProviderInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class RequestIdExtension extends AbstractExtension
{
    public function __construct(private ProviderInterface $provider)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('request_id', [$this->provider, 'getRequestId']),
        ];
    }
}
