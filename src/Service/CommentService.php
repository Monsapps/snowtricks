<?php
/**
 * This class contain comment functions
 */
namespace App\Service;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use App\Repository\CommentRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

class CommentService
{
    private $managerRegistry;
    private $commentRepo;

    public function __construct(
        ManagerRegistry $managerRegistry,
        CommentRepository $commentRepo)
    {
        $this->managerRegistry = $managerRegistry;
        $this->commentRepo = $commentRepo;
    }

    public function newComment(): Comment
    {
        return new Comment();
    }

    /**
     * Get comment for a trick
     * @param Trick $trick
     * @return array|Comment
     */
    public function getCommentForJson(Trick $trick, int $limit, int $page): array
    {
        $offset = ($page > 0) ? (($page - 1) * $limit) : 0 ;

        $commentsArray = [];

        /**
         * We need to generate url for avatar because
         * JS values not properly rendered with Twig function
         */
        $package = new Package(new EmptyVersionStrategy());
        $avatarBaseUrl = $package->getUrl("/images/avatars/");

        $comments = $this->commentRepo->getComments($trick, $limit, $offset);

        foreach($comments as $comment) {
            $user = $comment->getUser();
            $avatar = $user->getAvatar();
            $avatarPath = $avatarBaseUrl . "default.png";
            if($avatar !== null) {
                $avatarPath = $avatarBaseUrl . $avatar->getAvatarPath();
            }

            $dateFormat = $comment->getDateComment()->format('Y/m/d H:i');

            $commentsArray[] = [
                "comment" => $comment->getContentComment(),
                "date" => $dateFormat,
                "username" => $user->getName(),
                "avatarPath" => $avatarPath
            ];
        }
        return $commentsArray;
    }

    /**
     * Return number of comment for a trick
     * @param Trick $trick
     * @return int
     */
    public function numberComment(Trick $trick): int
    {
        return $this->commentRepo->countComments($trick);
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

    /**
     * Remove all comment's trick
     */
    public function removeComments(Trick $trick)
    {

        $entityManager = $this->managerRegistry->getManager();

        $comments = $trick->getComments();

        foreach($comments as $comment) {

            $entityManager->remove($comment);
            
        }
        $entityManager->flush();
    }
}
