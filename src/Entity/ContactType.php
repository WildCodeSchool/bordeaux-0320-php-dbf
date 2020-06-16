<?php

namespace App\Entity;

use App\Repository\ContactTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContactTypeRepository::class)
 */
class ContactType
{
    const CONTACT      = 'contact';
    const ABANDON      = 'abandon';
    const NOT_ELIGIBLE =  'nl';
    const MSG1         = 'msg1';
    const MSG2         = 'msg2';
    const MSG3         = 'msg3';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $identifier;

    /**
     * @ORM\OneToMany(targetEntity=CallProcessing::class, mappedBy="contactType", orphanRemoval=true)
     */
    private $callProcessings;

    public function __construct()
    {
        $this->callProcessings = new ArrayCollection();
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

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function setIdentifier(?string $identifier): self
    {
        $this->identifier = $identifier;

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
            $callProcessing->setContactType($this);
        }

        return $this;
    }

    public function removeCallProcessing(CallProcessing $callProcessing): self
    {
        if ($this->callProcessings->contains($callProcessing)) {
            $this->callProcessings->removeElement($callProcessing);
            // set the owning side to null (unless already changed)
            if ($callProcessing->getContactType() === $this) {
                $callProcessing->setContactType(null);
            }
        }

        return $this;
    }
}
