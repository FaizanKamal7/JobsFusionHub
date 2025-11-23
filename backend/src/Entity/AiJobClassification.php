<?php

namespace App\Entity;

use App\Repository\AiJobClassificationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AiJobClassificationRepository::class)]
#[ORM\Table(name: 'ai_job_classifications')]
#[ORM\Index(name: 'idx_job', columns: ['job_id'])]
#[ORM\Index(name: 'idx_category', columns: ['category'])]
class AiJobClassification
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'aiClassifications')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Job $job = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $category = null; // 'engineering', 'sales', 'marketing', etc.

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $subcategory = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $techStack = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $seniorityLevel = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 4)]
    private ?string $confidenceScore = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $modelVersion = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $classifiedAt = null;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->classifiedAt = new \DateTime();
    }

    public function getId(): ?string { return $this->id; }
    public function getJob(): ?Job { return $this->job; }
    public function setJob(?Job $job): self { $this->job = $job; return $this; }
    public function getCategory(): ?string { return $this->category; }
    public function setCategory(string $category): self { $this->category = $category; return $this; }
    public function getSubcategory(): ?string { return $this->subcategory; }
    public function setSubcategory(?string $subcategory): self { $this->subcategory = $subcategory; return $this; }
    public function getTechStack(): ?array { return $this->techStack; }
    public function setTechStack(?array $techStack): self { $this->techStack = $techStack; return $this; }
    public function getSeniorityLevel(): ?string { return $this->seniorityLevel; }
    public function setSeniorityLevel(?string $seniorityLevel): self { $this->seniorityLevel = $seniorityLevel; return $this; }
    public function getConfidenceScore(): ?string { return $this->confidenceScore; }
    public function setConfidenceScore(string $confidenceScore): self { $this->confidenceScore = $confidenceScore; return $this; }
    public function getModelVersion(): ?string { return $this->modelVersion; }
    public function setModelVersion(?string $modelVersion): self { $this->modelVersion = $modelVersion; return $this; }
    public function getClassifiedAt(): ?\DateTimeInterface { return $this->classifiedAt; }
    public function setClassifiedAt(\DateTimeInterface $classifiedAt): self { $this->classifiedAt = $classifiedAt; return $this; }
}
