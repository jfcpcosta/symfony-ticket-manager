<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

abstract class ApiController extends AbstractController
{
    protected $statusCode = 200;

    public function getStatusCode(): int {
        return $this->statusCode;
    }

    protected function setStatusCode(int $statusCode): self {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function response($data, $headers = ['Content-Type' => 'application/json']): Response {
        return new Response($this->serialize($data), $this->statusCode, $headers);
    }

    public function respondWithErrors($errors, $headers = ['Content-Type' => 'application/json']): Response {
        $data = [
            'status' => $this->getStatusCode(),
            'errors' => $errors
        ];

        return new Response($this->serialize($data), $this->getStatusCode(), $headers);
    }

    public function respondWithSuccess($success, $headers = ['Content-Type' => 'application/json']): Response {
        $data = [
            'status' => $this->getStatusCode(),
            'success' => $success
        ];

        return new Response($this->serialize($data), $this->getStatusCode(), $headers);
    }

    public function respondWithUnauthorized(string $message = 'Not authorized!'): Response {
        return $this
            ->setStatusCode(401)
            ->respondWithErrors($message);
    }
    
    public function respondWithValidationError(string $message = 'Validation errors!'): Response {
        return $this
            ->setStatusCode(422)
            ->respondWithErrors($message);
    }
    
    public function respondWithNotFound(string $message = 'Not found!'): Response {
        return $this
            ->setStatusCode(404)
            ->respondWithErrors($message);
    }
    
    public function respondWithCreated($data = []): Response {
        return $this
            ->setStatusCode(201)
            ->response($data);
    }

    protected function transformJsonBody(Request $request) {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return $request;
        }

        $request->request->replace($data);

        return $request;
    }

    protected function serialize($data): string {
        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        return $serializer->serialize($data, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);
    }
}
