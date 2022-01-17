<?php
/**
 * This class contain Trick's functions
 */
namespace App\Service;

use App\Entity\Trick;
use App\Repository\TrickRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickService
{
    private $managerRegistry;
    private $tokenManager;
    private $router;
    private $trickRepo;
    private $typeService;
    private $imageService;
    private $mediaService;
    private $commentService;

    public function __construct(
        ManagerRegistry $managerRegistry,
        CsrfTokenManagerInterface $tokenManager,
        TrickRepository $trickRepo,
        TrickTypeService $typeService,
        TrickImageService $imageService,
        TrickMediaService $mediaService,
        UrlGeneratorInterface $router,
        CommentService $commentService)
    {
        $this->managerRegistry = $managerRegistry;
        $this->tokenManager = $tokenManager;
        $this->router = $router;
        $this->trickRepo = $trickRepo;
        $this->typeService = $typeService;
        $this->imageService = $imageService;
        $this->mediaService = $mediaService;
        $this->commentService = $commentService;
    }

    /**
     * Create Trick
     * @return Trick
     */
    public function newTrick(): Trick
    {
        return new Trick();
    }

    /**
     * Add trick with images/medias to the database
     */
    public function addTrick(Form $form, Trick $trick)
    {

        $entityManager = $this->managerRegistry->getManager();

        if($form->get("addNewType")->getData() === true) {

            $trickType = $this->typeService->createTrickType($form->get("newTrickType")->getData());
            $trick->setTrickType($trickType);

        }

        $images = $form->get("images")->getData();

        $this->imageService->addImagesToTrick($images, $trick);

        $medias = $form->get("medias")->getData();

        $this->mediaService->addMediasToTrick($medias, $trick);

        $this->addGenerateInfo($trick);

        $entityManager->persist($trick);
        $entityManager->flush();

    }

    /**
     * Add current date and slug to trick
     * @param Trick $trick
     * @return void
     */
    public function addGenerateInfo(Trick $trick)
    {
        // Add current timestamp
        $dateTime = new \DateTime();
        $trick->setCreationDateTrick($dateTime->setTimestamp(time()));

        // Slug name for SEO
        $slugger = new AsciiSlugger();
        $slug = $slugger->slug($trick->getNameTrick());

        $trick->setSlugTrick($slug);

    }

    /**
     * Update trick
     */
    public function updateTrick(Form $form, Trick $trick)
    {
        $entityManager = $this->managerRegistry->getManager();

        $images = $form->get("images")->getData();
        $this->imageService->addImagesToTrick($images, $trick);

        $medias = $form->get("medias")->getData();

        $this->mediaService->addMediasToTrick($medias, $trick);

        $this->addEditTime($trick);

        $entityManager->flush();
    }

    /**
     * Add modification date to trick
     */
    public function addEditTime(Trick $trick)
    {
        // Add current timestamp
        $dateTime = new \DateTime();
        $trick->setModificationDateTrick($dateTime->setTimestamp(time()));
    }

    /**
     * Delete trick with images and medias
     */
    public function deleteTrick(Trick $trick)
    {
        $entityManager = $this->managerRegistry->getManager();

        $this->imageService->removeImages($trick);

        $this->mediaService->removeMedias($trick);

        $this->commentService->removeComments($trick);

        $entityManager->remove($trick);
        $entityManager->flush();
    }

    /**
     * Return array of tricks for homepage
     */
    public function getTricksForJson(int $limit, int $offset): array
    {
        /**
         * We need to generate some values inside symfony because
         * when we render some symfony values in Twig with JavaScript,
         * JS values not properly rendered
         */

        $tricks = $this->trickRepo->getTricks($limit, $offset);

        $tricksArray = [];

        foreach($tricks as $trick) {

            $image = "";
            if($trick->getImages()[0] != null) {
                $image = $trick->getImages()[0]->getPathTrickImage();
            }

            $token = $this->tokenManager->getToken('delete' . $trick->getIdTrick())->getValue();

            
            $trickUrl = $this->router->generate("tricks_details", [
                "slugTrick" => $trick->getSlugTrick(),]);

            $updateUrl = $this->router->generate("update_trick", [
                "slugTrick" => $trick->getSlugTrick(),]);

            $deleteUrl = $this->router->generate("delete_trick", [
                "slugTrick" => $trick->getSlugTrick(),]);
            
            $tricksArray[] = [
                "id" => $trick->getIdTrick(),
                "name" => $trick->getNameTrick(),
                "slug" => $trickUrl,
                "imagePath" => $image,
                "updateUrl" => $updateUrl,
                "deleteUrl" => $deleteUrl,
                "token" => $token
            ];

        }

        return $tricksArray;
    }

    /**
     * Return number of tricks
     */
    public function countTrick(): Int
    {
        return $this->trickRepo->countTricks();
    }

}
