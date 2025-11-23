<?php

namespace App\Entity;

use App\Repository\HiddenJobRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HiddenJobRepository::class)]
#[ORM\Table(name: 'hidden_jobs')]
#[ORM\UniqueConstraint(name: 'UNIQ_HIDDEN_JOB', columns: ['candidate_profile_id', 'job_id'])]
#[ORM\Index(name: 'idx_candidate', columns: ['candidate_profile_id'])]
#[ORM\Index(name: 'idx_job', columns: ['job_id'])]
class HiddenJob
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: CandidateProfile::class, inversedBy: 'hiddenJobs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CandidateProfile $candidateProfile = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'hiddenByUsers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Job $job = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $hiddenAt = null;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->hiddenAt = new \DateTime();
    }

    public function getId(): ?string { return $this->id; }
    public function getCandidateProfile(): ?CandidateProfile { return $this->candidateProfile; }
    public function setCandidateProfile(?CandidateProfile $candidateProfile): self { $this->candidateProfile = $candidateProfile; return $this; }
    public function getJob(): ?Job { return $this->job; }
    public function setJob(?Job $job): self { $this->job = $job; return $this; }
    public function getReason(): ?string { return $this->reason; }
    public function setReason(?string $reason): self { $this->reason = $reason; return $this; }
    public function getHiddenAt(): ?\DateTimeInterface { return $this->hiddenAt; }
    public function setHiddenAt(\DateTimeInterface $hiddenAt): self { $this->hiddenAt = $hiddenAt; return $this; }
}
