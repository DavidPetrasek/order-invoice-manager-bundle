<?php

namespace Psys\SimpleOrderInvoice\Repository;

use Psys\SimpleOrderInvoice\Entity\FakturaOdberatel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FakturaOdberatel>
 *
 * @method FakturaOdberatel|null find($id, $lockMode = null, $lockVersion = null)
 * @method FakturaOdberatel|null findOneBy(array $criteria, array $orderBy = null)
 * @method FakturaOdberatel[]    findAll()
 * @method FakturaOdberatel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FakturaOdberatelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FakturaOdberatel::class);
    }

    public function save(FakturaOdberatel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FakturaOdberatel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FakturaOdberatel[] Returns an array of FakturaOdberatel objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?FakturaOdberatel
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
