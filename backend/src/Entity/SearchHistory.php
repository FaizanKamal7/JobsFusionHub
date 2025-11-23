<?php

namespace App\Entity;

use App\Repository\SearchHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SearchHistoryRepository::class)]
#[ORM\Table(name: 'search_history')]
#[ORM\Index(name: 'idx_user', columns: ['user_id'])]
#[ORM\Index(name: 'idx_created', columns: ['created_at'])]
class SearchHistory
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'searchHistories')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $searchQuery = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $filtersJson = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $resultsCount = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $clickedJobIds = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?string { return $this->id; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getSearchQuery(): ?string { return $this->searchQuery; }
    public function setSearchQuery(?string $searchQuery): self { $this->searchQuery = $searchQuery; return $this; }
    public function getFiltersJson(): ?array { return $this->filtersJson; }
    public function setFiltersJson(?array $filtersJson): self { $this->filtersJson = $filtersJson; return $this; }
    public function getResultsCount(): ?int { return $this->resultsCount; }
    public function setResultsCount(?int $resultsCount): self { $this->resultsCount = $resultsCount; return $this; }
    public function getClickedJobIds(): ?array { return $this->clickedJobIds; }
    public function setClickedJobIds(?array $clickedJobIds): self { $this->clickedJobIds = $clickedJobIds; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }
}
