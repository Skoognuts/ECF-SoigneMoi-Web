<?php

namespace App\Repository;

use App\Entity\User;
use App\Entity\Stay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Stay>
 */
class StayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Stay::class);
    }

    public function save(Stay $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Stay $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Stay[] Returns an array of Stay objects
     */
    public function findAllByUser(User $user): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :val')
            ->setParameter('val', $user)
            ->orderBy('s.date_from', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Stay[] Returns an array of Stay objects
     */
    public function findAllByDoctor(User $doctor): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.doctor = :val')
            ->setParameter('val', $doctor)
            ->orderBy('s.date_from', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }
}
