<?php
/**
 * This class contain Trick type functions
 */
namespace App\Service;

use App\Entity\TrickType;

class TrickTypeService
{
    /**
     * Create new trick type and add name
     * @param string $name
     * @return TrickType
     */
    function createTrickType(string $name): TrickType
    {
        $trickType = new TrickType();

        $trickType->setName($name);

        return $trickType;
    }

}
