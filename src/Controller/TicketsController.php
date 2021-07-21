<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\SeverityRepository;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tickets')]
class TicketsController extends AbstractController
{
    #[Route('', name: 'tickets')]
    public function tickets(Request $request, TicketRepository $repository): Response
    {
        if ($request->query->has('search')) {
            $searchTerm = $request->query->get('search');
            $title = 'Searching for: ' . $searchTerm;
            $tickets = $repository->findBySearchTerm($searchTerm);
        } else {
            $title = 'Active Tickets';
            $tickets = $repository->findBy(['done' => false]);
        }


        return $this->render('tickets/tickets.html.twig', [
            'tickets' => $tickets,
            'title' => $title
        ]);
    }
    
    #[Route('/archived', name: 'archived_tickets')]
    public function archived(TicketRepository $repository): Response
    {
        $tickets = $repository->findBy(['done' => true]);

        return $this->render('tickets/tickets.html.twig', [
            'tickets' => $tickets,
            'title' => 'Archived Tickets'
        ]);
    }
    
    #[Route('/add', name: 'add_ticket')]
    public function add(SeverityRepository $repository, UserRepository $userRepository): Response
    {
        return $this->render('tickets/form.html.twig', [
            'severities' => $repository->findAll(),
            'users' => $userRepository->findAll()
        ]);
    }
    
    #[Route('/save', name: 'save_ticket', methods: ['POST'])]
    public function save(Request $request, TicketRepository $ticketRepository, SeverityRepository $severityRepository, UserRepository $userRepository, EntityManagerInterface $em): RedirectResponse
    {
        $id = $request->get('id');
        $title = $request->get('title');
        $description = $request->get('description');
        $severityId = $request->get('severity');
        $assignedToId = $request->get('assignedTo');

        if ($id) {
            $ticket = $ticketRepository->find($id);
            if (!$ticket) {
                throw new NotFoundHttpException('Ticket not found!');
            }
        } else {
            $ticket = new Ticket();
            $ticket->setCreatedBy($this->getUser());
        }

        $severity = $severityRepository->find($severityId);
        $assignedTo = $userRepository->find($assignedToId);

        $ticket->setTitle($title);
        $ticket->setDescription($description);
        $ticket->setSeverity($severity);
        $ticket->setAssignedTo($assignedTo);

        $em->persist($ticket);
        $em->flush();

        $this->addFlash('success', $id ? 'Ticket updated' : 'Ticket created');

        return $this->redirectToRoute('ticket_detail', ['id' => $ticket->getId()]);
    }

    #[Route('/{id}', name: 'ticket_detail')]
    public function detail(Ticket $ticket): Response
    {
        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found!');
        }

        return $this->render('tickets/detail.html.twig', [
            'ticket' => $ticket
        ]);
    }

    #[Route('/{id}/update', name: 'ticket_update')]
    public function update(Ticket $ticket, SeverityRepository $repository): Response
    {
        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found!');
        }

        return $this->render('tickets/form.html.twig', [
            'ticket' => $ticket,
            'severities' => $repository->findAll()
        ]);
    }

    #[Route('/{id}/done', name: 'ticket_mark_resolved')]
    public function done(Ticket $ticket, EntityManagerInterface $em): RedirectResponse
    {
        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found!');
        }

        $ticket->setDone(true);
        $em->persist($ticket);
        $em->flush();

        $this->addFlash('info', 'Ticket archived');

        return $this->redirectToRoute('ticket_detail', ['id' => $ticket->getId()]);
    }
    
    #[Route('/{id}/remove', name: 'ticket_remove')]
    public function remove(Ticket $ticket, EntityManagerInterface $em): RedirectResponse
    {
        if (!$ticket) {
            throw new NotFoundHttpException('Ticket not found!');
        }

        $em->remove($ticket);
        $em->flush();

        $this->addFlash('info', 'Ticket removed');

        return $this->redirectToRoute('tickets');
    }
}
