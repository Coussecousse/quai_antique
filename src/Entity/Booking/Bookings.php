<?php

namespace App\Booking\Entity;

use App\Repository\BookingsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingsRepository::class)]
class Bookings
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $places = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $allergies = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $allergies_other = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?int $service = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    private ?\DateTimeInterface $scedule = null;

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

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(int $places): self
    {
        $this->places = $places;

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

    public function getAllergiesOther(): ?string
    {
        return $this->allergies_other;
    }

    public function setAllergiesOther(?string $allergies_other): self
    {
        $this->allergies_other = $allergies_other;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getService(): ?int
    {
        return $this->service;
    }

    public function setService(int $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getScedule(): ?\DateTimeInterface
    {
        return $this->scedule;
    }

    public function setScedule(\DateTimeInterface $scedule): self
    {
        $this->scedule = $scedule;

        return $this;
    }
}
