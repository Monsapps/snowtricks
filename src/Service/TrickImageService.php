<?php
/**
 * This class contain Trick's image functions
 */
namespace App\Service;

use App\Entity\Trick;
use App\Entity\TrickImage;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;

class TrickImageService
{
    private $path;
    private $managerRegistry;

    public function __construct(
        ContainerInterface $container,
        ManagerRegistry $managerRegistry
    )
    {
        $this->path = $container->getParameter("trick_image_path");
        $this->managerRegistry = $managerRegistry;
    }
    /**
     * Add images to Trick and move it to the proper folder
     */
    public function addImagesToTrick(array $images, Trick $trick)
    {
        foreach($images as $image) {
            $imageName = md5(uniqid()).'.'.$image->guessExtension();
            $image->move(
                $this->path,
                $imageName
            );
            $imageEntity = new TrickImage();
            $imageEntity->setPathTrickImage($imageName);
            $trick->addImage($imageEntity);
        }
    }

    /**
     * Remove images to Trick and delete it from server
     */
    public function removeImages(Trick $trick)
    {
        $entityManager = $this->managerRegistry->getManager();
        $filesystem = new Filesystem();

        $images = $trick->getImages();

        foreach($images as $image) {
            $imagePath = $image->getPathTrickImage();

            $filesystem->remove($this->path . "/" . $imagePath);

            $entityManager->remove($image);
        }
        $entityManager->flush();
    }
}
