<?php

namespace App\Entity;

use App\Repository\AcademicYearRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AcademicYearRepository::class)]
class AcademicYear
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $endDate = null;


    #[ORM\OneToMany(mappedBy: 'academicYear', targetEntity: Group::class)]
    private Collection $groups;

    #[ORM\ManyToMany(targetEntity: Teacher::class, mappedBy: 'academicYears')]
    private Collection $teachers;

    public function __construct() {
        $this->groups = new ArrayCollection();
        $this->teachers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getGroups(): Collection {
        return $this->groups;
    }

    public function addGroup(Group $group): self {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->setAcademicYear($this);
        }
        return $this;
    }

    public function removeGroup(Group $group): self {
        if ($this->groups->removeElement($group)) {
            if ($group->getAcademicYear() === $this) {
                $group->setAcademicYear(null);
            }
        }
        return $this;
    }
    public function getTeachers(): Collection {
        return $this->teachers;
    }

    public function addTeacher(Teacher $teacher): self {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers[] = $teacher;
            $teacher->addAcademicYear($this);
        }
        return $this;
    }

    public function removeTeacher(Teacher $teacher): self {
        if ($this->teachers->removeElement($teacher)) {
            $teacher->removeAcademicYear($this);
        }
        return $this;
    }
}
