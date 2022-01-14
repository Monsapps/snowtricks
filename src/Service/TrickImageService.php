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
use Symfony\Component\HttpFoundation\File\File;

class TrickImageService
{
    private $path;
    private $managerRegistry;

    public function __construct(
        ContainerInterface $container,
        ManagerRegistry $managerRegistry)
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

            $imageName = $this->moveFile($image);

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

        $images = $trick->getImages();

        foreach($images as $image) {

            $this->removeFile($image);

            $entityManager->remove($image);
        }
        $entityManager->flush();
    }

    /**
     * Move image file & return filename
     * @param TrickImage $trickImage
     * @return string
     */
    public function moveFile(File $image): string
    {
        $imageName = md5(uniqid()).'.'.$image->guessExtension();
        $image->move(
            $this->path,
            $imageName
        );
        return $imageName;
    }

    /**
     * Remove image file 
     * @param TrickImage $trickImage
     */
    public function removeFile(TrickImage $trickImage)
    {
        $filesystem = new Filesystem();

        // Get image filename
        $imagePath = $trickImage->getPathTrickImage();
        // Remove file
        $filesystem->remove($this->path . "/" . $imagePath);
    }

    /**
     * Update image
     */
    public function updateImage(TrickImage $trickImage, File $image)
    {
        $this->removeFile($trickImage);

        $imageName = $this->moveFile($image);

        $trickImage->setPathTrickImage($imageName);

        $entityManager = $this->managerRegistry->getManager();
        $entityManager->flush();
    }

    /**
     * Remove single image
     */
    public function removeImage(TrickImage $trickImage)
    {
        $this->removeFile($trickImage);

        $entityManager = $this->managerRegistry->getManager();

        $entityManager->remove($trickImage);

        $entityManager->flush();
    }
}
