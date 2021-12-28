<?php
/**
 * Trick image entity
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Trick image entity store in doctrine
 * @ORM\Entity()
 * @ORM\Table(name="tricks_images")
 */
class TrickImage
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *      type="integer",
     *      name="id_trick_image")
     */
    public $idTrickImage;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="App\Entity\Trick",
     *      inversedBy="images")
     * @ORM\JoinColumn(
     *      name="id_trick",
     *      referencedColumnName="id_trick")
     */
    public $trick;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="path_trick_image")
     */
    public $pathTrickImage;

    /**
     * @ORM\Column(
     *      type="boolean",
     *      name="is_main_trick_image",
     *      options={"default":false},
     *      nullable=true)
     */
    public $isMainTrickImage;

    public function getId(): int
    {
        return $this->idTrickImage;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }

    public function getPathTrickImage(): ?string
    {
        return $this->pathTrickImage;
    }

    public function setPathTrickImage(string $pathTrickImage): self
    {
        $this->pathTrickImage = $pathTrickImage;

        return $this;
    }

    public function getIsMainTrickImage(): ?bool
    {
        return $this->isMainTrickImage;
    }

    public function setIsMainTrickImage(bool $isMainTrickImage): self
    {
        $this->isMainTrickImage = $isMainTrickImage;

        return $this;
    }
}
