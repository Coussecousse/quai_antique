<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "Veuillez remplir ce champ.")]
    #[Assert\Length(min: 10, max: 255, minMessage: "Chaîne de caractères trop courte.", maxMessage: "Chaîne de caractères trop longue.")]
    #[Assert\Email(message: "Chaîne de caractères non valide.")]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(message: "Veuillez remplir ce champ.")]
    #[Assert\Regex("/(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/", message: "Le mot de passe doit posséder au minimum 8 caractères, une lettre majuscule, une lettre minuscule et un chiffre.")]
    private ?string $password = null;
    private ?string $confirm = null;
    
    #[ORM\Column(type: "boolean")]
    private $isVerified = false;

    #[ORM\Column(length: 20)]
    private ?string $code = null;
    
    public_html function getId(): ?int
    {
        return $this->id;
    }

    public_html function getEmail(): ?string
    {
        return $this->email;
    }

    public_html function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public_html function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public_html function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public_html function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public_html function getPassword(): string
    {
        return $this->password;
    }

    public_html function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public_html function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * Get the value of isVerified
     */ 
    public_html function getIsVerified()
    {
        return $this->isVerified;
    }

    /**
     * Set the value of isVerified
     *
     * @return  self
     */ 
    public_html function setIsVerified($isVerified)
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * Get the value of code
     */ 
    public_html function getCode()
    {
        return $this->code;
    }

    /**
     * Set the value of code
     *
     * @return  self
     */ 
    public_html function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the value of confirm
     */ 
    public_html function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * Set the value of confirm
     *
     * @return  self
     */ 
    public_html function setConfirm($confirm)
    {
        $this->confirm = $confirm;

        return $this;
    }
}
