<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(
        message:"Veuillez renseigner un prenom valide",
    )]
    #[ORM\Column(length: 20, unique: true)]
    private ?string $surname = null;

    #[Assert\NotBlank(
        message:"Veuillez renseigner un prenom valide",
    )]
    #[ORM\Column(length: 9, unique: true)]
    private ?string $telephone = null;

    #[ORM\Column(length: 25)]
    private ?string $adresse = null;

    #[ORM\OneToOne(mappedBy: 'compte', targetEntity: Client::class)]
    private ?Client $client = null;


    /**
     * @var Collection<int, Dette>
     */
    #[ORM\OneToMany(targetEntity: Dette::class, mappedBy: 'client')]
    private Collection $dettes;

    #[Assert\Type(type:User::class)]
    
    #[Assert\Valid(groups:["WITH_COMPTE"])]
    #[ORM\OneToOne(targetEntity: self::class, inversedBy: 'compte', cascade: ['persist', 'remove'])]
    private ?self $compte = null;


    public function __construct()
    {
        $this->dettes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSurname(): ?string
    {
        return $this->surname;
    }

    public function setSurname(string $surname): self
    {
        $this->surname = $surname;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    // Getter et Setter pour l'entité Client
    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        if ($client === null && $this->client !== null){
            $this->client->setCompte(null);
        }

        if ($client !== null && $client->getCompte() !== $this){
            $this->client->setCompte(null);
        }

        $this->client = $client;
        return $this;
    }


    /**
     * @return Collection<int, Dette>
     */
    public function getDettes(): Collection
    {
        return $this->dettes;
    }

    public function addDette(Dette $dette): self
    {
        if (!$this->dettes->contains($dette)) {
            $this->dettes->add($dette);
            $dette->setClient($this);
        }

        return $this;
    }

    public function removeDette(Dette $dette): self
    {
        if ($this->dettes->removeElement($dette)) {
            // set the owning side to null (unless already changed)
            if ($dette->getClient() === $this) {
                $dette->setClient(null);
            }
        }

        return $this;
    }

    public function getCompte(): ?self
    {
        return $this->compte;
    }

    public function setCompte(?self $compte): static
    {
        $this->compte = $compte;

        return $this;
    }

    
}
