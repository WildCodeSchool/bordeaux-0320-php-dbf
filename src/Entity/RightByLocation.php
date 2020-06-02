<?php

namespace App\Entity;

use App\Repository\RightByLocationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RightByLocationRepository::class)
 */
class RightByLocation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="rightByLocations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="rightByLocations")
     */
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity=Concession::class, inversedBy="rightByLocations")
     */
    private $concession;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="rightByLocations")
     */
    private $city;

    /**
     * @ORM\ManyToOne(targetEntity=Right::class, inversedBy="rightByLocations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $authorization;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getConcession(): ?Concession
    {
        return $this->concession;
    }

    public function setConcession(?Concession $concession): self
    {
        $this->concession = $concession;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getAuthorization(): ?Right
    {
        return $this->authorization;
    }

    public function setAuthorization(?Right $authorization): self
    {
        $this->authorization = $authorization;

        return $this;
    }
}
