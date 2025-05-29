<?php

namespace App\Controller;

use App\Service\HelloWorldService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class HelloWorldController
{
    #[Route('/hello-world')]
    public function getHelloWorld(HelloWorldService $service) : Response
    {
        return new Response($service->helloWorld());
    }
}