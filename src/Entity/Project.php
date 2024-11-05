<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\Repository\ProjectRepository;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToOne(targetEntity: Student::class, inversedBy: 'project')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Teacher $proposedBy = null;

    #[ORM\ManyToMany(targetEntity: Subject::class, mappedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $subjects;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    public function getStudent(): ?Student {
        return $this->student;
    }

    public function setStudent(?Student $student): self {
        $this->student = $student;
        return $this;
    }

    public function getProposedBy(): ?Teacher
    {
        return $this->proposedBy;
    }

    public function setProposedBy(?Teacher $teacher): self
    {
        $this->proposedBy = $teacher;
        return $this;
    }

    public function getSubjects(): Collection {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self {
        if (!$this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
        }
        return $this;
    }

    public function removeSubject(Subject $subject): self {
        $this->subjects->removeElement($subject);
        return $this;
    }
}
