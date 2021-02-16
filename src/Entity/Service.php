<?php

namespace App\Entity;

use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
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
     *  @Assert\NotBlank(message="Veuillez remplir le Nom du service avant de valider")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=Concession::class, inversedBy="services")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Veuillez sélectionner une concession avant de valider")
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

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="service")
     */
    private $users;

    /**
     * @ORM\OneToMany(targetEntity=ServiceHead::class, mappedBy="service", orphanRemoval=true)
     */
    private $serviceHeads;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $brand = 'Audi';

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isCarBodyWorkshop;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isDirection;

    /**
     * @ORM\OneToMany(targetEntity=DbfContact::class, mappedBy="service")
     */
    private $dbfContacts;

    public function __construct()
    {
        $this->calls = new ArrayCollection();
        $this->rightByLocations = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->serviceHeads = new ArrayCollection();
        $this->dbfContacts = new ArrayCollection();
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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setService($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
            // set the owning side to null (unless already changed)
            if ($user->getService() === $this) {
                $user->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ServiceHead[]
     */
    public function getServiceHeads(): Collection
    {
        return $this->serviceHeads;
    }

    public function addServiceHead(ServiceHead $serviceHead): self
    {
        if (!$this->serviceHeads->contains($serviceHead)) {
            $this->serviceHeads[] = $serviceHead;
            $serviceHead->setService($this);
        }

        return $this;
    }

    public function removeServiceHead(ServiceHead $serviceHead): self
    {
        if ($this->serviceHeads->contains($serviceHead)) {
            $this->serviceHeads->removeElement($serviceHead);
            // set the owning side to null (unless already changed)
            if ($serviceHead->getService() === $this) {
                $serviceHead->setService(null);
            }
        }

        return $this;
    }


    /**
     * @return string
     */
    public function getConcessionAndCityFromService(): string
    {
        $concession = $this->getConcession();
        $city = $concession->getTown();
        return $city->getName() . ' > ' . $concession->getName() . ' > ' . $this->getName();
    }

    public function isServiceHead(User $user): bool
    {
        foreach ($this->getServiceHeads() as $head) {
            if ($head->getUser() === $user) {
                return true;
            }
        }
        return false;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {

        $this->brand = $brand;

        return $this;
    }

    public function getIsCarBodyWorkshop(): ?bool
    {
        return $this->isCarBodyWorkshop;
    }

    public function setIsCarBodyWorkshop(?bool $isCarBodyWorkshop): self
    {
        $this->isCarBodyWorkshop = $isCarBodyWorkshop;

        return $this;
    }

    public function getIsDirection(): ?bool
    {
        return $this->isDirection;
    }

    public function setIsDirection(?bool $isDirection): self
    {
        $this->isDirection = $isDirection;

        return $this;
    }

    /**
     * @return Collection|DbfContact[]
     */
    public function getDbfContacts(): Collection
    {
        return $this->dbfContacts;
    }

    public function addDbfContact(DbfContact $dbfContact): self
    {
        if (!$this->dbfContacts->contains($dbfContact)) {
            $this->dbfContacts[] = $dbfContact;
            $dbfContact->setService($this);
        }

        return $this;
    }

    public function removeDbfContact(DbfContact $dbfContact): self
    {
        if ($this->dbfContacts->contains($dbfContact)) {
            $this->dbfContacts->removeElement($dbfContact);
            // set the owning side to null (unless already changed)
            if ($dbfContact->getService() === $this) {
                $dbfContact->setService(null);
            }
        }

        return $this;
    }
}
