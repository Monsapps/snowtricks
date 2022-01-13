<?php
/**
 * This class contain comment functions
 */
namespace App\Service;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class CommentService
{
    private $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    public function newComment(): Comment
    {
        return new Comment();
    }

    /**
     * Add comment with user, trick & date
     */
    public function addComment(Comment $comment, Trick $trick, User $user)
    {

        $entityManager = $this->managerRegistry->getManager();

        $comment->setTrick($trick);

        // Add current timestamp
        $dateTime = new \DateTime();
        $comment->setDateComment($dateTime->setTimestamp(time()));

        $comment->setUser($user);

        $entityManager->persist($comment);
        $entityManager->flush();
    }
}
