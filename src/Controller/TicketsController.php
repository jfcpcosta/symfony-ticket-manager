<?php

namespace App\Controller;

use App\Entity\Severity;
use App\Entity\Ticket;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tickets')]
class TicketsController extends AbstractController
{
    #[Route('/', name: 'tickets')]
    public function index(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy(['done' => false]);

        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'title' => 'Pendent Tickets'
        ]);
    }
    
    #[Route('/archived', name: 'archived_tickets')]
    public function archived(): Response
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $tickets = $repository->findBy(['done' => true]);

        return $this->render('tickets/index.html.twig', [
            'tickets' => $tickets,
            'title' => 'Archived Tickets'
        ]);
    }

    #[Route('/add', name: 'add_ticket')]
    public function create(): Response {
        $repository = $this->getDoctrine()->getRepository(Severity::class);

        return $this->render('tickets/form.html.twig', [
            'severities' => $repository->findAll()
        ]);
    }
    
    #[Route('/save', name: 'save_ticket', methods: ['POST'])]
    public function save(Request $request): RedirectResponse {

        $id = $request->get('id');
        $title = $request->get('title');
        $description = $request->get('description');
        $severityId = $request->get('severity');

        $repository = $this->getDoctrine()->getRepository(Ticket::class);

        if ($id) {
            $ticket = $repository->find($id);
            if (!$ticket) {
                throw new NotFoundHttpException('Ticket not found!');
            }
        } else {
            $ticket = new Ticket();
            $ticket->setCreatedBy($this->getUser());
        }

        $severityRepository = $this->getDoctrine()->getRepository(Severity::class);
        $severity = $severityRepository->find($severityId);

        $ticket->setTitle($title);
        $ticket->setDescription($description);
        $ticket->setSeverity($severity);

        $em = $this->getDoctrine()->getManager();
        $em->persist($ticket);
        $em->flush();

        $this->addFlash('success', $id ? 'Ticket updated' : 'Ticket created');

        return $this->redirectToRoute('tickets');
    }

    #[Route('/{id}', name: 'ticket_detail')]
    public function detail(int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found!');
        }

        return $this->render('tickets/detail.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{id}/update', name: 'ticket_update')]
    public function update(int $id): Response
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found!');
        }

        $repository = $this->getDoctrine()->getRepository(Severity::class);

        return $this->render('tickets/form.html.twig', [
            'ticket' => $ticket,
            'severities' => $repository->findAll()
        ]);
    }
    
    #[Route('/{id}/done', name: 'ticket_mark_done')]
    public function markDone(int $id): RedirectResponse
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found!');
        }

        $ticket->setDone(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($ticket);
        $em->flush();

        $this->addFlash('info', 'Ticket archived');

        return $this->redirectToRoute('ticket_detail', ['id' => $id]);
    }
    
    #[Route('/{id}/remove', name: 'ticket_remove')]
    public function remove(int $id): RedirectResponse
    {
        $repository = $this->getDoctrine()->getRepository(Ticket::class);
        $ticket = $repository->find($id);

        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found!');
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($ticket);
        $em->flush();

        $this->addFlash('info', 'Ticket removed');

        return $this->redirectToRoute('tickets');
    }
}