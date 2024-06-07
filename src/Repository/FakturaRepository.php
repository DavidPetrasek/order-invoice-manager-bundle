<?php

namespace Psys\SimpleOrderInvoice\Repository;

use Psys\SimpleOrderInvoice\Entity\Faktura;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Faktura>
 *
 * @method Faktura|null find($id, $lockMode = null, $lockVersion = null)
 * @method Faktura|null findOneBy(array $criteria, array $orderBy = null)
 * @method Faktura[]    findAll()
 * @method Faktura[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FakturaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Faktura::class);
    }

    public function save(Faktura $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Faktura $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Faktura[] Returns an array of Faktura objects
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

//    public function findOneBySomeField($value): ?Faktura
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
