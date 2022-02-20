<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Provider;

final class SimpleRequestIdProvider implements ProviderInterface
{
    private ?string $requestId = null;

    /**
     * {@inheritdoc}
     */
    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    /**
     * {@inheritdoc}
     */
    public function setRequestId(?string $id): void
    {
        $this->requestId = $id;
    }
}
