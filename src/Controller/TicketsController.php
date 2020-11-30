<?php

namespace App\Controller;

use App\Entity\Severity;
use App\Entity\Ticket;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tickets")
 */
class TicketsController extends AbstractController
{
    /**
     * @Route("/", name="tickets")
     */
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy(['done' => false]);
        
        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'severityClass' => ['info', 'primary', 'success', 'warning', 'danger'],
            'title' => 'Pendent tickets'
        ]);
    }

    /**
     * @Route("/archived", name="tickets_archived")
     */
    public function archived(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy(['done' => true]);
        
        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'title' => 'Archived tickets'
        ]);
    }

    /**
     * @Route("/add", name="add_ticket")
     */
    public function create(): Response {
        $repository = $this->getDoctrine()->getRepository(Severity::class);
        $severities = $repository->findAll();

        return $this->render('tickets/add.html.twig', [
            'severities' => $severities
        ]);
    }

    /**
     * @Route("/save", name="save_ticket", methods={"POST"})
     */
    public function save(Request $request): RedirectResponse {

        $id = $request->get('id');
        $title = $request->get('title');
        $description = $request->get('description');
        $severityId = $request->get('severity');

        $repository = $this->getDoctrine()->getRepository(Ticket::class);

        if ($id) {
            $ticket = $repository->find($id);
        } else {
            $ticket = new Ticket();
            $ticket->setCreatedBy($this->getUser());
        }

        $ticket->setTitle($title);

        $severityRepository = $this->getDoctrine()->getRepository(Severity::class);
        $severity = $severityRepository->find($severityId);
        $ticket->setSeverity($severity);
        
        $ticket->setDescription($description);

        $em = $this->getDoctrine()->getManager();
        $em->persist($ticket);

        $em->flush();

        $this->addFlash('success', $id ? 'Ticket updated' : 'Ticket created');

        return $this->redirectToRoute('tickets');
    }

    /**
     * @Route("/{id}", name="ticket_detail")
     */
    public function detail(int $id): Response {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);

        if (!$ticket) {
            throw new NotFoundHttpException("Ticket not found!");
        }

        return $this->render('tickets/detail.html.twig', [
            'ticket' => $ticket
        ]);
    }

    /**
     * @Route("/{id}/update", name="ticket_update")
     */
    public function update(int $id): Response {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);

        $severityRepository = $this->getDoctrine()->getRepository(Severity::class);
        $severities = $severityRepository->findAll();

        if (!$ticket) {
            throw new NotFoundHttpException("Ticket not found!");
        }

        return $this->render('tickets/add.html.twig', [
            'ticket' => $ticket,
            'severities' => $severities
        ]);
    }

    /**
     * @Route("/{id}/done", name="ticket_mark_done")
     */
    public function markDone(int $id): RedirectResponse {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);

        if (!$ticket) {
            throw new NotFoundHttpException("Ticket not found!");
        }

        $ticket->setDone(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($ticket);

        $em->flush();

        $this->addFlash('info', 'Ticket archived');

        return $this->redirectToRoute('tickets');
    }
    
    /**
     * @Route("/{id}/remove", name="ticket_remove")
     */
    public function remove(int $id): RedirectResponse {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);

        if (!$ticket) {
            throw new NotFoundHttpException("Ticket not found!");
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($ticket);

        $em->flush();

        $this->addFlash('info', 'Ticket removed');

        return $this->redirectToRoute('tickets');
    }
}
