<?php

namespace App\Entity;

use App\Repository\ResumeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResumeRepository::class)]
#[ORM\Table(name: 'resumes')]
#[ORM\Index(name: 'idx_candidate', columns: ['candidate_profile_id'])]
#[ORM\Index(name: 'idx_is_primary', columns: ['is_primary'])]
class Resume
{
    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: CandidateProfile::class, inversedBy: 'resumes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CandidateProfile $candidateProfile = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $fileName = null;

    #[ORM\Column(type: 'string', length: 500)]
    private ?string $filePath = null;

    #[ORM\Column(type: 'integer')]
    private ?int $fileSize = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $mimeType = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $parsedText = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPrimary = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $embeddingVectorId = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $uploadedAt = null;

    #[ORM\OneToMany(mappedBy: 'resume', targetEntity: Application::class)]
    private Collection $applications;

    #[ORM\OneToMany(mappedBy: 'resume', targetEntity: ResumeJobMatch::class, orphanRemoval: true)]
    private Collection $jobMatches;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->applications = new ArrayCollection();
        $this->jobMatches = new ArrayCollection();
        $this->uploadedAt = new \DateTime();
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCandidateProfile(): ?CandidateProfile
    {
        return $this->candidateProfile;
    }

    public function setCandidateProfile(?CandidateProfile $candidateProfile): self
    {
        $this->candidateProfile = $candidateProfile;
        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): self
    {
        $this->fileSize = $fileSize;
        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(?string $mimeType): self
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    public function getParsedText(): ?string
    {
        return $this->parsedText;
    }

    public function setParsedText(?string $parsedText): self
    {
        $this->parsedText = $parsedText;
        return $this;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): self
    {
        $this->isPrimary = $isPrimary;
        return $this;
    }

    public function getEmbeddingVectorId(): ?string
    {
        return $this->embeddingVectorId;
    }

    public function setEmbeddingVectorId(?string $embeddingVectorId): self
    {
        $this->embeddingVectorId = $embeddingVectorId;
        return $this;
    }

    public function getUploadedAt(): ?\DateTimeInterface
    {
        return $this->uploadedAt;
    }

    public function setUploadedAt(\DateTimeInterface $uploadedAt): self
    {
        $this->uploadedAt = $uploadedAt;
        return $this;
    }

    /**
     * @return Collection<int, Application>
     */
    public function getApplications(): Collection
    {
        return $this->applications;
    }

    /**
     * @return Collection<int, ResumeJobMatch>
     */
    public function getJobMatches(): Collection
    {
        return $this->jobMatches;
    }

    public function addJobMatch(ResumeJobMatch $jobMatch): self
    {
        if (!$this->jobMatches->contains($jobMatch)) {
            $this->jobMatches->add($jobMatch);
            $jobMatch->setResume($this);
        }

        return $this;
    }

    public function removeJobMatch(ResumeJobMatch $jobMatch): self
    {
        if ($this->jobMatches->removeElement($jobMatch)) {
            if ($jobMatch->getResume() === $this) {
                $jobMatch->setResume(null);
            }
        }

        return $this;
    }
}
