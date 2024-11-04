<?php

namespace App\Entity;

use App\Repository\DetteRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: DetteRepository::class)]
class Dette
{
    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $date = null; // Type de colonne spécifié

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private ?float $montant = null;

    #[ORM\Column(type: 'float')]
    private ?float $montantVerse = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'dettes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\OneToMany(mappedBy: 'dette', targetEntity: Payment::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $payments;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setDette($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // Si l'élément a été retiré, définir la propriété de la dette à null
            if ($payment->getDette() === $this) {
                $payment->setDette(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMontantVerse(): ?float
    {
        return $this->montantVerse;
    }

    public function setMontantVerse(float $montantVerse): self
    {
        $this->montantVerse = $montantVerse;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }
}
