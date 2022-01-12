<?php
/**
 * This class contain Trick's media functions
 */
namespace App\Service;

use App\Entity\Trick;
use App\Entity\TrickImage;

class TrickImageService
{
    /**
     * Add images to Trick and move it to the proper folder
     */
    public function addImagesToTrick(array $images, string $path, Trick $trick)
    {
        foreach($images as $image) {
            $imageName = md5(uniqid()).'.'.$image->guessExtension();
            $image->move(
                $path,
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
    public function removeImages(array $images, string $path, Trick $trick)
    {
        
    }
}
