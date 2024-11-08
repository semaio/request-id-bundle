<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Generator;

use function uniqid;

final class PhpUniqidGenerator implements GeneratorInterface
{
    /**
     * @see http://php.net/manual/en/function.uniqid.php
     */
    public function __construct(private string $prefix = '', private bool $moreEntropy = false) {}

    /**
     * {@inheritdoc}
     */
    public function generate(): string
    {
        return uniqid($this->prefix, $this->moreEntropy);
    }
}
