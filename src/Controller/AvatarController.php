<?php
/**
 * Avatar controlelr
 */
namespace App\Controller;

use App\Entity\Avatar;
use App\Type\AvatarType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AvatarController extends AbstractController
{
    /**
     * @Route("update/avatar",
     *      name="update_avatar",
     *      methods={"GET", "POST"})
     */
    function updateAvatar(
        Request $request,
        ManagerRegistry $managerRegistry)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $user = $this->getUser();
        
        $avatar = new Avatar();

        $updateAvatar = false;

        if($user->avatar !== null) {
            $avatar = $user->avatar;
            $updateAvatar = true;
        }

        $form = $this->createForm(AvatarType::class, $avatar, [
            "action" => $this->generateUrl("update_avatar"),
            "method" => "POST"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if($updateAvatar) {
                // Remove old avatar and add the new one
                $filesystem = new Filesystem();
                $filesystem->remove($this->getParameter("avatar_image_path") . "/" . $avatar ->getAvatarPath());
            }

            $avatarFile = $form->get("image")->getData();

            $avatarName = md5(uniqid()) .".". $avatarFile->guessExtension();

            try {
                $avatarFile->move(
                    $this->getParameter("avatar_image_path"),
                    $avatarName
                );
            } catch(FileException $e) {

                $this->addFlash("negative-response", "Error when moving file inside the server: ". $e);

                return $this->renderForm("user/_user_avatar_form.html.twig", [
                    "avatar" => $avatar,
                    "form" => $form
                ]);
            }

            $avatar->setAvatarPath($avatarName);
            $entityManager = $managerRegistry->getManager();

            if(!$updateAvatar) {
                // add user to the new avatar entity
                $avatar->setUser($user);
                $entityManager->persist($avatar);
            }

            $entityManager->flush();
        }

        return $this->renderForm("user/_user_avatar_form.html.twig", [
            "avatar" => $avatar,
            "form" => $form
        ]);
    }
}
