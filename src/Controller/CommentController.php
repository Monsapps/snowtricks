<?php
/**
 * Comment controller
 */
namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Repository\CommentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * Add comment function
     * @Route("/comment/{slugTrick}/{limit<\d+>}/{page<\d+>}/",
     *      name="get_comment",
     *      methods={"GET"})
     */
    public function getComments(
        Trick $trick,
        int $limit,
        int $page,
        CommentRepository $commentRepo)
    {

        $offset = ($page > 0) ? (($page - 1) * $limit) : 0 ;

        $comments = $commentRepo->getComments($trick, $limit, $offset);
        $commentArray = [];

        $package = new Package(new EmptyVersionStrategy);
        $avatarBaseUrl = $package->getUrl("/images/avatars/");

        foreach($comments as $comment) {
            $user = $comment->getUser();
            $avatar = $user->getAvatar();
            $avatarPath = $avatarBaseUrl . "default.png";
            if($avatar !== null) {
                $avatarPath = $avatarBaseUrl . $avatar->getAvatarPath();
            }

            $dateFormat = $comment->getDateComment()->format('Y/m/d H:i');

            $commentArray[] = [
                "comment" => $comment->getContentComment(),
                "date" => $dateFormat,
                "username" => $user->getName(),
                "avatarPath" => $avatarPath
            ];
        }

        $count = $commentRepo->countComments($trick);

        $newUrl = $this->generateUrl("get_comment", [
            "slugTrick" => $trick->getSlugTrick(),
            "limit" => $limit,
            "page" => $page+1]);

        return new JsonResponse([
            "count" => $count,
            "next" => $newUrl,
            "comments" => $commentArray
        ]);
    }

}
