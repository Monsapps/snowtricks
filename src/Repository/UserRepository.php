<?php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User
     */
    public function findEmailToken(string $token): User
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery("
            SELECT u
            FROM App\Entity\User u
            WHERE u.registrationToken = :token
        ")->setParameter("token", $token);

        return $query->getResult();
    }
}