<?php
/**
 * This is the service container for avatar
 */
namespace App\Service;

use App\Entity\Avatar;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

class AvatarService
{
    private $managerRegistry;
    private $path;

    public function __construct(
        ContainerInterface $container,
        ManagerRegistry $managerRegistry)
    {
        $this->path = $container->getParameter("avatar_image_path");
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * Create new avatar
     * @return Avatar
     */
    public function createAvatar(): Avatar
    {
        return new Avatar();
    }

    /**
     * Remove old avatar file
     */
    public function removeAvatarFile(string $filename)
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->path . "/" . $filename);
    }

    /**
     * Add or update avatar?
     */
    public function addOrUpdate(User $user, Avatar $avatar, File $image): bool
    {
        $isUpdate = false;

        if($user->avatar !== null) {
            $avatar = $user->avatar;
            $this->removeAvatarFile($avatar->getAvatarPath());
            $isUpdate = true;
        }

        $avatarName = md5(uniqid()) .".". $image->guessExtension();

        try {
            $image->move(
                $this->path,
                $avatarName
            );
        } catch(FileException $e) {
            unset($e);
            return false;
        }

        $avatar->setAvatarPath($avatarName);

        $entityManager = $this->managerRegistry->getManager();

        if(!$isUpdate) {
            // add user to the new avatar entity
            $avatar->setUser($user);
            $entityManager->persist($avatar);
        }

        $entityManager->flush();

        return true;
    }

}
