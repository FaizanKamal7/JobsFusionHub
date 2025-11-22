<?php

namespace App\Entity;

use App\Repository\ResumeJobMatchRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResumeJobMatchRepository::class)]
#[ORM\Table(name: 'resume_job_matches')]
#[ORM\UniqueConstraint(name: 'UNIQ_RESUME_JOB', columns: ['resume_id', 'job_id'])]
#[ORM\Index(name: 'idx_resume', columns: ['resume_id'])]
#[ORM\Index(name: 'idx_job', columns: ['job_id'])]
#[ORM\Index(name: 'idx_score', columns: ['overall_match_score'])]
class ResumeJobMatch
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Resume::class, inversedBy: 'jobMatches')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Resume $resume = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'resumeMatches')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Job $job = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    private ?string $overallMatchScore = null; // 0.00 to 100.00

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $skillsMatchScore = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $experienceMatchScore = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $educationMatchScore = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2, nullable: true)]
    private ?string $locationMatchScore = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $matchingSkills = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $missingSkills = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $matchDetails = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $calculatedAt = null;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->calculatedAt = new \DateTime();
    }

    public function getId(): ?string { return $this->id; }
    public function getResume(): ?Resume { return $this->resume; }
    public function setResume(?Resume $resume): self { $this->resume = $resume; return $this; }
    public function getJob(): ?Job { return $this->job; }
    public function setJob(?Job $job): self { $this->job = $job; return $this; }
    public function getOverallMatchScore(): ?string { return $this->overallMatchScore; }
    public function setOverallMatchScore(string $overallMatchScore): self { $this->overallMatchScore = $overallMatchScore; return $this; }
    public function getSkillsMatchScore(): ?string { return $this->skillsMatchScore; }
    public function setSkillsMatchScore(?string $skillsMatchScore): self { $this->skillsMatchScore = $skillsMatchScore; return $this; }
    public function getExperienceMatchScore(): ?string { return $this->experienceMatchScore; }
    public function setExperienceMatchScore(?string $experienceMatchScore): self { $this->experienceMatchScore = $experienceMatchScore; return $this; }
    public function getEducationMatchScore(): ?string { return $this->educationMatchScore; }
    public function setEducationMatchScore(?string $educationMatchScore): self { $this->educationMatchScore = $educationMatchScore; return $this; }
    public function getLocationMatchScore(): ?string { return $this->locationMatchScore; }
    public function setLocationMatchScore(?string $locationMatchScore): self { $this->locationMatchScore = $locationMatchScore; return $this; }
    public function getMatchingSkills(): ?array { return $this->matchingSkills; }
    public function setMatchingSkills(?array $matchingSkills): self { $this->matchingSkills = $matchingSkills; return $this; }
    public function getMissingSkills(): ?array { return $this->missingSkills; }
    public function setMissingSkills(?array $missingSkills): self { $this->missingSkills = $missingSkills; return $this; }
    public function getMatchDetails(): ?array { return $this->matchDetails; }
    public function setMatchDetails(?array $matchDetails): self { $this->matchDetails = $matchDetails; return $this; }
    public function getCalculatedAt(): ?\DateTimeInterface { return $this->calculatedAt; }
    public function setCalculatedAt(\DateTimeInterface $calculatedAt): self { $this->calculatedAt = $calculatedAt; return $this; }
}
