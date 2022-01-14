<?php
/**
 * Trick media controller
 */
namespace App\Controller;

use App\Entity\TrickMedia;
use App\Service\TrickMediaService;
use App\Type\TrickMediaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TrickMediaController extends AbstractController
{
    /**
     * @Route("/update/media/{id}", name="update_media", methods={"POST", "GET"})
     */
    public function updateMedia(
        TrickMedia $trickMedia,
        Request $request,
        TrickMediaService $mediaService)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $form = $this->createForm(TrickMediaType::class, $trickMedia, [
            "action" => $this->generateUrl("update_media", ["id" => $trickMedia->getId()]),
            "method" => "POST"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $mediaService->updateMedia();
            
        }

        return $this->renderForm("trick/_trick_media_form.html.twig", array(
            "media" => $trickMedia,
            "form" => $form
        ));
    }

    /**
     * @Route("/delete/media/{id}", name="delete_media", methods={"DELETE"})
     */
    public function deleteMedia(
        TrickMedia $trickMedia,
        Request $request,
        TrickMediaService $mediaService)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete'.$trickMedia->getId(), $data['__token'])){

            $mediaService->removeMedia($trickMedia);

            return new JsonResponse(['success' => 1]);
        }
        return new JsonResponse(['error' => 'Invalid token'], 400);
    }
}
