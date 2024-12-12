<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TeacherRepository::class)]
class Teacher extends User
{

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\OneToMany(mappedBy: 'proposedBy', targetEntity: Project::class)]
    private Collection $projects;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'tutors', cascade: ['persist'])]
    private Collection $groups;

    public function __construct() {
        $this->projects = new ArrayCollection();
        $this->groups = new ArrayCollection();
        $this->addRole('ROLE_TEACHER');
    }

    public function getGroups(): Collection {
        return $this->groups;
    }

    public function addGroup(Group $group): self {
        if (!$this->groups->contains($group)) {
            $this->groups->add($group);
            $group->addTutor($this);
        }
        return $this;
    }

    public function removeGroup(Group $group): self {
        if ($this->groups->removeElement($group)) {
            $group->removeTutor($this);
        }
        return $this;
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
        return !$this->groups->isEmpty();
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
            if ($project->getProposedBy() === $this) {
                $project->setProposedBy(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function promoteToAdmin(): self
    {
        $this->addRole('ROLE_ADMIN');
        return $this;
    }
}
