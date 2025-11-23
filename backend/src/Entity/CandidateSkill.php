<?php

namespace App\Entity;

use App\Repository\CandidateSkillRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CandidateSkillRepository::class)]
#[ORM\Table(name: 'candidate_skills')]
#[ORM\Index(name: 'idx_candidate', columns: ['candidate_profile_id'])]
#[ORM\Index(name: 'idx_skill', columns: ['skill_name'])]
class CandidateSkill
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: CandidateProfile::class, inversedBy: 'skills')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?CandidateProfile $candidateProfile = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $skillName = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $proficiencyLevel = null; // 'beginner', 'intermediate', 'advanced', 'expert'

    #[ORM\Column(type: 'decimal', precision: 3, scale: 1, nullable: true)]
    private ?string $yearsOfExperience = null;

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

    public function getSkillName(): ?string
    {
        return $this->skillName;
    }

    public function setSkillName(string $skillName): self
    {
        $this->skillName = $skillName;
        return $this;
    }

    public function getProficiencyLevel(): ?string
    {
        return $this->proficiencyLevel;
    }

    public function setProficiencyLevel(?string $proficiencyLevel): self
    {
        $this->proficiencyLevel = $proficiencyLevel;
        return $this;
    }

    public function getYearsOfExperience(): ?string
    {
        return $this->yearsOfExperience;
    }

    public function setYearsOfExperience(?string $yearsOfExperience): self
    {
        $this->yearsOfExperience = $yearsOfExperience;
        return $this;
    }
}
