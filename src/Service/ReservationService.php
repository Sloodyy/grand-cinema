<?php

namespace App\Service;

use App\Entity\Reservation;
use App\Entity\Seat;
use App\Entity\Session;
use App\Entity\User;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use DateTimeImmutable;

class ReservationService
{
    public function __construct(
        private ReservationRepository $reservationRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Crée une réservation après vérification et verrouillage des places.
     *
     * @param int[] $seatIds
     */
    public function createReservation(
        User $user,
        Session $session,
        array $seatIds
    ): Reservation {
        if (empty($seatIds)) {
            throw new InvalidArgumentException(
                'Veuillez sélectionner au moins une place.'
            );
        }

        $uniqueSeatIds = array_values(array_unique($seatIds));

        if (count($uniqueSeatIds) !== count($seatIds)) {
            throw new InvalidArgumentException(
                'Une même place ne peut pas être sélectionnée plusieurs fois.'
            );
        }

        sort($uniqueSeatIds);

        return $this->entityManager->wrapInTransaction(
            function (EntityManagerInterface $entityManager) use (
                $user,
                $session,
                $uniqueSeatIds
            ): Reservation {
                $seats = [];

                foreach ($uniqueSeatIds as $seatId) {
                    $seat = $entityManager
                        ->getRepository(Seat::class)
                        ->find($seatId);

                    if (!$seat) {
                        throw new InvalidArgumentException(
                            'Une place sélectionnée est introuvable.'
                        );
                    }

                    if ($seat->getRoomNumber() !== $session->getRoomNumber()) {
                        throw new InvalidArgumentException(
                            'Une place ne correspond pas à la salle de cette séance.'
                        );
                    }

                    $entityManager->lock(
                        $seat,
                        LockMode::PESSIMISTIC_WRITE
                    );

                    $seats[] = $seat;
                }

                $reservedSeats = $this->reservationRepository
                    ->findReservedSeatsForSession($session);

                $reservedSeatIds = array_map(
                    fn ($seat) => $seat->getId(),
                    $reservedSeats
                );

                foreach ($seats as $seat) {
                    if (in_array($seat->getId(), $reservedSeatIds, true)) {
                        throw new InvalidArgumentException(
                            'Une des places sélectionnées est déjà réservée.'
                        );
                    }
                }

                $reservation = new Reservation();
                $reservation->setUser($user);
                $reservation->setSession($session);
                $reservation->setCreatedAt(new DateTimeImmutable());
                $reservation->setStatus('confirmed');

                foreach ($seats as $seat) {
                    $reservation->addSeat($seat);
                }

                $entityManager->persist($reservation);
                $entityManager->flush();

                return $reservation;
            }
        );
    }
}
