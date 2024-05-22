<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SubjectRepository;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToMany(targetEntity: Group::class, mappedBy: 'subjects')]
    private Collection $groups;

    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'subjects')]
    private Collection $projects;

    public function __construct() {
        $this->groups = new ArrayCollection();
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

    public function getGroups(): Collection {
        return $this->groups;
    }

    public function addGroup(Group $group): self {
        if (!$this->groups->contains($group)) {
            $this->groups[] = $group;
            $group->addSubject($this);
        }
        return $this;
    }

    public function removeGroup(Group $group): self {
        if ($this->groups->removeElement($group)) {
            $group->removeSubject($this);
        }
        return $this;
    }

    public function getProjects(): Collection {
        return $this->projects;
    }

    public function addProject(Project $project): self {
        if (!$this->projects->contains($project)) {
            $this->projects[] = $project;
            $project->addSubject($this);
        }
        return $this;
    }

    public function removeProject(Project $project): self {
        if ($this->projects->removeElement($project)) {
            $project->removeSubject($this);
        }
        return $this;
    }
}