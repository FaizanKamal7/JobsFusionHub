<?php

namespace App\Entity;

use App\Repository\SavedJobRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SavedJobRepository::class)]
#[ORM\Table(name: 'saved_jobs')]
#[ORM\UniqueConstraint(name: 'UNIQ_SAVED_JOB', columns: ['candidate_profile_id', 'job_id'])]
#[ORM\Index(name: 'idx_candidate', columns: ['candidate_profile_id'])]
#[ORM\Index(name: 'idx_job', columns: ['job_id'])]
#[ORM\Index(name: 'idx_saved_date', columns: ['saved_at'])]
class SavedJob
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: CandidateProfile::class, inversedBy: 'savedJobs')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CandidateProfile $candidateProfile = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'savedByUsers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Job $job = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $savedAt = null;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->savedAt = new \DateTime();
    }

    public function getId(): ?string { return $this->id; }
    public function getCandidateProfile(): ?CandidateProfile { return $this->candidateProfile; }
    public function setCandidateProfile(?CandidateProfile $candidateProfile): self { $this->candidateProfile = $candidateProfile; return $this; }
    public function getJob(): ?Job { return $this->job; }
    public function setJob(?Job $job): self { $this->job = $job; return $this; }
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }
    public function getSavedAt(): ?\DateTimeInterface { return $this->savedAt; }
    public function setSavedAt(\DateTimeInterface $savedAt): self { $this->savedAt = $savedAt; return $this; }
}
