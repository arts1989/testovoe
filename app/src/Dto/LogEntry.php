<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

readonly class LogEntry
{
    public function __construct(
        #[Assert\Sequentially([
            new Assert\NotBlank(message: '"timestamp" is required.'),
            new Assert\Regex(
                pattern: '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(Z|[+-]\d{2}:\d{2})$/',
                message: '"timestamp" must be a valid ISO 8601 datetime.',
            ),
        ])]
        public ?string $timestamp = null,

        #[Assert\Sequentially([
            new Assert\NotBlank(message: '"level" is required.'),
            new Assert\Choice(
                choices: ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'],
                message: '"level" must be one of: emergency, alert, critical, error, warning, notice, info, debug.',
            ),
        ])]
        public ?string $level = null,

        #[Assert\NotBlank(message: '"service" is required.')]
        public ?string $service = null,

        #[Assert\NotBlank(message: '"message" is required.')]
        public ?string $message = null,

        public ?array  $context = null,
        public ?string $trace_id = null,
    ) {
    }
}
