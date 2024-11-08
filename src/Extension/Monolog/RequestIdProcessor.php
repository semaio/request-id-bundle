<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Extension\Monolog;

use Monolog\LogRecord;
use Semaio\RequestId\Provider\ProviderInterface;

final class RequestIdProcessor
{
    public function __construct(private ProviderInterface $provider) {}

    public function __invoke(LogRecord $record): LogRecord
    {
        if ($requestId = $this->provider->getRequestId()) {
            $record['extra']['request_id'] = $requestId; // @phpstan-ignore-line
        }

        return $record;
    }
}
