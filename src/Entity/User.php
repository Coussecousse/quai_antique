<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[Assert\NotBlank(message : "Veuillez remplir ce champ.")]
    #[Assert\Length(min : 10, max: 255, minMessage: "Chaîne de caractères trop courte.", maxMessage: "Chaîne de caractères trop longue.")]
    #[Assert\Email(message : "Chaîne de caractères non valide.")]
    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    #[ORM\Column(length: 72)]
    #[Assert\NotBlank(message : "Veuillez remplir ce champ.")]
    #[Assert\Regex(pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/', message: "Le mot de passe doit posséder au minimum 8 caractères, une lettre majuscule, une lettre minuscule et un chiffre.")]
    private ?string $password = null;
    private ?string $confirm = null;
    
    #[ORM\Column(type: "boolean")]
    private $isVerified = false;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getRoles(): array
    {
        $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $this->$passwordHasher->hashPassword($this, $password);

        return $this;
    }

    /**
     * Get the value of confirm
     */ 
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * Set the value of confirm
     *
     * @return  self
     */ 
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;

        return $this;
    }

    /**
     * Get the value of isVerified
     */ 
    public function getIsVerified(): bool
    {
        return $this->isVerified;
    }

    /**
     * Set the value of isVerified
     *
     * @return  self
     */ 
    public function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }
}
