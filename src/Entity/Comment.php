<?php
/**
 * Comment entity
 */
namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment entity store in doctrine
 * @ORM\Entity()
 * @ORM\Table(name="comments")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *      type="integer",
     *      name="id_comment")
     */
    public $idComment;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="App\Entity\Trick",
     *      inversedBy="comments")
     * @ORM\JoinColumn(
     *      name="id_trick",
     *      referencedColumnName="id_trick")
     */
    public $trick;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="App\Entity\User",
     *      inversedBy="comments")
     * @ORM\JoinColumn(
     *      name="id_user",
     *      referencedColumnName="id_user")
     */
    public $user;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="content_comment")
     * @Assert\NotBlank(message = "Content comment cannot be empty.")
     */
    public $contentComment;

    /**
     * @ORM\Column(
     *      type="datetime",
     *      name="date_comment")
     * @Assert\DateTime
     */
    public $dateComment;

    public function getTrick(): Trick
    {
        return $this->trick;
    }

    public function setTrick(Trick $trick): self
    {
        $this->trick = $trick;

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

    public function getContentComment(): string
    {
        return $this->contentComment;
    }

    public function setContentComment(string $contentComment): self
    {
        $this->contentComment = $contentComment;

        return $this;
    }

    public function getDateComment(): DateTime
    {
        return $this->dateComment;
    }

    public function setDateComment(DateTime $dateComment): self
    {
        $this->dateComment = $dateComment;

        return $this;
    }

}
