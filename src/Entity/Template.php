<?php

namespace App\Entity;

use App\Repository\TemplateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TemplateRepository::class)]
class Template
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length:150)]
    private ?string $title = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $place = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $allergies = [];

    #[ORM\ManyToOne(inversedBy: 'template')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPlace(): ?int
    {
        return $this->place;
    }

    public function setPlace(int $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getAllergies(): array
    {
        return $this->allergies;
    }

    public function setAllergies(?array $allergies): self
    {
        $this->allergies = $allergies;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get the value of title
     */ 
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the value of title
     *
     * @return  self
     */ 
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }
}
