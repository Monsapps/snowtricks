<?php
/**
 * Avatar entity 
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Avatar entity store in doctrine
 * @ORM\Entity()
 * @ORM\Table(name="avatars")
 */
class Avatar
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *      type="integer",
     *      name="id_avatar")
     */
    public $idAvatar;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="path_avatar")
     */
    public $pathAvatar;

    /**
     * @ORM\OneToOne(
     *      targetEntity="App\Entity\User",
     *      inversedBy="avatar")
     * @ORM\JoinColumn(
     *      name="id_user",
     *      referencedColumnName="id_user")
     */
    public $user;

    public function getAvatarPath(): ?string
    {
        return $this->pathAvatar;
    }

    public function setAvatarPath(string $pathAvatar): self
    {
        $this->pathAvatar = $pathAvatar;

        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
