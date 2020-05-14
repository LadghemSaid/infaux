<?php

namespace App\Repository;

use App\Entity\NotificationMessagerie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NotificationMessagerie|null find($id, $lockMode = null, $lockVersion = null)
 * @method NotificationMessagerie|null findOneBy(array $criteria, array $orderBy = null)
 * @method NotificationMessagerie[]    findAll()
 * @method NotificationMessagerie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NotificationMessagerieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NotificationMessagerie::class);
    }

    // /**
    //  * @return Notification[] Returns an array of Notification objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('n.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Notification
    {
        return $this->createQueryBuilder('n')
            ->andWhere('n.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
