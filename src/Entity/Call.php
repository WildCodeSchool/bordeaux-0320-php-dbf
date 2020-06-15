<?php

namespace App\Entity;

use App\Repository\CallRepository;
use \DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="calls",cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Vehicle::class, inversedBy="calls", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $vehicle;

    /**
     * @ORM\ManyToOne(targetEntity=Subject::class, inversedBy="calls", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $subject;

    /**
     * @ORM\ManyToOne(targetEntity=Comment::class, inversedBy="calls", cascade={"persist"})
     */
    private $comment;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isUrgent;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isProcessEnded;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isAppointmentTaken;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="calls", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $service;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="date")
     */
    private $recallDate;

    /**
     * @ORM\Column(type="time")
     */
    private $recallHour;


    /**
     * @ORM\ManyToOne(targetEntity=RecallPeriod::class, inversedBy="calls", cascade={"persist"})
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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isProcessed;

    /**
     * @ORM\OneToMany(targetEntity=CallTransfer::class, mappedBy="referedCall", orphanRemoval=true)
     */
    private $callTransfers;

    /**
     * @ORM\OneToMany(targetEntity=CallProcessing::class, mappedBy="referedCall",
     *     orphanRemoval=true)
     */
    private $callProcessings;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=true)
     */
    private $recipient;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="callsUserCreate")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;


    public function __construct()
    {
        $this->callTransfers = new ArrayCollection();
        $this->callProcessings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService()
    {
        return $this->service;
    }

    public function getConcession()
    {
        if ($this->getService()) {
            return $this->getService()->getConcession();
        }
    }

    public function getCity()
    {
        if ($this->getConcession()) {
            return $this->getConcession()->getTown();
        }
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


    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     * @return $this
     * @throws Exception
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = new DateTime();
        return $this;
    }

    public function getRecallDate(): ?\DateTimeInterface
    {
        return $this->recallDate;
    }

    public function setRecallDate(DateTime $recallDate): self
    {
        $this->recallDate = $recallDate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getRecallHour()
    {
        return $this->recallHour;
    }

    /**
     * @param mixed $recallHour
     */
    public function setRecallHour($recallHour): void
    {
        $this->recallHour = $recallHour;
    }

    public function getRecallPeriod(): ?RecallPeriod
    {
        return $this->recallPeriod;
    }

    public function setRecallPeriod(?RecallPeriod $recallPeriod): self
    {
        $this->recallPeriod = $recallPeriod;

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

    public function getRecipient(): ?User
    {
        return $this->recipient;
    }

    public function setRecipient(?User $recipient): self
    {
        $this->recipient = $recipient;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

}
