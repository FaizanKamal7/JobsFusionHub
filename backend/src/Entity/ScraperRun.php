<?php

namespace App\Entity;

use App\Repository\ScraperRunRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ScraperRunRepository::class)]
#[ORM\Table(name: 'scraper_runs')]
#[ORM\Index(name: 'idx_scraper', columns: ['scraper_config_id'])]
#[ORM\Index(name: 'idx_status', columns: ['status'])]
#[ORM\Index(name: 'idx_started', columns: ['started_at'])]
class ScraperRun
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: ScraperConfig::class, inversedBy: 'scraperRuns')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?ScraperConfig $scraperConfig = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $status = 'pending'; // 'pending', 'running', 'completed', 'failed'

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $jobsFound = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $jobsCreated = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $jobsUpdated = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $duplicatesDetected = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $errorsCount = 0;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $errorDetails = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $startedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $completedAt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $durationSeconds = null;

    public function getId(): ?string { return $this->id; }
    public function getScraperConfig(): ?ScraperConfig { return $this->scraperConfig; }
    public function setScraperConfig(?ScraperConfig $scraperConfig): self { $this->scraperConfig = $scraperConfig; return $this; }
    public function getStatus(): ?string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getJobsFound(): int { return $this->jobsFound; }
    public function setJobsFound(int $jobsFound): self { $this->jobsFound = $jobsFound; return $this; }
    public function getJobsCreated(): int { return $this->jobsCreated; }
    public function setJobsCreated(int $jobsCreated): self { $this->jobsCreated = $jobsCreated; return $this; }
    public function getJobsUpdated(): int { return $this->jobsUpdated; }
    public function setJobsUpdated(int $jobsUpdated): self { $this->jobsUpdated = $jobsUpdated; return $this; }
    public function getDuplicatesDetected(): int { return $this->duplicatesDetected; }
    public function setDuplicatesDetected(int $duplicatesDetected): self { $this->duplicatesDetected = $duplicatesDetected; return $this; }
    public function getErrorsCount(): int { return $this->errorsCount; }
    public function setErrorsCount(int $errorsCount): self { $this->errorsCount = $errorsCount; return $this; }
    public function getErrorDetails(): ?array { return $this->errorDetails; }
    public function setErrorDetails(?array $errorDetails): self { $this->errorDetails = $errorDetails; return $this; }
    public function getStartedAt(): ?\DateTimeInterface { return $this->startedAt; }
    public function setStartedAt(?\DateTimeInterface $startedAt): self { $this->startedAt = $startedAt; return $this; }
    public function getCompletedAt(): ?\DateTimeInterface { return $this->completedAt; }
    public function setCompletedAt(?\DateTimeInterface $completedAt): self { $this->completedAt = $completedAt; return $this; }
    public function getDurationSeconds(): ?int { return $this->durationSeconds; }
    public function setDurationSeconds(?int $durationSeconds): self { $this->durationSeconds = $durationSeconds; return $this; }
}
