<?php

namespace App\Entity;

use App\Repository\VehicleRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VehicleRepository::class)
 *  @ORM\HasLifecycleCallbacks()
 */
class Vehicle
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15)
     */
    private $immatriculation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $chassis;

    /**
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    private $hasCome;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="vehicles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity=Call::class, mappedBy="vehicle")
     */
    private $calls;

    public function __construct()
    {
        $this->calls = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImmatriculation(): ?string
    {
        return $this->immatriculation;
    }

    public function setImmatriculation(string $immatriculation): self
    {
        $this->immatriculation = $immatriculation;

        return $this;
    }

    public function getChassis(): ?string
    {
        return $this->chassis;
    }

    public function setChassis(?string $chassis): self
    {
        $this->chassis = $chassis;

        return $this;
    }

    public function getHasCome(): ?bool
    {
        return $this->hasCome;
    }

    public function setHasCome(bool $hasCome): self
    {
        $this->hasCome = $hasCome;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return $this
     * @throws \Exception
     * @ORM\PrePersist()
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = new DateTime();
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Collection|Call[]
     */
    public function getCalls(): Collection
    {
        return $this->calls;
    }

    public function addCall(Call $call): self
    {
        if (!$this->calls->contains($call)) {
            $this->calls[] = $call;
            $call->setVehicle($this);
        }

        return $this;
    }

    public function removeCall(Call $call): self
    {
        if ($this->calls->contains($call)) {
            $this->calls->removeElement($call);
            // set the owning side to null (unless already changed)
            if ($call->getVehicle() === $this) {
                $call->setVehicle(null);
            }
        }

        return $this;
    }
}
