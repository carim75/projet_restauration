<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandeRepository::class)
 */
class Commande
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
     * @ORM\OneToMany(targetEntity=Achat::class, mappedBy="commande")
     */
    private $achats;

    /**
     * @ORM\Column(type="float")
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $restaurateur;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="commandefourn")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fournisseur;

    /**
     * @ORM\OneToOne(targetEntity=Livraison::class, mappedBy="commande", cascade={"persist", "remove"})
     */
    private $livraison;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $checked;

    /**
     * @ORM\Column(type="string", length=3)
     */
    private $checkfourn;

    public function __construct()
    {
        $this->achats = new ArrayCollection();
    }

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

    /**
     * @return Collection|Achat[]
     */
    public function getAchats(): Collection
    {
        return $this->achats;
    }

    public function addAchat(Achat $achat): self
    {
        if (!$this->achats->contains($achat)) {
            $this->achats[] = $achat;
            $achat->setCommande($this);
        }

        return $this;
    }

    public function removeAchat(Achat $achat): self
    {
        if ($this->achats->contains($achat)) {
            $this->achats->removeElement($achat);
            // set the owning side to null (unless already changed)
            if ($achat->getCommande() === $this) {
                $achat->setCommande(null);
            }
        }

        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getRestaurateur(): ?Societe
    {
        return $this->restaurateur;
    }

    public function setRestaurateur(?Societe $restaurateur): self
    {
        $this->restaurateur = $restaurateur;

        return $this;
    }

    public function getFournisseur(): ?Societe
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Societe $fournisseur): self
    {
        $this->fournisseur = $fournisseur;

        return $this;
    }

    public function getLivraison(): ?Livraison
    {
        return $this->livraison;
    }

    public function setLivraison(Livraison $livraison): self
    {
        $this->livraison = $livraison;

        // set the owning side of the relation if necessary
        if ($livraison->getCommande() !== $this) {
            $livraison->setCommande($this);
        }

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
}
