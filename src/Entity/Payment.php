<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    // Ajoutez d'autres propriétés, par exemple, le montant et la date de paiement
    #[ORM\Column(type: 'float')]
    private float $amount;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $paymentDate;

    #[ORM\ManyToOne(targetEntity: Dette::class, inversedBy: 'payments')]
    private ?Dette $dette = null;

    public function getDette(): ?Dette
    {
        return $this->dette;
    }

    public function setDette(?Dette $dette): self
    {
        $this->dette = $dette;

        return $this;
    }

    // Ajoutez les méthodes getter et setter

    public function getId(): int
    {
        return $this->id;
    }


    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    public function getPaymentDate(): \DateTime
    {
        return $this->paymentDate;
    }

    public function setPaymentDate(\DateTime $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }
}
