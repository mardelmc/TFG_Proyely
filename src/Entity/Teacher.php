<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column]
    private ?bool $tutor = null;

    #[ORM\OneToMany(mappedBy: 'proposedBy', targetEntity: Project::class)]
    private Collection $projects;


    #[ORM\OneToOne(mappedBy: 'tutor', targetEntity: Group::class)]
    private ?Group $tutoredGroup = null;


    #[ORM\ManyToMany(targetEntity: AcademicYear::class, inversedBy: 'teachers')]
    #[ORM\JoinTable(name: 'teacher_academic_year')]
    private Collection $academicYears;


    public function __construct() {
        $this->projects = new ArrayCollection();
        $this->academicYears = new ArrayCollection();
    }



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function isTutor(): ?bool
    {
        return $this->tutor;
    }

    public function setTutor(bool $tutor): static
    {
        $this->tutor = $tutor;

        return $this;
    }

    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->setProposedBy($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            // set the owning side to null (unless already changed)
            if ($project->getProposedBy() === $this) {
                $project->setProposedBy(null);
            }
        }

        return $this;
    }

    public function getTutoredGroup(): ?Group
    {
        return $this->tutoredGroup;
    }

    public function setTutoredGroup(?Group $group): self
    {
        $this->tutoredGroup = $group;

        return $this;
    }

    public function getAcademicYears(): Collection {
        return $this->academicYears;
    }

    public function addAcademicYear(AcademicYear $academicYear): self {
        if (!$this->academicYears->contains($academicYear)) {
            $this->academicYears[] = $academicYear;
            $academicYear->addTeacher($this);
        }
        return $this;
    }

    public function removeAcademicYear(AcademicYear $academicYear): self {
        if ($this->academicYears->removeElement($academicYear)) {
            $academicYear->removeTeacher($this);
        }
        return $this;
    }
    public function __toString(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }
}
