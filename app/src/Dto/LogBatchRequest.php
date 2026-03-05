<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class LogBatchRequest
{
    /**
     * @param LogEntry[] $logs
     */
    public function __construct(
        #[Assert\Valid]
        #[Assert\Count(
            min: 1,
            max: 1000,
            minMessage: 'The "logs" field is required and must not be empty.',
            maxMessage: 'Batch size exceeds the maximum of {{ limit }} logs.',
        )]
        public array $logs = [],
    ) {
    }
}
