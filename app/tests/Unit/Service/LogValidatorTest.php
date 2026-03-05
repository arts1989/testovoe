<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Dto\LogBatchRequest;
use App\Dto\LogEntry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LogValidatorTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    public function testValidBatchPassesValidation(): void
    {
        $batch = new LogBatchRequest([
            new LogEntry(
                timestamp: '2026-02-26T10:30:45Z',
                level: 'error',
                service: 'auth-service',
                message: 'Auth failed',
                context: ['user_id' => 123],
                trace_id: 'abc123',
            ),
        ]);

        $violations = $this->validator->validate($batch);

        $this->assertCount(0, $violations);
    }

    public function testInvalidBatchFailsValidation(): void
    {
        $batch = new LogBatchRequest([
            new LogEntry(
                timestamp: 'not-a-date',
                level: 'supercritic',
            ),
        ]);

        $violations = $this->validator->validate($batch);

        $this->assertGreaterThanOrEqual(3, count($violations));

        $messages = array_map(fn($v) => $v->getMessage(), iterator_to_array($violations));
        $combined = implode(' ', $messages);

        $this->assertStringContainsString('timestamp', $combined);
        $this->assertStringContainsString('level', $combined);
        $this->assertStringContainsString('service', $combined);
    }
}
