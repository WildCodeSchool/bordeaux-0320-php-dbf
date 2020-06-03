<?php

namespace App\Entity;

use App\Repository\CallRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=CallRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="`call`")
 */
class Call
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Vehicle::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $vehicle;

    /**
     * @ORM\ManyToOne(targetEntity=Subject::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="calls")
     */
    private $comment;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isUrgent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isProcessEnded;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAppointmentTaken;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $recallDate;

    /**
     * @ORM\ManyToOne(targetEntity=RecallPeriod::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $recallPeriod;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $freeComment;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $source;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $internet;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isProcessed;

    /**
     * @ORM\OneToMany(targetEntity=CallTransfer::class, mappedBy="referedCall", orphanRemoval=true)
     */
    private $callTransfers;

    /**
     * @ORM\OneToMany(targetEntity=CallProcessing::class, mappedBy="referedCall", orphanRemoval=true)
     */
    private $callProcessings;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="callsToUser")
     */
    private $recipient;

    public function __construct()
    {
        $this->callTransfers = new ArrayCollection();
        $this->callProcessings = new ArrayCollection();
        $this->recipient = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVehicle(): ?Vehicle
    {
        return $this->vehicle;
    }

    public function setVehicle(?Vehicle $vehicle): self
    {
        $this->vehicle = $vehicle;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getIsUrgent(): ?bool
    {
        return $this->isUrgent;
    }

    public function setIsUrgent(bool $isUrgent): self
    {
        $this->isUrgent = $isUrgent;

        return $this;
    }

    public function getIsProcessEnded(): ?bool
    {
        return $this->isProcessEnded;
    }

    public function setIsProcessEnded(bool $isProcessEnded): self
    {
        $this->isProcessEnded = $isProcessEnded;

        return $this;
    }

    public function getIsAppointmentTaken(): ?bool
    {
        return $this->isAppointmentTaken;
    }

    public function setIsAppointmentTaken(bool $isAppointmentTaken): self
    {
        $this->isAppointmentTaken = $isAppointmentTaken;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     * @param \DateTimeInterface $createdAt
     * @return $this
     * @throws Exception
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = new DateTime();
        return $this;
    }

    public function getRecallDate(): ?\DateTimeInterface
    {
        return $this->recallDate;
    }

    public function setRecallDate(\DateTimeInterface $recallDate): self
    {
        $this->recallDate = $recallDate;

        return $this;
    }

    public function getRecallPeriod(): ?RecallPeriod
    {
        return $this->recallPeriod;
    }

    public function setRecallPeriod(?RecallPeriod $recallPeriod): self
    {
        $this->recallDate = $recallPeriod;

        return $this;
    }

    public function getFreeComment(): ?string
    {
        return $this->freeComment;
    }

    public function setFreeComment(?string $freeComment): self
    {
        $this->freeComment = $freeComment;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function getInternet(): ?string
    {
        return $this->internet;
    }

    public function setInternet(?string $internet): self
    {
        $this->internet = $internet;

        return $this;
    }

    public function getIsProcessed(): ?bool
    {
        return $this->isProcessed;
    }

    public function setIsProcessed(bool $isProcessed): self
    {
        $this->isProcessed = $isProcessed;

        return $this;
    }

    /**
     * @return Collection|CallTransfer[]
     */
    public function getCallTransfers(): Collection
    {
        return $this->callTransfers;
    }

    public function addCallTransfer(CallTransfer $callTransfer): self
    {
        if (!$this->callTransfers->contains($callTransfer)) {
            $this->callTransfers[] = $callTransfer;
            $callTransfer->setReferedCall($this);
        }

        return $this;
    }

    public function removeCallTransfer(CallTransfer $callTransfer): self
    {
        if ($this->callTransfers->contains($callTransfer)) {
            $this->callTransfers->removeElement($callTransfer);
            // set the owning side to null (unless already changed)
            if ($callTransfer->getReferedCall() === $this) {
                $callTransfer->setReferedCall(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|CallProcessing[]
     */
    public function getCallProcessings(): Collection
    {
        return $this->callProcessings;
    }

    public function addCallProcessing(CallProcessing $callProcessing): self
    {
        if (!$this->callProcessings->contains($callProcessing)) {
            $this->callProcessings[] = $callProcessing;
            $callProcessing->setReferedCall($this);
        }

        return $this;
    }

    public function removeCallProcessing(CallProcessing $callProcessing): self
    {
        if ($this->callProcessings->contains($callProcessing)) {
            $this->callProcessings->removeElement($callProcessing);
            // set the owning side to null (unless already changed)
            if ($callProcessing->getReferedCall() === $this) {
                $callProcessing->setReferedCall(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getRecipient(): Collection
    {
        return $this->recipient;
    }

    public function addRecipient(User $recipient): self
    {
        if (!$this->recipient->contains($recipient)) {
            $this->recipient[] = $recipient;
        }

        return $this;
    }

    public function removeRecipient(User $recipient): self
    {
        if ($this->recipient->contains($recipient)) {
            $this->recipient->removeElement($recipient);
        }

        return $this;
    }
}
