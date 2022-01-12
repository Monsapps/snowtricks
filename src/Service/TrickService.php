<?php
/**
 * This class contain Trick's functions
 */
namespace App\Service;

use App\Entity\Trick;
use Symfony\Component\String\Slugger\AsciiSlugger;

class TrickService
{
    /**
     * Add current date and slug to trick
     * @param Trick $trick
     * @return void
     */
    function addGenerateInfo(Trick $trick)
    {
        // Add current timestamp
        $dateTime = new \DateTime();
        $trick->setCreationDateTrick($dateTime->setTimestamp(time()));

        // Slug name for SEO
        $slugger = new AsciiSlugger();
        $slug = $slugger->slug($trick->getNameTrick());

        $trick->setSlugTrick($slug);

    }

}
