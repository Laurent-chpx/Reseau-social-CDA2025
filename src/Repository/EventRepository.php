<?php

namespace App\Repository;

use App\Entity\Event;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    //Last event create
    public function findLatest(int $limit = 3): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.dateStart >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //Last promote event
    public function findLatestPromoted(int $limit = 3): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.promote', 'p')
            ->andWhere('e.dateStart >= :now')
            ->andWhere('p.dateStart <= :now')
            ->andWhere('p.dateEnd >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('p.dateStart', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //list event with filter
    public function findWithFilters(
        ?int $cityId = null,
        ?int $departmentId = null,
        ?int $categoryId = null,
        ?string $dateFrom = null,
        ?string $dateTo = null,
        ?bool $freeOnly = false,
        ?string $search = null
    ): array {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.city', 'c')
            ->leftJoin('c.department', 'd')
            ->leftJoin('e.categories', 'cat')
            ->leftJoin('e.promote', 'p')
            ->addSelect('CASE WHEN p.id IS NOT NULL AND p.dateStart <= :now AND p.dateEnd >= :now THEN 1 ELSE 0 END AS HIDDEN isPromoted')
            ->andWhere('e.dateStart >= :now')
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('isPromoted', 'DESC')
            ->addOrderBy('e.dateStart', 'ASC');

        if ($cityId) {
            $qb->andWhere('c.id = :cityId')
                ->setParameter('cityId', $cityId);
        }

        if ($departmentId) {
            $qb->andWhere('d.id = :departmentId')
                ->setParameter('departmentId', $departmentId);
        }

        if ($categoryId) {
            $qb->andWhere('cat.id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        if ($dateFrom) {
            $qb->andWhere('e.dateStart >= :dateFrom')
                ->setParameter('dateFrom', new \DateTimeImmutable($dateFrom));
        }

        if ($dateTo) {
            $qb->andWhere('e.dateStart <= :dateTo')
                ->setParameter('dateTo', new \DateTimeImmutable($dateTo . ' 23:59:59'));
        }

        if ($freeOnly) {
            $qb->andWhere('e.price IS NULL OR e.price = 0');
        }

        if ($search) {
            $qb->andWhere('e.title LIKE :search OR e.description LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $qb->getQuery()->getResult();
    }


    public function findByFollowedCities(User $user, int $limit = 10): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.city', 'c')
            ->innerJoin('App\Entity\UserCity', 'uc', 'WITH', 'uc.city = c')
            ->andWhere('uc.user = :user')
            ->andWhere('e.dateStart >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.dateStart', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByFollowedUsers(User $user, int $limit = 10): array
    {
        return $this->createQueryBuilder('e')
            ->innerJoin('e.createdBy', 'u')
            ->innerJoin('App\Entity\UserFollow', 'uf', 'WITH', 'uf.followed = u')
            ->andWhere('uf.follower = :user')
            ->andWhere('e.dateStart >= :now')
            ->setParameter('user', $user)
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('e.dateStart', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function findByDepartmentWithPromotedFirst(?int $departmentId, int $offset = 0, int $limit = 3): array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.city', 'c')
            ->leftJoin('c.department', 'd')
            ->leftJoin('e.promote', 'p')
            ->andWhere('e.dateStart >= :today')
            ->addSelect('CASE WHEN p.id IS NOT NULL AND p.dateStart <= :now AND p.dateEnd >= :now THEN 1 ELSE 0 END AS HIDDEN isPromoted')
            ->setParameter('today', new \DateTimeImmutable('today'))
            ->setParameter('now', new \DateTimeImmutable())
            ->orderBy('isPromoted', 'DESC')
            ->addOrderBy('e.dateStart', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        if ($departmentId) {
            $qb->andWhere('d.id = :departmentId')
                ->setParameter('departmentId', $departmentId);
        }

        return $qb->getQuery()->getResult();
    }

    public function countByDepartment(?int $departmentId): int
    {
        $qb = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->leftJoin('e.city', 'c')
            ->leftJoin('c.department', 'd');

        if ($departmentId) {
            $qb->andWhere('d.id = :departmentId')
                ->setParameter('departmentId', $departmentId);
        }

        return (int) $qb->getQuery()->getSingleScalarResult();
    }
}
