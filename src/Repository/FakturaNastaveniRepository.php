<?php

namespace Psys\SimpleOrderInvoice\Repository;

use Psys\SimpleOrderInvoice\Entity\FakturaNastaveni;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<FakturaNastaveni>
 *
 * @method FakturaNastaveni|null find($id, $lockMode = null, $lockVersion = null)
 * @method FakturaNastaveni|null findOneBy(array $criteria, array $orderBy = null)
 * @method FakturaNastaveni[]    findAll()
 * @method FakturaNastaveni[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FakturaNastaveniRepository extends ServiceEntityRepository
{
    use NastaveniTrait;
    
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FakturaNastaveni::class);
    }

    public function save(FakturaNastaveni $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(FakturaNastaveni $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return FakturaNastaveni[] Returns an array of FakturaNastaveni objects
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

//    public function findOneBySomeField($value): ?FakturaNastaveni
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
