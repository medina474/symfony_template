<?php

namespace App\Repository;

use App\Entity\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Country>
 */
class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    /**
     * @param array{country: string, code: string}|null $cursor
     * @return list<Country>
     */
    public function findPaginatedBySearch(
        int $maxResult,
        ?string $search,
        ?array $cursor
    ): array
    {
        $query = $this->createQueryBuilder('c');

        if (!empty($search)) {
            $query
                ->addSelect('similarity(c._country, norm(:search)) AS HIDDEN score')
                ->andWhere('
                    c._country like norm(:like)
                    or similarity(c._country, norm(:search)) > 0.1
                ')
                ->setParameter('search', $search)
                ->setParameter('like', '%' . $search . '%')
                ->orderBy('score', 'DESC');
        }

        $query
            ->orderBy('c.country', 'ASC')
            ->addOrderBy('c.code', 'ASC')
            ->setMaxResults($maxResult);

        if ($cursor !== null) {
            $query
                ->andWhere(
                    $query->expr()->orX(
                        'c.country > :country',
                        $query->expr()->andX(
                            'c.country = :country',
                            'c.code > :code'
                        )
                    )
                )
                ->setParameter('country', $cursor['country'])
                ->setParameter('code', $cursor['code']);
        }

        return $query->getQuery()->getResult();
    }

    //    /**
    //     * @return Country[] Returns an array of Country objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Country
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
