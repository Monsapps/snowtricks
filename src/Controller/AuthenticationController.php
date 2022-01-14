<?php
/**
 * Authentication controller, users can register, sign in, sign out, forget password
 */
namespace App\Controller;

use App\Service\AuthenticationService;
use App\Type\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthenticationController extends AbstractController
{
    /**
     * Display sign in page
     * 
     * @Route("/login", name="signin")
     */
    public function index(AuthenticationUtils $authenticationUtils)
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render("user/signin.html.twig", [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

        /**
     * Display sign up form page
     * 
     * @Route("/registration", name="signup")
     * @return void
     */
    public function register(
        Request $request,
        AuthenticationService $userService)
    {
        $user = $userService->newUser();

        $registrationForm = $this->createForm(RegistrationType::class, $user);

        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {

            $userService->registerUser($user);

            // Return to homepage with flash message
            $this->addFlash("positive-response", "Account created, however we have sent you an email to confirm it.");

            return $this->redirect("/");
        }

        return $this->render("user/signup.html.twig", array(
            "form" => $registrationForm->createView()
        ));
    }

    /**
     * Confirm user with specific token
     * 
     * @Route("/confirm/{token}", name="confirm_email")
     */
    public function confirmUserEmail(
        $token,
        AuthenticationService $userService,
        AuthenticationUtils $authenticationUtils)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $user = $userService->confirmUser($token);

        if($user) {

            $this->addFlash("positive-response", "Account activated, let's sign in!");

            return $this->render("user/signin.html.twig", [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]);
        }

        // Add flash with error
        $this->addFlash("negative-response", "Account no found, did you get an old email?");
        
        return $this->render("user/signin.html.twig", [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    /**
     * @Route("/forgot_password", name="forgot_password")
     */
    public function forgotPassword(
        Request $request,
        AuthenticationService $userService)
    {

        $form = $this->createFormBuilder()
            ->add("name", TextType::class, [
                "label" => "Username"
            ])
            ->add("ask", SubmitType::class, [
                "label" => "Ask for reset password",
                "attr" => [
                    "class" => "btn btn-primary"
                ]
            ])
            ->getForm();

        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userService->searchUsername($form->get("name")->getData());

            if($user) {

                // Send email with password token and write token in db
                $userService->sendConfirmationEmail($user);

                $this->addFlash("positive-response", "An email has been send to create a new password.");
                return $this->redirect("/");
            }

            $this->addFlash("negative-response", "Unknow username");
        }

        return $this->renderForm("user/forgot_password.html.twig", [
            "form" => $form
        ]);
    }

    /**
     * @Route("/reset_password/{token}", name="reset_password")
     */
    public function resetPassword(
        $token,
        Request $request,
        AuthenticationService $userService
    )
    {
        $form = $this->createFormBuilder()
        ->add("username", TextType::class)
        ->add("password", PasswordType::class, [
            "label" => "Password",
            "help" => "Type your new password"
        ])
        ->add("token", HiddenType::class, [
            "data" => $token
        ])
        ->add("reset", SubmitType::class, [
            "label" => "Reset",
            "attr" => [
                "class" => "btn btn-primary"
            ]
        ])
        ->getForm();

    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userService->searchUserTokenPass(
                $form->get("username")->getData(),
                $form->get("token")->getData()
            );

            if($user) {

                // hash new password and push it in db
                $password = $form->get("password")->getData();
                
                $userService->renewPassword($user, $password);

                $this->addFlash("positive-response", "Your new password has been set, ");
                return $this->redirect("/");
            }

            $this->addFlash("negative-response", "Unknow user, you must ask a new forget password request");
        }
        return $this->renderForm("user/reset_password.html.twig", [
            "form" => $form
        ]);
    }
}
