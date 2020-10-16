<?php

namespace App\Entity;

use App\Repository\LivraisonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LivraisonRepository::class)
 */
class Livraison
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity=Commande::class, inversedBy="livraison", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $checked;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $checkfourn;

    /**
     * @ORM\OneToOne(targetEntity=Facture::class, mappedBy="livraison", cascade={"persist", "remove"})
     */
    private $facture;

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

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getChecked(): ?string
    {
        return $this->checked;
    }

    public function setChecked(string $checked): self
    {
        $this->checked = $checked;

        return $this;
    }

    public function getCheckfourn(): ?string
    {
        return $this->checkfourn;
    }

    public function setCheckfourn(string $checkfourn): self
    {
        $this->checkfourn = $checkfourn;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(Facture $facture): self
    {
        $this->facture = $facture;

        // set the owning side of the relation if necessary
        if ($facture->getLivraison() !== $this) {
            $facture->setLivraison($this);
        }

        return $this;
    }
}
