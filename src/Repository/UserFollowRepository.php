<?php

namespace App\Repository;

use App\Entity\UserFollow;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserFollow>
 */
class UserFollowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserFollow::class);
    }

    public function findFollow(User $follower, User $followed): ?UserFollow
    {
        return $this->findOneBy([
            'follower' => $follower,
            'followed' => $followed,
        ]);
    }

    public function findFollowedUsers(User $user): array
    {
        return $this->createQueryBuilder('uf')
            ->innerJoin('uf.followed', 'u')
            ->andWhere('uf.follower = :user')
            ->setParameter('user', $user)
            ->orderBy('uf.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findFollowers(User $user): array
    {
        return $this->createQueryBuilder('uf')
            ->innerJoin('uf.follower', 'u')
            ->andWhere('uf.followed = :user')
            ->setParameter('user', $user)
            ->orderBy('uf.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return UserFollow[] Returns an array of UserFollow objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserFollow
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
