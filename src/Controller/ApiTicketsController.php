<?php

namespace App\Controller;

use App\Entity\Severity;
use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/tickets")
 */
class ApiTicketsController extends ApiController
{
    
    /**
     * @Route("", methods={"GET"})
     */
    public function index(): Response
    {
        $tickets = $this->getDoctrine()->getRepository(Ticket::class)->findAll();
        return $this->respondWithSuccess($tickets);
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
    public function show(Ticket $ticket): Response
    {
        return $this->respondWithSuccess($ticket);
        // return new Response($this->serialize($ticket), 200, ['Content-Type' => 'application/json']);
    }
    
    /**
     * @Route("", methods={"POST"})
     */
    public function create(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        $request = $this->transformJsonBody($request);

        $title = $request->get('title');
        $description = $request->get('description');
        $severityId = $request->get('severity');

        $severity = $this->getDoctrine()->getRepository(Severity::class)->find($severityId);

        $ticket = new Ticket($title);
        $ticket->setDescription($description);
        $ticket->setSeverity($severity);
        $ticket->setCreatedBy($user);

        $em->persist($ticket);
        $em->flush();

        return $this->respondWithCreated($ticket);
    }

    /**
     * @Route("/{id}", methods={"PUT"})
     */
    public function update(Ticket $ticket, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $request = $this->transformJsonBody($request);

        $title = $request->get('title');
        $description = $request->get('description');
        $severityId = $request->get('severity');

        $severity = $this->getDoctrine()->getRepository(Severity::class)->find($severityId);

        $ticket->setTitle($title);
        $ticket->setDescription($description);
        $ticket->setSeverity($severity);
        $ticket->setCreatedBy($user);

        $em->persist($ticket);
        $em->flush();

        return $this->response($ticket);
    }
    
    /**
     * @Route("/{id}", methods={"DELETE"})
     */
    public function remove(Ticket $ticket): Response
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($ticket);;
        $em->flush();

        return $this->setStatusCode(204)->response([]);
    }
}
