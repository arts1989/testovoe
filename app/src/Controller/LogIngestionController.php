<?php

declare(strict_types=1);

namespace App\Controller;

use App\Dto\LogBatchRequest;
use App\Service\LogIngestionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

class LogIngestionController extends AbstractController
{
    public function __construct(
        private readonly LogIngestionService $logIngestionService,
    ) {
    }

    #[Route('/api/logs/ingest', name: 'api_logs_ingest', methods: ['POST'])]
    public function ingest(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] LogBatchRequest $batch,
    ): JsonResponse {
        $result = $this->logIngestionService->process($batch);

        return $this->json($result, Response::HTTP_ACCEPTED);
    }
}
