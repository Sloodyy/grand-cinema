<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Session;
use App\Entity\User;
use App\Form\BookingType;
use App\Repository\ReservationRepository;
use App\Repository\SeatRepository;
use App\Service\ReservationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BookingController extends AbstractController
{
    #[Route('/booking/{id}', name: 'app_booking', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function index(
        Request $request,
        Session $session,
        ReservationRepository $reservationRepository,
        SeatRepository $seatRepository,
        ReservationService $reservationService
    ): Response {
        $reservedSeats = $reservationRepository
            ->findReservedSeatsForSession($session);

        $reservedSeatIds = array_map(
            fn ($seat) => $seat->getId(),
            $reservedSeats
        );

        $seats = $seatRepository->findBy(
            ['roomNumber' => $session->getRoomNumber()],
            ['rowLabel' => 'ASC', 'seatNumber' => 'ASC']
        );

        $availableSeats = array_filter(
            $seats,
            fn ($seat) => !in_array($seat->getId(), $reservedSeatIds, true)
        );

        $form = $this->createForm(BookingType::class, new Reservation(), [
            'available_seats' => $availableSeats,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            if (!$user instanceof User) {
                return $this->redirectToRoute('app_login');
            }

            $selectedSeats = $form->get('seats')->getData();

            $seatIds = array_map(
                fn ($seat) => $seat->getId(),
                $selectedSeats->toArray()
            );

            try {
                $reservationService->createReservation(
                    $user,
                    $session,
                    $seatIds
                );

                $this->addFlash('success', 'Votre réservation a été effectuée avec succès.');

                return $this->redirectToRoute('app_reservation');
            } catch (\InvalidArgumentException $exception) {
                $this->addFlash('danger', $exception->getMessage());
            }
        }

        return $this->render('booking/index.html.twig', [
            'session' => $session,
            'seats' => $seats,
            'reservedSeatIds' => $reservedSeatIds,
            'form' => $form->createView(),
        ]);
    }
}
