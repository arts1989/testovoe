<?php

declare(strict_types=1);

namespace App\Message;

readonly class LogEntryMessage
{
    public function __construct(
        public string $batchId,
        public string $timestamp,
        public string $level,
        public string $service,
        public string $message,
        public ?array $context,
        public ?string $traceId,
        public string $publishedAt,
        public int $retryCount = 0,
    ) {
    }
}
