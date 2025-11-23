<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'UNIQ_EMAIL', columns: ['email'])]
#[ORM\Index(name: 'idx_user_type', columns: ['user_type'])]
#[ORM\Index(name: 'idx_created_at', columns: ['created_at'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $passwordHash = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $userType = null; // 'candidate', 'recruiter', 'admin'

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $emailVerified = false;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastLoginAt = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: CandidateProfile::class, cascade: ['persist', 'remove'])]
    private ?CandidateProfile $candidateProfile = null;

    #[ORM\OneToOne(mappedBy: 'user', targetEntity: RecruiterProfile::class, cascade: ['persist', 'remove'])]
    private ?RecruiterProfile $recruiterProfile = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class, orphanRemoval: true)]
    private Collection $notifications;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: SearchHistory::class, orphanRemoval: true)]
    private Collection $searchHistories;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->notifications = new ArrayCollection();
        $this->searchHistories = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // Getters and Setters

    public function getId(): ?string
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

    public function getPassword(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): self
    {
        $this->passwordHash = $passwordHash;
        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;
        return $this;
    }

    public function isEmailVerified(): bool
    {
        return $this->emailVerified;
    }

    public function setEmailVerified(bool $emailVerified): self
    {
        $this->emailVerified = $emailVerified;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
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

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getLastLoginAt(): ?\DateTimeInterface
    {
        return $this->lastLoginAt;
    }

    public function setLastLoginAt(?\DateTimeInterface $lastLoginAt): self
    {
        $this->lastLoginAt = $lastLoginAt;
        return $this;
    }

    public function getCandidateProfile(): ?CandidateProfile
    {
        return $this->candidateProfile;
    }

    public function setCandidateProfile(?CandidateProfile $candidateProfile): self
    {
        if ($candidateProfile === null && $this->candidateProfile !== null) {
            $this->candidateProfile->setUser(null);
        }

        if ($candidateProfile !== null && $candidateProfile->getUser() !== $this) {
            $candidateProfile->setUser($this);
        }

        $this->candidateProfile = $candidateProfile;
        return $this;
    }

    public function getRecruiterProfile(): ?RecruiterProfile
    {
        return $this->recruiterProfile;
    }

    public function setRecruiterProfile(?RecruiterProfile $recruiterProfile): self
    {
        if ($recruiterProfile === null && $this->recruiterProfile !== null) {
            $this->recruiterProfile->setUser(null);
        }

        if ($recruiterProfile !== null && $recruiterProfile->getUser() !== $this) {
            $recruiterProfile->setUser($this);
        }

        $this->recruiterProfile = $recruiterProfile;
        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SearchHistory>
     */
    public function getSearchHistories(): Collection
    {
        return $this->searchHistories;
    }

    public function addSearchHistory(SearchHistory $searchHistory): self
    {
        if (!$this->searchHistories->contains($searchHistory)) {
            $this->searchHistories->add($searchHistory);
            $searchHistory->setUser($this);
        }

        return $this;
    }

    public function removeSearchHistory(SearchHistory $searchHistory): self
    {
        if ($this->searchHistories->removeElement($searchHistory)) {
            if ($searchHistory->getUser() === $this) {
                $searchHistory->setUser(null);
            }
        }

        return $this;
    }

    // UserInterface methods

    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->userType === 'admin') {
            $roles[] = 'ROLE_ADMIN';
        } elseif ($this->userType === 'recruiter') {
            $roles[] = 'ROLE_RECRUITER';
        } elseif ($this->userType === 'candidate') {
            $roles[] = 'ROLE_CANDIDATE';
        }

        return array_unique($roles);
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
