<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Generator;

interface GeneratorInterface
{
    /**
     * Create a new request ID.
     */
    public function generate(): string;
}
