<?php
/**
 * Trick media entity
 */
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trick media entity store in doctrine
 * @ORM\Entity()
 * @ORM\Table(name="tricks_medias")
 */
class TrickMedia
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *      type="integer",
     *      name="id_trick_media")
     */
    public $idTrickMedia;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="App\Entity\Trick",
     *      inversedBy="medias")
     * @ORM\JoinColumn(
     *      name="id_trick",
     *      referencedColumnName="id_trick")
     */
    public $trick;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="path_trick_media")
     */
    public $urlTrickMedia;

    public function getId(): int
    {
        return $this->idTrickMedia;
    }

    public function getUrlTrickMedia(): ?string
    {
        return $this->urlTrickMedia;
    }

    public function setUrlTrickMedia(string $urlTrickMedia): self
    {
        $this->urlTrickMedia = $urlTrickMedia;

        return $this;
    }

    /**
     * Get and set trick entity to media
     */
    public function getTrick(): Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }


}
