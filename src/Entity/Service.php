<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ServiceRepository::class)
 */
class Service
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Concession::class, inversedBy="services")
     * @ORM\JoinColumn(nullable=false)
     */
    private $concession;

    /**
     * @ORM\OneToMany(targetEntity=Call::class, mappedBy="service", orphanRemoval=true)
     */
    private $calls;

    /**
     * @ORM\OneToMany(targetEntity=RightByLocation::class, mappedBy="service")
     */
    private $rightByLocations;

    public function __construct()
    {
        $this->calls = new ArrayCollection();
        $this->rightByLocations = new ArrayCollection();
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

    public function getConcession(): ?Concession
    {
        return $this->concession;
    }

    public function setConcession(?Concession $concession): self
    {
        $this->concession = $concession;

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
            $call->setService($this);
        }

        return $this;
    }

    public function removeCall(Call $call): self
    {
        if ($this->calls->contains($call)) {
            $this->calls->removeElement($call);
            // set the owning side to null (unless already changed)
            if ($call->getService() === $this) {
                $call->setService(null);
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
            $rightByLocation->setService($this);
        }

        return $this;
    }

    public function removeRightByLocation(RightByLocation $rightByLocation): self
    {
        if ($this->rightByLocations->contains($rightByLocation)) {
            $this->rightByLocations->removeElement($rightByLocation);
            // set the owning side to null (unless already changed)
            if ($rightByLocation->getService() === $this) {
                $rightByLocation->setService(null);
            }
        }

        return $this;
    }
}
