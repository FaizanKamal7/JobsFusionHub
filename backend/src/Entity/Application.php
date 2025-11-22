<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
#[ORM\Table(name: 'applications')]
#[ORM\UniqueConstraint(name: 'UNIQ_CANDIDATE_JOB', columns: ['candidate_profile_id', 'job_id'])]
#[ORM\Index(name: 'idx_candidate', columns: ['candidate_profile_id'])]
#[ORM\Index(name: 'idx_job', columns: ['job_id'])]
#[ORM\Index(name: 'idx_status', columns: ['status'])]
#[ORM\Index(name: 'idx_match_score', columns: ['match_score'])]
#[ORM\Index(name: 'idx_applied_date', columns: ['applied_at'])]
class Application
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: CandidateProfile::class, inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CandidateProfile $candidateProfile = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Job $job = null;

    #[ORM\ManyToOne(targetEntity: Resume::class, inversedBy: 'applications')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Resume $resume = null;

    #[ORM\Column(type: 'string', length: 20, options: ['default' => 'applied'])]
    private string $status = 'applied'; // 'draft', 'applied', 'viewed', 'shortlisted', 'interviewing', 'offered', 'accepted', 'rejected', 'withdrawn'

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $coverLetter = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $matchScore = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $matchDetails = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $appliedAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->appliedAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    public function getId(): ?string { return $this->id; }
    public function getCandidateProfile(): ?CandidateProfile { return $this->candidateProfile; }
    public function setCandidateProfile(?CandidateProfile $candidateProfile): self { $this->candidateProfile = $candidateProfile; return $this; }
    public function getJob(): ?Job { return $this->job; }
    public function setJob(?Job $job): self { $this->job = $job; return $this; }
    public function getResume(): ?Resume { return $this->resume; }
    public function setResume(?Resume $resume): self { $this->resume = $resume; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getCoverLetter(): ?string { return $this->coverLetter; }
    public function setCoverLetter(?string $coverLetter): self { $this->coverLetter = $coverLetter; return $this; }
    public function getMatchScore(): ?string { return $this->matchScore; }
    public function setMatchScore(?string $matchScore): self { $this->matchScore = $matchScore; return $this; }
    public function getMatchDetails(): ?array { return $this->matchDetails; }
    public function setMatchDetails(?array $matchDetails): self { $this->matchDetails = $matchDetails; return $this; }
    public function getAppliedAt(): ?\DateTimeInterface { return $this->appliedAt; }
    public function setAppliedAt(\DateTimeInterface $appliedAt): self { $this->appliedAt = $appliedAt; return $this; }
    public function getUpdatedAt(): ?\DateTimeInterface { return $this->updatedAt; }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self { $this->updatedAt = $updatedAt; return $this; }
}
