<?php

declare(strict_types=1);

/**
 * This file is part of semaio/request-id-bundle.
 *
 * Copyright (c) semaio GmbH. For full copyright information
 * see LICENSE.md file distributed with this source code.
 */

namespace Semaio\RequestId\Extension\Monolog;

use Semaio\RequestId\Provider\ProviderInterface;

/**
 * @phpstan-import-type Record from \Monolog\Logger
 */
final class RequestIdProcessor
{
    public function __construct(private ProviderInterface $provider)
    {
    }

    /**
     * @phpstan-param Record $record
     * @phpstan-return Record
     */
    public function __invoke(array $record): array
    {
        if ($requestId = $this->provider->getRequestId()) {
            $record['extra']['request_id'] = $requestId;
        }

        return $record;
    }
}
