<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Session;
use App\Entity\Seat;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    
    /**
     * Récupère les places déjà associées à une réservation pour une séance.
     *
     * @return Seat[]
     */
    public function findReservedSeatsForSession(Session $session): array
    {
        return $this->getEntityManager()
            ->createQueryBuilder()
            ->select('DISTINCT seat')
            ->from(Seat::class, 'seat')
            ->innerJoin('seat.reservations', 'reservation')
            ->andWhere('reservation.session = :session')
            ->andWhere('reservation.status != :cancelled')
            ->setParameter('session', $session)
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getResult();
    }

}
