<?php

namespace App\Repository;

use App\Entity\Film;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Film>
 */
class FilmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Film::class);
    }

    /**
     * Récupère les films avec leurs séances à venir.
     *
     * @return Film[]
     */
    public function findAllWithUpcomingSessions(): array
    {
        return $this->createQueryBuilder('f')
            ->leftJoin(
                'f.sessions',
                's',
                'WITH',
                's.startsAt >= :now'
            )
            ->addSelect('s')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('f.title', 'ASC')
            ->addOrderBy('s.startsAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
