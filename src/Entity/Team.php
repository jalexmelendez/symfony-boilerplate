<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\TeamRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ApiResource]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 500)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'teams')]
    private ?User $owner = null;

    #[ORM\OneToMany(mappedBy: 'team', targetEntity: TeamMembership::class,  cascade: ['persist'])]
    private Collection $teamMemberships;

    public function __construct()
    {
        $this->teamMemberships = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, TeamMembership>
     */
    public function getTeamMemberships(): Collection
    {
        return $this->teamMemberships;
    }

    public function addTeamMembership(TeamMembership $teamMembership): self
    {
        if (!$this->teamMemberships->contains($teamMembership)) {
            $this->teamMemberships->add($teamMembership);
            $teamMembership->setTeam($this);
        }

        return $this;
    }

    public function removeTeamMembership(TeamMembership $teamMembership): self
    {
        if ($this->teamMemberships->removeElement($teamMembership)) {
            // set the owning side to null (unless already changed)
            if ($teamMembership->getTeam() === $this) {
                $teamMembership->setTeam(null);
            }
        }

        return $this;
    }
}
