<?php

namespace App\Entity;

use App\Repository\SocieteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=SocieteRepository::class)
 */
class Societe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\NotBlank(message="Le nom est manquant")
     * @Assert\Length(max="20", maxMessage="Le nom ne peut contenir plus de 20 caractères.")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\NotBlank(message="Le numéro est manquant")
     * @Assert\Length(max="10", maxMessage="Le numéro ne peut contenir plus de 10 chiffres.")
     */
    private $tel_societe;



    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     * @Assert\Length(max="50", maxMessage="L'email ne peut contenir plus de 50 caractères.")
     */
    private $mail;

    /**
     * @ORM\OneToMany(targetEntity=Produit::class, mappedBy="societe")
     */
    private $produits;


    /**
     * @ORM\OneToMany(targetEntity=Utilisateur::class, mappedBy="societe")
     */
    private $utilisateurs;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="restaurateur")
     */
    private $commandes;

    /**
     * @ORM\OneToMany(targetEntity=Commande::class, mappedBy="fournisseur")
     */
    private $commandefourn;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;




    public function __construct()
    {
        $this->produits = new ArrayCollection();
        $this->utilisateurs = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->commandefourn = new ArrayCollection();

    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getTelSociete(): ?string
    {
        return $this->tel_societe;
    }

    public function setTelSociete(string $tel_societe): self
    {
        $this->tel_societe = $tel_societe;

        return $this;
    }



    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    /**
     * @return Collection|Produit[]
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): self
    {
        if (!$this->produits->contains($produit)) {
            $this->produits[] = $produit;
            $produit->setSociete($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): self
    {
        if ($this->produits->contains($produit)) {
            $this->produits->removeElement($produit);
            // set the owning side to null (unless already changed)
            if ($produit->getSociete() === $this) {
                $produit->setSociete(null);
            }
        }

        return $this;
    }



    /**
     * @return Collection|Utilisateur[]
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
            $utilisateur->setSociete($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->removeElement($utilisateur);
            // set the owning side to null (unless already changed)
            if ($utilisateur->getSociete() === $this) {
                $utilisateur->setSociete(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): self
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes[] = $commande;
            $commande->setRestaurateur($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): self
    {
        if ($this->commandes->contains($commande)) {
            $this->commandes->removeElement($commande);
            // set the owning side to null (unless already changed)
            if ($commande->getRestaurateur() === $this) {
                $commande->setRestaurateur(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Commande[]
     */
    public function getCommandefourn(): Collection
    {
        return $this->commandefourn;
    }

    public function addCommandefourn(Commande $commandefourn): self
    {
        if (!$this->commandefourn->contains($commandefourn)) {
            $this->commandefourn[] = $commandefourn;
            $commandefourn->setFournisseur($this);
        }

        return $this;
    }

    public function removeCommandefourn(Commande $commandefourn): self
    {
        if ($this->commandefourn->contains($commandefourn)) {
            $this->commandefourn->removeElement($commandefourn);
            // set the owning side to null (unless already changed)
            if ($commandefourn->getFournisseur() === $this) {
                $commandefourn->setFournisseur(null);
            }
        }

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






}