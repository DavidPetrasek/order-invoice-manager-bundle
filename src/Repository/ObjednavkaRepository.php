<?php

namespace Psys\SimpleOrderInvoice\Repository;

use Psys\SimpleOrderInvoice\Entity\Objednavka;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Objednavka>
 *
 * @method Objednavka|null find($id, $lockMode = null, $lockVersion = null)
 * @method Objednavka|null findOneBy(array $criteria, array $orderBy = null)
 * @method Objednavka[]    findAll()
 * @method Objednavka[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ObjednavkaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objednavka::class);
    }

    public function save(Objednavka $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Objednavka $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Objednavka[] Returns an array of Objednavka objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('o.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Objednavka
//    {
//        return $this->createQueryBuilder('o')
//            ->andWhere('o.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
