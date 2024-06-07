<?php

namespace Psys\SimpleOrderInvoice\Repository;

use Psys\SimpleOrderInvoice\Entity\FakturaDodavatel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FakturaDodavatel>
 *
 * @method FakturaDodavatel|null find($id, $lockMode = null, $lockVersion = null)
 * @method FakturaDodavatel|null findOneBy(array $criteria, array $orderBy = null)
 * @method FakturaDodavatel[]    findAll()
 * @method FakturaDodavatel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FakturaDodavatelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FakturaDodavatel::class);
    }

    public function save(FakturaDodavatel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FakturaDodavatel $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FakturaDodavatel[] Returns an array of FakturaDodavatel objects
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

//    public function findOneBySomeField($value): ?FakturaDodavatel
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
