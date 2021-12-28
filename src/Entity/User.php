<?php
/**
 * User entity
 */
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User entity store in database via Doctrine
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="users")
 * @UniqueEntity(
 *      fields={"email"},
 *      errorPath="email",
 *      message="Email already exist, forgot password?")
 * @UniqueEntity(
 *      fields={"name"},
 *      errorPath="name",
 *      message="Username already exist")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *      type="integer",
     *      name="id_user")
     */
    public $idUser;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="name_user",
     *      unique=true)
     * @Assert\NotBlank(message = "Username must be set.")
     */
    public $name;

    /**
     * @ORM\Column(type="string", name="password_user")
     * @Assert\Length(
     *      min = 6,
     *      minMessage = "Password minimum length : 6 characters")
     * @Assert\NotBlank(message = "Password must be set.")
     */
    public $password;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="email_user",
     *      unique=true)
     * @Assert\NotBlank(message = "Email must be set.")
     * @Assert\Email(message = "You must enter a valid email adress.")
     */
    public $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(
     *      type="datetime",
     *      name="registration_date_user")
     * @Assert\DateTime
     */
    public $registrationDate;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="registration_token_user",
     *      nullable=true)
     */
    public $registrationToken;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="password_token_user",
     *      nullable=true)
     */
    public $passwordToken;

    /**
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\Comment",
     *      mappedBy="user")
     */
    public $comments;

    /**
     * @ORM\OneToOne(
     *      targetEntity="App\Entity\Avatar",
     *      mappedBy="user")
     */
    public $avatar;

    /**
     * Get name of user
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRegistrationDate(): ?DateTime
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(DateTime $date): self
    {
        $this->registrationDate = $date;

        return $this;
    }

    public function getRegistrationToken(): ?string
    {
        return $this->registrationToken;
    }

    public function setRegistrationToken(?string $token): self
    {
        $this->registrationToken = $token;

        return $this;
    }

    public function getPasswordToken(): ?string
    {
        return $this->passwordToken;
    }

    public function setPasswordToken(string $token): self
    {
        $this->passwordToken = $token;
        return $this;
    }

    /**
     * The public representation of the user (e.g. a username, an email address, etc.)
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->name;
    }

    public function getUsername(): string
    {
        return (string) $this->name;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

}
