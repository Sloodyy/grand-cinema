<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReservationController extends AbstractController
{
    #[Route('/mes-reservations', name: 'app_reservation')]
    public function index(
        ReservationRepository $reservationRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $reservations = $reservationRepository->findBy(
            ['user' => $user],
            ['createdAt' => 'DESC']
        );

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    #[Route('/mes-reservations/{id}/billet', name: 'app_reservation_ticket', requirements: ['id' => '\d+'])]
    public function ticket(
        Reservation $reservation
    ): Response {
        if ($reservation->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('reservation/ticket.html.twig', [
            'reservation' => $reservation,
        ]);
    }

}
