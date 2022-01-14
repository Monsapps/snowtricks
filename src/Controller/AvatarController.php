<?php
/**
 * Avatar controlelr
 */
namespace App\Controller;

use App\Service\AvatarService;
use App\Type\AvatarType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        AvatarService $avatarService)
    {
        $this->denyAccessUnlessGranted("ROLE_CONFIRMED_USER");

        $user = $this->getUser();
        
        $avatar = $avatarService->createAvatar();

        $form = $this->createForm(AvatarType::class, $avatar, [
            "action" => $this->generateUrl("update_avatar"),
            "method" => "POST"
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $avatarFile = $form->get("image")->getData();

            if(!$avatarService->addOrUpdate($user, $avatar, $avatarFile) ) {

                $this->addFlash("negative-response", "Error when moving file inside the server");

                return $this->renderForm("user/_user_avatar_form.html.twig", [
                    "avatar" => $avatar,
                    "form" => $form
                ]);
            }

        }

        return $this->renderForm("user/_user_avatar_form.html.twig", [
            "avatar" => $avatar,
            "form" => $form
        ]);
    }
}
