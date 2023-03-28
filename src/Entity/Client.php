<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client extends User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Reservation::class)]
    private Collection $reservation;

    #[ORM\OneToMany(mappedBy: 'client', targetEntity: Template::class, orphanRemoval: true)]
    private Collection $template;

    public function __construct(UserRepository $userRepository)
    {
        $this->reservation = new ArrayCollection();
        $this->template = new ArrayCollection();
        $this->userRepository = $userRepository;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation->add($reservation);
            $reservation->setClient($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservation->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getClient() === $this) {
                $reservation->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Template>
     */
    public function getTemplate(): Collection
    {
        return $this->template;
    }

    public function addTemplate(Template $template): self
    {
        if (!$this->template->contains($template)) {
            $this->template->add($template);
            $template->setClient($this);
        }

        return $this;
    }

    public function removeTemplate(Template $template): self
    {
        if ($this->template->removeElement($template)) {
            // set the owning side to null (unless already changed)
            if ($template->getClient() === $this) {
                $template->setClient(null);
            }
        }

        return $this;
    }
}
