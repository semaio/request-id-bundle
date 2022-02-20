<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Generator;

use function md5;

final class Md5Generator implements GeneratorInterface
{
    public function __construct(private GeneratorInterface $generator)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function generate(): string
    {
        return md5($this->generator->generate());
    }
}
