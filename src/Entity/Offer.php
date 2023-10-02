<?php

namespace App\Entity;

use App\Repository\OfferRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OfferRepository::class)]
class Offer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $description = [];

    #[ORM\Column(nullable: true)]
    private ?string $conditions = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\ManyToOne(inversedBy: 'offers')]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Menu $menu = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFoods(): array
    {
        return $this->description;
    }

    public function setFoods(array $description): self
    {
        $this->description = $description;

        return $this;
    }
    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getMenu(): ?Menu
    {
        return $this->menu;
    }

    public function setMenu(?Menu $menu): self
    {
        $this->menu = $menu;

        return $this;
    }

    /**
     * Get the value of conditions
     */ 
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * Set the value of conditions
     *
     * @return  self
     */ 
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }
}
