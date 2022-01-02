<?php
/**
 * Trick type entity
 */
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Trick type entity store in doctrine 
 * @ORM\Entity()
 * @ORM\Table(name="tricks_type")
 * @UniqueEntity(
 *      fields={"name_trick_type"},
 *      errorPath="name_trick_type",
 *      message="Trick type name exist, update it")
 */
class TrickType
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *      type="integer",
     *      name="id_trick_type")
     */
    public $idTrickType;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="name_trick_type",
     *      unique=true)
     * @Assert\NotBlank(message = "Trick type name must be set.")
     */
    public $nameTrickType;

    /**
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\Trick",
     *      mappedBy="trickType")
     */
    private $tricks;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nameTrickType;
    }

    public function getId(): int
    {
        return $this->idTrickType;
    }
    
    /**
     * Get trick type name
     * @return string
     */
    public function getName(): ?string
    {
        return $this->nameTrickType;
    }

    public function setName(string $nameTrickType): self
    {
        $this->nameTrickType = $nameTrickType;

        return $this;
    }

    /**
     * Get all tricks by trick type
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }
}
