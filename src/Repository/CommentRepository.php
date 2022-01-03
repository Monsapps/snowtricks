<?php
/**
 * Comment repository
 */
namespace App\Repository;

use App\Entity\Comment;
use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Comment::class);
    }

    public function getComments(
        Trick $trick,
        int $limit,
        int $offset)
    {
        $entityManager = $this->getEntityManager();

        /*$query = $entityManager->createQuery("
            SELECT c, u.name, a.pathAvatar
            FROM App\Entity\Comment c
            INNER JOIN App\Entity\User u 
            INNER JOIN App\Entity\Avatar a
            WHERE c.trick = :trick
            ORDER BY c.dateComment DESC
        ")
        ->setMaxResults($limit)
        ->setFirstResult($offset)
        ->setParameter("trick", $trick);
        
        return $query->getResult();*/

        $query = $entityManager->createQueryBuilder()
            ->select("c")
            ->from("App\Entity\Comment", "c")
            ->join("App\Entity\User", "u")
            ->where("c.trick = :trick")
            ->orderBy("c.dateComment", "DESC")
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter("trick", $trick);

        return $query->getQuery()->getResult();
    }

    public function countComments(Trick $trick)
    {
        $entityManager = $this->getEntityManager();
        // return comment count
        $query = $entityManager->createQuery("
        SELECT count(c)
        FROM App\Entity\Comment c
        WHERE c.trick = :trick
        ")
        ->setParameter("trick", $trick);
        
        return $query->getSingleScalarResult();
    }

}
