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

    #[ORM\OneToMany(mappedBy: 'Client', targetEntity: Booking::class)]
    private Collection $booking;

    #[ORM\OneToMany(mappedBy: 'Client', targetEntity: Template::class, orphanRemoval: true)]
    private Collection $template;

    public function __construct(UserRepository $userRepository)
    {
        $this->booking = new ArrayCollection();
        $this->template = new ArrayCollection();
        $this->userRepository = $userRepository;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBooking(): Collection
    {
        return $this->booking;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->booking->contains($booking)) {
            $this->booking->add($booking);
            $booking->setClient($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->booking->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getClient() === $this) {
                $booking->setClient(null);
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
