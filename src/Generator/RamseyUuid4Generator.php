<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Generator;

use Ramsey\Uuid\UuidFactoryInterface;

final class RamseyUuid4Generator implements GeneratorInterface
{
    public function __construct(private UuidFactoryInterface $factory)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function generate(): string
    {
        return (string) $this->factory->uuid4();
    }
}
