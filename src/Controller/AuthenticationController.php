<?php
/**
 * Authentication controller, users can register, sign in, sign out, forget password
 */
namespace App\Controller;

use App\Entity\User;
use App\Event\ConfirmEmailEvent;
use App\Event\ForgetEmailEvent;
use App\Repository\UserRepository;
use App\Service\TokenGenerator;
use App\Type\RegistrationType;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
        UserPasswordHasherInterface $passwordHasher,
        EventDispatcherInterface $eventDispatcher,
        MailerInterface $mailer,
        ManagerRegistry $managerRegistry,
        TokenGenerator $token)
    {
        $user = new User();
        $registrationForm = $this->createForm(RegistrationType::class, $user);

        $registrationForm->handleRequest($request);

        if ($registrationForm->isSubmitted() && $registrationForm->isValid()) {
            $user = $registrationForm->getData();

            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $user->getPassword()
            );

            // Update with hashed password
            $user->setPassword($hashedPassword);

            // Add current timestamp
            $dateTime = new \DateTime();
            $user->setRegistrationDate($dateTime->setTimestamp(time()));

            // Add token for confirmation email
            $confirmToken = $token->getToken();
            $user->setRegistrationToken($confirmToken);
            
            // Doctrine and add user into database
            $entityManager = $managerRegistry->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // Add event to send email
            $event = new ConfirmEmailEvent(
                $user->getEmail(),
                $user->getName(),
                $confirmToken,
                $request,
                $mailer
            );
            $eventDispatcher->dispatch($event, ConfirmEmailEvent::NAME);

            // Return to homepage with flash message
            $this->addFlash("positive-response", "Account created, however we have sent you an email to confirm it.");

            return $this->redirect("/");
            //dump($user);
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

            return $this->render("user/signin.html.twig", [
                'last_username' => $lastUsername,
                'error'         => $error,
            ]);
        }
        // Add flash with error
        $this->addFlash("negative-response", "Account no found, did you get an old email?");
        
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        
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
        UserRepository $userRepository,
        EventDispatcherInterface $eventDispatcher,
        ManagerRegistry $managerRegistry,
        MailerInterface $mailer,
        TokenGenerator $token
    )
    {

        $form = $this->createFormBuilder()
            ->add("name", TextType::class)
            ->add("ask", SubmitType::class, [
                "label" => "Ask for reset password"
            ])
            ->getForm();

        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->findOneBy(['name' => $form->get("name")->getData()]);

            if($user) {
                // Send email with password token and write token in db
                $forgotToken = $token->getToken();

                $user->setPasswordToken($forgotToken);

                // Update user in db
                $entityManager = $managerRegistry->getManager();
                $entityManager->flush();

                // Add event to send email
                $event = new ForgetEmailEvent(
                    $user->getEmail(),
                    $user->getName(),
                    $forgotToken,
                    $request,
                    $mailer
                );
                $eventDispatcher->dispatch($event, ForgetEmailEvent::NAME);

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
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher,
        ManagerRegistry $managerRegistry
    )
    {
        $form = $this->createFormBuilder()
        ->add("username", TextType::class)
        ->add("password", PasswordType::class, [
            "label" => "Password"
        ])
        ->add("token", HiddenType::class, [
            "data" => $token
        ])
        ->add("reset", SubmitType::class, [
            "label" => "Reset"
        ])
        ->getForm();

    
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $userRepository->findOneBy([
                "name" => $form->get("username")->getData(),
                "passwordToken" => $form->get("token")->getData()
            ]);
            if($user) {

                // hash new password and push it in db
                $password = $form->get("password")->getData();
                $hashedPassword = $passwordHasher->hashPassword(
                    $user,
                    $password
                );
                $user->setPassword($hashedPassword);

                // Delete forget password token
                $user->setPasswordToken(null);

                // Update user in db
                $entityManager = $managerRegistry->getManager();
                $entityManager->flush();

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
