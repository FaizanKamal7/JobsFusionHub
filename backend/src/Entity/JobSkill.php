<?php

namespace App\Entity;

use App\Repository\JobSkillRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: JobSkillRepository::class)]
#[ORM\Table(name: 'job_skills')]
#[ORM\Index(name: 'idx_job', columns: ['job_id'])]
#[ORM\Index(name: 'idx_skill', columns: ['skill_name'])]
class JobSkill
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    private ?string $id = null;

    #[ORM\ManyToOne(targetEntity: Job::class, inversedBy: 'jobSkills')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Job $job = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $skillName = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isRequired = true;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $proficiencyLevel = null;

    public function getId(): ?string { return $this->id; }
    public function getJob(): ?Job { return $this->job; }
    public function setJob(?Job $job): self { $this->job = $job; return $this; }
    public function getSkillName(): ?string { return $this->skillName; }
    public function setSkillName(string $skillName): self { $this->skillName = $skillName; return $this; }
    public function isRequired(): bool { return $this->isRequired; }
    public function setIsRequired(bool $isRequired): self { $this->isRequired = $isRequired; return $this; }
    public function getProficiencyLevel(): ?string { return $this->proficiencyLevel; }
    public function setProficiencyLevel(?string $proficiencyLevel): self { $this->proficiencyLevel = $proficiencyLevel; return $this; }
}
