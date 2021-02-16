<?php

namespace App\Entity;

use App\Repository\DbfContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DbfContactRepository::class)
 */
class DbfContact
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Civility::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $civility;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $immat;

    /**
     * @ORM\Column(type="date")
     */
    private $date;

    /**
     * @ORM\Column(type="integer")
     */
    private $hour;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $minute;

    /**
     * @ORM\ManyToOne(targetEntity=Service::class, inversedBy="dbfContacts")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank
     */
    private $service;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCivility(): ?Civility
    {
        return $this->civility;
    }

    public function setCivility(?Civility $civility): self
    {
        $this->civility = $civility;

        return $this;
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

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getImmat(): ?string
    {
        return $this->immat;
    }

    public function setImmat(string $immat): self
    {
        $this->immat = $immat;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getHour(): ?int
    {
        return $this->hour;
    }

    public function setHour(int $hour): self
    {
        $this->hour = $hour;

        return $this;
    }

    public function getMinute(): ?string
    {
        return $this->minute;
    }

    public function setMinute(string $minute): self
    {
        $this->minute = $minute;

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

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }
}
