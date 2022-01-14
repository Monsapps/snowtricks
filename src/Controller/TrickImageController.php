<?php
/**
 * Trick image controlller
 */
namespace App\Controller;

use App\Entity\TrickImage;
use App\Service\TrickImageService;
use App\Type\TrickImageType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TrickImageController extends AbstractController
{
    /**
     * @Route("/update/image/{id}", name="update_image", methods={"POST", "GET"})
     */
    public function updateImage(
        TrickImage $trickImage,
        Request $request,
        TrickImageService $imageService)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $form = $this->createForm(TrickImageType::class, $trickImage, [
            "action" => $this->generateUrl("update_image", ["id" => $trickImage->getId()]),
            "method" => "POST"
        ]);
        $form->handleRequest($request);

        // overwrite new image with the name of actual image
        if ($form->isSubmitted() && $form->isValid()) {

            $imageService->updateImage($trickImage, $form->get("image")->getData());

        }

        return $this->renderForm("trick/_trick_image_form.html.twig", array(
            "image" => $trickImage,
            "form" => $form
        ));
    }

    /**
     * @Route("/delete/image/{id}", name="delete_image", methods={"DELETE"})
     */
    public function deleteImage(
        TrickImage $trickImage,
        Request $request,
        TrickImageService $imageService)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete'.$trickImage->getId(), $data['__token'])){

            $imageService->removeImage($trickImage);

            return new JsonResponse(['success' => 1]);
        }

        return new JsonResponse(['error' => 'Invalid token'], 400);
    }

}
