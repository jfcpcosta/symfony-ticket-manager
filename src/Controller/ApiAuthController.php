<?php

namespace App\Controller;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @Route("/api/auth")
 */
class ApiAuthController extends ApiController
{

    /**
     * @Route("/login")
     */
    public function login(UserInterface $user, JWTTokenManagerInterface $jwtManager): JsonResponse {
        return new JsonResponse(['token' => $jwtManager->create($user)]);
    }
}
