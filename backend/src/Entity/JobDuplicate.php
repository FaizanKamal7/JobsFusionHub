<?php

namespace App\Entity;

use App\Repository\JobDuplicateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobDuplicateRepository::class)]
#[ORM\Table(name: 'job_duplicates')]
#[ORM\UniqueConstraint(name: 'UNIQ_DUPLICATE_PAIR', columns: ['canonical_job_id', 'duplicate_job_id'])]
#[ORM\Index(name: 'idx_canonical', columns: ['canonical_job_id'])]
#[ORM\Index(name: 'idx_duplicate', columns: ['duplicate_job_id'])]
#[ORM\Index(name: 'idx_similarity', columns: ['similarity_score'])]
class JobDuplicate
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'duplicates')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Job $canonicalJob = null;

    #[ORM\ManyToOne(targetEntity: Job::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Job $duplicateJob = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 4)]
    private ?string $similarityScore = null;

    #[ORM\Column(type: 'string', length: 20)]
    private ?string $detectionMethod = null; // 'embedding', 'fuzzy_match', 'manual'

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $detectedAt = null;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->detectedAt = new \DateTime();
    }

    public function getId(): ?string { return $this->id; }
    public function getCanonicalJob(): ?Job { return $this->canonicalJob; }
    public function setCanonicalJob(?Job $canonicalJob): self { $this->canonicalJob = $canonicalJob; return $this; }
    public function getDuplicateJob(): ?Job { return $this->duplicateJob; }
    public function setDuplicateJob(?Job $duplicateJob): self { $this->duplicateJob = $duplicateJob; return $this; }
    public function getSimilarityScore(): ?string { return $this->similarityScore; }
    public function setSimilarityScore(string $similarityScore): self { $this->similarityScore = $similarityScore; return $this; }
    public function getDetectionMethod(): ?string { return $this->detectionMethod; }
    public function setDetectionMethod(string $detectionMethod): self { $this->detectionMethod = $detectionMethod; return $this; }
    public function getDetectedAt(): ?\DateTimeInterface { return $this->detectedAt; }
    public function setDetectedAt(\DateTimeInterface $detectedAt): self { $this->detectedAt = $detectedAt; return $this; }
}
