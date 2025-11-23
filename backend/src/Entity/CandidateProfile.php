<?php

namespace App\Entity;

use App\Repository\CandidateProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidateProfileRepository::class)]
#[ORM\Table(name: 'candidate_profiles')]
#[ORM\Index(name: 'idx_location', columns: ['location'])]
#[ORM\Index(name: 'idx_country', columns: ['country_code'])]
class CandidateProfile
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\OneToOne(inversedBy: 'candidateProfile', targetEntity: User::class, cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?User $user = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: 'string', length: 2, nullable: true)]
    private ?string $countryCode = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $desiredJobTitle = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?string $desiredSalaryMin = null;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 2, nullable: true)]
    private ?string $desiredSalaryMax = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $desiredWorkMode = null; // 'remote', 'hybrid', 'onsite'

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $yearsOfExperience = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $currentCompany = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $linkedinUrl = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $githubUrl = null;

    #[ORM\Column(type: 'string', length: 500, nullable: true)]
    private ?string $portfolioUrl = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'candidateProfile', targetEntity: CandidateSkill::class, orphanRemoval: true)]
    private Collection $skills;

    #[ORM\OneToMany(mappedBy: 'candidateProfile', targetEntity: Resume::class, orphanRemoval: true)]
    private Collection $resumes;

    #[ORM\OneToMany(mappedBy: 'candidateProfile', targetEntity: Application::class, orphanRemoval: true)]
    private Collection $applications;

    #[ORM\OneToMany(mappedBy: 'candidateProfile', targetEntity: SavedJob::class, orphanRemoval: true)]
    private Collection $savedJobs;

    #[ORM\OneToMany(mappedBy: 'candidateProfile', targetEntity: HiddenJob::class, orphanRemoval: true)]
    private Collection $hiddenJobs;

    public function __construct()
    {
        $this->id = \Symfony\Component\Uid\Uuid::v4()->toRfc4122();
        $this->skills = new ArrayCollection();
        $this->resumes = new ArrayCollection();
        $this->applications = new ArrayCollection();
        $this->savedJobs = new ArrayCollection();
        $this->hiddenJobs = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    // Getters and Setters

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
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

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountryCode(?string $countryCode): self
    {
        $this->countryCode = $countryCode;
        return $this;
    }

    public function getDesiredJobTitle(): ?string
    {
        return $this->desiredJobTitle;
    }

    public function setDesiredJobTitle(?string $desiredJobTitle): self
    {
        $this->desiredJobTitle = $desiredJobTitle;
        return $this;
    }

    public function getDesiredSalaryMin(): ?string
    {
        return $this->desiredSalaryMin;
    }

    public function setDesiredSalaryMin(?string $desiredSalaryMin): self
    {
        $this->desiredSalaryMin = $desiredSalaryMin;
        return $this;
    }

    public function getDesiredSalaryMax(): ?string
    {
        return $this->desiredSalaryMax;
    }

    public function setDesiredSalaryMax(?string $desiredSalaryMax): self
    {
        $this->desiredSalaryMax = $desiredSalaryMax;
        return $this;
    }

    public function getDesiredWorkMode(): ?string
    {
        return $this->desiredWorkMode;
    }

    public function setDesiredWorkMode(?string $desiredWorkMode): self
    {
        $this->desiredWorkMode = $desiredWorkMode;
        return $this;
    }

    public function getYearsOfExperience(): ?int
    {
        return $this->yearsOfExperience;
    }

    public function setYearsOfExperience(?int $yearsOfExperience): self
    {
        $this->yearsOfExperience = $yearsOfExperience;
        return $this;
    }

    public function getCurrentCompany(): ?string
    {
        return $this->currentCompany;
    }

    public function setCurrentCompany(?string $currentCompany): self
    {
        $this->currentCompany = $currentCompany;
        return $this;
    }

    public function getLinkedinUrl(): ?string
    {
        return $this->linkedinUrl;
    }

    public function setLinkedinUrl(?string $linkedinUrl): self
    {
        $this->linkedinUrl = $linkedinUrl;
        return $this;
    }

    public function getGithubUrl(): ?string
    {
        return $this->githubUrl;
    }

    public function setGithubUrl(?string $githubUrl): self
    {
        $this->githubUrl = $githubUrl;
        return $this;
    }

    public function getPortfolioUrl(): ?string
    {
        return $this->portfolioUrl;
    }

    public function setPortfolioUrl(?string $portfolioUrl): self
    {
        $this->portfolioUrl = $portfolioUrl;
        return $this;
    }

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): self
    {
        $this->bio = $bio;
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
     * @return Collection<int, CandidateSkill>
     */
    public function getSkills(): Collection
    {
        return $this->skills;
    }

    public function addSkill(CandidateSkill $skill): self
    {
        if (!$this->skills->contains($skill)) {
            $this->skills->add($skill);
            $skill->setCandidateProfile($this);
        }

        return $this;
    }

    public function removeSkill(CandidateSkill $skill): self
    {
        if ($this->skills->removeElement($skill)) {
            if ($skill->getCandidateProfile() === $this) {
                $skill->setCandidateProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Resume>
     */
    public function getResumes(): Collection
    {
        return $this->resumes;
    }

    public function addResume(Resume $resume): self
    {
        if (!$this->resumes->contains($resume)) {
            $this->resumes->add($resume);
            $resume->setCandidateProfile($this);
        }

        return $this;
    }

    public function removeResume(Resume $resume): self
    {
        if ($this->resumes->removeElement($resume)) {
            if ($resume->getCandidateProfile() === $this) {
                $resume->setCandidateProfile(null);
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

    public function addApplication(Application $application): self
    {
        if (!$this->applications->contains($application)) {
            $this->applications->add($application);
            $application->setCandidateProfile($this);
        }

        return $this;
    }

    public function removeApplication(Application $application): self
    {
        if ($this->applications->removeElement($application)) {
            if ($application->getCandidateProfile() === $this) {
                $application->setCandidateProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, SavedJob>
     */
    public function getSavedJobs(): Collection
    {
        return $this->savedJobs;
    }

    public function addSavedJob(SavedJob $savedJob): self
    {
        if (!$this->savedJobs->contains($savedJob)) {
            $this->savedJobs->add($savedJob);
            $savedJob->setCandidateProfile($this);
        }

        return $this;
    }

    public function removeSavedJob(SavedJob $savedJob): self
    {
        if ($this->savedJobs->removeElement($savedJob)) {
            if ($savedJob->getCandidateProfile() === $this) {
                $savedJob->setCandidateProfile(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, HiddenJob>
     */
    public function getHiddenJobs(): Collection
    {
        return $this->hiddenJobs;
    }

    public function addHiddenJob(HiddenJob $hiddenJob): self
    {
        if (!$this->hiddenJobs->contains($hiddenJob)) {
            $this->hiddenJobs->add($hiddenJob);
            $hiddenJob->setCandidateProfile($this);
        }

        return $this;
    }

    public function removeHiddenJob(HiddenJob $hiddenJob): self
    {
        if ($this->hiddenJobs->removeElement($hiddenJob)) {
            if ($hiddenJob->getCandidateProfile() === $this) {
                $hiddenJob->setCandidateProfile(null);
            }
        }

        return $this;
    }
}
