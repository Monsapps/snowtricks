<?php
/**
 * This class containe authentication functions
 */
namespace App\Service;

use App\Entity\User;
use App\Event\ConfirmEmailEvent;
use App\Event\ForgetEmailEvent;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthenticationService
{
    private $managerRegistry;
    private $passwordHasher;
    private $tokenGen;
    private $eventDispatcher;
    private $requestStack;
    private $mailer;

    private $userRepo;

    public function __construct(
        ManagerRegistry $managerRegistry,
        UserPasswordHasherInterface $passwordHasher,
        TokenGenerator $tokenGen,
        EventDispatcherInterface $eventDispatcher,
        MailerInterface $mailer,
        RequestStack $requestStack,
        UserRepository $userRepo)
    {
        $this->managerRegistry = $managerRegistry;
        $this->passwordHasher = $passwordHasher;
        $this->tokenGen = $tokenGen;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->mailer = $mailer;
        $this->userRepo = $userRepo;
    }

    /**
     * Create User
     */
    public function newUser(): User
    {
        return new User();
    }

    /**
     * Register user
     */
    public function registerUser(User $user)
    {

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );

        // Update with hashed password
        $user->setPassword($hashedPassword);

        // Add current timestamp
        $dateTime = new \DateTime();
        $user->setRegistrationDate($dateTime->setTimestamp(time()));

        // Add token for confirmation email
        $confirmToken = $this->tokenGen->getToken();
        $user->setRegistrationToken($confirmToken);

        // Doctrine and add user into database
        $entityManager = $this->managerRegistry->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        // Add event to send email
        $event = new ConfirmEmailEvent(
            $user->getEmail(),
            $user->getName(),
            $confirmToken,
            $this->requestStack->getCurrentRequest(),
            $this->mailer
        );
        $this->eventDispatcher->dispatch($event, ConfirmEmailEvent::NAME);
    }

    /**
     * Confirm user in database if found
     */
    public function confirmUser(string $token): bool
    {

        $user = $this->userRepo->findOneBy(['registrationToken' => $token]);

        if($user){
            // User exist
            $user->setRoles(["ROLE_USER", "ROLE_CONFIRMED_USER"]);

            // Delete regristration token
            $user->setRegistrationToken(null);

            $entityManager = $this->managerRegistry->getManager();

            $entityManager->flush();

            return true;
        }

        return false;
    }

    /**
     * Search user by name
     */
    public function searchUsername(string $username): ?User
    {
        return $this->userRepo->findOneBy(['name' => $username]);  
    }

    /**
     * Add forget password to user in db & send confirmation email
     */
    public function sendConfirmationEmail(User $user)
    {
        $forgotToken = $this->tokenGen->getToken();
        $user->setPasswordToken($forgotToken);

        $entityManager = $this->managerRegistry->getManager();
        $entityManager->flush();

        // Add event to send email
        $event = new ForgetEmailEvent(
            $user->getEmail(),
            $user->getName(),
            $forgotToken,
            $this->requestStack->getCurrentRequest(),
            $this->mailer
        );
        $this->eventDispatcher->dispatch($event, ForgetEmailEvent::NAME);
    }

    /**
     * Search username with forget password token
     */
    public function searchUserTokenPass(string $username, string $token): ?User
    {
        return $this->userRepo->findOneBy([
            "name" => $username,
            "passwordToken" => $token
        ]);  
    }

    /**
     * Hash new password and push it into db
     */
    public function renewPassword(User $user, string $password)
    {
        
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $password
        );

        // Update with hashed password
        $user->setPassword($hashedPassword);

        // Delete forget password token
        $user->setPasswordToken(null);

        $entityManager = $this->managerRegistry->getManager();
        $entityManager->flush();
    }

}
