<?php

namespace App\Entity;

use App\Repository\CallProcessingRepository;
use Doctrine\ORM\Mapping as ORM;
use \DateTime;
use App\Service\CallTreatmentDataMaker;

/**
 * @ORM\Entity(repositoryClass=CallProcessingRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="`call_processing`")
 */
class CallProcessing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=ContactType::class, inversedBy="callProcessings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contactType;

    /**
     * @ORM\ManyToOne(targetEntity=Call::class, inversedBy="callProcessings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $referedCall;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @ORM\PrePersist
     * @return $this
     */
    public function setCreatedAt(): self
    {
        $this->createdAt = new DateTime('Europe/Paris');
        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }

    public function getContactType(): ?ContactType
    {
        return $this->contactType;
    }

    public function setContactType(?ContactType $contactType): self
    {
        $this->contactType = $contactType;

        return $this;
    }

    public function getReferedCall(): ?Call
    {
        return $this->referedCall;
    }

    public function setReferedCall(?Call $referedCall): self
    {
        $this->referedCall = $referedCall;

        return $this;
    }

    public function getStepColors()
    {
        return CallTreatmentDataMaker::stepMakerForProcess($this);
    }
}
