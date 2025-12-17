<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\PromoteRequest;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PromoteRequest>
 */
class PromoteRequestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PromoteRequest::class);
    }

    public function findRequestsByUser(User $user) : array
    {
        return $this->createQueryBuilder('pr')
            ->innerJoin('pr.event', 'e')
            ->andWhere('e.createdBy = :user')
            ->setParameter('user', $user)
            ->orderBy('pr.createdBy', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findPendingByEvent(Event $event) : ?PromoteRequest
    {
        return $this->findOneBy(['event'=> $event, 'status' => PromoteRequest::STATUS_PENDING]);
    }

    public function countPending(): int
    {
        return $this->createQueryBuilder('pr')
            ->select('COUNT(pr.id)')
            ->andWhere('pr.status = :status')
            ->setParameter('status', PromoteRequest::STATUS_PENDING)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findAllPending(): array
    {
        return $this->createQueryBuilder('pr')
            ->andWhere('pr.status = :status')
            ->setParameter('status', PromoteRequest::STATUS_PENDING)
            ->orderBy('pr.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return PromoteRequest[] Returns an array of PromoteRequest objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?PromoteRequest
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
