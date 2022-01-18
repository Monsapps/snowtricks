<?php
/**
 * Trick controlller
 */
namespace App\Controller;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use App\Service\CommentService;
use App\Service\TrickService;
use App\Type\CommentType;
use App\Type\TrickType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TrickController extends AbstractController
{
    /**
     * Display all tricks
     * @Route("/tricks", name="tricks")
     */
    public function tricks(TrickRepository $trickRepository)
    {
        return $this->render('trick/tricks.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    /**
     * Display add trick page
     * 
     * @Route("/tricks/add", name="add_trick")
     */
    public function addTrickPage(
        Request $request,
        TrickService $trickService)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $trick = $trickService->newTrick();

        $trickForm = $this->createForm(TrickType::class, $trick);

        $trickForm->handleRequest($request);
        
        if ($trickForm->isSubmitted() && $trickForm->isValid()) {

            $trickService->addTrick($trickForm, $trick);

            $this->addFlash("positive-response", "Trick added successfully!");

            return $this->redirectToRoute("tricks");
        }

        return $this->renderForm("trick/add_trick.html.twig", array(
            "trick" => $trick,
            "form" => $trickForm
        ));
    }

    /**
     * Display unique trick
     * @Route("/tricks/details/{slugTrick}", name="tricks_details")
     */
    public function trick(
        Trick $trick,
        Request $request,
        CommentService $commentService)
    {
        $user = $this->getUser();

        $comment = $commentService->newComment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $commentService->addComment($comment, $trick, $user);

            $this->addFlash("positive-response", "Comment added successfully!");
            return $this->redirectToRoute("tricks_details", ["slugTrick" => $trick->getSlugTrick()]);
        }

        return $this->renderForm('trick/trick.html.twig', [
            "trick" => $trick,
            "form" => $form,
            "user" => $user
        ]);
    }

    /**
     * Update trick page
     * @Route("/tricks/update/{slugTrick}", name="update_trick")
     */
    public function update(
        Trick $trick,
        Request $request,
        TrickService $trickService)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");
        
        $form = $this->createForm(
            TrickType::class,
            $trick,
            [
                "createName" => false,
                "createType" => false
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $trickService->updateTrick($form, $trick);
            
            $this->addFlash("positive-response", "Trick updated successfully");
            return $this->redirectToRoute("tricks_details", ["slugTrick" => $trick->getSlugTrick()]);
        }

        return $this->renderForm('trick/update_trick.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/trick/delete/{slugTrick}",
     *      name="delete_trick",
     *      methods={"DELETE"})
     */
    public function delete(
        Trick $trick,
        Request $request,
        TrickService $trickService)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete' . $trick->getIdTrick(), $data["__token"])){

            $trickService->deleteTrick($trick);

            return new JsonResponse([
                "success" => 1,
                "tricksPage" => $this->generateUrl("tricks")
            ]);
        }

        return new JsonResponse(['error' => 'Invalid token'], 400);
    }

    /**
     * @Route("/tricks/get/{limit}/{page}/",
     *      name="get_tricks",
     *      methods={"GET"})
     */
    public function getTrick(
        int $limit,
        int $page,
        TrickService $trickService)
    {

        if(!is_numeric($limit) || !is_numeric($page)) {
            return new JsonResponse(["error" => "Bad request"], 400);
        }

        $offset = ($page > 0) ? (($page - 1) * $limit) : 0 ;

        $tricks = $trickService->getTricksForJson($limit, $offset);

        $count = $trickService->countTrick();

        return new JsonResponse([
            "count" => $count,
            "tricks" => $tricks
        ]);
    }
}
