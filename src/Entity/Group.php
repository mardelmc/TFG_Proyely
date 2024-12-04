<?php
namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupRepository;

#[ORM\Entity(repositoryClass: GroupRepository::class)]
#[ORM\Table(name: '`group`')]
class Group
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'group', targetEntity: Student::class)]
    private Collection $students;

    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'groups')]
    #[ORM\JoinTable(name: 'group_subject')]
    private Collection $subjects;

    #[ORM\ManyToMany(targetEntity: Teacher::class, inversedBy: 'groups')]
    #[ORM\JoinTable(name: 'group_teacher')]
    private Collection $tutors;

    #[ORM\ManyToOne(targetEntity: AcademicYear::class, inversedBy: 'groups')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AcademicYear $academicYear = null;

    public function __construct() {
        $this->subjects = new ArrayCollection();
        $this->students = new ArrayCollection();
        $this->tutors = new ArrayCollection();
    }

    public function getTutors(): Collection {
        return $this->tutors;
    }

    public function addTutor(Teacher $teacher): self {
        if (!$this->tutors->contains($teacher)) {
            $this->tutors->add($teacher);
            $teacher->addGroup($this);
        }
        return $this;
    }

    public function removeTutor(Teacher $teacher): self {
        if ($this->tutors->removeElement($teacher)) {
            $teacher->removeGroup($this);
        }
        return $this;
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

    public function getStudents(): Collection {
        return $this->students;
    }

    public function addStudent(Student $student): self {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
            $student->setGroup($this);
        }
        return $this;
    }

    public function removeStudent(Student $student): self {
        if ($this->students->removeElement($student)) {
            if ($student->getGroup() === $this) {
                $student->setGroup(null);
            }
        }
        return $this;
    }

    public function getSubjects(): Collection {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): self {
        if (!$this->subjects->contains($subject)) {
            $this->subjects[] = $subject;
            $subject->addGroup($this);
        }
        return $this;
    }

    public function removeSubject(Subject $subject): self {
        if ($this->subjects->removeElement($subject)) {
            $subject->removeGroup($this);
        }
        return $this;
    }

    public function getAcademicYear(): ?AcademicYear {
        return $this->academicYear;
    }

    public function setAcademicYear(?AcademicYear $academicYear): self {
        $this->academicYear = $academicYear;
        return $this;
    }
}
