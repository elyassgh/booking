<?php

namespace App\Entity;

use App\Repository\PrixSaisonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PrixSaisonRepository::class)
 */
class PrixSaison
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Chambre::class, inversedBy="prixSaison", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $chambre;

    /**
     * @ORM\Column(type="float")
     */
    private $prix;

    /**
     * @ORM\Column(type="float",options={"default":1})
     */
    private $taux;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChambre(): ?Chambre
    {
        return $this->chambre;
    }

    public function setChambre(Chambre $chambre): self
    {
        $this->chambre = $chambre;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getTaux(): ?float
    {
        return $this->taux;
    }

    public function setTaux(float $taux): self
    {
        $this->taux = $taux;

        return $this;
    }

}
