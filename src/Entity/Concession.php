<?php

namespace App\Entity;

use App\Repository\ConcessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ConcessionRepository::class)
 */
class Concession
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Veuillez remplir le Nom de la concession avant de valider")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez remplir l'adresse de la concession avant de valider")
     */
    private $address;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Veuillez remplir le code postal avant de valider")
     */
    private $postcode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez mettre la ville avant de valider")
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez mettre une marque de voiture avant de valider")
     */
    private $brand;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez mettre un numéro avant de valider")
     */
    private $phone;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="concessions")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Veuillez selectionner une plaque avant de valider")
     */
    private $town;

    /**
     * @ORM\OneToMany(targetEntity=Service::class, mappedBy="concession", orphanRemoval=true)
     */
    private $services;

    /**
     * @ORM\OneToMany(targetEntity=RightByLocation::class, mappedBy="concession")
     */
    private $rightByLocations;

    /**
     * @ORM\OneToMany(targetEntity=ConcessionHead::class, mappedBy="concession")
     */
    private $concessionHeads;

    public function __construct()
    {
        $this->services = new ArrayCollection();
        $this->rightByLocations = new ArrayCollection();
        $this->concessionHeads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPostcode(): ?int
    {
        return $this->postcode;
    }

    public function setPostcode(int $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getTown(): ?City
    {
        return $this->town;
    }

    public function setTown(?City $town): self
    {
        $this->town = $town;

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
            $service->setConcession($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            // set the owning side to null (unless already changed)
            if ($service->getConcession() === $this) {
                $service->setConcession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RightByLocation[]
     */
    public function getRightByLocations(): Collection
    {
        return $this->rightByLocations;
    }

    public function addRightByLocation(RightByLocation $rightByLocation): self
    {
        if (!$this->rightByLocations->contains($rightByLocation)) {
            $this->rightByLocations[] = $rightByLocation;
            $rightByLocation->setConcession($this);
        }

        return $this;
    }

    public function removeRightByLocation(RightByLocation $rightByLocation): self
    {
        if ($this->rightByLocations->contains($rightByLocation)) {
            $this->rightByLocations->removeElement($rightByLocation);
            // set the owning side to null (unless already changed)
            if ($rightByLocation->getConcession() === $this) {
                $rightByLocation->setConcession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ConcessionHead[]
     */
    public function getConcessionHeads(): Collection
    {
        return $this->concessionHeads;
    }

    public function addConcessionHead(ConcessionHead $concessionHead): self
    {
        if (!$this->concessionHeads->contains($concessionHead)) {
            $this->concessionHeads[] = $concessionHead;
            $concessionHead->setConcession($this);
        }

        return $this;
    }

    public function removeConcessionHead(ConcessionHead $concessionHead): self
    {
        if ($this->concessionHeads->contains($concessionHead)) {
            $this->concessionHeads->removeElement($concessionHead);
            // set the owning side to null (unless already changed)
            if ($concessionHead->getConcession() === $this) {
                $concessionHead->setConcession(null);
            }
        }

        return $this;
    }
}
