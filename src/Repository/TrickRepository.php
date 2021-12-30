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

    public function countTricks()
    {
        $queryBuilder = $this->createQueryBuilder('trick');
    }

}
