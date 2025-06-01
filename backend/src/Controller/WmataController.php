<?php

namespace App\Controller;

use App\Service\WmataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Psr\Log\LoggerInterface;

#[Route('/api/wmata', name: 'api_wmata_')]
class WmataController extends AbstractController
{
    public function __construct(
        private WmataService $wmataService,
        private LoggerInterface $logger
    ) {}

    #[Route('/stations', name: 'stations', methods: ['GET'])]
    public function getStations(): JsonResponse
    {
        try {
            $stations = $this->wmataService->getStations();
            return $this->json($stations);
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch stations: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->json(['error' => 'Failed to fetch stations: ' . $e->getMessage()], 500);
        }
    }

    #[Route('/predictions/{stationCode}', name: 'predictions', methods: ['GET'])]
    public function getPredictions(string $stationCode): JsonResponse
    {
        try {
            if (empty($stationCode)) {
                throw new BadRequestException('Station code is required');
            }
            
            $predictions = $this->wmataService->getNextTrains($stationCode);
            return $this->json($predictions);
        } catch (BadRequestException $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch predictions: ' . $e->getMessage(), [
                'stationCode' => $stationCode,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            return $this->json(['error' => 'Failed to fetch predictions: ' . $e->getMessage()], 500);
        }
    }
} 