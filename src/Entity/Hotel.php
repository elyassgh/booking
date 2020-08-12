<?php

namespace App\Entity;

use App\Repository\HotelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=HotelRepository::class)
 */
class Hotel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $siteweb;

    /**
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $region;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ville;

    /**
     * @ORM\Column(type="float")
     */
    private $distanceCentre;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbrEtoiles;

    /**
     * @ORM\OneToMany(targetEntity=Admin::class, mappedBy="hotel", orphanRemoval=true)
     */
    private $admins;

    /**
     * @ORM\OneToMany(targetEntity=Chambre::class, mappedBy="hotel", orphanRemoval=true)
     */
    private $chambres;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $recommended;

    /**
     * @ORM\Column(type="string", length=140)
     */
    private $Description;

    public function __construct()
    {
        $this->admins = new ArrayCollection();
        $this->chambres = new ArrayCollection();
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

    public function getSiteweb(): ?string
    {
        return $this->siteweb;
    }

    public function setSiteweb(string $siteweb): self
    {
        $this->siteweb = $siteweb;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getDistanceCentre(): ?float
    {
        return $this->distanceCentre;
    }

    public function setDistanceCentre(float $distanceCentre): self
    {
        $this->distanceCentre = $distanceCentre;

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

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;

        return $this;
    }

    public function getNbrEtoiles(): ?int
    {
        return $this->nbrEtoiles;
    }

    public function setNbrEtoiles(int $nbrEtoiles): self
    {
        $this->nbrEtoiles = $nbrEtoiles;

        return $this;
    }

    /**
     * @return Collection|Admin[]
     */
    public function getAdmins(): Collection
    {
        return $this->admins;
    }

    public function addAdmin(Admin $admin): self
    {
        if (!$this->admins->contains($admin)) {
            $this->admins[] = $admin;
            $admin->setHotel($this);
        }

        return $this;
    }

    public function removeAdmin(Admin $admin): self
    {
        if ($this->admins->contains($admin)) {
            $this->admins->removeElement($admin);
            // set the owning side to null (unless already changed)
            if ($admin->getHotel() === $this) {
                $admin->setHotel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Chambre[]
     */
    public function getChambres(): Collection
    {
        return $this->chambres;
    }

    public function addChambre(Chambre $chambre): self
    {
        if (!$this->chambres->contains($chambre)) {
            $this->chambres[] = $chambre;
            $chambre->setHotel($this);
        }

        return $this;
    }

    public function removeChambre(Chambre $chambre): self
    {
        if ($this->chambres->contains($chambre)) {
            $this->chambres->removeElement($chambre);
            // set the owning side to null (unless already changed)
            if ($chambre->getHotel() === $this) {
                $chambre->setHotel(null);
            }
        }

        return $this;
    }

    public function getRecommended(): ?bool
    {
        return $this->recommended;
    }

    public function setRecommended(?bool $recommended): self
    {
        $this->recommended = $recommended;

        return $this;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function __toString() {
        return strval(($this->getNom()));
    }
}
