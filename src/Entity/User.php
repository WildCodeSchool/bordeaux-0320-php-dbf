<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User
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
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity=Call::class, mappedBy="user")
     */
    private $calls;

    /**
     * @ORM\OneToMany(targetEntity=CallTransfer::class, mappedBy="byWhom")
     */
    private $callTransfersBy;

    /**
     * @ORM\OneToMany(targetEntity=CallTransfer::class, mappedBy="fromWhom")
     */
    private $callTransfersFrom;

    /**
     * @ORM\OneToMany(targetEntity=CallTransfer::class, mappedBy="toWhom")
     */
    private $callTransfersTo;

    /**
     * @ORM\ManyToMany(targetEntity=Call::class, mappedBy="recipient")
     */
    private $callsToUser;

    /**
     * @ORM\OneToMany(targetEntity=RightByLocation::class, mappedBy="user", orphanRemoval=true)
     */
    private $rightByLocations;

    public function __construct()
    {
        $this->calls = new ArrayCollection();
        $this->callTransfersBy = new ArrayCollection();
        $this->callTransfersTo = new ArrayCollection();
        $this->callTransfersFrom = new ArrayCollection();
        $this->callsToUser = new ArrayCollection();
        $this->rightByLocations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

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
            $call->setUser($this);
        }

        return $this;
    }

    public function removeCall(Call $call): self
    {
        if ($this->calls->contains($call)) {
            $this->calls->removeElement($call);
            // set the owning side to null (unless already changed)
            if ($call->getUser() === $this) {
                $call->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CallTransfer[]
     */
    public function getCallTransfersBy(): Collection
    {
        return $this->callTransfersBy;
    }

    /**
     * @return Collection|CallTransfer[]
     */
    public function getCallTransfersTo(): Collection
    {
        return $this->callTransfersTo;
    }

    /**
     * @return Collection|CallTransfer[]
     */
    public function getCallTransfersFrom(): Collection
    {
        return $this->callTransfersFrom;
    }

    ////////////////////
    public function addCallTransferBy(CallTransfer $callTransfer): self
    {
        if (!$this->callTransfersBy->contains($callTransfer)) {
            $this->callTransfersBy[] = $callTransfer;
            $callTransfer->setByWhom($this);
        }

        return $this;
    }

    public function removeCallTransferBy(CallTransfer $callTransfer): self
    {
        if ($this->callTransfersBy->contains($callTransfer)) {
            $this->callTransfersBy->removeElement($callTransfer);
            // set the owning side to null (unless already changed)
            if ($callTransfer->getByWhom() === $this) {
                $callTransfer->setByWhom(null);
            }
        }

        return $this;
    }


    ////////////////////
    public function addCallTransferTo(CallTransfer $callTransfer): self
    {
        if (!$this->callTransfersTo->contains($callTransfer)) {
            $this->callTransfersTo[] = $callTransfer;
            $callTransfer->setToWhom($this);
        }

        return $this;
    }

    public function removeCallTransferTo(CallTransfer $callTransfer): self
    {
        if ($this->callTransfersTo->contains($callTransfer)) {
            $this->callTransfersTo->removeElement($callTransfer);
            // set the owning side to null (unless already changed)
            if ($callTransfer->getToWhom() === $this) {
                $callTransfer->setToWhom(null);
            }
        }

        return $this;
    }

    ////////////////////
    public function addCallTransferFrom(CallTransfer $callTransfer): self
    {
        if (!$this->callTransfersFrom->contains($callTransfer)) {
            $this->callTransfersFrom[] = $callTransfer;
            $callTransfer->setFromWhom($this);
        }

        return $this;
    }

    public function removeCallTransferFrom(CallTransfer $callTransfer): self
    {
        if ($this->callTransfersFrom->contains($callTransfer)) {
            $this->callTransfersFrom->removeElement($callTransfer);
            // set the owning side to null (unless already changed)
            if ($callTransfer->getFromWhom() === $this) {
                $callTransfer->setFromWhom(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Call[]
     */
    public function getCallsToUser(): Collection
    {
        return $this->callsToUser;
    }

    public function addCallsToUser(Call $callsToUser): self
    {
        if (!$this->callsToUser->contains($callsToUser)) {
            $this->callsToUser[] = $callsToUser;
            $callsToUser->addRecipient($this);
        }

        return $this;
    }

    public function removeCallsToUser(Call $callsToUser): self
    {
        if ($this->callsToUser->contains($callsToUser)) {
            $this->callsToUser->removeElement($callsToUser);
            $callsToUser->removeRecipient($this);
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
            $rightByLocation->setUser($this);
        }

        return $this;
    }

    public function removeRightByLocation(RightByLocation $rightByLocation): self
    {
        if ($this->rightByLocations->contains($rightByLocation)) {
            $this->rightByLocations->removeElement($rightByLocation);
            // set the owning side to null (unless already changed)
            if ($rightByLocation->getUser() === $this) {
                $rightByLocation->setUser(null);
            }
        }

        return $this;
    }
}
