<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Provider;

interface ProviderInterface
{
    /**
     * Get the identifier of the request.
     */
    public function getRequestId(): ?string;

    /**
     * Set the request ID.
     */
    public function setRequestId(?string $id): void;
}
