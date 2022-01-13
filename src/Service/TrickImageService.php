<?php
/**
 * This class contain Trick's image functions
 */
namespace App\Service;

use App\Entity\Trick;
use App\Entity\TrickImage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TrickImageService
{
    private $path;

    public function __construct(ContainerInterface $container)
    {
        $this->path = $container->getParameter("trick_image_path");
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
    public function removeImages(array $images, Trick $trick)
    {
        
    }
}
