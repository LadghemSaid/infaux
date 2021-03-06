<?php

namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Like;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */

    public function findByPostField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.post = :val')
            ->setParameter('val', $value)
            ->orderBy('c.created_at', 'DESC')
            ->getQuery()
            ->getResult();
    }


    public function findPostsComment($id, $order)
    {
        return $this->createQueryBuilder('posts')
            ->andWhere('posts.posts = :val')
            ->andWhere('posts.approved = 1')
            ->setParameter('val', $id)
            ->orderBy('posts.created_at', $order)
            ->getQuery()
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */


}
