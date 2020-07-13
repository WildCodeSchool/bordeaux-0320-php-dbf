<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CityRepository::class)
 */
class City
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=155)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Concession::class, mappedBy="town", orphanRemoval=true)
     */
    private $concessions;

    /**
     * @ORM\OneToMany(targetEntity=Subject::class, mappedBy="city")
     */
    private $subjects;

    /**
     * @ORM\OneToMany(targetEntity=RightByLocation::class, mappedBy="city")
     */
    private $rightByLocations;

    /**
     * @ORM\OneToMany(targetEntity=CityHead::class, mappedBy="city")
     */
    private $cityHeads;

    public function __construct()
    {
        $this->concessions = new ArrayCollection();
        $this->subjects = new ArrayCollection();
        $this->rightByLocations = new ArrayCollection();
        $this->cityHeads = new ArrayCollection();
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

    /**
     * @return Collection|Concession[]
     */
    public function getConcessions(): Collection
    {
        return $this->concessions;
    }

    public function addConcession(Concession $concession): self
    {
        if (!$this->concessions->contains($concession)) {
            $this->concessions[] = $concession;
            $concession->setTown($this);
        }

        return $this;
    }

    public function removeConcession(Concession $concession): self
    {
        if ($this->concessions->contains($concession)) {
            $this->concessions->removeElement($concession);
            // set the owning side to null (unless already changed)
            if ($concession->getTown() === $this) {
                $concession->setTown(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Subject[]
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
            $subject->setCity($this);
        }

        return $this;
    }

    public function removeSubject(Subject $subject): self
    {
        if ($this->subjects->contains($subject)) {
            $this->subjects->removeElement($subject);
            // set the owning side to null (unless already changed)
            if ($subject->getCity() === $this) {
                $subject->setCity(null);
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
            $rightByLocation->setCity($this);
        }

        return $this;
    }

    public function removeRightByLocation(RightByLocation $rightByLocation): self
    {
        if ($this->rightByLocations->contains($rightByLocation)) {
            $this->rightByLocations->removeElement($rightByLocation);
            // set the owning side to null (unless already changed)
            if ($rightByLocation->getCity() === $this) {
                $rightByLocation->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CityHead[]
     */
    public function getCityHeads(): Collection
    {
        return $this->cityHeads;
    }

    public function addCityHead(CityHead $cityHead): self
    {
        if (!$this->cityHeads->contains($cityHead)) {
            $this->cityHeads[] = $cityHead;
            $cityHead->setCity($this);
        }

        return $this;
    }

    public function removeCityHead(CityHead $cityHead): self
    {
        if ($this->cityHeads->contains($cityHead)) {
            $this->cityHeads->removeElement($cityHead);
            // set the owning side to null (unless already changed)
            if ($cityHead->getCity() === $this) {
                $cityHead->setCity(null);
            }
        }

        return $this;
    }

    public function isPhoneCity(): bool
    {
        return ($this->getName() === 'Cellule Téléphonique') ? true : false;
    }
}
