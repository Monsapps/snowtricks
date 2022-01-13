<?php
/**
 * This class generate trick type
 */
namespace App\DataFixtures;

use App\Service\TrickTypeService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TrickTypeFixtures extends Fixture
{

    const TYPES = [
        "Grabs",
        "Straight airs",
        "Flips",
        "Spins"
    ];

    public const GRABS_TYPE = 'grabs';
    private $trickTypeService;

    public function __construct(TrickTypeService $trickTypeService)
    {
        $this->trickTypeService = $trickTypeService;
    }

    public function load(ObjectManager $manager)
    {
        foreach(self::TYPES as $type){

            $trickType = $this->trickTypeService->createTrickType($type);

            if($type == "Grabs") {
                $this->addReference(self::GRABS_TYPE, $trickType);
            }

            //dump($trickType);
            $manager->persist($trickType);
            $manager->flush();
        }
    }

}
