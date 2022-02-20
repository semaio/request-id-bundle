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

interface PolicyInterface
{
    public function canHandleRequest(Request $request): bool;

    public function canTrustRequestIdHeader(Request $request): bool;
}
