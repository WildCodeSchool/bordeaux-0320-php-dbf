<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SubjectRepository::class)
 */
class Subject
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez remplir le Nom du motif avant de valider")
     */
    private $name;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isForAppWorkshop;

    /**
     * @ORM\ManyToOne(targetEntity=City::class, inversedBy="subjects")
     * @Assert\NotBlank(message="Veuillez remplir la plaque avant de valider")
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity=Call::class, mappedBy="subject")
     */
    private $calls;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isHidden;

    public function __construct()
    {
        $this->calls = new ArrayCollection();
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

    public function getIsForAppWorkshop(): ?bool
    {
        return $this->isForAppWorkshop;
    }

    public function setIsForAppWorkshop(bool $isForAppWorkshop): self
    {
        $this->isForAppWorkshop = $isForAppWorkshop;

        return $this;
    }

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): self
    {
        $this->city = $city;

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
            $call->setSubject($this);
        }

        return $this;
    }

    public function removeCall(Call $call): self
    {
        if ($this->calls->contains($call)) {
            $this->calls->removeElement($call);
            // set the owning side to null (unless already changed)
            if ($call->getSubject() === $this) {
                $call->setSubject(null);
            }
        }

        return $this;
    }

    public function getIsHidden(): ?bool
    {
        return $this->isHidden;
    }

    public function setIsHidden(?bool $isHidden): self
    {
        $this->isHidden = $isHidden;

        return $this;
    }
}
