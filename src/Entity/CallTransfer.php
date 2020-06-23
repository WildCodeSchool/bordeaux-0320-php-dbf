<?php

namespace App\Entity;

use \DateTime;
use App\Repository\CallTransferRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

/**
 * @ORM\Entity(repositoryClass=CallTransferRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class CallTransfer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $comment;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="callTransfersBy")
     * @ORM\JoinColumn(nullable=false)
     */
    private $byWhom;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="callTransfersFrom")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fromWhom;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="callTransfersTo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $toWhom;

    /**
     * @ORM\ManyToOne(targetEntity=Call::class, inversedBy="callTransfers")
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
     * @throws Exception
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

    public function getByWhom(): ?User
    {
        return $this->byWhom;
    }

    public function setByWhom(?User $byWhom): self
    {
        $this->byWhom = $byWhom;

        return $this;
    }

    public function getFromWhom(): ?User
    {
        return $this->fromWhom;
    }

    public function setFromWhom(?User $fromWhom): self
    {
        $this->fromWhom = $fromWhom;

        return $this;
    }

    public function getToWhom(): ?User
    {
        return $this->toWhom;
    }

    public function setToWhom(?User $toWhom): self
    {
        $this->toWhom = $toWhom;

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
}
