<?php

namespace App\Entity;

use App\Repository\ScraperConfigRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScraperConfigRepository::class)]
#[ORM\Table(name: 'scraper_configs')]
#[ORM\UniqueConstraint(name: 'UNIQ_PLATFORM', columns: ['platform_name'])]
#[ORM\Index(name: 'idx_active', columns: ['is_active'])]
class ScraperConfig
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private ?string $id = null;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private ?string $platformName = null;

    #[ORM\Column(type: 'string', length: 500)]
    private ?string $baseUrl = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'integer', options: ['default' => 60])]
    private int $scrapeFrequencyMinutes = 60;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastSuccessfulScrape = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastErrorAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $lastErrorMessage = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $totalJobsScraped = 0;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $configJson = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'scraperConfig', targetEntity: ScraperRun::class, orphanRemoval: true)]
    private Collection $scraperRuns;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->scraperRuns = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?string { return $this->id; }
    public function getPlatformName(): ?string { return $this->platformName; }
    public function setPlatformName(string $platformName): self { $this->platformName = $platformName; return $this; }
    public function getBaseUrl(): ?string { return $this->baseUrl; }
    public function setBaseUrl(string $baseUrl): self { $this->baseUrl = $baseUrl; return $this; }
    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): self { $this->isActive = $isActive; return $this; }
    public function getScrapeFrequencyMinutes(): int { return $this->scrapeFrequencyMinutes; }
    public function setScrapeFrequencyMinutes(int $scrapeFrequencyMinutes): self { $this->scrapeFrequencyMinutes = $scrapeFrequencyMinutes; return $this; }
    public function getLastSuccessfulScrape(): ?\DateTimeInterface { return $this->lastSuccessfulScrape; }
    public function setLastSuccessfulScrape(?\DateTimeInterface $lastSuccessfulScrape): self { $this->lastSuccessfulScrape = $lastSuccessfulScrape; return $this; }
    public function getLastErrorAt(): ?\DateTimeInterface { return $this->lastErrorAt; }
    public function setLastErrorAt(?\DateTimeInterface $lastErrorAt): self { $this->lastErrorAt = $lastErrorAt; return $this; }
    public function getLastErrorMessage(): ?string { return $this->lastErrorMessage; }
    public function setLastErrorMessage(?string $lastErrorMessage): self { $this->lastErrorMessage = $lastErrorMessage; return $this; }
    public function getTotalJobsScraped(): int { return $this->totalJobsScraped; }
    public function setTotalJobsScraped(int $totalJobsScraped): self { $this->totalJobsScraped = $totalJobsScraped; return $this; }
    public function getConfigJson(): ?array { return $this->configJson; }
    public function setConfigJson(?array $configJson): self { $this->configJson = $configJson; return $this; }
    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): self { $this->createdAt = $createdAt; return $this; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
    
    /**
     * @return Collection<int, ScraperRun>
     */
    public function getScraperRuns(): Collection { return $this->scraperRuns; }
}
