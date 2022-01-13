<?php
/**
 * This class contain trick media functions
 */
namespace App\Service;

use App\Entity\Trick;
use App\Entity\TrickMedia;

class TrickMediaService
{
    public function addMediasToTrick(array $medias, Trick $trick)
    {
        foreach($medias as $media) {
            // If media box is not empty
            if(!empty($media)) {
                $mediaEntity = new TrickMedia();
                $mediaEntity->setUrlTrickMedia($media);
                $trick->addMedia($mediaEntity);
            }
        }
    }
}
