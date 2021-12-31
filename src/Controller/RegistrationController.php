<?php
/**
 * Registration controller
 * 
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Mailer\MailerInterface;
use Doctrine\Persistence\ManagerRegistry;

use App\Entity\User;
use App\Type\RegistrationType;
use App\Event\ConfirmEmailEvent;

class RegistrationController extends AbstractController
{
    /**
     * Display sign up form page
     * 
     * @Route("/signup", name="signup")
     */
    public function new(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EventDispatcherInterface $eventDispatcher,
        MailerInterface $mailer,
        ManagerRegistry $managerRegistry)
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
            $confirmToken = bin2hex(openssl_random_pseudo_bytes(6));
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

            return $this->redirect("./");
            //dump($user);
        }

        return $this->render("signup/signup.html.twig", array(
            "form" => $registrationForm->createView()
        ));
    }
}
