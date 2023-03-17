<?php

namespace App\Entity;

use App\Repository\DateRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DateRepository::class)]
class Date
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $noon__start = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $noon_end = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $evening_start = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $evening_end = null;

    #[ORM\Column]
    private ?bool $evening_close = false;

    #[ORM\Column]
    private ?bool $noon_close = false;

    #[ORM\Column]
    private ?bool $evening_normal = false;

    #[ORM\Column]
    private ?bool $noon_normal = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNoonStart(): ?\DateTimeInterface
    {
        return $this->noon__start;
    }

    public function setNoonStart(?\DateTimeInterface $noon__start): self
    {
        $this->noon__start = $noon__start;

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

    /**
     * Get the value of evening_close
     */ 
    public function getEvening_close()
    {
        return $this->evening_close;
    }

    /**
     * Set the value of evening_close
     *
     * @return  self
     */ 
    public function setEvening_close($evening_close)
    {
        $this->evening_close = $evening_close;

        return $this;
    }

    /**
     * Get the value of noon_close
     */ 
    public function getNoon_close()
    {
        return $this->noon_close;
    }

    /**
     * Set the value of noon_close
     *
     * @return  self
     */ 
    public function setNoon_close($noon_close)
    {
        $this->noon_close = $noon_close;

        return $this;
    }

    /**
     * Get the value of evening_normal
     */ 
    public function getEvening_normal()
    {
        return $this->evening_normal;
    }

    /**
     * Set the value of evening_normal
     *
     * @return  self
     */ 
    public function setEvening_normal($evening_normal)
    {
        $this->evening_normal = $evening_normal;

        return $this;
    }

    /**
     * Get the value of noon_normal
     */ 
    public function getNoon_normal()
    {
        return $this->noon_normal;
    }

    /**
     * Set the value of noon_normal
     *
     * @return  self
     */ 
    public function setNoon_normal($noon_normal)
    {
        $this->noon_normal = $noon_normal;

        return $this;
    }
}
