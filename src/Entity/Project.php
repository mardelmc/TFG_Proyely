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

    #[ORM\OneToOne(inversedBy: 'project', targetEntity: Student::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Student $student = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Teacher $proposedBy = null;

    #[ORM\ManyToMany(targetEntity: Subject::class, mappedBy: 'projects')]
    #[ORM\JoinColumn(nullable: true)]
    private Collection $subjects;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'projects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $group = null;

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

        // Sincronizar el lado inverso
        if ($student && $student->getProject() !== $this) {
            $student->setProject($this);
        }

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

    public function setSubjects($subjects): Collection {
        $this->subjects = new ArrayCollection($subjects);
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

    public function getGroup(): ?Group
    {
        return $this->group;
    }

    public function setGroup(?Group $group): self
    {
        $this->group = $group;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
