<?php
/**
 * Trick controlller
 */
namespace App\Controller;

use App\Entity\Trick;
use App\Entity\TrickImage;
use App\Entity\TrickMedia;
use App\Entity\TrickType as EntityTrickType;
use App\Repository\TrickRepository;
use App\Type\TrickImageType;
use App\Type\TrickMediaType;
use App\Type\TrickType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\AsciiSlugger;

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
        ManagerRegistry $managerRegistry)
    {

        $trick = new Trick();

        $trickForm = $this->createForm(TrickType::class, $trick);

        $trickForm->handleRequest($request);
        
        if ($trickForm->isSubmitted() && $trickForm->isValid()) {

            $entityManager = $managerRegistry->getManager();

            if($trickForm->get("addNewType")->getData() === true) {
                // Create new trick type
                $trickType = new EntityTrickType();
                $trickType->setName($trickForm->get("newTrickType")->getData());
                $trick->setTrickType($trickType);
            }

            $images = $trickForm->get("images")->getData();
            foreach($images as $image) {
                $imageName = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter("trick_image_path"),
                    $imageName
                );
                $imageEntity = new TrickImage();
                $imageEntity->setPathTrickImage($imageName);
                $trick->addImage($imageEntity);
            }

            $medias = $trickForm->get("medias")->getData();
            foreach($medias as $media) {
                // If media box is not empty
                if(!empty($media)) {
                    $mediaEntity = new TrickMedia();
                    $mediaEntity->setUrlTrickMedia($media);
                    $trick->addMedia($mediaEntity);
                }
            }

            // Add current timestamp
            $dateTime = new \DateTime();
            $trick->setCreationDateTrick($dateTime->setTimestamp(time()));

            // Slug name for SEO
            $slugger = new AsciiSlugger();
            $slug = $slugger->slug($trickForm->get("nameTrick")->getData());

            $trick->setSlugTrick($slug);

            $entityManager->persist($trick);
            $entityManager->flush();

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
    public function trick(Trick $trick)
    {
        return $this->render('trick/trick.html.twig', [
            'trick' => $trick,
        ]);
    }

    /**
     * Update trick page
     * 
     * @Route("/tricks/update/{slugTrick}", name="update_trick")
     */
    public function update(
        Trick $trick,
        Request $request,
        ManagerRegistry $managerRegistry)
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

            $entityManager = $managerRegistry->getManager();

            $images = $form->get("images")->getData();
            foreach($images as $image) {
                $imageName = md5(uniqid()).'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter("trick_image_path"),
                    $imageName
                );
                $imageEntity = new TrickImage();
                $imageEntity->setPathTrickImage($imageName);
                $trick->addImage($imageEntity);
            }

            $medias = $form->get("medias")->getData();
            foreach($medias as $media) {
                // If media box is not empty
                if(!empty($media)) {
                    $mediaEntity = new TrickMedia();
                    $mediaEntity->setUrlTrickMedia($media);
                    $trick->addMedia($mediaEntity);
                }
            }

            // Add current timestamp
            $dateTime = new \DateTime();
            $trick->setModificationDateTrick($dateTime->setTimestamp(time()));

            $entityManager->flush();
            
            $this->addFlash("positive-response", "Trick updated successfully");
            return $this->redirectToRoute("tricks_details", ["slugTrick" => $trick->getSlugTrick()]);
        }

        return $this->renderForm('trick/update_trick.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/trick/delete/{idTrick}",
     *      name="delete_trick",
     *      methods={"DELETE"})
     */
    public function delete(
        Trick $trick,
        Request $request,
        ManagerRegistry $managerRegistry)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $data = json_decode($request->getContent(), true);
        if($this->isCsrfTokenValid('delete'.$trick->getIdTrick(), $data['_token'])){

            $entityManager = $managerRegistry->getManager();

            $entityManager->remove($trick);
            $entityManager->flush();
    
            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Token Invalide'], 400);
        }
    }

    /**
     * @Route("/tricks/get/{limit}/{page}/",
     *      name="get_tricks",
     *      methods={"GET"})
     */
    public function getTrick(
        $limit,
        $page,
        ManagerRegistry $managerRegistry)
    {

        if(!is_numeric($limit) || !is_numeric($page)) {
            return new JsonResponse(["error" => "Bad request"], 400);
        }

        $offset = ($page > 0) ? ($page - 1) : 0 ;
        
        $tricks = $managerRegistry->getRepository(Trick::class)
            ->findBy(
                array(),
                array('creationDateTrick' => 'DESC'),
                $limit,
                $offset
            );
    
        $tricksArray = [];
        foreach($tricks as $trick) {
            $image = "";
            if($trick->getImages()[0] != null) {
                $image = $trick->getImages()[0]->getPathTrickImage();
            }
            $tricksArray[] = [
                "name" => $trick->getNameTrick(),
                "slug" => $trick->getSlugTrick(),
                "imagePath" => $image
            ];
        }

        $count = count($tricks);

        return new JsonResponse([
            "count" => $count,
            "tricks" => $tricksArray
        ]);
    }


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
            unlink($this->getParameter("trick_image_path").'/'.$imageName);

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
            unlink($this->getParameter("trick_image_path") . "/" . $imagePath);

            // On supprime l'entrée de la base
            $entityManager = $managerRegistry->getManager();
            $entityManager->remove($trickImage);
            $entityManager->flush();

            // On répond en json
            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Invalid token'], 400);
        }
    }

    /**
     * @Route("/update/media/{id}", name="update_media", methods={"POST", "GET"})
     */
    public function updateMeda(
        TrickMedia $trickMedia,
        Request $request,
        ManagerRegistry $managerRegistry)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $form = $this->createForm(TrickMediaType::class, $trickMedia, [
            "action" => $this->generateUrl("update_media", ["id" => $trickMedia->getId()]),
            "method" => "POST"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /*$media = $form->get("media")->getData();

            $trickMedia->setUrlTrickMedia($media);*/

            $entityManager = $managerRegistry->getManager();
            $entityManager->flush();
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
        ManagerRegistry $managerRegistry)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $data = json_decode($request->getContent(), true);

        if($this->isCsrfTokenValid('delete'.$trickMedia->getId(), $data['__token'])){

            $entityManager = $managerRegistry->getManager();
            $entityManager->remove($trickMedia);
            $entityManager->flush();

            return new JsonResponse(['success' => 1]);
        }else{
            return new JsonResponse(['error' => 'Invalid token'], 400);
        }
    }

}
