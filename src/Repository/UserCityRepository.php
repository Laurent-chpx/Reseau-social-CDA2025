<?php

namespace App\Repository;

use App\Entity\UserCity;
use App\Entity\User;
use App\Entity\City;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserCity>
 */
class UserCityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserCity::class);
    }

    public function findFollow(User $user, City $city): ?UserCity
    {
        return $this->findOneBy([
            'user' => $user,
            'city' => $city,
        ]);
    }

    public function findFollowedCities(User $user): array
    {
        return $this->createQueryBuilder('uc')
            ->innerJoin('uc.city', 'c')
            ->andWhere('uc.user = :user')
            ->setParameter('user', $user)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
