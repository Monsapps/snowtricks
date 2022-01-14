<?php
/**
 * Comment controller
 */
namespace App\Controller;

use App\Entity\Trick;
use App\Service\CommentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        CommentService $commentService)
    {

        $commentArray = $commentService->getCommentForJson($trick, $limit, $page);

        $count = $commentService->numberComment($trick);

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
