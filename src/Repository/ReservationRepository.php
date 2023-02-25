<?php

namespace App\Repository;

use App\Entity\Reservation;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getTotalReservations(): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getTotalReservationsByClientId(?int $id = null): int
    {
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.client = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getTotalReservationsSales(): int
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.price)')
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getTotalReservationsSalesByClientId(?int $id): int
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.price)')
            ->where('r.client = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getReservationsInProgress(): int
    {
        $now = new DateTime('now');
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where(':now BETWEEN r.startAt AND r.endAt')
            ->setParameter('now', $now)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getReservationsInProgressByClientId(?int $id = null): int
    {
        $now = new DateTime('now');
        return $this->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where(':now BETWEEN r.startAt AND r.endAt')
            ->andWhere('r.client = :id')
            ->setParameter('now', $now)
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    public function getTotalSalesByYearAndMonth($year, $month, $apartment = null)
    {
        return $this->createQueryBuilder('r')
            ->select('SUM(r.price)')
            ->where('YEAR(r.startAt) = :year')
            ->andWhere('MONTH(r.startAt) = :month')
            ->andWhere('r.apartment = :apartment')
            ->setParameter('year', $year)
            ->setParameter('month', $month)
            ->setParameter('apartment', $apartment)
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
