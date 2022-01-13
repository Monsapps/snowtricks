<?php
/**
 * Trick repository
 */
namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * This is a class to fetch tricks data with Doctrine
 */
class TrickRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }

    public function getTricks(
        int $limit,
        int $offset): array
    {

        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQueryBuilder()
        ->select("t")
        ->from("App\Entity\Trick", "t")
        ->orderBy("t.idTrick", "DESC")
        ->setMaxResults($limit)
        ->setFirstResult($offset);

        return $query->getQuery()->getResult();
    }

    public function countTricks(): int
    {
        $entityManager = $this->getEntityManager();
        // return count count
        $query = $entityManager->createQuery("
        SELECT count(t)
        FROM App\Entity\Trick t
        ");
        
        return $query->getSingleScalarResult();
    }

}
