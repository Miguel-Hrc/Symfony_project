<?php

namespace App\Repository;

use App\Entity\Sweatshirt;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sweatshirt>
 *
 * @method Sweatshirt|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sweatshirt|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sweatshirt[]    findAll()
 * @method Sweatshirt[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SweatshirtRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sweatshirt::class);
    }

    public function add(Sweatshirt $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sweatshirt $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
    * return all product per page 
    * @return void
    */
    public function getPaginatedSweatshirt($page){
        $query = $this->createQueryBuilder('p')
        ->setFirstResult(($page * $_ENV['LIMIT_PAGINATION_5']) - $_ENV['LIMIT_PAGINATION_5'])
        ->setMaxResults($_ENV['LIMIT_PAGINATION_5'])
        ;
        return $query->getQuery()->getResult();
    }

    /**
    * return number of Products
    * @return void
    */
    public function getTotalSweatshirts()
    {
        $query = $this->createQueryBuilder('p')
        ->select('COUNT(p)')
        ;
        //seulement chiffre décimaux texte, pas tableau getSingleScalarResult
        return $query->getQuery()->getSingleScalarResult();
    }

    public function findByPriceRange(float $min, float $max): array
{
    return $this->createQueryBuilder('s')
        ->andWhere('s.price >= :min')
        ->andWhere('s.price <= :max')
        ->setParameter('min', $min)
        ->setParameter('max', $max)
        ->getQuery()
        ->getResult();
}

//    /**
//     * @return Sweatshirts[] Returns an array of Sweatshirts objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sweatshirts
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
