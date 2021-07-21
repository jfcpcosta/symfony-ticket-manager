<?php

namespace App\Controller;

use App\Repository\SeverityRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(TicketRepository $ticketRepository, SeverityRepository $severityRepository): Response
    {
        $activeTickets = $ticketRepository->findBy(['done' => false]);
        $archivedTickets = $ticketRepository->findBy(['done' => true]);
        $severities = $severityRepository->findAll();

        return $this->render('dashboard/index.html.twig', [
            'activeTickets' => $activeTickets,
            'archivedTickets' => $archivedTickets,
            'severities' => $severities
        ]);
    }
}
