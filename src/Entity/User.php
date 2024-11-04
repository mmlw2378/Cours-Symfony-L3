<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_LOGIN', fields: ['login'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(
        message:"Veuillez renseigner un login valide",
        groups:['WITH_COMPTE']
    )]
    #[ORM\Column(length: 180)]
    private ?string $login = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = []; // Initialize as an empty array

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank(
        message:"Veuillez renseigner un mot de passe valide",
        groups:['WITH_COMPTE']
    )]
    #[ORM\Column]
    private ?string $password = null;

    #[Assert\NotBlank(
        message:"Veuillez renseigner un nom valide",
        groups:['WITH_COMPTE']
    )]
    #[ORM\Column(length: 35)]
    private ?string $nom = null;

    #[Assert\NotBlank(
        message:"Veuillez renseigner un prenom valide",
        groups:['WITH_COMPTE']
    )]
    #[ORM\Column(length: 24)]
    private ?string $prenom = null;

    #[ORM\OneToOne(inversedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Client $client = null;

    #[ORM\Column]
    private ?bool $isBlocked = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updateAt = null;

    public function __construct()
    {
        $this->createAt = new \DateTimeImmutable();
        $this->updateAt = new \DateTimeImmutable();
        $this->isBlocked = false;
        $this->roles = []; // Ensure roles are initialized
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): static
    {
        $this->login = $login;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        // Ensure every user has at least ROLE_USER
        $roles = $this->roles;
        $roles[] = 'ROLE_USER'; // Add default role if not already present

        return array_unique($this->roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUser Interface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function isBlocked(): ?bool
    {
        return $this->isBlocked;
    }

    public function setBlocked(bool $isBlocked): static
    {
        $this->isBlocked = $isBlocked;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }
}