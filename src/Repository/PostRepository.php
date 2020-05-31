<?php

namespace App\Repository;

use App\Entity\Post;
use Doctrine\ORM\Query\Expr;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    // /**
    //  * @return Post[] Returns an array of Post objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findAllDesc()
    {


        return $this->createQueryBuilder('p')
            ->andWhere('p.published = 1')
            ->orderBy('p.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findAllByLikes()
    {

        // SELECT *,COUNT(p.id) compteur FROM `post` p INNER JOIN liketable l ON p.id = l.post_id GROUP BY l.post_id
        $qb = $this->createQueryBuilder('p');
        $qb->select('p')
            ->innerJoin(' App\Entity\Like', 'l', Join::WITH, $qb->expr()->eq('p.id', 'l.post'))
            ->where('p.published = 1')
            ->groupBy('l.post')
            ->orderBy('COUNT(p.id)', 'DESC');

        return $qb->getQuery()->getResult();;

    }

    public function findPostsByString($str)
    {

        return $this->createQueryBuilder('p')
           // ->where("SOUNDEX(p.text) = SOUNDEX(:val)")
            ->where("p.text Like  :val")
            //->where('p.text like :val')
            ->setParameter('val',  '%'.$str.'%')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult();

    }


}
