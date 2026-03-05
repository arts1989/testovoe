<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\LogBatchRequest;
use App\Message\LogEntryMessage;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Uid\Uuid;

readonly class LogIngestionService
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    public function process(LogBatchRequest $batch): array
    {
        $batchId = 'batch_' . str_replace('-', '', Uuid::v4()->toRfc4122());
        $publishedAt = (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM);

        foreach ($batch->logs as $entry) {
            $this->messageBus->dispatch(
                new LogEntryMessage(
                    $batchId,
                    $entry->timestamp,
                    $entry->level,
                    $entry->service,
                    $entry->message,
                    $entry->context,
                    $entry->trace_id,
                    $publishedAt,
                )
            );
        }

        return [
            'status' => 'accepted',
            'batch_id' => $batchId,
            'logs_count' => count($batch->logs),
        ];
    }
}
