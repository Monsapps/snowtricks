<?php
/**
 * Trick entity
 */
namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Trick entity store in doctrine 
 * @ORM\Entity(repositoryClass="App\Repository\TrickRepository")
 * @ORM\Table(name="tricks")
 * @UniqueEntity(
 *      fields={"nameTrick"},
 *      errorPath="nameTrick",
 *      message="Trick name exist, update it")
 */
class Trick
{

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(
     *      type="integer",
     *      name="id_trick")
     */
    protected $idTrick;

    /**
     * @ORM\ManyToOne(
     *      targetEntity="App\Entity\TrickType",
     *      inversedBy="tricks",
     *      cascade={"persist"})
     * @ORM\JoinColumn(
     *      name="id_trick_type",
     *      referencedColumnName="id_trick_type")
     */
    protected $trickType;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="name_trick")
     * @Assert\NotBlank(message = "Trick name must be set.")
     */
    protected $nameTrick;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="description_trick",
     *      length=2555)
     * @Assert\NotBlank(message = "Trick description must be set.")
     */
    protected $descriptionTrick;

    /**
     * @ORM\Column(
     *      type="datetime",
     *      name="creation_date_trick")
     * @Assert\Type("\DateTimeInterface")
     */
    protected $creationDateTrick;

    /**
     * @ORM\Column(
     *      type="datetime",
     *      name="modification_date_trick",
     *      nullable=true)
     * @Assert\Type("\DateTimeInterface")
     */
    protected $editDateTrick;

    /**
     * @ORM\Column(
     *      type="string",
     *      name="slug_trick",
     *      unique=true)
     */
    protected $slugTrick;

    /**
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\TrickImage",
     *      mappedBy="trick",
     *      cascade={"persist"})
     */
    protected $images;

    /**
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\TrickMedia",
     *      mappedBy="trick",
     *      cascade={"persist"})
     */
    protected $medias;

    /**
     * @ORM\OneToMany(
     *      targetEntity="App\Entity\Comment",
     *      mappedBy="trick",
     *      cascade={"persist"})
     */
    protected $comments;

    public function __construct()
    {
        $this->images = new ArrayCollection();
        $this->medias = new ArrayCollection();
    }

    public function __toString()
    {
        return (string) $this->getNameTrick();
    }

    public function getIdTrick(): int
    {
        return $this->idTrick;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection|TrickImage[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(TrickImage $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setTrick($this);
        }
        return $this;
    }

    public function removeImage(TrickImage $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getTrick() === $this) {
                $image->setTrick(null);
            }
        }
        return $this;
    }

    //public function setImages(Collection $images): self ??

    /**
     * @return Collection|TrickMedia[]
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(TrickMedia $media): self
    {
        if (!$this->medias->contains($media)) {
            $this->medias[] = $media;
            $media->setTrick($this);
        }
        return $this;
    }

    public function removeMedia(TrickMedia $media): self
    {
        if ($this->medias->contains($media)) {
            $this->medias->removeElement($media);
            // set the owning side to null (unless already changed)
            if ($media->getTrick() === $this) {
                $media->setTrick(null);
            }
        }
        return $this;
    }


    /**
     * @return TrickType
     */
    public function getTrickType(): ?TrickType
    {
        return $this->trickType;
    }

    public function setTrickType(TrickType $trickType): self
    {
        $this->trickType = $trickType;

        return $this;
    }

    public function getNameTrick(): string
    {
        return $this->nameTrick;
    }

    public function setNameTrick(string $nameTrick): self
    {
        $this->nameTrick = $nameTrick;

        return $this;
    }

    public function getDescriptionTrick(): string
    {
        return $this->descriptionTrick;
    }

    public function setDescriptionTrick(string $descriptionTrick): self
    {
        $this->descriptionTrick = $descriptionTrick;

        return $this;
    }

    public function getCreationDateTrick(): DateTime
    {
        return $this->creationDateTrick;
    }

    public function setCreationDateTrick(DateTime $creationDateTrick): self
    {
        $this->creationDateTrick = $creationDateTrick;

        return $this;
    }

    public function getModificationDateTrick(): ?DateTime
    {
        return $this->editDateTrick;
    }

    public function setModificationDateTrick(DateTime $editDateTrick): self
    {
        $this->editDateTrick = $editDateTrick;

        return $this;
    }

    public function getSlugTrick(): string
    {
        return $this->slugTrick;
    }

    public function setSlugTrick(string $slugTrick): self
    {
        $this->slugTrick = $slugTrick;

        return $this;
    }
}
