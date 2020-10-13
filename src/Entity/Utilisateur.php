<?php

namespace App\Entity;

use App\Repository\UtilisateurRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=UtilisateurRepository::class)
 */
class Utilisateur implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @Assert\NotBlank(message="Veuillez indiquer votre prénom et nom")
     */
    private $nomComplet;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $fonction;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $role= 'ROLE_USER';

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @var string|null
     *
     * @Assert\NotBlank(message="Le mot de passe est obligatoire")
     * @Assert\Regex("/^[a-zA-Z0-9]{6,20}$/", message="Mot de passe non conforme")
     *
     */
    private $plainMdp;

    /**
     * @ORM\ManyToOne(targetEntity=Societe::class, inversedBy="utilisateurs")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Societe;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getNomComplet(): ?string
    {
        return $this->nomComplet;
    }

    public function setNomComplet(string $nomComplet): self
    {
        $this->nomComplet = $nomComplet;

        return $this;
    }

    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainMdp(): ?string
    {
        return $this->plainMdp;
    }

    /**
     * @param string|null $plainMdp
     * @return Utilisateur
     */
    public function setPlainMdp(?string $plainMdp): Utilisateur
    {
        $this->plainMdp = $plainMdp;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return [$this->role];
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {

    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {

        return $this->pseudo;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {

    }

    public function getSociete(): ?Societe
    {
        return $this->Societe;
    }

    public function setSociete(?Societe $Societe): self
    {
        $this->Societe = $Societe;

        return $this;
    }

}
