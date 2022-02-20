<?php

declare(strict_types=1);

namespace Semaio\RequestId\Test\Functional\Stubs;

use Monolog\Handler\AbstractProcessingHandler;

final class MemoryHandler extends AbstractProcessingHandler implements \Countable
{
    private array $logs = [];

    public function count(): int
    {
        return count($this->logs);
    }

    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record): void
    {
        $this->logs[] = (string) $record['formatted'];
    }
}
