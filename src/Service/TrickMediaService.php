<?php
/**
 * This class contain trick media functions
 */
namespace App\Service;

use App\Entity\Trick;
use App\Entity\TrickMedia;
use Doctrine\Persistence\ManagerRegistry;

class TrickMediaService
{

    private $managerRegistry;

    public function __construct(
        ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
        
    }

    /**
     * Add medias to trick
     */
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

    /**
     * Store updated media url to database
     */
    public function updateMedia()
    {
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->flush();
    }

    /**
     * Remove media from database
     */
    public function removeMedia(TrickMedia $trickMedia)
    {
        $entityManager = $this->managerRegistry->getManager();

        $entityManager->remove($trickMedia);

        $entityManager->flush();
    }


    /**
     * Remove medias to trick
     */
    public function removeMedias(Trick $trick)
    {
        $entityManager = $this->managerRegistry->getManager();

        $medias = $trick->getMedias();

        foreach($medias as $media) {
            $entityManager->remove($media);
        }
        
        $entityManager->flush();
    }
}
