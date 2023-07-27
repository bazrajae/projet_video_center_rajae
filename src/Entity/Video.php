<?php

namespace App\Entity;
use App\Entity\Traits\Timestampable;
use App\Repository\VideoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\Table(name: "videos")]
#[ORM\HasLifecycleCallbacks]
#[Vich\Uploadable]

class Video
{
    use Timestampable;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Veuillez entrer un titre")]
    #[Assert\Length(min: 3, minMessage: "Vous devez avoir un titre de minimum 3 caractères")]
    #[Assert\NotIdenticalTo(value:"merde")]
    private ?string $title = null;

    #[ORM\Column(length: 500)]
    private ?string $videoLink = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "Veuillez entrer une discription")]
    #[Assert\Length(min: 20, minMessage: "Vous devez avoir un discription de minimum 20 caractères")]
    #[Assert\NotIdenticalTo(value:"wesh")]
    private ?string $description = null;



    public function __construct()
    {

        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column]
    private ?bool $isPremiumvideo = null;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getVideoLink(): ?string
    {
        return $this->videoLink;
    }

    public function setVideoLink(string $videoLink): static
    {
        $this->videoLink = $videoLink;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function isIsPremiumvideo(): ?bool
    {
        return $this->isPremiumvideo;
    }

    public function setIsPremiumvideo(bool $isPremiumvideo): static
    {
        $this->isPremiumvideo = $isPremiumvideo;

        return $this;
    }

}
   

