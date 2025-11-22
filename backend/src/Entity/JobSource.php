<?php

namespace App\Entity;

use App\Repository\JobSourceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobSourceRepository::class)]
#[ORM\Table(name: 'job_sources')]
#[ORM\UniqueConstraint(name: 'UNIQ_SOURCE_JOB', columns: ['source_platform', 'source_job_id'])]
#[ORM\Index(name: 'idx_job', columns: ['job_id'])]
#[ORM\Index(name: 'idx_source', columns: ['source_platform'])]
#[ORM\Index(name: 'idx_scraped', columns: ['last_scraped_at'])]
class JobSource
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'jobSources')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Job $job = null;

    #[ORM\Column(type: 'string', length: 50)]
    private ?string $sourcePlatform = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $sourceJobId = null;

    #[ORM\Column(type: 'string', length: 1000)]
    private ?string $sourceUrl = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $sourcePostedDate = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $lastScrapedAt = null;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->lastScrapedAt = new \DateTime();
    }

    public function getId(): ?string { return $this->id; }
    public function getJob(): ?Job { return $this->job; }
    public function setJob(?Job $job): self { $this->job = $job; return $this; }
    public function getSourcePlatform(): ?string { return $this->sourcePlatform; }
    public function setSourcePlatform(string $sourcePlatform): self { $this->sourcePlatform = $sourcePlatform; return $this; }
    public function getSourceJobId(): ?string { return $this->sourceJobId; }
    public function setSourceJobId(?string $sourceJobId): self { $this->sourceJobId = $sourceJobId; return $this; }
    public function getSourceUrl(): ?string { return $this->sourceUrl; }
    public function setSourceUrl(string $sourceUrl): self { $this->sourceUrl = $sourceUrl; return $this; }
    public function getSourcePostedDate(): ?\DateTimeInterface { return $this->sourcePostedDate; }
    public function setSourcePostedDate(?\DateTimeInterface $sourcePostedDate): self { $this->sourcePostedDate = $sourcePostedDate; return $this; }
    public function getLastScrapedAt(): ?\DateTimeInterface { return $this->lastScrapedAt; }
    public function setLastScrapedAt(\DateTimeInterface $lastScrapedAt): self { $this->lastScrapedAt = $lastScrapedAt; return $this; }
}
