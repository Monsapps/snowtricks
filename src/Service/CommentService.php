<?php
/**
 * This class contain comment functions
 */
namespace App\Service;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;

class CommentService
{
    public function newComment(): Comment
    {
        return new Comment();
    }

    /**
     * Add user, trick & date to comment
     */
    public function addInfo(Comment $comment, Trick $trick, User $user)
    {
        $comment->setTrick($trick);

        // Add current timestamp
        $dateTime = new \DateTime();
        $comment->setDateComment($dateTime->setTimestamp(time()));

        $comment->setUser($user);
    }
}
