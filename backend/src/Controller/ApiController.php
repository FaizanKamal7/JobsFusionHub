<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class ApiController extends AbstractController
{
    #[Route('/api/test', name: 'app_api')]
    public function index(): JsonResponse
    {
        return $this->json([
            'controller_name' => 'ApiController',
            'message' => 'Hello from Symfony API!',
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }
}
