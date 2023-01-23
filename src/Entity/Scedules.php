<?php

namespace App\Entity;

use App\Repository\ScedulesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScedulesRepository::class)]
class Scedules
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $day = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $noon_start = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $noon_end = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $evening_start = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $evening_end = null;

    #[ORM\Column]
    private ?int $places = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDay(): ?int
    {
        return $this->day;
    }

    public function setDay(int $day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getNoonStart(): ?\DateTimeInterface
    {
        return $this->noon_start;
    }

    public function setNoonStart(?\DateTimeInterface $noon_start): self
    {
        $this->noon_start = $noon_start;

        return $this;
    }

    public function getNoonEnd(): ?\DateTimeInterface
    {
        return $this->noon_end;
    }

    public function setNoonEnd(?\DateTimeInterface $noon_end): self
    {
        $this->noon_end = $noon_end;

        return $this;
    }

    public function getEveningStart(): ?\DateTimeInterface
    {
        return $this->evening_start;
    }

    public function setEveningStart(?\DateTimeInterface $evening_start): self
    {
        $this->evening_start = $evening_start;

        return $this;
    }

    public function getEveningEnd(): ?\DateTimeInterface
    {
        return $this->evening_end;
    }

    public function setEveningEnd(?\DateTimeInterface $evening_end): self
    {
        $this->evening_end = $evening_end;

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
}
