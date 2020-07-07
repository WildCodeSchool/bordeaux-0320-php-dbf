<?php

namespace App\Entity;

use \DateTime;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */

class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;


    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $phone;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;
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
     * @ORM\OneToMany(targetEntity=RightByLocation::class, mappedBy="user", orphanRemoval=true)
     */
    private $rightByLocations;

    /**
     * @ORM\OneToMany(targetEntity=Call::class, mappedBy="recipient")
     */
    private $calls;

    /**
     * @ORM\OneToMany(targetEntity=Call::class, mappedBy="author")
     */
    private $callsUserCreate;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=true)
     */
    private $service;

    /**
     * @ORM\OneToMany(targetEntity=ServiceHead::class, mappedBy="user", orphanRemoval=true)
     */
    private $serviceHeads;

    public function __construct()
    {
        $this->calls = new ArrayCollection();
        $this->callTransfersBy = new ArrayCollection();
        $this->callTransfersTo = new ArrayCollection();
        $this->callTransfersFrom = new ArrayCollection();
        $this->rightByLocations = new ArrayCollection();
        $this->callsUserCreate = new ArrayCollection();
        $this->serviceHeads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFullName()
    {
        return $this->firstname . ' ' . $this->lastname;
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

    /**
     * @param \DateTimeInterface|null $updatedAt
     * @return User
     */
    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @ORM\PreUpdate
     */
    public function setUpdatedAtPrePersist()
    {
        $this->updatedAt = new DateTime('Europe/Paris');

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
            $call->setRecipient($this);
        }

        return $this;
    }

    public function removeCall(Call $call): self
    {
        if ($this->calls->contains($call)) {
            $this->calls->removeElement($call);
            // set the owning side to null (unless already changed)
            if ($call->getRecipient() === $this) {
                $call->setRecipient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Call[]
     */
    public function getCallsUserCreate(): Collection
    {
        return $this->callsUserCreate;
    }

    public function addCallsUserCreate(Call $callsUserCreate): self
    {
        if (!$this->callsUserCreate->contains($callsUserCreate)) {
            $this->callsUserCreate[] = $callsUserCreate;
            $callsUserCreate->setAuthor($this);
        }

        return $this;
    }

    public function removeCallsUserCreate(Call $callsUserCreate): self
    {
        if ($this->callsUserCreate->contains($callsUserCreate)) {
            $this->callsUserCreate->removeElement($callsUserCreate);
            // set the owning side to null (unless already changed)
            if ($callsUserCreate->getAuthor() === $this) {
                $callsUserCreate->setAuthor(null);
            }
        }

        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array("ROLE_ADMIN", $this->roles);
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
            $serviceHead->setUser($this);
        }

        return $this;
    }

    public function removeServiceHead(ServiceHead $serviceHead): self
    {
        if ($this->serviceHeads->contains($serviceHead)) {
            $this->serviceHeads->removeElement($serviceHead);
            // set the owning side to null (unless already changed)
            if ($serviceHead->getUser() === $this) {
                $serviceHead->setUser(null);
            }
        }

        return $this;
    }
}
