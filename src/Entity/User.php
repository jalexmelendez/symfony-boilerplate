<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']],
    operations: [
        new Get(
            security: "is_granted('ROLE_USER')",
        ),
        new Post(
            routeName: 'app_api_register'
        ),
        new Put(security: "is_granted('ROLE_USER') or object.owner == user")
    ],
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * The available user roles, feel free to extend this.
     * 
     * @var array<string>
     */
    public const USER_ROLES = [
        'SUPER_ADMIN' => 'ROLE_SUPER_ADMIN',
        'ADMIN' => 'ROLE_ADMIN',
        'STAFF' => 'ROLE_STAFF',
        'USER' => 'ROLE_USER',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['read'])]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: true)]
    #[Groups(['read', 'write'])]
    private $username;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups(['read', 'write'])]
    private $email;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private $name;

    #[ORM\Column(type: 'json')]
    #[Groups(['read', 'write'])]
    private $roles = [];

    #[ORM\Column(type: 'string')]
    #[Groups(['write'])]
    private $password;

    #[ORM\OneToMany(mappedBy: 'owner', targetEntity: Team::class)]
    private Collection $teams;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: TeamMembership::class)]
    private Collection $teamMemberships;

    public function __construct()
    {
        $this->teams = new ArrayCollection();
        $this->teamMemberships = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->email;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): self
    {
        if (!$this->teams->contains($team)) {
            $this->teams->add($team);
            $team->setOwner($this);
        }

        return $this;
    }

    public function removeTeam(Team $team): self
    {
        if ($this->teams->removeElement($team)) {
            // set the owning side to null (unless already changed)
            if ($team->getOwner() === $this) {
                $team->setOwner(null);
            }
        }

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
            $teamMembership->setUser($this);
        }

        return $this;
    }

    public function removeTeamMembership(TeamMembership $teamMembership): self
    {
        if ($this->teamMemberships->removeElement($teamMembership)) {
            // set the owning side to null (unless already changed)
            if ($teamMembership->getUser() === $this) {
                $teamMembership->setUser(null);
            }
        }

        return $this;
    }
}
