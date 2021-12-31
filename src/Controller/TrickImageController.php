<?php
/**
 * Trick image controlller
 */
namespace App\Controller;

use App\Entity\TrickImage;
use App\Type\TrickImageType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
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
        ManagerRegistry $managerRegistry)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $form = $this->createForm(TrickImageType::class, $trickImage, [
            "action" => $this->generateUrl("update_image", ["id" => $trickImage->getId()]),
            "method" => "POST"
        ]);
        $form->handleRequest($request);

        // overwrite new image with the name of actual image
        if ($form->isSubmitted() && $form->isValid()) {

            $imageName = $trickImage->getPathTrickImage();
            // Delete old image
            $filesystem = new Filesystem();
            $filesystem->remove($this->getParameter("trick_image_path") . "/" . $imageName);

            $image = $form->get("image")->getData();

            $newImageName = md5(uniqid()).'.'.$image->guessExtension();
            // Move new image to the directory
            $image->move(
                $this->getParameter("trick_image_path"),
                $newImageName
            );
            $trickImage->setPathTrickImage($newImageName);

            $entityManager = $managerRegistry->getManager();
            $entityManager->flush();
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
        ManagerRegistry $managerRegistry)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $data = json_decode($request->getContent(), true);

        // On vérifie si le token est valide
        if($this->isCsrfTokenValid('delete'.$trickImage->getId(), $data['__token'])){
            // On récupère le nom de l'image
            $imagePath = $trickImage->getPathTrickImage();

            // On supprime le fichier
            $filesystem = new Filesystem();
            $filesystem->remove($this->getParameter("trick_image_path") . "/" . $imagePath);

            // On supprime l'entrée de la base
            $entityManager = $managerRegistry->getManager();
            $entityManager->remove($trickImage);
            $entityManager->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }

        return new JsonResponse(['error' => 'Invalid token'], 400);
    }

}
