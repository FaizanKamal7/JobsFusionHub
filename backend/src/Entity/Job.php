<?php

namespace App\Entity;

use App\Repository\JobRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobRepository::class)]
#[ORM\Table(name: 'jobs')]
#[ORM\Index(name: 'idx_canonical', columns: ['canonical_id'])]
#[ORM\Index(name: 'idx_company', columns: ['company_id'])]
#[ORM\Index(name: 'idx_location', columns: ['country_code', 'city'])]
#[ORM\Index(name: 'idx_job_type', columns: ['job_type'])]
#[ORM\Index(name: 'idx_work_mode', columns: ['work_mode'])]
#[ORM\Index(name: 'idx_posted_date', columns: ['posted_date'])]
#[ORM\Index(name: 'idx_active', columns: ['is_active'])]
#[ORM\Index(name: 'idx_duplicate', columns: ['is_duplicate'])]
class Job
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\Column(type: 'uuid', nullable: true)]
    private ?string $canonicalId = null;

    #[ORM\Column(type: 'string', length: 500)]
    private ?string $title = null;

    #[ORM\ManyToOne(targetEntity: Company::class, inversedBy: 'jobs')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Company $company = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $companyName = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $requirements = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $responsibilities = null;

    // Location fields
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $stateProvince = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 2, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isLocationRemote = false;

    // Job details
    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $jobType = null; // 'full-time', 'part-time', 'contract', 'internship', 'temporary'

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $workMode = null; // 'remote', 'hybrid', 'onsite'

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $experienceLevel = null; // 'entry', 'mid', 'senior', 'lead', 'executive'

    // Salary
    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?string $salaryMin = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?string $salaryMax = null;

    #[ORM\Column(type: 'string', length: 3, options: ['default' => 'USD'])]
    private string $salaryCurrency = 'USD';

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $salaryPeriod = null; // 'hourly', 'monthly', 'yearly'

    // Application
    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $applicationUrl = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $applicationEmail = null;

    #[ORM\Column(type: 'string', length: 1000, nullable: true)]
    private ?string $externalApplyUrl = null;

    // Status
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isDuplicate = false;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isSpam = false;

    #[ORM\Column(type: 'decimal', precision: 3, scale: 2, nullable: true)]
    private ?string $spamConfidenceScore = null;

    // AI/ML fields
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $embeddingVectorId = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $classificationTags = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $extractedKeywords = null;

    // Timestamps
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $postedDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $expiresAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $scrapedAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    // Relationships
    #[ORM\OneToMany(mappedBy: 'job', targetEntity: JobSource::class, orphanRemoval: true)]
    private Collection $jobSources;

    #[ORM\OneToMany(mappedBy: 'canonicalJob', targetEntity: JobDuplicate::class, orphanRemoval: true)]
    private Collection $duplicates;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: JobSkill::class, orphanRemoval: true)]
    private Collection $jobSkills;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: Application::class, orphanRemoval: true)]
    private Collection $applications;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: SavedJob::class, orphanRemoval: true)]
    private Collection $savedByUsers;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: HiddenJob::class, orphanRemoval: true)]
    private Collection $hiddenByUsers;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: AiJobClassification::class, orphanRemoval: true)]
    private Collection $aiClassifications;

    #[ORM\OneToMany(mappedBy: 'job', targetEntity: ResumeJobMatch::class, orphanRemoval: true)]
    private Collection $resumeMatches;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->jobSources = new ArrayCollection();
        $this->duplicates = new ArrayCollection();
        $this->jobSkills = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->savedByUsers = new ArrayCollection();
        $this->hiddenByUsers = new ArrayCollection();
        $this->aiClassifications = new ArrayCollection();
        $this->resumeMatches = new ArrayCollection();
        $this->scrapedAt = new \DateTime();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // Getters and Setters
    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCanonicalId(): ?string
    {
        return $this->canonicalId;
    }

    public function setCanonicalId(?string $canonicalId): self
    {
        $this->canonicalId = $canonicalId;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;
        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): self
    {
        $this->companyName = $companyName;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getRequirements(): ?string
    {
        return $this->requirements;
    }

    public function setRequirements(?string $requirements): self
    {
        $this->requirements = $requirements;
        return $this;
    }

    public function getResponsibilities(): ?string
    {
        return $this->responsibilities;
    }

    public function setResponsibilities(?string $responsibilities): self
    {
        $this->responsibilities = $responsibilities;
        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    public function getStateProvince(): ?string
    {
        return $this->stateProvince;
    }

    public function setStateProvince(?string $stateProvince): self
    {
        $this->stateProvince = $stateProvince;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function isLocationRemote(): bool
    {
        return $this->isLocationRemote;
    }

    public function setIsLocationRemote(bool $isLocationRemote): self
    {
        $this->isLocationRemote = $isLocationRemote;
        return $this;
    }

    public function getJobType(): ?string
    {
        return $this->jobType;
    }

    public function setJobType(?string $jobType): self
    {
        $this->jobType = $jobType;
        return $this;
    }

    public function getWorkMode(): ?string
    {
        return $this->workMode;
    }

    public function setWorkMode(?string $workMode): self
    {
        $this->workMode = $workMode;
        return $this;
    }

    public function getExperienceLevel(): ?string
    {
        return $this->experienceLevel;
    }

    public function setExperienceLevel(?string $experienceLevel): self
    {
        $this->experienceLevel = $experienceLevel;
        return $this;
    }

    public function getSalaryMin(): ?string
    {
        return $this->salaryMin;
    }

    public function setSalaryMin(?string $salaryMin): self
    {
        $this->salaryMin = $salaryMin;
        return $this;
    }

    public function getSalaryMax(): ?string
    {
        return $this->salaryMax;
    }

    public function setSalaryMax(?string $salaryMax): self
    {
        $this->salaryMax = $salaryMax;
        return $this;
    }

    public function getSalaryCurrency(): string
    {
        return $this->salaryCurrency;
    }

    public function setSalaryCurrency(string $salaryCurrency): self
    {
        $this->salaryCurrency = $salaryCurrency;
        return $this;
    }

    public function getSalaryPeriod(): ?string
    {
        return $this->salaryPeriod;
    }

    public function setSalaryPeriod(?string $salaryPeriod): self
    {
        $this->salaryPeriod = $salaryPeriod;
        return $this;
    }

    public function getApplicationUrl(): ?string
    {
        return $this->applicationUrl;
    }

    public function setApplicationUrl(?string $applicationUrl): self
    {
        $this->applicationUrl = $applicationUrl;
        return $this;
    }

    public function getApplicationEmail(): ?string
    {
        return $this->applicationEmail;
    }

    public function setApplicationEmail(?string $applicationEmail): self
    {
        $this->applicationEmail = $applicationEmail;
        return $this;
    }

    public function getExternalApplyUrl(): ?string
    {
        return $this->externalApplyUrl;
    }

    public function setExternalApplyUrl(?string $externalApplyUrl): self
    {
        $this->externalApplyUrl = $externalApplyUrl;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function isDuplicate(): bool
    {
        return $this->isDuplicate;
    }

    public function setIsDuplicate(bool $isDuplicate): self
    {
        $this->isDuplicate = $isDuplicate;
        return $this;
    }

    public function isSpam(): bool
    {
        return $this->isSpam;
    }

    public function setIsSpam(bool $isSpam): self
    {
        $this->isSpam = $isSpam;
        return $this;
    }

    public function getSpamConfidenceScore(): ?string
    {
        return $this->spamConfidenceScore;
    }

    public function setSpamConfidenceScore(?string $spamConfidenceScore): self
    {
        $this->spamConfidenceScore = $spamConfidenceScore;
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

    public function getClassificationTags(): ?array
    {
        return $this->classificationTags;
    }

    public function setClassificationTags(?array $classificationTags): self
    {
        $this->classificationTags = $classificationTags;
        return $this;
    }

    public function getExtractedKeywords(): ?array
    {
        return $this->extractedKeywords;
    }

    public function setExtractedKeywords(?array $extractedKeywords): self
    {
        $this->extractedKeywords = $extractedKeywords;
        return $this;
    }

    public function getPostedDate(): ?\DateTimeInterface
    {
        return $this->postedDate;
    }

    public function setPostedDate(?\DateTimeInterface $postedDate): self
    {
        $this->postedDate = $postedDate;
        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getScrapedAt(): ?\DateTimeInterface
    {
        return $this->scrapedAt;
    }

    public function setScrapedAt(\DateTimeInterface $scrapedAt): self
    {
        $this->scrapedAt = $scrapedAt;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return Collection<int, JobSource>
     */
    public function getJobSources(): Collection
    {
        return $this->jobSources;
    }

    public function addJobSource(JobSource $jobSource): self
    {
        if (!$this->jobSources->contains($jobSource)) {
            $this->jobSources->add($jobSource);
            $jobSource->setJob($this);
        }

        return $this;
    }

    public function removeJobSource(JobSource $jobSource): self
    {
        if ($this->jobSources->removeElement($jobSource)) {
            if ($jobSource->getJob() === $this) {
                $jobSource->setJob(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, JobDuplicate>
     */
    public function getDuplicates(): Collection
    {
        return $this->duplicates;
    }

    /**
     * @return Collection<int, JobSkill>
     */
    public function getJobSkills(): Collection
    {
        return $this->jobSkills;
    }

    public function addJobSkill(JobSkill $jobSkill): self
    {
        if (!$this->jobSkills->contains($jobSkill)) {
            $this->jobSkills->add($jobSkill);
            $jobSkill->setJob($this);
        }

        return $this;
    }

    public function removeJobSkill(JobSkill $jobSkill): self
    {
        if ($this->jobSkills->removeElement($jobSkill)) {
            if ($jobSkill->getJob() === $this) {
                $jobSkill->setJob(null);
            }
        }

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
     * @return Collection<int, SavedJob>
     */
    public function getSavedByUsers(): Collection
    {
        return $this->savedByUsers;
    }

    /**
     * @return Collection<int, HiddenJob>
     */
    public function getHiddenByUsers(): Collection
    {
        return $this->hiddenByUsers;
    }

    /**
     * @return Collection<int, AiJobClassification>
     */
    public function getAiClassifications(): Collection
    {
        return $this->aiClassifications;
    }

    /**
     * @return Collection<int, ResumeJobMatch>
     */
    public function getResumeMatches(): Collection
    {
        return $this->resumeMatches;
    }
}
