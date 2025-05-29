<?php

namespace App\Controller;

use App\Service\WmataService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

#[Route('/api/wmata', name: 'api_wmata_')]
class WmataController extends AbstractController
{
    public function __construct(
        private WmataService $wmataService
    ) {}

    #[Route('/stations', name: 'stations', methods: ['GET'])]
    public function getStations(): JsonResponse
    {
        try {
            $stations = $this->wmataService->getStations();
            return $this->json($stations);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to fetch stations'], 500);
        }
    }

    #[Route('/predictions/{stationCode}', name: 'predictions', methods: ['GET'])]
    public function getPredictions(string $stationCode): JsonResponse
    {
        try {
            $predictions = $this->wmataService->getNextTrains($stationCode);
            return $this->json($predictions);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Failed to fetch predictions'], 500);
        }
    }
} 