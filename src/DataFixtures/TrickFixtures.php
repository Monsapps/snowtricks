<?php
/**
 * This class generate trick
 */
namespace App\DataFixtures;

use App\Entity\Trick;
use App\Service\TrickService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TrickFixtures extends Fixture implements DependentFixtureInterface
{

    const TRICKS = [
        "One-Two",
        "A B",
        "Beef Carpaccio",
        "Beef Curtains",
        "Bloody Dracula",
        "Chicken salad",
        "Drunk Driver",
        "Gorilla",
        "Japan air",
        "Mute",
        "Pickpocket",
        "Rocket Air"
    ];

    const DEFAULT_DESC = "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";

    private $trickService;

    public function __construct(TrickService $trickService)
    {
        $this->trickService = $trickService;
    }

    public function load(ObjectManager $manager)
    {

        foreach(self::TRICKS as $newTrick) {

            // Call Service it's better

           $trick = new Trick();

           $trick->setNameTrick($newTrick);

           $trick->setTrickType($this->getReference(TrickTypeFixtures::GRABS_TYPE));

           $trick->setDescriptionTrick(self::DEFAULT_DESC);

           $this->trickService->addGenerateInfo($trick);

           //dump($trick);
            $manager->persist($trick);
            $manager->flush();
       }
    }

    public function getDependencies()
    {
        return [
            TrickTypeFixtures::class
        ];
    }
}
