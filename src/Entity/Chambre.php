<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ChambreRepository::class)
 */
class Chambre
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(name="numero", type="integer")
     */
    private $numero;

    /**
     * @ORM\Column(type="integer")
     */
    private $etage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $categorie;

    /**
     * @ORM\Column(type="float")
     */
    private $superficie;

    /**
     * @ORM\Column(type="integer")
     */
    private $capacity;

    /**
     * @ORM\ManyToOne(targetEntity=Hotel::class, inversedBy="chambres")
     * @ORM\JoinColumn(nullable=false)
     */
    private $hotel;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="chambre", orphanRemoval=true)
     */
    private $reservations;

    /**
     * @ORM\Column(type="string")
     */
    private $image;

    /**
     * @ORM\OneToOne(targetEntity=PrixSaison::class, mappedBy="chambre", cascade={"persist", "remove"})
     */
    private $prixSaison;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Images::class, mappedBy="chambre", orphanRemoval=true)
     */
    private $images;

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="chambre", orphanRemoval=true)
     */
    private $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEtage(): ?int
    {
        return $this->etage;
    }

    public function setEtage(int $etage): self
    {
        $this->etage = $etage;

        return $this;
    }

    public function getSuperficie(): ?float
    {
        return $this->superficie;
    }

    public function setSuperficie(float $superficie): self
    {
        $this->superficie = $superficie;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): self
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getCapacity(): ?int
    {
        return $this->capacity;
    }

    public function setCapacity(int $capacity): self
    {
        $this->capacity = $capacity;

        return $this;
    }

    public function getHotel(): ?Hotel
    {
        return $this->hotel;
    }

    public function setHotel(?Hotel $hotel): self
    {
        $this->hotel = $hotel;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setChambre($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
            // set the owning side to null (unless already changed)
            if ($reservation->getChambre() === $this) {
                $reservation->setChambre(null);
            }
        }

        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

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

    public function getPrixSaison(): ?PrixSaison
    {
        return $this->prixSaison;
    }

    public function setPrixSaison(PrixSaison $prixSaison): self
    {
        $this->prixSaison = $prixSaison;

        // set the owning side of the relation if necessary
        if ($prixSaison->getChambre() !== $this) {
            $prixSaison->setChambre($this);
        }

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Images[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setChambre($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getChambre() === $this) {
                $image->setChambre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->setChambre($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            // set the owning side to null (unless already changed)
            if ($service->getChambre() === $this) {
                $service->setChambre(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return strval(($this->numero));
    }

}
