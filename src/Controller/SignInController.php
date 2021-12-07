<?php
/**
 * Sign in controller
 * 
 * Control authenticity of email and sign in
 * 
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use Doctrine\Persistence\ManagerRegistry;

use App\Repository\UserRepository;

class SignInController extends AbstractController
{
    /**
     * Display sign in page
     * 
     * @Route("/signin", name="signin")
     */
    public function index(AuthenticationUtils $authenticationUtils)
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render("signin/signin.html.twig", [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * Confirm user with specific token
     * 
     * @Route("/confirm/{token}", name="confirm_email")
     */
    public function new(
        $token,
        UserRepository $userRepository,
        ManagerRegistry $managerRegistry,
        AuthenticationUtils $authenticationUtils)
    {
        $user = $userRepository->findOneBy(['registrationToken' => $token]);
        if($user) {
            // User exist
            $entityManager = $managerRegistry->getManager();
            $user->setRoles(["ROLE_USER", "ROLE_CONFIRMED_USER"]);

            // Delete regristration token
            $user->setRegistrationToken(null);
            $entityManager->flush();
            $this->addFlash("positive-response", "Account activated, let's sign in!");

            $error = $authenticationUtils->getLastAuthenticationError();
            $lastUsername = $authenticationUtils->getLastUsername();

            return $this->render("signin/signin.html.twig", [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]);
        }
        // Add flash with error
        $this->addFlash("negative-response", "Account no found, did you get an old email?");
        
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
        return $this->render("signin/signin.html.twig", [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * Create a sign in form
     * @return SignInType
     */
    private function getSignInForm()
    {
        // return signin form
        return $this->render("signin/signin.html.twig");
    }

}