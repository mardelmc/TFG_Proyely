<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\StudentRepository;

#[ORM\Entity(repositoryClass: StudentRepository::class)]
class Student
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(nullable: true)]
    private ?float $mark = null;

    #[ORM\ManyToOne(targetEntity: Group::class, inversedBy: 'students')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Group $group = null;

    #[ORM\OneToOne(targetEntity: Project::class, mappedBy: 'student')]
    private ?Project $project = null;

    #[ORM\ManyToMany(targetEntity: Subject::class, mappedBy: 'students')]
    private Collection $subjects;

    public function __construct()
    {
        $this->subjects = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getFirstName(): ?string {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self {
        $this->lastName = $lastName;
        return $this;
    }

    public function getMark(): ?float {
        return $this->mark;
    }

    public function setMark(?float $mark): self {
        $this->mark = $mark;
        return $this;
    }

    public function getGroup(): ?Group {
        return $this->group;
    }

    public function setGroup(?Group $group): self {
        $this->group = $group;
        return $this;
    }

    public function getProject(): ?Project {
        return $this->project;
    }

    public function setProject(?Project $project): self {
        $this->project = $project;
        return $this;
    }

    public function getSubjects(): Collection {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self {
        if (!$this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
            $subject->addStudent($this); // Add this line
        }
        return $this;
    }

    public function removeSubject(Subject $subject): self {
        if ($this->subjects->removeElement($subject)) {
            $subject->removeStudent($this); // Add this line
        }
        return $this;
    }
}