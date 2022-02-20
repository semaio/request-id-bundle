<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Policy;

use Symfony\Component\HttpFoundation\Request;

final class RejectRequestIdHeaderPolicy implements PolicyInterface
{
    /**
     * @inheritDoc
     */
    public function canHandleRequest(Request $request): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function canTrustRequestIdHeader(Request $request): bool
    {
        return false;
    }
}
